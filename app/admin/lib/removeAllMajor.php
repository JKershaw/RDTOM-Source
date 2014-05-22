<?php

$terms_array = array("rule-set" => "WFTDA7");
$allQuestions = get_questions($terms_array);

foreach ($allQuestions as $id => $question) {
	$answers = $question->get_Answers(100);

	foreach ($answers as $id => $answer) {

		$text = $answer->get_Text();

		$text = str_ireplace("major", "", $text);

		$text = trim($text);

		update_answer($answer, $text);
	}
}

?>