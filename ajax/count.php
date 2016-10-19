<?php

error_reporting(0);

require '../incs/db_connect.php';
require '../incs/validation.php';

if(!empty($_POST))
    {
        if($_POST['user'] != $_SESSION['userid'])
            {
                $downloads = mysqli_fetch_array(mysqli_query($dbh, 'SELECT downloads FROM users WHERE user ="'.$_SESSION['userid'].'";'));
                $downloads[0]++;
                mysqli_query($dbh, 'UPDATE users SET downloads = \''.$downloads[0].'\' WHERE user=\''.$_SESSION['userid'].'\';');
        
                $downloads = mysqli_fetch_array(mysqli_query($dbh, 'SELECT downloads FROM uploads WHERE user ="'.$_POST['user'].'" AND up_no="'.$_POST['fno'].'";'));
                $downloads[0]++;
                mysqli_query($dbh, 'UPDATE uploads SET downloads = \''.$downloads[0].'\' WHERE user ="'.$_POST['user'].'" AND up_no="'.$_POST['fno'].'";');
            }
    }
else
    {
        echo 'Please do not open this page manually!';
        echo '<meta http-equiv="refresh" content="2;../profile.php">';
    }
?>