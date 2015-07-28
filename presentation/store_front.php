<?php
class StoreFront
{
	public $mSiteUrl;
	// Определяем файл шаблона для содержимого страницы
	public $mContentsCell = "first_page_contents.tpl";
	// Определяем файл шаблона для ячеек категорий
	public $mCategoriesCell = 'blank.tpl';
	
	// Конструктор класса
	public function __construct()
	{
		$this->mSiteUrl = Link::Build('');
	}
	
	// Инициализируем объект представления
	public function init()
	{
		// Загружаем подробные сведения об отделе на страницу отдела
		if (isset($_GET['DepartmentId']))
		{
			$this->mContentsCell = 'department.tpl';
			$this->mCategoriesCell = 'categories_list.tpl';
		}
		elseif (isset($_GET['ProductId']) && 
						isset($_SESSION['link_to_continue_shopping']) &&
						strpos($_SESSION['link_to_continue_shopping'], 'DepartmentId', 0) 
						!== false)
		{
			$this->mCategoriesCell = 'categories_list.tpl';
		}
		
		// Загружаем сведения о товаре на страницу товара
		if (isset($_GET['ProductId']))
			$this->mContentsCell = 'product.tpl';
	}
}