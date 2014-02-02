<?php
function set_test_rating($test_ID, $Rating, $User_ID = -1)
{
	global $myPDO;
	// if there is a user logged in, check if the user has already rated this test
	if (($User_ID > 0) && (get_test_rating($test_ID, $User_ID) !== false))
	{
		// update from user ID
		$statement = $myPDO->prepare("
		UPDATE rdtom_test_ratings 
		SET  
			Rating = :Rating,
			Timestamp = :Timestamp
		WHERE 
			Test_ID = :Test_ID
		AND
			User_ID = :User_ID
		;");
		$statement->bindValue(':Rating', $Rating);	
		$statement->bindValue(':Timestamp', gmmktime());	
		$statement->bindValue(':Test_ID', $test_ID);
		$statement->bindValue(':User_ID', $User_ID);		
		$statement->execute();
	}
	
	// if there's no user logged in, check if the IP address has already rated this test
	elseif (($User_ID < 0) && (get_test_rating($test_ID, false, get_ip()) !== false))
	{
		// update from IP
		$statement = $myPDO->prepare("
		UPDATE rdtom_test_ratings 
		SET  
			Rating = :Rating,
			Timestamp = :Timestamp
		WHERE 
			Test_ID = :Test_ID
		AND
			IP = :IP
		AND 
			User_ID = -1
		;");
		$statement->bindValue(':Rating', $Rating);	
		$statement->bindValue(':Timestamp', gmmktime());	
		$statement->bindValue(':Test_ID', $test_ID);
		$statement->bindValue(':IP', get_ip());	
		$statement->execute();
		
	}
	else
	{
		// we're adding a new rating
		$statement = $myPDO->prepare("
		INSERT 
		INTO rdtom_test_ratings 
		(
			Test_ID ,
			User_ID ,
			Rating ,
			IP ,
			Timestamp
		)
		VALUES 
		(
			:Test_ID ,
			:User_ID ,
			:Rating ,
			:IP ,
			:Timestamp
		);");
		
		$statement->bindValue(':Test_ID', $test_ID);
		$statement->bindValue(':User_ID', $User_ID);	
		$statement->bindValue(':Rating', $Rating);	
		$statement->bindValue(':IP', get_ip());	
		$statement->bindValue(':Timestamp', gmmktime());	
		$statement->execute();
	}
	
	// refresh the test's average
	update_test_average_rating($test_ID);
}

function get_test_rating($test_ID, $User_ID = false, $IP = false)
{
	global $myPDO;
	if ($User_ID)
	{
		// get a rating for a given test, given a specific user ID
		$statement = $myPDO->prepare("SELECT Rating AS Average_Rating FROM rdtom_test_ratings WHERE Test_ID = :Test_ID AND User_ID = :User_ID");
		$statement->execute(array(':Test_ID' => $test_ID, ':User_ID' => $User_ID));
	}
	elseif ($IP)
	{
		// get a rating for a given test, given a specific IP address
		$statement = $myPDO->prepare("SELECT Rating AS Average_Rating FROM rdtom_test_ratings WHERE Test_ID = :Test_ID AND IP = :IP AND User_ID = -1");
		$statement->execute(array(':Test_ID' => $test_ID, ':IP' => $IP));
	}
	else
	{
		// get the current average rating
		$statement = $myPDO->prepare("SELECT AVG(Rating) AS Average_Rating FROM rdtom_test_ratings WHERE Test_ID = :Test_ID");
		$statement->execute(array(':Test_ID' => $test_ID));
	}
	
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	if ($result['Average_Rating'] == null)
	{
		return false;
	}
	
	return $result['Average_Rating'];
}

function update_test_average_rating($test_ID)
{
	// given a test ID, update it's average rating.

	$test = get_test_from_ID($test_ID);
	$test->set_Average_Rating(get_test_rating($test_ID));
	set_test($test);
	
}