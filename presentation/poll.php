<?php 
		
// the questions
$poll_questions = array();

$poll_questions[] = array("id" => 10, "text" => "Other rule sets (e.g. No Minors Beta, USARS, WORD)");
$poll_questions[] = array("id" => 1, "text" => "Questions for officials (refs & NSOs)");
$poll_questions[] = array("id" => 2, "text" => "[Done] Stats showing what sections you're good/bad at");
$poll_questions[] = array("id" => 3, "text" => "Select which questions you will be given (difficulty, rules sections etc.)");
$poll_questions[] = array("id" => 4, "text" => "A free mobile app");
$poll_questions[] = array("id" => 5, "text" => "Auto-generated mock tests");
$poll_questions[] = array("id" => 6, "text" => "Other languages");
$poll_questions[] = array("id" => 7, "text" => "User submitted (and moderated) questions");
$poll_questions[] = array("id" => 8, "text" => "Questions with images");
$poll_questions[] = array("id" => 9, "text" => "Discussion areas to discuss specific questions, rules, and the site in general");

shuffle($poll_questions);
// poll goes here

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

foreach($poll_questions as $poll_question)
{
	echo "<a style=\"font-size: 14px; line-height: 30px;\" id=\"poll_question" . $poll_question["id"] . "\" onclick=\"selected_answer(" . $poll_question["id"] . ")\">" . $poll_question["text"] . "</a></br>";
}
?>
<span style="font-size: 14px; line-height: 30px;">Other: <input type="text" id="text_other"  name="text_other"  style="width: 80%;"></input></span>
</p>

<p><a onclick="save_answers()">Save Answers</a> <br /><span style="font-size:10px">If you have already voted once, your vote won't count. <a onclick="get_results()">View Results</a>.</span></p>


<script type="text/javascript">
var selected_count = 0;
var answer_choice = new Array();

answer_choice[10] = false;
answer_choice[1] = false;
answer_choice[2] = false;
answer_choice[3] = false;
answer_choice[4] = false;
answer_choice[5] = false;
answer_choice[6] = false;
answer_choice[7] = false;
answer_choice[8] = false;
answer_choice[9] = false;

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
		answer10: answer_choice[10], 
		answer1: answer_choice[1], 
		answer2: answer_choice[2], 
		answer3: answer_choice[3], 
		answer4: answer_choice[4], 
		answer5: answer_choice[5], 
		answer6: answer_choice[6], 
		answer7: answer_choice[7], 
		answer8: answer_choice[8], 
		answer9: answer_choice[9],
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
I recently <a href="https://www.google.co.uk/search?q=sad+violin+music">sprained my ankle</a> and will be off skates for the next few weeks (pity me!), so figure this is a great opportunity to get some features built. Yey code! I know what features <i>I'm</i> interested in, but as there's many more thousands of you than there are of me I thought it sensible to gather some opinions on what people want.
</p>
<p style="font-size:14px">
If you have an awesome idea you're more than welcome to <a href="mailto:wardrox@gmail.com?Subject=Roller%20Derby%20Test%20O'Matic">email</a> me or post a comment on <a href="http://www.facebook.com/RollerDerbyTestOMatic">the Roller Derby Test O'Matic Facebook page</a>.
			</p>		
<?php include("footer.php"); ?>