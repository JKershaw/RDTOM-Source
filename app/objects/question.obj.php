<?php
class question
{
	private $ID;
	private $Text;
	private $Section;
	private $Added;
	private $Notes;
	private $Source;
	
	private $SuccessRate;
	private $ResponseCount;
	
	private $all_answers = Array();
	private $answers = Array();
	
	private $all_terms;
	
	private $correct_answer;
	private $wrong_answers;
	
	function __construct(
		$req_ID,
		$req_Text,
		$req_Section,
		$req_Added,
		$req_Notes)
	{
		$this->ID = $req_ID;
		$this->Text = $req_Text;
		$this->Section = $req_Section;
		$this->Added = $req_Added;
		$this->Notes = $req_Notes;
	}
	
	public function get_Text()
	{
		return $this->Text;
	}

	public function get_ID()
	{
		return $this->ID;
	}

	public function get_Source()
	{
		$source_terms = $this->get_terms("source");
		if ($source_terms)
		{
			foreach($source_terms as $source_term)
			{
				return $source_term->get_Name();
			}
		}
		return false;
	}

	public function get_Author()
	{
		global $mydb;
		$source_terms = $this->get_terms("author-id");
		if ($source_terms)
		{
			foreach($source_terms as $source_term)
			{
				$tmp_user = $mydb->get_user_from_ID($source_term->get_Name());
				return $tmp_user->get_Name();
			}
		}
		return false;
	}

	public function get_Section()
	{
		// get the first section
		$section_array = $this->get_Sections();
		return $section_array[0];
	}

	public function get_Section_String()
	{
		// get the first section
		return $this->Section;
	}

	public function get_Sections()
	{
		// does the question only have one section or many?
		if ($this->Section)
		{
			$section_array = explode(",", $this->Section);
			
			foreach($section_array as $id => $section_string)
			{
				$section_array[$id] = trim($section_string);
			}
		}
		else
		{
			$section_array = array();
		}
		
		return $section_array;
	}

	public function get_Added()
	{
		return $this->Added;
	}

	public function get_Notes()
	{
		return $this->Notes;
	}

	public function get_Fixed_Answers()
	{

	}
	
	public function get_Answers($max_num_answers = 4, $random_seed = false)
	{
		
		// generate random correct & wrong answers
		global $mydb, $last_correct_answer_index;
		
		// now we have all the answers, split them into correct and wrong arrays.
		$correct_answers = Array();
		$wrong_answers = Array();
		
		// have we already set the correct & wrong answers?
		if ($this->correct_answer && $this->wrong_answers)
		{
			$wrong_answers = $this->wrong_answers;
			$correct_answers[] = $this->correct_answer;
		}
		else
		{
			// if not already set, get a full array of them
			foreach ($this->get_all_Answers() as $answer)
			{
				if ($answer->is_correct())
				{
					$correct_answers[] = $answer;
				}
				else
				{
					$wrong_answers[] = $answer;
				}
			}
		}
		
		// check for errors
		if (count($correct_answers) < 1)
		{
			throw new exception("Uh oh... No correct answers found for question number " . $this->ID);
		}
		
		if (count($wrong_answers) < 1)
		{
			throw new exception("Error: No wrong answers found for question number " . $this->ID);
		}
	
		// if we are seeding the random settings from a known point, use this
		if ($random_seed)
		{
			srand($random_seed);
			mt_srand($random_seed);
		}
		
		//select one right answer and $max_num_answers-1 wrong answers;
		$this->answers[] = $correct_answers[array_rand($correct_answers)];
		
		$wrong_answers_wanted = $max_num_answers-1;
		if ($wrong_answers_wanted > count($wrong_answers))
		{
			$wrong_answers_wanted = count($wrong_answers);
		}
		
		if ($wrong_answers_wanted > 1)
		{
			$wrong_indexs = array_rand($wrong_answers, $wrong_answers_wanted);
		
			foreach ($wrong_indexs as $wrong_index)
			{
				$this->answers[] = $wrong_answers[$wrong_index];
			}
		}
		else
		{
			$this->answers[] = $wrong_answers[array_rand($wrong_answers)];
		}
		
		// we have an array where entry [0] is always correct
		
		
		// rotate the array a random number of times
		$number_of_rotations = round(mt_rand(0, $max_num_answers*2));
		//echo "<br /> Rotations: " . $number_of_rotations . " ";
		
		for($i=0; $i<=$number_of_rotations;$i++)
		{
			array_push($this->answers, array_shift($this->answers));
		}
		
		// if this correct answer index is the same as the last correct answer index, rotate again
		foreach ($this->answers as $index => $answer)
		{
			// we have the right answer
			if ($answer->is_correct())
			{
				//echo " index: " . $index . " ";
				// is it the same as the last one?
				if ($last_correct_answer_index == $index)
				{
					// rotate the array in a random direction
					$random_number = round(mt_rand(0,100));
					
					if ($random_number > 20)
					{
						if ($random_number >= 60)
						{
							//echo "<";
							array_push($this->answers, array_shift($this->answers));
						}
						else
						{
							//echo ">";
							array_unshift($this->answers, array_pop($this->answers));
						}
					}
					//shuffle($this->answers);
				}
				else
				{
					//echo "-";
					// different answer, all is fine, remeber what this index is for next time
					$last_correct_answer_index = $index;
				}
				
				// we found the right answer, don't care about anything else.
				break;
			}
			
		}
		
		return $this->answers;
	}
	
