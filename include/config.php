<?php 
// в SITE_ROOT содежится полный путь к папки tshirtshop
define('SITE_ROOT', dirname(dirname(__FILE__)));

// Папки приложения
define('PRESENTATION_DIR', SITE_ROOT . '/presentation/');
define('BUSINESS_DIR', SITE_ROOT . '/business/');

// Настройки, необходимые для конфигурирования Smarty
define('SMARTY_DIR', SITE_ROOT . '/libs/smarty/');
define('TEMPLATE_DIR', PRESENTATION_DIR . 'templates');
define('COMPILE_DIR', PRESENTATION_DIR . 'templates_c');
define('CONFIG_DIR', SITE_ROOT . '/include/configs');

// Эти значения должны быть равны true на этапе разработки
define('IS_WARNING_FATAL', true);
define('DEBUGGING', true);

// Типы ошибок, о которых должны составляться сообщения
define('ERROR_TYPES', E_ALL);

// Настройки отправки сообщений администраторам по электронной почте
define('SEND_ERROR_MAIL', false);
define('ADMIN_ERROR_MAIL', 'Administrator@example.com');
define('SENDMAIL_FROM', 'Errors@example.com');
ini_set('sendmail_from', SENDMAIL_FROM);

// По умолчанию мы не записываем сообщения в журнал
define('LOG_ERRORS', false);
define('LOG_ERRORS_FILE', 'F:\\иван\\OpenServer\\domains\\simpleshop');

/* Общее сообщение об ошибке, которое должно отображаться вместо 
подробной информации (если DEBUGGING равно false)*/
define('SITE_GENERIC_ERROR_MESSAGE', '<h1>TShirtShop Error!</h1>');

// Параметры соединения с базой данных
define('DB_PERSISTENCY', 'true');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_PORT', '3306');
define('DB_DATABASE', 'simpleshop');
define('PDO_DSN', 'mysql:host=' . DB_SERVER . ';port=' . DB_PORT . ';dbname=' . DB_DATABASE);

// Порт HTTP-сервера (можно пропустить, если используем порт 80)
//define('HTTP_SERVER_PORT', '80');

/* Имя вируально директории, в которой распологается сайт, например
		'/simpleshop/' если сайт работает из папки 
	http://www.example.com/simpleshop/ 
		'/' если сайт работает из папки http://www.example.com/ */
	define('VIRTUAL_LOCATION', '/');
	
// Задаем параметры, используемы при генерации списков товаров
define ('SHORT_PRODUCT_DESCRIPTION_LENGTH', 150);
define('PRODUCTS_PER_PAGE', 4);