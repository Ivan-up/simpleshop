<?php 
// Отвечает за отображение подробных сведений о товаре
class Product
{
	// Public-переменные для использования в шаблоне Smarty
	public $mProduct;
	public $mProductLocations;
	public $mLinkToContinueShopping;
	public $mLocations;
	
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
			else
				$this->mLinkToContinueShopping = Link::ToIndex($page);
		}
		
		if ($this->mProduct['image'])
			$this->mProduct['image'] = 
				Link::Build('product_images/' . $this->mProduct['image']);
		
		if ($this->mProduct['image_2'])
			$this->mProduct['image_2'] = 
				Link::Build('product_images/' . $this->mProduct['image_2']);
		
		$this->mLocations = Catalog::GetProductLocations($this->_mProductId);
	
		// Генерируем ссылки на страницы отдела и категории
		for ($i = 0; $i < count($this->mLocations); $i++)
		{
			$this->mLocations[$i]['link_to_department'] = 
				Link::ToDepartment($this->mLocations[$i]['department_id']);
			
			$this->mLocations[$i]['link_to_category'] = 
				Link::ToCategory($this->mLocations[$i]['department_id'],
													$this->mLocations[$i]['category_id']);
		}
	}
}