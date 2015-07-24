<?php 
//die(SMARTY_DIR . 'Smarty.class.php');
// Ссылка на библиотеку Smarty
require_once SMARTY_DIR . 'Smarty.class.php';
/* Класс, расширяющий Smarty, используется для обработки и отображения файлов Smarty */
class Application extends Smarty 
{
	// Конструктор класса
	public function __construct(){
		// Вызов конструктора Smarty
		parent::__construct();
		// Меняем папки шаблонов по умолчанию
		$this->template_dir = TEMPLATE_DIR;
		$this->compile_dir = COMPILE_DIR;
		$this->config_dir = CONFIG_DIR;
	}
}