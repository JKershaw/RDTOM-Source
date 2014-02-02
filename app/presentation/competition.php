<?php 
// The page for RDTOM competitions

// show the page
set_page_subtitle("Turn left and win some stuff.");
include("header.php"); 
?>

<p style="text-align:center; "><img style="max-width:90%" src="http://rollerderbytestomatic.com/images/sponsors3.png" title="Thanks to our awsome sponsors: Wicked Skatewear, 5th Blocker Skates, Roller Derby City .com and Fast Girl Skates"/></p>

<p style="text-align:center; font-size:14px; margin-bottom:1px;">Total questions answered:</p>
<p class="competition_number_wrap"><span class="competition_number" id="count_responses_string">Loading ...</span> <br /> &nbsp;<span id="count_responses_difference_string"></span></p>


<div class="layout_box" style="	margin: 40px 0;">

	<p><strong>The Prize Draw</strong></p>
	<p style="text-align:center"><iframe width="853" height="480" src="http://www.youtube.com/embed/mG3iwKmS1zw?rel=0" frameborder="0" allowfullscreen></iframe></p>

	<p>The Grand Prize was won by <strong>Brazen Hussy</strong>, the two runner's-up are <strong>therev71</strong> and <strong>Olivia</strong>.</p>
</div>


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