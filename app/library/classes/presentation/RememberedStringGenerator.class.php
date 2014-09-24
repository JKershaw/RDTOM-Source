<?php
include_once(__DIR__ . "/ColourFromPercentageCalculator.class.php");

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

        $perc_value = 100;

        $percentageColour = $this->ColourFromPercentageCalculator->calculate($perc_value);
        
        if ($questionsAnswered <= 0) {
            return "You've not answered any questions recently.";
        }
        
        return "You have a current success rate of <span style=\"font-weight:bold; color:" . $percentageColour . "\">100%</span> (" . $questionsAnswered . " correct out of " . $questionsAnswered . "). <a href=\"" . $this->site_url . "forget\">Forget</a>";
    }
}