<?php 

// show the page
set_page_subtitle("Turn left and learn about the API.");
include("header.php"); 
?>
<h3>API Documentation</h3>	
<p id="version_0_0_link"><a onclick="ajax_load_api_documentation('0.0');">Version 0.0</a> (Depreciated)</p>
<p id="version_0_1_link"><a onclick="ajax_load_api_documentation('0.1');">Version 0.1</a> (Stable)</p>
<p id="version_0_2_link"><a onclick="ajax_load_api_documentation('0.2');">Version 0.2</a> (In development/unreliable/subject to change)</p>

<span id="version_body"></span>

<script type="text/javascript">


	function ajax_load_api_documentation(version)
	{
		$("#version_body").html("Loading ...");
		$("#version_body").load("<?php echo get_site_URL(); ?>api/" + version + "/html/documentation");
	}

</script>
<?php include("footer.php"); ?>