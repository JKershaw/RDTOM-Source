<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */
				

/*
 * /test = set parameters
 * /test/generate = generate test
 * /test/[number] = load saved test
 */


// display the page
include("header.php");

if (!$url_array[1])
{
	// show the parameter selection form
?>

<h3>Create your test:</h3>
<form id="submittestparameters" name="submittestparameters" method="post" action="<? echo get_site_URL()?>test/generate">

<p><strong>Difficulty</strong></p>
<p>
	<input type="radio" name="test_difficulty" value="beginner"> Beginner</checkbox><br />
	<input type="radio" name="test_difficulty" value="intermediate" checked> Intermediate</checkbox><br />
	<input type="radio" name="test_difficulty" value="expert"> Expert</checkbox>
</p>

<p><strong>Number of questions</strong></p>

<p>
	<input type="text" size="5" id="test_number_of_questions" name="test_number_of_questions" value="45" />
	
	<script type="text/javascript">

	$(document).ready(function(){
	    var intervalID = setInterval(function(){
		    var percentage;
		    var pass_mark;
		    var question_count;

		    question_count = parseInt($('#test_number_of_questions').val());

		    if (question_count < 1)
		    {
		    	question_count = 1;
		    }
		    
		    percentage = parseInt($('#test_pass_percentage').val());
		    if (percentage < 1)
		    {
		    	percentage = 1;
		    }
		    if (percentage > 100)
		    {
		    	percentage = 100;
		    }

		    pass_mark = Math.round(question_count * (percentage / 100));

	    	if (!isNaN(pass_mark))
	    	{
    	    	$('#test_number_of_questions_span').html(pass_mark + " / " + question_count);
	    	}
	    }, 100); // 100 ms check
	});
	
	</script>
</p>

<p><strong>Pass percentage</strong></p>

<p>
	<input type="text" size="5" id="test_pass_percentage"  name="test_pass_percentage" value="80" />&#37; (<span id="test_number_of_questions_span">36 / 45</span>)
</p>

<p>
	<a class="button mobilebutton" onclick="document.submittestparameters.submit();" >Generate Test</a>
</p>
</form>
<p>
</p>
<?php 
}
elseif ($url_array[1] == "generate")
{
	// generate the test
	
	// clean input
	$test_number_of_questions = $_POST['test_number_of_questions'];
	settype($test_number_of_questions, "integer");
	$test_pass_percentage = $_POST['test_pass_percentage'];
	settype($test_pass_percentage, "integer");
	
	
	if ($_POST['test_difficulty'] == "beginner")
	{
		$lower_limit = 80;
		$upper_limit = 100;
	}
	elseif ($_POST['test_difficulty'] == "intermediate")
	{
		$lower_limit = 40;
		$upper_limit = 90;
	}
	elseif ($_POST['test_difficulty'] == "expert")
	{
		$lower_limit = 0;
		$upper_limit = 60;
	}
	
	// get all the applicable questions
	$all_questions = get_questions_difficulty_limit($lower_limit, $upper_limit);
	
	if ($test_number_of_questions > count($all_questions))
	{
		$test_number_of_questions = count($all_questions);
	}
	
	// randomly get a subsection of the array
	
	$random_questions_array_keys = array_rand($all_questions, $test_number_of_questions);
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
	
	
	
	$test_pass_mark = round($test_number_of_questions * ($test_pass_percentage / 100));
	
	// display the test
	?>
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
			<td><strong>Pass mark:</strong> <?php echo $test_pass_mark; ?> / <?php echo $test_number_of_questions; ?></td>
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
			<?php 
			$i = 0;
			foreach ($test_questions as $question)
			{
				$answers = $question->get_Answers();
				$i++;
				
				echo "
				<tr>
					<td style=\"text-align:center; padding-top: 5px;\"><strong>" . $i . "</strong></td>
					<td style=\"padding: 5px;\">
						" . htmlentities(stripslashes($question->get_Text()));
						
				//echo "<span style=\"color: " . get_colour_from_percentage($question->get_SuccessRate()) . "\">" . $question->get_SuccessRate() . "%</span> (" . number_format($question->get_ResponseCount()) . " responses)";
				echo "
						<ol type=\"A\" style=\"list-style-type: upper-alpha;\">";
						$answer_count = 0;
						foreach ($answers as $answer)
						{
							
							if ($answer->is_correct())
							{
								$answer_array[$i] = chr(65+ $answer_count);
							}
							$answer_count++;
							
							$quick_answer[] = $answer->get_ID();
							
							echo "<li>" . htmlentities(stripslashes($answer->get_Text())) . "</li>";
						}
				echo "
						</ol>
						
					</td>
					<td></td>
					<td></td>
				</tr>";
				
			}
			?>
		</tbody>
	</table>
	
	<p id="p_answers_link"><a onclick="$('#p_answers_link').hide(); $('#p_answers').show();">View the answers</a></p>
	<p id="p_answers" style="display:none;">Answers: <?php 
	foreach ($answer_array as $Question_Number => $Answer)
	{
		$answers_string[] = $Question_Number . " = " . $Answer;
	}
	echo implode(", ", $answers_string);
	?>
	<br /><a onclick="$('#p_answers_link').show(); $('#p_answers').hide();">Hide the answers</a></p>
	<?php 
}
include("footer.php");
?>