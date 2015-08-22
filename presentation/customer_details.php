<?php 
class CustomerDetails
{
	// Public-атрибуты
	public $mEditMode = 0;
	public $mEmail;
	public $mName;
	public $mPassword;
	public $mDayPhone = null;
	public $mEvePhone = null;
	public $mMobPhone = null;
	public $mNameError = 0;
	public $mEmailAlreadyTaken = 0;
	public $mEmailError = 0;
	public $mPasswordError = 0;
	public $mPasswordConfirmError = 0;
	public $mPasswordMatchError = 0;
	public $mLinkToAccountDetails;
	public $mLinkToCancelPage;
	
	// Private - атрибуты
	private $_mErrors = 0;
	
	// Конструктор класса
	public function __construct()
	{
		// Проверяем, работаем ли мы с новым пользователем или 
		// с уже прошедшим аутентификацию
		if (Customer::IsAuthenticated())
			$this->mEditMode = 1;
		
		if ($this->mEditMode == 0)
			$this->mLinkToAccountDetails = Link::ToRegisterCustomer();
		else
			$this->mLinkToAccountDetails = Link::ToAccountDetails();
		
		// Задаем ссылку для отмены
		if (isset ($_SESSION['customer_cancel_link']))
			$this->mLinkToCancelPage = $_SESSION['customer_cancel_link'];
		else 
			$this->mLinkToCancelPage = Link::ToIndex();
		
		// Проверяем, выполнялась ли отправка данных
		if (isset($_POST['sended']))
		{ 
			// Имя дожно быть задано
			if (empty($_POST['name']))
			{
				$this->mNameError = 1;
				$this->_mErrors++;
			}
			else
				$this->mName = $_POST['name'];
			
			if ($this->mEditMode == 0 && empty ($_POST['email']))
			{
				$this->mEmailError = 1;
				$this->_mErrors++;
			}
			else 
				$this->mEmail = $_POST['email'];
			
			// Пароль не может быть пустым
			if (empty($_POST['password']))
			{
				$this->mPasswordError = 1;
				$this->_mErrors++;
			}
			else 
				$this->mPassword = $_POST['password'];
			
			// Подтверждения пароля не может быть пустым 
			if (empty($_POST['passwordConfirm']))
			{
				$this->mPasswordConfirmError = 1;
				$this->_mErrors++;
			}
			else
				$password_confirm = $_POST['passwordConfirm'];
			
			// Значение в полях пароля и подтверждения должны совпадать 
			if (!isset ($password_confirm) || 
				$this->mPassword != $password_confirm)
			{
				$this->mPasswordMatchError = 1;
				$this->_mErrors ++;
			}
			
			if ($this->mEditMode == 1)
			{
				if (!empty($_POST['dayPhone']))
					$this->mDayPhone = $_POST['dayPhone'];
				if (!empty($_POST['evePhone']))
					$this->mEvePhone = $_POST['evePhone'];
				if (!empty($_POST['mobPhone']))
					$this->mMobPhone = $_POST['mobPhone'];
			}
		}
	}
	
	public function init()
	{
		// Если данные были отправлены и в них нет ошибок 
		if ((isset($_POST['sended'])) && ($this->_mErrors == 0))
		{
			// Проверяем, есть ли зарегистрированный пользователь 
			// с таким адресом электронной почты ...
			$customer_read = Customer::GetLoginInfo($this->mEmail);
			/* ...если есть, а мы создаем новую учетную запись,
				то сообщаем об ошибке*/
			if ((!(empty($customer_read['customer_id']))) && ($this->mEditMode == 0))
			{
				$this->mEmailAlreadyTaken = 1;
				return;
			}
			
			// Работае с ноым пользователем или обновляем сведения о 
			// зарегистрированных ранее
			if ($this->mEditMode == 0)
				Customer::Add($this->mName, $this->mEmail, $this->mPassword);
			else
				Customer::UpdateAccountDetails($this->mName, $this->mEmail,
					$this->mPassword, $this->mDayPhone, $this->mEvePhone,
					$this->mMobPhone);
			
			header('Location:' . $this->mLinkToCancelPage);
			exit();
		}
		
		if ($this->mEditMode == 1 && !isset($_POST['sended']))
		{
			// Редактируем сведения о зарегистрированном пользователе
			$customer_data = Customer::Get();
			
			$this->mName = $customer_data['name'];
			$this->mEmail = $customer_data['email'];
			$this->mDayPhone = $customer_data['day_phone'];
			$this->mEvePhone = $customer_data['eve_phone'];
			$this->mMobPhone = $customer_data['mob_phone'];		
		}
	}
	
	
}