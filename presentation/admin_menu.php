<?php 
class AdminMenu
{
	public $mLinkToStoreAdmin;
	public $mLinkToAttributesAdmin;
	public $mLinkToStoreFront;
	public $mLinkToLogout;
	
	public function __construct()
	{
		$this->mLinkToStoreAdmin = Link::ToAdmin();
		$this->mLinkToAttributesAdmin = Link::ToAttributesAdmin();
		$this->mLinkToStoreFront = Link::ToIndex();
		$this->mLinkToLogout = Link::ToLogout();
	}
}