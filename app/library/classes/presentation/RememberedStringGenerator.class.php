<?php
include_once (__DIR__ . "/ColourFromPercentageCalculator.class.php");

class RememberedStringGenerator
{
	private $site_url;
	private $ColourFromPercentageCalculator;
	private $showStreakWhenStreakLength = 4;
	
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
		
		if ($currentStreak > $this->showStreakWhenStreakLength) {
			$streakString = " You are on a winning streak of <strong>" . $currentStreak . "</strong>.";
		} else {
			if ($this->hasStreakEndedSpecification($questionsAnsweredResults)) {
				$streakString = " <span style=\"color:#FF0000\">You just ended your streak of <strong>6</strong></span>.";
			} else {
				$streakString = "";
			}
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
	
	private function calculateStreak($questionsAnsweredResults, $offset = 0) {
		
		$current_streak = 0;
		
		for ($i = count($questionsAnsweredResults) - 1 - $offset; $i >= 0; $i--) {
			if ($questionsAnsweredResults[$i]) {
				$current_streak++;
			} else {
				break;
			}
		}
		
		return $current_streak;
	}

	private function hasStreakEndedSpecification($questionsAnsweredResults) {

		$wasLastAnswerWrong = $questionsAnsweredResults[count($questionsAnsweredResults) -1] === false;

		$priorStreakLength = $this->calculateStreak($questionsAnsweredResults, 1);

		return ($priorStreakLength > $this->showStreakWhenStreakLength) && $wasLastAnswerWrong;
	}
}
