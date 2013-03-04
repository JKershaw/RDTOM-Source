<?php
class term
{
	private $ID;
	private $name;
	private $description;
	private $taxonomy;
	
	
	function __construct(
		$req_ID,
		$req_name,
		$req_description,
		$req_taxonomy)
	{
		$this->ID = $req_ID;
		$this->name = $req_name;
		$this->description = $req_description;
		$this->taxonomy = $req_taxonomy;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_Name()
	{
		return $this->name;
	}
	
	public function get_Description()
	{
		return $this->description;
	}
	
	public function get_taxonomy()
	{
		return $this->taxonomy;
	}
	
	public function __toString()
	{
		return "(" . $this->get_taxonomy() . ") " . $this->get_Name();
	}
	
}