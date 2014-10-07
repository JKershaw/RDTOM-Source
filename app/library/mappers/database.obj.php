<?php

/*
 * A generic object for dealing with a mySQL databse
*/
class database
{
	protected $dbUser = "";
	protected $dbUserPw = "";
	protected $dbName = "";
	protected $dbHost = "";

	private $saved_link;
	
	function __construct() {
		$this->dbUser = DATABASE_USERNAME;
		$this->dbUserPw = DATABASE_PASSWORD;
		$this->dbName = DATABASE_NAME;
		$this->dbHost = DATABASE_HOST;
	}
	
	// run a query and return a single variable
	public function get_var($req_query) {
		$results = $this->run_query($req_query);
		if (!$results) return null;
		
		$row = mysql_fetch_row($results);
		
		return $row[0];
	}
	
	// run a query and return a single result/row as a one dimensional assoc aray
	public function get_row($req_query) {
		$results = $this->run_query($req_query);
		if (!$results) return null;
		
		// return the first row/column
		return mysql_fetch_assoc($results);
	}
	
	// run a query and return a single column as a one dimensional array
	public function get_col($req_query) {
		$results = $this->run_query($req_query);
		if (!$results) return null;
		
		while ($row = mysql_fetch_row($results)) $result_array[] = $row[0];
		
		return $result_array;
	}
	
	// run a query and return an array containing an assoc array.
	public function get_results($req_query) {
		$results = $this->run_query($req_query);
		
		if (!$results) return null;
		
		// pass results into a multidimensional array
		while ($row = mysql_fetch_assoc($results)) $result_array[] = $row;
		return $result_array;
	}
	
	// run a query and return the results
	public function run_query($req_query) {

		if (!$this->saved_link) {
			$this->saved_link = mysql_connect($this->dbHost, $this->dbUser, $this->dbUserPw) or die("Could not connect : " . mysql_error());
			
			mysql_select_db($this->dbName) or die("Could not select database");
		}
		
		$results = mysql_query($req_query) or die("Query error:<br />" . $req_query . "<br />" . mysql_error());
		
		return $results;
	}
	
	// execute multiple queries
	public function run_multi_query($req_query) {
		$link = mysqli_connect($this->dbHost, $this->dbUser, $this->dbUserPw, $this->dbName);
		
		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		
		/* execute multi query */
		$results = mysqli_multi_query($link, $req_query) or die("Query error:<br />" . $req_query . "<br />" . mysqli_error());
		
		/* close connection */
		mysqli_close($link);
		
		return $results;
	}
	
	public function does_table_exist($req_table_name) {
		
		$link = mysql_connect($this->dbHost, $this->dbUser, $this->dbUserPw) or die("Could not connect : " . mysql_error());
		
		mysql_select_db($this->dbName) or die("Could not select database");
		
		$val = mysql_query('SELECT 1 FROM ' . $req_table_name);
		
		if ($val !== FALSE) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_inserted_id() {
		return mysql_insert_id();
	}
	
	public function mysql_res($req_text) {
		global $saved_link;
		
		if (!$saved_link) {
			$saved_link = mysql_connect($this->dbHost, $this->dbUser, $this->dbUserPw) or die("Could not connect : " . mysql_error());
		}
		
		mysql_select_db($this->dbName) or die("Could not select database");
		
		$req_text = mysql_real_escape_string($req_text);
		
		return $req_text;
	}
	
	// connect to the database
	public function mysql_dbconnect() {
		$link = mysql_connect($this->dbHost, $this->dbUser, $this->dbUserPw) or die("Could not connect : " . mysql_error());
		
		mysql_select_db($this->dbName) or die("Could not select database");
	}
}
