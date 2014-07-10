<?php

class User  {

	public $id;
	public $login;
	public $first;
	public $last;
	public $password;   // hashed version
	public $salt;
	public $email;	
	public $user_status_id = User::OFFLINE;
	public $invite_id;
	public $match_id;
	
	public function encryptPassword($clearPassword) {
		$this->salt = mt_rand();
		$this->password = sha1($this->salt . $clearPassword);
	}
	
	
}
