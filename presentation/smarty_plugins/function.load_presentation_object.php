<?php 
// Подключаемые функции из подключаемых файлов должны быть
// именоваться smarty_имя_типа
function smarty_function_load_presentation_object($params, $smarty)
{
	require_once PRESENTATION_DIR . $params['filename'] . '.php';
	
	$className = str_replace(' ', '', 
														ucfirst(str_replace('_', ' ',
																								$params['filename'])));
	// Создаем объект представления
	$obj = new $className();
	
	if (method_exists($obj, 'init')) 
	{
		$obj->init();
	}
	
	// Присваиваем переменную шаблона
	$smarty->assign($params['assign'], $obj);
}