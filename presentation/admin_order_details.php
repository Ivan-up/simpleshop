<?php 
// Класс уровня представления, отвечающий за администрирования деталей заказов 
class AdminOrderDetails
{
	// Public - переменные, доступные в шаблоне smarty 
	public $mOrderId;
	public $mOrderInfo;
	public $mOrderDetails;
	public $mEditEnabled;
	public $mOrderStatusOptions;
	public $mLinkToAdmin;
	public $mLinkToOrdersAdmin;
	
	// Конструктор класса
	public function __construct()
	{
		// Получаем ссылку на предыдущую страницу из сеансовой переменной
		$this->mLinkToOrdersAdmin = $_SESSION['link_to_orders_admin'];
		
		$this->mLinkToAdmin = Link::ToAdmin();
		
		// Мы получаем идентификатор заказа в строке запроса
		if (isset ($_GET['OrderId']))
			$this->mOrderId = (int) $_GET['OrderId'];
		else 
			trigger_error ('OrderId parameter is required');
		
		$this->mOrderStatusOptions = Orders::$mOrderStatusOptions;
	}
	
	// Инициализируем элементы класса 
	public function init()
	{
		if (isset ($_GET['submitUpdate']))
		{
			Orders::UpdateOrder($this->mOrderId, $_GET['status'],
				$_GET['comments'], $_GET['customerName'], $_GET['shippingAddress'],
				$_GET['customerEmail']);
		}
		
		$this->mOrderInfo = Orders::GetOrderInfo($this->mOrderId);
		$this->mOrderDetails = Orders::GetOrderDetails($this->mOrderId);
		
		// Значение, указывающее, включен или отключен режим редактирования 
		if (isset ($_GET['submitEdit']))
			$this->mEditEnabled = true;
		else
			$this->mEditEnabled = false;
		
	}
}