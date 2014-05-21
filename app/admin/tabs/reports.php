
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
	
			//$reports = $mydb->get_reports();
			
			if ($reports)
			{
				foreach ($reports as $report)
				{
					echo get_formatted_admin_report($report);
				}
			}
			else
			{
				?>No reports found<?php 
			}
			?>
		</p>
		
		
	</div>