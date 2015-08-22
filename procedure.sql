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

-- Создаем хранимую процедуру catalog_get_attributes
CREATE PROCEDURE catalog_get_attributes()
BEGIN
	SELECT attribute_id, name FROM attribute ORDER BY attribute_id;
END$$

-- Создаем хранимую процедуру catalog_add_attribute
CREATE PROCEDURE catalog_add_attribute(IN inName VARCHAR(100))
BEGIN
	INSERT INTO attribute (name) VALUES (inName);
END$$

-- Создаем хранимую процедуру catalog_update_attribute
CREATE PROCEDURE catalog_update_attribute(
	IN inAttributeId INT, IN inName VARCHAR(100))
BEGIN
	UPDATE attribute SET name = inName WHERE attribute_id = inAttributeId;
END$$

--  Создаем хранимую процедуру catalog_delete_attribute
CREATE PROCEDURE catalog_delete_attribute(IN inAttributeId INT)
BEGIN
	DECLARE attributeRowsCount INT;
	
	SELECT count(*)
	FROM attribute_value
	WHERE attribute_id = inAttributeId
	INTO attributeRowsCount;
	
	IF attributeRowsCount = 0 THEN
		DELETE FROM attribute WHERE attribute_id = inAttributeId;
		
		SELECT 1;
	ELSE
		SELECT -1;
	END IF;
END$$

-- Создаем хранимую процедуру catalog_get_attribute_details
CREATE PROCEDURE catalog_get_attribute_details(IN inAttributeId INT)
BEGIN
	SELECT attribute_id, name 
	FROM attribute
	WHERE attribute_id = inAttributeId;
END$$

-- Создаем хранимую процедуру catalog_get_attribute_values
CREATE PROCEDURE catalog_get_attribute_values(IN inAttributeId INT)
BEGIN
	SELECT attribute_value_id, value
	FROM attribute_value
	WHERE attribute_id = inAttributeId
	ORDER BY attribute_id;
END$$

-- Создаем хранимую процедуру catalog_add_attribute_value
CREATE PROCEDURE catalog_add_attribute_value(
	IN inAttributeId INT, IN inValue VARCHAR(100))
BEGIN
	INSERT INTO attribute_value (attribute_id, value)
					VALUES (inAttributeId, inValue);
END$$

-- Создаем хранимую процедуру catalog_update_attribute_value
CREATE PROCEDURE catalog_update_attribute_value(
	IN inAttributeValueId INT, IN inValue VARCHAR(100))
BEGIN 
	UPDATE attribute_value
	SET value = inValue
	WHERE attribute_value_id = inAttributeValueId;
END$$

-- Создаем хранимую процедуру catalog_delete_attribute_value
CREATE PROCEDURE catalog_delete_attribute_value(IN inAttributeValueId INT)
BEGIN 
	DECLARE productAttributeRowsCount INT;
	
	SELECT count(*)
	FROM product p 
	INNER JOIN product_attribute pa
								ON p.product_id = inAttributeValueId
	WHERE pa.attribute_value_id = inAttributeValueId
	INTO productAttributeRowsCount;
	
	IF productAttributeRowsCount = 0 THEN 
		DELETE FROM attribute_value WHERE attribute_value_id = inAttributeValueId;
	
		SELECT 1;
	ELSE
		SELECT -1;
	END IF;
END$$

-- Создаем хранимую процедуру catalog_get_category_products
CREATE PROCEDURE catalog_get_category_products (IN inCategoryId INT)
BEGIN
	SELECT p.product_id, p.name, p.description, p.price,
					p.discounted_price
	FROM product p
	INNER JOIN product_category pc 
		USING (product_id)
	WHERE pc.category_id = inCategoryId 
	ORDER BY p.product_id;
END$$

-- Cоздаем хранимую процедуру catalog_add_product_to_category
CREATE PROCEDURE catalog_add_product_to_category (IN inCategoryId INT,
	IN inName VARCHAR(100), IN inDescription VARCHAR(1000),
	IN inPrice DECIMAL(10, 2))
