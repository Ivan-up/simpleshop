<?php 
class CustomerLogin
{
	// Public-переменные
	public $mErrorMessage;
	public $mLinkToLogin;
	public $mLinkToRegisterCustomer;
	public $mEmail = "";
	
	//Конструктор класса 
	public function __construct()
	{
		if (USE_SSL == 'yes' && getenv('HTTPS') != 'on')
			$this->mLinkToLoging =
				Link::Build(mb_substr($_SERVER['REQUEST_URI'],
											mb_strlen(VIRTUAL_LOCATION)),
										'https');
		else 
			$this->mLinkToLoging =
				Link::Build(mb_substr($_SERVER['REQUEST_URI'],
											mb_strlen(VIRTUAL_LOCATION)));
		$this->mLinkToRegisterCustomer = Link::ToRegisterCustomer();
	}
	
	public function init()
	{
		// Проверяем, выполнялась ли аутентификация 
		if (isset ($_POST['Login']))
		{
			// Получаем статут аутентификации
			$login_status = Customer::IsValid($_POST['email'], $_POST['password']);
		
			switch ($login_status)
			{
				case 2:
					$this->mErrorMessage = 'Unrecognized Email.';
					$this->mEmail = $_POST['email'];
					
					break;
				case 1: 
					$this->mErrorMessage = 'Unrecognized password.';
					$this->mEmail = $_POST['email'];
					
					break;
				case 0: 
					$redirect_to_link = 
						Link::Build(mb_substr($_SERVER['REQUEST_URI'],
												mb_strlen(VIRTUAL_LOCATION)));
					header('Location:' . $redirect_to_link);
					exit();
			}
		}
	}
}