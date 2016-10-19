<?php

$db_hostname = "localhost";
$db_username = "user";
$db_pass = "password";
$db_name = "upload";

$dbh = mysqli_connect ($db_hostname, $db_username, $db_pass,$db_name);

if(empty($dbh))
    die ('Database Error!');

mysqli_select_db ($dbh, $db_name);

?>