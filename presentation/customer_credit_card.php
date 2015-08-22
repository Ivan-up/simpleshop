<?php
class CustomerCreditCard
{
	// Public-атрибуты 
	public $mCardHolderError;
	public $mCardNumberError;
	public $mExpDateError;
	public $mCardTypesError;
	public $mPlainCreditCard;
	public $mCardTypes;
	public $mLinkToCreditCardDetails;
	public $mLinkToCancelPage;
	
	// Private-атрибуты
	private $_mErrors = 0;
	
	public function __construct()
	{
		$this->mPlainCreditCard = array ('card_holder' => '',
			'card_number' => '', 'issue_date' => '', 'expiry_date' => '',
			'issue_number' => '', 'card_type' => '', 'card_number_x' => '');
			
		// Задаем цель действия
		$this->mLinkToCreditCardDetails = Link::ToCreditCardDetails();
		
		// Задаем страницу для отмены 
		if (isset($_SESSION['customer_cancel_link']))
			$this->mLinkToCancelPage = $_SESSION['customer_cancel_link'];
		else
			$this->mLinkToCancelPage = Link::ToIndex();
		
		$this->mCardTypes = array ('MasterCard' => 'MasterCard', 
			'Visa' => 'Visa', 'Switch' => 'Switch' , 'Solo' => 'Solo',
			'American Express' => 'American Express');
			
		// Проверяем, отправились ли данные
		if (isset($_POST['sended']))
		{
			// Инициализация и проверка данных 
			if (empty($_POST['cardHolder']))
			{
				$this->mCardHolderError = 1;
				$this->_mErrors++;
			}
			else
				$this->mPlainCreditCard['card_holder'] = $_POST['cardHolder'];
			
			if (empty($_POST['cardNumber']))
			{
				$this->mCardNumberError = 1;
				$this->_mErrors++;
			}
			else 
				$this->mPlainCreditCard['card_number'] = $_POST['cardNumber'];
			
			if (empty ($_POST['expDate']))
			{
				$this->mExpDateError = 1;
				$this->_mErros++;
			}
			else
				$this->mPlainCreditCard['expiry_date'] = $_POST['expDate'];
			
			if (isset($_POST['issueDate']))
				$this->mPlainCreditCard['issue_date'] = $_POST['issueDate'];
			
			if (isset($_POST['issueNumber']))
				$this->mPlainCreditCard['issue_number'] = $_POST['issueNumber'];
			
			$this->mPlainCreditCard['card_type'] = $_POST['cardType'];
			
			if (empty ($this->mPlainCreditCard['card_type']))
			{
				$this->mCardTypeError = 1;
				$this->_mErrors++;
			}
		}
	}
	
	public function init()
	{
		if (!isset($_POST['sended']))
		{
			// Получаем информацию о кредитной карте
			$this->mPlainCreditCard = Customer::GetPlainCreditCard();
		}
		elseif ($this->_mErrors == 0)
		{
			// Обновляем информацию о кредитной карте 
			Customer::UpdateCreditCardDetails($this->mPlainCreditCard);
			
			header('Location:' . $this->mLinkToCancelPage);
			
			exit();
		}
	}
}