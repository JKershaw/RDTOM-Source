<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */


// display the page
set_page_subtitle("Turn left and check out these awesome stats.");
include("header.php");
?>
	<p>
		<a class="button" onclick="$('#layout_box_graphs').hide();$('#layout_box_traffic').hide();$('#layout_box_general').fadeIn();$('#layout_box_questions').hide();">General</a>
		<a class="button" onclick="$('#layout_box_general').hide();$('#layout_box_traffic').hide();$('#layout_box_graphs').fadeIn();$('#layout_box_questions').hide();drawChart();">Graphs and Charts</a>
		<a class="button" onclick="$('#layout_box_general').hide();$('#layout_box_graphs').hide();$('#layout_box_traffic').fadeIn();$('#layout_box_questions').hide();">Web Traffic</a>
		<a class="button" onclick="$('#layout_box_general').hide();$('#layout_box_graphs').hide();$('#layout_box_traffic').hide();$('#layout_box_questions').fadeIn();">Questions</a>
	</p>
	
	<div class="layout_box" id="layout_box_general">
		<h3>General Stats:</h3>
		<p>
			Questions: <span id="count_questions_string">Loading ...</span><span id="count_questions_difference_string"></span><br />
			Answers: <span id="count_answers_string">Loading ...</span><span id="count_answers_difference_string"></span><br />
			Accounts: <span id="count_users_string">Loading ...</span><span id="count_users_difference_string"></span><br />
		</p>
		
		<p><strong>Responses:</strong></p>
		<p>When somebody selects an answer to a question, their response is saved.</p>
		<p>
			Total: <span id="count_responses_string">Loading ...</span> <span id="count_responses_difference_string"></span><br />
			Individuals: <span id="count_unique_IPs_string">Loading ...</span> <span id="count_unique_IPs_difference_string"></span><br />
			Current per-hour rate: <span id="count_hourly_responses_string">Loading ...</span> <span id="count_hourly_responses_difference_string"></span><br />
			Current per-minute rate: <span id="count_minutly_responses_string">Loading ...</span> <span id="count_minutly_responses_difference_string"></span><br />
		</p>
	</div>
	
	<div class="layout_box" id="layout_box_graphs" style="display:none;">
		<h3>Graphs and charts:</h3>
		
		<p>
			A graph of responses-per-hour covering the past 24 hours:
		</p>
		<p>
			<?php echo return_chart_24hour_responses(); ?>
		</p>
		
		<p>
			A chart showing each section of the rules and the % of related questions people have gotten right. The chart uses the last 10,000 responses:
		</p>
		
		<p>
			<?php echo return_chart_section_percentages(get_sections_array(), $mydb->get_responses(10000)); ?>
		</p>
	</div>
	
	<div class="layout_box" id="layout_box_traffic" style="display:none;">
		<h3>Web Traffic:</h3>
		
		<p>
			The following charts are taken from the site's Google Analytics (via seethestats.com to generate the widgets). If you would like a more detailed breakdown, get in touch.
		</p>
		
		<p style="text-align: center;">
			<iframe src="http://www.seethestats.com/stats/6319/Pageviews_a9e7eab7a_ifr.html" style="width:700px;height:300px;border:none;" scrolling="no" frameborder="0"></iframe>
		</p>
		
		<p style="text-align: center;">
			<iframe src="http://www.seethestats.com/stats/6319/VisitsByCountry_a4d7b822a_ifr.html" style="width:700px;height:300px;border:none;" scrolling="no" frameborder="0"></iframe>
		</p>
		
		<p style="text-align: center;">
			<iframe src="http://www.seethestats.com/stats/6319/Visitors_5174a3827_ifr.html" style="width:700px;height:300px;border:none;" scrolling="no" frameborder="0"></iframe>
		</p>
	</div>
	
	<div class="layout_box" id="layout_box_questions" style="display:none;">
		<h3>Question breakdown by type (there is overlap):</h3>
		<p>WFTDA 5 (May 2010): <?php echo number_format(count(get_questions(array("rule-set" => "WFTDA5"))));?></p>
		<p>WFTDA 6 (Jan 2013): <?php echo number_format(count(get_questions(array("rule-set" => "WFTDA6"))));?></p>
		
		
		
	</div>
		<script type="text/javascript">

		
			function ajax_update_value(req_field_name)
			{
				if (typeof current[req_field_name] === "undefined")
				{
					current[req_field_name] = -1;
				}
				
				if (typeof current_difference[req_field_name] === "undefined")
				{
					current_difference[req_field_name] = 0;
				}
					
				$.post("ajax.php", { 
					call: req_field_name},
					function(data) {
						
						if (current[req_field_name] != data)
						{
							$("#" + req_field_name + "_string").hide();
							if (isNaN(data))
							{
								$("#" + req_field_name + "_string").html(data);
							}
							else
							{
								$("#" + req_field_name + "_string").html(addCommas(data));
							}
							$("#" + req_field_name + "_string").fadeIn();
						}
	
						if (current[req_field_name] != -1 && !isNaN(data))
						{
							current_difference[req_field_name] = data - current[req_field_name];
	
							if (current_difference[req_field_name] > 0)
							{
								$("#" + req_field_name + "_difference_string").hide();
								$("#" + req_field_name + "_difference_string").html("<span style='color:green'> +" + current_difference[req_field_name] + '</span>');
								$("#" + req_field_name + "_difference_string").fadeIn().fadeOut('slow');
							}
							
							if (current_difference[req_field_name] < 0)
							{
								$("#" + req_field_name + "_difference_string").hide();
								$("#" + req_field_name + "_difference_string").html("<span style='color:red'> " + current_difference[req_field_name] + '</span>');
								$("#" + req_field_name + "_difference_string").fadeIn().fadeOut('slow');
							}
						}
	
						current[req_field_name] = data;					
						setTimeout("ajax_update_value('" + req_field_name + "')", check_frequency[req_field_name] * 1000);
					}
				);
			}
			
			//function ajax_update_graph(req_graph_name)
			//{
			//	$.post("ajax.php", { 
			//		call: req_graph_name},
			//		function(data) {
			//			$("#" + req_graph_name).html('<img style="max-width:100%" src="' + data + '" />');		
			//			//setTimeout("ajax_update_graph('" + req_graph_name + "')", 60000);
			//		}
			//	);
			//}
		
			function addCommas(nStr)
			{
				nStr += '';
				x = nStr.split('.');
				x1 = x[0];
				x2 = x.length > 1 ? '.' + x[1] : '';
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + ',' + '$2');
				}
				return x1 + x2;
			}


			// how often each value should be updated (in seconds). Use prime numbers to stagger requests.
			// 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97, 101, 103
			var check_frequency = new Array();
			check_frequency["count_responses"] 			= 3;
			check_frequency["count_minutly_responses"] 	= 5;
			check_frequency["count_hourly_responses"] 	= 11;
			check_frequency["count_unique_IPs"] 		= 17;
			check_frequency["count_questions"] 			= 41;
			check_frequency["count_answers"] 			= 43;
			check_frequency["count_users"] 				= 47;
			

			// values to define which values need to be updated
			var current = new Array();
			var current_difference = new Array();
			
			// function calls to start updating ever value with a check_frequency
			for (var key in check_frequency) 
			{
				ajax_update_value(key);
			}

			// update the graph
			//ajax_update_graph("graph_url_24_hour_responses");
			
		</script>
<?php 
include("footer.php");
?>