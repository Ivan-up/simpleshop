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
		$link = 'index.php?DepartmentId=' . $departmentId;
		
		if ($page > 1)
			$link .= '&Page=' . $page;
		
		return self::Build($link);
	}
	
	public static function ToCategory($departmentId, $categoryId, $page = 1)
	{
		$link = 'index.php?DepartmentId=' . $departmentId .
						'&CategoryId=' . $categoryId;
		if ($page > 1)
			$link .= '&Page=' . $page;
		
		return self::Build($link);
	}
	
	public static function ToProduct($productId)
	{
		return self::Build('index.php?ProductId=' . $productId);
	}
	
	public static function ToIndex($page = 1) 
	{
		$link = '';
		
		if ($page > 1)
			$link .= 'index.php?Page=' . $page;
		
		return self::Build($link);
	}
}