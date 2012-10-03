<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */


// if the user isn't an admin, show an error message
if (!is_admin())
{
	// show error page if not admin
	?>
	Sorry, you must be logged in to view this page.
	<?php 
	exit;
}

// has a question been saved?
if ($_POST)
{
	// are we editing a question or adding a new one?
	if ($_POST['question_id'] > 0)
	{
		if ($_POST['question_text'] == "delete")
		{
			$mydb->remove_question_and_answers($_POST['question_id']);
			$message .= "Deleted. ";
			$question_deleted = true;
			
		}
		else
		{
			// editing a post

			// save all the answers submitted into an array
			foreach ($_POST['answer'] as $id => $answer)
			{
				if (trim($answer))
				{
					$is_correct = $_POST['correct'][$id] == 1;
					$temp_answer_array[] = new answer(-1, $_POST['question_id'], trim($answer), $is_correct);
				}
			}
			
			$tmp_question = get_question_from_ID($_POST['question_id']);
			
			// have the answers changed? There may not be any answers.
			if ($temp_answer_array && ($tmp_question->is_answers_different($temp_answer_array)))
			{
				
				// delete existing post & questions
				$mydb->remove_answers_given_questionID($tmp_question->get_ID());
				$message = "Answers deleted! ";
				
				// save all the answers
				foreach ($_POST['answer'] as $id => $answer)
				{
					if (trim($answer))
					{
						$is_correct = $_POST['correct'][$id] == 1;
						add_answer($tmp_question->get_ID(), trim($answer), $is_correct);
					}
				}
				$message .= "Answers saved! ";	
			}
			else
			{
				$message .= "Answers unchanged! ";
			}
			
			// edit the question
			edit_question($tmp_question->get_ID(), $_POST['question_text'], $_POST['question_section'], trim($_POST['question_notes']));
			$message .= "Question edited! ";	
			
			// check the applicable rule set
			// remove all relationships
			$mydb->remove_relationship_given_Question_ID($tmp_question->get_ID());
			$message .= "Relationships Removed! ";	
			
			// build new ones
			if ($_POST['term_checkbox'])
			{
				foreach ($_POST['term_checkbox'] as $term_ID => $data)
				{
					$mydb->add_relationship($tmp_question->get_ID(), $term_ID);
				}
				$message .= "Relationships Rebuilt! ";	
			}
		}
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
		$message .= "Question saved!";
		
		// build new relationships
		if ($_POST['term_checkbox'])
		{
			foreach ($_POST['term_checkbox'] as $term_ID => $data)
			{
				$mydb->add_relationship($question_id, $term_ID);
			}
			$message .= "Relationships Built! ";	
		}
	}
}

//update reports when needed
if ($_GET['update_report'])
{
	$report = $mydb->get_report_from_ID($_GET['update_report']);

	if ($_GET['new_status'] == "open")
	{
		$report->set_Status(REPORT_OPEN);
	}
	if ($_GET['new_status'] == "fixed")
	{
		$report->set_Status(REPORT_FIXED);
	}
	if ($_GET['new_status'] == "incorrect")
	{
		$report->set_Status(REPORT_INCORRECT);
	}
	if ($_GET['new_status'] == "clarified")
	{
		$report->set_Status(REPORT_CLARIFIED);
	}
	if ($_GET['new_status'] == "noaction")
	{
		$report->set_Status(REPORT_NOACTION);
	}
	
	set_report($report);
	
	$message .= "Report updated!";
}


// is a question being edited
if (($url_array[1] == "edit") && !$question_deleted)
{
	$question = get_question_from_ID($url_array[2]);
	try {	
		$answers = $question->get_all_Answers();
	} 
		catch (Exception $e) 
	{
		$message .= $e->getMessage();
	}
	
	// get the reports for this question
	$reports_question = $mydb->get_reports_from_question_ID($question->get_ID(), REPORT_OPEN);
}

// get the open reports (and the value for the menu)
$reports_open = $mydb->get_reports(REPORT_OPEN);
		
if ($reports_open && count($reports_open) > 0)
{
	$reports_menu_string = " (" . get_open_report_count($reports_open) . ")";
}



