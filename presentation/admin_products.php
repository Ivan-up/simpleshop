<?php 
// Класс, отвечающий за администрирования товаров категорий
class AdminProducts
{
	// Public-переменные, доступны в шаблоне Smarty
	public $mProductsCount;
	public $mProducts;
	public $mErrorMessage;
	public $mDepartmentId;
	public $mCategoryId;
	public $mCategoryName;
	public $mLinkToDepartmentCategoriesAdmin;
	public $mLinkToCategoryProductsAdmin;
	
	// Private - переменные
	private $_mAction;
	private $_mActionedProductId;
	
	// Конструктор класса
	public function __construct()
	{
		if (isset ($_GET['DepartmentId']))
			$this->mDepartmentId = (int)$_GET['DepartmentId'];
		else
			trigger_error('DepartmentId not set');
		
		if (isset ($_GET['CategoryId']))
			$this->mCategoryId = (int)$_GET['CategoryId'];
		else
			trigger_error('CategoryId not set');
		
		$category_details = Catalog::GetCategoryDetails($this->mCategoryId);
		foreach ($_POST as $key => $value)
			// Если нажата кнопка отправки ...
			if (substr($key, 0, 6) == 'submit')
			{
				/* Получаем позицую последнего символа '_' из имени кнопки, 
					например, strrpos('submit_edit_prod_1', '_') вернет значение 17 */
				$last_underscore = strrpos($key, '_');
				/* Получаем область действия кнопки 
					(например, 'edit_prod' из 'submit_edit_prod_1') */
				$this->_mAction  = substr($key, strlen('submit_'),
					$last_underscore - strlen('submit_'));
				/* Получаем идентификатор атрибута, к которому относится кнопка
					(номер в конце имени этой кнопки), например,
					'1' из 'submit_edit_prod_1'*/
				$this->_mActionedProductId = (int)substr($key, $last_underscore + 1);
				break;
			}
		
		$this->mLinkToDepartmentCategoriesAdmin =
			Link::ToDepartmentCategoriesAdmin($this->mDepartmentId,
																				$this->mCategoryId);
	}
	
	public function init()
	{
		// При добавлении нового товара...
		if ($this->_mAction == 'add_prod')
		{
			$product_name = $_POST['product_name'];
			$product_description = $_POST['product_description'];
			$product_price = $_POST['product_price'];
			
			if ($product_name == null)
				$this->mErrorMessage = 'Product name is empty';
			
			if ($product_description == null)
				$this->mErrorMessage = 'Product description is empty';
			
			if ($product_price == null || !is_numeric($product_price))
				$this->mErrorMessage = 'Product price must be a number!';
			
			if ($this->mErrorMessage == null)
			{
				Catalog::AddProductToCategory($this->mCategoryId, $product_name,
					$product_description, $product_price);
					
				header('Location: ' . 
								htmlspecialchars_decode(
									$this->mLinkToCategoryProductsAdmin));
			}
		}
		
		// Если мы хотим просмотреть сведения о товаре 
		if ($this->_mAction == 'edit_prod')
		{
			header('Location: ' .
							htmlspecialchars_decode(
							Link::ToProductAdmin($this->mDepartmentId,
																		$this->mCategoryId,
																		$this->_mActionedProductId)));
			exit();
		}
		$this->mProducts = Catalog::GetCategoryProducts($this->mCategoryId);
		$this->mProductsCount = count($this->mProducts);
	}
}