<?php

class api_resource_questions extends api_resource
{
	protected function build_XML($parameters)
	{

		$fileCache = new FileCache();
		
		$cache_name = "questions_cache_" . md5(serialize($parameters));
			
		// is there a cached version?
		if (!$parameters['nocache'])
		{
			$cached_questions_XML = $fileCache->get($cache_name);
		}
		if ($cached_questions_XML)
		{
			$this->resource_XML = new SimpleXMLElement($cached_questions_XML);;
			return;
		}
		
		global $default_terms_array;
		
		if ($parameters['search'])
		{
			// are they searching for a given difficulty?
			if (in_array($parameters['search'], Array("difficulty:beginner", "difficulty:intermediate", "difficulty:expert")))
			{
				// user wants a difficulty
				$terms_array["rule-set"] = "WFTDA6";
				$terms_array["tag"] = "Test Question";
				
				if ($parameters['search'] == "difficulty:beginner")
					$terms_array["difficulty"] = "Beginner";
				if ($parameters['search'] == "difficulty:intermediate")
					$terms_array["difficulty"] = "Intermediate";
				if ($parameters['search'] == "difficulty:expert")
					$terms_array["difficulty"] = "Expert";
				
				$questions = get_questions($terms_array);
				
			}
			else
			{
				$questions = get_questions_search($parameters['search']);
			}
		}
		else
		{
			$questions = get_questions($default_terms_array);
		}
		
		$XML_newquestions = $this->resource_XML->addChild('questions');
		
		if ($questions)
		{
			foreach ($questions as $question)
			{
					
				// save the new question
				$XML_newquestion = $XML_newquestions->addChild('question');
				
				// generic question values
				$XML_newquestion->addChild('id', $question->get_ID());
				$XML_newquestion->addChild('text', htmlentities(stripslashes($question->get_Text())));
				$XML_newquestion->addChild('wftda_link', htmlentities($question->get_WFTDA_Link()));
			
				// meta data
				$terms = $question->get_terms();
				
				$XML_questionmeta = $XML_newquestion->addChild('terms');
				if ($terms)
				{
					foreach ($terms as $term)
					{
						$XML_questionmeta->addChild($term->get_taxonomy(), $term->get_Name());
					}
				}
				
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
				
				//if we're searching, get ALL answers
				if ($parameters['search'])
				{
					$all_answers = $question->get_all_Answers();
				}
				else
				{
					$all_answers = $question->get_Answers();
				}
				
				// save the answers
				foreach ($all_answers as $answer)
				{
					$XML_newanswer = $XML_answers->addChild('answer');
					$XML_newanswer->addChild('id', $answer->get_ID());
					
					/*
					 * // how to detect non UTF-8 characters
					if (!mb_detect_encoding($answer->get_Text(), 'UTF8', true))
					{	
						$XML_newanswer->addChild('this one', "foo");
					}
					*/
					
					$XML_newanswer->addChild('text', utf8_encode(htmlentities($answer->get_Text())));
					
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
		}
		
		// set the cache
		$fileCache->set($cache_name, $this->resource_XML->asXML(), 7200);
	}
}
?>