	public function get_WFTDA_Link()
	{
		// if there's a section
		if ($this->get_Section() && $this->is_taxonomy_and_value("rule-set", "WFTDA6"))
		{
			// if it looks like a valid WFTDA rule
			if (preg_match("@^([1-9][\.]?)+@", $this->get_Section()))
			{
				// get the first two values
				$section_array = explode(".", $this->get_Section());
				if (count($section_array) == 1)
				{
					return "http://wftda.com/rules/20130101/section/" . $section_array[0];
				}
				elseif (count($section_array) <= 3)
				{
					return "http://wftda.com/rules/20130101/section/" . $section_array[0] . "." . $section_array[1];
				}
				else
				{
					return "http://wftda.com/rules/20130101/section/" . $section_array[0] . "." . $section_array[1] . "." . $section_array[2];
				}
			}
		}
		
		return false;
	}
	
	public function get_all_Answers($max_num_answers = 4, $get_ResponseRate = false)
	{
		global $mydb;
		if ($this->all_answers)
		{
			return $this->all_answers;
		}
		$this->all_answers = get_answers_from_question_ID($this->ID);
		
		if ($get_ResponseRate)
		{
			$Responseperc_array = get_answer_response_perc($this->ID);
			
			$sum_count = 0;
			if ($Responseperc_array)
			{
				foreach ($Responseperc_array as $count)
				{
					$sum_count += $count;
				}
				
				foreach ($this->all_answers as $answer)
				{
					$percentage = round(($Responseperc_array[$answer->get_ID()] / $sum_count) * 100);
					$answer->set_SelectionPerc($percentage);
				}
			}
		}
		
		return $this->all_answers;
		
	}
	
	public function set_SuccessRate($req_SuccessRate)
	{
		settype($req_SuccessRate, "integer");
		$this->SuccessRate = $req_SuccessRate;
	}
	
	public function get_SuccessRate()
	{
		if (!$this->SuccessRate)
		{
			$correct_perc = (integer)get_question_correct_perc($this->get_ID());
			$this->set_SuccessRate($correct_perc);

		}
		return $this->SuccessRate;
	}
	
	public function set_ResponseCount($req_ResponseCount)
	{
		settype($req_ResponseCount, "integer");
		$this->ResponseCount = $req_ResponseCount;
	}
	
	public function get_ResponseCount()
	{
		global $mydb;
		if (!$this->ResponseCount)
		{
			$ResponseCount = (integer)$mydb->get_response_count_from_Question_ID($this->get_ID());
			$this->set_ResponseCount($ResponseCount);
		}
		return $this->ResponseCount;
	}
	
