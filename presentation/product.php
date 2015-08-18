<?php 
// Отвечает за отображение подробных сведений о товаре
class Product
{
	// Public-переменные для использования в шаблоне Smarty
	public $mProduct;
	public $mProductLocations;
	public $mLinkToContinueShopping;
	public $mLocations;
	public $mEditActionTarget;
	public $mShowEditButton;
	public $mRecommendations;
	
	// Private-переменная
	private $_mProductId;
	
	// Конструктор класса
	public function __construct()
	{
		// Инициализация переменной
		if (isset($_GET['ProductId']))
			$this->_mProductId = (int)$_GET['ProductId'];
		else
			trigger_error('ProductId not set');
		
		// Отображаем кнопку редактирования для администратора
		if (!(isset($_SESSION['admin_logged'])) ||
				$_SESSION['admin_logged'] != true)
			$this->mShowEditButton = false;
		else
			$this->mShowEditButton = true;
	}
	
	public function init()
	{
		// Получаем сведения о товаре из уровня логики приложения
		$this->mProduct = Catalog::GetProductDetails($this->_mProductId);
		
		if (isset ($_SESSION['link_to_continue_shopping']))
		{
			$continue_shopping = 
				Link::QueryStringToArray($_SESSION['link_to_continue_shopping']);
			
			$page = 1;
			
			if (isset ($continue_shopping['Page']))
				$page = (int)$continue_shopping['Page'];
			
			if(isset ($continue_shopping['CategoryId']))
				$this->mLinkToContinueShopping =
					Link::ToCategory((int)$continue_shopping['DepartmentId'],
														(int)$continue_shopping['CategoryId'], $page);
			elseif (isset ($continue_shopping['DepartmentId']))
				$this->mLinkToContinueShopping =
					Link::ToDepartment((int)$continue_shopping['DepartmentId'], $page);
			elseif (isset ($continue_shopping['SearchResults']))
				$this->mLinkToContinueShopping =
					Link::ToSearchResults(
						trim(str_replace('-', ' ', $continue_shopping['SearchString'])),
						$continue_shopping['AllWords'], $page);
			else
				$this->mLinkToContinueShopping = Link::ToIndex($page);
		}
		
		if ($this->mProduct['image'])
			$this->mProduct['image'] = 
				Link::Build('product_images/' . $this->mProduct['image']);
		
		if ($this->mProduct['image_2'])
			$this->mProduct['image_2'] = 
				Link::Build('product_images/' . $this->mProduct['image_2']);
		
		$this->mProduct['attributes'] = 
			Catalog::GetProductAttributes($this->mProduct['product_id']);
		
		$this->mLocations = Catalog::GetProductLocations($this->_mProductId);
		
		// Генерируем ссылку Add to Cart
		$this->mProduct['link_to_add_product'] =
			Link::ToCart(ADD_PRODUCT, $this->_mProductId);
		
		// Получаем рекомендации товаров 
		$this->mRecommendations =
			Catalog::GetRecommendations($this->_mProductId); 
	
		// Генерируем ссылки на рекомендованные товары 
		for ($i = 0; $i < count($this->mRecommendations); $i++)
			$this->mRecommendations[$i]['link_to_product'] =
				Link::ToProduct($this->mRecommendations[$i]['product_id']);
				
		// Генерируем ссылки на страницы отдела и категории
		for ($i = 0; $i < count($this->mLocations); $i++)
		{
			$this->mLocations[$i]['link_to_department'] = 
				Link::ToDepartment($this->mLocations[$i]['department_id']);
			
			$this->mLocations[$i]['link_to_category'] = 
				Link::ToCategory($this->mLocations[$i]['department_id'],
													$this->mLocations[$i]['category_id']);
		}
		
		// Подготавливаем кнопку редактирования
		$this->mEditActionTarget =
			Link::Build(mb_substr($_SERVER['REQUEST_URI'], 
									mb_strlen(VIRTUAL_LOCATION)));
		
		if (isset($_SESSION['admin_logged']) &&
				$_SESSION['admin_logged'] == true &&
				isset ($_POST['submit_edit']))
		{
			$product_locations = $this->mLocations;
			
			if (count($product_locations) > 0)
			{
				$department_id = $product_locations[0]['deparment_id'];
				$category_id = $product_locations[0]['category_id'];
				
				header ('Location: ' . 
								htmlspecialchars_decode(
								Link::ToProductAdmin($department_id,
																			$category_id,
																			$this->_mProductId)));
			}
		}
	}
}