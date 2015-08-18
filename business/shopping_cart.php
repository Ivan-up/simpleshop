<?php 
// Класс уровня логики приложения, отвечающий за корзину покупателя 
class ShoppingCart
{
	// Хранит идентификатор корзины
	private static $_mCartId;
	
	// Private-конструктор, не позволяющий напрямую создавать объекты класса
	private function __construct()
	{
	}
	
	/* Этот метод будет вызываться GetCartId(), чтобы убедиться, что
		в сеансе пользователя есть идентификатор корзины, если в 
		$mCartId не задано значение */
	public static function SetCartId()
	{
		// Если идентификатор корзины еще не задан...
		if (self::$_mCartId == '')
		{
			// Если в сеансе есть идентификатор корзины, получаем его оттуда
			if (isset ($_SESSION['cart_id']))
			{
				self::$_mCartId = $_SESSION['cart_id'];
			}
			// Если нет, проверяем, не сохранен ли идентификатор в cookie_файле
			elseif (isset ($_COOKIE['cart_id']))
			{
				// Сохраняем идентификатор из cookie-файла
				self::$_mCartId = $_COOKIE['cart_id'];
				$_SESSION['cart_id'] = self::$_mCartId;
				
				// Регенерируем cookie-файл, чтобы он был
				// действителен 7 дней (604800 секунд)
				setcookie('cart_id', self::$_mCartId, time() + 604800);
			}
			else
			{
				/* Генерируем идентификатор корзины и сохраняем его в элементе класса 
					$_mCartId, сеансе и cookie-файле (при последующих вызовах $_mCartId
					будет получать значение из сеанса) */
				self::$_mCartId = md5(uniqid(rand(), true));
				
				// Сохраняем идентификатор корзины в сеансе 
				$_SESSION['cart_id'] = self::$_mCartId;
				// Cookie-файл будет действителен в течение 7 дней (604800 секунд)
				setcookie('cart_id', self::$_mCartId, time() + 604800);
			}
		}
	}
	
	// Возращаем идентификатор текущей корзины покупателя
	public static function GetCartId()
	{
		// Проверяем, есть ли идентификатор у корзины 
		if (!isset (self::$_mCartId))
			self::SetCartId();
		
		return self::$_mCartId;
	}
	
	// Добавляет товар в корзину покупателя 
	public static function AddProduct ($productId, $attributes)
	{
		// Составляем SQL-запрос 
		$sql = 'CALL shopping_cart_add_product(
							:cart_id, :product_id, :attributes)';
		
		// Создаем массив параметров 
		$params = array(':cart_id' => self::GetCartId(),
										':product_id' => $productId,
										':attributes' => $attributes);
		
		// Выполняем запрос 
		DatabaseHandler::Execute($sql, $params);
	}
	
	// Обновляет количество товаров в корзине покупателя 
	public static function Update($itemId, $quantity)
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_update(:item_id, :quantity)';
		
		// Создаем массив параметров 
		$params = array (':item_id' => $itemId,
											':quantity' => $quantity);
											
		// Выполняем запрос 
		DatabaseHandler::Execute($sql, $params);
	}
	
	// Удаляет товар из корзины 
	public static function RemoveProduct($itemId)
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_remove_product(:item_id)';
		
		// Создаем массив параметров 
		$params = array (':item_id' => $itemId);
		
		// Выполняем запрос 
		DatabaseHandler::Execute($sql, $params);
	}
	
	// Получает список товаров в корзине 
	public static function GetCartProducts($cartProductsType)
	{
		$sql = '';
		// При получении списка товаров для немедленной оплаты ...
		if ($cartProductsType == GET_CART_PRODUCTS)
		{
			// Составляем SQL-запрос
			$sql = 'CALL shopping_cart_get_products(:cart_id)';
		}
		// При получении списка товаров, отложенных для оплаты в будущем...
		elseif ($cartProductsType == GET_CART_SAVED_PRODUCTS)
		{
			// Составляем SQL-запрос
			$sql = 'CALL shopping_cart_get_saved_products(:cart_id)';
		}
		else
			trigger_error($cartProductsType . ' value unknown' , E_USER_ERROR);
		
		// Создаем массив параметров
		$params = array (':cart_id' => self::GetCartId());
		
		// Выполняем запрос и возращаем результаты
		return DatabaseHandler::GetAll($sql, $params);
	}
	
	/* Получает общую стоимость товаров в корзине (кроме товаров, 
		отложенных для оплаты в будущем) */
	public static function GetTotalAmount()
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_get_total_amount(:cart_id)';
		
		// Создаем массив параметров
		$params = array (':cart_id' => self::GetCartId());
		
		// Выполняем запрос и возращаем результаты
		return DatabaseHandler::GetOne($sql, $params);
	}
	
	// Переносить товар в список отложенных для оплаты в будущем
	public static function SaveProductForLater($itemId)
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_save_product_for_later(:item_id)';
		
		// Создаем массив параметров 
		$params = array (':item_id' => $itemId);
		
		// Выполняем запрос 
		DatabaseHandler::Execute($sql, $params);
	}
	
	// Возращаем товар из списка отложенных в список 
	// подлежащих немедленной оплате 
	public static function MoveProductToCart($itemId)
	{
		// Составляем SQL-запрос 
		$sql = 'CALL shopping_cart_move_product_to_cart(:item_id)';
		
		// Создаем массив параметров
		$params = array (':item_id' => $itemId);
		
		// Выполняем запрос 
		DatabaseHandler::Execute($sql, $params);
	}
	
	// Подсчитываем старые корзины в базе данных
	public static function CountOldShoppingCarts($days)
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_count_old_carts(:days)';
		
		// Создаем массив параметров 
		$params = array (':days' => $days);
		
		// Выполняем запрос и возращаем результаты
		return DatabaseHandler::GetOne($sql, $params);
	}
	
	// Удаляем старые корзины 
	public static function DeleteOldShoppingCarts($days)
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_delete_old_carts(:days)';
		
		// Создаем массив параметров 
		$params = array(':days' => $days);
		
		// Выполняем запрос
		DatabaseHandler::Execute($sql, $params);
	}
	
	// Создаем новый заказ 
	public static function CreateOrder()
	{
		// Создаем SQL-запрос
		$sql = 'CALL shopping_cart_create_order(:cart_id)';
		
		// Создаем массив параметров 
		$params = array (':cart_id' => self::GetCartId());
		
		// Выполняем запрос и возращаем результат
		return DatabaseHandler::GetOne($sql, $params);
	}
	
	// Получаем рекомендации для товаров из корзины покупателя 
	public static function GetRecommendation()
	{
		// Составляем SQL-запрос
		$sql = 'CALL shopping_cart_get_recommendations(
									:cart_id, :short_product_description_lenght)';
		
		// Создаем массив параметров 
		$params = array (':cart_id' => self::GetCartId(),
										 ':short_product_description_lenght' => 
											SHORT_PRODUCT_DESCRIPTION_LENGTH);
		
		// Выполняем запрос и возвращаем результаты
		return DatabaseHandler::GetAll($sql, $params);
	}
}