<?php
class test
{
	private $ID;
	private $Questions;
	
	private $pass_percentage;
	private $number_of_questions;
	private $difficulty;
	private $output_format = "HTML";
	
	private $seed;
	
	function __construct()
	{
		// generate a seed for this test
		list($usec, $sec) = explode(' ', microtime());
  		$this->seed = (float) $sec + ((float) $usec * 100000) + rand(0, 1000000);
		
	}
	
	public function populate($opt_number_of_questions = 45, $difficulty = "intermediate", $question_IDs = false)
	{
		if ($opt_number_of_questions < 1)
		{
			$opt_number_of_questions = 1;
			//throw new exception("Could not generate test, number of requested questions too low or missing");
		}
		
		// get all applicable questions	
		if ($question_IDs)
		{
			if (is_array($question_IDs) && (count($question_IDs) < 1))
			{
				throw new exception("Could not generate test, number of requested questions too low or missing");
			}
			// we know what questions we want;
			foreach($question_IDs as $question_ID)
			{
				try {
					$all_questions[] = get_question_from_ID($question_ID);
				} catch (Exception $e) {
				}
			}
		}
		else
		{
			
			// get all the applicable questions
			
			$terms_array = array();
			
			if ($difficulty == "beginner")
			{
				$terms_array["difficulty"] = "Beginner";
			}
			elseif ($difficulty == "expert")
			{
				$terms_array["difficulty"] = "Expert";
			}
			elseif ($difficulty == "intermediate") // default
			{
				$terms_array["difficulty"] = "Intermediate";
			}
			
			$this->difficulty = $difficulty;
			
			// we want only WFTDA 5 questions and questions tagged with "Test Question" to be shown
			$terms_array["rule-set"] = "WFTDA6";
			$terms_array["tag"] = "Test Question";
			
			$all_questions = get_questions($terms_array);
		}
			
		// clean the input
		settype($opt_number_of_questions, "integer");
		
		if ($opt_number_of_questions < 1)
		{
			$opt_number_of_questions = 1;
		}
		
		if ($opt_number_of_questions > count($all_questions))
		{
			$opt_number_of_questions = count($all_questions);
		}
		
		// randomly get a subsection of the array the correct length
		
		$random_questions_array_keys = array_rand($all_questions, $opt_number_of_questions);
		if (is_array($random_questions_array_keys))
		{
			foreach ($random_questions_array_keys as $random_question_array_key)
			{
				$test_questions[] = $all_questions[$random_question_array_key];
			}
		}
		else
		{
			$test_questions[] = $all_questions[$random_questions_array_keys];
		}		
		
		$this->Questions = $test_questions;
		$this->number_of_questions = $opt_number_of_questions;
		
	}
	
	public function set_seed($req_seed)
	{
		$this->seed = $req_seed;
	}
	
	public function get_difficulty()
	{
		if ($this->difficulty == "beginner")
		{
			return "beginner";
		}
		elseif ($this->difficulty == "expert")
		{
			return "expert";
		}
		elseif ($this->difficulty == "intermediate")
		{
			return "intermediate";
		}
		else
		{
			return "mixed";
		}
		return false;
	}
	
	public function set_pass_percentage($req_pass_percentage)
	{
		settype($req_pass_percentage, "integer");
		$this->pass_percentage = $req_pass_percentage;
	}
	
	public function get_ID()
	{
		return $this->ID;
	}
	
	public function get_Questions()
	{
		return $this->Questions;
	}
	
	public function get_pass_mark()
	{
		return round($this->number_of_questions * ($this->pass_percentage / 100));
	}
	
	public function set_output_format($req_output_format)
	{
		$this->output_format = $req_output_format;
	}
	
	public function get_output_format()
	{
		if ($this->output_format == "HTML")
		{
			return "HTML";
		}
		return "interactiveHTML";
	}
	
	public function get_formatted_output($type = false)
	{
		/*
		 * Make a "Test" object which can produce its output in many formats:
		 * 		- HTML
		 * 		- .doc
		 * 		- interactive HTML (JavaScript)
		 * 		- .txt
		 * 		- .pdf?
		 */
	
		if (!$this->Questions)
		{
			Throw new exception("No questions found for the test.");
		}
		
		if (!$type)
		{
			$type = $this->output_format;
		}
		else
		{
			$this->set_output_format($type);
		}
		
		switch($this->get_output_format())
		{
			case "HTML":
				return $this->get_formatted_output_HTML();
				break;
			case "interactiveHTML":
				default:
				return $this->get_formatted_output_interactiveHTML();
				break;
				
				
		} 
	}
	
