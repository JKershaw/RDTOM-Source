<?php
class ColourFromPercentageCalculator
{
    
    public function calculate($perc_value) {
        
        if ($perc_value >= 80) {
            $perc_colour = "#008000";
        } elseif ($perc_value >= 70) {
            $perc_colour = "#CC6600";
        } else {
            $perc_colour = "#FF0000";
        }
        
        return $perc_colour;
    }
}
