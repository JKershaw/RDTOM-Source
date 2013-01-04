<?php
// Code for speed & query tracker stuff goes here

function tracker_add_query($query, $time_taken)
{
	//if (!is_admin())
	//	return;
		
	global $tracker;
	$tracker['queries'][] = array("query" => $query, "time" => $time_taken);
}

function set_up_tracker()
{
	// starts before we've set the user
	//if (function_exists("is_admin") && is_admin())
	//{
		global $page_timer_start;
		list($usec, $sec) = explode(" ", microtime());
		$page_timer_start = ((float)$usec + (float)$sec);
	//}
}

function tracker_get_query_string()
{
	if (!is_admin())
		return;
		
	global $tracker, $page_timer_start;	
	
	list($usec, $sec) = explode(" ", microtime());
	$page_timer_end = ((float)$usec + (float)$sec);
	
	$total_page_load_time = number_format($page_timer_end - $page_timer_start, 3);
	
	// return HTML for the queries executed
	if ($tracker['queries'])
	{
		$count = count($tracker['queries']);
		if ($count == 1)
		{
			$count_string = "1 query";
		}
		else
		{
			$count_string = $count . " queries";
		}
		
		foreach ($tracker['queries'] as $query_data)
		{
			$total_query_time += $query_data['time'];
		}
		if ($total_query_time > 0)#
		{
			$percentage_query_string = " (" . number_format($total_query_time, 3) . " seconds, " . round(($total_query_time / $total_page_load_time) * 100) . "% of total)";
		}
	}
	
	$out .= " " . $total_page_load_time . " seconds, 
	<a onclick=\"$('#dev_query_string').slideToggle()\">" . $count_string . $percentage_query_string . "</a>
	<span id=\"dev_query_string\" style=\"display:none; text-align:left;\">
	";
	if ($tracker['queries'])
	{
		foreach ($tracker['queries'] as $query_data)
		{
			$out .= "<br />[" . number_format($query_data['time'], 4) . "] " . htmlentities($query_data['query']);
		}
	}
	$out .= "</span>";
	
	return $out;
}

/**
* Extends PDO and logs all queries that are executed and how long
* they take, including queries issued via prepared statements
*/
class LoggedPDO extends PDO
{
    public static $log = array();
    
    public function __construct($dsn, $username = null, $password = null) {
        parent::__construct($dsn, $username, $password);
    }
    
    /**
     * Print out the log when we're destructed. I'm assuming this will
     * be at the end of the page. If not you might want to remove this
     * destructor and manually call LoggedPDO::printLog();
     */
   // public function __destruct() {
        
    //}
    
    public function query($query) {
        $start = microtime(true);
        $result = parent::query($query);
        $time = microtime(true) - $start;
        tracker_add_query($query, $time);
        return $result;
    }

    /**
     * @return LoggedPDOStatement
     */
    /*
    public function prepare($query) {
        return new LoggedPDOStatement(parent::prepare($query));
    }
    */
}

/**
* PDOStatement decorator that logs when a PDOStatement is
* executed, and the time it took to run
* @see LoggedPDO
*/
class LoggedPDOStatement {
    /**
     * The PDOStatement we decorate
     */
    private $statement;

    public function __construct(PDOStatement $statement) {
        $this->statement = $statement;
    }

    /**
    * When execute is called record the time it takes and
    * then log the query
    * @return PDO result set
    */
    public function execute() {
        $start = microtime(true);
        $result = $this->statement->execute();
        $time = microtime(true) - $start;
        tracker_add_query($this->statement->queryString, $time);
        return $result;
    }
    /**
    * Other than execute pass all other calls to the PDOStatement object
    * @param string $function_name
    * @param array $parameters arguments
    */
    public function __call($function_name, $parameters) {
        return call_user_func_array(array($this->statement, $function_name), $parameters);
    }
}
?>