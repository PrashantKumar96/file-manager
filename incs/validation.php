<?php

if(!(isset($_SESSION))) {session_start();}

if(!isset($_SESSION['userid']))
    {
        echo 'Please login first. <br/> Redirecting to login page in 2 seconds.<br/>';
        echo '<meta http-equiv=\'refresh\' content=\'2;http://localhost/upload/login.php\' />';
        die();
    }

if ((time() - $_SESSION['start']) > 1800)
    {
        $_SESSION['userid'] = NULL;
        die('Session Expired!<br/>Go to <a href=\'http://localhost/upload/login.php\'>Login page</a> and login again.');
    }

?>