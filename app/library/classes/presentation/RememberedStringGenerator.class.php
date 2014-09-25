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
		$currentStreak = $this->calculateStreak($questionsAnsweredResults);
		
		if ($questionsAnswered <= 0) {
			return "You've not answered any questions recently.";
		}
		
		$currentSuccessString = "You have a current success rate of <span style=\"font-weight:bold; color:" . $percentageColour . "\">" . $percentageCorrect . "%</span> (" . $questionsAnsweredCorrectly . " correct out of " . $questionsAnswered . ").";
		
		$forgetString = " <a href=\"" . $this->site_url . "forget\">Forget</a>";
		
		if ($currentStreak > 4) {
			$streakString = " You are on a winning streak of <strong>" . $currentStreak . "</strong>.";
		} else {
			$streakString = "";
		}
		
		return $currentSuccessString . $streakString . $forgetString;
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
	
	private function calculateStreak($questionsAnsweredResults) {
		
		$current_streak = 0;
		
		for ($i = count($questionsAnsweredResults) - 1; $i >= 0; $i--) {
			if ($questionsAnsweredResults[$i]) {
				$current_streak++;
			} else {
				break;
			}
		}
		
		return $current_streak;
	}
}
