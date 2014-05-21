<?php

// editing a post

// save all the answers submitted into an array
foreach ($_POST['answer'] as $id => $answer) {
    if (trim($answer)) {
        $is_correct = $_POST['correct'][$id] == 1;
        $temp_answer_array[] = new answer(-1, $_POST['question_id'], trim($answer), $is_correct);
    }
}

$tmp_question = get_question_from_ID($_POST['question_id']);

$old_question_string = (string)$tmp_question;

// have the answers changed? There may not be any answers.
if ($temp_answer_array && ($tmp_question->is_answers_different($temp_answer_array))) {
    
    // delete existing post & questions
    $mydb->remove_answers_given_questionID($tmp_question->get_ID());
    $message = "Answers deleted! ";
    
    // save all the answers
    foreach ($_POST['answer'] as $id => $answer) {
        if (trim($answer)) {
            $is_correct = $_POST['correct'][$id] == 1;
            add_answer($tmp_question->get_ID(), trim($answer), $is_correct);
        }
    }
    $message.= "Answers saved! ";
} else {
    $message.= "Answers unchanged! ";
}

// edit the question
edit_question($tmp_question->get_ID(), $_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));
$message.= "Question edited! ";

// check the applicable rule set
// remove all relationships
$mydb->remove_relationship_given_Question_ID($tmp_question->get_ID());
$message.= "Relationships Removed! ";

// build new ones
if ($_POST['term_checkbox']) {
    foreach ($_POST['term_checkbox'] as $term_ID => $data) {
        $mydb->add_relationship($tmp_question->get_ID(), $term_ID);
    }
    $message.= "Relationships Rebuilt! ";
}

// rebuild the holes map, if the new question falls into the parameters defined in default_terms_array
if ($tmp_question->is_default_terms_array()) {
    rebuild_questions_holes_map();
    $message.= "Holes map rebuilt! ";
}

// save a comment
$comment_text = "Question Edited \n\nFrom: \n " . $old_question_string . " \nTo: \n" . get_question_from_ID($tmp_question->get_ID());

// make a new comment
$comment = new comment(-1, $user->get_ID(), $tmp_question->get_ID(), gmmktime(), $comment_text, QUESTION_CHANGED);

// save the comment
set_comment($comment);
?>