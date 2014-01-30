<?php
function get_comment_from_array($req_array)
{
	return new comment(
		$req_array['ID'],
		$req_array['User_ID'],
		$req_array['Question_ID'],
		$req_array['Timestamp'],
		stripslashes($req_array['Text']),
		$req_array['Type']);
}

function set_comment($req_comment)
{
	global $myPDO;

	if (!$req_comment->get_Text())
	{
		throw new exception ("No text given for comment.");
	}
	
	if ($req_comment->get_ID() <= 0)
	{
		$statement = $myPDO->prepare("
		INSERT 
		INTO rdtom_comments 
		(
			User_ID ,
			Question_ID ,
			Timestamp ,
			Text ,
			Type
		)
		VALUES 
		(
			:User_ID ,
			:Question_ID,
			:Timestamp ,
			:Text ,
			:Type
		);");
	}
	else
	{
		$statement = $myPDO->prepare("
		UPDATE  rdtom_comments 
		SET  
			User_ID = :User_ID,
			Question_ID = :Question_ID;
			Timestamp = :Timestamp,
			Text = :Text,
			Type =  :Type 
		WHERE 
			ID = :ID 
		;");
		$statement->bindValue(':ID', $req_comment->get_ID());
		
	}

	$statement->bindValue(':User_ID', $req_comment->get_User_ID());
	$statement->bindValue(':Question_ID', $req_comment->get_Question_ID());
	$statement->bindValue(':Timestamp', $req_comment->get_Timestamp());
	$statement->bindValue(':Text', $req_comment->get_Text());
	$statement->bindValue(':Type', $req_comment->get_Type());

	$statement->execute();
	/*
	if ($statement->execute())
	{
		echo "true";
	}
	else
	 print_r($statement->errorInfo());
	 */
}

function get_comments_from_question_ID($req_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_comments WHERE Question_ID = :ID");
	$statement->execute(array(':ID' => $req_ID));
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[] = get_comment_from_array($result);
		}
			
		return $out;
	}
	return false;
}

function get_comments_since($req_timestamp)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_comments WHERE Timestamp >= :since");
	$statement->execute(array(':since' => $req_timestamp));
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[] = get_comment_from_array($result);
		}
			
		return $out;
	}
	return false;
}
?>