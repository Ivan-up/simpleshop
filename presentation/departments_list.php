<?php 
// Отвечает за список отделов
class DepartmentsList
{
	/* Public-переменные, доступные в шаблоне Smarty departments_list.tpl */
	public $mSelectedDepartment = 0;
	public $mDepartments;
	
	// Конструктор считывает строку запроса как параметр
	public function __construct()
	{
		/* Если в строке запроса есть DepartmentId, мы посещаем отдел */
		if (isset ($_GET['DepartmentId']))
			$this->mSelectedDepartment = (int)$_GET['DepartmentId'];
		elseif (isset($_GET['ProductId']) && 
						isset($_SESSION['link_to_continue_shopping']))
		{
			$continue_shopping =
				Link::QueryStringToArray($_SESSION['link_to_continue_shopping']);
				
			if (array_key_exists('DepartmentId', $continue_shopping))
				$this->mSelectedDepartment =
					(int)$continue_shopping['DepartmentId'];
		}
	}
	/* Вызываем метод уровня логики приложения для считывания списка отделов
		и создания соответствующих ссылок */
	public function init()
	{
		// Получаем список отдело из уровня логики приложения
		$this->mDepartments = Catalog::GetDepartments();
		
		// Создаем ссылки на отделы
		for ($i = 0; $i < count($this->mDepartments); $i++) {
			$this->mDepartments[$i]['link_to_department'] =
				Link::ToDepartment($this->mDepartments[$i]['department_id']);
		}
	}
}