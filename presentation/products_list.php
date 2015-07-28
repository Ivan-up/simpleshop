<?php 
class ProductsList
{
	// Public-переменные, доступные из шаблона Smarty
	public $mPage = 1;
	public $mrTotalPages;
	public $mLinkToNextPage;
	public $mLinkToPreviousPage;
	public $mProducts;
	
	// Private-переменные
	private $_mDepartmentId;
	private $_mCategoryId;
	
	// Конструктор класса
	public function __construct()
	{
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
		/* Если посетитель просматривает категорию, получаем список ее товаров, 
				вызывая метод уровня логики приложения GetProductsInCategory */
		if (isset($this->_mCategoryId))
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
				if (isset($this->_mCategoryId))
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
		}
	}
}