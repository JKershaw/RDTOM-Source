<?php
include_once (__DIR__ . "/ColourFromPercentageCalculator.class.php");

class RememberedStringGenerator
{
	private $site_url;
	private $ColourFromPercentageCalculator;
	
	function __construct($site_url) {
		$this->site_url = $site_url;
		$this->ColourFromPercentageCalculator = new ColourFromPercentageCalculator();
	}
	
	public function generate($questionsAnsweredResults) {
		
		$questionsAnswered = count($questionsAnsweredResults);
		$questionsAnsweredCorrectly = $this->calculateCorrectCount($questionsAnsweredResults);
		
		$percentageCorrect = $this->calculatePercentage($questionsAnswered, $questionsAnsweredCorrectly);
		
		$percentageColour = $this->ColourFromPercentageCalculator->calculate($percentageCorrect);
		
		if ($questionsAnswered <= 0) {
			return "You've not answered any questions recently.";
		}
		
		return "You have a current success rate of <span style=\"font-weight:bold; color:" . $percentageColour . "\">" . $percentageCorrect . "%</span> (" . $questionsAnsweredCorrectly . " correct out of " . $questionsAnswered . "). <a href=\"" . $this->site_url . "forget\">Forget</a>";
	}
	
	private function calculateCorrectCount($questionsAnsweredResults) {
		
		$correct_count = 0;
		
		if (count($questionsAnsweredResults) > 0) {
			foreach ($questionsAnsweredResults as $tmp_result) {
				if ($tmp_result) {
					$correct_count++;
				}
			}
		}
		
		return $correct_count;
	}
	
	private function calculatePercentage($total, $correct) {
		if ($correct > 0) {
			return round((($correct / $total) * 100) , 2);
		} else {
			return 0;
		}
	}
}
