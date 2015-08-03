<?php 
// Класс, обеспечивающий администрирование значений атрибутов
class AdminAttributeValues
{
	//Public - переменные, доступные в шаблоне Smarty
	public $mAttributeValuesCount;
	public $mAttributeValues;
	public $mErrorMessage;
	public $mEditItem;
	public $mAttributeId;
	public $mAttributeName;
	public $mAttributeAdmin;
	public $mLinkToAttributeAdmin;
	
	// Private-переменные
	private $_mAction;
	private $_mActionedAttributeValueId;
	
	// Конструктор класса
	public function __construct()
	{ 
		if (isset ($_GET['AttributeId']))
			$this->mAttributeId = (int)$_GET['AttributeId'];
		else
			trigger_error('AttributeId not set');
		
		$attribute_details = Catalog::GetAttributeDetails($this->mAttributeId);
		$this->mAttributeName = $attribute_details['name'];
		
		foreach ($_POST as $key => $value)
			// Если нажата кнопка отправки...
			if (substr($key, 0, 6) == 'submit')
			{
				/* Получаем позицию последнего символа '_' из имени кнопки, 
					например, strrpos('submit_edit_val_1', '_') вернет значение 16 */
				$last_underscore = strrpos($key, '_');
			
				/* Получаем область действия кнопки 
					(например, 'edit_cat' from 'submit_edit_val_1')*/
				$this->_mAction = substr($key, strlen('submit_'), 
					$last_underscore - strlen('sumbit_'));
				
				/* Получаем идентификатор значения атрибута, к которому
					относилась нажатая кнопка (номер в конце имени кнопки),
					например, '1' из 'submit_edit_val_1'*/
				$this->_mAction = substr($key, strlen('submit_'),
					$last_underscore - strlen('submit_'));
				
				/* Получаем идентификатор значения атрибута, к которому
					относилась нажатая кнопка (номер в конце имени кнопки),
					например, '1' из 'submit_edit_val_1' */
				$this->_mActionedAttributeValueId =
					(int)substr($key, $last_underscore + 1);
				
				break;
			}
		$this->mLinkToAttributeAdmin = Link::ToAttributesAdmin();
		
		$this->mLinkToAttributeValuesAdmin =
			Link::ToAttributeValuesAdmin($this->mAttributeId);
	}
	
	public function init()
	{
		// При добавлении нового значения атрибута...
		if ($this->_mAction == 'add_val') {
			$attribute_value = $_POST['attribute_value'];
			
			if ($attribute_value == null)
				$this->mErrorMessage = 'Attribute value is empty';
			
			if ($this->mErrorMessage == null)
			{
				Catalog::AddAttributeValue($this->mAttributeId, $attribute_value);
				
				header('Location: ' .
								htmlspecialchars_decode(
									$this->mLinkToAttributeValuesAdmin));
			}
		}
		
		// При редактировании существующего значения атрибута...
		if ($this->_mAction == 'edit_val')
		{
			$this->mEditItem = $this->_mActionedAttributeValueId;
		}
		
		// При обновлении значения атрибута...
		if ($this->_mAction == 'update_val')
		{
			$attribute_value = $_POST['value'];
			
			if ($attribute_value == null)
					$this->mErrorMessage = 'Attribute value is empty';
			if ($this->mErrorMessage == null)
			{
				Catalog::UpdateAttributeValue(
					$this->_mActionedAttributeValueId, $attribute_value);
					
				header('Location: ' . 
								htmlspecialchars_decode(
									$this->mLinkToAttributeValuesAdmin));
			}
		}
			
		// При удалении значения атрибута...
		if ($this->_mAction == 'delete_val')
		{ 
			$status = 
				Catalog::DeleteAttributeValue($this->_mActionedAttributeValueId);
			
			if ($status < 0)
				$this->mErrorMessage = 'Cannot delete this attribute value. ' .
																'One or more products are using it!';
			else 
				header('Location: ' .
								htmlspecialchars_decode(
									$this->mLinkToAttributeValuesAdmin));
		}
		
		// Загружаем список значений атрибута 
		$this->mAttributeValues =
			Catalog::GetAttributeValues($this->mAttributeId);
		$this->mAttributeValuesCount = count($this->mAttributeValues);
	}
}