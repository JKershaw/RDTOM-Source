<?php 
// an error has occured and been handled, so show the user something			
// display the page
set_page_subtitle("Turn left and break the site.");
include("header.php");
?>
<h3>Oh noes!</h3>
<p>For some reason the site has generated an error. It's been logged and will be delt with accordingly (Being a broken website, Major!). You can try doing what you just did again and see if it's only a temporary bug.<p>
<p>The error was:</p>
<pre><?php echo get_error_string(); ?></pre>
<?php 
include("footer.php");
?>