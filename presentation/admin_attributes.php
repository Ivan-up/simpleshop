<?php 
// Класс, обеспечивающий администрирование атрибутов
class AdminAttributes
{
	// Public - переменные, доступны в шаблоне Smarty
	public $mAttributesCount;
	public $mAttributes;
	public $mErrorMessage;
	public $mEditItem;
	public $mLinkToAttributesAdmin;
	
	// Private-переменные
	private $_mAction;
	private $_mActionedAttributeId;
	
	// Конструктор класса
	public function __construct()
	{
		// Просматриваем список переданных переменных
		foreach ($_POST as $key => $value)
			// Если нажата кнопка отправки...
			if(substr($key, 0, 6) == 'submit')
			{
				/* Получаем позицую последнего символа '_' из имени кнопки, 
					например, strrpos('submit_edit_attr_1', '_') вернет значение 17*/
				$last_underscore = strrpos($key, '_');
				/* Получаем область действия кнопки 
					(например, 'edit_atrr' из 'submit_edit_attr_1')*/
				$this->_mAction = substr($key, strlen('submit'),
																	$last_underscore - strlen('submit'));
				/* Получаем область действия кнопки 
						(например, 'edit_attr' из 'submit_edit_attr_1')*/
				$this->_mAction = substr($key, strlen('submit_'),
																	$last_underscore - strlen('submit_'));
				
				/* Получаем идентификатор атрибута, которому относится кнопка
					(номер в конце имени этой кнопки), например, 
					'1' из 'submit_edit_attr_1'*/
				$this->_mActionedAttributeId = substr($key, $last_underscore + 1);
				
				break;
			}				
		$this->mLinkToAttributesAdmin = Link::ToAttributesAdmin();
	}
	
	public function init()
	{
		// При добавлении нового атрибута...
		if ($this->_mAction == 'add_attr')
		{
			$attribute_name = $_POST['attribute_name'];
			
			if($attribute_name == null)
				$this->mErrorMessage = 'Attribute name required';
			
			if($this->mErrorMessage == null)
			{
				Catalog::AddAttribute($attribute_name);
				header('Location: ' . $this->mLinkToAttributesAdmin);
			}
		}
		
		// При редактировании существующего атрибута...
		if ($this->_mAction == 'edit_attr')
			$this->mEditItem = $this->_mActionedAttributeId;
		
		// При редактировании существующего атрибута
		if ($this->_mAction == 'update_attr') 
		{
			$attribute_name = $_POST['name'];
			if (attribute_name == null)
				$this->mErrorMessage = 'Attribute name requored';
			
			if ($this->mErrorMessage == null)
			{
				Catalog::UpdateAttribute($this->_mActionedAttributeId,
					$attribute_name);
				header('Location: ' . $this->mLinkToAttributesAdmin);
			}
		}
		
		// При удалении атрибута 
		if ($this->_mAction == 'delete_attr')
		{
			$status = Catalog::DeleteAttribute($this->_mActionedAttributeId);
			
			if ($status < 0 )
				$this->mErrorMessage = 
					'Attribute has one ore more values and cannot be deleted';
			else
				header('Location: ' . $this->mLinkToAttributesAdmin);
		}
		
		// При редактировании значений атрибута...
		if ($this->_mAction == 'edit_val')
		{
			header('Location: ' . 
							htmlspecialchars_decode(
								Link::ToAttributeValuesAdmin(
									$this->_mActionedAttributeId)));
			exit;
		}
		
		// Загружаем список атрибутов 
		$this->mAttributes = Catalog::GetAttributes();
		$this->mAttributesCount = count($this->mAttributes);
	}
}