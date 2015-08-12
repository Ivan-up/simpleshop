// Хранит экземпляр объекта XMLHttpRequest
var xmlHttp = createXmlHttpRequestObject();

// Отображаем сообщения об ошибках (если true) или отказываемся
// от использования AJAX (если false)
var showErrors = true;

// Содержит ссылку или форму, оправленную посетителем на сервер
var actionObject = "";

// Создаем экземпляр XMLHttpRequest
function createXmlHttpRequestObject()
{
	// Будет хранить объект XMLHttpRequest
	var xmlHttp;
	
	// Создаем объект XMLHttpRequest
	try
	{
		// Пытаемся создать встроенный объект XMLHttpRequest
		xmlHttp = new XMLHttpRequest();
	}
	catch(e)
	{
		// Если используется IE6 или более старый браузер
		var XmlHttpVersions = new Array(
			"MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0",
			"MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP");
			
		// Перебираем идентификаторы, пока один из них не сработает
		for (i = 0; i < XmlHttpVersions.length && !xmlHttp; i++)
		{
			try 
			{
				// Пытаемся создать объект XMLHttpRequest
				xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
			}
			catch (e) {} // Игнорируем потенциальные ошибки
		}
	}
		
	// Если объект XMLHttpRequest успешно создан, возращаем его
	if (xmlHttp)
	{
		return xmlHttp;
	}
	// Если произошла ошибка, передаем ее handlerError
	else
	{
		handleError("Error creating the XMLHttpRequest object.");
	}
}
	
// Отображение сообщение об ошибке или отказывается от использования AJAX
function handleError($message)
{
	// Игнорирует ошибки, если значение showErrors равно false
	if (showError)
	{
		// Отображаем сообщение об ошибке 
		alert("Error encountered: \n" + $message);
		return false;
	}
	// Отказываемся от использования AJAX
	else if (!actionObject.tagName)
	{
		return true;
	}
	// Отказываемся от использования AJAX и выполняем переход по ссылке
	else if (actionObject.tagName == 'A')
	{
		windows.location = actionObject.href;
	}
	// Отказываемся от использования AJAX и отправляем на сервер форму
	else if (actionObject.tagName = 'FORM')
	{
		actionObject.submit();
	}
}

// Добавляет товар в корзину
function addProductToCart(form)
{
	// Отображаем сообщение "Updating"
	document.getElementById('updating').style.visibilty = 'visible';
	
	// Переходим к классической отправке форм, если XMLHttpRequest не доступен
	if (!xmlHttp) return true;
	
	// Создаем URL для асинхронного открытия
	request = form.action + '&AjaxRequest';
	params = '';
	
	// Получаем выбранные атрибуты 
	formSelects = form.getElementsByTagName('SELECT');
	if (formSelects)
	{
		for (i = 0; i < formSelects.length; i++)
		{
			params += '&' + formSelects[i].name + '=';
			selected_index = formSelects[i].selectedIndex;
			params += encodeURIComponent(formSelects[i][selected_index].text);
		}
		
		// Пытаемся связаться с сервером
		try
		{
			// Продолжаем, только если XMLHttpRequest свободен
			if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
			{
				// Делаем запрос к серверу для проверки извлеченных данных
				xmlHttp.open("POST", request, true);
				xmlHttp.setRequestHeader("Content-Type",				"application/x-www-form-urlencoded");
				xmlHttp.onreadystatechange = addToCartStateChange;
				xmlHttp.send(params);
			}
		}
		catch (e)
		{
			// Обрабатываем ошибку
			handleError(e.toString());
		}
		
		// Останавливаем классическую отправку формы, если AJAX успешно отработал
		return false;
	}
}

// Функция, получающая HTTP-ответ
function addToCartStateChange()
{
	// Когда значение readyState равно 4, мы считываем ответ сервера
	if (xmlHttp.readyState == 4)
	{
		// Продолжаем, только если HTTP-код состояния означает "OK"
		if (xmlHttp.status == 200)
		{
			try
			{
				updateCartSummary();
			}
			catch (e)
			{
				handleError(e.toString());
			}
		}
		else
		{
			handleError(xmlHttp.statusText);
		}
	}
}

// Обрабатываем ответ сервера
function updateCartSummary()
{
	// Считываем ответ
	response = xmlHttp.responseText;
	// Ошибка сервера?
	if (response.indexOf("ERRNO") >=0 || response.indexOf("error") >= 0)
	{
		handleError(response);
	}
	else
	{
		// Извлекаем содержимое элемента div cart_summary
		var cartSummaryRegEx = /^<div class="box" id="cart-summary">?_([\s\S]*)<\/div>$/m;
		matches = cartSummaryRegEx.exec(response);
		response = matches[1];
		
		// Обновляем поле содержимого корзины и убираем сообщение об обновлении
		document.getElementById("cart-summary").innerHTML = response;
		
		// Убираем сообщение "Updating..."
		document.getElementById('updating').style.visibility = 'hidden';
	}
}
	
// Вызывается при обновлениях содержимого корзины
function executeCartAction(obj)
{
	// Отображаем сообщение "Updating..."
	document.getElementById('updating').style.visibility = 'visible';
	
	// Переходим к классической оправке формы, если XMLHttpRequest недоступен 
	if (!xmlHttp) return true;
	
	// Сохраняем ссылку на объект 
	actionObject = obj;
	
	// Инициализируем ответ и параметры
	response = '';
	params = '';
	
	// Если была активизирована ссылка, получаем ее атрибут href
	if (obj.tagName == 'A')
	{
		url = obj.href + '&AjaxRequest';
	}
	// Если была отправлена форма, получаем ее элементы 
	else 
	{
		url = obj.action + '&AjaxRequest';
		formElements = obj.getElementsByName('INPUT');
		if (formElements)
		{
			for (i = 0; i < formElements.length; i++)
			{
				params += '&' + formElements[i].name + '=';
				params += encodeURIComponent(formElements[i].value);
			}
		}
	}
	// Пытаемся связаться с сервером
	try
	{
		// Отправляем запрос, только если XMLHttpRequest не занят
		if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
		{
			xmlHttp.open("POST", url, true);
			xmlHttp.setRequestHeader("Content-Type", 
																"application/x-www-form-urlencoded");
																
			xmlHttp.onreadystatechange = cartActionStateChange;
			xmlHttp.send(params);
		}
	}
	catch (e)
	{
		// Обрабатываем ошибку
		handleError(e.toString());
	}
	
	// Останавливаем классическую отправку формы, если AJAX успешно отработал
	return false;
}

// Функция, получающая HTTP-ответ
function cartActionStateChange()
{
	// Когда readyState равно 4, считываем ответ сервера 
	if (xmlHttp.readyState == 4)
	{
		// Продолжаем, только если получен HTTP-код состояния "OK"
		if (xmlHttp.status == 200)
		{
			try
			{
				// Считываем ответ
				response = xmlHttp.responseText;
				
				// Ошибка сервера?
				if (response.indexOf("ERROR") >= 0 || response.indexOf("error") >= 0)
				{
					handleError(response);
				}
				else
				{
					// Обновляем корзину 
					document.getElementById("contents").innerHTML = response;
					// Скрываем сообщение "Updating..."
					document.getElementById('updating').style.visibility = 'hidden';
				}
			}
			catch (e)
			{
				// Обрабатываем ошибку 
				handleError(e.toString());
			}
		}
		else
		{
			// Обрабатываем ошибку 
			handleError(xmlHttp.statusText);
		}
	}
}

