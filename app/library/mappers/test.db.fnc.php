<?php

// we're generating a new test from some given parameters
function get_test_from_parameters($parameters)
{
	/*
			'number_of_questions' => $_POST['test_number_of_questions'],
			'difficulty' => $_POST['test_difficulty'],
			'pass_percentage' => $_POST['test_pass_percentage'],
			'output_format' => $_POST['test_output'],
			'question_IDs' => array(1, 2, 3, 4),
			'seed' => array(1, 2, 3, 4)
	 */
	$test = new test();

	// check if it exists, or we'll set the seed to ""
	if ($parameters['seed'])
	{
		$test->set_seed($test_seed);
	}
	
	if($parameters['question_IDs'])
	{
		populate_test($test, $parameters['number_of_questions'], $parameters['difficulty'], $parameters['question_IDs']);
	}
	else
	{
		populate_test($test, $parameters['number_of_questions'], $parameters['difficulty']);
	}
		
	$test->set_pass_percentage($parameters['pass_percentage']);
		
		
	// set output format
	$test->set_output_format($parameters['output_format']);
	
	return $test;
}

function get_test_from_array($result)
{
	$test = new test();
		
	$test->set_ID($result['ID']);
	$test->set_Title($result['Title']);
	$test->set_Description($result['Description']);
	$test->set_Status($result['Status']);
	$test->set_link_hash($result['Link_Hash']);
	$test->set_Author_ID($result['User_ID']);
	$test->set_Timestamp_created($result['Date_Created']);
	$test->set_Timestamp_edited($result['Date_Edited']);
	$test->set_Views_Count($result['Views_Count']);
	$test->set_Complete_Count($result['Completes_Count']);
	$test->set_Average_Rating($result['Average_Rating']);
	
	//Questions_and_Answers
	if ($result['Questions_and_Answers'])
	{
		$test->set_QandA_ID_Array(unserialize($result['Questions_and_Answers']));
	}
		
	return $test;
}

function get_test_from_ID($req_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_tests WHERE ID = :ID");
	$statement->execute(array(':ID' => $req_ID));
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	if ($result)
	{
		return  get_test_from_array($result);
	}
	else
	{
		throw new exception("Whoops, no test found in the database with ID");
	}
}

function get_tests_from_Author_ID($req_ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_tests WHERE User_ID = :ID");
	$statement->execute(array(':ID' => $req_ID));
	$results = $statement->fetchAll();
	
	if ($results)
	{
		foreach ($results as $result)
		{
			$out[] = get_test_from_array($result);
		}
			
		return $out;
	}
	else
	{
		return false;
	}
}

function populate_test(&$test, $opt_number_of_questions = 45, $difficulty = "intermediate", $question_IDs = false)
{
	// clean the input
	settype($opt_number_of_questions, "integer");
	
	if ($opt_number_of_questions < 1)
	{
		$opt_number_of_questions = 1;
	}

	if ($opt_number_of_questions >= 300)
	{
		$opt_number_of_questions = 300;
	}
	
	// get all applicable questions	
	
	// we've been given a list of questions
	if ($question_IDs)
	{
		if (is_array($question_IDs) && (count($question_IDs) < 1))
		{
			throw new exception("Could not generate test, number of requested questions too low or missing.");
		}
		// we know what questions we want;
		foreach($question_IDs as $question_ID)
		{
			try {
				$test_questions[] = get_question_from_ID($question_ID);
			} catch (Exception $e) {
				// question missing, oh well.
			}
		}
	}
	else
	{
		// we need to fetch them according to difficulty
		
		// get all the applicable questions
		if ($difficulty == "wftda")
		{
			// we want $opt_number_of_questions questions
			// 45% Beginner, 45% Intermediate, 10% Expert
			
			// get the questions
			$terms_array["rule-set"] = "WFTDA7";
			$terms_array["tag"] = "Test Question";
			$all_questions = get_questions($terms_array);
			
			// we're starting from the top, so shuffle before we begin
			shuffle($all_questions);
			
			// randomly choose the right number of each question
			// we can use ROUND rather than FLOOR because we're using < 0.5, so it'll never round up.
			$beginner_perc_number = round($opt_number_of_questions * 0.40);
			$expert_perc_number = round($opt_number_of_questions * 0.1);
			$intermediate_perc_number = $opt_number_of_questions - $beginner_perc_number - $expert_perc_number;
			 
			foreach($all_questions as $question)
			{
				// found a beginner question
				if (($beginner_perc_number > 0) && $question->is_relationship_true("difficulty", "Beginner"))
				{
					$beginner_perc_number--;
					$test_questions[] = $question;
					continue;
				}
				
				// found an intermediate question
				if (($intermediate_perc_number > 0) && $question->is_relationship_true("difficulty", "Intermediate"))
				{
					$intermediate_perc_number--;
					$test_questions[] = $question;
					continue;
				}
				
				// found an expert question
				if (($expert_perc_number > 0) && $question->is_relationship_true("difficulty", "Expert"))
				{
					$expert_perc_number--;
					$test_questions[] = $question;
					continue;
				}
			}
		
		}
		else
		{
			$terms_array = array();
			
			if ($difficulty == "beginner")
			{
				$terms_array["difficulty"] = "Beginner";
			}
			elseif ($difficulty == "expert")
			{
				$terms_array["difficulty"] = "Expert";
			}
			elseif ($difficulty == "intermediate")
			{
				$terms_array["difficulty"] = "Intermediate";
			}
			
			//$test->set_difficulty = $difficulty;
			
			// we want only WFTDA 5 questions and questions tagged with "Test Question" to be shown
			$terms_array["rule-set"] = "WFTDA7";
			$terms_array["tag"] = "Test Question";
			
			$all_questions = get_questions($terms_array);
			
			// randomly get an assortment of questions

            if ($opt_number_of_questions > count($all_questions))
            {
                $opt_number_of_questions = count($all_questions);
            }

			// randomly get a subsection of the array the correct length
			$random_questions_array_keys = array_rand($all_questions, $opt_number_of_questions);
			if (is_array($random_questions_array_keys))
			{
				foreach ($random_questions_array_keys as $random_question_array_key)
				{
					$test_questions[] = $all_questions[$random_question_array_key];
				}
			}
			else
			{
				$test_questions[] = $all_questions[$random_questions_array_keys];
			}		
		}
	}
	
	// add the question & random answers to the test 
	foreach ($test_questions as $test_question)
	{
		$test->add_question($test_question, $test_question->get_Answers(4, $test->get_seed()));
	}
}


