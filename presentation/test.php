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

/*
 * Seelct if you want an interactive test or not
 * Select questions
 * Have "show answers" or "test complete" button
 * Mark questions as right or wrong, adjust question formatting to fit
 * Fill in all values at the top of the page.
 */

// display the page
include("header.php");

if (!$url_array[1])
{
	// show the parameter selection form
?>

<h3>Create your test:</h3>
<p><strong>Until the questions for the new rules have been used for a few weeks, the tests this page generates should not be relied on.</strong></p>
<p>Note: Tests answers are not currently saved and don't count towards any of your statistics. For feedback, feature requests, questions and bug reports; please visit the <a href="http://www.facebook.com/RollerDerbyTestOMatic">RDTOM Facebook page</a>. I take no responsibility for anything you choose to do with these tests.</p>
<form id="submittestparameters" name="submittestparameters" method="post" action="<? echo get_site_URL()?>test/generate">

<div id="test_default" style="display:none;">
	<p>
	Difficulty: Intermediate<br />
	Number of questions: 45<br />
	Pass percentage: 80% (36 / 45)<br />
	Output: Online, interactive test<br />
	</p>
	<p>
		<a onclick="$('#test_default').hide();$('#test_customisation').fadeIn();">Customise</a>
	</p>
</div>
<div id="test_customisation">
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
	
			    if ((question_count < 1) || (question_count == ""))
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
	
	<p><strong>Pass percentage:</strong></p>
	
	<p>
		<input type="text" size="5" id="test_pass_percentage"  name="test_pass_percentage" value="80" />&#37; (<span id="test_number_of_questions_span">36 / 45</span>)
	</p>
	
	<p><strong>Type of test to generate:</strong></p>
	
	<p>
		<input type="radio" name="test_output" value="interactiveHTML" checked> Interactive (can be filled in online) </checkbox><br />
		<input type="radio" name="test_output" value="HTML"> Non-interactive (can be printed, answers are at the bottom of the page)</checkbox><br />
		<!-- 
		<input DISABLED type="radio" name="test_output" value="txt"> Basic Text .txt file (experimental)</checkbox><br />
		<input DISABLED type="radio" name="test_output" value="doc"> Word / OpenOffice .doc file (experimental)</checkbox><br />
		<input DISABLED type="radio" name="test_output" value="pdf"> Pdf file (experimental)</checkbox>
		 -->
	</p>
</div>
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
	$test = new test();
	
	if ($_POST)
	{
		$test_number_of_questions = $_POST['test_number_of_questions'];
		$test_difficulty = $_POST['test_difficulty'];
		$test_pass_percentage = $_POST['test_pass_percentage'];
		$test_output = $_POST['test_output'];

		$test->populate($test_number_of_questions, $test_difficulty);
		$test->set_pass_percentage($test_pass_percentage);
	}
	elseif ($_GET['n'])
	{
		$test_number_of_questions = $_GET['n'];
		$test_difficulty = $_GET['d'];
		$test_pass_percentage = $_GET['p'];
		$test_output = $_GET['o'];
		$test_seed = $_GET['s'];
		
		$test_question_IDs = explode(".", $_GET['q']);
	
		$test->populate($test_number_of_questions, $test_difficulty, $test_question_IDs);
		$test->set_pass_percentage($test_pass_percentage);
		if ($test_seed)
		{
			$test->set_seed($test_seed);
		}
	}
	else
	{
		$test->populate();
	}
	

	echo $test->get_formatted_output($test_output);

	
	// display the test
	
}
include("footer.php");
?>