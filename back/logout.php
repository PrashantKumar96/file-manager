<?php

session_start();
session_destroy();
echo 'Logged Out successfully! Redirecting to Login in 2 seconds!';
echo '<meta http-equiv="refresh" content="2;../login.php">';

?>