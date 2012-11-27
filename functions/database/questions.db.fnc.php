<?php
function get_question_from_array($req_array)
{
	return new question(
		$req_array['ID'],
		$req_array['Text'],
		$req_array['Section'],
		$req_array['Added'],
		$req_array['Notes']);
}
	
function get_question_from_ID($req_ID)
{
	global $myPDO;
	
	// prep the statement
	$statement = $myPDO->prepare('SELECT * FROM rdtom_questions WHERE ID = :ID LIMIT 1');
	$statement->execute(array(':ID' => $req_ID));
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
	global $myPDO, $remeber_in_session;
	
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

function get_questions($terms_array = false)
{
	global $myPDO, $default_terms_array;
	
	if ($terms_array)
	{
	
		// the array holds a list of taxonomys and values to use in the search for questions
		/*
		 * for example:
		 * (
		 * 		"tag" => "Test Question"
		 * 		"rule-set" => "WFTDA6"
		 * )
		 * 
		 * would return every question in the WFTDA6 rule set which is suitable for being a test question
		 */
	
		/*
		Successful multiple taxonomies OR situation
		
		SELECT 
			* 
		FROM 
			rdtom_questions
		JOIN
		(
			SELECT 
				Question_ID
			FROM 
				rdtom_relationships
			JOIN
			(
				SELECT 
				ID as term_ID 
				FROM 
					rdtom_terms 
				WHERE 
					(taxonomy = "rule-set" AND name = "WFTDA5") OR 
					(taxonomy = "tag" AND name = "Test Question")
			) T 
			ON 
				T.term_ID = rdtom_relationships.Term_ID
		) R 
		ON 
			R.Question_ID = rdtom_questions.ID
		
		
		 */
		
		/*
		Successful multiple taxonomies AND situation
		SELECT 
			* 
		FROM 
			rdtom_questions
		JOIN
		(
			SELECT
			*
			FROM
			(
				SELECT 
					Question_ID, count(*) as count
				FROM 
					rdtom_relationships
				JOIN
				(
					SELECT 
					ID as term_ID 
					FROM 
						rdtom_terms 
					WHERE 
						(taxonomy = "rule-set" AND name = "WFTDA5") OR 
						(taxonomy = "tag" AND name = "Test Question")
				) T 
				ON 
					T.term_ID = rdtom_relationships.Term_ID
				
				GROUP BY Question_ID
			) C
			WHERE C.count = 2
		) R 
		ON 
			R.Question_ID = rdtom_questions.ID
		
		
		 */
		
		foreach ($terms_array as $taxonomy => $taxonomy_name)
		{
			$taxonomy_query_string[] = "(rdtom_terms.taxonomy = '$taxonomy' AND rdtom_terms.name = '$taxonomy_name')";
		}
		
		$taxonomy_query_string = implode( " OR ", $taxonomy_query_string);
		
		$query_string = ("
			SELECT 
			* 
			FROM 
				rdtom_questions
			JOIN
			(
				SELECT
				*
				FROM
				(
					SELECT 
						Question_ID, count(*) as count
					FROM 
						rdtom_relationships
					JOIN
					(
						SELECT 
						ID as term_ID 
						FROM 
							rdtom_terms 
						WHERE 
							" . $taxonomy_query_string . "
					) T 
					ON 
						T.term_ID = rdtom_relationships.Term_ID
					
					GROUP BY Question_ID
				) C
				WHERE C.count = " . count($terms_array) . "
			) R 
			ON 
				R.Question_ID = rdtom_questions.ID");
		
		$statement = $myPDO->query($query_string);
	
	}
	else
	{
		$statement = $myPDO->query("SELECT * FROM rdtom_questions ORDER BY Section ASC");
	}
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
	global $myPDO;
	
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

function get_sections_array_from_User_ID($req_User_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("
		SELECT rdtom_questions.ID, rdtom_questions.Section
		FROM rdtom_questions
		JOIN rdtom_responses ON rdtom_responses.Question_ID = rdtom_questions.ID
		WHERE rdtom_responses.User_ID = :ID");

	$statement->bindValue(':ID', $req_User_ID, PDO::PARAM_INT);
	$statement->execute();
	
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[$result['ID']] = $result['Section'];
		}
		return $out;
	}
	else
	{
		return false;
	}
}

function get_sections_array()
{
	global $myPDO;
	$statement = $myPDO->prepare("SELECT ID, Section FROM rdtom_questions");
	$statement->execute();
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[$result['ID']] = $result['Section'];
		}
		return $out;
	}
	else
	{
		return false;
	}
}


