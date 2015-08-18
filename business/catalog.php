<?php 
// Класс уровня логики приложения для считывания информации
// о каталоге товаров
class Catalog
{
	// Определяет места отображения товара
	public static $mProductDisplayOptions = array ('Default', // 0
																									'On Catalog',// 1
																									'On Department', // 2
																									'On Both'); // 3
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
		
		// Возращает список категорий отдела
		public static function GetDepartmentCategories($departmentId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_department_categories(:department_id)';
			
			// Создаем массив параметров
			$params = array (':department_id' => $departmentId);
			
			// Выполняем запрос и возращаем результаты
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Добавляем новую категорию
		public static function AddCategory($departmentId, $categoryName,
																				$categoryDescription)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_add_category(:department_id, :category_name,
																					:category_description)';
			// Создаем массив параметров 
			$params = array (':department_id' => $departmentId,
												':category_name' => $categoryName,
												':category_description' => $categoryDescription);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Обновляет категорию
		public static function UpdateCategory($categoryId, $categoryName,
																					$categoryDescription)
		{
			// Составляе SQL-запрос
			$sql = 'CALL catalog_update_category(:category_id, :category_name,
																						:category_description)';
			// Создаем массив параметров
			$params = array(':category_id' => $categoryId,
											':category_name' => $categoryName,
											':category_description' => $categoryDescription);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Удаляем категорию
		public static function DeleteCategory($categoryId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_delete_category(:category_id)';
			
			// Создаем массив параметров 
			$params = array ('category_id' => $categoryId);
			
			// Выполняем запрос и возвращаем результаты 
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Возвращаем все атрибуты
		public static function GetAttributes()
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_attributes()';
			
			// Выполняем и возвращаем результаты 
			return DatabaseHandler::GetAll($sql);
		}
		
		// Добавляет атрибут 
		public static function AddAttribute($attributeName)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_add_attribute(:attribute_name)';
			
			// Создаем массив параметров
			$params = array (':attribute_name' => $attributeName);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Обновляем имя атрибута
		public static function UpdateAttribute($attributeId, $attributeName)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_update_attribute(:attribute_id, :attribute_name)';
			
			// Создаем массив параметров 
			$params = array (':attribute_id' => $attributeId,
												':attribute_name' =>$attributeName);
			
			// Выполняем запрос
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Удаляем атрибут
		public static function DeleteAttribute($attributeId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_delete_attribute(:attribute_id)';
			
			// Создаем массив параметров 
			$params = array (':attribute_id' => $attributeId);
			
			// Выполняем запрос и возращаем результаты 
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Возращаем свеедения о выбранном атрибуте
		public static function GetAttributeDetails($attributeId)
		{
			// Создаем SQL-запрос 
			$sql = 'CALL catalog_get_attribute_details(:attribute_id)';
			
			// Создаем массив параметров
			$params = array (':attribute_id' => $attributeId);
			
			// Выполняем запрос и возращаем результаты 
			return DatabaseHandler::GetRow($sql, $params);
		}
		
		// Возвращаем значения атрибута 
		public static function GetAttributeValues($attributeId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_attribute_values(:attribute_id)';
			
			// Создаем массив параметров 
			$params = array (':attribute_id' => $attributeId);
			
			// Выполняем запрос и возвращаем результаты 
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Добавляем значение атрибута 
		public static function AddAttributeValue($attributeId, $attributeValue)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_add_attribute_value(:attribute_id, :value)';
			
			// Создаем массив параметров 
			$params = array(':attribute_id' => $attributeId,
				':value' => $attributeValue);
				
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Обновляем значение атрибута 
		public static function UpdateAttributeValue(
															$attributeValueId, $attributeValue)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_update_attribute_value(
										:attribute_value_id, :value)';
			// Создаем массив параметров
			$params = array (':attribute_value_id' => $attributeValueId,
												':value' => $attributeValue);
	
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);														
		}
		
		// Удаляет значение атрибута 
		public static function DeleteAttributeValue($attributeValueId)
		{
			// Создаем SQL - запрос 
			$sql = 'CALL catalog_delete_attribute_value(:attribute_value_id)';
			
			// Создаем массив параметров 
			$params = array (':attribute_value_id' => $attributeValueId);
			
			// Выполняем запрос и возвращаем результаты
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Получаем товары заданной категории 
		public static function GetCategoryProducts ($categoryId)
		{ 
			// Составляем SQL-запрос
			$sql = 'CALL catalog_get_category_products(:category_id)';
			
			// Создаем массив параметров 
			$params = array (':category_id' => $categoryId);
			
			// Выполняем запрос и возращаем результаты 
			return DatabaseHandler::GetAll($sql, $params);			
		}
		
		// Создаем товар и зачисляем его в категорию 
		public static function AddProductToCategory($categoryId, $productName, 
											$productDescription, $productPrice)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_add_product_to_category(:category_id, :product_name,
											:product_description, :product_price)';
			// Составляем массив параметров 
			$params = array (':category_id' => $categoryId, 
												':product_name' => $productName,
												':product_description' => $productDescription,
												':product_price' => $productPrice);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Обновляем сведения о товаре
		public static function UpdateProduct($productId, $productName,
											$productDescription, $productPrice,
											$productDiscountedPrice)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_update_product(:product_id, :product_name,
										:product_description, :product_price,
										:product_discounted_price)';
			
			// Создаем массив параметров
			$params = array(':product_id' => $productId,
											':product_name' => $productName,
											':product_description' => $productDescription,
											':product_price' => $productPrice,
											':product_discounted_price' => $productDiscountedPrice);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Удаляет товар из каталога 
		public static function DeleteProduct($productId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_delete_product(:product_id)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Удаляет товар из категории 
		public static function RemoveProductFromCategory($productId, $categoryId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_remove_product_from_category(
											:product_id, :category_id)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId,
												':category_id' => $categoryId);
			
			// Выполняем запрос и возращаем результаты 
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		// Возращаем список категорий 
		public static function GetCategories()
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_get_categories()';
			
			// Выполняем запрос и возращаем результаты 
			return DatabaseHandler::GetAll($sql);
		}
		
		// Возращает сведения о товаре 
		public static function GetProductInfo($productId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_get_product_info(:product_id)';
			
			// Создаем массив параметров 
			$params = array(':product_id' => $productId);
			
			// Выполняем запрос и возращаем результаты 
			return DatabaseHandler::GetRow($sql, $params);
		}
		
		// Возращаем список категорий, к которым относится товар
		public static function GetCategoriesForProduct($productId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_get_categories_for_product(:product_id)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId);
			
			// Выполняем запрос и возращаем результаты
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Включает товар в категорию 
		public static function SetProductDisplayOption($productId, $display)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_set_product_display_option(
								:product_id, :display)';
			
			// Создаем массив параметров 
			$params = array(':product_id' => $productId,
											':display' => $display);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Включает товар в категорию 
		public static function AssignProductToCategory($productId, $categoryId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_assign_product_to_category(
								:product_id, :category_id)';
								
			// Создаем массив параметров 
			$params = array (':product_id' => $productId,
												':category_id' => $categoryId);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Перемещает товар из одной категории в другую
		public static function MoveProductToCategory($productId, $sourceCategoryId,
														$targetCategoryId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_move_product_to_category(:product_id, 
								:source_category_id, :target_category_id)';
								
			// Создаем массив параметров 
			$params = array(':product_id' => $productId,
											':source_category_id' => $sourceCategoryId,
											':target_category_id' => $targetCategoryId);
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Возвращаем атрибуты, не присвоенные никаким товарам
		public static function GetAttributesNotAssignedToProduct($productId)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_get_attributes_not_assigned_to_product(:product_id)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId);
			
			// Выполняем запрос и возращаем результаты
			return DatabaseHandler::GetAll($sql, $params);
		}
		
		// Присваивает значение атрибута указанному товару
		public static function AssignAttributeValueToProduct($productId,
															$attributeValueId)
		{
			// Составляем SQL - запрос 
			$sql = 'CALL catalog_assign_attribute_value_to_product(
				:product_id, :attribute_value_id)';
			
			// Создаем массив параметров 
			$params = array(':product_id' => $productId,
											':attribute_value_id' => $attributeValueId);
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId,
											':attribute_value_id' => $attributeValueId);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Удаляет значение атрибута для товара 
		public static function RemoveProductAttributeValue($productId,
															$attributeValueId)
		{
			// Составляем SQL-запрос
			$sql = 'CALL catalog_remove_product_attribute_value(
								:product_id, :attribute_value_id)';
			// Создаем массив параметров 
			$params = array (':product_id' => $productId,
												':attribute_value_id' => $attributeValueId);
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Изменяет имя файла изображения товара в базе данных
		public static function SetImage ($productId, $imageName)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_set_image(:product_id, :image_name)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId, ':image_name' => $imageName);
			
			// Выполянем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Изменяет имя файла второго изображения товара в базе данных 
		public static function SetImage2($productId, $imageName)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_set_image_2(:product_id, :image_name)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId, ':image_name' => $imageName);
			
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Изменяет имя файла уменьшенного изображения товара в базе данных 
		public static function setThumbnail($productId, $thumbnailName)
		{
			// Составляем SQL-запрос 
			$sql = 'CALL catalog_set_thumbnail(:product_id, :thumbnail_name)';
			
			// Создаем массив параметров 
			$params = array (':product_id' => $productId, 
											':thumbnail_name' => $thumbnailName);
											
			// Выполняем запрос 
			DatabaseHandler::Execute($sql, $params);
		}
		
		// Получаем рекомендации для товаров 
		public static function GetRecommendations($productId)
		{
			// Составляем SQL - запрос 
			$sql = 'CALL catalog_get_recommendations(
											:product_id, :short_product_description_length)';
			
			// Создаем массив параметров
			$params = array (':product_id' => $productId,
												':short_product_description_length' =>
												SHORT_PRODUCT_DESCRIPTION_LENGTH);
			// Выполняем запрос и возращаем результаты
			return DatabaseHandler::GetAll($sql, $params);
		}
}