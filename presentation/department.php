<?php 
// Занимается извлечение сведений об отделе
class Department
{
	// Public-переменные для шаблона Smarty
	public $mName;
	public $mDescription;
	
	// Private-элементы
	private $_mDepartmentId;
	private $_mCategoryId;
	
	// Конструктор класса
	public function __construct()
	{
		// В строке запроса должен присутствовать параметр DepartmentId
		if (isset ($_GET['DepartmentId']))
			$this->_mDepartmentId = (int)$_GET['DepartmentId'];
		else
			trigger_error('DepartmentId not set');
		
		/* Если CategoryId есть в строке запроса, мы сохраняем его значение
			(преобразуя его в integer для защиты от некорректных значений)*/
		if (isset ($_GET['CategoryId']))
			$this->_mCategoryId = (int)$_GET['CategoryId'];
	}
	
	public function init()
	{
		// Если посещаем отдел
		$department_details =
			Catalog::GetDepartmentDetails($this->_mDepartmentId);
		
		$this->mName = $department_details['name'];
		$this->mDescription = $department_details['description'];
		
		// Если посещаем категорию ...
		if (isset($this->_mCategoryId))
		{ 
			$category_details =
				Catalog::GetCategoryDetails($this->_mCategoryId);
			$this->mName = $this->mName . ' &raquo;' .
				$category_details['name'];
			$this->mDescription = $category_details['description'];
		}
	}
	
}