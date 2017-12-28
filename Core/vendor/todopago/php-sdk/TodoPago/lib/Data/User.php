<?php
namespace TodoPago\Data;

class User {
	
	protected $user = null;
	protected $password = null;
	protected $merchant = null;
	protected $apikey = null;
	
	public function __construct($user = null, $password = null){	
		if(is_array($user)) {
			$this->user = $user["user"];
			$this->password = $user["password"];
		} else {
			$this->user = $user;
			$this->password = $password;
		}
		
	}
	
	public function getUser(){
		return $this->user;
	}

	public function setUser($user){
		$this->user = $user;
	}

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
	}

	public function getMerchant(){
		return $this->merchant;
	}

	public function setMerchant($merchant){
		$this->merchant = $merchant;
	}

	public function getApikey(){
		return $this->apikey;
	}

	public function setApikey($apikey){
		$this->apikey = $apikey;
	}
	
	public function getData() {
		if($this->getUser() == null) {
			throw new \TodoPago\Exception\Data\EmptyFieldUserException();
		}
		if($this->getPassword() == null) {
			throw new \TodoPago\Exception\Data\EmptyFieldPasswordException();
		}
		
		$data = array(
			"USUARIO" => $this->getUser(),
			"CLAVE" => $this->getPassword()
		);

		return $data;
	}
}