BEGIN
	DECLARE productLastInsertId INT;
	
	INSERT INTO product (name, description, price)
				VALUES (inName, inDescription, inPrice);
	
	SELECT LAST_INSERT_ID() INTO productLastInsertId;
	
	INSERT INTO product_category (product_id, category_id)
					VALUES (productLastInsertId, inCategoryId);
END$$

-- Создаем хранимую процедуру catalog_update_product
CREATE PROCEDURE catalog_update_product(IN inProductId INT, 
	IN inName VARCHAR(100), IN inDescription VARCHAR(1000),
	IN inPrice DECIMAL(10, 2), IN inDiscountedPrice DECIMAL(10, 2))
BEGIN
	UPDATE product
	SET name = inName, description = inDescription, price = inPrice, 
		discounted_price = inDiscountedPrice
	WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_delete_product
CREATE PROCEDURE catalog_delete_product (IN inProductId INT)
BEGIN
	DELETE FROM product_attribute WHERE product_id = inProductId;
	DELETE FROM product_category WHERE product_id = inProductId;
	DELETE FROM shopping_cart WHERE product_id = inProductId;
	DELETE FROM product WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_remove_product_from_category
CREATE PROCEDURE catalog_remove_product_from_category(
IN inProductId INT, IN inCategoryId INT)
BEGIN
	DECLARE productCategoryRowsCount INT;
	
	SELECT count(*)
	FROM product_category
	WHERE product_id = inProductId
	INTO productCategoryRowsCount;
	
	IF productCategoryRowsCount = 1 THEN
		CALL catalog_delete_product(inProductId);
		
		SELECT 0;
	ELSE
		DELETE FROM product_category
		WHERE category_id = inCategoryId AND product_id = inProductId;
		SELECT 1;
	END IF;
END$$

-- Создаем хранимую процедуру catalog_get_categories 
CREATE PROCEDURE catalog_get_categories()
BEGIN
	SELECT category_id, name, description
	FROM category
	ORDER BY category_id;
END$$

-- Создаем хранимую процедуру catalog_get_product_info
CREATE PROCEDURE catalog_get_product_info(IN inProductId INT)
BEGIN
	SELECT product_id, name, description, price, discounted_price,
					image, image_2, thumbnail, display
	FROM product
	WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_get_categories_for_product
CREATE PROCEDURE catalog_get_categories_for_product(IN inProductId INT)
BEGIN
	SELECT c.category_id, c.department_id, c.name
	FROM category c
	JOIN product_category pc
		USING (category_id)
	WHERE pc.product_id = inProductId
	ORDER BY category_id;
END$$

-- Создаем хранимую процедуру catalog_set_product_display_option
CREATE PROCEDURE catalog_set_product_display_option
	(IN inProductId INT, IN inDisplay SMALLINT)
BEGIN
	UPDATE product SET display = inDisplay WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_assing_product_to_category
CREATE PROCEDURE catalog_assign_product_to_category(
	IN inProductId INT, IN inCategoryId INT)
BEGIN
	INSERT INTO product_category (product_id, category_id)
		VALUES (inProductId, inCategoryId);
END$$

-- Создаем хранимую процедуру catalog_move_product_to_category
CREATE PROCEDURE catalog_move_product_to_category( IN inProductId INT,
	IN inSourceCategoryId INT, IN inTargetCategoryId INT)
BEGIN
	UPDATE product_category
	SET category_id = inTargetCategoryId
	WHERE product_id = inProductId
		AND category_id = inSourceCategoryId;
END$$

-- Создаем хранимую процедуру catalog_get_attributes_not_assigned_to_product
CREATE PROCEDURE catalog_get_attributes_not_assigned_to_product(
	IN inProductId INT)
BEGIN
	SELECT a.name AS attribute_name,
		av.attribute_value_id, av.value AS attribute_value
	FROM attribute_value av 
	INNER JOIN attribute a 
		USING (attribute_id)
	WHERE av.attribute_value_id NOT IN
		(SELECT attribute_value_id
			FROM product_attribute
			WHERE product_id = inProductId)
	ORDER BY attribute_name, av.attribute_value_id;
END$$

