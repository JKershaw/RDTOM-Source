<?php 
/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw
 * 
 * Built to help Roller Derby players learn the rules
 */
				
// display the page
include("header.php");

//TODO quick hack to get it working
if ($url_array[1] == "hard")
	$_GET['hard'] = "yes";
if ($url_array[1] == "easy")
	$_GET['easy'] = "yes";
	
//TODO get hard or easy question URLs working
?>
		<?php if ($_GET['hard'] == "yes")
		{?>
		<p>Here are the questions that most people get wrong. The number of correct responses is the percentage before the section number (this includes responses given before a question has been edited). How people resond is given as the percentages in the answers. If there's no percentages it means nobody has been asked that question since it was most recently edited.</p>
		<?php 
		}
		elseif ($_GET['easy'] == "yes")
		{?>
		<p>Here are the questions that most people get right. The number of correct responses is the percentage before the section number (this includes responses given before a question has been edited). How people resond is given as the percentages in the answers. If there's no percentages it means nobody has been asked that question since it was most recently edited.</p>
		<?php 
		}
		else
		{?>
		<p>Here are all the questions currently in the database.</p>
		<?php 
		}?>
		<?php 
			if (($_GET['hard'] == "yes") || ($_GET['easy'] == "yes"))
			{
				$questions = get_questions_hard(50, ($_GET['easy'] == "yes"));
			}
			else
			{
				$questions = get_questions();
			}
			
			foreach ($questions as $question)
			{
				if (($_GET['hard'] == "yes") || ($_GET['easy'] == "yes"))
				{
					$answers = $question->get_all_Answers(null, true);
				}
				else
				{
					$answers = $question->get_all_Answers();
				}
				
				echo "
				<p>
					<strong>";
				if (($_GET['hard'] == "yes") || ($_GET['easy'] == "yes"))
				{
					echo "<span style=\"color: " . get_colour_from_percentage($question->get_SuccessRate()) . "\">" . $question->get_SuccessRate() . "%</span> ";
				}
				echo htmlentities(stripslashes($question->get_Section())) . "
					</strong> 
					<a href=\"" . get_site_URL() . "question/" . $question->get_ID() . "\">" . htmlentities(stripslashes($question->get_Text())) . "</a>
				";
				
				echo "<ol type=\"A\">";
				foreach ($answers as $answer)
				{
					echo "<li>";
					if ((($_GET['hard'] == "yes") || ($_GET['easy'] == "yes")) && is_numeric($answer->get_SelectionPerc()))
					{
						echo $answer->get_SelectionPerc() . "% "; 
					}
					
					echo htmlentities(stripslashes($answer->get_Text()));
					
					if ($answer->is_correct())
					{
						echo " [Correct]";
					}
					echo "</li>";
				}
				echo "</ol>";
				echo "	</p>";
				
				if ($question->get_Notes()) 
				{
					echo "<p>Note: " . htmlentities(stripslashes($question->get_Notes())) . "</p>";
				}
				
				if ($question->get_WFTDA_Link())
				{
					echo "<p>Link: " . $question->get_WFTDA_Link() . "</p>";
				}
				
			}
		?>
		
<?php 
include("footer.php");
?>