<?php
class StoreFront
{
	public $mSiteUrl;
	// Определяем файл шаблона для содержимого страницы
	public $mContentsCell = "blank.tpl";
	
	// Конструктор класса
	public function __construct()
	{
		$this->mSiteUrl = Link::Build('');
	}
	
	// Инициализируем объект представления
	public function init()
	{
		// Загружаем подробные сведения об отделе на страницу отдела
		if (isset($_GET['DepartmentId']))
		{
			$this->mContentsCell = "department.tpl";
		}
	}
}