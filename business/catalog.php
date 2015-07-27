<?php 
// Класс уровня логики приложения для считывания информации
// о каталоге товаров
class Catalog
{
	// Получаем список отделов
	public static function GetDepartments()
	{
		// Составляем SQL-запрос
		$sql = 'CALL catalog_get_departments_list()';
		
		// Выполняем запрос и получаем результаты 
		return DatabaseHandler::GetAll($sql);
	}
	
	// Возращает подробные сведения о выбранном отделе
	public static function GetDepartmentDetails($departmentId) 
	{
		// Составляем SQL-запрос
		$sql = 'CALL catalog_get_department_details(:department_id)';
		
		// Создает массив параметров
		$params = array(':department_id' => $departmentId);
		
		// Выполняем запрос и возращаем результат
		return DatabaseHandler::GetRow($sql, $params);
	}
	
	// Возращает список категорий, относящихся к выбранному отделу
	public static function GetCategoriesInDepartment($departmentId)
	{
		// Составляем SQL-запрос
		$sql = 'CALL catalog_get_categories_list(:department_id)';
		// Создаем массив параметров 
		$params = array (':department_id' => $departmentId);
		// Выполняем  запрос и возращаем результат
		return DatabaseHandler::GetAll($sql, $params);
	}
	
	// Возращает название и описание выбранной категории 
	public static function GetCategoryDetails($categoryId)
	{ 
		// Составляем SQL-запрос
		$sql = "CALL catalog_get_category_details(:category_id)";
		// Создаем массив параметров
		$params = array(':category_id' => $categoryId);
		// Выполняем запрос и возвращаем результат
		return DatabaseHandler::GetRow($sql, $params);
	}
	
	/* Вычисляет, сколько страниц понадобится для отображения все товаров -
	количество товаров возращает запрос $countSql */
	private static function HowManyPages($countSql, $countSqlParams)
	{
		// Создаем хеш для SQL-запроса
		$queryHashCode = md5($countSql . var_export($countSqlParams, true));
		
		// Проверяем, есть ли результаты выполнения запроса в кэше
		if (isset ($_SESSION['last_count_hash']) &&
				isset ($_SESSION['how_many_pages']) &&
				$_SESSION['last_count_hash'] == $queryHashCode)
		{
			// Извлекаем кэшированное значение 
			$how_many_pages = $_SESSION['how_many_pages'];
		}
		else
		{
			// Выполняем запрос
			$items_count = DatabaseHandler::GetOne($countSql, $countSqlParams);
			
			// Вычисляем количество страниц
			$how_many_pages = ceil($items_count/PRODUCTS_PER_PAGE);
			
			// Сохраняем данные в сеансовых переменных
			$_SESSION['last_count_hash'] = $queryHashCode;
			$_SESSION['how_many_pages'] = $how_many_pages;
		}
		
		// Возращаем количество страниц
		return $how_many_pages;
	}
	
	// Возращает список товаров, принадлежащих к заданной категории
	public static function GetProductsInCategory(
													$categoryId, $pageNo, &$rHowManyPages)
		{
			// Запрос, возвращающий количество товаров в категории
			$sql = 'CALL catalog_count_products_in_category(:category_id)';
			// Создаем массив параметров
			$params = array(':category_id' => $categoryId);
			
			// Определяем, сколько страниц понадобится для отображения товаров
			$rHowManyPages = Catalog::HowManyPages($sql, $params);
			// Определяем, какой товар будет первым
			$start_item = ($pageNo - 1) * PRODUCTS_PER_PAGE;
		
			// Получаем список товаров
			$sql = 'CALL catalog_get_products_in_category(
											:category_id, :short_product_description_length,
											:products_per_page, :start_item)';
			
			// Создаем массив параметров 
			$params = array (
				':category_id' => $categoryId,
				':short_product_description_length' =>
					SHORT_PRODUCT_DESCRIPTION_LENGTH,
				':products_per_page' => PRODUCTS_PER_PAGE,
				':start_item' => $start_item);
			
			// Выполняем запрос и возвращаем результат
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Возращаем список товаров для страницы отдела
		public static function GetProductsOnDepartment(
														$departmentId, $pageNo, &$rHowManyPages)
		{
			// Запрос, возвращающий количество товаров для страницы отдела
			$sql = 'CALL catalog_count_products_on_department(:department_id)';
			// Создаем массив параметров 
			$params = array(':department_id' => $departmentId);
			
			// Определяем, сколько страниц понадобится для отображения товаров
			$rHowManyPages = Catalog::HowManyPages($sql, $params);
			// Определяем, какой товар будет первым 
			$start_item = ($pageNo - 1) * PRODUCTS_PER_PAGE;
			
			// Получаем список товаров
			$sql = "CALL catalog_get_products_on_department(
										:department_id, :short_product_description_length,
										:products_per_page, :start_item)";
			
			// Cоздаем массив параметров 
			$params = array (
				':department_id' => $departmentId,
				':short_product_description_length' => 
					SHORT_PRODUCT_DESCRIPTION_LENGTH, 
				':products_per_page' => PRODUCTS_PER_PAGE,
				':start_item' => $start_item);
				
			// Выполняем запрос и возвращаем результат
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Возращает список товаров для главной страницы каталога
		public static function GetProductsOnCatalog($pageNo, &$rHowManyPages)
		{
			// Запрос, возвращающий количество товаров для главной страницы каталога
			$sql = "CALL catalog_count_products_on_catalog()";
			
			// Определяем, сколько страниц понадобится для отображения товаров
			$rHowManyPages = Catalog::HowManyPages($sql, null);
			
			// Определяет, какой товар будет первым
			$start_item = ($pageNo - 1) * PRODUCTS_PER_PAGE;
			
			// Получаем список товаров
			$sql = 'CALL catalog_get_products_on_catalog(
										:short_product_description_length,
										:product_per_page, :start_item)';
			
			// Создаем массив параметров
			$params = array(
				':short_product_description_length' =>
					SHORT_PRODUCT_DESCRIPTION_LENGTH,
				':product_per_page' => PRODUCTS_PER_PAGE,
				'start_item' => $start_item );
			
			//  Выполняем запрос и возвращаем результат
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Возвращаем подробную информацию о товаре
		public static function GetProductDetails($productId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_product_details(:product_id)';
			
			// Создаем массив параметров 
			$params = array(':product_id' => $productId);
			
			// Выполнять запрос и возвращаем результат
			return DatabaseHandler::GetRow($sql, $params);
		}
		
		// Возвращаем список отделов и категорий, которым принадлежит товар 
		public static function GetProductLocations($productId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_get_product_locations(:product_id)';
			
			// Создаем массив параметров
			$params = array(':product_id' => $productId);
			
			// Выполняем запрос и возращаем результат
			return DatabaseHandler::GetAll($sql, $params);
		}
}