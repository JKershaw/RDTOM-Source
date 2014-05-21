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