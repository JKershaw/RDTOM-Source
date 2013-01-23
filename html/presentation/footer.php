<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */

/*
 * useful NEW tag: <span style="font-weight:bold; color:yellow; background-color: #333; padding:0 3px;">NEW!</span> 
 */
// display the page
?>	
		<div class="footer">
		
		<?php 
		/*
		<div class="layout_box" style="	margin: 20px 0; box-shadow: 3px 3px 5px #888; border: 1px solid red; min-height: 150px;">
	
			<img style="width:400px; max-width:90%; margin-right:20px; margin-bottom:20px; float:left;" src="http://rollerderbytestomatic.com/images/sponsors3_400.png" title="Thanks to our awsome sponsors: Wicked Skatewear, 5th Blocker Skates, Roller Derby City .com and Fast Girl Skates"/>
	
			<div id="competition_string">
			<?php
			echo get_competition_footer_string();
			?>
			</div>
	
		</div>
		*/
		?>
		
		<?php 
		if (is_question()) 
		{
			?>
			<p id="remebered_string_p" <?php if (!is_remebering_results()) { echo " style=\"display:none;\""; } ?>>
				<span id="remebered_string"><?php echo get_remebered_string(); ?></span>
			</p>
			<?php 
		} 
		else 
		{
			?>		
			<p>
				<a href="<?php echo get_site_URL(); ?>">Answer more questions</a> 
			</p>
			<?php 
		} 
		
		if (is_logged_in()) 
		{
			?>		
			<p>
				You are logged in as <strong><?php echo htmlentities(stripslashes($user->get_Name()))?></strong>, <a href="<?php echo get_site_URL(); ?>profile">view your profile</a>.
			</p>
			<?php 
		} 
		else 
		{
			?>
			<p>
				<a href="<?php echo get_site_URL(); ?>profile">Log in or sign up</a>. When logged in the site will track which sections of the rules you're good at, and which you need to brush up on.
			</p>	
			<?php 
		}
		?>		
			
		<?php if (is_question()) 
		{
			?>		
				<p>
					<a class="report_link" onclick="allow_keypress = false;$('#hidden_report_form').slideToggle();">Report this question</a> 
				</p>
			<?php 
		} 
		?>		
				
			<p>
				<a href="<?php echo get_site_URL(); ?>test/">Generate a Rules Test</a>
			</p>
			<p id="p_about_link"><a onclick="$('#p_about_link').hide();$('#p_about').fadeIn();">About</a></p>
			<p id="p_about" style="display:none">
	<?php if (is_question()) {?>
				For reference, this is question <a href="<?php echo get_site_URL(); ?>question/<?php echo $question->get_ID(); ?>"><strong>#<?php echo $question->get_ID(); ?></strong></a>. 
	<?php } ?>
	<?php	
	/*
				The database currently has <strong><?php echo number_format(get_question_count()); ?></strong> questions in it. 
				<br /><br />
	*/
	?>
				This site is made and maintained by me, Sausage Roller. I skate with New Wheeled Order, the Manchester Roller Derby league mens team. I'll be adding more features as I go (things like individual score tracking, difficulty, tests and what-not) so you should check back every so often. Full source code for the website can be <a href="https://github.com/RDTOM/RDTOM-Source">found on GitHub</a>.
				Feel free to send me requests and feedback via <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">email</a>, <a href="http://www.facebook.com/RollerDerbyTestOMatic">Facebook</a>, <a href="http://twitter.com/#!/wardrox">Twitter</a>, <a href="http://wardrox.tumblr.com/ask">Tumblr</a> or <a href="https://plus.google.com/108172735871267076610/posts">Google+</a>. <a onclick="$('#p_about_link').fadeIn();$('#p_about').hide();">Hide</a>
			</p>
			
			<p id="p_disclaimer_link"><a onclick="$('#p_disclaimer_link').hide();$('#p_disclaimer').fadeIn();">Disclaimer &amp; Privacy Policy</a></p>
			<p id="p_disclaimer" style="display:none">
				<strong>Disclaimer</strong>: This site isn't endorsed or affiliated with the WFTDA ... or anyone for that matter. 
				I made it to help me learn the rules of Roller Derby and if you find it useful, hurray!
				The questions were correct (to the best of my knowledge) when I wrote them<!-- and are written for the May 26, 2010 rule set -->. However you should never trust things you read on the internet so be sure to check <a href="http://wftda.com/rules">WFTDA.com/rules</a>.
				<br /><br />
				<strong>Privacy Policy</strong>: Privacy on the internet is important. So, I figure it's only fair for me to fess up: <strong>This site is remembering stuff about you</strong>. It needs to in order to function and be awesome.  As the site's always changing I can't really tell you exactly what it's remembering (though you're welcome to <a href="mailto:wardrox@gmail.com?Subject=Roller%20Derby%20Test%20O'Matic">email me</a> and get the latest), but it's the least amount of information possible. If you're not logged in then the site's probably remembering your IP address as well as what questions you've answered and what answers you gave for use in statistics gathering.
				<br /><br />
				The site will only remember information as long as necessary, and won't disclose personal information to outside sources without your explicit permission. The site will only collect personal information by lawful and fair means. Personal data collected will be relevant to the purposes for which it is to be used, and, to the extent necessary for those purposes, should be accurate, complete, and up-to-date. The site will protect your personal information by reasonable security safeguards against loss or theft, as well as unauthorized access, disclosure, copying, use or modification.  <a onclick="$('#p_disclaimer_link').fadeIn();$('#p_disclaimer').hide();">Hide</a> 
			</p>

			<!-- <p class="vote_string">
				<a href="<?php echo get_site_URL(); ?>poll">What features should be added next to the Test O'Matic?</a>
			</p> -->
			
		<?php if (is_admin()) 
		{
			?>		
				<p>
					<a href="<?php echo get_site_URL(); ?>admin/">Admin<?php echo get_open_report_count_string(); ?></a>, 
					<a href="<?php echo get_site_URL(); ?>stats/">Stats</a><?php 
					if (is_question()) 
					{
						?>, <a href="<?php echo get_site_URL(); ?>admin/edit/<?php echo $question->get_ID(); ?>#edit_question">Edit question</a><?php 
					}
					echo ", " . tracker_get_query_string(); ?>
				</p>
			<?php 
		} 
		?>	
		
			<div class="facebook_wrap_wide">
				<div class="fb-like" data-href="http://rollerderbytestomatic.com" data-send="false" data-width="500" data-show-faces="true"></div>
			</div>
			<div class="facebook_wrap_narrow">
				<div class="fb-like" data-href="http://rollerderbytestomatic.com/" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>
			</div>

		</div>
		<div class="print_footer">
			<p>This page was generated by the Roller Derby Test O'Matic</p>
		</div>
		<?php echo get_google_chart_script(); ?>
	</body>
</html>