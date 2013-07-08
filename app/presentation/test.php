<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */
				

/*
 * /test 			= set parameters
 * /test/generate 	= generate a random test
 * /test/build 		= make test
 * /test/[number] 	= load saved test
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
	
	<p>Click the button to randomly generate an online rules test with its difficulty balanced to be similar to the <a href="http://wftda.com/resources/wftda-rules-test.pdf" >WFTDA's sample test</a>.</p>
	
	<p>
		<a class="button mobilebutton" onclick="document.submittestparameters.submit();" >Generate Rules Test</a>
	</p>
	
	
	<p id="test_customisation_link"><a onclick="$('#test_customisation_link').hide();$('#test_customisation').show()">Customise the test</a></p>
	
	<div id="test_customisation" style="display:none">
		<form id="submittestparameters" name="submittestparameters" method="get" action="<? echo get_site_URL()?>test/generate/">
	
		<p><strong>Difficulty</strong></p>
		<p>
			<input type="radio" name="d" value="wftda" checked> Balanced - Roughly the same difficulty as the WFTDA sample test<br />
			<input type="radio" name="d" value="mixed"> Mixed - A random selection of questions from all difficulties<br />
		</p>
		<p>
			<input type="radio" name="d" value="beginner"> Beginner - only questions most people get right<br />
			<input type="radio" name="d" value="intermediate"> Intermediate - only questions that are about average<br />
			<input type="radio" name="d" value="expert"> Expert - only hard questions
		</p>
		
		<p><strong>Number of questions</strong></p>
		
		<p>
			<input type="text" size="5" id="n" name="n" value="45" />
			
			<script type="text/javascript">
		
			$(document).ready(function(){
			    var intervalID = setInterval(function(){
				    var percentage;
				    var pass_mark;
				    var question_count;
		
				    question_count = parseInt($('#n').val());
		
				    if ((question_count < 1) || (question_count == ""))
				    {
				    	question_count = 1;
				    }
				    
				    percentage = parseInt($('#p').val());
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
			<input type="text" size="5" id="p"  name="p" value="80" />&#37; (<span id="test_number_of_questions_span">36 / 45</span>)
		</p>
		
		<p><strong>Output format</strong></p>
		
		<p>
			<input type="radio" name="o" value="interactiveHTML" checked> Interactive (can be filled in online) </checkbox><br />
			<input type="radio" name="o" value="HTML"> Non-interactive (can be printed, answers are at the bottom of the page)</checkbox><br />
			<!-- 
			<input DISABLED type="radio" name="test_output" value="txt"> Basic Text .txt file (experimental)</checkbox><br />
			<input DISABLED type="radio" name="test_output" value="doc"> Word / OpenOffice .doc file (experimental)</checkbox><br />
			<input DISABLED type="radio" name="test_output" value="pdf"> Pdf file (experimental)</checkbox>
			 -->
		</p>
		
		</form>
	
	</div>
	<?php 
}
elseif ($url_array[1] == "builder")
{
	if (is_logged_in())
	{
		if ($url_array[2])
		{
			// editor
			if ($url_array[2] != "new")
			{
				$test = get_test_from_ID($url_array[2]);
				// is the user the author?
				if ($user->get_ID() != $test->get_Author_ID())
				{
					throw new exception ("You must be the author of the test in order to edit it");
				}
			}
			?>
			
		<p><a href="<?php echo get_site_URL(); ?>test/builder/">Back to tests overview</a></p>
		<h3>Test Editor</h3>
		<div id="test_builder_wrap" style="width: 900px; border: 1px solid #AAA; overflow: auto;">
			<div id="test_builder_questions" style="width:440px; float:left; border: 1px solid #AAA; overflow: auto; margin:2px; padding:2px;  min-height:175px;">
				
				<p style="text-align: center; margin: 10px;"><a class="button mobilebutton" onclick="save_test();" id="button_save">Save</a></p>
				<p id="last_saved" class="small_p" style="display:none; text-align:center;"></p>
				
				<p><strong>Title</strong><br /><input type="text" id="test_title" name="test_title" style="font-size: 20px; width: 90%;" value="<?php if ($test) { echo htmlentities(stripslashes($test->get_Title())); } ?>"/></p>
				
				<p><strong>Description</strong><br /><textarea id="test_description" style="width:90%" name="test_description" rows="5"><?php if ($test) { echo stripslashes($test->get_Description()); } ?></textarea></p>
				
				<p>
					<input type="radio" name="test_status" value="draft" <?php if (($test && ($test->get_Status() == "draft")) || !$test) { echo 'checked="checked"'; } ?>>Draft - visible only to you<br />
					<input type="radio" name="test_status" value="private" <?php if ($test && ($test->get_Status() == "private")) { echo 'checked="checked"'; } ?>>Private - visible only to people with a link<br />
					<input type="radio" name="test_status" value="public" <?php if ($test && ($test->get_Status() == "public")) { echo 'checked="checked"'; } ?>>Public - visible to everyone<br />
				</p>
				
				<p><strong>Questions</strong> <i>- drag and drop to reorder</i></p>
				
				<ul id="test_questions">
				</ul>
				
			</div>
			<div id="test_builder_finder" style="width:440px; float:left; border: 1px solid #AAA; overflow: auto; margin:2px; padding:2px; min-height:175px;">
				<h3>Find Questions</h3>
				<p style="text-align:center">Search (keyword or section): <br /><input type="text" id="search_string" style="width: 50%;"> <a onclick="question_search();">Search</a></p>
				<p style="text-align:center">- Or - </p>
				<p style="text-align:center">Load all: <a onclick="questions_from_difficulty('beginner');">Beginner</a>, <a onclick="questions_from_difficulty('intermediate');">Intermediate</a>, <a onclick="questions_from_difficulty('expert');">Expert</a></p>
				<ul id="result_questions" style="display:none;">
				</ul>
			</div>
		
		</div>
	  
		<script type="text/javascript">

		// the hidden parameters of the test
		
		// ID, if -1 it means we're unsaved
		var test_id = <?php if ($test) { echo $test->get_ID(); } else { echo -1; }?>;

		// hash that goes at the end of the link for private links
		var link_hash = "<?php if ($test) { echo $test->get_link_hash(); } else { echo generatealphaneumericSalt(100);; }?>";
		
		$("#search_string").keypress(function(event) {
		    if (event.which == 13) {
		    	question_search();
		    }
		});
		
		  $(function() {
		    $( "#test_questions" ).sortable({
		      placeholder: "ui-state-highlight"
		    }).disableSelection();
		  });

		$( "#test_questions").on( "sortupdate", handle_test_question_change);
		  
		function question_search()
		{
			load_questions_given_URI('<?php echo get_site_URL() ?>api/0.2/json/questions/?developer=RDTOM&application=testbuilderpage&search=' + $("#search_string").val());
		}
		  
		function questions_from_difficulty(difficulty)
		{
			switch(difficulty)
			{
				case "beginner":
					load_questions_given_URI('<?php echo get_site_URL() ?>api/0.2/json/questions/?developer=RDTOM&application=testbuilderpage&search=difficulty:beginner');
					break;
				case "intermediate":
					load_questions_given_URI('<?php echo get_site_URL() ?>api/0.2/json/questions/?developer=RDTOM&application=testbuilderpage&search=difficulty:intermediate');
					break;
				case "expert":
					load_questions_given_URI('<?php echo get_site_URL() ?>api/0.2/json/questions/?developer=RDTOM&application=testbuilderpage&search=difficulty:expert');
					break;
			}
		}
		
		function load_questions_given_URI(req_URI)
		{
			$("#result_questions").show().html("Loading ... ");
			// get the new question
			$.getJSON(req_URI, function(data) {
		
				$("#result_questions").html("");
				if (data.resource.questions.question instanceof Array)
				{
					$.each(data.resource.questions.question, function(key, question) 
					{
						append_question(question);
					});
				}
				else
				{
					// 1 or 0 results
					if (typeof data.resource.questions.question  !== 'undefined')
					{
						// 1 result
						append_question(data.resource.questions.question);
					}
				}
			});
		}
		
	
		var loaded_questions_array = [];
		var correct_answer_ids = [];
		var test_question_ids = [];
		
		// add a question to the Find a Question list
		function append_question(question)
		{
			var is_WFTDA6 = false;
			for (var key in question.terms) 
			{
				console.debug(question.id + " - " + JSON.stringify(question.terms));
				
				if (question.terms.hasOwnProperty(key)) 
				{
					if (question.terms[key] instanceof Array)
					{
						console.debug(question.id + " - " + JSON.stringify(question.terms[key]));
						$(question.terms[key]).each(function( index, item ) {
							if (item == "WFTDA6")
							{
								is_WFTDA6 = true;
							}
						});				
					}
					else
					{
						if (question.terms[key] == "WFTDA6")
						{
							is_WFTDA6 = true;
						}
					}
				}
			}
	
			if (!is_WFTDA6)
			{
				console.debug("Question " + question.id + " is not WFTDA 6");
				return;
			}
	
			// save the loaded question, we may need it later
			//loaded_questions_array[question.id] = question;
			loaded_questions_array["" + question.id] = question;
			 
			
			$(question.sections).each(function( index, section ) {
				section_string = section.section;	
				return false;
			});
	
			var answers_string = "";
			var correct_answers_string = "";
			var wrong_answers_string = "";
			
			$.each(question.answers.answer, function(key, answer) 
			{
				
				if (answer.correct == "true")
				{
					correct_answers_string = correct_answers_string + "<br />- " + answer.text;
				}
				else
				{
					wrong_answers_string = wrong_answers_string + "<br />- " + answer.text;
				}
			});
	
			answers_string = "<p><br /><strong>Correct Answer/s:</strong>" + correct_answers_string + "</p><p><strong>Incorrect Answer/s:</strong>" + wrong_answers_string + "</p>";
			
			
			question_html = "<li class=\"ui-state-default\" id=\"question_" + (question.id) + "\">" + 
				section_string + " " + 
				(question.text).replace(/\\(.)/mg, "$1") + 
				" <span id=\"answers_" + (question.id) + "\" style=\"display:none;\">" + answers_string + "</span><br /><a onclick=\"add_to_test('" + (question.id) + "');\">Add to test</a> | <a onclick=\"$('#answers_" + (question.id) + "').toggle();\">Answers</a> </li>";
			
			$("#result_questions").append(question_html);
		}
	
		function add_to_test(question_id)
		{
			question_id = parseInt(question_id);
			
			// add to the array if not already there
			if ($.inArray(question_id, test_question_ids) == -1)
			{
				// get the question
				question = loaded_questions_array["" + question_id];

				
	
				// hide from the search list
				$("#result_questions #question_" + (question.id)).slideUp();
				
				// append the question object 
				append_question_to_test(question, false);
			}
			else
			{
				alert("Question already in the test.");
			}
			
		}

		function append_question_to_test(question, check_chosen_answers)
		{
			
			console.log (question);
			// show it on the test list
			
			$(question.sections).each(function( index, section ) {
				section_string = section.section;	
				return false;
			});

			var answers_string = "";
			var unused_answers_string = "";
			var correct_answers_count = 0;
			var wrong_answers_count = 0;
			
			$.each(question.answers.answer, function(key, answer) 
			{
				
				if (answer.correct == "true")
				{
					//remeber if this answer is correct. We'll need this info later.
					correct_answer_ids["" + answer.id] = true;
					
					if (correct_answers_count >= 1 || (check_chosen_answers && !answer.chosen))
					{
						unused_answers_string = unused_answers_string + "<li id=\"" + answer.id + "\" style=\"background-color:#AFA\"> " + answer.text + "</li>";
					}
					else
					{
						correct_answers_count = correct_answers_count + 1;
						answers_string = answers_string + "<li id=\"" + answer.id + "\" style=\"background-color:#AFA\"> " + answer.text + "</li>";
					}
				}
				else
				{
					//remeber if this answer is correct. We'll need this info later.
					correct_answer_ids["" + answer.id] = false;
					
					if (wrong_answers_count >= 3 || (check_chosen_answers && !answer.chosen))
					{
						unused_answers_string = unused_answers_string + "<li id=\"" + answer.id + "\">" + answer.text + "</li>";
					}
					else
					{
						wrong_answers_count = wrong_answers_count + 1;
						answers_string = answers_string + "<li id=\"" + answer.id + "\">" + answer.text + "</li>";
					}
							
				}
			});

			answers_string = "<p id=\"error_" + question.id + "\"style=\"display:none; font-weight:bold; color:red;\"></p><p><br /><strong>Chosen answers:</strong><ul class=\"test_answers test_chosen_answers test_answers_" + question.id + "\" id=\"answers_chosen_" + (question.id) + "\">" + answers_string + "</ul></p><p><strong>Unused Answers:</strong><ul  class=\"test_answers test_unused_answers test_answers_" + question.id + "\" id=\"answers_unused_" + (question.id) + "\">" + unused_answers_string + "</ul></p>";
			
			
			question_html = "<li style=\"display:none\" class=\"ui-state-default\" id=\"question_" + (question.id) + "\">" + 
				section_string + " " + 
				(question.text).replace(/\\(.)/mg, "$1") + 
				" <span id=\"answers_" + (question.id) + "\" style=\"display:none;\">" + answers_string + "</span><br /><a onclick=\"remove_from_test('" + (question.id) + "')\">Remove</a> | <a onclick=\"$('#answers_" + (question.id) + "').toggle();\">Edit answers</a> </li>";

			$("#test_questions").append(question_html);

			$("#test_questions #question_" + (question.id)).fadeIn('fast', function() {
				handle_test_question_change();
		      });

			
			// make the answers sortable

		    $( "#answers_chosen_" + question.id ).sortable({
		    	connectWith: ".test_answers_" + question.id
			    }).disableSelection();

		    $( "#answers_unused_" + question.id ).sortable({
		    	connectWith: ".test_answers_" + question.id
			    }).disableSelection();

		    // create event for when change
			$( "#answers_chosen_"+ question.id ).on( "sortupdate", {question_id: question.id}, handle_test_answer_change);


			
			
		}
	
		function handle_test_answer_change(event)
		{
			validate_test_question(event.data.question_id);
		}

		function handle_test_question_change()
		{
			// the order of the test questions has changed, we need to update test_question_ids to match

			var result = $( "#test_questions").sortable('toArray');

			// rebuild the test_question_ids array
			test_question_ids = [];
			
			if (result.length > 0)
			{
				for (var i = 0; i < result.length; i++) 
				{
					test_question_ids.push(parseInt(result[i].substr(9)));
				}
			}

			
		}
		
		function validate_test_question(id)
		{
			console.log("validate_test_question " + id);
			
			var result = $( "#answers_chosen_"+ id ).sortable('toArray');
	
			// check there is one right answer, and at leats one wrong answer, no more than 4 answers in total
	
			var correct_answer_count = 0;
			var incorrect_answer_count = 0;
			
			for (var i = 0; i < result.length; i++) 
			{
			  if (correct_answer_ids["" + result[i]])
			  {
				  correct_answer_count = correct_answer_count + 1;
			  }
			  else
			  {
				  incorrect_answer_count = incorrect_answer_count + 1;
			  }
			}
	
			var error = false;
			
			if (correct_answer_count != 1)
			{
				error = "There must be one, and only one, correct answer.";
			}
			
			if (incorrect_answer_count < 1)
			{
				error = "There must be at least one wrong answer.";
			}
			
			if ((correct_answer_count + incorrect_answer_count) > 4)
			{
				error = "There can not be more than four answers.";
			}
	
			if (error)
			{
				$( "#error_"+ id ).html("<br />" + error);
				$( "#error_"+ id ).show();
				$( "#question_"+ id).css("color", "pink");
			}
			else
			{
				$( "#error_"+ id ).hide();
				$( "#question_"+ id).css("color", "black");
			}
			
		}
		
		
		function remove_from_test(question_id)
		{
			question_id = parseInt(question_id);
			
			var index = $.inArray(question_id, test_question_ids);
			
			if (index > -1)
			{
	
				// hide then remove the item from the list
				$("#test_questions #question_" + (question_id)).slideUp('slow', function() {
					$("#test_questions #question_" + (question_id)).remove();
					handle_test_question_change();
				});
	
				// if it's still on the results list, show it
				$("#result_questions #question_" + (question_id)).fadeIn();
			}
			else
			{
				alert("Question not on the test");
			}

		}
	
		var saving = false;
		function save_test()
		{
			// basic validation
			if ($('#test_title').val() == "")
			{
				alert ("You need to give your test a title");
				return false;
			}
			
			// save the data
			
			// if we're in the process of saving, don't save over the top.
			if (saving)
			{
				return;
			}
	
			saving = true;

			$('#last_saved').show();
			$('#last_saved').html("Saving ...");
	
			// gather the data
	
			// we need to send the following to ajax.php to save:
			// ID (-1 if unsaved)
			// Title
			// Description
			// Questions and Answers (array of IDs)
			// Status
			// Link_hash
			
			// ajax.php can get the following
			// Author ID
			
			// View count
			// Completed count
			// Questions Count
			// Average Score
			// Date Created
			// Date last edited
			
			// get the a&a data
			var qanda = [];

			// test_question_ids is an array of question IDs
			if (test_question_ids.length > 0)
			{
				for (var i = 0; i < test_question_ids.length; i++) 
				{
					qanda.push([test_question_ids[i], $( "#answers_chosen_"+ test_question_ids[i] ).sortable('toArray')]);
				}
			}

			// save it
			console.debug("Saving " + qanda);
			
			$.post("ajax.php", {
					call: "save_test", 
					id: test_id,
					title: $('#test_title').val(),
					description: $('textarea#test_description').val(),
					questions_and_answers: qanda,
					status: $('input:radio[name=test_status]:checked').val(),
					link_hash: link_hash 
					}, function(data) {
				// sucessfully saved
				saving = false;

				var currentdate = new Date(); 
				var mins = currentdate.getMinutes();
				if (mins < 10)
				{
					mins = "0" + mins;
				}
				var hours = currentdate.getHours();
				if (hours < 10)
				{
					hours = "0" + mins;
				}
				$('#last_saved').html("Last saved at " + hours + ":"  + mins);

				console.debug(data);
				var data_as_int = parseInt(data);
				
				if (data_as_int > 0)
				{
					test_id = data_as_int;
					load_test(data_as_int);
				}
				else
				{
					alert ("Error saving");
				}
				
				
			})
			.fail(function() { 
				// error saving
				saving = false;
				alert("Error saving. Try again? Sorry I can't be more help, this isn't supposed to happen."); 
			});
	
		}
	
		function load_test(id)
		{
			console.log("loading test ID " + id);
			
			if (id == -1)
			{
				alert("Test not loaded. It may not have saved. Make sure you're logged in.");
				
				return;
			}
			// instead of ajax loading, we're going to just go to a specific URL to load a test
		}


		<?php 
		if ($test) {
			// load the questions for a given test
			if ($test->get_Questions())
			{
				// get the question
				foreach ($test->get_Questions() as $question)
				{
					// order all the possible answers with the chosen ones first 
					$chosen_answers = $test->get_Answers($question->get_ID());
					
					$all_answers = $question->get_all_Answers();
					
					$unchosen_answers = Array();
					$answer_objects = Array();
					
					// get the unchosen answers
					foreach ($all_answers as $all_answer)
					{
						if ($chosen_answers)
						{
							foreach ($chosen_answers as $chosen_answer)
							{
								// if we have found an All Answer which is a hosen answer, skip
								if ($all_answer->get_ID() == $chosen_answer->get_ID())
								{
									continue 2;
								}
							}
						}
						
						$unchosen_answers[] = $all_answer;
					}
					
					// we ahve the answers for the question, so we can now build the object
					if($chosen_answers)
					foreach ($chosen_answers as $chosen_answer)
					{
						if ($chosen_answer->is_correct())
						{
							$correct = "true";
						}
						else
						{
							$correct = "false";
						}
						
						$answer_objects[] = "chosen:true, correct:\"" . $correct . "\", id: \"" . $chosen_answer->get_ID() ."\", text: \"" . addslashes($chosen_answer->get_Text()) ."\"";
					}
					
					if ($unchosen_answers)
					foreach ($unchosen_answers as $unchosen_answer)
					{
						if ($unchosen_answer->is_correct())
						{
							$correct = "true";
						}
						else
						{
							$correct = "false";
						}
						
						$answer_objects[] = "chosen:false, correct:\"" . $correct . "\", id: \"" . $unchosen_answer->get_ID() ."\", text: \"" . addslashes($unchosen_answer->get_Text()) ."\"";
					}
					
					$answers_object = "answers: {answer: [{" . implode("}, {", $answer_objects) . "}]}";
					
					// the question object
					
					$question_object = "{id: \"" . $question->get_ID() . "\", text: \"" . addslashes($question->get_Text()) . "\", sections: {section:\"" . addslashes($question->get_Section()) . "\"}, " . $answers_object . "}";
					
					echo "
					
					question = " . $question_object . ";
					console.debug(question);
					append_question_to_test(question, true);";
				} // foreach question
			}// if there are questions
		} // if there's a test
		?>
		
		</script>			
			
			
			<?php 
		}
		else
		{
			// overview
			?>
			<h3>Test Builder</h3>
			<p>For feedback, feature requests, questions and bug reports please <a href="<? echo get_site_URL()?>forum">visit the forum</a>. I take no responsibility for anything you choose to do with these tests.</p>
			
			<h3>My tests</h3>
			<p>
				<table style="width: 100%; text-align: left; background-color: #eee;">
					<tr>
						<th>Title</th>
						<th style="text-align:center">Status</th>
						<th style="text-align:center">Views</th>
						<th style="text-align:center">Completes</th>
						<th style="text-align:center">Rating</th>
					</tr>
					<?php 
					$tests = get_tests_from_Author_ID($user->get_ID());
					if ($tests)
					{
						foreach ($tests as $tmp_test)
						{
							if ($tmp_test->get_Average_Rating() > 0)
							{
								$average_rating = number_format($tmp_test->get_Average_Rating(), 1);
							}
							else
							{
								$average_rating = "-";
							}
							
							echo "
							<tr style=\"padding: 5px\">
								<td>
									<a href=\"" . get_site_URL() . "test/builder/" . $tmp_test->get_ID() . "\">" . htmlentities(stripslashes($tmp_test->get_Title()))  . "</a> 
								</td>
								<td style=\"text-align:center\">
									<a href=\"" . $tmp_test->get_test_URL() . "\">" . htmlentities(stripslashes($tmp_test->get_Status())) . "</a>
							
								</td>
								<td style=\"text-align:center\">" . number_format($tmp_test->get_Views_Count()) . "</td>
								<td style=\"text-align:center\">" . number_format($tmp_test->get_Complete_Count()) . "</td>
								<td style=\"text-align:center\">" . $average_rating . "</td>
							</tr>";
						}
					}
					?>
				</table>
			</p>
			<p><a class="button mobilebutton" href="<?php echo get_site_URL(); ?>test/builder/new">Make a new test</a></p>
			<?php 
		}
		?>
		
		
		
		
		<?php 
	}
	else
	{
		?>
		<p>You must be logged in to build a test.</p>
		<?php 
	}
}
elseif ($url_array[1] == "generate")
{
	if ($_REQUEST['n'])
	{
		$parameters = Array (
			'number_of_questions' => $_REQUEST['n'],
			'difficulty' => $_REQUEST['d'],
			'pass_percentage' => $_REQUEST['p'],
			'output_format' => $_REQUEST['o']
		);
		
		// optional parameters
		// specific questions
		if ($_REQUEST['q'])
		{
			$parameters['question_IDs'] = explode(".", $_REQUEST['q']);
		}
		
		// seed
		if ($_REQUEST['s'])
		{
			$parameters['seed'] = $_REQUEST['s'];
		}
		
		
		// get the test
		$test = get_test_from_parameters($parameters);
		
		// display the test
		echo $test->get_formatted_output();
	}
	else
	{
		echo "<p>You need to give details on the kind of test you would like to generate.</p>
		<p><a href=\"" . get_site_URL() . "test/\">Click here to generate a Rules Test</a></p>";
	}
	

}
else
{
	// get the test
	try 
	{
		$test = get_test_from_ID($url_array[1]);
	
		// can it be viewed?
		if ($test->get_Status() == "draft")
		{
			if (is_logged_in() && ($user->get_ID() == $test->get_Author_ID()))
			{
				// display the test
				echo $test->get_formatted_output($_REQUEST['o']);
			}
			else 
			{
				echo"<p>This test is set to Draft. Only the Author can view it.</p>";
			}
		}
		elseif ($test->get_Status() == "private")
		{
			// increase the view count
			$test->set_Views_Count($test->get_Views_Count() + 1);
			set_test($test);
			
			// use strlower as the URL is always converted to lower case
			if ((is_logged_in() && ($user->get_ID() == $test->get_Author_ID())) || (strtolower($url_array[2]) == strtolower($test->get_link_hash())))
			{
				// display the test
				echo $test->get_formatted_output($_REQUEST['o']);
			}
			else 
			{
				echo"<p>This test is set to Private. You can only view the test if you have the private link.</p>";
				
			}
			// display the test
			
		}
		else
		{
			// increase the view count
			$test->set_Views_Count($test->get_Views_Count() + 1);
			set_test($test);
			
			// display the test
			echo $test->get_formatted_output($_REQUEST['o']);
		}
	
	} 
	catch (Exception $e) 
	{
		echo"<p>Sorry, we couldn't find your test. please check your link.</p>";
	}

}
include("footer.php");
?>