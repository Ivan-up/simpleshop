<?php 
// Класс, отвечающий за работу корзины покупателя
class CartDetails{
	// Public-переменные, доступные в шаблоне Smarty
	public $mCartProducts;
	public $mSavedCartProducts;
	public $mTotalAmount;
	public $mIsCartNowEmpty = 0; // Корзина пуста?
	public $mIsCartLaterEmpty = 0; // Список отложенных товаров пуст?
	public $mUpdateCartTarget;
	
	// Private-атрибуты
	private $_mItemId;
	private $_mCartAction;
	
	// Конструктор класса
	public function __construct()
	{
		if (isset ($_GET['CartAction']))
			$this->_mCartAction = $_GET['CartAction'];
		else
			trigger_error('CartAction not set', E_USER_ERROR);
		
		// Эти операции с корзиной требуют корректного идентификатора товара 
		if ($this->_mCartAction == ADD_PRODUCT ||
				$this->_mCartAction == REMOVE_PRODUCT ||
				$this->_mCartAction == SAVE_PRODUCT_FOR_LATER ||
				$this->_mCartAction == MOVE_PRODUCT_TO_CART)
		{
			if (isset($_GET['ItemId']))
				$this->_mItemId = $_GET['ItemId'];
			else
				trigger_error('ItemId must be set for this type of request', E_USER_ERROR);
		}
		$this->mUpdateCartTarget = Link::ToCart(UPDATE_PRODUCTS_QUANTITIES);
		
		// Задаем цель для ссылки, возращающей в каталог
		if (isset ($_SESSION['link_to_last_page_loaded']))
			$this->mLinkToContinueShopping = $_SESSION['link_to_last_page_loaded'];
	}
	
	public function init()
	{
		switch ($this->_mCartAction)
		{
			case ADD_PRODUCT:
				$selected_attributes = array();
				$selected_attribute_values = array();
				
				// Получаем выбранные атрибуты товаров, если они есть
				foreach ($_POST as $key => $value)
				{
					// Если в массиве POST есть элементы, начинающие с "attr_"
					if (substr($key, 0, 5) == 'attr_')
					{
						// Получаем имя и значение выбранного атрибута 
						$selected_attributes[] = substr($key, mb_strlen('attr_'));
						$selected_attribute_values[] = $_POST[$key];
					}
				}
				$attributes = '';
			
				if(count($selected_attributes) > 0)
					$attributes = implode('/', $selected_attributes) . ':' .
												implode('/', $selected_attribute_values);
				ShoppingCart::AddProduct($this->_mItemId, $attributes);
				
				if (!isset ($_GET['AjaxRequest']))
					header('Location: ' . $this->mLinkToContinueShopping);
				else
					return;
				
				break;
			case REMOVE_PRODUCT:
				ShoppingCart::RemoveProduct($this->_mItemId);
				if (!isset ($_GET['AjaxRequest']))
					header('Location: ' . Link::ToCart());
				break;
			case UPDATE_PRODUCTS_QUANTITIES:
				for ($i = 0; $i < count($_POST['itemId']); $i++)
					ShoppingCart::Update($_POST['itemId'][$i],
																$_POST['quantity'][$i]);
				if (!isset ($_GET['AjaxRequest']))
					header('Location: ' . Link::ToCart());
				break;
			case SAVE_PRODUCT_FOR_LATER:
				ShoppingCart::SaveProductForLater($this->_mItemId);
				if (!isset ($_GET['AjaxRequest']))
					header('Location:' . Link::ToCart());
				break;
			case MOVE_PRODUCT_TO_CART:
				ShoppingCart::MoveProductToCart($this->_mItemId);
				header('Location: ' . Link::ToCart());
				break;
			default: 
				// Не делаем ничего 
				break;
		}
		
		/* Вычисляем общую стоимость товаров в корзине
			без учета налогов и цены доставки*/
		$this->mTotalAmount = ShoppingCart::GetTotalAmount();
		
		// Если нажата кнопка Place Order...
		if (isset ($_POST['place_order']))
		{
			// Создаем заказ и получаем его идентификатор
			$order_id = ShoppingCart::CreateOrder();
			
			exit('Перенаравление на страницу оплаты');
		}
		
		// Получаем список товаров в корзине
		$this->mCartProducts =
			ShoppingCart::GetCartProducts(GET_CART_PRODUCTS);
		
		// Получаем список товаров, отложенных для оплаты в будущем
		$this->mSavedCartProducts =
			ShoppingCart::GetCartProducts(GET_CART_SAVED_PRODUCTS);
			
		// Проверяем, не пуста ли корзина 
		if (count($this->mCartProducts) == 0)
			$this->mIsCartNowEmpty = 1;
		
		// Проверяем, не пуст ли список отложеных товаров
		if (count($this->mSavedCartProducts) == 0)
			$this->mIsCartLaterEmpty = 1;
		
		// Создаем ссылки для операции с корзиной
		for ($i = 0; $i < count($this->mCartProducts); $i++)
		{
			$this->mCartProducts[$i]['save'] =
				Link::ToCart(SAVE_PRODUCT_FOR_LATER,
											$this->mCartProducts[$i]['item_id']);
			$this->mCartProducts[$i]['remove'] =
				Link::ToCart(REMOVE_PRODUCT,
										$this->mCartProducts[$i]['item_id']);
		}
		
		for ($i = 0; $i < count($this->mSavedCartProducts); $i++)
		{
			$this->mSavedCartProducts[$i]['move'] =
				Link::ToCart(MOVE_PRODUCT_TO_CART,
											$this->mSavedCartProducts[$i]['item_id']);
			$this->mSavedCartProducts[$i]['remove'] =
				Link::ToCart(REMOVE_PRODUCT,
										$this->mSavedCartProducts[$i]['item_id']);
		}
	}
}