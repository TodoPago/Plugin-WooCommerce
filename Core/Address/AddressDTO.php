<?php
namespace TodoPago\Core\Address;
use \TodoPago\Core\AbstractClass\AbstractDTO as AbstractDTO;

class AddressDTO extends AbstractDTO
{	
	protected $city;
	protected $country;
	protected $postalcode;
	protected $phonenumber;
	protected $state;
	protected $street;

	public function __construct(){
	}

	public function getCity(){
		return $this->city;
	}

	public function getCountry(){
		return $this->country;
	}

	public function getPostalCode(){
		return $this->postalcode;
	}

	public function getPhoneNumber(){
		return $this->phonenumber;
	}

	public function getState(){
		return $this->state;
	}

	public function getStreet(){
		return $this->street;
	}

	public function setCity($city){ 
		$this->city = $city;
	}

	public function setCountry($country){ 
		$this->country = $country;
	}

	public function setPostalCode($postalcode){ 
		$this->postalcode = $postalcode;
	}

	public function setPhoneNumber($phonenumber){ 
		$this->phonenumber = $phonenumber;
	}

	public function setState($state){ 
		$this->state = $state;
	}

	public function setStreet($street){ 
		$this->street = $street;
	}


}


?>