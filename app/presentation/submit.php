<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

set_page_subtitle("Turn left and submit questions.");

// display the page
include("header.php");


if (is_logged_in())
{
	$author_id_term = $mydb->get_term_from_taxonomy_and_name("author-id", $user->get_ID());
	if ($author_id_term)
	{
		// has a question been saved?
		if ($_POST)
		{
			// are we editing a question or adding a new one?
			if ($_POST['question_id'] > 0)
			{
				// editing a post
				$tmp_question = get_question_from_ID($_POST['question_id']);
				
				// is the Author, the Author of this post
				if (!$tmp_question->is_relationship_true("author-id", $user->get_ID()))
				{
					throw new exception ("You must be the author of this question to edit it.");
				}
				
	
				// save all the answers submitted into an array
				foreach ($_POST['answer'] as $id => $answer)
				{
					if (trim($answer))
					{
						$is_correct = $_POST['correct'][$id] == 1;
						$temp_answer_array[] = new answer(-1, $_POST['question_id'], trim($answer), $is_correct);
					}
				}
				
				
				// have the answers changed? There may not be any answers.
				if ($temp_answer_array && ($tmp_question->is_answers_different($temp_answer_array)))
				{
					
					// delete existing post & questions
					$mydb->remove_answers_given_questionID($tmp_question->get_ID());
					
					
					// save all the answers
					foreach ($_POST['answer'] as $id => $answer)
					{
						if (trim($answer))
						{
							$is_correct = $_POST['correct'][$id] == 1;
							add_answer($tmp_question->get_ID(), trim($answer), $is_correct);
						}
					}
				}
				
				// edit the question
				edit_question($tmp_question->get_ID(), $_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));
				
				// remove all relationships
				$mydb->remove_relationship_given_Question_ID($tmp_question->get_ID());
				
				// build new ones
			
				// build new relationships for the rule set
				$rule_set_term = $mydb->get_term_from_taxonomy_and_name("rule-set", "WFTDA6_Draft");
				$mydb->add_relationship($tmp_question->get_ID(), $rule_set_term->get_ID());
				
				// build new relationships for the author
				$mydb->add_relationship($tmp_question->get_ID(), $author_id_term->get_ID());
				
				// build new relationships for the language
				$language_term = $mydb->get_term_from_taxonomy_and_name("language", "English");
				$mydb->add_relationship($tmp_question->get_ID(), $language_term->get_ID());
				
				
				
				$message .= "Question edited! ";
					
			}
			else 
			{
				// Adding a new question
				// try to save the question
				$question_id = add_question($_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));
				
				// save all the answers
				foreach ($_POST['answer'] as $id => $answer)
				{
					if (trim($answer))
					{
						$is_correct = $_POST['correct'][$id] == 1;
						add_answer($question_id, $answer, $is_correct);
					}
				}
				
				// build new relationships for the rule set
				$rule_set_term = $mydb->get_term_from_taxonomy_and_name("rule-set", "WFTDA6_Draft");
				$mydb->add_relationship($question_id, $rule_set_term->get_ID());
				
				// build new relationships for the author
				$mydb->add_relationship($question_id, $author_id_term->get_ID());
				
				$message .= "New question saved! ";
	
			}
		}	
		
	
		
		// is a question being edited
		if ($url_array[1] == "edit")
		{
			$question = get_question_from_ID($url_array[2]);
			try 
			{	
				$answers = $question->get_all_Answers();
			} 
			catch (Exception $e) 
			{
				$message .= $e->getMessage();
			}
		}
		
		if ($message)
		{
			echo "<div class=\"message\">" . $message . "</div>";
		}
	
			
		?>
		<h3>Submit a new Question</h3>
		<form id="editquestionform" name="editquestionform" method="post" action="<?php 
				if ($question)
				{
					echo get_site_URL() . "submit/edit/" . $question->get_ID();
				}
				else
				{
					echo get_site_URL() . "submit/";
				}
			
				?>">
				
				<input type="hidden" name="question_id" value = "<?php 
				if ($question)
				{
					echo $question->get_ID();
				}
				?>">
				
				<table>	
					<tr>
						<td style="width:200px">Question:</td>
						<td><textarea id="question_text" style="width:500px" name="question_text" cols="40" rows="5"><?php 
						if ($question)
						{
							echo htmlentities(stripslashes($question->get_Text()));
						}
						?></textarea></td>
					</tr>
					<tr>
						<td style="width:200px">Section:</td>
						<td><input type="text"  id="question_section" name="question_section" style="width:100px" value="<?php 
						if ($question)
						{
							$section_value = htmlentities(stripslashes($question->get_Section_String()));
						}
						else 
						{
							$section_value = htmlentities(addslashes($_POST['question_section']));
						}
						
						echo $section_value;
						?>"> <span id="section_question_string"></span><?php
						if ($section_value)
						{
							$section_array = explode(".", $section_value);
							if (count($section_array)>1 && is_numeric($section_array[count($section_array)-1]))
							{
								$section_array[count($section_array)-1]++;
								$incrimneted_section_array = implode(".", $section_array);
							}
							
							if ($incrimneted_section_array)
							{
								?><a id="incrimentbutton" onclick="$('input[name=\'question_section\']').val('<?php echo $incrimneted_section_array ?>'); $('#incrimentbutton').hide()">+</a><?php 
							}
							
							if ($question && $question->get_WFTDA_Link())
							{
								?> <a target="_blank" href="<?php echo $question->get_WFTDA_Link(); ?>"> Link</a> <?php
							}
								
						}
						?>
						</td>
					</tr>
					<tr>
						<td>Answers:<br />
						(Tick correct answers)<br />
						(<a onclick="setdefaultanswers('tf');">TF</a> <a onclick="setdefaultanswers('pen');">Penalty</a>)</td>
						<td><?php 
						for ($i=0; $i<NUMBER_OF_ANSWERS; $i++)
						{
							$checked = "";
							$value = "";
							
							// if we're editing
							if ($answers && $answers[$i])
							{
								if ($answers[$i]->is_correct())
								{
									$checked = " checked";
								}
								$value = htmlentities(stripslashes($answers[$i]->get_Text()));
							}
							
							// if we're remebering
							if ($_POST['remeberanswers'])
							{
								if ($_POST['correct'][$i] == 1)
								{
									$checked = " checked";
								}
								$value = $_POST['answer'][$i];
								
							}
							
							echo "<input type=\"checkbox\" id=\"correct[" . $i . "]\" name=\"correct[" . $i . "]\" value=\"1\" $checked /> <input style=\"width:500px\"  type=\"text\" id=\"answer[" . $i . "]\" name=\"answer[" . $i . "]\" value=\"$value\" /><br />";
							
							
						}?></td>
					<!-- 
					<tr>
						<td style="width:200px">Notes:</td>
						<td>
							<input type="text"  id="question_notes" name="question_notes" style="width:500px" value="<?php 
							if ($question)
							{
								echo htmlentities(stripslashes($question->get_Notes()));
							}
							?>"></input>
						</td>
					</tr>
					 -->
					
					<tr>
						<td></td>
						<td><a class="button" onclick="newquestionvalidation('editquestionform');return false;"/><?php 
							if ($question)
							{
								echo "Edit";
							}
							else 
							{
								echo "Add";
							}
							?> Question</a>
						</td>
					</tr>
					<tr>
						<td>Remember answers:</td>
						<td><input <?php if ($_POST['remeberanswers']) { echo " checked"; }?> type="checkbox" value="yes" name="remeberanswers"/></td>
					</tr>
				</table>
			</form>
			
			<script type="text/javascript">
			function setdefaultanswers(type)
			{
				if (type == "tf")
				{
					$("input[name='answer[0]']").val("True");
					$("input[name='answer[1]']").val("False");
					<?php for ($i = 2; $i < NUMBER_OF_ANSWERS; $i++) { ?>
						$("input[name='answer[<?php echo $i ?>]']").val("");	
					<?php } ?>
				}
				
				if (type == "pen")
				{
					$("input[name='answer[0]']").val("No Impact/No Penalty");
					$("input[name='answer[1]']").val("Major Penalty");
					$("input[name='answer[2]']").val("Expulsion");
					<?php for ($i = 3; $i < NUMBER_OF_ANSWERS; $i++) { ?>
					$("input[name='answer[<?php echo $i ?>]']").val("");	
					<?php } ?>
				}
			}
	
			function newquestionvalidation(formname)
			{
				var error_string;
				
				var fields = $("input[name='correct']").serializeArray(); 
				if (fields.length == 0) 
				{ 
					//error_string = 'No correct answer given'; 
				} 	
	
				if ($("#question_section").val().length == 0)
				{
					error_string = 'No question asked'; 
				}
	
				if (error_string)
				{
					alert(error_string);
				}
				else
				{
					document.editquestionform.submit();
				}
			}
	
			var filter_string;
			function getnumberofquestions()
			{
				var section_string = $("#question_section").val();
	
				if (filter_string != section_string)
				{
					filter_string = section_string;
					
					// remove the training full stop if there is one
					if (section_string.substr(section_string.length - 1) == ".")
					{
						section_string = section_string.slice(0, - 1);
					}
				
					// fetch the number of questions for this section into an array
					var question_count_string_array = new Array();
					<?php 
						$all_WFTDA6_questions = get_questions(array("rule-set" => "WFTDA6"));
						
						if($all_WFTDA6_questions)
							foreach ($all_WFTDA6_questions as $question)
							{
								$section_array = explode("." , $question->get_Section());
								
								// build array counters
								$question_count_string_array[$section_array[0]]++;
								
								if ($section_array[1])
									$question_count_string_array[$section_array[0] . "." . $section_array[1]]++;
								
								if ($section_array[2])
									$question_count_string_array[$section_array[0] . "." . $section_array[1] . "." . $section_array[2]]++;
								
								if ($section_array[3])
									$question_count_string_array[$section_array[0] . "." . $section_array[1] . "." . $section_array[2] . "." . $section_array[3]]++;
								
								if ($section_array[4])
									$question_count_string_array[$section_array[0] . "." . $section_array[1] . "." . $section_array[2] . "." . $section_array[3] . "." . $section_array[4]]++;
							}
						
						try {
							$all_WFTDA6_Draft_questions = get_questions(array("rule-set" => "WFTDA6_Draft"));
						
						
							if ($all_WFTDA6_Draft_questions)
								foreach ($all_WFTDA6_Draft_questions as $question)
								{
									$section_array = explode("." , $question->get_Section());
									
									// build array counters
									$question_count_string_array[$section_array[0]]++;
									
									if ($section_array[1])
										$question_count_string_array[$section_array[0] . "." . $section_array[1]]++;
									
									if ($section_array[2])
										$question_count_string_array[$section_array[0] . "." . $section_array[1] . "." . $section_array[2]]++;
									
									if ($section_array[3])
										$question_count_string_array[$section_array[0] . "." . $section_array[1] . "." . $section_array[2] . "." . $section_array[3]]++;
									
									if ($section_array[4])
										$question_count_string_array[$section_array[0] . "." . $section_array[1] . "." . $section_array[2] . "." . $section_array[3] . "." . $section_array[4]]++;
								}
						} 
						catch (Exception $e) 
						{
						}
					
						foreach($question_count_string_array as $key=>$value)
						{
							if ($value == 1)
							{
								echo "question_count_string_array[\"$key\"] = \"1 question for this section currently in the database.\";";
							}
							elseif ($value > 1)
							{
								echo "question_count_string_array[\"$key\"] = \"$value questions for this section currently in the database.\";";
							}
						}
					?>
					
					if (question_count_string_array[section_string])
					{
						question_count_string = question_count_string_array[section_string];
					}
					else
					{
						if (section_string != "")
						{
							question_count_string = "<span style=\"color:green;\">No questions for this section currently in the database!</span>";
						}
						else
						{
							question_count_string = "<span style=\"color:grey;\">Enter a section to get question count</span>";
						}
					}
					
					$("#section_question_string").html(question_count_string);
				}
			}		
	
			$(document).ready(function(){
			    var intervalID = setInterval(function(){
			    	getnumberofquestions()
			    }, 100); // 100 ms check
			});
		
			</script>
			
		<?php 
		
		// get all questions for the user,
		try {
			$all_author_questions = get_questions(array("author-id" => $user->get_ID()));
		} catch (Exception $e) {
		}
		
		
		// show with edit links
		if ($all_author_questions)
		{
			
			?><h3>Edit a Question</h3>
			<p>Below are all the questions you have written:</p>
			<?php 
			foreach ($all_author_questions as $question)
			{
				echo "<p>" 
					. $question->get_Section() 
					. " <a href=\"" . get_site_URL() . "submit/edit/" . $question->get_ID() . "#edit_question\">" 
					. htmlentities(stripslashes($question->get_Text())) . "</a>
					</p>";
			}
		}
	}
	else
	{
		?>
		<p>To submit a new question you must be a registered question Author. If you would like to become an Author, please <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">email me</a>.</p>
		<?php 
	}
}
else
{
	?>
	<p>To submit a new question you must be <a href="<?php echo get_site_URL(); ?>profile">logged in</a> and be a registered question Author. If you would like to become an Author, please <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">email me</a>.</p>
	<?php 
}

