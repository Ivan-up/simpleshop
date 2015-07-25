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