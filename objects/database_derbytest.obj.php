<?php 
/*
 * A database object for dealing specifically with the Roller Derby Test-O-Matic database
 */
include_once('database.obj.php');

class database_derbytest extends database
{

	function __construct()
	{
		global $database_username, $database_userpassword, $database_name, $database_host;

		$this->dbUser = $database_username;
		$this->dbUserPw = $database_userpassword;
		$this->dbName = $database_name;
		$this->dbHost = $database_host;
	}
	
	public function get_random_question()
	{
		global $random_questions_to_remeber, $remeber_in_session, $random_question_find_attempts;
	
		// if the holes table is being rebuilt, cheat
		if (!$this->does_table_exist("rdtom_questions_holes_map"))
		{
			return $this->get_random_question_simple();
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
				
			$result = $this->get_row($query);
			
			if ($result)
			{
				$question = $this->get_question_from_array($result);
				
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
		//echo "*";
		
		// we tried 5 times to find a unique question, and failed, so resort back to the old random question getter
		return $this->get_random_question_simple();
	}
	
	public function get_random_question_simple()
	{
		global $random_questions_to_remeber, $remeber_in_session;
		
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
		
		$result = $this->get_row($query);
		
		if ($result)
		{
			$question = $this->get_question_from_array($result);
		}
		else
		{
			throw new exception("Woah, either the site ran out of questions, or the database is being updated. Try reloading the page.");
		}	

		return $question;
	}
	
	public function get_question_from_ID($req_ID)
	{
		settype($req_ID, "integer");
		$query = "SELECT * FROM rdtom_questions WHERE ID = '" . $req_ID . "' LIMIT 1";
		
		$result = $this->get_row($query);
		
		if ($result)
		{
			return $this->get_question_from_array($result);
		}
		else
		{
			throw new exception("Whoops, no question found with the ID of " . $req_ID);
		}
	}
	
	
	public function get_question_from_array($req_array)
	{
		return new question(
			$req_array['ID'],
			$req_array['Text'],
			$req_array['Section'],
			$req_array['Added'],
			$req_array['Notes'],
			$req_array['Source']);
	}
	
	public function get_answer_from_array($req_array)
	{
		return new answer(
			$req_array['ID'],
			$req_array['Question_ID'],
			$req_array['Text'],
			$req_array['Correct']);
	}
	
	public function is_question_ID_valid($req_ID)
	{
		settype($req_ID, "integer");
		$query = "SELECT ID FROM rdtom_questions WHERE ID = '" . $req_ID . "' LIMIT 1";
		
		$result = $this->get_var($query);
		
		
		if ($result == $req_ID)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function get_report_from_array($req_array)
	{
		return new report(
			$req_array['ID'],
			$req_array['IP'],
			$req_array['Timestamp'],
			$req_array['Question_ID'],
			$req_array['User_ID'],
			$req_array['Text'],
			$req_array['Status']);
	}
	
	
	public function get_answers_from_question_ID($req_ID)
	{
		settype($req_ID, "integer");
		
		$query = "SELECT * FROM rdtom_answers WHERE Question_ID = '$req_ID'";
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_answer_from_array($result);
			}
				
			return $out;
		}
		else
		{
			throw new exception("Whoops, no answers found in the database for question (ID: " . $req_ID . ")");
		}
	}
	

	
	public function get_answer_from_ID($req_ID)
	{
		settype($req_ID, "integer");
		$query = "SELECT * FROM rdtom_answers WHERE ID = '" . $req_ID . "' LIMIT 1";
		
		$result = $this->get_row($query);
		
		if ($result)
		{
			return $this->get_answer_from_array($result);
		}
		else
		{
			throw new exception("Whoops, no question found with the ID of " . $req_ID);
		}
	}
	
	public function is_answer_correct_from_ID($req_ID)
	{
		settype($req_ID, "integer");
		$query = "SELECT Correct FROM rdtom_answers WHERE ID = '" . $req_ID . "' LIMIT 1";
		
		$result = $this->get_var($query);
		
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
	
	public function get_questions()
	{
		
		$query = "SELECT * FROM rdtom_questions ORDER BY Section ASC";
		
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_question_from_array($result);
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
	
	public function get_questions_from_User_ID($req_User_ID, $limit = false, $timelimit = false, $opt_only_wrong = false)
	{
		
		settype($req_User_ID, "integer");
		
		if ($opt_only_wrong)
		{
			$clause = " AND rdtom_responses.Correct = false";
		}
		
		if ($timelimit)
		{
			settype($timelimit, "integer");
			$clause .= " AND rdtom_responses.Timestamp >= '" . (gmmktime() - $timelimit) . "' ";
			$order = " ORDER BY rdtom_responses.Timestamp Desc";
		}
		
		if ($limit)
		{
			settype($limit, "integer");
			$limit = " LIMIT 0, " . $limit;
		}
		
		$query = "
			SELECT rdtom_questions . * 
			FROM rdtom_questions
			JOIN rdtom_responses ON rdtom_responses.Question_ID = rdtom_questions.ID
			WHERE rdtom_responses.User_ID = '" . $req_User_ID . "'" . $clause . $order . $limit;
		
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_question_from_array($result);
			}
			return $out;
		}
		else
		{
			return false;
		}
	}
	

	public function get_sections_array_from_User_ID($req_User_ID)
	{
		
		settype($req_User_ID, "integer");
		
		$query = "
			SELECT rdtom_questions.ID, rdtom_questions.Section
			FROM rdtom_questions
			JOIN rdtom_responses ON rdtom_responses.Question_ID = rdtom_questions.ID
			WHERE rdtom_responses.User_ID = '" . $req_User_ID . "'";
		
		$results = $this->get_results($query);
		
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
	
	public function get_sections_array()
	{

		$query = "SELECT ID, Section FROM rdtom_questions";
		
		$results = $this->get_results($query);
		
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
	
	public function get_hard_questions($limit = 30, $easy = false)
	{

		settype($limit, "integer");
		
		if ($easy)
		{
			$order = "DESC";
		}
		else
		{
			$order = "ASC";
		}
		
		// The query to get the IDs of hard questions
		$query = "
		SELECT 
			Question_ID, 
			(COUNT( CASE  `Correct` WHEN 1 THEN  `Correct` END ) / COUNT( * )) *100 AS  'correct_perc'
		FROM  
			`rdtom_responses` 
		GROUP BY 
			`Question_ID` 
		ORDER BY 
			`correct_perc` $order 
		LIMIT 0 , $limit";

		$result_IDs = $this->get_results($query);
		
		if ($result_IDs)
		{
			foreach ($result_IDs as $result_array)
			{
				$question_tmp = $this->get_question_from_ID($result_array['Question_ID']);
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
	
	public function get_answer_response_perc($req_QuestionID)
	{
		settype($req_QuestionID, "integer");
		
		$query = "
		SELECT Answer_ID, COUNT( * ) AS count
		FROM  `rdtom_responses` 
		JOIN rdtom_answers ON rdtom_answers.ID = rdtom_responses.Answer_ID
		WHERE rdtom_responses.Question_ID =$req_QuestionID
		GROUP BY  `Answer_ID` ";
		
		$results = $this->get_results($query);
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[$result['Answer_ID']] = $result['count'];
			}
			return $out;
		}
	}
	
	
	public function add_question($req_text, $req_section, $req_notes, $req_source)
	{
		$req_text = $this->mysql_res($req_text);
		$req_section = $this->mysql_res($req_section);
		$req_notes = $this->mysql_res($req_notes);
		$req_source = $this->mysql_res($req_source);
		
		$query = "
		INSERT 
			INTO rdtom_questions 
			(
				Text,
				Section,
				Added,
				Notes,
				Source
			)
			VALUES 
			(
				'" . $req_text . "',  
				'" . $req_section . "',
				'" . gmmktime() . "',
				'" . $req_notes . "',
				'" . $req_source . "'
			);";
		
		$this->run_query($query);
		
		$this->rebuild_questions_holes_map();
		
		return $this->get_inserted_id();
		
	}
	

	
	public function edit_question($req_ID, $req_text, $req_section, $req_notes, $req_source)
	{
		settype($req_ID, "integer");
		$req_text = $this->mysql_res($req_text);
		$req_section = $this->mysql_res($req_section);
		$req_notes = $this->mysql_res($req_notes);
		$req_source = $this->mysql_res($req_source);
		
		$query = "
		UPDATE 
			rdtom_questions 
		SET 
			Text = '" . $req_text . "', 
			Section = '" . $req_section . "', 
			Notes = '" . $req_notes . "' , 
			Source = '" . $req_source . "' 
		WHERE 
			ID = '" . $req_ID . "'
			";
		
		$this->run_query($query);
		
		$this->rebuild_questions_holes_map();
	}
	
	public function add_answer($req_Question_ID, $req_Text, $req_Correct)
	{
		settype($req_Question_ID, "integer");
		$req_Text = $this->mysql_res($req_Text);
		settype($req_Correct, "integer");
		
		if ($req_Text == "")
		{
			throw new exception ("No text given for answer;");
		}
		
		$query = "
		INSERT 
			INTO rdtom_answers 
			(
				`Question_ID` ,
				`Text` ,
				`Correct`
			)
			VALUES 
			(
				'$req_Question_ID', 
				'$req_Text',  
				'$req_Correct'
			);
		";
		$this->run_query($query);
	}
	

	public function set_report($req_report)
	{
		
		$req_ID = $req_report->get_ID();
		
		if (!$req_ID)
		{
			throw new exception ("Report not found");
		}
		
		$req_IP = $req_report->get_IP();
		$req_Timestamp = $req_report->get_Timestamp();
		$req_Question_ID = $req_report->get_Question_ID();
		$req_User_ID = $req_report->get_User_ID();
		$req_Text = $req_report->get_Text();
		$req_Status = $req_report->get_Status();
		
		settype($req_ID, "integer");
		$req_IP = $this->mysql_res($req_IP);
		settype($req_Timestamp, "integer");
		settype($req_Question_ID, "integer");
		settype($req_User_ID, "integer");
		$req_Text = trim($this->mysql_res($req_Text));
		settype($req_Status, "integer");
		
		if ($req_Text == "")
		{
			throw new exception ("No text given for answer;");
		}
		
		if ($req_ID <= 0)
		{
			$query = "
			INSERT 
				INTO rdtom_reports 
				(
					IP ,
					Timestamp ,
					Question_ID ,
					User_ID ,
					Text ,
					Status
				)
				VALUES 
				(
					'$req_IP',  
					'$req_Timestamp',  
					'$req_Question_ID',  
					'$req_User_ID',  
					'$req_Text',  
					'$req_Status'
				);
			";
			$this->run_query($query);
		}
		else
		{
			$query = "
			UPDATE  rdtom_reports 
			SET  
				IP =  '$req_IP',
				Timestamp =  '$req_Timestamp',
				Question_ID =  '$req_Question_ID',
				User_ID =  '$req_User_ID',
				Text =  '$req_Text',
				Status =  '$req_Status' 
			WHERE ID = '$req_ID' 
			;";
			$this->run_query($query);
		}
	}

	public function get_reports($status = false)
	{
		if ($status !== false)
		{
			settype($status, "integer");
			$clause = "WHERE Status = '$status'";
		}
		
		$query = "SELECT * FROM rdtom_reports $clause ORDER BY Timestamp ASC";
		
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_report_from_array($result);
			}
		}
		return $out;
	}

	public function get_reports_from_question_ID($question_ID, $status = false)
	{
		settype($question_ID, "integer");
		$clause = "WHERE Question_ID = '$question_ID'";
		
		if ($status !== false)
		{
			settype($status, "integer");
			$clause .= "AND Status = '$status'";
		}
		
		$query = "SELECT * FROM rdtom_reports $clause ORDER BY Timestamp ASC";
		
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_report_from_array($result);
			}
		}
		return $out;
	}

