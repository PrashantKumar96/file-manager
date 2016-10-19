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

        <h1>Public downloads</h1><br/>

        <table border="1" style="margin-left: auto; margin-right: auto;">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>File Name</th>
                    <th>User</th>
                    <th>Downloads</th>
                </tr>
            </thead>
            <?php
                
                $pub_files = mysqli_query($dbh, 'SELECT * FROM uploads WHERE public = "on";');
                $i=1;

                while($file = mysqli_fetch_array($pub_files, MYSQL_ASSOC))
                {
                    echo '<tr>';

                    echo '<td>'.$i.'</td>';
                    echo '<td><a href="reviews.php?un='.$file['up_no'].'&user='.$file['user'].'">'.$file['filename'].'</a></td>';
                    echo '<td>'.$file['user'].'</td>';
                    echo '<td>'.$file['downloads'].'</td>';

                    echo '</tr>';

                    $i++;
                }
            ?>
        </table>
        <br/><br/><a href="back/logout.php">Logout</a><br/>
        <a href="profile.php">Profile Page</a><br/>
    </body>
</html>
<?php
    }
?>
