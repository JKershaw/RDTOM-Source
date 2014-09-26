<?php 
		
// process the range of forms which may be submitted
if ($_POST['loginform'] == "yes")
{
	try 
	{
		user_log_in($_POST['name'], $_POST['password'], $_POST['remeber']=="Yes");
	}
	catch (Exception $e) 
	{
		$error_string = $e->getMessage();
	}
}
elseif (($_POST['logoutform'] == "yes") && is_logged_in())
{
	user_log_out();
	$profile_message = "You have logged out. Bye!";
}
elseif ($_POST['signupform'] == "yes")
{
	try 
	{
		user_sign_up($_POST['name'], $_POST['password'], $_POST['email']);
		//user_log_in($_POST['name'], $_POST['password'], $_POST['remeber']=="Yes");
		$profile_message = "Your account has been made, please log in now and experience the joy* of a Roller Derby Test O'Matic account (*joy not guaranteed).";
	}
	catch (Exception $e) 
	{
		$error_string = $e->getMessage();
		$sign_up_error = true;
	}
}
elseif ($_POST['disassociateform'] == "yes")
{
	$mydb->responses_disassociate($user->get_ID());
	$profile_message = "Answers disassociated! The site may take a little while to update, be patient.";
	
}
elseif ($_POST['formpasswordupdate'] == "yes")
{
	try 
	{
		user_update_password($_POST['oldpassword'], $_POST['newpassword']);
		$profile_message = "Your password has been updated.";
	}
	catch (Exception $e) 
	{
		$profile_message = $e->getMessage();
	}
	
}
elseif ($_POST['formnameupdate'] == "yes")
{
	try 
	{
		user_update_name($_POST['name']);
		$profile_message = "Your name has been updated.";
	}
	catch (Exception $e) 
	{
		$profile_message = $e->getMessage();
	}
	
}
elseif ($_POST['formemailupdate'] == "yes")
{
	try 
	{
		user_update_email($_POST['email']);
		$profile_message = "Your email has been updated.";
	}
	catch (Exception $e) 
	{
		$profile_message = $e->getMessage();
	}
	
}

// show the page
set_up_stats_header();
set_page_subtitle("Turn left and view your profile.");
include("header.php"); 


if ($profile_message)
{
	?>
<p><?php echo $profile_message; ?></p>
	<?php 
}

