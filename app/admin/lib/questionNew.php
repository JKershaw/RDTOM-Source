<?php

// Adding a new question
// try to save the question
$question_id = add_question($_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));

// save all the answers
foreach ($_POST['answer'] as $id => $answer) {
	if (trim($answer)) {
		$is_correct = $_POST['correct'][$id] == 1;
		add_answer($question_id, $answer, $is_correct);
	}
}
$message.= "New question saved! ";

// build new relationships
if ($_POST['term_checkbox']) {
	foreach ($_POST['term_checkbox'] as $term_ID => $data) {
		$mydb->add_relationship($question_id, $term_ID);
	}
	$message.= "Relationships Built! ";
}

// do we need to rebuild the holes map
$tmp_question = get_question_from_ID($question_id);

// rebuild the holes map, if the new question falls into the parameters defined in default_terms_array
if ($tmp_question->is_default_terms_array()) {
	rebuild_questions_holes_map();
	$message.= "Holes map rebuilt! ";
}

// save a comment
$comment_text = "Question Created \n\n" . $tmp_question;

// make a new comment
$comment = new comment(-1, $user->get_ID(), $tmp_question->get_ID(), gmmktime(), $comment_text, QUESTION_CHANGED);

// save the comment
set_comment($comment);
?>