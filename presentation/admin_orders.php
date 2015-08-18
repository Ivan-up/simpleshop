<?php 
// Класс уровня представления, отвечающий за администрирования заказов 
class AdminOrders
{
	// Public - переменные, доступные в шаблона smarty
	public $mOrders;
	public $mStartDate;
	public $mEndDate;
	public $mRecordCount = 20;
	public $mOrdersStatusOptions;
	public $mSelectedStatus =0;
	public $mErrorMessage = '';
	public $mLinkToAdmin;
	
	// Конструктор класса
	public function __construct()
	{ 
		/* Сохраняем ссылку на текущую страницу в сеансовой переменной 
			link_to_orders_admin; она понадобится нам для создания ссылки
			"back_to_admin_orders..." на страницах деталей заказов */
		$_SESSION['link_to_orders_admin'] =
			Link::Build(mb_substr($_SERVER['REQUEST_URI'],
									mb_strlen(VIRTUAL_LOCATION)));
		$this->mLinkToAdmin = Link::ToAdmin();
		$this->mOrdersStatusOptions = Orders::$mOrderStatusOptions;
	}
	public function init()
	{
		// Если используется фильтр "Показать х последних заказов..."
		if (isset ($_GET['submitMostRecent']))
		{ 
			// Если количество записей - не корректное целое число, 
			// выводим сообщение об ошибке
			if ((string)(int)$_GET['recordCount'] == (string)$_GET['recordCount'])
			{
				$this->mRecordCount = (int)$_GET['recordCount'];
				$this->mOrders = Orders::GetMostRecentOrders($this->mRecordCount);
			}
			else
				$this->mErrorMessage = $_GET['recordCount'] . ' is not a number ';
		}
		/* Если используется фильтр "Показать заказы, сделанные между
			date_1 и date_2" ...*/
		if (isset($_GET['submitBetweenDates']))
		{
			$this->mStartDate = $_GET['startDate'];
			$this->mEndDate = $_GET['endDate'];
			
			// Проверяем корректность начальной даты 
			if (($this->mStartDate == '') ||
					($timestamp = strtotime($this->mStartDate)) == false)
				$this->mErrorMessage .= 'The start date is invalid.';
			else
				// Преобразуем дату в формат YYYY/MM/DD HH:MM:SS
				$this->mStartDate =
					strftime('%Y/%m/%d %H:%M:%S', strtotime($this->mStartDate));
					
			// Проверяем корректность конечной даты 
			if (($this->mEndDate == '') ||
					($timestamp = strtotime($this->mEndDate)) == false)
				$this->mErrorMessage .= 'The end date is invalid.';
			else
				// Преобразуем дату в формат YYYY/MM/DD HH:MM:SS
				$this->mEndDate =
					strftime('%Y/%m/%d %H:%M:%S', strtotime($this->mEndDate));
		
			// Проверяем правильность следования дат
			if ((empty ($this->mErrorMessage)) &&
					(strtotime($this->mStartDate)) > strtotime($this->mEndDate))
				$this->mErrorMessage .=
					'The start date should be more recent than the end date.';
			
			// Если ошибок нет, получаем заказы, сделанные между двумя датами
			if (empty($this->mErrorMessage))
				$this->mOrders = Orders::GetOrdersBetweenDates(
														$this->mStartDate, $this->mEndDate);
		}
		
		// Если используется фильтр "Показать заказы с выбранным статусом" ...
		if (isset ($_GET['submitOrdersByStatus']))
		{
			$this->mSelectedStatus = $_GET['status'];
			$this->mOrders = Orders :: GetOrdersByStatus($this->mSelectedStatus);
		}
		
		if (is_array($this->mOrders) && count($this->mOrders) == 0)
			$this->mErrorMessage =
				'No orders found matching your searching criteria!';
		
		// Создаем ссылку View Details
		for ($i = 0; $i < count($this->mOrders); $i++)
		{
			$this->mOrders[$i]['link_to_order_details_admin'] =
				Link::ToOrderDetailsAdmin($this->mOrders[$i]['order_id']);
		}
	}
}