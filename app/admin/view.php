<?php 

include(__DIR__ . "/../presentation/header.php"); 

if ($message) {
	echo "<div class=\"message\">" . $message . "</div>";
}

?>
		
<p>
	<a class="button" onclick="show_page('edit_question');">Edit Question</a>
	<a class="button" onclick="show_page('reports');">Reports<?php echo $reports_menu_string; ?></a>
	<a class="button" onClick="show_page('all_questions');">All Questions</a>
	<a class="button" onClick="show_page('logs');">Logs</a>
	<a class="button" onClick="show_page('recompute');">Recompute</a>
	<a class="button" onClick="show_page('test');">Test</a>
</p>

<?php include(__DIR__ . "/tabs/editQuestion.php"); ?>

<?php include(__DIR__ . "/tabs/allQuestions.php"); ?>

<?php include(__DIR__ . "/tabs/logs.php"); ?>

<?php include(__DIR__ . "/tabs/recompute.php"); ?>

<?php include(__DIR__ . "/tabs/test.php"); ?>

<?php include(__DIR__ . "/tabs/reports.php"); ?>

	
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
		function get_search_questions_list()
		{
			$("#viewalllist").html("<p>Loading...</p>");

			$.post("ajax.php", { 
				call: "get_admin_questions_list", 
				search: $("#questions_search").val()
				},
				
				function(data) {

					$("#viewalllist").html(data);
					
				}
			);	
		}
		
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
				$("input[name='answer[0]']").val("No Impact/No Penalty");
				$("input[name='answer[1]']").val("Major Penalty");
				$("input[name='answer[2]']").val("Expulsion");
				<?php for ($i = 3; $i < NUMBER_OF_ANSWERS; $i++) { ?>
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
			
			if (page_name == "test")
			{
	    		$('#layout_box_test').fadeIn();
			}
			else
			{
	    		$('#layout_box_test').hide();
			}
			
			if (page_name == "recompute")
			{
	    		$('#layout_box_recompute').fadeIn();
			}
			else
			{
	    		$('#layout_box_recompute').hide();
			}
			
	    	window.location.hash='#'+page_name;
	    	
		}

		// if there's a hash location for a page, go there
		if (location.href.indexOf("#") != -1) 
		{
	        show_page(location.href.substr(location.href.indexOf("#") + 1));
	    }

		function toggle_term_relationship(term_id, question_id)
		{
			// send AJAX request
			// make it light grey and remeber what it was
			var tmp_colour = $("#term_" + term_id + "_" + question_id).css("color"); 
			$("#term_" + term_id + "_" + question_id).css("color", "grey"); 

			$.post("ajax.php", { 
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