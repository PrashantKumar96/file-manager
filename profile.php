<?php

error_reporting(0);

require 'incs/db_connect.php';
require 'incs/validation.php';

if(isset($_SESSION['userid']))
    {
        ?>
<!DOCTYPE html>
<html>
    <head>
        <style type="text/css">
                    body{
                        text-align: center;
                        font-family: 'Segoe UI';
                    }
        </style>
    </head>
    <body>

        <h1>Profile Page</h1>
        <?php
            $user = $_SESSION['userid'];
            $rdata = mysqli_query($dbh, "SELECT * FROM users where user = '$user';");
            $adata = mysqli_fetch_array($rdata);
            echo 'User Name: '.$adata[user].'<br/>';
            echo 'Uploads till now: '.$adata[uploads].'<br/>';
            echo 'Score: '.$adata[score].'<br/><br/>';
        ?>
        <a href="view.php">My Uploads</a><br/>
        <a href="public.php">Public Downloads</a><br/><br/>
        <?php
            }
        else
            {
                echo 'Please login first. <br/> Redirecting to login page in 2 seconds.<br/>';
                echo '<meta http-equiv=\'refresh\' content=\'2;login.php\' />';
            }
        ?>
        <a href="login.php">User Login</a><br/>
        <a href='back/logout.php'>Logout</a><br/>
    </body>
</html>
