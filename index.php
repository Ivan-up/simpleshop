<?php 
// Подключем служебные файлы
require_once 'include/config.php';

// Загружаем шаблон страницы приложения
require_once PRESENTATION_DIR . 'application.php';

// Загружаем файл шаблонов Smarty
$application = new Application();

// Отображаем страницу
$application->display('store_front.tpl');