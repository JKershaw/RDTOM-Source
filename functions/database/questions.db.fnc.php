<?php
function get_question_from_array($req_array)
{
	return new question(
		$req_array['ID'],
		$req_array['Text'],
		$req_array['Section'],
		$req_array['Added'],
		$req_array['Notes'],
		$req_array['Source']);
}
	
function get_question_from_ID($req_ID)
{
	global $myPDO;
	
	// prep the statement
	$statement = $myPDO->prepare('SELECT * FROM rdtom_questions WHERE ID = :ID LIMIT 1');
	$statement->execute(array(':ID' => $req_ID));
	// get an associate array of the results
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	if ($result)
	{
		return get_question_from_array($result);
	}
	else
	{
		throw new exception("Whoops, no question found with the ID of " . $req_ID);
	}
}

function get_question_count()
{
	global $myPDO;
	
	$statement = $myPDO->query('SELECT COUNT(*) FROM rdtom_questions');
	// return the results
	return $statement->fetchColumn();
}

function get_question_random()
{
	global $mydb, $myPDO, $remeber_in_session, $random_question_find_attempts;

	// if the holes table is being rebuilt, cheat
	if (!$mydb->does_table_exist("rdtom_questions_holes_map"))
	{
		return get_question_random_simple();
	}

	// at most try to find a new unique question 5 times
	for($i=0;$i<$random_question_find_attempts;$i++)
	{
		//echo "*";
		// code from http://jan.kneschke.de/projects/mysql/order-by-rand/
		$query = "
		SELECT * FROM rdtom_questions
		  JOIN (SELECT r1.Question_ID
		         FROM rdtom_questions_holes_map AS r1
		         JOIN (SELECT (RAND() *
		                      (SELECT MAX(row_id)
		                         FROM rdtom_questions_holes_map)) AS row_id)
		               AS r2
		        WHERE r1.row_id >= r2.row_id
		        ORDER BY r1.row_id ASC
		        LIMIT 1) as rows ON (id = Question_ID);";	
		
		$statement = $myPDO->query($query);
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		
		if ($result)
		{
			$question = get_question_from_array($result);
			
			// if the question hasn't already been asked recently OR we're not remebering things in the session, return it
			if (!$remeber_in_session || ($_SESSION['random_questions_asked'] && !in_array($question->get_ID(), $_SESSION['random_questions_asked'])))
			{
				return $question;
			}
		}
		else
		{
			throw new exception("Woah, either the site ran out of questions, or the database is being updated. Try reloading the page.");
		}
	}
	
	// we tried 5 times to find a unique question, and failed, so resort back to the old random question getter
	return get_question_random_simple();
}

function get_question_random_simple()
{
	global $mydb, $myPDO, $remeber_in_session;
	
	$clause = "";
	
	// exclude remebered questions
	if ($remeber_in_session)
	{
		if (count($_SESSION['random_questions_asked']) > 0)
		{
			foreach ($_SESSION['random_questions_asked'] as $ID_to_ignore)
			{
				$where_array[$ID_to_ignore] = "ID != '$ID_to_ignore'";
			}
			$clause = " WHERE " . implode(" AND ", $where_array);
		}
	}
	
	$query = "SELECT * FROM rdtom_questions" . $clause . " ORDER BY RAND() LIMIT 1";
	
	$statement = $myPDO->query($query);
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	if ($result)
	{
		$question = get_question_from_array($result);
	}
	else
	{
		throw new exception("Woah, either the site ran out of questions, or the database is being updated. Try reloading the page.");
	}	

	return $question;
}

function get_questions()
{
	global $myPDO;
	
	$statement = $myPDO->query("SELECT * FROM rdtom_questions ORDER BY Section ASC");
	$results = $statement->fetchAll(PDO::FETCH_ASSOC);
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[] = get_question_from_array($result);
		}
		
		// sort questions, naturally, by section 
		usort($out, 'compare_questions');
		
		return $out;
	}
	else
	{
		throw new exception("Whoops, no questions found in the database");
	}
}

function get_questions_from_User_ID($req_User_ID, $opt_limit = false, $opt_timelimit = false, $opt_only_wrong = false)
{
	global $myPDO, $mydb;
	
	if ($opt_only_wrong)
	{
		$clause = " AND rdtom_responses.Correct = false";
	}
	
	if ($opt_timelimit)
	{
		$clause .= " AND rdtom_responses.Timestamp >= '" . (gmmktime() - $opt_timelimit) . "' ";
		$order = " ORDER BY rdtom_responses.Timestamp Desc";
	}
	
	if ($opt_limit)
	{
		$limit = " LIMIT 0, :opt_lim ";
	}
	
	$statement = $myPDO->prepare("SELECT rdtom_questions . * 
		FROM rdtom_questions
		JOIN rdtom_responses ON rdtom_responses.Question_ID = rdtom_questions.ID
		WHERE rdtom_responses.User_ID = :ID " . $clause . $order . $limit);

	$statement->bindValue(':opt_lim', $opt_limit, PDO::PARAM_INT);
	$statement->bindValue(':ID', $req_User_ID, PDO::PARAM_INT);
	$statement->execute();
	
	$results = $statement->fetchAll();

	if ($results)
	{
		foreach ($results as $result)
		{
			$out[] = get_question_from_array($result);
		}
		return $out;
	}
	else
	{
		return false;
	}
}
?>