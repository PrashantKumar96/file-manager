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
                    }
        </style>
    </head>
    <body>
        
        <h1>Search Results</h1>
        <?php
        $term = htmlentities($_POST['term']);
        $term = mysqli_real_escape_string($dbh,$term);
        $term=trim($term);

        $files = mysqli_query($dbh, 'SELECT * FROM uploads where user=\''.$_SESSION['userid'].'\';');

        while ($file = mysqli_fetch_array($files))
            {
                if(stristr($file[filename],$term))
                    echo '<a href="uploads/'.$_SESSION['userid'].'/'.$file['folder'].$file['filename'].'">'.$file['filename'].'</a><br/>';
            }
        ?>
        <br/><br/><a href="back/logout.php">Logout</a><br/>
        <a href="profile.php">Profile Page</a><br/>
    </body>
    </html>
<?php    }
?>