-- Создаем хранимую процедуру catalog_assign_attribute_value_to_product
CREATE PROCEDURE catalog_assign_attribute_value_to_product(
	IN inProductId INT, IN inAttributeValueId INT)
BEGIN
	INSERT INTO product_attribute (product_id, attribute_value_id)
		VALUES (inProductId, inAttributeValueId);
END$$

-- Создаем хранимую процедуру catalog_remove_product_attribute_value
CREATE PROCEDURE catalog_remove_product_attribute_value(
	IN inProductId INT, IN inAttributeValueID INT)
BEGIN
	DELETE FROM product_attribute
		WHERE product_id = inProductId AND
			attribute_value_id = inAttributeValueId;
END$$

-- Создаем хранимую процедуру catalog_set_image
CREATE PROCEDURE catalog_set_image(
	IN inProductId INT, IN inImage VARCHAR(150))
BEGIN
	UPDATE product SET image = inImage WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_set_image_2
CREATE PROCEDURE catalog_set_image_2(
	IN inProductId INT, IN inImage VARCHAR(150))
BEGIN
	UPDATE product SET image_2 = inImage WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру catalog_set_thumbnail
CREATE PROCEDURE catalog_set_thumbnail(
	IN inProductId INT, IN inThumbnail VARCHAR(150))
BEGIN
	UPDATE product
	SET thumbnail = inThumbnail
	WHERE product_id = inProductId;
END$$

-- Создаем хранимую процедуру shopping_cart_add_product
CREATE PROCEDURE shopping_cart_add_product(IN inCartId CHAR(32),
	IN inProductId INT, IN inAttributes VARCHAR(1000))
BEGIN
	DECLARE productQuantity INT;
	
	-- Получаем количество единиц заданного товара в корзине
	SELECT quantity
	FROM shopping_cart
	WHERE cart_id = inCartId
			AND product_id = inProductId
			AND attributes = inAttributes
	INTO productQuantity;
	
	-- Создаем новую запись в корзине, или увеличиваем количество
	-- товаров в уже существующей записи
	IF productQuantity IS NULL THEN 
		INSERT INTO shopping_cart(cart_id, product_id, attributes,
															quantity, added_on)
						VALUES (inCartId, inProductId, inAttributes, 1, NOW());
	ELSE
		UPDATE shopping_cart
		SET quantity = quantity + 1, buy_now = true
		WHERE cart_id = inCartId
			AND product_id = inProductId
			AND attributes = inAttributes;
	END IF;	
END$$

-- Создаем хранимую процедуру shopping_cart_update_product
CREATE  PROCEDURE shopping_cart_update(IN inItemId INT, IN inQuantity INT)
BEGIN 
	IF inQuantity > 0 THEN 
		UPDATE shopping_cart 
		SET quantity = inQuantity, added_on = NOW()
		WHERE item_id = inItemId;
	ELSE 
		CALL shopping_cart_remove_product(inItemId);
	END IF;
END$$

-- Создаем хранимую процедуру shopping_cart_remove_product 
CREATE PROCEDURE shopping_cart_remove_product(IN inItemId INT)
BEGIN
	DELETE FROM shopping_cart WHERE item_id = inItemId;
END$$

-- Создаем хранимую процедуру shopping_cart_get_products
CREATE PROCEDURE shopping_cart_get_products(IN inCartId CHAR(32))
BEGIN
	SELECT sc.item_id, p.name, sc.attributes,
		COALESCE(NULLIF(p.discounted_price, 0), p.price) AS price,
			sc.quantity,
		COALESCE(NULLIF(p.discounted_price, 0),
			p.price) * sc.quantity AS subtotal
	FROM shopping_cart sc
	INNER JOIN product p
		ON sc.product_id = p.product_id
	WHERE sc.cart_id = inCartId AND sc.buy_now;
END$$

-- Создаем хранимую процедуру shopping_cart_get_saved_products
CREATE PROCEDURE shopping_cart_get_saved_products (IN inCartId CHAR(32))
BEGIN
	SELECT sc.item_id, p.name, sc.attributes,
				COALESCE(NULLIF(p.discounted_price, 0), p.price) AS price
	FROM shopping_cart sc 
	INNER JOIN product p
							ON sc.product_id = p.product_id 
	WHERE sc.cart_id = inCartId AND NOT sc.buy_now;
