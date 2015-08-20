<?php 
// Представляет кредитную карту 
class SecureCard
{
	// Private-элементы, содержащие сведения о кредитной карте 
	private $_mIsDecrypted = false;
	private $_mIsEncrypred = false;
	private $_mCardHolder;
	private $_mCardNumber;
	private $_mIssueDate;
	private $_mExpiryDate;
	private $_mIssueNumber;
	private $_mCardType;
	private $_mEncryptedData;
	private $_mXmlCardData;
	
	// Конструктор класса
	public function __construct()
	{
		// Конструктор пуст 
	}
	
	// Расшифровываем данные
	public function LoadEncryptedDataAndDecrypt($newEnctypredData)
	{
		$this->_mEncryptedData = $newEnctypredData;
		$this->DecryptedData();
	}
	
	// Шифруем данные 
	public function LoadPlainDataAndEncrypt($newCardHolder, $newCardNumber,
																					$newIssueDate, $newExpiryDate,
																					$newIssueNumber, $newCardType)
	{
		$this->_mCardHolder = $newCardHolder;
		$this->_mCardNumber = $newCardNumber;
		$this->_mIssueDate = $newIssueDate;
		$this->_mExpiryDate = $newExpiryDate;
		$this->_mIssueNumber = $newIssueNumber;
		$this->_mCardType = $newCardType;
		$this->EncryptData();
	}
	
	// Создаем XML - документ с данными о карте 
	private function CreateXml()
	{
		// Помещаем данные в XML-документ
		$xml_card_data = &$this->_mXmlCardData;
		$xml_card_data = new DOMDocument();
		
		$document_root = $xml_card_data->createElement('CardDetails');
		
		$child = $xml_card_data->createElement('CardHolder');
		$child = $document_root->appendChild($child);
		$value = $xml_card_data->createTextNode($this->_mCardHolder);
		$value = $child->appendChild($value);
		
		$child = $xml_card_data->createElement('CardNumber');
		$child = $document_root->appendChild($child);
		$value = $xml_card_data->createTextNode($this->_mCardNumber);
		$value = $child->appendChild($value);
		
		$child = $xml_card_data->createElement('IssueDate');
		$child = $document_root->appendChild($child);
		$value = $xml_card_data->createTextNode($this->_mIssueDate);
		$value = $child->appendChild($value);
		
		$child = $xml_card_data->createElement('ExpiryDate');
		$child = $document_root->appendChild($child);
		$value = $xml_card_data->createTextNode($this->_mExpiryDate);
		$value = $child->appendChild($value);
		
		$child = $xml_card_data->createElement('IssueNumber');
		$child = $document_root->appendChild($child);
		$value = $xml_card_data->createTextNode($this->_mIssueNumber);
		$value = $child->appendChild($value);
		
		$child = $xml_card_data->createElement('CardType');
		$child = $document_root->appendChild($child);
		$value = $xml_card_data->createTextNode($this->_mCardType);
		$value = $child->appendChild($value);
		
		$document_root = $xml_card_data->appendChild($document_root);
	}
	
	// Извлекаем данные о карте из XML-документа 
	private function ExtractXml($decryptedData)
	{
		$xml = simplexml_load_string($decryptedData);
		$this->_mCardHolder = (string) $xml->CardHolder;
		$this->_mCardNumber = (string) $xml->CardNumber;
		$this->_mIssueDate = (string) $xml->IssueDate;
		$this->_mExpiryDate = (string) $xml->ExpiryDate;
		$this->_mIssueNumber = (string) $xml->IssueNumber;
		$this->_mCardType = (string) $xml->CardType;
	}
	
	// Защифровываем данные о кредитной карте 
	private function EncryptData()
	{
		// Помещаем в XML-документ
		$this->CreateXml();
		
		// Шифруем данные 
		$this->_mEncryptedData =
			SymmetricCrypt::Encrypt($this->_mXmlCardData->saveXML());
		
		// Устанавливаем флаг
		$this->_mIsEncrypred = true;
	}
	
	// Расшифровываем данные о карте и извлекаем их из XML-документа
	public function DecryptedData()
	{
		// Расшифровываем данные 
		$decrypted_data = SymmetricCrypt::Decrypt($this->_mEncryptedData);
		
		// Извлекаем данные из XML-документа
		$this->ExtractXml($decrypted_data);
		
		// Устанавливаем флаг расшифровки 
		$this->_mIsDecrypted = true;
	}
	
	public function __get($name)
	{
		if ($name == 'EncryptedData')
		{
			if ($this->_mIsEncrypred)
				return $this->_mEncryptedData;
			else
				throw new Exception('Data not enctrypted');
		}
		elseif ($name == 'CardNumberX')
		{
			if ($this->_mIsDecrypted)
				return 'XXXX-XXXX-XXXX-' .
					substr($this->_mCardNumber, strlen($this->_mCardNumber) - 4, 4);
			else 
				throw new Exception ('Data not descrypted');
		}
		elseif (in_array($name, array ('CardHolder', 'CardNumber', 'IssueDate',
																		'ExpiryDate', 'IssueNumber', 'CardType')))
		{
			$name = '_m' . $name;
			
			if ($this->_mIsDecrypted)
				return $this->$name;
			else
				throw new Exception('Data not decrypted');
		}
		else
		{
			throw new Exception('Property' . $name . ' not found');
		}
	}
}