<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */
				
// display the page
include("header.php");
?>

<h3><?php 
if (is_random_question())
{
	echo "Random question:";
}
else
{	
	echo "Question #" . $question->get_ID() . ":";
}
?></h3>

<p>
	<?php echo htmlentities(stripslashes($question->get_Text())); ?>
</p>

<ol type="A">
	<?php 

	foreach ($answers as $answer)
	{
		$quick_answer[] = $answer->get_ID();
		
		echo "<li>";
		echo "<a  class=\"mobilebutton\"  onclick=\"select_answer(" . $answer->get_ID() . ");\">" . htmlentities(stripslashes($answer->get_Text())) . "</a>";
		if ($answer->is_correct())
		{
			$section_string = "";
			
			if ($question->get_WFTDA_Link())
			{
				$section_string .= "See rule " . htmlentities(stripslashes($question->get_Section()));
			
				$section_string .= " (<a target=\"_blank\" href=\"" . $question->get_WFTDA_Link() . "\" title=\"the section of the rules as found on wftda.com\" >view on WFTDA.com</a>)";
			}
			
			
			echo " <span style=\"display:none;\" class=\"correct_answer_win\"><strong>You Win!</strong> " . $section_string . "</span><span style=\"display:none;\" class=\"correct_answer\"><strong> The correct answer.</strong> " . $section_string . "</span>";
		}
		else
		{
			echo " <span style=\"display:none;\" class=\"wrong_answer\" id=\"wrong_answer" . $answer->get_ID() . "\"><strong>Wrong!</strong></span>";
		}
		echo "</li>";
	}

	?>
</ol>

<?php if ($question->get_Notes()) {?>
	<p  style="display:none;" class="question_notes">Note: <?php echo htmlentities(stripslashes($question->get_Notes())); ?></p>
<?php } ?>

<p>
	<a class="button mobilebutton" href="<?php echo get_site_URL(); ?>">New Question</a>
</p>

<?php if ($question->get_Source()) {?>
	<p class="small_p" >Source: <?php echo htmlentities(stripslashes($question->get_Source())); ?></p>
<?php } ?>

<!--<?php if ($question->get_Author()) {?>
	<p class="small_p" >Author: <?php echo htmlentities(stripslashes($question->get_Author())); ?></p>
<?php } ?>-->




<script type="text/javascript">
	var answered = false;
	
	function select_answer(selected)
	{
		if (!answered)
		{
			// make sure we only answer once
			answered = true;

			// show what was right and what was wrong
			if (selected == <?php echo $correct_answer->get_ID()?>)
			{
				// correct!
				$(".correct_answer_win").show();
			}
			else
			{
				// wrong!
				$(".correct_answer").show();
				$("#wrong_answer" + selected).show();
			}

			<?php if ($question->get_Notes()) {?>
			// show the notes
			$(".question_notes").show();
			<?php } ?>

			// ajax save the response for stats tracking
			$.post("ajax.php", { 
				call: "save_response", 
				question_ID: "<?php echo $question->get_ID(); ?>",
				response_ID: selected,
				return_remebered_questions_string: true},
				function(data) {
					$("#remebered_string_p").show();
					$("#remebered_string").hide();
					$("#remebered_string").html(data);
					$("#remebered_string").fadeIn();

				}
			);

			

			
		}
	}
	
	var allow_keypress = true;
	$(document).keypress(function(e) {
		if (allow_keypress)
		{
		    if((e.which == 78) || (e.which == 110))
			{
		    	window.location.reload();
		    }
		    <?php 
		    for ($i = 0; $i < count($answers); $i++)
		    {
			    ?>
			    if((e.which == <?php echo $i + 49 ?>) || (e.which == <?php echo $i + 65 ?>) || (e.which == <?php echo $i + 97 ?>))
				{
			    	select_answer(<?php echo $quick_answer[$i]; ?>);
			    }
			    <?php 
			}?>
		}
	});
	
</script>

<div class="report_form" id="hidden_report_form">
	
	<h3>Report this question:</h3>
	<p>You should report a question if you think it's incorrect or if it's poorly written (including spelling mistakes or bad grammar). If you think the question is wrong be sure to double check the wording of the question <i>and</i> the specific rule it references, which in this case is <strong><?php if ($question) { echo htmlentities(stripslashes($question->get_Section())); } ?></strong>. Until the great robot uprising, we're only human so mistakes happen. Thanks for helping!</p>
	<p>In the text box below please let me know what it is which made you report this question.</p>
	
	<form name="formreport" method="post" action="<?php echo get_site_URL(); ?>report">	
	<p>
		<input type="hidden" id="report_question_ID" name="report_question_ID" value="<?php if ($question) echo $question->get_ID(); ?>" />
		<textarea name="report_text"  id="report_text" rows="10"><?php 
		if ($_POST['report_text']) 
		{
			echo stripslashes(htmlentities($_POST['report_text']));
		}
		else
		{
			echo "I'm reporting question #";
			if ($question) 
				echo $question->get_ID();
			echo " because ... ";
		}
		?></textarea>
	</p>
	<p>
		To prevent spam reports, please complete the following sentence:<br /> "Roller <input id="report_extra" name="report_extra" type="text" /> is an awesome sport."
	</p>

	<p>
		<a class="button" onClick="document.formreport.submit()">Submit Report</a> <a class="button" onClick="$('#hidden_report_form').slideUp()">Cancel</a> 
	</p>
	</form>
	
	<p>A small message from Sausage Roller (the guy who made the site): Thank you. No seriously, <i>thank you</i>. The reports I've gotten from people have been so useful, and really helped me improve, clarify and fix the questions. I'm sorry there's no easy way for me to show my gratitude, but the response from this report feature has reminded me, once again, why I love the global derby community.</p>
</div>
<?php 
include("footer.php");
?>