	private function get_formatted_output_HTML()
	{
		$out = '
		
		<p>
			<strong>Name:</strong>___________________________________________
		</p>
		<p>
			<strong>Skate Name:</strong>___________________________________________
		</p>
		<p>
			<strong>Skate Number:</strong>_______
		</p>
		<p>
			<strong>Date:</strong>_____________
		</p>
		
		<table style="width: 100%; margin: 0 0 1em;">
			<tr>
				<td><strong>Pass mark:</strong> ' . $this->get_pass_mark() . ' / ' . $this->number_of_questions . '</td>
				<td style="text-align: center;"><strong>Score:</strong>_____</td>
				<td style="text-align: right;">PASS / FAIL</td>
			</tr>
		</table>
			  
	
		<table class="test_table">
			<thead>
				<tr>
					<td style="width:50px; text-align:center">#</td>
					<td style="text-align:center">Question</td>
					<td style="width:75px; text-align:center">Answer</td>
					<td style="width:75px; text-align:center">Mark</td>
				</tr>
			</thead>
			<tbody style="vertical-align:top;">
				';
				$i = 0;
				foreach ($this->Questions as $question)
				{
					$answers = $question->get_Answers(4, $this->seed);
					$i++;
					
					$out .= "
					<tr style=\"page-break-inside: avoid\">
						<td style=\"text-align:center; padding-top: 5px;\"><strong>" . $i . "</strong></td>
						<td style=\"padding: 5px; page-break-inside: avoid\">
							" . htmlentities(stripslashes($question->get_Text()));
							
					$out .= "
							<ol type=\"A\" style=\"list-style-type: upper-alpha;\">";
							$answer_count = 0;
							foreach ($answers as $answer)
							{
								
								if ($answer->is_correct())
								{
									$answer_array[$i] = chr(65+ $answer_count);
									$section_array[$i] = $question->get_Section();
								}
								$answer_count++;
								
								$quick_answer[] = $answer->get_ID();
								
								$out .= "<li>" . htmlentities(stripslashes($answer->get_Text())) . "</li>";
							}
					$out .= "
							</ol>
							
						</td>
						<td></td>
						<td style=\"text-align:center\">
						</td>
					</tr>";
					
				}
		
		$out .= "
			</tbody>
		</table>";
				
		$out .= "
		<p style=\"page-break-before: always;\">Answers:</p>
		<p class=\"small_p\">";
		foreach ($answer_array as $Question_Number => $Answer)
		{
			$answers_string[] = $Question_Number . " = " . $Answer . " (see " . $section_array[$Question_Number] . ")";
		}
		$out .= implode("<br />", $answers_string);
		
		$out .= "
		</p>
		";
		
		return $out;
	}
	

