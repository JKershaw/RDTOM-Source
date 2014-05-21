<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

// if the user isn't an admin, show an error message
if (!is_admin())
{
	// show error page if not admin
	?>
	Sorry, you must be logged in to view this page.
	<?php 
	exit;
}

// has a question been saved?
if ($_POST)
{
	// are we editing a question or adding a new one?
	if ($_POST['question_id'] > 0)
	{
		if (strtolower($_POST['question_text']) == "delete")
		{
			include("./lib/questionDelete.php");
		}
		else
		{
			// editing a post

			// save all the answers submitted into an array
			foreach ($_POST['answer'] as $id => $answer)
			{
				if (trim($answer))
				{
					$is_correct = $_POST['correct'][$id] == 1;
					$temp_answer_array[] = new answer(-1, $_POST['question_id'], trim($answer), $is_correct);
				}
			}
			
			$tmp_question = get_question_from_ID($_POST['question_id']);
			
			$old_question_string = (string)$tmp_question;
			
			// have the answers changed? There may not be any answers.
			if ($temp_answer_array && ($tmp_question->is_answers_different($temp_answer_array)))
			{
				
				// delete existing post & questions
				$mydb->remove_answers_given_questionID($tmp_question->get_ID());
				$message = "Answers deleted! ";
				
				// save all the answers
				foreach ($_POST['answer'] as $id => $answer)
				{
					if (trim($answer))
					{
						$is_correct = $_POST['correct'][$id] == 1;
						add_answer($tmp_question->get_ID(), trim($answer), $is_correct);
					}
				}
				$message .= "Answers saved! ";	
			}
			else
			{
				$message .= "Answers unchanged! ";
			}
			
			// edit the question
			edit_question($tmp_question->get_ID(), $_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));
			$message .= "Question edited! ";	
			
			
			// check the applicable rule set
			// remove all relationships
			$mydb->remove_relationship_given_Question_ID($tmp_question->get_ID());
			$message .= "Relationships Removed! ";	
			
			// build new ones
			if ($_POST['term_checkbox'])
			{
				foreach ($_POST['term_checkbox'] as $term_ID => $data)
				{
					$mydb->add_relationship($tmp_question->get_ID(), $term_ID);
				}
				$message .= "Relationships Rebuilt! ";	
			}
			
			// rebuild the holes map, if the new question falls into the parameters defined in default_terms_array
			if ($tmp_question->is_default_terms_array())
			{
				rebuild_questions_holes_map();
				$message .= "Holes map rebuilt! ";	
			}
			
			// save a comment
			$comment_text = "Question Edited \n\nFrom: \n " . $old_question_string . " \nTo: \n" . get_question_from_ID($tmp_question->get_ID());
			
			// make a new comment
			$comment = new comment(-1, $user->get_ID(), $tmp_question->get_ID(), gmmktime(), $comment_text, QUESTION_CHANGED);
			
			// save the comment
			set_comment($comment);
		}
	}
	else 
	{
		// Adding a new question
		// try to save the question
		$question_id = add_question($_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));
		
		// save all the answers
		foreach ($_POST['answer'] as $id => $answer)
		{
			if (trim($answer))
			{
				$is_correct = $_POST['correct'][$id] == 1;
				add_answer($question_id, $answer, $is_correct);
			}
		}
		$message .= "New question saved! ";
		
		// build new relationships
		if ($_POST['term_checkbox'])
		{
			foreach ($_POST['term_checkbox'] as $term_ID => $data)
			{
				$mydb->add_relationship($question_id, $term_ID);
			}
			$message .= "Relationships Built! ";	
		}
		
		// do we need to rebuild the holes map
		$tmp_question = get_question_from_ID($question_id);

		// rebuild the holes map, if the new question falls into the parameters defined in default_terms_array
		if ($tmp_question->is_default_terms_array())
		{
			rebuild_questions_holes_map();
			$message .= "Holes map rebuilt! ";	
		}
		
		// save a comment
		$comment_text = "Question Created \n\n" . $tmp_question;
		
		// make a new comment
		$comment = new comment(-1, $user->get_ID(), $tmp_question->get_ID(), gmmktime(), $comment_text, QUESTION_CHANGED);
		
		// save the comment
		set_comment($comment);
	}
}