	public function is_answers_different($req_new_answers)
	{
		// There may be no answers in the database
		try {
			$existing_answers = $this->get_all_Answers();
		} 
		catch (Exception $e) 
		{
			if ($req_new_answers)
			{
				return true;
			} 
			else 
			{
				return false;
			}
		}
		

		// are the numbers different?
		if (count($existing_answers) != count($req_new_answers))
		{
			return true;
		}
		
		// for each new answer
		foreach ($req_new_answers as $new_answer)
		{
			// does it already exist?
			foreach ($existing_answers as $existing_answer)
			{
				if (
					($existing_answer->is_correct() == $new_answer->is_correct()) &&
					($existing_answer->get_Text() == $new_answer->get_Text()) &&
					($existing_answer->get_Question_ID() == $new_answer->get_Question_ID()))
					{
						// found a match
						continue 2;
					}
			}
			// if we've not found a match, the answers must be different
			return true;
		}
		
		// if all match, they must be the same
		return false;
	}
	
	public function get_URL()
	{
		return get_site_URL() . "question/" . $this->ID;
	}
	
	public function get_terms($req_taxonomy = false)
	{
		// set up the local cached copy of the terms
		if (!$this->all_terms)
		{
			global $mydb;
			$this->all_terms = $mydb->get_terms(false, $this->ID);
		}
		
		if ($req_taxonomy)
		{
			// find the terms from the cached copy, given a specific taxonomy
			if ($this->all_terms)
			{
				foreach ($this->all_terms as $term)
				{
					if ($term->get_taxonomy() == $req_taxonomy)
					{
						$terms[$term->get_ID()] = $term;
					}
				}
				
				// return value
				if ($terms)
				{
					return $terms;
				}
			}
		}
		else
		{
			return $this->all_terms;
		}
		
		return false;
	}
	
	public function is_relationship_true($req_taxonomy, $req_termname)
	{
		global $mydb;
		$terms = $this->get_terms($req_taxonomy);
		if ($terms)
		{
			foreach ($terms as $term)
			{
				if ($term->get_Name() == $req_termname)
				{
					return true;
				}
			}
		}
		
		return false;
		
	}
	
	public function is_default_terms_array()
	{
		global $default_terms_array;
		// return true if this question falls within any of the default_terms_array
		
		// for each of the default taxonomy value pairs
		
		foreach ($default_terms_array as $taxonomy => $value)
		{
			// get all the terms for this taxonomy, if they exist
			$terms = $this->get_terms($taxonomy);
			
			if ($terms)
			{
				// otherwise,return true if there's a partial match
				foreach ($terms as $term)
				{
					if ($value == $term->get_Name())
					{
						return true;
						//echo "Holes map not built because there are terms which do not match in the $taxonomy taxonomy ($value != " . $term->get_Name() . ")";
						//break 2;
					}
				}
			}
		}
		
		return false;
	}
	
	public function is_taxonomy_and_value($taxonomy, $value)
	{
		$terms = $this->get_terms($taxonomy);
			
		if ($terms)
		{
			// otherwise,return true if there's a partial match
			foreach ($terms as $term)
			{
				if ($value == $term->get_Name())
				{
					return true;
				}
			}
		}
		return false;
	}
	
	public function get_reports($report_status = REPORT_OPEN)
	{
		return get_reports_from_question_ID($this->get_ID(), $report_status);
	}
	
	public function get_comments()
	{
		return get_comments_from_question_ID($this->get_ID());
	}
	
	public function __toString()
    {
        
        $answers = $this->get_all_Answers();
        
        $out .= "\n" . $this->get_Text() . "\n\n";
        
        foreach ($answers as $answer)
        {
        	if ($answer->is_correct())
			{
				$out .=  "[Correct] ";
			}
			else
			{
				$out .=  "          ";
			}
			$out .= stripslashes($answer->get_Text()) . "\n";
        }
        
        if ($this->get_terms(true))
        {
        	$out .= "\nTerms: " . implode(", ", $this->get_terms(true)) . "\n";
        }
        else
        {
        	$out .= "\nTerms: \n";
        }
        return $out;
    }
    
    public function set_correct_answer($req_answer)
    {
    	if ($req_answer->is_correct())
    	{
    		$this->correct_answer = $req_answer;
    	}
    	else
    	{
    		throw new exception ("Attempted to save a wrong answer as a correct answer");
    	}
    }
    
    public function set_wrong_answers($req_answers)
    {
    	foreach ($req_answers as $req_answer)
    	{
	    	if (!$req_answer->is_correct())
	    	{
    			$this->wrong_answers[] = $req_answers;
	    	}
	    	else
	    	{
	    		throw new exception ("Attempted to save a correct answer as a wrong answer");
	    	}
    	}
    }
}