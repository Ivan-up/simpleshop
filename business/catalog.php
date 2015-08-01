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
		
		// Извлекает атрибуты товаров 
		public static function GetProductAttributes($productId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_product_attributes(:product_id)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId);
			
			// Выполняем запрос и возвращаем результаты
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Получаем название отдела
		public static function GetDepartmentName($departmentId)
		{	
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_get_department_name(:department_id)';
			// Создаем массив параметров
			$params = array(':department_id' => $departmentId);
			// Выполняем запрос и возращаем результаты
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Получаем название категории 
		public static function GetCategoryName($categoryId)
		{ 
			// Составляем SQL-запрос
			$sql  = 'CALL catalog_get_category_name(:category_id)';
			// Создаем массив параметров 
			$params = array(':category_id' => $categoryId);
			// Выполняем запрос и возвращаем результаты
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Получаем название товара
		public static function GetProductName($productId)
		{ 
			// Составляем SQL-запрос
			$sql  = 'CALL catalog_get_product_name(:product_id)';
			// Создаем массив параметров 
			$params = array(':product_id' => $productId);
			// Выполняем запрос и возвращаем результаты
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Поиск в каталоге
		public static function Search($searchString, $allWords, 
																	$pageNo, &$rHowManyPages)
		{
			// Результат поиска будет массивом следующие структуры
			$search_result = array ('accepted_words' => array (),
															'ignored_words' => array (),
															'products' => array());
		
			// Возвращаем void, если строка поиска пустая
			if (empty($searchString))
				return $search_result;
			
			// Символы-разделители
			$delimiters = ',.; ';
			
			/* При первом вызове strtok мы передаем ей всю строку поиска
				и список разделителей. Она возращает первое слово строки. */
			$word = strtok($searchString, $delimiters);
			// Просматриваем строку до конца, слово за словом
			while ($word)
			{
				// Короткие слова добавляются в список ignored_words из $search_result
				if (mb_strlen($word) < FT_MIN_WORD_LEN)
					$search_result['ignored_words'][] = $word;
				else
					$search_result['accepted_words'][] = $word;
				
				// Получаем следующее слово из строки из поиска
				$word = strtok($delimiters);
			}
			
			// Если подходящиз слов нет, возращаем $search_result 
			if (count($search_result['accepted_words']) == 0)
				return $search_result;
				
			// Составляем $search_string из подходящих слов
			$search_string = '';
			
			// Если $allWords в значении 'on', добавляем символы ' +' к каждому слову
			if (strcmp($allWords, "on") == 0)
				$search_string = implode(" +", $search_result['accepted_words']);
			else
				$search_string = implode(" ", $search_result['accepted_words']);
	
			// Подсчитывае кол-во результатов поиска
			$sql = 'CALL catalog_count_search_result(:search_string, :all_words)';
			$params = array(':search_string' => $search_string,
											':all_words' => $allWords);
			// Вычисляем количество страниц, необходимое для отображения товаров
			$rHowManyPages = Catalog::HowManyPages($sql, $params);
			// Определяем номер первого товара
			$start_item = ($pageNo - 1) * PRODUCTS_PER_PAGE;
			
			// Извлекаем список подходящих товаров 
			$sql = 'CALL catalog_search(:search_string, :all_words, 
																	:short_product_description_length,
																	:products_per_page, :start_item)';
			
			// Создаем массив параметров
			$params = array (':search_string' => $search_string,
												':all_words' => $allWords,
												':short_product_description_length' =>
													SHORT_PRODUCT_DESCRIPTION_LENGTH,
												':products_per_page' => PRODUCTS_PER_PAGE,
												':start_item' => $start_item);
			
			// Выполняем запрос
			$search_result['products'] = DatabaseHandler::GetAll($sql, $params);
			
			// Возращаем результаты
			return $search_result;
		}
		
		// Извлекаем из базы данных названия и описания всех отделов
		public static function GetDepartmentsWithDescriptions()
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_departments()';
			
			// Выполняем запрос и возращаем результаты
			return DatabaseHandler::GetAll($sql);
		}
		
		// Добавляет отдел 
		public static function AddDepartment($departmentName, $departmentDescription)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_add_department(:department_name,
																					:department_description)';
			
			// Создаем массив параметров 
			$params = array (':department_name' => $departmentName,
												':department_description' => $departmentDescription);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Обновляем сведения об отделе
		public static function UpdateDepartment($departmentId, $departmentName,
																						$departmentDescription)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_update_department(:department_id, :department_name,
																							:department_description)';
			
			// Создаем массив параметров 
			$params = array (':department_id' => $departmentId,
												':department_name' => $departmentName,
												':department_description' => $departmentDescription);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Удаляет отдел
		public static function DeleteDepartment($departmentId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_delete_department(:department_id)';
			
			// Создаем массив параметров
			$params = array(':department_id' => $departmentId);
			
			// Выполняем запрос  и возращаем результаты 
			return DatabaseHandler::GetOne($sql, $params);
		}
}