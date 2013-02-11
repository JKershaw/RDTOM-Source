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
	<h3>About RDTOM</h3>
	<p>
		This site is made and maintained by me, Sausage Roller. I skate with New Wheeled Order, the <a href="http://manchesterrolerderby">Manchester Roller Derby</a> league men's team. I'll be adding more features as I go (things like individual score tracking, difficulty, tests and what-not) so you should check back every so often. Full source code for the website can be <a href="https://github.com/RDTOM/RDTOM-Source">found on GitHub</a>.
	</p>
	<p>
		Feel free to send me requests and feedback via <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">email</a>, <a href="http://www.facebook.com/RollerDerbyTestOMatic">Facebook</a>, <a href="http://twitter.com/#!/wardrox">Twitter</a> or <a href="http://wardrox.tumblr.com/ask">Tumblr</a>.
	</p>
	
	<p>
		The database currently has <strong><?php echo number_format(get_question_count()); ?></strong> questions in it. There's more cool information over <a href="<?php echo get_site_URL(); ?>stats/">on the stats page</a>.
	</p>
	
	<h3>Disclaimer</h3>
	<p>
		This site isn't endorsed or affiliated with the WFTDA ... or anyone for that matter. 
		I made it to help me learn the rules of Roller Derby and if you find it useful, hurray!
		The questions were correct (to the best of my knowledge) when I wrote them. However you should never trust things you read on the internet so be sure to check <a href="http://wftda.com/rules">WFTDA.com/rules</a>.
	</p>
	
	<h3>Privacy Policy</h3>
	<p>
		Privacy on the internet is important. So, I figure it's only fair for me to fess up: <strong>This site is remembering stuff about you</strong>. It needs to in order to function and be awesome.  As the site's always changing I can't really tell you exactly what it's remembering (though you're welcome to <a href="mailto:wardrox@gmail.com?Subject=Roller%20Derby%20Test%20O'Matic">email me</a> and get the latest), but it's the least amount of information possible. If you're not logged in then the site's probably remembering your IP address as well as what questions you've answered and what answers you gave for use in statistics gathering.
	</p>
	<p>	
		The site will only remember information as long as necessary, and won't disclose personal information to outside sources without your explicit permission. The site will only collect personal information by lawful and fair means. Personal data collected will be relevant to the purposes for which it is to be used, and, to the extent necessary for those purposes, should be accurate, complete, and up-to-date. The site will protect your personal information by reasonable security safeguards against loss or theft, as well as unauthorized access, disclosure, copying, use or modification. 
	</p>
<?php 
include("footer.php");
?>