<?php 
// Класс, обеспечивающий администрирование категорий
class AdminCategories
{
	// Public-переменные, доступные в шаблоне Smarty
	public $mCategoriesCount;
	public $mCategories;
	public $mErrorMessage;
	public $mEditItem;
	public $mDepartmentId;
	public $mDepartmentName;
	public $mLinkToDepartmentsAdmin;
	public $mLinkToDepartmentCategoriesAdmin;
	
	// Privatу-переменные
	private $_mAction;
	private $_mActionedCategoryId;
	
	//Конструктор класса
	public function __construct()
	{
		if (isset ($_GET['DepartmentId']))
			$this->mDepartmentId = (int)$_GET['DepartmentId'];
		else
			trigger_error('DepartmentId not set');
		
		$department_details = Catalog::GetDepartmentDetails($this->mDepartmentId);
		$this->mDepartmentName = $department_details['name'];
		
		foreach ($_POST as $key => $value)
			// При щелчке на кнопке...
			if (substr($key, 0, 6) == 'submit')
			{
				/* Получаем позицию последнего символа '_' из имени нажатой 
					кнопки, например strrpos('submit_edit_cat_1', '_') будет 16*/
				$last_underscore = strrpos($key, '_');
				/* Узнаем тип нажатой кнопки
						(например, 'edit_cat' из 'submit_edit_cat_1')*/
				$this->_mAction = substr($key,strlen('submit_'),
																	$last_underscore - strlen('submit_'));
				/* Получаем идентификатор категорий, для которой нажата кнопка 
					(номер в конце имени нажатой кнопки),
					например, '1' из 'submit_edit_cat_1'*/
				$this->_mActionedCategoryId = (int)substr($key, $last_underscore + 1);
				break;
			}
		$this->mLinkToDepartmentsAdmin = Link::ToDepartmentsAdmin();
		
		$this->mLinkToDepartmentCategoriesAdmin =
			Link::ToDepartmentCategoriesAdmin($this->mDepartmentId);
	}
	
	public function init()
	{	
		// При добавлении новой категории...
		if ($this->_mAction == 'add_cat')
		{
			$category_name = $_POST['category_name'];
			$category_description = $_POST['category_description'];
			
			if ($category_name == null)
				$this->mErrorMessage = 'Category name is empty';
			
			if ($this->mErrorMessage == null)
			{
				Catalog::AddCategory($this->mDepartmentId, $category_name,
															$category_description);
				header('Location: ' .
								htmlspecialchars_decode(
									$this->mLinkToDepartmentCategoriesAdmin));
			}			
		}
		
		// При редактировании существующей категории...
		if ($this->_mAction == 'edit_cat')
		{
			$this->mEditItem = $this->_mActionedCategoryId;
		}
		
		// При обновлении категории...
		if ($this->_mAction == 'update_cat')
		{
			$category_name = $_POST['name'];
			$category_description = $_POST['description'];
			
			if ($category_name == null)
				$this->mErrorMessage = 'Category name is empty';
			
			if ($this->mErrorMessage == null)
			{
				Catalog::UpdateCategory($this->_mActionedCategoryId, $category_name,
																$category_description);
				header('Location: ' . 
								htmlspecialchars_decode(
									$this->mLinkToDepartmentCategoriesAdmin));
			}
		}
		
		// При удалении категории
		if ($this->_mAction == 'delete_cat')
		{
			$status = Catalog::DeleteCategory($this->_mActionedCategoryId);
			
			if ($status < 0)
				$this->mErrorMessage = "Category not empty";
			else
				header('Location: ' .
							htmlspecialchars_decode(
								$this->mLinkToDepartmentCategoriesAdmin));
		}
		
		// При редактировании товаров из категории ...
		if ($this->_mAction == 'edit_prod')
		{
			header('Location: ' . 
							htmlspecialchar_decode(
								Link::ToCategoryProductsAdmin($this->mDepartmentId,
																								$this->_mActionedCategoryId)));
			exit();
		}
		
		// Загружаем список категорий 
		$this->mCategories = 
			Catalog::GetDepartmentCategories($this->mDepartmentId);
		$this->mCategoriesCount = count($this->mCategories);
	}
}