<?php 
class Link
{
	public static function Build($link, $type = 'http')
	{
		$base = (($type == 'http' || USE_SSL == 'no') ? 'http://' : 'https://') . 
						getenv('SERVER_NAME');
		// Если константа HTTP_SERVER_PORT определена и значение отличается
		// от используемого по умолчанию...
		if (defined('HTTP_SERVER_PORT') && HTTP_SERVER_PORT != 80 &&
				strpos($base, 'https') === false)
		{
			// Добавляем номер порта
			$base .= ':' . HTTP_SERVER_PORT;
		}
		$link = $base . VIRTUAL_LOCATION . $link;
		// Escape-символы для html 
		return htmlspecialchars($link, ENT_QUOTES);
	}
	
	public static function ToDepartment($departmentId, $page = 1)
	{
		$link = self::CleanUrlText(Catalog::GetDepartmentName($departmentId)) .
						'-d' . $departmentId . '/';
		
		if ($page > 1)
			$link .= 'page-' . $page . '/';
		
		return self::Build($link);
	}
	
	public static function ToCategory($departmentId, $categoryId, $page = 1)
	{
		$link = self::CleanUrlText(Catalog::GetDepartmentName($departmentId)) .
						'-d' . $departmentId . '/' .
						self::CleanUrlText(Catalog::GetCategoryName($categoryId)) .
						'-c' . $categoryId . '/';
						
		if ($page > 1)
			$link .= 'page-' . $page . '/';
		
		return self::Build($link);
	}
	
	public static function ToProduct($productId)
	{
		$link = self::CleanUrlText(Catalog::GetProductName($productId)) .
						'-p' . $productId . '/';
						
		return self::Build($link);
	}
	
	public static function ToIndex($page = 1) 
	{
		$link = '';
		
		if ($page > 1)
			$link .= 'page-' . $page . '/';
		
		return self::Build($link);
	}
	
	public static function QueryStringToArray($queryString)
	{
		$result = array();
		if ($queryString != '')
		{
			$elements = explode('&', $queryString);
			
			foreach($elements as $key => $value)
			{
				$element = explode('=', $value);
				$result[urldecode($element[0])] = 
					isset($element[1]) ? urldecode($element[1]) : '';
			}			
		}
		return $result;
	}
	
	// Подготавливает строку к использованию в URL
	public static function CleanUrlText($string)
	{
		// Удаляем все символы, кроме a-z, 0-9, дефиса,
		// знака подчеркивания и пробела
		$not_acceptable_characters_regex = '#[^-a-zA-Z0-9_ ]#';
		$string = preg_replace($not_acceptable_characters_regex, '', $string);
		// Удаляет все пробелы в начале и в конце строки
		$string = trim($string);
		// Заменяет все дефисы, знаки подчеркивания и пробелы дефисами
		$string = preg_replace('#[-_ ]+#', '-', $string);
		return strtolower($string);
	}
	
