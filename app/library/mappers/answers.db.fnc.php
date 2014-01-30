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
		throw new exception("Whoops, no answers found in the database for question");
	}
}

function get_answer_from_ID($req_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_answers WHERE ID = :ID");
	$statement->execute(array(':ID' => $req_ID));
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	if ($result)
	{
		$out = get_answer_from_array($result);
			
		return $out;
	}
	else
	{
		throw new exception("Whoops, no answers found in the database with ID");
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
		throw new Exception("database object error: no answer found with the ID " . (integer)$req_ID);
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
		
	}
	
	// add the archive responses
	$statement = $myPDO->prepare("
		SELECT Answer_ID, COUNT( * ) AS count
		FROM  `rdtom_responses_archive` 
		JOIN rdtom_answers ON rdtom_answers.ID = rdtom_responses_archive.Answer_ID
		WHERE rdtom_responses_archive.Question_ID = :QuestionID
		GROUP BY  `Answer_ID` ");
	$statement->execute(array(':QuestionID' => $req_QuestionID));
	$results = $statement->fetchAll();	
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[$result['Answer_ID']] += $result['count'];
		}
	}
	
	return $out;
}

function add_answer($req_Question_ID, $req_Text, $req_Correct)
{
	global $myPDO;

	if ($req_Text == "")
	{
		throw new exception ("No text given for answer;");
	}
	
	$statement = $myPDO->prepare("
	INSERT 
		INTO rdtom_answers 
		(
			`Question_ID` ,
			`Text` ,
			`Correct`
		)
		VALUES 
		(
			:Question_ID , 
			:Text ,  
			:Correct
		);
	");
	
	$statement->execute(array(':Question_ID' => $req_Question_ID, ':Text' => $req_Text, ':Correct' => $req_Correct));

}
?>