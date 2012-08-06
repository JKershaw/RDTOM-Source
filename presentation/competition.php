<?php 
// The page for RDTOM competitions

// show the page
set_page_subtitle("Turn left and win some stuff.");
include("header.php"); 
?>

<p style="text-align:center; "><img style="max-width:90%" src="http://rollerderbytestomatic.com/images/sponsors3.png" title="Thanks to our awsome sponsors: Wicked Skatewear, 5th Blocker Skates, Roller Derby City .com and Fast Girl Skates"/></p>

<p style="text-align:center; font-size:14px; margin-bottom:1px;">Total questions answered:</p>
<p class="competition_number_wrap"><span class="competition_number" id="count_responses_string">Loading ...</span> <br /> &nbsp;<span id="count_responses_difference_string"></span></p>

<?php 
if (is_competiton_on())
{
?>

<div class="layout_box" style="	margin: 40px 0;">

	<p><strong>Your eligibility:</strong></p>
	
	<?php
	if (!is_logged_in()) 
	{
		?>
		
		<p style="width: 100%;">
			<span style="color:red;">Not eligibile:</span> You need to be <a href="http://rollerderbytestomatic.com/profile">logged in</a> to take part. 
		</p>	
		<p style="width: 100%;">
			If you don't have an account yet they're <a href="http://rollerderbytestomatic.com/profile#signup">easy to set up</a>.
		</p>	
		<?php 
	} 
	else 
	{
		$timestamp_millionth = 1343197039;
		
		$responses_since_million = $mydb->get_responses_from_User_ID($user->get_ID(), $timestamp_millionth);
		
		$total_count = 0;
		$correct_count = 0;
		
		if ($responses_since_million)
		{
			foreach ($responses_since_million as $response)
			{
				$total_count++;
				if ($response->is_correct())
				{
					$correct_count++;
				}
			}
		}
		
		if ($total_count > 0)
		{
			$perc_value = round ((($correct_count / $total_count) * 100), 2);
		}
		$perc_colour = get_colour_from_percentage($perc_value);
		
		
		// have they answered enough questions
		// have they gotten enough correct
		if ($total_count < $competition_min_questions)
		{
			?>		
			<p style="width: 100%;">
				<span style="color:red;">Not eligibile:</span> You need to <a href="http://rollerderbytestomatic.com">answer more questions</a> to be entered into the prize draw. Only questions you've answered since the competition began will count.
			</p>
			<?php 
		}
		elseif ($perc_value < $competition_min_perc)
		{
			?>		
			<p style="width: 100%;">
				<span style="color:red;">Not eligibile:</span> You need to <a href="http://rollerderbytestomatic.com">answer more questions correctly</a> to be entered into the prize draw. You need to get at least <span style="color:<?php echo get_colour_from_percentage(100); ?>"><i>at least</i> 80&#37;</span> of the questions correct since the competition began to qualify.
			</p>
			<?php
		}
		else
		{
			?>		
			<p style="width: 100%;">
				<span style="color:green;">Eligibile:</span> You've answered enough questions and got enough correct! Yey! 
			</p>
			<p style="width: 100%;">
				What now? Well, I guess you should probably <a href="http://rollerderbytestomatic.com">answer more questions</a>, because it's fun!
			</p>
			<?php 
		}
		
		// what are they currently on
		if ($total_count > 0)
		{
		?>		
		<p style="width: 100%;">
			Since the competition started you've answered <strong><?php echo $total_count; ?></strong> question<?php if ($total_count != 1) echo "s"; ?> and have a success rate of <?php echo "<span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span>"?>.
		</p>
		<?php 
		}
	}
	?>
	</div>
<?php }
else
{
?>
<div class="layout_box" style="	margin: 40px 0;">

	<p><strong>The competition has ended!</strong></p>
	
	<?php
	if (!is_logged_in()) 
	{
		?>
		<p style="width: 100%;">
			To see if you're eligable to win you need to be <a href="http://rollerderbytestomatic.com/profile">logged in</a>. 
		</p>	
		<?php 
	} 
	else 
	{
		$timestamp_millionth = 1343197039;
		
		$responses_since_million = $mydb->get_responses_from_User_ID($user->get_ID(), $timestamp_millionth);
		
		$total_count = 0;
		$correct_count = 0;
		
		if ($responses_since_million)
		{
			foreach ($responses_since_million as $response)
			{
				$total_count++;
				if ($response->is_correct())
				{
					$correct_count++;
				}
			}
		}
		
		if ($total_count > 0)
		{
			$perc_value = round ((($correct_count / $total_count) * 100), 2);
		}
		$perc_colour = get_colour_from_percentage($perc_value);
		
		
		// have they answered enough questions
		// have they gotten enough correct
		if ($total_count < $competition_min_questions)
		{
			?>		
			<p style="width: 100%;">
				Alas, you were <span style="color:red;">not eligibile</span> to win. You needed to <a href="http://rollerderbytestomatic.com">answer more questions</a> to be entered into the prize draw.
			</p>
			<?php 
		}
		elseif ($perc_value < $competition_min_perc)
		{
			?>		
			<p style="width: 100%;">
				Alas, you were <span style="color:red;">not eligibile</span> to win. You needed to <a href="http://rollerderbytestomatic.com">answer more questions correctly</a> to be entered into the prize draw. You needed to get at least <span style="color:<?php echo get_colour_from_percentage(100); ?>"><i>at least</i> 80&#37;</span> of the questions correct since the competition began to qualify.
			</p>
			<?php
		}
		else
		{
			?>		
			<p style="width: 100%;">
				Ooh! You've been <span style="color:green;">entered into the prize draw</span>! Details will be coming soon. Yey!
			</p>
			<p style="width: 100%;">
				What now? Well, I guess you should probably <a href="http://rollerderbytestomatic.com">answer more questions</a>, because it's fun!
			</p>
			<?php 
		}
		
		// what are they currently on
		if ($total_count > 0)
		{
		?>		
		<p style="width: 100%;">
			During the two weeks of the competition, you answered <strong><?php echo $total_count; ?></strong> question<?php if ($total_count != 1) echo "s"; ?> and had a success rate of <?php echo "<span style=\"font-weight:bold; color:" . $perc_colour . "\">" . $perc_value . "%</span>"?>.
		</p>
		<?php 
		}
	}
	?>
	</div>

<?php 
}?>


	
	<?php 
