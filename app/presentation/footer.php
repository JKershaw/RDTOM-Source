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
		
		<?php if (is_view_only_changes())
		{ ?>
			<p>
				You are being given questions which <strong>only</strong> relate to rules which have been <a href="http://wftda.com/rules/change-summary/rules-2013-01-01" target="_blank">updated in the WFTDA 2013 rule set</a>. <a href="<?php echo get_site_URL(); ?>changes">Click here to be tested on all the rules.</a>
			</p>	
		<?php 
		} 
		else
		{
		?>
			<p>
				<a href="<?php echo get_site_URL(); ?>changes">Click here to be tested on <strong>only</strong> the rules which have been updated with the new WFTDA 2013 rule set</a>
			</p>	
			<?php 
		}
		?>	
		
			<p>
				<a href="<?php echo get_site_URL(); ?>test/">Generate a Rules Test</a> | 
				<a href="<?php echo get_site_URL(); ?>search">Search</a> | 
				<a href="<?php echo get_site_URL(); ?>about/">About, Disclaimer &amp; Privacy Policy</a> |
				<a href="<?php echo get_site_URL(); ?>cat">Meow</a> | 
				<a href="<?php echo get_site_URL(); ?>forum">Forum:</a> <span id="footer_forum_thread"></span>
			</p>
		
			
			<p>
				<!-- Google Play -->
				<a href="https://play.google.com/store/apps/details?id=com.rollerderbytestomatic.lite"><img border="0" alt="Android app on Google Play" src="https://developer.android.com/images/brand/en_app_rgb_wo_45.png" /></a>
				
				<!-- iTunes -->
				<a href="https://itunes.apple.com/us/app/roller-derby-test-omatic-lite/id642903652?ls=1&mt=8"><img border="0" alt="iOS app on iTunes" src="<?php echo get_site_URL(); ?>images/ios.png" /></a>
				
				
				<!-- Amazon -->
				<a href="http://www.amazon.com/gp/product/B00CMNI6QI/ref=as_li_ss_il?ie=UTF8&camp=1789&creative=390957&creativeASIN=B00CMNI6QI&linkCode=as2&tag=rdtom-20"><img border="0" style="height: 45px;" alt="Android app on Amazon for Kindle"
						src="<?php echo get_site_URL(); ?>images/amazon.png" /></a>
				<img src="http://www.assoc-amazon.com/e/ir?t=rdtom-20&l=as2&o=1&a=B00CMNI6QI" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				
			</p>	
		
			<div class="facebook_wrap_wide">
				<iframe src="http://www.facebook.com/plugins/like.php?href=http://rollerderbytestomatic.com/"
				scrolling="no" frameborder="0"
				style="border:none; width:450px; height:80px"></iframe>
	       	</div>
        
			<div class="facebook_wrap_narrow">
        		<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Frollerderbytestomatic.com%2F&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=131848900255414" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;"></iframe>
			</div>
			
			<?php if (is_admin()) 
			{
				?>		
					<p style="text-align:right;">
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
			
			
		</div>
		<div class="print_footer">
			<p>This page was generated by the Roller Derby Test O'Matic</p>
		</div>
		<?php echo get_google_chart_script(); ?>
		<script type="text/javascript">

			function ajax_update_forum_thread()
			{
				
				$.post("<?php echo get_site_URL(); ?>ajax.php", { 
					call: "latest_forum_thread"},
					function(data) 
					{
						// Show the data
						$("#footer_forum_thread").hide().html(data).fadeIn('slow'); 
					}				
				);
			}
			
			ajax_update_forum_thread();

		</script>
	</body>
</html>