// display the page
set_page_subtitle("Turn left and administer all the things.");
include("header.php"); 
?>
		<?php 
		if ($message)
			echo "<div class=\"message\">" . $message . "</div>";
		?>
		
	<p>
		<a class="button" onclick="show_page('edit_question');">Edit Question</a>
		<a class="button" onclick="show_page('reports');">Reports<?php echo $reports_menu_string; ?></a>
		<a class="button" onClick="show_page('all_questions');">All Questions</a>
		<a class="button" onClick="show_page('logs');">Logs</a>
		<!-- <a class="button" onClick="show_page('competition');">Competition</a> -->
		<a class="button" onClick="show_page('test');">Test</a>
		<a class="button" onClick="show_page('admin');">Admin</a>
	</p>
		
	<div class="layout_box" id="layout_box_edit_question">	
		<h3><?php 
			if ($question)
			{
				echo "Edit";
			}
			else 
			{
				echo "Add";
			}
			?> question<?php 
			if ($question)
			{
				echo " #" . $question->get_ID();
			}
			?>:</h3>

		<form id="editquestionform" name="editquestionform" method="post" action="<?php 
			if ($question)
			{
				echo get_site_URL() . "admin/edit/" . $question->get_ID();
			}
			else
			{
				echo get_site_URL() . "admin/";
			}
		
			?>">
		
			<input type="hidden" name="question_id" value = "<?php 
			if ($question)
			{
				echo $question->get_ID();
			}
			?>"></input>
		
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
					?>"></input> <?php 
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
				<tr>
					<td style="width:200px">Source:</td>
					<td>
						<?php 
							echo get_admin_terms_checkboxes("source", $question);
						?>
						
						
					</td>
				</tr>				
				<tr>
					<td style="width:200px">Applicable Rule Set:</td>
					<td>
						<?php 
							echo get_admin_terms_checkboxes("rule-set", $question);
						?>
					</td>
				</tr>			
				<tr>
					<td style="width:200px">Tags:</td>
					<td>
						<?php 
							echo get_admin_terms_checkboxes("tag", $question);
						?>
					</td>
				</tr>
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
				<?php  if($question)
				{?>
				<tr>
					<td>Success rate:</td>
					<td><?php echo "<span style=\"color: " . get_colour_from_percentage($question->get_SuccessRate()) . "\">" . $question->get_SuccessRate() . "%</span> (" . number_format($question->get_ResponseCount()) . " responses)"; ?></td>
				</tr>
				<?php  } ?>
			</table>
		</form>
		
		<?php 
		
		if ($reports_question)
		{
			?>
			<h3>Reports:</h3>
			<p>
			<?php 
			foreach ($reports_question as $report)
			{
				if (($_POST['question_id'] == $report->get_Question_ID()) || ($url_array[2] == $report->get_Question_ID()))
				{
					echo "<strong>";
				}
				
				echo get_formatted_admin_report($report);
				
				if (($_POST['question_id'] == $report->get_Question_ID()) || ($url_array[2] == $report->get_Question_ID()))
				{
					echo "</strong>";
				}
			}
			?>
			</p>
			<?php 
		}
		?>
		

		
	</div>
	
	<div class="layout_box" id="layout_box_reports" style="display:none;">
		
		<h3>Reports:</h3>
		<p>
		<?php 
		
		if ($reports_open)
		{
			foreach ($reports_open as $report)
			{
				if (($_POST['question_id'] == $report->get_Question_ID()) || ($url_array[2] == $report->get_Question_ID()))
				{
					echo "<strong>";
				}
				
				echo get_formatted_admin_report($report);
				
				if (($_POST['question_id'] == $report->get_Question_ID()) || ($url_array[2] == $report->get_Question_ID()))
				{
					echo "</strong>";
				}
			}
		}
		else
		{
			?>No open reports<?php 
		}
		?>
		</p>
		<p id="viewallreportslink"><a onclick="$('#viewallreportslink').hide(); $('#viewallreportslist').show();">View all reports</a></p>
		<p id="viewallreportslist" style="display:none">
			<?php 
			$reports = $mydb->get_reports();
			
			if ($reports)
			{
				foreach ($reports as $report)
				{
					?>
					<a href="<?php echo get_site_URL() ?>admin/?edit=<?php echo $report->get_Question_ID();?>">
						<?php echo $report->get_Question_ID();?>
					</a>
					(<a href="<?php echo get_site_URL() ?>admin/?update_report=<?php echo $report->get_ID();?>&new_status=open">open</a>, 
					<a href="<?php echo get_site_URL() ?>admin/?update_report=<?php echo $report->get_ID();?>&new_status=fixed">fixed</a>, 
					<a href="<?php echo get_site_URL() ?>admin/?update_report=<?php echo $report->get_ID();?>&new_status=incorrect">incorrect</a>, 
					<a href="<?php echo get_site_URL() ?>admin/?update_report=<?php echo $report->get_ID();?>&new_status=clarified">clarified</a>, 
					<a href="<?php echo get_site_URL() ?>admin/?update_report=<?php echo $report->get_ID();?>&new_status=noaction">no action taken</a>): 
					<?php echo htmlentities(stripslashes($report->get_Text())); ?><br />
					<?php 
				}
			}
			else
			{
				?>No reports found<?php 
			}
			?>
		</p>
		
		
	</div>
	
	<div class="layout_box" id="layout_box_all_questions" style="display:none;">
	
		<p id="viewalllink"><a onclick="$('#viewalllink').hide(); $('#viewalllist').show(); get_all_questions_list();">Load all questions</a></p>
		<p id="viewalllist" style="display:none">
		</p>

	</div>
	
	<div class="layout_box" id="layout_box_test" style="display:none;">
	
		<?php 
		/*
		$all_users = $mydb->get_users();
		
		for ($i = 0; $i < 1000; $i++)
		{
			$tmp_user = array_pop($all_users);
			echo "<br />" . $tmp_user->get_Name() . "<br />" .  return_stats_user_progress($tmp_user);
		}
		*/
		
		
		$params = array(
				"tag" => "Test Question",
				"rule-set" => "WFTDA6"
		);
		
		print_r($params);
		echo "<br />";
		try {
			
			$questions = get_questions($params);
		
			foreach ($questions as $question)
			{
				echo " " . $question->get_ID();
			}
		} catch (Exception $e) {
			echo "No questions found";
		}
		echo "<br />";
		
		$params = array(
				"tag" => "Test Question"
		);
		
		print_r($params);
		echo "<br />";
		try {
			
			$questions = get_questions($params);
		
			foreach ($questions as $question)
			{
				echo " " . $question->get_ID();
			}
		} catch (Exception $e) {
			echo "No questions found";
		}
		echo "<br />";
		
		$params = array(
				"rule-set" => "WFTDA6"
		);
		
		print_r($params);
		echo "<br />";
		try {
			
			$questions = get_questions($params);
		
			foreach ($questions as $question)
			{
				echo " " . $question->get_ID();
			}
		} catch (Exception $e) {
			echo "No questions found";
		}
		echo "<br />";
		
		?>

	</div>
	
	<div class="layout_box" id="layout_box_logs" style="display:none;">
	
		<p>
		<?php 
		// list files in the log directory
		// create an array to hold directory list
		$results = array();

		// create a handler for the directory
		$handler = @opendir("logs");

		if ($handler)
		{
			// open directory and walk through the filenames
			while ($file = readdir($handler)) 
			{
				// if file isn't this directory or its parent, add it to the results
				if ($file != "." && $file != "..") 
				{
					// show the link to the log file
					$file_string_array[$file] = "<a href=\"" . get_site_URL() . "logs/" . $file . "\">" . $file . "</a>";
				}
			}
			
			sort($file_string_array);
			echo implode("<br />", $file_string_array);
	
			 // tidy up: close the handler
			closedir($handler);		
		}
		?>
		</p>
		

	</div>
	
	<div class="layout_box" id="layout_box_competition" style="display:none;">
	
		<p id="viewcompetitionlink"><a onclick="$('#viewcompetitionlink').hide(); $('#viewcompetition').show(); get_competition_list();">Load competition results</a></p>
		<p id="viewcompetition" style="display:none">
		</p>

	</div>
	
	<div class="layout_box" id="layout_box_admin" style="display:none;">
	
		<p>
			<a href="admin/?action=logout">Log out</a>
		</p>

	</div>

		<script type="text/javascript">

		function get_all_questions_list()
		{
			$("#viewalllist").html("<p>Loading...</p>");

			$.post("ajax.php", { 
				call: "get_admin_questions_list" 
				},
				
				function(data) {

					$("#viewalllist").html(data);
					
				}
			);	
		}

		function get_competition_list()
		{
			$("#viewcompetition").html("<p>Loading...</p>");

			$.post("ajax.php", { 
				call: "get_competition_list" 
				},
				
				function(data) {

					$("#viewcompetition").html(data);
					
				}
			);	
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
				$("input[name='answer[1]']").val("Minor Penalty");
				$("input[name='answer[2]']").val("Major Penalty");
				$("input[name='answer[3]']").val("Expulsion");
				<?php for ($i = 4; $i < NUMBER_OF_ANSWERS; $i++) { ?>
				$("input[name='answer[<?php echo $i ?>]']").val("");	
				<?php } ?>
			}
		}

		function show_page(page_name)
		{
			if (page_name == "edit_question")
			{
	    		$('#layout_box_edit_question').fadeIn();
			}
			else
			{
	    		$('#layout_box_edit_question').hide();
			}
			
			if (page_name == "reports")
			{
	    		$('#layout_box_reports').fadeIn();
			}
			else
			{
	    		$('#layout_box_reports').hide();
			}
			
			if (page_name == "all_questions")
			{
	    		$('#layout_box_all_questions').fadeIn();
			}
			else
			{
	    		$('#layout_box_all_questions').hide();
			}
			
			if (page_name == "logs")
			{
	    		$('#layout_box_logs').fadeIn();
			}
			else
			{
	    		$('#layout_box_logs').hide();
			}
			
			if (page_name == "competition")
			{
	    		$('#layout_box_competition').fadeIn();
			}
			else
			{
	    		$('#layout_box_competition').hide();
			}
			
			if (page_name == "admin")
			{
	    		$('#layout_box_admin').fadeIn();
			}
			else
			{
	    		$('#layout_box_admin').hide();
			}
			
			if (page_name == "test")
			{
	    		$('#layout_box_test').fadeIn();
			}
			else
			{
	    		$('#layout_box_test').hide();
			}
			
	    	window.location.hash='#'+page_name;
	    	
		}

		// if there's a hash location for a page, go there
		if (location.href.indexOf("#") != -1) 
		{
	        show_page(location.href.substr(location.href.indexOf("#") + 1));
	    }
		</script>
<?php include("footer.php");  ?>