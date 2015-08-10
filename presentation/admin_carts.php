<?php 
// Класса, отвечающий за администрирования корзин покупателей
class AdminCarts
{
	// Public - переменные, доступные в шаблоне smarty
	public $mMessage;
	public $mDaysOptions = array ( 0 => 'All shopping carts',
																	1 => 'One day old',
																	10 => 'Ten days old',
																	20 => 'Twenty days old',
																	30 => 'Thirty days old',
																	90 => 'Ninety days old');
	public $mSelectedDaysNumber = 0;
	public $mLinkToCartsAdmin;
	
	// Private-переменные 
	public $_mAction = '';
	
	// Конструктор класса
	public function __construct()
	{ 
		foreach ($_POST as $key => $value)
			// При щелчке на кнопке отправки...
			if (substr($key, 0, 6) == 'submit')
			{
				// Получаем область действия кнопки 
				$this->_mAction = substr($key, strlen('submit_'), strlen($key));
				
				// Получаем выбранное количество дней
				if (isset ($_POST['days']))
					$this->mSelectedDaysNumber = (int) $_POST['days'];
				else
					trigger_error('days value not set');
			}
			$this->mLinkToCartsAdmin = Link::ToCartsAdmin();
	}
	
	public function init()
	{
		// При подсчете корзин...
		if ($this->_mAction == 'count')
		{
			$count_old_carts =
				ShoppingCart::CountOldShoppingCarts($this->mSelectedDaysNumber);
			
			if ($count_old_carts == 0)
				$count_old_carts = 'no';
			
			$this->mMessage = 'There are ' . $count_old_carts .
				' old shopping carts (selected option:' .
				$this->mDaysOptions[$this->mSelectedDaysNumber] .
				').';
		}
		
		// При удалении корзин...
		if ($this->_mAction == 'delete')
		{
			$this->mDeletedCarts = 
				ShoppingCart::DeleteOldShoppingCarts($this->mSelectedDaysNumber);
			
			$this->mMessage = 'The old shopping carts were removed from the 
				database (selected option: ' .
				$this->mDaysOptions[$this->mSelectedDaysNumber] . ').';
		}
	}
}