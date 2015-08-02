-- Создание хранимой процедуры catalog_get_departments_list
CREATE PROCEDURE catalog_get_departments_list()
	BEGIN
	SELECT department_id, name FROM department ORDER BY department_id;
END$$

-- Создаем хранимую процедуру catalog_get_department_details
CREATE PROCEDURE catalog_get_department_details(IN inDepartmentId INT)
BEGIN
	SELECT name, description
	FROM department
	WHERE department_id = inDepartmentId;
END$$

-- Создаем хранимую процедуру catalog_get_categories_list
CREATE PROCEDURE catalog_get_categories_list (IN inDepartmentId INT)
BEGIN
	SELECT category_id, name
	FROM category 
	WHERE department_id = inDepartmentId
	ORDER BY category_id;
END$$

-- Создаем хранимую процедуру catalog_get_categories_details
CREATE PROCEDURE catalog_get_category_details(IN inCategoryId INT)
BEGIN
	SELECT name, description
	FROM category
	WHERE category_id = inCategoryId;
END$$

-- Создаем хранимую процедуру catalog_count_products_in_category 
CREATE PROCEDURE catalog_count_products_in_category(IN inCategoryId INT)
BEGIN
	SELECT COUNT(*) AS categories_count
	FROM product p 
	INNER JOIN product_category pc
		USING (product_id)
	WHERE pc.category_id = inCategoryId;
END$$

-- Создаем хранимую процедуру catalog_get_products_in_category
CREATE PROCEDURE catalog_get_products_in_category(
	IN inCategoryId INT, IN inShortProductDescriptionLength INT,
	IN inProductsPerPage INT, inStartItem INT)
BEGIN
	-- Подготавливаем оператор
	PREPARE statement FROM
		"SELECT p.product_id, p.name,
						IF(CHAR_LENGTH(p.description) <?, p.description,
							CONCAT(LEFT(p.description, ?),'...')) AS description,
						p.price, p.discounted_price, p.thumbnail
		FROM product p
		INNER JOIN product_category pc
		USING (product_id)
		WHERE pc.category_id = ?
		ORDER BY p.display DESC
		LIMIT ?, ?";
	-- Определяем параметры запроса
	SET @p1 = inShortProductDescriptionLength;
	SET @p2 = inShortProductDescriptionLength;
	SET @p3 = inCategoryId;
	SET @p4 = inStartItem;	
	SET @p5 = inProductsPerPage;
	-- Выполняем оператор
	EXECUTE statement USING @p1, @p2, @p3, @p4, @p5;
END$$

-- Создаем хранимую процедуру catalog_count_products_on_department
CREATE PROCEDURE catalog_count_products_on_department(IN inDepartmentId INT)
BEGIN
	SELECT DISTINCT COUNT(*) AS products_on_department_count
	FROM product p
	INNER JOIN product_category pc
		USING (product_id)
	INNER JOIN category c 
		USING (category_id)
	WHERE (p.display = 2 OR p.display = 3)
		AND c.department_id = inDepartmentId;
END$$

-- Создаем хранимую процедуру catalog_get_products_on_department
CREATE PROCEDURE catalog_get_products_on_department(
	IN inDepartmentId INT, IN inShortProductDescriptionLength INT,
	IN inProductsPerPage INT, IN inStartItem INT)
BEGIN
	PREPARE statement FROM
		"SELECT DISTINCT p.product_id, p.name,
						IF(CHAR_LENGTH(p.description) <= ?, p.description,
							CONCAT(LEFT(p.description, ?), '...')) AS description,
						p.price, p.discounted_price, p.thumbnail
		FROM product p
		INNER JOIN product_category pc
			USING(product_id)
		INNER JOIN category c
			USING(category_id)
		WHERE (p.display = 2 OR p.display = 3)
			AND c.department_id = ?
		ORDER BY p.display DESC
		LIMIT ?, ?";
	SET @p1 = inShortProductDescriptionLength;
	SET @p2 = inShortProductDescriptionLength;
	SET @p3 = inDepartmentId;
	SET @p4 = inStartItem;
	SET @p5 = inProductsPerPage;
	
	EXECUTE statement USING @p1, @p2, @p3, @p4, @p5;
