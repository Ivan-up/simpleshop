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