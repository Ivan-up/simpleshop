<?php 
// Отвечает за списки категорий
class CategoriesList
{
	// Public-переменные для шаблона smarty
	public $mSelectedCategory = 0;
	public $mSelectedDepartment = 0;
	public $mCategories;
	
	// Конструктор считывает параметр из строки запроса
	public function __construct()
	{
		if (!isset($_GET['ProductId'])){
			if (isset($_GET['DepartmentId']))
				$this->mSelectedDepartment = (int)$_GET['DepartmentId'];
			else
				trigger_error('DepartmentId not set');
			
			if (isset($_GET['CategoryId']))
				$this->mSelectedCategory = (int)$_GET['CategoryId'];
		}
		else
		{
			$continue_shopping =
				Link::QueryStringToArray($_SESSION['link_to_continue_shopping']);
			
			if(array_key_exists('DepartmentId', $continue_shopping))
				$this->mSelectedDepartment =
					(int)$continue_shopping['DepartmentId'];
			else
				trigger_error('DepartmentId not set');
			
			if(array_key_exists('CategoryId', $continue_shopping))
				$this->mSelectedCategory =
					(int)$continue_shopping['CategoryId'];
		}

	}
	
	public function init()
	{  
		$this->mCategories =
			Catalog::GetCategoriesInDepartment($this->mSelectedDepartment);
			
		// Генерируем ссылки для страниц категорий
		for ($i = 0; $i < count($this->mCategories); $i++)
			$this->mCategories[$i]['link_to_category'] =
				Link::ToCategory($this->mSelectedDepartment,
													$this->mCategories[$i]['category_id']);
	}
}