<?php

/*
 * Roller Derby Test O'Matic
 * Created by John Kershaw (Skate name: Sausage Roller, GitHub user: JKershaw)
 *
 * Built to help Roller Derby players learn the rules
*/

include ('include.php');

// start the output buffer
ob_start();

set_up_database();
set_up_user();

Router::route(__DIR__);

//Output the buffer
while (@ob_end_flush());
