<?php 
// Класса уровня логики приложения для администрирования заказов 
class Orders
{
	public static $mOrderStatusOptions = array ('placed',    // 0
																							'verified',  // 1
																							'completed', // 2
																							'canceled'); // 3
	
	// Получаем $how_many последних заказов 
	public static function GetMostRecentOrders($how_many)
	{
		// Составляем SQL-запрос 
		$sql = 'CALL orders_get_most_recent_orders(:how_many)';
		
		// Создаем массив параметров 
		$params = array (':how_many' => $how_many);
		
		// Выполняем запрос и возвращаем результаты
		return DatabaseHandler::GetAll($sql, $params);
	}
	
	// Получаем заказы, сделанные между заданными датами
	public static function GetOrdersBetweenDates($startDate, $endDate)
	{
		// Составляем SQL-запрос
		$sql = 'CALL orders_get_orders_between_dates(:start_date, :end_date)';
		
		// Создаем массив параметров 
		$params = array (':start_date' => $startDate, ':end_date' => $endDate);
		
		// Выполняем запрос и возвращаем результаты
		return DatabaseHandler::GetAll($sql, $params);
	}
	
	// Получаем заказы с выбранным статусом
	public static function GetOrdersByStatus($status)
	{
		// Составляем SQL-запрос
		$sql = 'CALL orders_get_orders_by_status(:status)';
		
		// Создаем массив параметров 
		$params = array (':status' => $status);
		
		// Выполняем запрос и возвращаем результаты
		return DatabaseHandler::GetAll($sql, $params);
	}
	
	// Считавает детали выбранного заказа
	public static function GetOrderInfo($orderId)
	{
		// Составляем SQL-запрос
		$sql = 'CALL orders_get_order_info(:order_id)';
		
		// Создаем массив параметров 
		$params = array (':order_id' => $orderId);
		
		// Выполняем запрос и возращаем результаты
		return DatabaseHandler::GetRow($sql, $params);
	}
	
	// Получаем товары, относящиеся к заданном заказу
	public static function GetOrderDetails($orderId)
	{
		// Составляем SQL-запрос
		$sql = 'CALL orders_get_order_details(:order_id)';
		
		// Создаем массив параметров
		$params = array (':order_id' => $orderId);
		
		return DatabaseHandler::GetAll($sql, $params);
	}
	
	// Обновляем детали заказа в базе данных
	public static function UpdateOrder($orderId, $status, $comments,
		$customerName, $shippingAddress, $customerEmail)
	{
		// Составляем SQL-запрос 
		$sql = 'CALL orders_update_order(:order_id, :status, :comments,
			:customer_name, :shipping_address, :customer_email)';
		
		// Создаем массива параметров
		$params = array (':order_id' => $orderId,
											':status' => $status,
											':comments'=> $comments,
											':customer_name' => $customerName,
											':shipping_address' => $shippingAddress,
											':customer_email' => $customerEmail);
		
		// Выполняем запрос 
		DatabaseHandler::Execute($sql, $params);
	}
	
}