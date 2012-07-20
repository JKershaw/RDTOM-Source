<?php
class answer
{
	private $ID;
	private $Question_ID;
	private $Text;
	private $Correct;
	private $SelectionPerc;
	
	
	function __construct(
		$req_ID,
		$req_Question_ID,
		$req_Text,
		$req_Correct)
	{
		$this->ID = $req_ID;
		$this->Question_ID = $req_Question_ID;
		$this->Text = $req_Text;
		$this->Correct = $req_Correct;
	}
	
	public function is_correct()
	{
		return ($this->Correct == 1);
	}
	
	public function get_Text()
	{
		return $this->Text;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_Question_ID()
	{
		return $this->Question_ID;
	}
	
	public function set_SelectionPerc($req_SelectionPerc)
	{
		settype($req_SelectionPerc, "integer");
		$this->SelectionPerc = $req_SelectionPerc;
	}
	
	public function get_SelectionPerc()
	{
		return $this->SelectionPerc;
	}
}