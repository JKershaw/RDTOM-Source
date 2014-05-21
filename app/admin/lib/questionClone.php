<?php
$questionToBeCloned = get_question_from_ID($_GET['clone_question']);

$new_question_id = clone_question($mydb, $questionToBeCloned);

$message.= "The question has been cloned.";

if ($_GET['updateToWFTDA7']) {
	updateToWFTDA7($mydb, $user, $questionToBeCloned);
	$message.= " Updated to WFTDA 7";
}

// save a comment
$comment_text = "Question Cloned from \n\n" . $questionToBeCloned;

// make a new comment
$comment = new comment(-1, $user->get_ID(), $new_question_id, gmmktime(), $comment_text, QUESTION_CHANGED);

// save the comment
set_comment($comment);

function clone_question($mydb, $questionToBeCloned) {
	
	// save the new question
	$new_question_id = add_question($questionToBeCloned->get_Text(), $questionToBeCloned->get_Section(), $questionToBeCloned->get_Notes());
	
	// save all the answers
	$answersToBeCloned = $questionToBeCloned->get_Answers(100);
	
	foreach ($answersToBeCloned as $id => $answer) {
		add_answer($new_question_id, $answer->get_Text(), $answer->is_correct());
	}
	
	// save the tags
	foreach ($questionToBeCloned->get_terms() as $term_ID => $term) {
		$mydb->add_relationship($new_question_id, $term->get_ID());
	}
	
	return $new_question_id;
}

function updateToWFTDA7($mydb, $user, $question) {
	
	// add the WFTDA 7 tag
	$WFTDA7_term_ID = "23";
	$mydb->add_relationship($question->get_ID(), $WFTDA7_term_ID);
	
	// decriment the first digit of the section number
	$section = $question->get_Section();
	
	if (strpos($section, '.') !== false) {
		$section_parts = explode(".", $section);
		
		$section_parts[0] = intval($section_parts[0]) - 1;

		$section = implode(".", $section_parts);
	}
	
	edit_question($question->get_ID(), $question->get_Text(), $section, $question->get_Notes());
	
	// save a comment
	$comment_text = "Question run through the update script to tag it WFTDA7";
	
	// make a new comment
	$comment = new comment(-1, $user->get_ID(), $question->get_ID(), gmmktime(), $comment_text, QUESTION_CHANGED);
	
	// save the comment
	set_comment($comment);
}
?>