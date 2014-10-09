<?php

include ('include.php');

set_up_database();
set_up_user();

// start the output buffer
ob_start();

Router::route(__DIR__);

//Output the buffer
while (@ob_end_flush());
