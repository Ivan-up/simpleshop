<?php
class StoreFront
{
	public $mSiteUrl;
	// Определяем файл шаблона для содержимого страницы
	public $mContentsCell = "first_page_contents.tpl";
	// Определяем файл шаблона для ячеек категорий
	public $mCategoriesCell = 'blank.tpl';
	// Определяем файл шаблона для ячейки содержимого корзины
	public $mCartSummaryCell = 'blank.tpl';
	// Определяем файл шаблона для полей аутентификации 
	public $mLoginOrLoggedCell = 'customer_login.tpl';
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
		$_SESSION['link_to_store_front'] =
			Link::Build(mb_substr($_SERVER['REQUEST_URI'], mb_strlen(VIRTUAL_LOCATION)));
		
		// Создаем ссылку "continue shopping"
		if (!isset($_GET['CardAction']) && 
				!isset($_GET['Logout']) &&
				!isset($_GET['RegisterCustomer']) &&
				!isset($_GET['AddressDetails']) &&
				!isset($_GET['CreditCardDetails']) &&
				!isset($_GET['AccountDetails']))
			$_SESSION['link_to_last_page_loaded'] = $_SESSION['link_to_store_front'];
			
		// Создаем ссылку "cancel" для страницы со сведениями о пользователе
		if (!isset($_GET['Logout']) &&
				!isset($_GET['RegisterCustomer']) &&
				!isset($_GET['AddressDetails']) &&
				!isset($_GET['CreditCardDetails']) &&
				!isset($_GET['AccountDetails']))
			$_SESSION['customer_cancel_link'] = $_SESSION['link_to_store_front'];
		
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
		
		// Загружаем шаблоны для отображения содержимого корзины
		if (isset($_GET['CartAction']))
			$this->mContentsCell = 'cart_details.tpl';
		else
			$this->mCartSummaryCell = 'cart_summary.tpl';
		
		if (Customer::IsAuthenticated())
			$this->mLoginOrLoggedCell = 'customer_logged.tpl';
		
		if (isset($_GET['RegisterCustomer']) || 
				isset($_GET['AccountDetails']))
			$this->mContentsCell = 'customer_details.tpl';
		elseif(isset($_GET['AddressDetails']))
			$this->mContentsCell = 'customer_address.tpl';
		elseif (isset($_GET['CreditCardDetails']))
			$this->mContentsCell = 'customer_credit_card.tpl';
			
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