//update reports when needed
if ($_GET['update_report'])
{
	$report = $mydb->get_report_from_ID($_GET['update_report']);

	if ($_GET['new_status'] == "open")
	{
		$report->set_Status(REPORT_OPEN);
	}
	if ($_GET['new_status'] == "fixed")
	{
		$report->set_Status(REPORT_FIXED);
	}
	if ($_GET['new_status'] == "incorrect")
	{
		$report->set_Status(REPORT_INCORRECT);
	}
	if ($_GET['new_status'] == "clarified")
	{
		$report->set_Status(REPORT_CLARIFIED);
	}
	if ($_GET['new_status'] == "noaction")
	{
		$report->set_Status(REPORT_NOACTION);
	}
	
	set_report($report);
	
	$message .= "Report updated!";
	
	header( 'Location: ' . get_site_URL()  . 'admin/edit/' . $report->get_Question_ID() ) ;
}


// is a question being edited
if (($url_array[1] == "edit") && !$question_deleted) {
	$question = get_question_from_ID($url_array[2]);
	try {	
		$answers = $question->get_all_Answers();
	} 
	catch (Exception $e) {
		$message .= $e->getMessage();
	}
}


// a recomptue request was recieved
if ($_GET['recompute'])
{
	if ($_GET['recompute'] == "difficulty")
	{
		// remove all difficulty relationships
		$difficulty_terms = $mydb->get_terms("difficulty");
		
		foreach($difficulty_terms as $term)
		{
			$mydb->remove_relationship_given_Term_ID($term->get_ID());
		}
		
		$message .= "Difficulty relationships removed! ";
		
		// for each difficulty level get all questions - limits are calculated to the nearest 10
		$all_beginner_questions = get_questions_difficulty_limit(80, 100);
		$all_intermediate_questions = get_questions_difficulty_limit(40, 90);
		$all_expert_questions = get_questions_difficulty_limit(0, 60);
		
		$message .= "All questions loaded (Beginner " . count($all_beginner_questions) . ", Intermediate: " . count($all_intermediate_questions) . ", Expert: " . count($all_expert_questions) . ")!";
		
		// for each question, add the difficulty relationship
		$beginner_term = $mydb->get_term_from_taxonomy_and_name("difficulty", "Beginner");
		$intermediate_term = $mydb->get_term_from_taxonomy_and_name("difficulty", "Intermediate");
		$expert_term = $mydb->get_term_from_taxonomy_and_name("difficulty", "Expert");
		
		$message .= "Term IDs loaded (Beginner " . $beginner_term->get_ID() . ", Intermediate: " . $intermediate_term->get_ID() . ", Expert: " . $expert_term->get_ID() . ")!";
		
		foreach($all_beginner_questions as $question)
		{
			$mydb->add_relationship($question->get_ID(), $beginner_term->get_ID());
		}
		foreach($all_intermediate_questions as $question)
		{
			$mydb->add_relationship($question->get_ID(), $intermediate_term->get_ID());
		}
		foreach($all_expert_questions as $question)
		{
			$mydb->add_relationship($question->get_ID(), $expert_term->get_ID());
		}
		
		$message .= " Relationships rebuilt!";

	}	
}
// get the open reports (and the value for the menu)
$reports_open = $mydb->get_reports(REPORT_OPEN);
		
if ($reports_open && count($reports_open) > 0)
{
	$reports_menu_string = " (" . get_open_report_count($reports_open) . ")";
}

// display the page
set_page_subtitle("Turn left and administer all the things.");
include("view.php"); 
?>