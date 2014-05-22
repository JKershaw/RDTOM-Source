<div class="layout_box" id="layout_box_all_questions" style="display:none;">
		
		<p>Search: <input type="text" id="questions_search" name="questions_search"/> <a onClick="get_search_questions_list();">Search</a></p>
		
		<p id="viewalllink"><a onclick="get_all_questions_list();">Load all questions</a></p>
		
		<p>Filter by rule set: 
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA5" onClick="change_current_ruleset('WFTDA5');">WFTDA5</a> 
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA6" onClick="change_current_ruleset('WFTDA6');">WFTDA6</a> 
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA6_Draft" onClick="change_current_ruleset('WFTDA6_Draft');">WFTDA6_Draft</a>
			<a class="current_ruleset_selector current_ruleset_selector_WFTDA7" onClick="change_current_ruleset('WFTDA7');">WFTDA7</a>
		</p>
		
		<p>Filter by section: <input type="text" id="filter_section" name="filter_section"/></p>
		
		
		<p id="viewalllist"></p>
		
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