// is the user logged in?
if (is_logged_in())
{
	?>
	<p>
		<a class="button" onclick="show_page_stats();">Your stats</a>
		<a class="button" onclick="show_page_profile();">Update profile</a>
		<a class="button" onClick="document.formlogout.submit()">Log out</a>
	</p>
	
	<form method="post" action="<?php echo get_site_URL(); ?>profile" name="formlogout">
		<input type="hidden" name="logoutform" id="logoutform" value="yes" ></input>
	</form>
	
	<div class="layout_box" id="layout_box_stats">
	
		<?php echo return_stats_user_totals() ?>
		
		<?php echo return_stats_user_section_totals() ?>
		
		<?php echo return_stats_user_progress() ?>
		
		<?php echo get_recent_wrong_questions() ?>
		
		<?php echo get_recent_questions() ?>
	</div>
	
	<div class="layout_box" id="layout_box_profile" style="display:none;">
		<h3>Update your password</h3>
		<form method="post" action="<?php echo get_site_URL(); ?>profile#update" name="formpasswordupdate">
			<input type="hidden" name="formpasswordupdate" id="formpasswordupdate" value="yes" ></input>
			<p>
				Old password: <br />
				<input class="input_text" type="password" name="oldpassword" id="oldpassword"></input>
			</p>
			<p>
				New password (8 character minimum): <br />
				<input class="input_text" type="password" name="newpassword" id="newpassword"></input>
			</p>
			<p>
				<a class="button" onClick="document.formpasswordupdate.submit()">Update password</a>
			</p>
		</form>
		
		<h3>Update your email</h3>
		
		<form method="post" action="<?php echo get_site_URL(); ?>profile#update" name="formemailupdate">
			<input type="hidden" name="formemailupdate" id="formemailupdate" value="yes" ></input>
			<p>
				New email address: <br />
				<input class="input_text" type="text" name="email" id="email" value="<?php echo htmlentities(stripslashes($user->get_Email())); ?>"></input>
			</p>
			<p>
				<a class="button" onClick="document.formemailupdate.submit()">Update email</a>
			</p>
		</form>
		
		<h3>Update your name</h3>
		
		<form method="post" action="<?php echo get_site_URL(); ?>profile#update" name="formnameupdate">
			<input type="hidden" name="formnameupdate" id="formnameupdate" value="yes" ></input>
			<p>
				New name: <br />
				<input class="input_text" type="text" name="name" id="name" value="<?php echo htmlentities(stripslashes($user->get_Name())); ?>"></input>
			</p>
			<p>
				<a class="button" onClick="document.formnameupdate.submit()">Update name</a>
			</p>
		</form>
		
		<h3>Disassociate questions</h3>
		<p>To disassociate yourself from all the questions you have currently answered, click this button. This is not reversable and is only to be done in dire situations. Every single question you have answered will be forgotten and you will have to start all over again. Think about that for a second.</p>
		
		<form method="post" action="<?php echo get_site_URL(); ?>profile#update" name="disassociateform">
			<p>
				<input type="hidden" name="disassociateform"  id="disassociateform" value="yes" ></input>
				<a class="button" onClick="if (confirm('Are you sure you want the site to forget every answer you have given? This CAN NOT be undone.')){ document.disassociateform.submit() };">Disassociate Answers</a>
			</p>
		</form>
		
		<p>If you would like the your account and associated data permanently deleted from the system, or have any questions about the data the site stores, please email <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">contact@rollerderbytestomatic.com</a>.</p>
	</div>
	
	<script type="text/javascript">
	    if (location.href.indexOf("#") != -1) 
		{
	        // Your code in here accessing the string like this
	        if (location.href.substr(location.href.indexOf("#")) == "#stats")
	        {
	        	show_page_stats();
	        }
	        if (location.href.substr(location.href.indexOf("#")) == "#update")
	        {
	        	show_page_profile();
	        }
	    }
	
	    function show_page_stats()
	    {
	    	$('#layout_box_profile').hide();
	    	$('#layout_box_stats').fadeIn();

	    	if (typeof(google) != "undefined")
	    	{
	    		var chart_user_section_totals = new google.visualization.ColumnChart(document.getElementById('chart_section_breakdown'));
	    		if (typeof(data_user_section_totals) != "undefined")
	    		{
	    			chart_user_section_totals.draw(data_user_section_totals, options_user_section_totals);	
	    		}
	    		
	    	    var chart_stats_user_progress = new google.visualization.LineChart(document.getElementById('chart_progress'));
	    	    if (typeof(data_stats_user_progress) != "undefined")
	    		{
	    	    	chart_stats_user_progress.draw(data_stats_user_progress, options_stats_user_progress);	
	    		}
	    	}
	    	
	    	window.location.hash='#stats';
	    }
	
	    function show_page_profile()
	    {
	    	$('#layout_box_stats').hide();
	    	$('#layout_box_profile').fadeIn();
	    	window.location.hash='#update';
	    }
	</script>
	<?php 
}
else
{
	// only show this page if we're on SSL
	force_secure();
	
	?>
	<div id="form_login" <?php if ($sign_up_error) { echo "style=\"display: none;\""; }?>>
		<h3>Login to your account</h3>
		
		<form method="post" action="<?php echo get_site_URL(true); ?>profile" name="formlogin">
		<input type="hidden"  name="loginform" id="loginform" value="yes"></input>
		<p>
			Name:<br />
			<input class="input_text" type="text" id="name" name="name" />
		</p>
		<p>
			Password:<br />
			<input class="input_text" type="password" id="password" name="password" />
		</p>
		<p class="small_p">	
			<input type="checkbox" name="remeber" id="remeber" value="Yes" /> remember me (don't select this if you're on a public computer)
		</p>
		<p>
			<a class="button" onclick="document.formlogin.submit()">Login</a>
		</p>
		</form>
		
		<p>
			Roller Derby Test O'Matic accounts are free, <a onclick="$('#form_login').hide();$('#form_signup').show();">click here to get one</a>.
		</p>
	</div>
	
	<div id="form_signup" <?php if (!$sign_up_error) { echo "style=\"display: none;\""; }?>>
		<h3>Sign up</h3>
		<form method="post" action="<?php echo get_site_URL(true); ?>profile" name="formsignup">
			<input type="hidden" id="signupform" name="signupform"  value="yes"></input>
		<p>		
			Name: <br />
			<input class="input_text" type="text" id="signup_name" name = "name" />
		</p>
		<p>		
			Password: <br />
			<input class="input_text" type="password" id="signup_password" name = "password" /> <span id="password_extra"></span>
		</p>
		<p>		
			Email: <br />
			<input class="input_text" type="text" id="signup_email" name = "email"> <span style="font-style:italic; color:#777">Optional</span>
		</p>
		<p>
			<a class="button" onclick="document.formsignup.submit()">Sign up</a>
		</p>
		</form>
		<p>
			If you already have an account <a onclick="$('#form_signup').hide();$('#form_login').show();">click here to login</a>.
		</p>
	</div>
	<p><a href="<?php echo get_site_URL(); ?>passwordreset">Forgotten your password?</a></p>
	
	<script type="text/javascript">
	    if (location.href.indexOf("#") != -1) {
	        // Your code in here accessing the string like this
	        if (location.href.substr(location.href.indexOf("#")) == "#signup")
	        {
	        	$('#form_login').hide();
	        	$('#form_signup').show();
	        }
	    }

	    $('#signup_password').on('input', function() {
	    	 if ( $('#signup_password').val().length < 8)
	    	 {
	    		 $('#password_extra').html("<span style='color: red;'>Must be at least 8 characters</span>");
	    	 }
	    	 else
	    	 {
	    		 $('#password_extra').html("");
	    	 }
	    });
	</script>
	<?php 
}

include("footer.php"); 
?>