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
				if (is_question()) 
				{
					?>
					<p id="remebered_string_p" <?php if (!is_remebering_results()) { echo " style=\"display:none;\""; } ?>>
						<span id="remebered_string"><?php echo get_remebered_string(); ?></span>
					</p>
					<?php 
				} 
			?>
			
			<div class="footer_block">

				<?php 
				if (is_question()) 
				{
					?>		
					<p>
						<a class="report_link" onclick="allow_keypress = false;$('#hidden_report_form').slideToggle();">Report this question</a> 
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
						<a href="<?php echo get_site_URL(); ?>profile">Log in or sign up</a> to track your stats.
					</p>	
					<?php 
				}
				?>	

				<p>
					<a href="<?php echo get_site_URL(); ?>test/">Generate a Rules Test</a>
				</p>

                <p>
                    <a href="<?php echo get_site_URL(); ?>test/builder">Build your own Test</a>
                </p>
			</div>

			<div class="footer_block">
                <p><a href="<?php echo get_site_URL(); ?>forum">Forum:</a> <span id="footer_forum_thread"></span></p>
				<p><a href="<?php echo get_site_URL(); ?>about">About, Disclaimer &amp; Privacy Policy</a></p>
				<!-- <p><a href="<?php echo get_site_URL(); ?>search">Search</a></p> -->
				<p><a href="<?php echo get_site_URL(); ?>stats">view site stats</a></p>
				<p><a href="<?php echo get_site_URL(); ?>cat">Meow</a></p> 
				
			</div>

			<div class="footer_block">
				<p>Officially licensed by the <a href="http://wftda.com/">WFTDA</a></p>
				<p>Get the Test O'Matic App!</p>
				<p>
					<!-- Google Play -->
					<a href="https://play.google.com/store/apps/details?id=com.rollerderbytestomatic.lite"><img border="0" alt="Android app on Google Play" src="https://developer.android.com/images/brand/en_app_rgb_wo_45.png" /></a>
					<br />
					<!-- iTunes -->
					<a href="https://itunes.apple.com/us/app/roller-derby-test-omatic-lite/id642903652?ls=1&mt=8"><img border="0" alt="iOS app on iTunes" src="<?php echo get_site_URL(true); ?>images/ios.png" /></a>
					<br />
					<!-- Amazon 
					<a href="<?php echo get_http_or_https(); ?>://www.amazon.com/gp/product/B00CMNI6QI/ref=as_li_ss_il?ie=UTF8&camp=1789&creative=390957&creativeASIN=B00CMNI6QI&linkCode=as2&tag=rdtom-20"><img border="0" style="height: 45px;" alt="Android app on Amazon for Kindle"
							src="<?php echo get_site_URL(true); ?>images/amazon.png" /></a>
					<img src="<?php echo get_http_or_https(); ?>://www.assoc-amazon.com/e/ir?t=rdtom-20&l=as2&o=1&a=B00CMNI6QI" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
					-->
				</p>	
				<p>Also available: <a href="<?php echo get_site_URL(); ?>minimumskills">Minimum Skills app</a></p>
		
			</div>
	
			
			
			<div class="facebook_wrap_wide">
				<iframe src="<?php echo get_http_or_https(); ?>://www.facebook.com/plugins/like.php?href=http://rollerderbytestomatic.com/"
				scrolling="no" frameborder="0"
				style="border:none; width:450px; height:80px"></iframe>
	       	</div>
        
			<div class="facebook_wrap_narrow">
        		<iframe src="<?php echo get_http_or_https(); ?>://www.facebook.com/plugins/like.php?href=http%3A%2F%2Frollerderbytestomatic.com%2F&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=131848900255414" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;"></iframe>
			</div>

            <p style="text-align:center;">
                Site built by <a href="http://jkershaw.com/">John Kershaw</a> (aka Sausage Roller).
            </p>

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
						?>
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
				
				$.post("<?php echo get_site_URL(true); ?>ajax.php", { 
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