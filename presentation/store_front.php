<?php
class StoreFront
{
	public $mSiteUrl;
	// Определяем файл шаблона для содержимого страницы
	public $mContentsCell = "first_page_contents.tpl";
	// Определяем файл шаблона для ячеек категорий
	public $mCategoriesCell = 'blank.tpl';
	// Заголовок страницы 
	public $mPageTitle;
	
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
		
		// Загружаем страницу с результатами поиска, если выполнялся поиска
		elseif (isset ($_GET['SearchResults']))
			$this->mContentsCell = 'search_results.tpl';
			
		// Загружаем заголовок страницы 
		$this->mPageTitle = $this->_GetPageTitle();
	}
	
	// Возращает заголовок страницы 
	private function _GetPageTitle()
	{
		$page_title = 'TShirtShop: ' . 
			'Demo Product Catalog from Beginning PHP and MySQL E-Commerce';
		
		if (isset ($_GET['DepartmentId']) && isset ($_GET['CategoryId']))
		{
			$page_title = 'TShirtShop: ' .
				Catalog::GetDepartmentName($_GET['DepartmentId']) . ' - ' .
				Catalog::GetCategoryName($_GET['CategoryId']);
				
			if (isset ($_GET['Page']) && ((int)$_GET['Page']) > 1)
				$page_title .= ' - Page ' . ((int)$_GET['Page']);
		}
		elseif (isset ($_GET['DepartmentId']))
		{
			$page_title = 'TShirtShop: ' .
				Catalog::GetDepartmentName($_GET['DepartmentId']);
				
			if (isset ($_GET['Page']) && ((int)$_GET['Page']) > 1)
				$page_title .= ' - Page ' . ((int)$_GET['Page']);
		}
		elseif (isset ($_GET['ProductId']))
		{
			$page_title = 'TShirtShop:' .
				Catalog::GetProductName($_GET['ProductId']);
		}
		elseif (isset ($_GET['SearchResult']))
		{
			$page_title = 'TShirtShop: "';
			
			// Отоббражаем строку поиска
			$page_title .= trim(str_replace('-', ' ', $_GET['SearchString'])) . '" (';
			
			// Отображаем "all-words search" или "any-words search"
			$all_words .= isset ($_GET['AllWords']) ? $_GET['AllWords'] : 'off';
			
			$page_title .= (($all_words == 'on') ? 'all' : 'any') .
											'-words search';
			
			// Отображаем номер страницы 
			if (isset ($_GET['Page']) && ((int)$_GET['Page']) > 1)
				$page_title .= ', page ' . ((int)$_GET['Page']);
			
			$page_title .= ')';
		}
		else
		{
			if (isset ($_GET['Page']) && ((int)$_GET['Page']) > 1)
				$page_title .= ' - Page ' . ((int)$_GET['Page']);
		}
		
		return $page_title;
	}
}