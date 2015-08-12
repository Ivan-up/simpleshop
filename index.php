<?php 
// Активируем сеанс
session_start();

// Создаем выходной буфер
ob_start();

// Подключем служебные файлы
require_once 'include/config.php';
require_once BUSINESS_DIR . 'error_handler.php';

// Задаем обработчик ошибок
//ErrorHandler::SetHandler();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// Загружаем шаблон страницы приложения
require_once PRESENTATION_DIR . 'application.php';
require_once PRESENTATION_DIR . 'link.php';

// Загружаем дескриптор базы данных
require_once BUSINESS_DIR . 'database_handler.php';

// Загружаем уровень логики приложения 
require_once BUSINESS_DIR . 'catalog.php';
require_once BUSINESS_DIR . 'shopping_cart.php';

// Коррекция URL
Link::CheckRequest();

// Загружаем файл шаблонов Smarty
$application = new Application();

// Обработка AJAX-запросов 
if (isset ($_GET['AjaxRequest']))
{
	// Заголовки отправляются, что предотвратить кэширование в браузерах
	header('Expires: Fri, 25 Dec 1980 00:00:00 GMT'); // Устаревшее время
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-type: text/html');
	
	if (isset ($_GET['CartAction']))
	{
		$cart_action = $_GET['CartAction'];
		
		if ($cart_action == ADD_PRODUCT)
		{
			require_once PRESENTATION_DIR . 'cart_details.php';
			
			$cart_details = new CartDetails();
			$cart_details->init();
			$application->display('cart_summary.tpl');
		}
		else
		{
			$application->display('cart_details.tpl');
		}
	}
	else 
		trigger_error ('CartAction not set', E_USER_ERROR);
}
else
{
	// Отображаем страницу
	$application->display('store_front.tpl');
}

// Закрываем соединение с базой данных 
DatabaseHandler::Close();

flush();
ob_flush();
ob_end_clean();
