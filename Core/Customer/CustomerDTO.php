<?php
namespace TodoPago\Core\Customer;
use \TodoPago\Core\AbstractClass\AbstractDTO as AbstractDTO;

class CustomerDTO extends AbstractDTO
{	
	protected $id;
	protected $username;
	protected $user_pass;
	protected $first_name;
	protected $last_name;
	protected $user_email;
	protected $user_registered;
	protected $ip_address;

	public function __construct(){
	}

	public function getId(){
		return $this->id;
	}

	public function getUserName(){
		return $this->username;
	}

	public function getUserPass(){
		return $this->user_pass;
	}

	public function getUserEmail(){
		return $this->user_email;
	}

	public function getUserRegistered(){
		return $this->user_registered;
	}

	public function getFirstName(){
		return $this->first_name;
	}

	public function getLastName(){
		return $this->last_name;
	}

	public function setFirstName($first_name){
		$this->first_name = $first_name;
	}

	public function setLastName($last_name){
		$this->last_name = $last_name;
	}

	public function setId($id){ 
		$this->id = $id;
	}

	public function setUserName($username){ 
		$this->username = $username;
	}

	public function setUserPass($user_pass){ 
		$this->user_pass = $user_pass;
	}

	public function setUserEmail($user_email){ 
		$this->user_email = $user_email;
	}

	public function setUserRegistered($user_registered){ 
		$this->user_registered = $user_registered;
	}
	
	public function getIpAddress(){ 
		return $this->ip_address;
	}

	public function setIpAddress($ip_address){ 
		$this->ip_address = $ip_address;
	}
	
}


?>