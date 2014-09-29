<?php
include_once("FileCache");

class api_resource_questions extends api_resource
{
	protected function build_XML($parameters)
	{
		$fileCache = new FileCache();

		// is there a cached version?
		$cache_name = "questions_cache_" . md5(serialize($parameters));

		$cached_questions_XML = $fileCache->get($cache_name);

		if ($cached_questions_XML)
		{
			$this->resource_XML = new SimpleXMLElement($cached_questions_XML);;
			return;
		}
		
		global $default_terms_array;
		$questions = get_questions($default_terms_array);
		
		$XML_newquestions = $this->resource_XML->addChild('questions');
		
		foreach ($questions as $question)
		{
				
			// save the new question
			$XML_newquestion = $XML_newquestions->addChild('question');
			
			// generic question values
			$XML_newquestion->addChild('id', $question->get_ID());
			$XML_newquestion->addChild('text', htmlentities(stripslashes($question->get_Text())));
			$XML_newquestion->addChild('wftda_link', htmlentities($question->get_WFTDA_Link()));
			
			// notes might be optional
			if (preg_match('#\S#', htmlentities(stripslashes($question->get_Notes())))) // Checks for non-whitespace character
				$XML_newquestion->addChild('notes', htmlentities(stripslashes($question->get_Notes())));
			else
				$XML_newquestion->addChild('notes');

			// the sections
			$XML_sections = $XML_newquestion->addChild('sections');
			foreach ($question->get_Sections() as $alternate_section)
			{
				$XML_sections->addChild('section', htmlentities($alternate_section));
			}
			
			// the answers
			$XML_answers = $XML_newquestion->addChild('answers');
			
			// save the answers
			foreach ($question->get_Answers() as $answer)
			{
				$XML_newanswer = $XML_answers->addChild('answer');
				$XML_newanswer->addChild('id', $answer->get_ID());
				$XML_newanswer->addChild('text', htmlentities($answer->get_Text()));
				
				if ($answer->is_correct())
				{
					$XML_newanswer->addChild('correct', "true");
				}
				else
				{
					$XML_newanswer->addChild('correct', "false");
				}
			}
		
		}
		
		// set the cache
		$fileCache->set($cache_name, $this->resource_XML->asXML(), 7200);
	}
}
?>