if (is_competiton_on())
{
?>
	<h3 style="text-align: center;">Over One Million!</h3>	

	<p style="text-align: center;">Since the site went live a few months ago, you guys have answered more than ONE MILLION questions! Whaaa! To celebrate we're going to have a little competition.</p>
<?php 
}
?>

	<p><strong>The Grand Prize</strong></p>
	
	<ul>
		<li>$50 gift certificate from <a href="http://wickedskatewear.com/">Wicked Skatewear</a></li>
		<li>$25 online gift certificate from <a href="http://www.fastgirlskates.com/">Fast Girl Skates</a> </li>
		<li>A pair of Powerdyne electric yellow Moonwalker toe stops, from <a href="http://www.5thblockerskates.co.uk/">5th Blocker Skates</a></li>
		<li>A t-shirt of your choice from <a href="http://www.rollerderbycity.com/">Roller Derby City</a></li>
		<li>A month of training, supplements and support to <a href="http://lucyindaskywithdiamonds.com/derbalife/">DerbaLife</a> provided by <a href="http://lucyindaskywithdiamonds.com/">The Life and Times of Lucy In Da Sky With Diamonds</a></li>
		<li>A <a href="http://www.facebook.com/ManchesterRollerDerby">Manchester Roller Derby</a> t-shirt, from me (Sausage Roller), as that's my league</li>
	</ul>
	
	<p>There's a $25 gift certificate from <a href="http://wickedskatewear.com/">Wicked Skatewear</a> for two runner-ups.</p>
	<?php 
if (is_competiton_on())
{
?>	
	<p>If you would like to supply something to add to the prize fund, <a href="mailto:contact@rollerderbytestomatic.com ?Subject=Roller%20Derby%20Test%20O'Matic">get in touch</a>!</p>
<?php 
}
?>
	


	
	<?php 
if (is_competiton_on())
{
?>
	<p><strong>How to win</strong></p>
	<ul>
		<li>Answer a whole bunch of questions* and get <span style="color:<?php echo get_colour_from_percentage(100); ?>"><i>at least</i> 80&#37;</span> of them correct</li>
		<li>Be <a href="http://rollerderbytestomatic.com/profile">logged into the site</a> when you answer questions**</li>
		<li>Two weeks after the millionth question is answered (in <strong><?php echo time_string_to_competition_end(); ?></strong>) there'll be a prize draw to pick the winners.</li>
	</ul>
	<p class="small_p">
		* More than 50, don't worry we'll tell you how many you've done<br />
		** Be sure your account has a valid email address associated with it, so we can contact you if you win
	</p>
		
	<?php 
}
else
{
?>	<p style="text-align:center; font-weight: bold; color: red;">The competition has now closed. The prize draw will happen shortly.</p><?php 
}
?>

	
	
	<script type="text/javascript">

		function ajax_update_value(req_field_name)
		{
			if (typeof current[req_field_name] === "undefined")
			{
				current[req_field_name] = -1;
			}
			
			if (typeof current_difference[req_field_name] === "undefined")
			{
				current_difference[req_field_name] = 0;
			}
				
			$.post("ajax.php", { 
				call: req_field_name},
				function(data) {
					
					if (current[req_field_name] != data)
					{
						
						//$("#" + req_field_name + "_string").hide();
						$("#" + req_field_name + "_string").html(addCommas(data));
						//$("#" + req_field_name + "_string").fadeIn();
					}

					if (current[req_field_name] != -1)
					{
						current_difference[req_field_name] = data - current[req_field_name];

						if (current_difference[req_field_name] > 0)
						{
							$("#" + req_field_name + "_difference_string").hide();
							$("#" + req_field_name + "_difference_string").html("<span style='color:green'> +" + current_difference[req_field_name] + '</span>');
							$("#" + req_field_name + "_difference_string").fadeIn().fadeOut('slow');
						}
						
						if (current_difference[req_field_name] < 0)
						{
							$("#" + req_field_name + "_difference_string").hide();
							$("#" + req_field_name + "_difference_string").html("<span style='color:red'> " + current_difference[req_field_name] + '</span>');
							$("#" + req_field_name + "_difference_string").fadeIn().fadeOut('slow');
						}
					}

					current[req_field_name] = data;					
					setTimeout("ajax_update_value('" + req_field_name + "')", check_frequency[req_field_name] * 1000);
							
				}
			);
		}
	
		function addCommas(nStr)
		{
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}


		// how often each value should be updated (in seconds). Use prime numbers to stagger requests.
		// 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97, 101, 103
		var check_frequency = new Array();
		check_frequency["count_responses"] 			= 3;

		// values to define which values need to be updated
		var current = new Array();
		var current_difference = new Array();
		
		// function calls to start updating ever value with a check_frequency
		for (var key in check_frequency) 
		{
			ajax_update_value(key);
		}

		// update the graph
		
	</script>
<?php 
include("footer.php"); ?>