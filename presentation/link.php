<?php 
class Link
{
	public static function Build($link)
	{
		$base = 'http://' . getenv('SERVER_NAME');
		// Если константа HTTP_SERVER_PORT определена и значение отличается
		// от используемого по умолчанию...
		if (defined('HTTP_SERVER_PORT') && HTTP_SERVER_PORT != 80)
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
}