END$$

-- Создаем хранимую процедуру catalog_count_product_on_catalog
CREATE PROCEDURE catalog_count_products_on_catalog()
BEGIN
	SELECT COUNT(*) AS products_on_catalog_count
	FROM product
	WHERE display = 1 OR display = 3;
END$$

-- Создаем хранимую процедуру catalog_get_product_on_catalog
CREATE PROCEDURE catalog_get_products_on_catalog(
	IN inShortProductDescriptionLength INT,
	IN inProductsPerPage INT, IN inStartItem INT)
BEGIN
	PREPARE statement FROM
		"SELECT product_id, name, 
						IF(CHAR_LENGTH(description) <= ?, description,
							CONCAT(LEFT(description, ?), '...')) AS description,
						price, discounted_price, thumbnail
		FROM product
		WHERE display = 1 OR display = 3
		ORDER BY display DESC
		LIMIT ?, ?";
	
	SET @p1 = inShortProductDescriptionLength;
	SET @p2 = inShortProductDescriptionLength;
	SET @p3 = inStartItem;
	SET @p4 = inProductsPerPage;
	
	EXECUTE statement USING @p1, @p2, @p3, @p4;
END$$

-- Создаем хранимую процедуру catalog_get_product_details
CREATE PROCEDURE catalog_get_product_details (IN inProductId INT)
BEGIN
	SELECT product_id, name, description,
				price, discounted_price, image, image_2
	FROM product
	WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_get_product_locations
CREATE PROCEDURE catalog_get_product_locations(IN inProductId INT)
BEGIN
	SELECT c.category_id, c.name AS category_name, c.department_id,
		( SELECT name	FROM department
			WHERE department_id = c.department_id ) AS department_name
			-- Подзапрос возращает название отдел заданной категории
	FROM category c 
	WHERE c.category_id IN
					( SELECT category_id
						FROM product_category
						WHERE product_id = inProductId);
						-- Этот подзапрос возращает идентификаторы категорий,
						-- к которым принадлежит товар
END$$

-- Создаем хранимую процедуру catalog_get_product_attbibutes
CREATE PROCEDURE catalog_get_product_attributes(IN inProductId INT)
BEGIN
	SELECT a.name AS attribute_name,
		av.attribute_value_id, av.value AS attribute_value
	FROM attribute_value av
	INNER JOIN attribute a 
		USING(attribute_id)
	WHERE av.attribute_value_id IN 
		(SELECT attribute_value_id
			FROM product_attribute
			WHERE product_id = inProductId)
	ORDER BY a.name;
END$$

-- Создаем хранимую процедуру catalog_get_department_name
CREATE PROCEDURE catalog_get_department_name(IN inDepartmentId INT)
BEGIN
	SELECT name FROM department WHERE department_id = inDepartmentId;
END$$

-- Создаем хранимую процедуру catalog_get_category_name
CREATE PROCEDURE catalog_get_category_name(IN inCategoryId INT)
BEGIN
	SELECT name FROM category WHERE category_id = inCategoryId;
END$$

-- Создаем хранимую процедуру catalog_get_product_name
CREATE PROCEDURE catalog_get_product_name(IN inProductId INT)
BEGIN
	SELECT name FROM product WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_count_search_result
CREATE PROCEDURE catalog_count_search_result(
	IN inSearchString TEXT, IN inAllWords VARCHAR(3))
BEGIN
	IF inAllWords = "on" THEN
		PREPARE statement FROM 
			"SELECT count(*)
				FROM product
				WHERE MATCH (name, description) AGAINST (? IN BOOLEAN MODE)";
	ELSE
		PREPARE statement FROM
			"SELECT count(*)
				FROM product WHERE MATCH (name, description) AGAINST (?)";
	END IF;
	
	SET @p1 = inSearchString;
	
	EXECUTE statement USING @p1;
END$$

-- Создаем хранимую процедуру catalog_search
CREATE PROCEDURE catalog_search(
	IN inSearchString TEXT, IN inAllWords VARCHAR(3),
	IN inShortProductDescriptionLength INT,
	IN inProductsPerPage INT, IN inStartItem INT)
