<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

// display the page
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<!-- Oh hey! Nice to see you there. My code's not awesome, but it does the job. If you're poking at the source code, drop me a tweet @wardrox. -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		
		<title>Roller Derby Test O'Matic</title>
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
   		
		<link rel="stylesheet" href="<?echo get_CSS_URL(); ?>?v=30April" type="text/css" />
        <link rel="icon" href="<?echo get_theme_directory(); ?>favicon.gif" type="image/gif"/>
        <link rel="apple-touch-icon-precomposed" href="<?echo get_theme_directory(); ?>RDTOM_touch_icon.png"/>		
		
		<meta name="viewport" content="width=device-width" />
		<meta property="og:title" content="Roller Derby Test O'Matic" />
		<meta property="og:description" content="<?php echo get_page_description(); ?>" />
		<meta name="Description" content="<?php echo get_page_description(); ?>">
	<?php if (is_competition_page())
	{
		?>
		<meta property="og:image" content="<?php echo get_site_URL() ?>images/sponsors.png" />
		<?php 
	}
	?>
	
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

	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=131848900255414";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

	<h1><a href="<?php echo get_site_URL(); ?>">Roller Derby Test O'Matic</a></h1>
	<h2><?php echo get_page_subtitle(); ?></h2>
		
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