<?php 

include(__DIR__ . "/../presentation/header.php"); 

if ($message) {
	echo "<div class=\"message\">" . $message . "</div>";
}

?>
		
<p>
	<a class="button" onclick="show_page('edit_question');">Edit Question</a>
	<a class="button" onClick="show_page('all_questions');">All Questions</a>
	<a class="button" onclick="show_page('reports');">Reports<?php echo $reports_menu_string; ?></a>
	<a class="button" onClick="show_page('logs');">Logs</a>
<!-- 	<a class="button" onClick="show_page('recompute');">Recompute</a>
	<a class="button" onClick="show_page('test');">Test</a> -->
</p>

<?php include(__DIR__ . "/tabs/editQuestion.php"); ?>

<?php include(__DIR__ . "/tabs/reports.php"); ?>

<?php include(__DIR__ . "/tabs/allQuestions.php"); ?>

<?php include(__DIR__ . "/tabs/logs.php"); ?>

<?php include(__DIR__ . "/tabs/recompute.php"); ?>

<?php include(__DIR__ . "/tabs/test.php"); ?>

	
	<script type="text/javascript">

		
		function newquestionvalidation(formname)
		{
			var error_string;	

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
				$("input[name='answer[0]']").val("Legal Play");
				$("input[name='answer[1]']").val("No Impact/No Penalty");
				$("input[name='answer[2]']").val("Penalty");
				$("input[name='answer[3]']").val("Expulsion");
				<?php for ($i = 4; $i < NUMBER_OF_ANSWERS; $i++) { ?>
				$("input[name='answer[<?php echo $i ?>]']").val("");	
				<?php } ?>
			}
		}

		
		function show_page(page_name) {

			$('.layout_box').hide();
	    	$('#layout_box_' + page_name).show();
			
			window.location.hash='#'+page_name;
	    	
		}

		//if there's a hash location for a page, go there
		if (location.href.indexOf("#") != -1) 
		{
			show_page(location.href.substr(location.href.indexOf("#") + 1));
		}

		function toggle_term_relationship(term_id, question_id)
		{
			// send AJAX request
			// make it light grey and remember what it was
			var tmp_colour = $("#term_" + term_id + "_" + question_id).css("color"); 
			$("#term_" + term_id + "_" + question_id).css("color", "grey"); 

			$.post("/ajax.php", { 
				call: "set_admin_relationship", 
				termID: term_id,
				questionID: question_id,
				},
				
				function(data) {

					// is now a relationship
					// make bold
					$("#term_" + term_id + "_" + question_id).css("font-weight", "bold"); 

					// remove light grey
					$("#term_" + term_id + "_" + question_id).css("color", tmp_colour); 
					
				}
			);	
			
		}
		</script>

<?php include(__DIR__ . "/../presentation/footer.php");  ?>