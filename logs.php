<?php

error_reporting(0);

require 'incs/db_connect.php';
require 'incs/validation.php';

if(isset($_SESSION['userid']))
    {
        $xml = simplexml_load_file('logs/error_log.xml');
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

        <h1>Logs</h1><br>



        <table border="1" style="margin-left: auto; margin-right: auto;">
        <?php
            
            foreach($xml->log as $log)
                {   
                    echo '<tr><td colspan="3"><b><big>'.$log->title.'</big></b></td></tr>';
                    echo '<tr><td><b>ID</b></td><td colspan="2">'.$log[@id].'</td></tr>';
                    echo '<tr><td><b>User</b></td><td colspan="2">'.$log->user.'</td></tr>';
                    echo '<tr><td><b>Time</b></td><td colspan="2">'.$log->time.'</td></tr>';
                    echo '<tr><td colspan="3"><b>Events</b></td></tr>';
                    echo '<tr><td>ID</td><td>Details</td><td>Checked</td></tr>';
                    foreach($log->event as $event)
                        {
                            echo '<tr style="background:'.$event->detail[@status].';">';
                            echo '<td>'.$event->id.'</td>';
                            echo '<td>'.$event->detail.'</td>';
                            echo '<td>'.(($event->checked=='No' || $event->checked=='Yes')?'<input type="checkbox" '.(($event->checked=='Yes')?'checked':'').'/>':'').'</td>';
                            echo '</tr>';
                        }  
                    echo '<tr><td colspan="3">&nbsp;</td></tr>';          
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
