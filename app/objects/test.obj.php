<?php
class test
{
	private $ID;
	private $Questions;
	
	// $Answers[QuestionID] = {A1, A2, A3, A4}
	// array of Answers, in the order of display
	private $Answers;
	
	// an array of just question ID and answer IDs, used so we can make a test object without needing to load all the questions
	private $QandA_ID_Array;
	
	
	private $pass_percentage = 80;
	private $output_format = "interactiveHTML";
	
	private $number_of_questions;
	private $difficulty;
	
	private $seed;
	
	private $title;
	private $description;
	private $status;
	private $link_hash;
	
	private $Author_ID;
	private $Timestamp_created;
	private $Timestamp_edited;
	private $count_view;
	private $count_completed;
	private $average_score;
	
	function __construct()
	{
		// generate a seed for this test
		list($usec, $sec) = explode(' ', microtime());
  		$this->seed = (float) $sec + ((float) $usec * 1000000) + rand(0, 1000000);
	}
	
	private function load_questions_and_answers()
	{
		// if we've already populated the test, don't worry about it.
		if ($this->Questions)
		{
			return;
		}
		
		// use $QandA_ID_Array to populate $Questions and $Answers
		if (!$this->QandA_ID_Array)
		{
			throw new exception ("No QandA_ID_Array");
		}
		
		foreach ($this->QandA_ID_Array as $qanda_IDs)
		{
			$answers = Array();
			$question = get_question_from_ID($qanda_IDs[0]);
			
			if ($qanda_IDs[1] && is_array($qanda_IDs[1]))
			{
				foreach ($qanda_IDs[1] as $answer_ID)
				{
					try {
						
					$answers[] = get_answer_from_ID($answer_ID);
					} catch (Exception $e) {
					}
				}
			}
			
			$this->add_question($question, $answers);
		}
	}
	
	public function add_question($question, $answers)
	{
		// add a question to the test
		// $answers is an array of Answers in the order we'd like them shown in.
		
		// save the question
		$this->Questions[$question->get_ID()] = $question;
		
		// save the answers
		$this->Answers[$question->get_ID()] = $answers;
		
		// incriment the question count 
		$this->number_of_questions++;
		
	}

	public function set_ID($req_ID)
	{
		$this->ID = $req_ID;
	}

	public function set_Timestamp_created($req)
	{
		$this->Timestamp_created = $req;
	}
	
	public function set_Timestamp_edited($req)
	{
		$this->Timestamp_edited = $req;
	}
	public function set_Author_ID($req_ID)
	{
		$this->Author_ID = $req_ID;
	}

	public function set_Title($req_Title)
	{
		$this->title = $req_Title;
	}

	public function set_Description($req_description)
	{
		$this->description = $req_description;
	}

	public function set_Status($req_status)
	{
		$this->status = $req_status;
	}

	public function set_link_hash($req_link_hash)
	{
		$this->link_hash = $req_link_hash;
	}
	
	public function set_Views_Count($req)
	{
		$this->count_view = $req;
	}
	
	public function set_Complete_Count($req)
	{
		$this->count_completed = $req;
	}
	
	public function set_Average_Rating($req)
	{
		$this->average_score = $req;
	}
	
	public function set_seed($req_seed)
	{
		$this->seed = $req_seed;
	}
	
	public function set_pass_percentage($req_pass_percentage)
	{
		settype($req_pass_percentage, "integer");
		$this->pass_percentage = $req_pass_percentage;
	}
	
	public function set_output_format($req_output_format)
	{
		$this->output_format = $req_output_format;
	}
	
	public function get_seed()
	{
		return $this->seed;
	}

	public function set_QandA_ID_Array($req_array)
	{
		return $this->QandA_ID_Array = $req_array;
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
	}
	
	public function get_ID()
	{
        if ($this->ID)
		    return $this->ID;
        else
            return -1;
	}
	
	public function get_Timestamp_created()
	{
		return $this->Timestamp_created;
	}
	
	public function get_Timestamp_edited()
	{
		return $this->Timestamp_edited;
	}

	public function get_Author_ID()
	{
		return $this->Author_ID;
	}

	public function get_Title()
	{
		return $this->title;
	}

	public function get_Description()
	{
		return $this->description;
	}

	public function get_Status()
	{
		// only three values it can be
		if (($this->status == "private") || ($this->status == "public"))
			return $this->status;
		else
			return "draft";
	}

	public function get_link_hash()
	{
		return $this->link_hash;
	}
	
	
	public function get_Questions()
	{
		$this->load_questions_and_answers();
		return $this->Questions;
	}
	
	public function get_Answers($question_ID)
	{
		$this->load_questions_and_answers();
		return $this->Answers[$question_ID];
	}
	
	
	public function get_Question_Count()
	{
		$this->load_questions_and_answers();
		return (integer)$this->number_of_questions;
	}
	
	public function get_Views_Count()
	{
		return (integer)$this->count_view;
	}
	
	public function get_Complete_Count()
	{
		return (integer)$this->count_completed;
	}
	
	public function get_Average_Rating()
	{
		if ($this->average_score != null)
		{
			return $this->average_score;
		}
		else
		{
			return -1;
		}
	}
	
	public function get_pass_mark()
	{
		return round($this->get_Question_Count() * ($this->pass_percentage / 100));
	}
	