BEGIN
	IF inAllWords = "on" THEN 
		PREPARE statement FROM 
			"SELECT product_id, name, 
								IF(CHAR_LENGTH(description) <=?, description,
										CONCAT(LEFT(description, ?),'...')) as description,
										price, discounted_price, thumbnail
			FROM product
			WHERE MATCH (name, description)
						AGAINST (? IN BOOLEAN MODE)
			ORDER BY match (name, description)
						AGAINST (? IN BOOLEAN MODE) DESC
			LIMIT ?, ?";
	ELSE 
		PREPARE statement FROM
			"SELECT product_id, name, 
							IF(CHAR_LENGTH(description) <= ?,	description,
									CONCAT(LEFT(description, ?), '...')) AS description,
									price, discounted_price, thumbnail
							FROM product 
							WHERE MATCH (name, description) AGAINST (?)
							ORDER BY MATCH (name, description) AGAINST (?) DESC
							LIMIT ?, ?";
	END IF;
	
	SET @p1 = inShortProductDescriptionLength;
	SET @p2 = inSearchString;
	SET @p3 = inStartItem;
	SET @p4 = inProductsPerPage;
	
	EXECUTE statement USING @p1, @p1, @p2, @p2, @p3, @p4;
END$$

-- Создаем хранимую процедуру catalog_get_departments
CREATE PROCEDURE catalog_get_departments()
BEGIN
	SELECT department_id, name, description
	FROM department
	ORDER BY department_id;
END$$

-- Создаем хранимую процедуру catalog_add_department
CREATE PROCEDURE catalog_add_department(
	IN inName VARCHAR(100), IN inDescription VARCHAR(1000))
BEGIN
	INSERT INTO department (name, description)
					VALUES (inName, inDescription);
END$$

-- Создаем хранимую процедуру  catalog_update_department
CREATE PROCEDURE catalog_update_department(IN inDepartmentId INT,
	IN inName VARCHAR(100), IN inDescription VARCHAR(1000))
BEGIN
	UPDATE department
	SET name = inName, description = inDescription
	WHERE department_id = inDepartmentId;
END$$

-- Создаем хранимую процедуру catalog_delete_department
CREATE PROCEDURE catalog_delete_department(IN inDepartmentId INT)
BEGIN 
	DECLARE categoryRowsCount INT;
	
	SELECT count(*)
	FROM category
	WHERE department_id = inDepartmentId 
	INTO categoryRowsCount;
	
	IF categoryRowsCount = 0 THEN
		DELETE FROM department WHERE department_id = inDepartmentId;
		
		SELECT 1;
	ELSE
		SELECT -1;
	END IF;
	
END$$

-- Cоздаение хранимой процедуры catalog_get_department_categories
CREATE PROCEDURE catalog_get_department_categories(IN inDepartmentId INT)
BEGIN
	SELECT category_id, name, description
	FROM category
	WHERE department_id = inDepartmentId
	ORDER BY category_id;
END$$

-- Создаение хранимой процедуры catalog_add_category
CREATE PROCEDURE catalog_add_category(IN inDepartmentId INT,
	IN inName VARCHAR(100), IN inDescription VARCHAR(1000))
BEGIN
INSERT INTO category (department_id, name, description)
				VALUES (inDepartmentId, inName, inDescription);
END$$

-- Создание хранимой процедуры category_update_category
CREATE PROCEDURE catalog_update_category(IN inCategoryId INT,
	IN inName VARCHAR(100), IN inDescription VARCHAR(1000))
BEGIN
	UPDATE category
	SET name = inName, description = inDescription
	WHERE category_id = inCategoryId;
END$$

-- Создание хранимой процедуры catalog_delete_category
CREATE PROCEDURE catalog_delete_category(IN inCategoryId INT)
BEGIN
	DECLARE productCategoryRowsCount INT;
	
	SELECT count(*)
	FROM product p
	INNER JOIN product_category pc
	ON p.product_id = pc.product_id
	WHERE pc.category_id = inCategoryId
	INTO productCategoryRowsCount;
	
	IF productCategoryRowsCount = 0 THEN 
		DELETE FROM category WHERE category_id = inCategoryId;
		
		SELECT 1;
	ELSE
		SELECT -1;
	END IF;
END$$