<?php 
// Класс, обеспечивающий функциональность администрирования отделов
class AdminDepartments
{
	// Public-переменные, доступные в шаблоне Smarty
	public $mDepartmentsCount;
	public $mDepartments;
	public $mErrorMessage;
	public $mEditItem;
	public $mLinkToDepartmentsAdmin;
	
	// Private-переменные
	private $_mAction;
	private $_mActionedDepartmentId;
	// Конструктор класса
	public function __construct()
	{
		// Просматриваем список переданных переменных
		foreach ($_POST as $key => $value)
			// При щелчке на кнопке отправки...
			if (substr($key, 0, 6) == 'submit')
			{
				/* Получаем позицию последнего символа '_' из имени кнопки, 
						например, strrpost('submit_edit_dept_1', '_') вернет 17 */
				$last_underscore = strrpos($key, '_');
				/* Получаем область действия из имени кнопки 
					(например, 'edit_dept' из 'submit_edit_dept_1')*/
				$this->_mAction = substr($key, strlen('submit_'), 
																	$last_underscore - strlen('submit_'));
				/* Получаем идентификатор отдела, к которому относится нажатая кнопка
					(цифру в конце имени кнопки),
					например, '1' из 'submit_edit_dept_1'*/
				$this->_mActionedDepartmentId = substr($key, $last_underscore + 1);
				
				break;
			}
		
		$this->mLinkToDepartmentsAdmin = Link::ToDepartmentsAdmin();
	}
	
	public function init(){
		// При добавлении нового отдела...
		if ($this->_mAction == 'add_dept')
		{
			$department_name = $_POST['department_name'];
			$department_description = $_POST['department_description'];
			
			if ($department_name == null)
				$this->mErrorMessage = 'Department name required';
			
			if($this->mErrorMessage == null)
			{
				Catalog::AddDepartment($department_name, $department_description);
				header('Location: ' . $this->mLinkToDepartmentsAdmin);
			}
		}
		
		// При редактировании существующего отдела ...
		if ($this->_mAction == 'edit_dept')
			$this->mEditItem = $this->_mActionedDepartmentId;
		
		// При обновление отдела ...
		if ($this->_mAction == 'update_dept')
		{
			$department_name = $_POST['name'];
			$department_description = $_POST['description'];
			
			if ($department_name == null)
				$this->mErrorMessage = 'Department name required';
			
			if ($this->mErrorMessage == null)
			{
				Catalog::UpdateDepartment($this->_mActionedDepartmentId,
																	$department_name, $department_description);
				header('Location:' . $this->mLinkToDepartmentsAdmin);
			}
		}
		
		// При удалении отдела...
		if ($this->_mAction == 'delete_dept')
		{
			$status = Catalog::DeleteDepartment($this->_mActionedDepartmentId);
			
			if ($status < 0)
				$this->mErrorMessage = 'Department not empty';
			else
				header('Location: ' . $this->mLinkToDepartmentsAdmin);
		}
		
		// При редактировании категорий отдела...
		if ($this->_mAction == 'edit_cat')
		{
			header('Location: ' .
							htmlspecialchars_decode(
								Link::ToDepartmentCategoriesAdmin(
									$this->_mActionedDepartmentId)));
			exit();
		}
		
		// Загружаем список отделов
		$this->mDepartments = Catalog::GetDepartmentsWithDescriptions();
		$this->mDepartmentsCount = count($this->mDepartments);
	}
}