	public function get_question_and_answers_IDs($return_string = true)
	{
		$this->load_questions_and_answers();
		if ($this->Questions)
		{
			foreach ($this->Questions as $question)
			{
				$answers = $this->Answers[$question->get_ID()];
				$answer_IDs = Array();
				foreach ($answers as $answer)
				{
					$answer_IDs[] = $answer->get_ID();
				}
				
				$result[] = Array(0 => $question->get_ID(), 1 => $answer_IDs);
			}
		}
		
		return $result;
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
		// incriment view count on test
		
		$this->load_questions_and_answers();
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
        $out = "";

		if ($this->title)
		{
			$out .= "<h3>" . htmlentities(stripslashes($this->title)) . "</h3>";
		}
		if ($this->description){
			$out .= "<p>" . nl2br(htmlentities(stripslashes($this->description))) . "</p>";
		}
		$out .= '
		
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
					$answers = $this->Answers[$question->get_ID()];
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
        $out = "";

		if ($this->title)
		{
			$out .= "<h3>" . htmlentities(stripslashes($this->title)) . "</h3>";
		}
		if ($this->description){
			$out .= "<p>" . nl2br(htmlentities(stripslashes($this->description))) . "</p>";
		}
		$out .= '

		
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
					$answers = $this->Answers[$question->get_ID()];
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
									$correct_string = "<span style=\"display:none;\" id=\"question_" . $question->get_ID() . "_correct_answer\" class=\"correct_answer\"><br />The correct answer is <strong>" . chr(65+ $answer_count) . "</strong></span>";
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
		</table>

		<p id="mark_test_button">
			<a class="button" onClick="mark_test();">I\'ve finished! Mark my test, please.</a>
			<br /><br />
			You can only have your test marked once, so be sure to double-check all of your answers! Your responses will be saved when you have your test marked.
		</p>
		';
		
		if ($this->ID > 0)
		{
			if (is_logged_in())
			{
				global $user;
				$rating = get_test_rating($this->ID, $user->get_ID());
			}
			else
			{
				$rating = get_test_rating($this->ID, false, get_ip());
			}
			
			if ($rating == null)
			{
				$rating = 0;
			}
			$out .= '
			<div style="margin: 0 0 1em;">
				<p style="display: inline;">
				Rate this test: 
				</p>
				<ul style="list-style-type: none; display: inline; padding: 0;" id="star_ratings" class="star_ratings">
					<li style="color: grey; font-size:20px; cursor:pointer; display: inline;" class="star_1">&#9733;</li>
					<li style="color: grey; font-size:20px; cursor:pointer; display: inline;" class="star_2">&#9733;</li>
					<li style="color: grey; font-size:20px; cursor:pointer; display: inline;" class="star_3">&#9733;</li>
					<li style="color: grey; font-size:20px; cursor:pointer; display: inline;" class="star_4">&#9733;</li>
					<li style="color: grey; font-size:20px; cursor:pointer; display: inline;" class="star_5">&#9733;</li>
				</ul>
			</div>
			
			<script type="text/javascript">
			// star rating script
			
			var star_rating = ' . $rating . ';
			
			$(".star_ratings li").hover(
				function() {
					mouseover_star($(".star_ratings li").index(this) + 1);
				},
				function() {
					mouseover_star(star_rating);
				}
			);
			
			$(".star_ratings li").click(
				function() {
					star_rating = $(".star_ratings li").index(this) + 1;
					mouseover_star(star_rating);
					$.post("ajax.php", { 
											call: "save_test_rating", 
											test_ID: ' . $this->ID . ',
											rating: star_rating
											},
											function(data) {
												
											}
					);					
				}
			);
	
	
			$(function() {
				mouseover_star(star_rating);
			});
			
			function mouseover_star(star_count)
			{
				for (var i=5;i>0;i--)
				{ 
					if (star_count < i)
					{
						$(".star_" + i).css("color", "grey");
					}
					else
					{
						$(".star_" + i).css("color", "#FF9900");
					}
				}
			}
			</script>
			';
		}
		$out .= "
		<p>
			Link to this test, answers are randomised every time (<a onclick=\"shorten_link();\">shorten using bit.ly</a>): <input id=\"link_to_test\" name=\"link_to_test\" type=\"text\" value=\"" . $this->get_test_URL() . "\"> <span id=\"loading_bitly\" style=\"display:none\">Loading...</span>
		</p>
		<p>
		    <a href=\"" . $this->get_test_URL(true) . "\">Edit and save this test</a>
		</p>
		";

        $QandA_ID_array_string = "";

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
		$out .='
		
		<script type="text/javascript">
		// code shortening script
		
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
		            "longUrl": "' . ($this->get_test_URL()) . '"
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
		    console.debug('mark_test begun');
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
						a_array: data_a_array";
		if($this->ID)
		{
			$out .= ",
						test_id: " .$this->ID;
		}
		
		$out .=
		"
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
	
	public function get_test_URL($edit = false)
	{
		/*
		$test_number_of_questions = $_GET['n'];
		$test_difficulty = $_GET['d'];
		$test_pass_percentage = $_GET['p'];
		$test_output = $_GET['o'];
		$test_question_IDs = explode(".", $_GET['q']);
		 */
		
		// is this a saved test?
		if ($this->ID)
		{
			if ($this->status == "private")
			{
				return get_site_URL() . "test/" . $this->ID . "/" . $this->link_hash;
			}
			else
			{
				return get_site_URL() . "test/" . $this->ID;
			}
		}
		
		$this->load_questions_and_answers();
		
		foreach ($this->Questions as $question)
		{
			$question_ID_array[] = $question->get_ID();
		}
		$question_IDs = implode(".", $question_ID_array);

        if ($edit)
        {
            $base = "test/builder/new/";
        }
        else
        {
            $base = "test/generate/";
        }
		return get_site_URL() . $base . "?n=" . $this->number_of_questions . "&d=" . $this->get_difficulty() . "&p=" . $this->pass_percentage . "&o=" . $this->get_output_format() . "&q=" . $question_IDs . "&s=" . $this->seed;
	}
}

