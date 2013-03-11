<?php
class comment
{
	private $ID;
	private $User_ID;
	private $Question_ID;
	private $Timestamp;
	private $Text;
	private $Type;
	
	function __construct(
		$req_ID,
		$req_User_ID,
		$req_Question_ID,
		$req_Timestamp,
		$req_Text,
		$req_Type)
	{
		$this->ID = $req_ID;
		$this->User_ID = $req_User_ID;
		$this->Question_ID = $req_Question_ID;
		$this->Timestamp = $req_Timestamp;
		$this->Text = $req_Text;
		$this->Type = $req_Type;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_User_ID()
	{
		return $this->User_ID;
	}
	
	public function get_Question_ID()
	{
		return $this->Question_ID;
	}
	
	public function get_Timestamp()
	{
		return $this->Timestamp;
	}
	
	public function get_Text()
	{
		return $this->Text;
	}
	
	public function get_Type()
	{
		return $this->Type;
	}
	
	public function get_author_name()
	{
		global $mydb;
		$user = $mydb->get_user_from_ID($this->User_ID);
		return $user->get_Name();
	}
}