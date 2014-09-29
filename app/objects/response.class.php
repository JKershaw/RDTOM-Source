<?php
class response
{
	private $ID;
	private $Question_ID;
	private $Answer_ID;
	private $Timestamp;
	private $Correct;
	private $IP;
	private $User_ID;
	
	function __construct(
		$req_ID,
		$req_Question_ID,
		$req_Answer_ID,
		$req_Timestamp,
		$req_Correct,
		$req_IP,
		$req_User_ID)
	{
		$this->ID = $req_ID;
		$this->Question_ID = $req_Question_ID;
		$this->Answer_ID = $req_Answer_ID;
		$this->Timestamp = $req_Timestamp;
		$this->Correct = $req_Correct;
		$this->IP = $req_IP;
		$this->User_ID = $req_User_ID;
	}
	
	public function is_correct()
	{
		return $this->Correct;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_Question_ID()
	{
		return $this->Question_ID;
	}
	
	public function get_Answer_ID()
	{
		return $this->Answer_ID;
	}
	
	public function get_Timestamp()
	{
		return $this->Timestamp;
	}

	public function get_Correct()
	{
		return $this->Correct;
	}
	
	public function get_IP()
	{
		return $this->IP;
	}
	
	public function get_User_ID()
	{
		return $this->User_ID;
	}
}