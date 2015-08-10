<?php 
// Класс, кратко отображающий содержимое корзины
class CartSummary
{
	// Public-переменные, доступные в шаблоне Smarty
	public $mTotalAmount;
	public $mItems;
	public $mLinkToCartDetails;
	public $mEmptyCart;
	
	// Конструктор класса
	public function __construct()
	{
		/* Вычисляем общую стоимость товаров в корзине
			без учета налогов и цены доставки*/
		$this->mTotalAmount = ShoppingCart::GetTotalAmount();
		// Получаем список товаров в корзине 
		$this->mItems = ShoppingCart::GetCartProducts(GET_CART_PRODUCTS);
		if (empty($this->mItems))
			$this->mEmptyCart = true;
		else
			$this->mEmptyCart = false;
		$this->mLinkToCartDetails = Link::ToCart();
	}
}