	public function get_report_from_ID($req_ID)
	{
		settype($req_ID, "integer");
		
		$query = "SELECT * FROM rdtom_reports WHERE ID = '$req_ID'";
		$result = $this->get_row($query);
		
		return $this->get_report_from_array($result);
	}
	
	public function get_question_count()
	{
		$query = "SELECT COUNT(*) FROM rdtom_questions";
		$result = $this->get_var($query);
		return $result;
	}
	
	public function get_answer_count()
	{
		$query = "SELECT COUNT(*) FROM rdtom_answers";
		$result = $this->get_var($query);
		return $result;
	}
	
	public function get_response_from_array($req_array)
	{
		return new response(
			$req_array["ID"], 
			$req_array["Question_ID"], 
			$req_array["Answer_ID"], 
			$req_array["Timestamp"], 
			$req_array["Correct"], 
			$req_array["IP"], 
			$req_array["User_ID"]);
	}
	
	public function get_response_count($optional_user_ID = false)
	{
		if ($optional_user_ID)
		{
			settype($optional_user_ID, "integer");
			$clause = " WHERE User_ID = '" . $optional_user_ID . "'";
		}
		
		$query = "SELECT COUNT(*) FROM rdtom_responses" . $clause;
		$result = $this->get_var($query);
		return $result;
	}
	
