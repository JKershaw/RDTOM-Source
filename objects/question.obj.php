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
	private $answers_array;
	
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
	
	public function get_Answers($max_num_answers = 4)
	{
		global $mydb;
		$answers = get_answers_from_question_ID($this->ID);
		
		
		// now we have all the answers, split them into correct and wrong arrays.
		$correct_answers = Array();
		$wrong_answers = Array();
		
		foreach ($answers as $answer)
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
		
		if (count($correct_answers) < 1)
		{
			throw new exception("Uh oh... No correct answers found for question number " . $this->ID);
		}
		
		if (count($wrong_answers) < 1)
		{
			throw new exception("Error: No wrong answers found");
		}
		
		//select one right answer and $max_num_answers-1 wrong answers;
		$out_answers[] = $correct_answers[array_rand($correct_answers)];
		
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
				$out_answers[] = $wrong_answers[$wrong_index];
			}
		}
		else
		{
			$out_answers[] = $wrong_answers[array_rand($wrong_answers)];
		}
		
		shuffle($out_answers);
		
		return $out_answers;
	}
	
	public function get_WFTDA_Link()
	{
		// if there's a section
		if ($this->get_Section())
		{
			// if it looks like a valid WFTDA rule
			if (preg_match("@^([1-9][\.]?)+@", $this->get_Section()))
			{
				// get the first two values
				$section_array = explode(".", $this->get_Section());
				if (count($section_array) == 1)
				{
					return "http://wftda.com/rules/20100526/section/" . $section_array[0];
				}
				elseif (count($section_array) <= 3)
				{
					return "http://wftda.com/rules/20100526/section/" . $section_array[0] . "." . $section_array[1];
				}
				else
				{
					return "http://wftda.com/rules/20100526/section/" . $section_array[0] . "." . $section_array[1] . "." . $section_array[2];
				}
			}
		}
		
		return false;
	}
	
	public function get_all_Answers($max_num_answers = 4, $get_ResponseRate = false)
	{
		global $mydb;
		$answers_array = get_answers_from_question_ID($this->ID);
		
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
				
				foreach ($answers_array as $answer)
				{
					$percentage = round(($Responseperc_array[$answer->get_ID()] / $sum_count) * 100);
					$answer->set_SelectionPerc($percentage);
				}
			}
		}
		
		return $answers_array;
		
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
	
	public function get_terms($req_taxonomy)
	{
		global $mydb;
		$terms = $mydb->get_terms($req_taxonomy, $this->ID);
		if ($terms)
		{
			return $terms;
		}
		else
		{
			return false;
		}
	}
}