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