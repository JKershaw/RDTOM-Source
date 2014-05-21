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