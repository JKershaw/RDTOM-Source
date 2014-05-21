<?php 

	include(__DIR__ . "/../presentation/header.php"); 
		if ($message)
			echo "<div class=\"message\">" . $message . "</div>";
		?>
		
	<p>
		<a class="button" onclick="show_page('edit_question');">Edit Question</a>
		<a class="button" onclick="show_page('reports');">Reports<?php echo $reports_menu_string; ?></a>
		<a class="button" onClick="show_page('all_questions');">All Questions</a>
		<a class="button" onClick="show_page('logs');">Logs</a>
		<a class="button" onClick="show_page('recompute');">Recompute</a>
		<a class="button" onClick="show_page('test');">Test</a>
		<a class="button" onClick="show_page('admin');">Admin</a>
	</p>

	<?php include("/tabs/editQuestion.php"); ?>
		
	
	<div class="layout_box" id="layout_box_all_questions" style="display:none;">
		
		
		<p>Search: <input type="text" id="questions_search" name="questions_search"/> <a onClick="get_search_questions_list();">Search</a></p>
		<p>Section: <input type="text" id="filter_section" name="filter_section"/></p>
		<p>Rule set: 
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA5" onClick="change_current_ruleset('WFTDA5');">WFTDA5</a> 
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA6" onClick="change_current_ruleset('WFTDA6');">WFTDA6</a> 
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA6_Draft" onClick="change_current_ruleset('WFTDA6_Draft');">WFTDA6_Draft</a></p>
		
		<p><a onclick="$('.extra_all').toggle();">Toggle all</a></p>
		
		<p id="viewalllink"><a onclick="$('#viewalllink').hide(); $('#viewalllist').show(); get_all_questions_list();">Load all questions</a></p>
		
		<p id="viewalllist" style="display:none">
		</p>
		
		<script type="text/javascript">
		var filter_string;
		var current_ruleset = "question_string";

		function change_current_ruleset(new_ruleset)
		{
			current_ruleset = '.' + new_ruleset;
			$('.current_ruleset_selector').css( "font-weight" , "normal");
			$('.current_ruleset_selector_' + new_ruleset).css( "font-weight" , "bold");
			filterQuestions(true);
		}
		
		function filterQuestions(var_force)
		{
			var current_section = $("#filter_section").val();

			// Do we need to filter?
			if ((filter_string != current_section) || var_force)
			{
				filter_string = current_section;
					
				// remove the training full stop if there is one
				if (current_section.substr(current_section.length - 1) == ".")
				{
					current_section = current_section.slice(0, - 1);
				}

				// hide everything, then decide what to show $('.question_string').hide();
				$('.question_string').hide();
				
				// filter by section
				if (current_section == "")
				{
					// no section specified, so show the current section
					$(current_ruleset).show();
				}
				else
				{
					// a section is given
					current_section = '.section_' + current_section.replace(/\./g, "_");
					$(current_section + current_ruleset).show();
				}
				
			}
		}

		$(document).ready(function(){
		    var intervalID = setInterval(function(){
		    	filterQuestions(false)
		    }, 100); // 100 ms check
		});
		</script>
	</div>
	
	<div class="layout_box" id="layout_box_recompute" style="display:none;">
	
		<p><a href="<?php echo get_site_URL() ?>admin/?recompute=difficulty#recompute">Recalculate difficulty terms for all questions</a></p>

	</div>
	
	<div class="layout_box" id="layout_box_test" style="display:none;">
	
	
				
		I wonder if this'll change stuff?
		
		
		<div id="unarchiving_status"><a onclick="start_unarchiving();">Start unarchiving</a></div> 
		<div id="unarchiving_count"></div> 
		
		
		<script type="text/javascript">
			var total_count;
			var start;
			var d = new Date();
			var timeout_count = 0;
			
			function start_unarchiving()
			{
				$("#unarchiving_status").html("Started");
				$("#unarchiving_count").html("0");

				start = d.getTime();
				total_count = 0;
				unarchive();
			}

			function unarchive()
			{
				$("#unarchiving_status").html("Calling");
				$.ajax({  
				    url: "http://rollerderbytestomatic.com/cron.php?force=unarchive_responses",  
				    dataType: "jsonp", 
				    timeout: 30000,
				    error: function(xhr, textStatus, errorThrown){
				    	$("#unarchiving_status").html("Done!" + textStatus);
				    	if (textStatus == "parsererror")
				    	{
				    		
				    		total_count = total_count + 10;
				    		d = new Date();
				    		var average_time = Math.floor((((d.getTime() - start) / 1000) / total_count) * 1000)/1000;
				    		var response_time = Math.floor(((d.getTime() - start) / 1000) / (total_count / 10) * 1000)/1000;
				    		var hourly_rate = Math.round(3600 / (((d.getTime() - start) / 1000) / total_count));
				    		
				    		$("#unarchiving_count").html("Unarchived: " + total_count + " in " + ((d.getTime() - start) / 1000) + " seconds<br />Average: " + average_time + " seconds<br />Response time: "  + response_time + " seconds<br >Hourly rate: " + hourly_rate + "<br />Timeouts: " + timeout_count);
				    		unarchive();
				    	}
				    	else
				    	{
				    		$("#unarchiving_status").html("Error " + textStatus);
				    		timeout_count = timeout_count + 1;
				    		unarchive();
					    }
				    }});
			}
		</script>

	</div>
	
	<div class="layout_box" id="layout_box_logs" style="display:none;">
	
		<p>
		<?php 
		// list files in the log directory
		// create an array to hold directory list
		$results = array();

		// create a handler for the directory
		$handler = @opendir("../logs/");

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