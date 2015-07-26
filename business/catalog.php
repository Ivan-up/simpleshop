<?php 
// Класс уровня логики приложения для считывания информации
// о каталоге товаров
class Catalog
{
	// Получаем список отделов
	public static function GetDepartments()
	{
		// Составляем SQL-запрос
		$sql = 'CALL catalog_get_departments_list()';
		
		// Выполняем запрос и получаем результаты 
		return DatabaseHandler::GetAll($sql);
	}
}