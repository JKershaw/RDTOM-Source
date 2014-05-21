<?php

// save a comment
$comment_text = "Question Deleted \n\n" . get_question_from_ID($_POST['question_id']);

// make a new comment
$comment = new comment(-1, $user->get_ID(), $_POST['question_id'], gmmktime(), $comment_text, QUESTION_DELETED);

// save the comment
set_comment($comment);

// delete the question
$mydb->remove_question_and_answers($_POST['question_id']);

// Stuff neded in the view and elsewhere
$message = "The question has been deleted. ";
$question_deleted = true;
?>