END$$

-- Создаем хранимую процедуру shopping_cart_get_totatl
CREATE PROCEDURE shopping_cart_get_total_amount(IN inCartId CHAR(32))
BEGIN
	SELECT SUM(COALESCE(NULLIF(p.discounted_price, 0), p.price)
							* sc.quantity) AS total_amount
	FROM shopping_cart sc 
	INNER JOIN product p
		USING(product_id)
	WHERE sc.cart_id = inCartId AND sc.buy_now;
END$$

-- Создаем хранимую процедуру shopping_cart_save_product_for_later
CREATE PROCEDURE shopping_cart_save_product_for_later(IN inItemId INT)
BEGIN 
	UPDATE shopping_cart
	SET buy_now = false, quantity = 1
	WHERE item_id = inItemId;
END$$

-- Создаем хранимую процедуру shopping_cart_move_product_to_cart
CREATE PROCEDURE shopping_cart_move_product_to_cart(IN inItemId INT)
BEGIN
	UPDATE shopping_cart
	SET buy_now = true, added_on = NOW()
	WHERE item_id = inItemId;
END$$

-- Создаем хранимую процедуру shopping_cart_count_old_carts
CREATE PROCEDURE shopping_cart_count_old_carts(IN inDays INT)
BEGIN
	SELECT COUNT(cart_id) AS old_shopping_carts_count
	FROM (SELECT cart_id 
				FROM shopping_cart
				GROUP BY cart_id 
				HAVING DATE_SUB(NOW(), INTERVAL inDays DAY) >= MAX(added_on))
				AS old_carts;
END$$

-- Создаем хранимую процедуру shopping_cart_delete_old_carts
CREATE PROCEDURE shopping_cart_delete_old_carts(IN inDays INT)
BEGIN
	DELETE FROM shopping_cart
	WHERE cart_id IN 
		(SELECT cart_id
			FROM ( SELECT cart_id 
			FROM shopping_cart
			GROUP BY cart_id
			HAVING DATE_SUB(NOW(), INTERVAL inDays DAY) >=
			MAX(added_on))
			AS sc);
END$$

-- Создаем хранимую процедуру shopping_cart_emtpy 
CREATE PROCEDURE shopping_cart_empty(IN inCartId CHAR(32))
BEGIN
	DELETE FROM shopping_cart WHERE cart_id = inCartId;
END$$

-- Создаем хранимую процедуру shopping_cart_created_order
CREATE PROCEDURE shopping_cart_create_order(IN inCartId CHAR(32))
BEGIN
	DECLARE orderId INT;
	
	-- Вставляем в таблицу orders новую запись и получаем новый 
	-- идентификатор записи 
	INSERT INTO orders (created_on) VALUES (NOW());
	-- Получаем новый идентификатор
	SELECT LAST_INSERT_ID() INTO orderId;
	
	INSERT INTO order_details (order_id, product_id, attributes,
		product_name, quantity, unit_cost)
	SELECT orderId, p.product_id, sc.attributes, p.name, sc.quantity,
	COALESCE(NULLIF(p.discounted_price, 0), p.price) AS unit_cost
	FROM shopping_cart sc
	INNER JOIN product p
								ON sc.product_id = p.product_id
	WHERE sc.cart_id = inCartId AND sc.buy_now;
	
	-- Сохраняем общую сумму заказа
	UPDATE orders 
	SET total_amount = (SELECT SUM(unit_cost * quantity)
											FROM order_details
											WHERE order_id = orderId)
	WHERE order_id = orderId;
	
	-- Очищаем корзину покупателя
	CALL shopping_cart_empty(inCartId);
	
	-- Возвращаем идентификатор заказа 
	SELECT orderId;
END$$

-- Создаем хранимую процедуру orders_get_most_recent_orders
CREATE PROCEDURE orders_get_most_recent_orders(IN inHowMany INT)
BEGIN
	PREPARE statement FROM
		"SELECT order_id, total_amount, created_on,
			shipped_on, status, customer_name
		FROM orders
		ORDER BY created_on DESC
		LIMIT ?";
		
		SET @p1 = inHowMany;
		
		EXECUTE statement USING @p1;