	// Выполняет перенаправление по корректному URL в случае необходимости
	public static function CheckRequest()
	{
		$proper_url = '';
		
		if (isset ($_GET['Search']) || isset($_GET['SearchResults']) ||
				isset($_GET['CartAction']))
		{
			return;
		}		
		// Получаем правильный URL для страниц категорий
		elseif (isset ($_GET['DepartmentId']) && isset($_GET['CategoryId']))
		{
			if (isset ($_GET['Page']))
				$proper_url = self::ToCategory($_GET['DepartmentId'],
																	$_GET['CategoryId'], $_GET['Page']);
			else
				$proper_url = self::ToCategory($_GET['DepartmentId'], 
												$_GET['CategoryId']);
		}
		
		// Получаем правильный URL для страниц отделов
		elseif (isset ($_GET['DepartmentId']))
		{
			if(isset ($_GET['Page']))
				$proper_url = self::ToDepartment($_GET['DepartmentId'],
												$_GET['Page']);
			else
				$proper_url = self::ToDepartment($_GET['DepartmentId']);
		}
		
		// Получаем правильный URL для страницы товаров
		elseif (isset ($_GET['ProductId']))
		{
			$proper_url = self::ToProduct($_GET['ProductId']);
		}
		
		// Получаем правильный URL для главной страницы
		else
		{
			if (isset($_GET['Page']))
				$proper_url = self::ToIndex($_GET['Page']);
			else
				$proper_url = self::ToIndex();
		}
		
		/* Удаляем виртуальные локации из запрошенного URL,
			чтобы можно было сравнить пути */
		$requested_url = self::Build(mb_substr($_SERVER['REQUEST_URI'], 
																	mb_strlen(VIRTUAL_LOCATION)));
		
		// Перенаправление с кодом 404, если запрошенный отдел, категория
		// или товар не существует
		if (mb_strstr($proper_url, '/-'))
		{
			// Очищаем буфер вывода
			ob_clean();
			
			// Загружаем страницу 404
			include '404.php';
			
			// Очищаем буфер вывода и прекращаем выполнение
			flush();
			ob_flush();
			ob_end_clean();
			exit();
		}
		
		// Перенаправление с кодом 301 по корректному URL при необходимости 
		if ($requested_url != $proper_url)
		{
			// Очищаем буфер вывода
			ob_clean();
			
			// Выполняем перенаправление по коду 301
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $proper_url);
			
			// Очищаем буфер вывода и завершаем работу
			flush();
			ob_flush();
			ob_end_clean();
			exit();
		}
	}
	// Создание ссылки на страницу поиска
	public static function ToSearch()
	{
		return self::Build('index.php?Search');
	}
	
	// Создание ссылки на страницу с результатами поиска 
	public static function ToSearchResults($searchString, $allWords,
																					$page = 1)
	{
		$link = 'search-results/find';
		
		if (empty($searchString))
			$link .= '/';
		else 
			$link .= '-' . self::CleanUrlText($searchString) . '/';
		
		$link .= 'all-words-' . $allWords . '/';
		
		if ($page > 1)
			$link .= 'page-' . $page . '/';
		
		return self::Build($link);
	}
	
	// Создаем ссылку на страницу администрирования
	public static function ToAdmin($params = '')
	{
		$link = 'admin.php'; 
		if ($params != '')
			$link .= '?' . $params;
		return self::Build($link, 'https');
	}
	
	// Создаем ссылку для выхода
	public static function ToLogout()
	{
		return self::ToAdmin('Page=Logout');
	}
	
	// Создание ссылки на страницу администрирования отделов
	public static function ToDepartmentsAdmin()
	{
		return self::ToAdmin('Page=Departments');
	}
	
	// Создает ссылку на страницу администрирования категорий
	public static function ToDepartmentCategoriesAdmin($departmentId)
	{
		$link = 'Page=Categories&DepartmentId=' . $departmentId;
		return self::ToAdmin($link);
	}
	
	// Создаем ссылку на страницу администрирования атрибутов
	public static function ToAttributesAdmin()
	{
		return self::ToAdmin('Page=Attributes');
	}
	
	// Создаем ссылку на страницу администрирования значений атрибутов 
	public static function ToAttributeValuesAdmin($attributeId)
	{
		$link = 'Page=AttributeValues&AttributeId=' . $attributeId;
		return self::ToAdmin($link);
	}
	
	// Создаем ссылку на страницу администрирования товаров
	public static function ToCategoryProductsAdmin($departmentId, $categoryId)
	{
		$link = 'Page=Products&DepartmentId=' . $departmentId .
							'&CategoryId=' . $categoryId;
		return self::ToAdmin($link);
	}
	
	// Создает ссылку на страницу администрирования информации о товарах
	public static function ToProductAdmin($departmentId, $categoryId, $productId)
	{
		$link = 'Page=ProductDetails&DepartmentId=' . $departmentId .
						'&CategoryId=' . $categoryId . '&ProductId=' . $productId;
		return self::ToAdmin($link);
	}
	
	// Создаем ссылки для корзины покупателя
	public static function ToCart($action = 0, $target = null){
		$link = '';
		switch ($action)
		{
			case ADD_PRODUCT:
				$link = 'index.php?CartAction=' . 
									ADD_PRODUCT . '&ItemId=' . $target;
				break;
			case REMOVE_PRODUCT:
				$link = 'index.php?CartAction=' . 
									REMOVE_PRODUCT . '&ItemId=' . $target;
				break;
			case UPDATE_PRODUCTS_QUANTITIES:
				$link = 'index.php?CartAction=' . 
									UPDATE_PRODUCTS_QUANTITIES;
				break;
			case SAVE_PRODUCT_FOR_LATER:
				$link = 'index.php?CartAction=' . 
									SAVE_PRODUCT_FOR_LATER . '&ItemId=' . $target;
				break;
			case MOVE_PRODUCT_TO_CART:
				$link = 'index.php?CartAction=' . 
									MOVE_PRODUCT_TO_CART . '&ItemId=' . $target;
				break;
			default:
				$link = 'cart-details/';
		}
		return self::Build($link);
	}
	// Создаем ссылку на страницу администрирования корзин 
	public static function ToCartsAdmin()
	{
		return self::ToAdmin('Page=Carts');
	}
}