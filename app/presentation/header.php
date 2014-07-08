<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

// display the page
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "<?php echo get_http_or_https(); ?>://www.w3.org/TR/html4/loose.dtd">
<!-- Oh hey! Nice to see you there. My code's not awesome, but it does the job. If you're poking at the source code, drop me a tweet @wardrox. -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		
		<title>Roller Derby Test O'Matic</title>
		
		<?
		//echo get_CSS_embed("minify"); 
		echo get_CSS_embed(); 
		echo get_CSS_embed("print"); 
		?>
		
        <link rel="icon" href="<?echo get_site_URL(true); ?>images/favicon.gif" type="image/gif">
        <link rel="apple-touch-icon-precomposed" href="<?echo get_site_URL(true); ?>images/RDTOM_touch_icon.png">		
		
		<meta name="viewport" content="width=device-width" >
		
		<meta property="og:title" content="Roller Derby Test O'Matic" >
		<meta property="og:description" content="<?php echo get_page_description(); ?>" >
		<meta property="og:image" content="<?echo get_site_URL(true); ?>images/RDTOM_touch_icon.png" >
		
		<meta name="Description" content="<?php echo get_page_description(); ?>">
	
		<script src="<?echo get_site_URL(true); ?>js/jquery_1_8_3.min.js" type="text/javascript"></script>
   		<script src="<?echo get_site_URL(true); ?>js/jquery-ui_1_10_3.js" type="text/javascript"></script>
   		
	<style>
  #result_questions, #test_questions { list-style-type: none; margin: 0; padding: 0; float: left;  border: 1px solid #AAF; min-height:40px; background-color: #DDF; width:438px;}
  #result_questions li, #test_questions li {margin: 5px; padding: 5px; border: 1px solid #AAF; background-color: white; font-size: 11px;}
  #test_questions li {cursor:pointer;}
  
  .ui-state-highlight { height: 1.5em; line-height: 1.2em; background-color: #EEF}
  
  .test_answers {  margin: 0; list-style-type: upper-alpha; min-height:10px;}
  .test_unused_answers {  margin: 0; list-style-type: none; min-height:10px;}
  .test_answers li {margin: 5px; padding: 5px; border: 1px solid #AAF; background-color: white; font-size: 11px; cursor:pointer;}

  </style>
		<?php 
		if (!is_admin_page())
		{
			?>
			<script type="text/javascript">
			
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-431964-17']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			
			</script>
			<?php 
		} 
		?>
	
		
	</head>
	
	<body>
	
	<h1><a href="<?php echo get_site_URL(); ?>"><?php echo get_page_title(); ?></a></h1>
	<!--<h2><?php echo get_page_subtitle(); ?></h2>-->
	<h2>We're still on the <i>old</i> rules. Expect an update shortly.</h2>
		
<?php 

// if error
if ($error_string)
{
	echo "<p class=\"error_string\">" . $error_string . "</p>";
}
// if report
elseif ($report_string)
{
	echo "<h3 class=\"error_string\">Your report has been filed. Thanks very much for your help!</h3>";
}
?>