?>
<h3>Notes on new Questions</h3>

<p>
This is the page where you can submit questions to the Test O'Matic. Submitted questions must be for the latest WFTDA rule set (<a href="http://wftda.com/rules/">available here</a>).
</p>
<p>
Your question will become a Draft question once submitted. It will be reviewed, possibly edited, and then most likely added to the database of questions. Your user name will be attached to the question as the Author Name automatically. If you would like different attribution (for example, you would like the site to credit a "Source", perhaps a league name, rather than an "Author") please <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">email me</a>.
</p>
<p>
Notes on question writing:
</p>
<ul>
	<li>
		Be as clear as you can when writing questions; somebody will always read it wrong.
	</li>
	<li>
		Common question types are True or False (written as "True or False: [statement]"), penalty severity questions (written as "[description of action] This is a/an:") or general scenario questions.
	</li>
	<li>
		A question can have more than four answers and more than one correct answer. When displaying the question a maximum of three randomly selected incorrect answers and one randomly selected correct answer (different each time the question is used) will be shown.
	</li>
	<li>
		You must include a section code with your question. If your question applies to multiple sections, put the most applicable section.
	</li>
	<li>
		A quick style guide:
		<ul>
			<li>Use the singular "they" as the pronoun of choice.</li>
			<li>"skaters" is not to be capitalised</li>
			<li>"Pivot", "Jammer" and "Blocker" are capitalised</li>
			<li>True or False questions shouldn't have a question mark</li>
			<li>If a question ends "..." (is a complete the sentence type question) the answers must start lower case</li>
			<li>"No Pack" is capitalised</li>
			<li>"in bounds" and "out of bounds" are not capitalised and don't have hyphens</li>
			<li>"Cutting the Track penalties" is capitalised</li>
		</ul>
	</li>
	<li>
		If you're not sure about something, or you have some feedback (good or bad), please <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">email me</a>.
	</li>
</ul>
<?php 
include("footer.php");
?>