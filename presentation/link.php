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
	
	public static function ToDepartment($departmentId)
	{
		$link = 'index.php?DepartmentId=' . $departmentId;
		return self::Build($link);
	}
}