function get_questions_hard($limit = 30, $easy = false)
{
	global $myPDO;

	if ($easy)
	{
		$order = "DESC";
	}
	else
	{
		$order = "ASC";
	}
	
	// The query to get the IDs of hard questions
	$statement = $myPDO->prepare("
	SELECT 
		Question_ID, 
		(COUNT( CASE  `Correct` WHEN 1 THEN  `Correct` END ) / COUNT( * )) *100 AS  'correct_perc'
	FROM  
		`rdtom_responses` 
	GROUP BY 
		`Question_ID` 
	ORDER BY 
		`correct_perc` $order 
	LIMIT 0 , :limit");

	$statement->bindValue(':limit', $limit, PDO::PARAM_INT);
	$statement->execute();
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result_array)
		{
			$question_tmp = get_question_from_ID($result_array['Question_ID']);
			$question_tmp->set_SuccessRate($result_array['correct_perc']);
			$out[] = $question_tmp;
		}
		return $out;
	}
	else
	{
		throw new exception("Whoops, no hard questions found in the database");
	}
}


function get_questions_difficulty_limit($lower_limit, $upper_limit)
{
	global $myPDO;

	
	// The query to get the IDs of hard questions
	$statement = $myPDO->prepare("
	SELECT 
		* ,
		correct_perc
	FROM 
		rdtom_questions
	JOIN
	(
		SELECT
	        Question_ID,
			correct_perc
	        
	   FROM
	        (
	        SELECT 
				Question_ID, 
				ROUND((COUNT( CASE  `Correct` WHEN 1 THEN  `Correct` END ) / COUNT( * )) *10) *10 AS  'correct_perc'
			FROM  
				`rdtom_responses` 
			GROUP BY 
				`Question_ID` 
			ORDER BY 
				`correct_perc` ASC 
			) O
		WHERE 
			O.correct_perc <= :upper_limit
		AND 
			O.correct_perc >= :lower_limit
	) R
	ON 
		R.Question_ID = rdtom_questions.ID
	");

	$statement->bindValue(':lower_limit', $lower_limit, PDO::PARAM_INT);
	$statement->bindValue(':upper_limit', $upper_limit, PDO::PARAM_INT);
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
/*
 * Query to fetch frequency of each percentage
	SELECT
        COUNT(Question_ID),
        correct_perc
        FROM
        (SELECT 
		Question_ID, 
		ROUND((COUNT( CASE  `Correct` WHEN 1 THEN  `Correct` END ) / COUNT( * )) *100) AS  'correct_perc'
	FROM  
		`rdtom_responses` 
	GROUP BY 
		`Question_ID` 
	ORDER BY 
		`correct_perc` ASC 
	) O
        GROUP BY correct_perc
 */
function get_question_correct_perc($req_ID)
{
	global $myPDO;

	
	// The query to get the IDs of hard questions
	$statement = $myPDO->prepare("
	SELECT 
		(COUNT( CASE  `Correct` WHEN 1 THEN  `Correct` END ) / COUNT( * )) *100 AS  'correct_perc'
	FROM  
		`rdtom_responses` 
	WHERE
		`Question_ID` = :Question_ID
	");

	$statement->bindValue(':Question_ID', $req_ID);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	return $result['correct_perc'];

}

function add_question($req_text, $req_section, $req_notes)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("
	INSERT 
		INTO rdtom_questions 
		(
			Text,
			Section,
			Added,
			Notes
		)
		VALUES 
		(
			:Text,
			:Section,
			" . gmmktime() . ",
			:Notes
		);");
	
	$statement->execute(array(
			':Text'=>$req_text,
			':Section'=>$req_section,
			':Notes'=>$req_notes));

	$lastInsertedID = $myPDO->lastInsertId();
	
	return $lastInsertedID;
}

function rebuild_questions_holes_map($terms_array = false)
{

	global $mydb, $default_terms_array;
	
	$all_questions = get_questions($default_terms_array);
	
	// delete then remake the holes map table
	$query = "DROP TABLE IF EXISTS rdtom_questions_holes_map;
	CREATE TABLE rdtom_questions_holes_map ( row_id int not NULL primary key, Question_ID int not null);";
	
	$mydb->run_multi_query($query);
	
	// populate the table
	$i = 0;
	foreach ($all_questions as $question)
	{
		$query = "INSERT INTO rdtom_questions_holes_map (
			row_id ,
			Question_ID
			)
			VALUES (
			'" . $i . "',  '" . $question->get_ID() . "'
			);";
		$i++;
		$mydb->run_query($query);
	}
}

function edit_question($req_ID, $req_text, $req_section, $req_notes)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("
	UPDATE 
		rdtom_questions 
	SET 
		Text = :Text, 
		Section = :Section, 
		Notes = :Notes
	WHERE 
		ID = :ID
		");
	
	$statement->execute(array(
			':Text'=>$req_text,
			':Section'=>$req_section,
			':Notes'=>$req_notes,
			':ID'=>$req_ID));
	
	rebuild_questions_holes_map();
}
?>