END$$

-- Создаем хранимую процедуру orders_get_orders_between_dates
CREATE PROCEDURE orders_get_orders_between_dates(
	IN inStartDate DATETIME, IN inEndDate DATETIME)
BEGIN
	SELECT order_id, total_amount, created_on,
		shipped_on, status, customer_name
	FROM orders
	WHERE created_on >= inStartDate AND created_on <= inEndDate
	ORDER BY created_on DESC;
END$$

-- Создаем хранимую процедуру orders_get_orders_by_status
CREATE PROCEDURE orders_get_orders_by_status(IN inStatus INT)
BEGIN
	SELECT order_id, total_amount, created_on,
		shipped_on, status, customer_name
	FROM orders
	WHERE status = inStatus
	ORDER BY created_on DESC;
END$$

-- Создаем хранимую процедуру orders_get_order_info
CREATE PROCEDURE orders_get_order_info(IN inOrderId INT)
BEGIN
	SELECT order_id, total_amount, created_on, shipped_on, status,
		comments, customer_name, shipping_address, customer_email
	FROM orders
	WHERE order_id = inOrderId;
END$$

-- Создаем хранимую процедуру order_get_order_details
CREATE PROCEDURE orders_get_order_details(IN inOrderId INT)
BEGIN
	SELECT order_id, product_id, attributes, product_name,
		quantity, unit_cost, (quantity * unit_cost) AS subtotal
	FROM order_details
	WHERE order_id = inOrderId;
END$$

-- Создаем хранимую процедуру orders_update_order
CREATE PROCEDURE orders_update_order(IN inOrderId INT, IN inStatus INT,
	IN inComments VARCHAR(255), IN inCustomerName VARCHAR(50),
	IN inShippingAddress VARCHAR(255), IN inCustomerEmail VARCHAR(50))
BEGIN
	DECLARE currentStatus INT;
	
	SELECT status
	FROM orders
	WHERE order_id = inOrderId
	INTO currentStatus;
	
	IF inStatus != currentStatus AND (inStatus = 0 OR inStatus = 1) THEN 
		UPDATE orders SET shipped_on = NULL WHERE order_id = inOrderId;
	ELSEIF inStatus != currentStatus AND inStatus = 2 THEN
		UPDATE orders SET shipped_on = NOW() WHERE order_id = inOrderId;
	END IF;
	
	UPDATE orders
	SET status = inStatus, comments = inComments,
		customer_name = inCustomerName,
		shipping_address = inShippingAddress,
		customer_email = inCustomerEmail
	WHERE order_id = inOrderId;
END$$

-- Создаем хранимую процедуру catalog_get_recommendations
CREATE PROCEDURE catalog_get_recommendations(
	IN inProductId INT, IN inShortProductDescriptionLength INT)
BEGIN
	PREPARE statement FROM 
		"SELECT od2.product_id, od2.product_name,
			IF(CHAR_LENGTH(p.description) <= ?, p.description,
				CONCAT(LEFT(p.description, ?), '...')) AS description
		FROM order_details od1
		JOIN order_details od2 ON od1.order_id = od2.order_id
		JOIN product p ON od2.product_id = p.product_id 
		WHERE od1.product_id = ? AND
			od2.product_id != ? 
		GROUP BY od2.product_id
		ORDER BY COUNT(od2.product_id) DESC
		LIMIT 5";
		
		SET @p1 = inShortProductDescriptionLength;
		SET @p2 = inProductId;
		
		EXECUTE statement USING @p1, @p1, @p2, @p2;
END$$

-- Создаем хранимую процедуру shopping_cart_get_recommendations 
CREATE PROCEDURE shopping_cart_get_recommendations (
	IN inCartId CHAR(32), IN inShortProductDescriptionLength INT)
