<?php

$questionToBeCloned = get_question_from_ID($_GET['clone_question']);

// Adding a new question
// try to save the question
$new_question_id = add_question($questionToBeCloned->get_Text(), $questionToBeCloned->get_Section(), $questionToBeCloned->get_Notes());

// save all the answers

$answersToBeCloned = $questionToBeCloned->get_Answers(100);

foreach ($answersToBeCloned as $id => $answer) {
	add_answer($new_question_id, $answer->get_Text(), $answer->is_correct());
}

$message.= "Cloned question and answers saved! ";

// build new relationships
foreach ($questionToBeCloned->get_terms() as $term_ID => $term) {
	$mydb->add_relationship($new_question_id, $term->get_ID());
}
$message.= "Relationships Built! ";

// save a comment
$comment_text = "Question Cloned from \n\n" . $questionToBeCloned;

// make a new comment
$comment = new comment(-1, $user->get_ID(), $new_question_id, gmmktime(), $comment_text, QUESTION_CHANGED);

// save the comment
set_comment($comment);
?>