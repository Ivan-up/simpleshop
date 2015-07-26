<?php
class StoreFront
{
	public $mSiteUrl;
	
	// Конструктор класса
	public function __construct()
	{
		$this->mSiteUrl = Link::Build('');
	}
}