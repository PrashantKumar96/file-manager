<?php

error_reporting(0);

require '../incs/db_connect.php';
require '../incs/validation.php';

if(isset($_POST))
    {
        $comment = htmlentities($_POST['comment']);
        $comment = mysqli_real_escape_string($dbh,$comment);

        $up_no = htmlentities($_POST['un']);
        $up_no = mysqli_real_escape_string($dbh,$up_no);
        $up_no = trim($up_no);

        $user = htmlentities($_POST['uploader']);
        $user = mysqli_real_escape_string($dbh,$user);
        $user = trim($user);

        $coms = mysqli_num_rows(mysqli_query($dbh, 'SELECT * FROM comments;'));
        $coms++;

        mysqli_query($dbh, 'INSERT INTO comments (user, comment, comid, uploader, up_no, time) VALUES ("'.$_SESSION['userid'].'", "'.$comment.'", "'.$coms.'", "'.$user.'", "'.$up_no.'", "'.date('d/m/y h:i:s',time()).'");');
        
        header('Location: ../reviews.php?un='.$up_no.'&user='.$user);
    }
else
    {
        echo 'Please do not open this page manually!';
        echo '<meta http-equiv="refresh" content="2;../profile.php">';
    }
?>