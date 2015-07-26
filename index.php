<?php 
// Подключем служебные файлы
require_once 'include/config.php';
require_once BUSINESS_DIR . 'error_handler.php';

// Задаем обработчик ошибок
//ErrorHandler::SetHandler();

// Загружаем шаблон страницы приложения
require_once PRESENTATION_DIR . 'application.php';

// Загружаем дескриптор базы данных
require_once BUSINESS_DIR . 'database_handler.php';

// Загружаем уровень логики приложения 
require_once BUSINESS_DIR . 'catalog.php';

// Загружаем файл шаблонов Smarty
$application = new Application();

// Отображаем страницу
$application->display('store_front.tpl');

// Закрываем соединение с базой данных 
DatabaseHandler::Close();
