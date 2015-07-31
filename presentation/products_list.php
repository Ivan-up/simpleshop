<?php 
class ProductsList
{
	// Public-переменные, доступные из шаблона Smarty
	public $mPage = 1;
	public $mrTotalPages;
	public $mLinkToNextPage;
	public $mLinkToPreviousPage;
	public $mProducts;
	public $mProductListPages;
	public $mSearchDescription;
	public $mAllWords = 'off';
	public $mSearchString;
	
	// Private-переменные
	private $_mDepartmentId;
	private $_mCategoryId;
	
	// Конструктор класса
	public function __construct()
	{
		// Получаем поисковую строку и параметр AllWords из строки запроса
		if (isset ($_GET['SearchResults']))
		{
			$this->mSearchString = trim(str_replace('-', ' ', $_GET['SearchString']));
			$this->mAllWords = isset ($_GET['AllWords']) ? $_GET['AllWords'] : 'off';
		}
		
		// Получаем DepartmentId из строки запроса и преобразуем его в int 
		if (isset($_GET['DepartmentId']))
			$this->_mDepartmentId = (int)$_GET['DepartmentId'];
		
		// Получаем CategoryId из строки запроса и преобразуем его в int 
		if (isset ($_GET['CategoryId']))
			$this->_mCategoryId = (int)$_GET['CategoryId'];
		
		// Получаем номер страницы из строки запроса и преобразуем его в int 
		if (isset ($_GET['Page']))
			$this->mPage = (int)$_GET['Page'];
		
		if ($this->mPage < 1)
			trigger_error('Incorrect Page value');		
		
		// Сохраняем адрес страницы, посещенной последней
		$_SESSION['link_to_continue_shopping'] = $_SERVER['QUERY_STRING'];
	}
	
	public function init()
	{	
		/* Если выполнялся поиск, получаем список товаров, вызывая 
			метод уровня логики приложения Search() */
		if (isset($this->mSearchString))
		{	
			// Получаем результаты поиска
			$search_results = Catalog::Search($this->mSearchString,
																				$this->mAllWords,
																				$this->mPage,
																				$this->mrTotalPages);
			// Получаем список товаров
			$this->mProducts = $search_results['products'];
			// Составляем заголовок для списка товаров
			if (count($search_results['accepted_words']) > 0)
				$this->mSearchDescription = 
					'<p class="description"> Products containing <font class="words">'
					. ($this->mAllWords == 'on' ? 'all' : 'any') . '</font>'
					. ' of these  words: <font class="words">'
					. implode(', ', $search_results['accepted_words']) .
					'</font></p>';
			if (count($search_results['ignored_words']) > 0)
				$this->mSearchDescription .=
					'<p class="description">Ignored words: <font class="words">'
					. implode(', ', $search_results['ignored_words']) .
					'</font></p>';
			if (!(count($search_results['products']) > 0))
				$this->mSearchDescription .=
					'<p class="description">Your search generated no results.</p>';				
		}
		/* Если посетитель просматривает категорию, получаем список ее товаров, 
				вызывая метод уровня логики приложения GetProductsInCategory */
		elseif (isset($this->_mCategoryId))
			$this->mProducts = Catalog::GetProductsInCategory(
				$this->_mCategoryId, $this->mPage, $this->mrTotalPages);
		
		/* Если посетитель просматривает отдел, получаем список его товаров,
			вызывая метод уровня логики приложения GetProductsOnDepartment() */
		elseif (isset ($this->_mDepartmentId))
			$this->mProducts = Catalog::GetProductsOnDepartment(
				$this->_mDepartmentId, $this->mPage, $this->mrTotalPages);
				
		/* Если посетитель просматривает первую страницу, получаем список его товаров, 	вызывая метод уровня логики приложения GetProductsOnCatalog() */
		else
			$this->mProducts = Catalog::GetProductsOnCatalog(
				$this->mPage, $this->mrTotalPages);
		
		/* Если список товаров разбит на несколько страниц, отображаем 
			навигационные элементы управления */
		if ($this->mrTotalPages > 1)
		{
			// Создаем ссылку Next
			if ($this->mPage < $this->mrTotalPages)
			{
				if (isset($_GET['SearchResults']))
					$this->mLinkToNextPage =
						Link::ToSearchResults($this->mSearchString, $this->mAllWords,
																	$this->mPage + 1);
				elseif (isset($this->_mCategoryId))
					$this->mLinkToNextPage = 
						Link::ToCategory($this->_mDepartmentId, $this->_mCategoryId,
															$this->mPage + 1);
				elseif (isset($this->_mDepartmentId))
					$this->mLinkToNextPage = 
						Link::ToDepartment($this->_mDepartmentId, $this->mPage + 1);
				else
					$this->mLinkToNextPage = Link::ToIndex($this->mPage + 1);
			}
			
			// Создаем ссылку Previous
			if ($this->mPage > 1)
			{ 
				if(isset($_GET['SearchResults']))
					$this->mLinkToPreviousPage =
						Link::ToSearchResults($this->mSearchString, $this->mAllWords, 
																	$this->mPage - 1);
				if(isset($this->_mCategoryId))
					$this->mLinkToPreviousPage =
						Link::ToCategory($this->_mDepartmentId, $this->_mCategoryId,
															$this->mPage - 1);
				elseif (isset($this->_mDepartmentId))
					$this->mLinkToPreviousPage =
						Link::ToDepartment($this->_mDepartmentId, $this->mPage - 1);
				else
					$this->mLinkToPreviousPage = Link::ToIndex($this->mPage - 1);
			}
			
			// Создаем ссылки на страницы списка
			for ($i = 1; $i <= $this->mrTotalPages; $i++)
				if (isset($_GET['SearchResults']))
					$this->mProductListPages =
						Link::ToSearchResults($this->mSearchString, $this->mAllWords, $i);
				elseif (isset($this->_mCategoryId))
					$this->mProductListPages[] =
						Link::ToCategory($this->_mDepartmentId, $this->_mCategoryId, $i);
				elseif (isset($this->_mDepartmentId))
					$this->mProductListPages[] =
						Link::ToDepartment($this->_mDepartmentId, $i);
				else
					$this->mProductListPages = Link::ToIndex($i);
		}
		
		/* Перенаправляем с кодом 404, если номер запрошенной страницы 
				больше общего числа страниц списка */
		if ($this->mPage > $this->mrTotalPages && !empty($this->mrTotalPages))
		{
			// Очищаем буфер вывода
			ob_clean();
		
			// Загружаем страницу 404
			include '404.php';
			
			// Очищаем буфер вывода и прекращаем выполнение
			flush();
			ob_flush();
			ob_end_clean();
			exit();
		}
		
		// Генерируем ссылки на страницы товаров
		for ($i = 0; $i < count($this->mProducts); $i++ )
		{
			$this->mProducts[$i]['link_to_product'] = 
				Link::ToProduct($this->mProducts[$i]['product_id']);
			
			if ($this->mProducts[$i]['thumbnail'])
				$this->mProducts[$i]['thumbnail'] =
					Link::Build('product_images/' . 
					$this->mProducts[$i]['thumbnail']);
			
			$this->mProducts[$i]['attributes'] = 
				Catalog::GetProductAttributes($this->mProducts[$i]['product_id']);
		}
	}
}