function set_test($req_test)
{
	global $myPDO, $user;
	
	// basic validation
	if (!$req_test->get_Title())
	{
		throw new exception ("No title given");
	}
	
	if (!is_logged_in() || ($req_test->get_Author_ID() != $user->get_ID()))
	{
		// TODO this, need to allow for view count
		//throw new exception ("Only the Author can edit the test");
	}
	
	if ($req_test->get_ID() <= 0)
	{
		// adding a new test
		$statement = $myPDO->prepare("
		INSERT 
		INTO rdtom_tests 
		(
			Title ,
			Description ,
			Questions_and_Answers ,
			Status ,
			Link_Hash ,
			User_ID ,
			Date_Created ,
			Date_Edited ,
			Views_Count ,
			Completes_Count ,
			Questions_Count ,
			Average_Rating 
		)
		VALUES 
		(
			:Title ,
			:Description ,
			:Questions_and_Answers ,
			:Status ,
			:Link_Hash ,
			:User_ID ,
			:Date_Created ,
			:Date_Edited ,
			:Views_Count ,
			:Completes_Count ,
			:Questions_Count ,
			:Average_Rating 
		);");
		
		$statement->bindValue(':Date_Created', gmmktime());
	}
	else
	{
		// editing a test
		$statement = $myPDO->prepare("
		UPDATE  rdtom_tests
		SET  
		
			Title = :Title ,
			Description = :Description ,
			Questions_and_Answers = :Questions_and_Answers ,
			Status = :Status ,
			Link_Hash = :Link_Hash ,
			User_ID = :User_ID ,
			Date_Edited = :Date_Edited ,
			Views_Count = :Views_Count ,
			Completes_Count = :Completes_Count ,
			Questions_Count = :Questions_Count ,
			Average_Rating = :Average_Rating 
			
		WHERE 
			ID = :ID 
		;");
		$statement->bindValue(':ID', $req_test->get_ID());
		
	}
	
	$statement->bindValue(':Title', $req_test->get_Title());
	$statement->bindValue(':Description', $req_test->get_Description());
	$statement->bindValue(':Status', $req_test->get_Status());
	$statement->bindValue(':Link_Hash', $req_test->get_link_hash());
	$statement->bindValue(':User_ID', $req_test->get_Author_ID());
	$statement->bindValue(':Date_Edited', gmmktime());
	$statement->bindValue(':Views_Count', $req_test->get_Views_Count());
	$statement->bindValue(':Completes_Count', $req_test->get_Complete_Count());
	$statement->bindValue(':Questions_Count', $req_test->get_Question_Count());
	$statement->bindValue(':Average_Rating', $req_test->get_Average_Rating());
	
	$statement->bindValue(':Questions_and_Answers', serialize($req_test->get_question_and_answers_IDs()));
			
	if (!$statement->execute())
	{
		print_r($statement->errorInfo());
	}
	
	if ($req_test->get_ID() <= 0)
	{
		return $myPDO->lastInsertId();
	}
	else
	{
		return $req_test->get_ID();
	}
}
?>