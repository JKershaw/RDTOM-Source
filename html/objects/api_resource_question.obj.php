<?php
class api_resource_question extends api_resource
{

	
	protected function build_XML($parameters)
	{
		
		if (is_numeric($parameters['ID']))
		{
			$question = get_question_from_ID($parameters['ID']);
		}
		else
		{
			$question = get_question_random();
		}
		
		$answers = $question->get_Answers();
		
		$this->out_XML->addChild("status_code", "200");
		$this->out_XML->addChild("api_version", "0.1");
		$this->out_XML->addChild("results");
		
		// save the question
		$XML_newquestion = $this->out_XML->results->addChild('question');
		$XML_newquestion->addChild('id', $question->get_ID());
		$XML_newquestion->addChild('text', htmlentities($question->get_Text()));
		$XML_newquestion->addChild('section', htmlentities($question->get_Section()));
		$XML_newquestion->addChild('notes', htmlentities($question->get_Notes()));
		$XML_newquestion->addChild('wftda_link', htmlentities($question->get_WFTDA_Link()));
		
		// save the answers
		foreach ($answers as $answer)
		{
			if ($answer->is_correct())
			{
				$is_correct_string = "yes";
			}
			else
			{
				$is_correct_string = "no";
			}
			$XML_newanswer = $this->out_XML->results->question->addChild('answer');
			$XML_newanswer->addChild('id', $answer->get_ID());
			$XML_newanswer->addChild('text', htmlentities($answer->get_Text()));
			$XML_newanswer->addChild('correct', $is_correct_string);
		}
	}
}
?>