<?php
class user
{
	private $ID;
	private $Name;
	private $Password_hash;
	private $Password_salt;
	private $Email;
	private $Registered_time;
	
	function __construct(
		$req_ID,
		$req_Name,
		$req_Password_hash,
		$req_Password_salt,
		$req_Email,
		$req_Registered_time)
	{
		$this->ID = $req_ID;
		$this->Name = $req_Name;
		$this->Password_hash = $req_Password_hash;
		$this->Password_salt = $req_Password_salt;
		$this->Email = $req_Email;
		$this->Registered_time = $req_Registered_time;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_Name()
	{
		return $this->Name;
	}
	
	public function get_Email()
	{
		return $this->Email;
	}
	
}