<?php 
// The page for RDTOM competitions

// show the page
set_page_subtitle("Turn left and get a happy cat.");
include("header.php"); 
?>

<script type="text/javascript">

var sadcats=["S3m7nIm.jpg","2twKY.gif","TrUEz.jpg", "Vtp7H.jpg", "h6RhM.jpg", "7dXP8.gif", "k0cjksd.jpg"];
var happycats=["nbJpi.jpg","7BNsToQ.jpg","4FIjL.jpg", "eLsOC.jpg", "a3OQ9.jpg", "VS7Pt3cb.jpg", "8UKs4.jpg", "y0w98.jpg"];

var have_answered = false;

// load a new question
function loadquestion()
{
	have_answered = false;
	
	// delete the image
	$("#cat_image").html("");
	
	// get rid of the old answers
	$("#answers").html("");
	$("#catimage").fadeOut();
	$("#catstate").fadeOut();

	$("#question").html("Loading ... ");
	// get the new question
	$.getJSON('http://rollerderbytestomatic.com/api/0.1/json/question/', function(data) {

			// show the new question text
			
			$("#question").html(unescape((data.results.question.text).replace(/\\(.)/mg, "$1")));


			// load each new answer
			$.each(data.results.question.answers.answer, function(key, answer) 
			{
				if (answer.correct == "true")
				{
					$("#answers").append("<a id=\"" + answer.id + "\" onclick=\"answer_question('correct', " + answer.id + ")\">" + (answer.text).replace(/\\(.)/mg, "$1") + "</a><br /><br />");
				}
				else
				{
					$("#answers").append("<a id=\"" + answer.id + "\" onclick=\"answer_question('wrong', " + answer.id + ")\">" + (answer.text).replace(/\\(.)/mg, "$1") + "</a><br /><br />");
				}
					
			 });

	});
			
}

function answer_question(type, answerID)
{
	if (have_answered)
	{
		return false;
	}
	have_answered = true;
	
	$("#" + answerID).css("font-weight","Bold");
	
	$("#catstate").fadeIn();
	$("#catimage").fadeIn();

	if (type == "correct")
	{
		$("#catstate").html("Yey! You were right! Happy cat!");
		var cat = happycats[Math.floor(Math.random()*happycats.length)];
	}
	else
	{
		$("#catstate").html("Oh no! You got the answer wrong. Sad cat.");
		var cat = sadcats[Math.floor(Math.random()*sadcats.length)];
	}
	
	$("#catimage").html("<img style='width:300px' src='http://imgur.com/" + cat + "'/>");
}

</script>
	
<div style="width:400px; float:left; min-height:600px;">
	<p id="catstate">Answer questions and get a happy cat if you're right. A sad cat if you're wrong.</p>
	<p style="height:200px;" id="catimage"></p>
</div>

<div style="width:400px; float:left; margin-left:10px; min-height:600px;">
	<p id="question"></p>
	<p id="answers"></p>
	
	<p id="reload"><a class="button mobilebutton" onclick="loadquestion()">Load new question</a></p>
</div>

<div style="clear:both;"></div>
	
<p>No data is stored on you. This does not count towards your stats. It's been built as a proof of concept for the Roller Derby Test O'Matic API. For more information get in touch. Please send pictures of sad and happy cats to me via <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Cats">email</a>, <a href="http://www.facebook.com/RollerDerbyTestOMatic">Facebook</a>, <a href="http://twitter.com/#!/wardrox">Twitter</a> or <a href="http://wardrox.tumblr.com/ask">Tumblr</a>.</p>
<?php 
include("footer.php"); ?>