	public function get_responses_from_User_ID($User_ID, $since_timestamp = false)
	{
		settype($User_ID, "integer");
	
		if (!$User_ID)
		{
			throw new exception ("No User ID given to get_responses_from_User_ID");
		}
		

		if ($since_timestamp)
		{
			settype($since_timestamp, "integer");
			$query = "SELECT * FROM rdtom_responses WHERE User_ID = '" . $User_ID . "' AND Timestamp > '$since_timestamp'";
		}
		else
		{
			$query = "SELECT * FROM rdtom_responses WHERE User_ID = '" . $User_ID . "'";
		}
		
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_response_from_array($result);
			}
		}
		else
		{
			return false;
		}
		
		return $out;
	}

	public function disassociate_responses($User_ID)
	{
		settype($User_ID, "integer");
		
		$user = $this->get_user_from_ID($User_ID);
		
		if (!$user)
		{
			throw new exception ("No User given to disassociate_responses");
		}
		
		$query = "
		UPDATE 
			rdtom_responses 
		SET 
			User_ID = '0'
		WHERE 
			User_ID = '" . $User_ID . "'
			";
		
		$this->run_query($query);
		
	}
	
	public function get_responses($limit = 100)
	{
		settype($limit, "integer");
	
		$query = "SELECT * FROM rdtom_responses ORDER BY ID DESC LIMIT 0, " . $limit;
		$results = $this->get_results($query);
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$out[] = $this->get_response_from_array($result);
			}
		}
		else
		{
			return false;
		}
		
		return $out;
	}
	
	public function get_response_count_since($req_timestamp)
	{
		settype($req_timestamp, "integer");
		$query = "SELECT COUNT(*) FROM rdtom_responses WHERE Timestamp > '$req_timestamp'";
		$result = $this->get_var($query);
		return $result;
	}
	
	public function get_response_distinct_ip_count()
	{
		$query = "SELECT COUNT(DISTINCT IP) FROM rdtom_responses";
		$result = $this->get_var($query);
		return $result;
	}
	
	public function remove_question_and_answers($req_question_ID)
	{
		settype($req_question_ID, "integer");
		// delete question
		$query = "DELETE FROM rdtom_questions WHERE ID = '" .$req_question_ID . "' LIMIT 1;";
		$this->run_query($query);
		
		$this->rebuild_questions_holes_map();
		
		// delete answers
		$query = "DELETE FROM rdtom_answers WHERE Question_ID = '" .$req_question_ID . "'";
		$this->run_query($query);
	}
	
	public function remove_answers_given_questionID($req_question_ID)
	{
		settype($req_question_ID, "integer");
		
		// delete answers
		$query = "DELETE FROM rdtom_answers WHERE Question_ID = '" .$req_question_ID . "'";
		$this->run_query($query);
	}
	
	public function set_response($req_response)
	{
		$req_Question_ID = $req_response->get_Question_ID();
		$req_Answer_ID = $req_response->get_Answer_ID();
		$req_Timestamp_ID = $req_response->get_Timestamp();
		$req_Correct = $req_response->get_Correct();
		$req_IP = $req_response->get_IP();
		$req_User_ID = $req_response->get_User_ID();
		
		settype($req_Question_ID, "integer");
		settype($req_Answer_ID, "integer");
		settype($req_Timestamp_ID, "integer");
		settype($req_Correct, "integer");
		$req_IP = $this->mysql_res($req_IP);
		settype($req_User_ID, "integer");
		
		$query = "
		INSERT INTO rdtom_responses 
		(
			Question_ID ,
			Answer_ID ,
			Timestamp ,
			Correct ,
			IP,
			User_ID
		) VALUES (
			'$req_Question_ID',  
			'$req_Answer_ID',  
			'$req_Timestamp_ID',  
			'$req_Correct',  
			'$req_IP',
			'$req_User_ID'
		);
		";
		
		$this->run_query($query);
	}
	
	public function rebuild_questions_holes_map()
	{
		// delete then remake the holes map table
		$query = "
		DROP TABLE IF EXISTS rdtom_questions_holes_map;
		CREATE TABLE rdtom_questions_holes_map ( row_id int not NULL primary key, Question_ID int not null);
		SET @id = 0;
		INSERT INTO rdtom_questions_holes_map SELECT @id := @id + 1, ID FROM rdtom_questions;";
		$this->run_multi_query($query);
	}
	
	public function get_stats_hourly_posts($hour_count)
	{
		$time_ago = gmmktime() - (60*60*$hour_count);
		$time_now = gmmktime();
		// round
		$time_ago = floor($time_ago/3600) * 3600;
		//$time_ago = floor($time_ago/3600) * 3600;
		
		$query = "
			SELECT 
			count(*) AS responses,
			FROM_UNIXTIME(Timestamp, '%Y %D %M %H') AS hour 
			FROM rdtom_responses 
			WHERE Timestamp > '$time_ago' 
			AND Timestamp < '$time_now'  
			GROUP BY hour 
			ORDER BY Timestamp ASC";
		return $this->get_results($query);
	}
	

	public function get_user_from_array($req_array)
	{
		return new user(
			$req_array['ID'], 
			$req_array['name'], 
			$req_array['password_hash'], 
			$req_array['password_salt'], 
			$req_array['email'], 
			$req_array['registered_time']);
	}
	
	public function get_user_password_hash_from_password($req_password, $req_user_salt)
	{
		global $database_salt;
		$hash = hash("sha384", $req_password . $database_salt);
		$real_hash = hash("sha512", $hash . $req_user_salt);
		
		return $real_hash;
	}

	public function get_user_from_name_and_password($req_user_name, $req_user_password)
	{
		// get the hash if the username is valid
		$user_salt = $this->get_user_password_salt_from_user_name($req_user_name);
		
		
		if (!$user_salt)
		{
			return false;
		}
		
		$user_password_hash = $this->get_user_password_hash_from_password($req_user_password, $user_salt);
		
		
		$user_name = $this->mysql_res($req_user_name);
		$user_password_hash = $this->mysql_res($user_password_hash);
		
		$query="SELECT * FROM rdtom_users WHERE name=\"" . $user_name . "\" AND  password_hash=\"" . $user_password_hash . "\"";
		$result = $this->get_row($query);
		
		if ($result)
		{
			return $this->get_user_from_array($result);
		}
		else
		{
			return false;
		}
		// an error has occured
		
	}
	
	public function get_user_from_ID($req_ID)
	{
		settype($req_ID, "integer");
		
		$query="SELECT * FROM rdtom_users WHERE ID=\"" . $req_ID . "\"";
		$result = $this->get_row($query);
		if ($result)
		{
			return $this->get_user_from_array($result);
		}
		else
		{
			throw new exception("User not found with ID " . $req_ID);
		} 
	}
	
	public function get_user_from_email($req_email)
	{
		
		if (!trim($req_email))
		{
			throw new exception("No email address give.");
		}
		
		$req_email = $this->mysql_res($req_email);
		
		$query="SELECT * FROM rdtom_users WHERE email=\"" . $req_email . "\"";
		$result = $this->get_row($query);
		if ($result)
		{
			return $this->get_user_from_array($result);
		}
		else
		{
			throw new exception("User not found with email " . $req_email);
		} 
	}

	public function get_user_password_salt_from_user_name($req_user_name)
	{
		$user_name = $this->mysql_res($req_user_name);
		
		$query="SELECT password_salt FROM rdtom_users WHERE name=\"" . $user_name . "\"";
		$result = $this->get_var($query);
		
		if ($result)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}

	public function get_user_count()
	{
		$query = "SELECT COUNT(*) FROM rdtom_users";
		$result = $this->get_var($query);
		return $result;
	}
	
	public function add_user($req_name, $req_password, $req_email)
	{
		$user_password_salt = generateSalt();
		$user_password_hash = $this->get_user_password_hash_from_password($req_password, $user_password_salt);
		$user_name = $this->mysql_res($req_name);
		$user_email = $this->mysql_res($req_email);
		
		// add the user
		$query="
			INSERT INTO rdtom_users 
			(
				name ,
				password_hash ,
				password_salt ,
				email ,
				registered_time
			)
			VALUES 
			(
				'" . $user_name . "',  
				'" . $user_password_hash . "',  
				'" . $user_password_salt . "',  
				'" . $user_email . "',  
				'" . gmmktime() . "'
			);
				";
		
		$this->run_query($query);
	}
	
	public function set_user_password($req_user_ID, $req_password)
	{
		$user_password_salt = generateSalt();
		$user_password_hash = $this->get_user_password_hash_from_password($req_password, $user_password_salt);
		settype($req_user_ID, "integer");
		
		if (!$req_user_ID)
		{
			throw new Exception("User ID not given");
		}
		
		// update the user
		$query= "
			UPDATE 
				rdtom_users 
			SET 
				password_hash = '$user_password_hash',
				password_salt = '$user_password_salt' 
			WHERE  
				ID = '$req_user_ID'";
		
		$this->run_query($query);
		
		save_log("password_reset", "RESET OR CHANGED for user ID: " . $req_user_ID);
	}
	
	public function set_user_name($req_User_ID, $req_username)
	{
		$selected_user = $this->get_user_from_ID($req_User_ID);
		
		$req_username = $this->mysql_res($req_username);
		settype($req_User_ID, "integer");
		
		$query = "
			UPDATE 
				rdtom_users
			SET
				name = '" . $req_username . "'
			WHERE
				ID = '" . $req_User_ID . "'";
		
		$this->run_query($query);
		
		save_log("username_change", "Changed from '". $selected_user->get_Name() . "' to '" . $req_username . "' for user ID: " . $req_User_ID);
	}
	
	public function set_user_email($req_User_ID, $req_email)
	{
		$selected_user = $this->get_user_from_ID($req_User_ID);
		
		$req_email = $this->mysql_res($req_email);
		settype($req_User_ID, "integer");
		
		$query = "
			UPDATE 
				rdtom_users
			SET
				email = '" . $req_email . "'
			WHERE
				ID = '" . $req_User_ID . "'";
		
		$this->run_query($query);
		
		save_log("email_change", "Changed from '". $selected_user->get_Email() . "' to '" . $req_email . "' for user ID: " . $req_User_ID);
	}
	
	
	public function add_token($req_token, $req_User_ID, $req_IP)
	{
		$req_token = $this->mysql_res($req_token);
		$req_IP = $this->mysql_res($req_IP);
		settype($req_User_ID, "integer");
		
		// add the user
		$query="
			INSERT INTO rdtom_usertokens 
			(
				User_ID ,
				Token ,
				IP ,
				Timestamp
			)
			VALUES 
			(
				'" . $req_User_ID . "',  
				'" . $req_token . "',  
				'" . $req_IP . "',  
				'" . gmmktime() . "'
			);
		";
		
		$this->run_query($query);		
	}
	
	public function get_user_from_token($req_token, $req_IP)
	{
		$req_token = $this->mysql_res($req_token);
		$req_IP = $this->mysql_res($req_IP);

		$query="SELECT User_ID FROM rdtom_usertokens WHERE Token = '" . $req_token . "' AND IP = '" . $req_IP . "'";
		$result = $this->get_var($query);
		
		if ($result)
		{
			return $this->get_user_from_ID($result);
		}
		
		return false;
	}
	
	public function delete_token($req_User_ID, $req_IP)
	{
		$req_IP = $this->mysql_res($req_IP);
		settype($req_User_ID, "integer");
		
		$query = "DELETE FROM rdtom_usertokens WHERE User_ID = '" . $req_User_ID . "' AND IP = '" . $req_IP . "'";
		$this->run_query($query);	
	}
	
	public function is_user_name_taken($req_user_name)
	{
		$user_name = $this->mysql_res($req_user_name);
		
		$query="SELECT count(*) FROM rdtom_users WHERE name=\"" . $user_name . "\"";
		$result = $this->get_var($query);
		
		if ($result == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function is_email_taken($req_email)
	{
		$user_email = $this->mysql_res($req_email);
		
		$query="SELECT count(*) FROM rdtom_users WHERE email=\"" . $user_email . "\"";
		$result = $this->get_var($query);
		
		if ($result == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/*
	 * functions to handle password reset tokens
	 */
	
	// save a password reset token
	public function set_password_reset_token($req_Token, $req_User_ID, $req_Email, $req_IP)
	{
		// clean the input
		$req_Token = $this->mysql_res($req_Token);
		settype($req_User_ID, "integer");
		$req_Email = $this->mysql_res($req_Email);
		$req_IP = $this->mysql_res($req_IP);
		
		$query = "
			INSERT INTO 
				rdtom_passwordresettokens (
				User_ID, 
				Email, 
				Token, 
				Timestamp, 
				IP, 
				Used
				) 
			VALUES 
				(
				'$req_User_ID', 
				'$req_Email', 
				'$req_Token', 
				'" . gmmktime() . "', 
				'$req_IP', 
				'0');";
		
		$this->run_query($query);
	}
	
	public function is_valid_password_reset_token($req_Token)
	{
		global $password_reset_token_expire;
		
		$req_Token = $this->mysql_res($req_Token);
		$time_ago = gmmktime() - $password_reset_token_expire;
		
		$query = "SELECT count(*) FROM rdtom_passwordresettokens WHERE Token = '$req_Token' AND Timestamp >= '$time_ago' AND Used = '0'";
	
		$result = $this->get_var($query);
		
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	
	public function get_user_from_password_reset_token($req_Token)
	{
		global $password_reset_token_expire;
		
		$req_Token = $this->mysql_res($req_Token);
		$time_ago = gmmktime() - $password_reset_token_expire;
		
		$query = "SELECT User_ID FROM rdtom_passwordresettokens WHERE Token = '$req_Token' AND Timestamp >= '$time_ago' AND Used = '0'";
	
		$User_ID = $this->get_var($query);
		
		$forgetful_user = $this->get_user_from_ID($User_ID);
		
		if ($forgetful_user)
		{
			return $forgetful_user;
		}
		else
		{
			throw new exception("Valid token, invalid user ID");
		}
	}
	
	
	public function use_password_reset_token($req_Token)
	{
		// mark all reset tokens for this account as used
		
		$forgetful_user = $this->get_user_from_password_reset_token($req_Token);
		
		$User_ID = $forgetful_user->get_ID();
		settype($User_ID, "integer");
		
		$query = "
			UPDATE 
				rdtom_passwordresettokens
			SET  
				Used =  '1' 
			WHERE 
				User_ID = '$User_ID'";
		
		$this->run_query($query);
	}
	
	public function get_timestamp_of_millionth()
	{
		global $competition_value;
		settype($competition_value, "integer");
		$competition_value = $competition_value -1;
		
		$query = "SELECT Timestamp FROM rdtom_responses LIMIT $competition_value, 1";
		return $this->get_var($query);
	}
} // class database
?>