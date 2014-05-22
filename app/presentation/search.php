<?php 
// The page for RDTOM competitions

// show the page
set_page_subtitle("Turn left and find a question.");
include("header.php"); 
?>

<p style="text-align: center">Enter a search term below:</p>
<p style="text-align: center"><input type="text" id="search_string" style="width: 50%;"> <input type="button" onclick="search();" value="Search"></p>
<p style="text-align: center" class="small_p" id="search_filters_link" ><a onclick="$('#search_filters_link').hide(); $('#search_filters').show();">Filter</a></p>
<p style="text-align: center; display:none" class="small_p" id="search_filters">
	<?php 
	// the filters ticked by default
	$default_ticked = Array("WFTDA7");
	
	$current_taxonomy = "";
	$breakdown_string_array = Array();
	
	foreach ($mydb->get_terms() as $term)
	{
		// don't care about some taxonomies
		if ($term->get_taxonomy() != "rule-set")
		{
			continue;
		}
		
		// don't care about some specific names
		if ($term->get_Name() == "WFTDA7_Draft")
		{
			continue;
		}
		
		if ($current_taxonomy != $term->get_taxonomy())
		{
			$current_taxonomy = $term->get_taxonomy();
			$breakdown_string_array[$current_taxonomy] .= htmlentities($term->get_taxonomy()) . ": ";
		}
		
		if (in_array($term->get_Name(), $default_ticked))
		{
			$checked = "checked";
		}
		else
		{
			$checked = "";
		}
		
		// generate a checkbox
		$breakdown_string_array[$current_taxonomy] .= "<input type='checkbox' onchange='apply_filters()' $checked id='filter_" . htmlentities(str_replace(" ", "_", $term->get_Name())) . "'>" . htmlentities($term->get_Name()) . " ";
	}
	
	echo implode("<br />", $breakdown_string_array);
	?>
</p>

<span id="results"></span>

<script type="text/javascript">

var number_of_results = 0;
// load a new question
function search()
{

	$("#results").html("Loading ... ");
	// get the new question
	$.getJSON('<?php echo get_site_URL() ?>api/0.2/json/questions/?developer=RDTOM&application=searchpage&search=' + $("#search_string").val(), function(data) {

		number_of_results = 0;
		$("#results").html("");
		if (data.resource.questions.question instanceof Array)
		{
			$.each(data.resource.questions.question, function(key, question) 
			{
				append_question(question);
			});
		}
		else
		{
			// 1 or 0 results
			if (typeof data.resource.questions.question  !== 'undefined')
			{
				// 1 result
				append_question(data.resource.questions.question);
			}
		}

		//if (number_of_results == 0)
		//{
		//	$("#results").append("No results found");
		//}

		apply_filters();
	});
}

function append_question(question)
{

	var classes = "";
	for (var key in question.terms) 
	{
		classes = classes + " taxonomy_" + key;
		
		if (question.terms.hasOwnProperty(key)) 
		{
			if (question.terms[key] instanceof Array)
			{
				$(question.terms[key]).each(function( index, item ) {
					classes = classes + " term_" + item;
				});				
			}
			else
			{
				classes = classes + " term_" + question.terms[key].replace(" ","_");
			}
		}
	}

	//alert(classes);
	var question_html = "<p class=\"result " + classes + "\">";

	$(question.sections).each(function( index, section ) {
		section_string = section.section;	
		return false;
	});
	
	question_html = question_html + "<a href='<?php echo get_site_URL() ?>question/" + question.id + "'>" + section_string + " " + (question.text).replace(/\\(.)/mg, "$1") + "</a>";
	
	question_html = question_html + "</p>";

	$("#results").append(question_html);

	// load each new answer
	//	$.each(data.resource.question.answers.answer, function(key, answer) 
	//	{
	//		if (answer.correct == "true")
	//		{
	//			$("#answers").append("<a id=\"" + answer.id + "\" onclick=\"answer_question('correct', " + answer.id + ")\">" + (answer.text).replace(/\\(.)/mg, "$1") + "</a><br /><br />");
	//		}
	//		else
	//		{
	//			$("#answers").append("<a id=\"" + answer.id + "\" onclick=\"answer_question('wrong', " + answer.id + ")\">" + (answer.text).replace(/\\(.)/mg, "$1") + "</a><br /><br />");
	//		}		
	//	 });
}

function apply_filters()
{
	$('.result').show();
	
	// if we are applying a filter about the rule-set
	//if ($('#filter_WFTDA5').is(':checked') || $('#filter_WFTDA6').is(':checked'))
	//{
		$('.taxonomy_rule-set').hide();
		
		if ($('#filter_WFTDA5').is(':checked'))
		{
			$('.term_WFTDA5').show();
		}

		if ($('#filter_WFTDA6').is(':checked'))
		{
			$('.term_WFTDA6').show();
		}
	//}
	
}

</script>
<?php 
include("footer.php"); ?>