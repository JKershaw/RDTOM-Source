<?php
class report
{
	private $ID;
	private $IP;
	private $Timestamp;
	private $Question_ID;
	private $User_ID;
	private $Text;
	private $Status;
	
	function __construct(
		$req_ID,
		$req_IP,
		$req_Timestamp,
		$req_Question_ID,
		$req_User_ID,
		$req_Text,
		$req_Status)
	{
		$this->ID = $req_ID;
		$this->IP = $req_IP;
		$this->Timestamp = $req_Timestamp;
		$this->Question_ID = $req_Question_ID;
		$this->User_ID = $req_User_ID;
		$this->Text = $req_Text;
		$this->Status = $req_Status;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_IP()
	{
		return $this->IP;
	}
	
	public function get_Timestamp()
	{
		return $this->Timestamp;
	}
	
	public function get_Question_ID()
	{
		return $this->Question_ID;
	}
	
	public function get_User_ID()
	{
		return $this->User_ID;
	}
	
	public function get_Text()
	{
		return $this->Text;
	}
	
	public function get_Status()
	{
		return $this->Status;
	}
	
	public function get_Status_String()
	{
		switch ($this->Status) 
		{
			case 0:
				return "OPEN";
			break;
			case 1:
				return "INCORRECT";
			break;
			case 2:
				return "FIXED";
			break;
			case 3:
				return "CLARIFIED";
			break;
			case 4:
				return "NO ACTION TAKEN";
			break;
			default:
				return "UNKNOWN";
			
		}
	}
	
	public function set_Status($req_status)
	{
		$this->Status = $req_status;
	}
}