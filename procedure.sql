-- Создание хранимой процедуры catalog_get_departments_list
CREATE PROCEDURE catalog_get_departments_list()
BEGIN
SELECT department_id, name FROM department ORDER BY department_id;
END$$