	private function get_formatted_output_interactiveHTML()
	{
		$out = '
		
		<table style="width: 100%; margin: 0 0 1em;">
			<tr>
				<td style="width: 25%;"><strong>Pass mark:</strong> ' . $this->get_pass_mark() . ' / ' . $this->number_of_questions . '</td>
				<td style="width: 25%; text-align: center;" id="text_finalscore"></td>
				<td style="text-align: left;" id="text_passorfail"></td>
			</tr>
		</table>
			  
	
		<table class="test_table">
			<thead>
				<tr>
					<td style="width:50px; text-align:center">#</td>
					<td style="text-align:center">Question</td>
					<td style="width:75px; text-align:center">Answer</td>
					<td style="width:75px; text-align:center">Mark</td>
				</tr>
			</thead>
			<tbody style="vertical-align:top;">
				';
				$i = 0;
				foreach ($this->Questions as $question)
				{
					$answers = $question->get_Answers(4, $this->seed);
					$i++;
					
					$out .= "
					<tr style=\"page-break-inside: avoid\">
						<td style=\"text-align:center; padding-top: 5px;\"><strong>" . $i . "</strong></td>
						<td style=\"padding: 5px; page-break-inside: avoid\">
							" . htmlentities(stripslashes($question->get_Text()));
							
					$out .= "
							<ol type=\"A\" style=\"list-style-type: upper-alpha;\">";
					
							$answer_count = 0;
							foreach ($answers as $answer)
							{
								
								$out .= "<li><a onclick=\"$('#question_" . $question->get_ID() . "_chosen').html('" . chr(65+ $answer_count) . "'); selected_answer(" . $question->get_ID() . ", " . $answer->get_ID() . ");\" id=\"answer_" . $question->get_ID() . "_" . $answer->get_ID() . "\">" . htmlentities(stripslashes($answer->get_Text())) . "</a></li>";
								
								$QandA_ID_array[$question->get_ID()][$answer->get_ID()] = $answer->is_correct();
								
								if ($answer->is_correct())
								{
									$correct_string .= "<span style=\"display:none;\" id=\"question_" . $question->get_ID() . "_correct_answer\" class=\"correct_answer\"><br />The correct answer is <strong>" . chr(65+ $answer_count) . "</strong></span>";
								}
								
								$answer_count++;
							}
					$out .= "
							</ol>
							
						</td>
						<td style=\"text-align:center\">
							<span id=\"question_" . $question->get_ID() . "_chosen\" style=\"color:black; font-size: 50px;\"></span> 
						</td>
						<td style=\"text-align:center\">
						
							<span id=\"question_" . $question->get_ID() . "_wrongicon\" style=\"color:red; font-size: 50px; display:none;\">&#10006;</span> 
							<span id=\"question_" . $question->get_ID() . "_correcticon\"style=\"color:green; font-size: 50px; display:none;\">&#10004;</span> 
							$correct_string
						
						</td>
					</tr>";
					
				}
		
		$out .= "
			</tbody>
		</table>
		";
		$out .= '
		<table style="width: 100%; margin: 0 0 1em;">
			<tr>
				<td style="width: 25%;"><strong>Pass mark:</strong> ' . $this->get_pass_mark() . ' / ' . $this->number_of_questions . '</td>
				<td style="width: 25%; text-align: center;" id="text_finalscore2"></td>
				<td style="text-align: left;" id="text_passorfail2"></td>
			</tr>
		</table>';
		
		
		$out .= "

		<p id=\"mark_test_button\">
			<a class=\"button\" onclick=\"mark_test();\">I've finished! Mark my test, please.</a>
			<br /><br />
			You can only have your test marked once, so be sure to double-check all of your answers! Your responses will be saved when you have your test marked.
		</p>
		<p>
			Link to this test (<a onclick=\"shorten_link();\">shorten using bit.ly</a>): <input id=\"link_to_test\" name=\"link_to_test\" type=\"text\" value=\"" . $this->return_test_URL() . "\"> <span id=\"loading_bitly\" style=\"display:none\">Loading...</span>
		</p>
		";

		
		// generate the data array to use for checking & formatting etc.
		foreach($QandA_ID_array as $Question_ID => $A_ID_array)
		{
			$QandA_ID_array_string .= "QandA_ID_array[$Question_ID] = new Array(" . count($A_ID_array) . "); ";
			
			foreach($A_ID_array as $Answer_ID => $is_correct)
			{
				if ($is_correct)
				{
					$QandA_ID_array_string .= "QandA_ID_array[$Question_ID][$Answer_ID] = true; ";
				}
				else
				{
					$QandA_ID_array_string .= "QandA_ID_array[$Question_ID][$Answer_ID] = false; ";
				}
			}
		}

		$out .="
		<script type=\"text/javascript\">
		
		";
		$out .= '
		var has_been_shortened = false;
		
		function shorten_link()
		{
			
			if (!has_been_shortened)
			{
			    $("#loading_bitly").fadeIn();
			    
			    
				$.getJSON(
		        "http://api.bitly.com/v3/shorten?callback=?", 
		        { 
		            "format": "json",
		            "apiKey": "R_fdb4c15ca55edd5f58b4a83793197706",
		            "login": "wardrox",
		            "longUrl": "' . ($this->return_test_URL()) . '"
		        },
		        function(response)
		        {
		        	$("#link_to_test").val(response.data.url);
			    	$("#loading_bitly").fadeOut();
		        	
		        }
			    );
		    }
		    has_been_shortened = true;

		}
		';
		$out .= "
		$(\"#link_to_test\").focus(function() {
		    var text_this = $(this);
		    text_this.select();
		
		    // Work around Chrome's little problem
		    text_this.mouseup(function() {
		        // Prevent further mouseup intervention
		        text_this.unbind(\"mouseup\");
		        return false;
		    });
		});
		
		
		var Test_Answers = new Array(" . count($QandA_ID_array) . ");
		
		var QandA_ID_array = new Array(" . count($QandA_ID_array) . ");
		
		" . $QandA_ID_array_string . "
		
		var data_q_array = new Array();
		var data_a_array = new Array();
		var data_array = new Array();
		
		function selected_answer(question_ID, answer_ID)
		{
		
			data_array[question_ID] = answer_ID;
			
			if (!has_marked_test)
			{
				Test_Answers[question_ID] = answer_ID;
				
				for(var i in QandA_ID_array[question_ID])
				{
					if (i == answer_ID)
					{
						$('#answer_' + question_ID + '_' + i).css('font-weight','bold');
					}
					else
					{
						$('#answer_' + question_ID + '_' + i).css('font-weight','normal');
					}
				}
			}
		}


		var correct_count;
		var has_marked_test = false;
		
		
		function mark_test()
		{
			correct_count = 0;
			
			for(var question_ID in QandA_ID_array)
			{
			
				for(var answer_ID in QandA_ID_array[question_ID])
				{
					$('#answer_' + question_ID + '_' + answer_ID).css('color','black');
					
				}
				
				
				
				if (Test_Answers[question_ID])
				{
					if (QandA_ID_array[question_ID][Test_Answers[question_ID]])
					{
						$('#question_' + question_ID + '_correcticon').show();
						$('#question_' + question_ID + '_wrongicon').hide();
						$('#question_' + question_ID + '_correct_answer').hide();
						correct_count = correct_count + 1;
						// highlight the correct answer, which was the chosen answer
						$('#answer_' + question_ID + '_' + Test_Answers[question_ID]).css('color','green');
					}
					else
					{
						$('#question_' + question_ID + '_correcticon').hide();
						$('#question_' + question_ID + '_wrongicon').show();
						$('#question_' + question_ID + '_correct_answer').show();
						
						// highlight the chosen, wrong answer, and the correct answer
						$('#answer_' + question_ID + '_' + Test_Answers[question_ID]).css('color','red');
						for(var answer_ID in QandA_ID_array[question_ID])
						{
							if (QandA_ID_array[question_ID][answer_ID])
							{
								$('#answer_' + question_ID + '_' + answer_ID).css('color','green');
							}
						}
					}
				}
			}	
				
			$('#text_finalscore').html('<strong>Your score:</strong> ' + correct_count);
			$('#text_finalscore2').html('<strong>Your score:</strong> ' + correct_count);
			if (correct_count >= " . $this->get_pass_mark() . ") 
			{
				$('#text_passorfail').html(\"PASS\");
				$('#text_passorfail2').html(\"PASS\");
			}
			else
			{
				$('#text_passorfail').html(\"FAIL :(\");
				$('#text_passorfail2').html(\"FAIL :(\");
			}
			
			$('#mark_test_button').fadeOut();
			has_marked_test = true;
			
			// ajax save the responses
			
			
			for(var question_ID in data_array)
			{
				data_q_array[data_q_array.length] = question_ID;
				data_a_array[data_a_array.length] = data_array[question_ID];
			}
			
			// parse the data array into something we can send
			data_array[question_ID] = question_ID;
			
			$.post(\"ajax.php\", { 
						call: \"save_responses\", 
						q_array: data_q_array,
						a_array: data_a_array
						},
						function(data) {
							//alert(data);
							//$('#text_passorfail2').html(data);
						}
					);
			
		}
		</script>
		
		";
		
		return $out;
	}
	
	public function return_test_URL()
	{
		/*
		$test_number_of_questions = $_GET['n'];
		$test_difficulty = $_GET['d'];
		$test_pass_percentage = $_GET['p'];
		$test_output = $_GET['o'];
		$test_question_IDs = explode(".", $_GET['q']);
		 */
		foreach ($this->Questions as $question)
		{
			$question_ID_array[] = $question->get_ID();
		}
		$question_IDs = implode(".", $question_ID_array);
		
		return get_site_URL() . "test/generate/?n=" . $this->number_of_questions . "&d=" . $this->get_difficulty() . "&p=" . $this->pass_percentage . "&o=" . $this->get_output_format() . "&q=" . $question_IDs . "&s=" . $this->seed;
	}
}