BEGIN
	PREPARE statement FROM 
		"-- Возвращает товары, присутствующие в списке заказов
			SELECT od1.product_id, od1.product_name,
				IF (CHAR_LENGTH(p.description) <= ?, p.description, 
						CONCAT(LEFT(p.description, ?), '...')) AS description
			FROM order_details od1 
			JOIN order_details od2
				ON od1.order_id = od2.order_id
			JOIN product p 
				ON od1.product_id = p.product_id
			JOIN shopping_cart
				ON od2.product_id = shopping_cart.product_id
			WHERE shopping_cart.cart_id = ?
				-- Среди рекомендаций не должно быть товаров 
				-- уже присутствующих в корзине покупателя
				AND od1.product_id NOT IN
				(-- Возвращаем товары, присутствующие
				 -- в корзине покупателя 
				SELECT product_id 
				FROM shopping_cart
				WHERE cart_id = ?)
			-- Группируем по product_id, чтобы подсчитать частоту встречаемости
			GROUP BY od1.product_id
			-- Упорядочиваем по убыванию частоты 
			ORDER BY COUNT(od1.product_id) DESC
			LIMIT 5";
			
	SET @p1 = inShortProductDescriptionLength;
	SET @p2 = inCartId;
	
	EXECUTE statement USING @p1, @p1, @p2, @p2;
END$$

-- Создаем хранимую процедуру customer_get_login_info
CREATE PROCEDURE customer_get_login_info(IN inEmail VARCHAR(100))
BEGIN 
	SELECT customer_id, password FROM customer WHERE email = inEmail;
END$$

-- Создаем хранимую процедуру customer_add
CREATE PROCEDURE customer_add(IN inName VARCHAR(50),
	IN inEmail VARCHAR(100), IN inPassword VARCHAR(50))
BEGIN
	INSERT INTO customer(name, email, password)
					VALUES (inName, inEmail, inPassword);
	SELECT LAST_INSERT_ID();
END$$

-- Создаем хранимую процедуру customer_get_customer
CREATE PROCEDURE customer_get_customer(IN inCustomerId INT)
BEGIN 
	SELECT customer_id, name, email, password, credit_card,
					address_1, address_2, city, region, postal_code, country,
					shipping_region_id, day_phone, eve_phone, mob_phone
	FROM customer 
	WHERE customer_id = inCustomerId;
END$$

-- Создаем хранимую процедуру customer_update_account
CREATE PROCEDURE customer_update_account(IN inCustomerId INT,
	IN inName VARCHAR(50), IN inEmail VARCHAR(100),
	IN inPassword VARCHAR(50), IN inDayPhone VARCHAR(100),
	IN inEvePhone VARCHAR(100), IN inMobPhone VARCHAR(100))
BEGIN
	UPDATE customer 
	SET name = inName, email = inEmail,
		password = inPassword, day_phone = inDayPhone,
		eve_phone = inEvePhone, mob_phone = inMobPhone
	WHERE customer_id = inCustomerId;
END$$

-- Создаем хранимую процедуру customer_update_credit_card
CREATE PROCEDURE customer_update_credit_card(
	IN inCustomerId INT, IN inCreditCard TEXT)
BEGIN
	UPDATE customer
	SET credit_card = inCreditCard
	WHERE customer_id = inCustomerId;
END$$

-- Создаем хранимую процедуру customer_get_shipping_regions
CREATE PROCEDURE customer_get_shipping_regions()
BEGIN 
	SELECT shipping_region_id, shipping_region FROM shipping_region;
END$$

-- Создаем хранимую процедуру customer_update_address 
CREATE PROCEDURE customer_update_address (IN inCustomerId INT,
	IN inAddress1 VARCHAR(100), IN inAddress2 VARCHAR(100),
	IN inCity VARCHAR(100), IN inRegion VARCHAR(100),
	IN inPostalCode VARCHAR(100), IN inCountry VARCHAR(100),
	IN inShippingRegionId INT)
BEGIN
	UPDATE customer 
	SET address_1 = inAddress1, address_2 = inAddress2, city = inCity,
		region = inRegion, postal_code = inPostalCode,
		country = inCountry, shipping_region_id = inShippingRegionId
	WHERE customer_id = inCustomerId;
END$$