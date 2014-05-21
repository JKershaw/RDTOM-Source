<?php

$questionToBeCloned = get_question_from_ID($_POST['clone_question']);

// Adding a new question
// try to save the question
$question_id = add_question($questionToBeCloned->get_Text(), $questionToBeCloned->get_Section(), $questionToBeCloned->get_Notes());

// save all the answers

$answersToBeCloned = $questionToBeCloned->get_Answers(100);

foreach ($answersToBeCloned as $id => $answer) {
	add_answer($question_id, $answer->get_Text(), $answer->is_correct());
}

$message.= "Cloned question and answers saved! ";

// // build new relationships
// if ($_POST['term_checkbox']) {
// 	foreach ($_POST['term_checkbox'] as $term_ID => $data) {
// 		$mydb->add_relationship($question_id, $term_ID);
// 	}
// 	$message.= "Relationships Built! ";
// }

// save a comment
$comment_text = "Question Cloned from \n\n" . $questionToBeCloned;

// make a new comment
$comment = new comment(-1, $user->get_ID(), $question_id, gmmktime(), $comment_text, QUESTION_CHANGED);

// save the comment
set_comment($comment);
?>