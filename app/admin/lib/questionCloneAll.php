<?php
exit;

// $terms_array = array("rule-set" => "WFTDA6");
// $allQuestions = get_questions($terms_array);

// foreach ($allQuestions as $id => $questionToBeCloned) {
	
// 	// Clone
// 	$new_question_id = clone_question($mydb, $questionToBeCloned);
	
// 	// save a comment
// 	$comment_text = "Question Cloned from \n\n" . $questionToBeCloned;
// 	$comment = new comment(-1, $user->get_ID(), $new_question_id, gmmktime(), $comment_text, QUESTION_CHANGED);
// 	set_comment($comment);
	
// 	// Edit existing question
// 	updateToWFTDA7($mydb, $user, $questionToBeCloned);
// }

// $message.= "WOAH! ";

// function clone_question($mydb, $questionToBeCloned) {
	
// 	// save the new question
// 	$new_question_id = add_question($questionToBeCloned->get_Text(), $questionToBeCloned->get_Section(), $questionToBeCloned->get_Notes());
	
// 	// save all the answers
// 	$answersToBeCloned = $questionToBeCloned->get_Answers(100);
	
// 	foreach ($answersToBeCloned as $id => $answer) {
// 		add_answer($new_question_id, $answer->get_Text(), $answer->is_correct());
// 	}
	
// 	// save the tags
// 	foreach ($questionToBeCloned->get_terms() as $term_ID => $term) {
// 		$mydb->add_relationship($new_question_id, $term->get_ID());
// 	}
	
// 	return $new_question_id;
// }

// function updateToWFTDA7($mydb, $user, $question) {
	
// 	//remove all tags
// 	$mydb->remove_relationship_given_Question_ID($question->get_ID());
	
// 	//add back all but rule-set tags
// 	foreach ($question->get_terms() as $term_ID => $term) {
// 		if ($term->get_taxonomy() != "rule-set") {
// 			$mydb->add_relationship($question->get_ID(), $term->get_ID());
// 		}
// 	}
	
// 	// add the WFTDA 7 tag
// 	$WFTDA7_term_ID = "23";
// 	$mydb->add_relationship($question->get_ID(), $WFTDA7_term_ID);
	
// 	// decriment the first digit of the section number
// 	$section = $question->get_Section();
	
// 	if (strpos($section, '.') !== false) {
// 		$section_parts = explode(".", $section);
		
// 		$section_parts[0] = intval($section_parts[0]) - 1;
		
// 		$section = implode(".", $section_parts);
// 	}
	
// 	edit_question($question->get_ID(), $question->get_Text(), $section, $question->get_Notes());
	
// 	// save a comment
// 	$comment_text = "Question run through the update script to tag it WFTDA7";
	
// 	// make a new comment
// 	$comment = new comment(-1, $user->get_ID(), $question->get_ID(), gmmktime(), $comment_text, QUESTION_CHANGED);
	
// 	// save the comment
// 	set_comment($comment);
// }
?>