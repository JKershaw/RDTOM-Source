<?php 

// shuffle the poss questions
shuffle($poll_questions);

// show the page
set_page_subtitle("Turn left and tell me what you think.");
include("header.php"); 
?>
<div style="margin: 30px auto;
border: 1px solid gray;
max-width: 600px;
padding: 30px;"
>
<div id="poll_body_content">
<p>Which new features would you most like to see added to the Roller Derby Test O'Matic? You can select up to 5 answers and/or add your own idea.</p>

<p>
<?php 

foreach($poll_questions as $poll_question_id => $poll_question_text)
{
	echo "<a style=\"font-size: 14px; line-height: 30px;\" id=\"poll_question" . $poll_question_id . "\" onclick=\"selected_answer(" . $poll_question_id . ")\">" . $poll_question_text . "</a></br>";
}
?>
<span style="font-size: 14px; line-height: 30px;">Other: <input type="text" id="text_other"  name="text_other"  style="width: 80%;"></input></span>
</p>

<p><a onclick="save_answers()">Save Answers</a> <br /><span style="font-size:10px">If you have already voted once, your vote won't count. <a onclick="get_results()">View Results</a>.</span></p>


<script type="text/javascript">
var selected_count = 0;
var answer_choice = new Array();

<?php 
foreach($poll_questions as $poll_question_id => $poll_question_text)
{
	echo "
	answer_choice[" . $poll_question_id . "] = false;";
}
?>

function selected_answer(question_id)
{
	
	if (answer_choice[question_id] == 1)
	{
		answer_choice[question_id] = 0;
		selected_count = selected_count - 1;
		$("#poll_question" + question_id).css("font-weight", "normal");
	}
	else
	{
		if (selected_count < 5)
		{
			answer_choice[question_id] = 1;
			selected_count = selected_count + 1;
			$("#poll_question" + question_id).css("font-weight", "bold");
		}
		else
		{
			alert("You can only select 5 answers");
		}
	}
	
}

function save_answers()
{
	var tmp_string = $("#text_other").val();
	$("#poll_body_content").html("<p>Saving...</p>");

	
	$.post("ajax.php", { 
		call: "save_poll_results", 
		<?php 
		foreach($poll_questions as $poll_question_id => $poll_question_text)
		{
			echo "
			answer" . $poll_question_id . ": answer_choice[" . $poll_question_id . "],";
		}
		?>

		answer_other: tmp_string},
		
		function(data) {

			$("#poll_body_content").html(data);
			
		}
	);
}

function get_results()
{
	$("#poll_body_content").html("<p>Loading...</p>");

	$.post("ajax.php", { 
		call: "get_poll_results" 
		},
		
		function(data) {

			$("#poll_body_content").html(data);
			
		}
	);	
}

</script>

</div></div>

<p style="font-size:14px">
Hello! John / Sausage Roller (guy who made the site) here, just wondering what you would like to see added to the site next?
</p>

<p style="font-size:14px">
If you have an awesome idea you're more than welcome to <a href="mailto:contact@rollerderbytestomatic.com?Subject=Roller%20Derby%20Test%20O'Matic">email</a> me or post a comment on <a href="http://www.facebook.com/RollerDerbyTestOMatic">the Roller Derby Test O'Matic Facebook page</a>.
			</p>		
<?php include("footer.php"); ?>