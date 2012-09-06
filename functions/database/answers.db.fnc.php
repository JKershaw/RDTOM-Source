<?php
function get_answer_from_array($req_array)
{
	return new answer(
		$req_array['ID'],
		$req_array['Question_ID'],
		$req_array['Text'],
		$req_array['Correct']);
}

function get_answers_from_question_ID($req_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_answers WHERE Question_ID = :ID");
	$statement->execute(array(':ID' => $req_ID));
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[] = get_answer_from_array($result);
		}
			
		return $out;
	}
	else
	{
		throw new exception("Whoops, no answers found in the database for question (ID: " . $req_ID . ")");
	}
}


function is_answer_correct_from_ID($req_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT Correct FROM rdtom_answers WHERE ID = :ID LIMIT 1");
	$statement->execute(array(':ID' => $req_ID));
	$result = $statement->fetchColumn();
	
	if ($result === false)
	{
		throw new Exception("database object error: no answer found with the ID " . $req_ID);
	}
	
	if ($result == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_answer_response_perc($req_QuestionID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("
		SELECT Answer_ID, COUNT( * ) AS count
		FROM  `rdtom_responses` 
		JOIN rdtom_answers ON rdtom_answers.ID = rdtom_responses.Answer_ID
		WHERE rdtom_responses.Question_ID = :QuestionID
		GROUP BY  `Answer_ID` ");
	$statement->execute(array(':QuestionID' => $req_QuestionID));
	$results = $statement->fetchAll();	
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[$result['Answer_ID']] = $result['count'];
		}
		return $out;
	}
}
?>