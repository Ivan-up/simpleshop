<?php 
class SymmetricCrypt
{
	// Ключ шифрования/расшифровки 
	private static $_msSecretKey = 'From Dusk Till Dawn';
	
	// Начальный вектор
	private static $_msHexaIv = 'c7098adc8d6128b5d4b4f7b2fe7f7f05';
	
	// Используем алгоритм AES 
	private static $_msCipherAlgorithm = MCRYPT_RIJNDAEL_128;
	
	/* Функция шифрует текстовую строку, полученную в качестве параметра, 
		и возвращает результат в шестнадцаричной форме */
	public static function Encrypt($plainString)
	{
		// Упаковка SymmetricCrypt::_msHevaIv  в двоичную строку 
		$binary_iv = pack('H*', self::$_msHexaIv);
		
		// Шифрование $plainString 
		$binary_encrypted_string =  mcrypt_encrypt(
																	self::$_msCipherAlgorithm, 
																	self::$_msSecretKey,
																	$plainString,
																	MCRYPT_MODE_CBC,
																	$binary_iv);
		
		// Преобразование $binary_encrypted_string в шестнадцаричную форму 
		$hexa_ecrypted_string = bin2hex($binary_encrypted_string);
		
		return $hexa_ecrypted_string;
	}
	
	/* Функция расшифровывает шестнадцаричную строку, полученную 
		в качестве параметра, и возвращает результат в шестнадцаричной форме*/
	public static function Decrypt($encryptedString)
	{
		// Упаковка Symmetric::_msHexaIv в двоичную строку 
		$binary_iv = pack('H*', self::$_msHexaIv);
		
		// Преобразование строки в байтовый массив 
		$binary_encrypted_string = pack('H*', $encryptedString);
		
		// Шифрования $plainString
		$decrypted_string = mcrypt_decrypt(
																	self::$_msCipherAlgorithm, 
																	self::$_msSecretKey,
																	$binary_encrypted_string,
																	MCRYPT_MODE_CBC,
																	$binary_iv);
		
		return $decrypted_string;
	}
	
}