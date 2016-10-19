<?php

error_reporting(0);

require 'incs/db_connect.php';
require 'incs/validation.php';

if(isset($_SESSION['userid']) && !empty($_GET))
    {
        $up_no = htmlentities($_GET['un']);
        $up_no = mysqli_real_escape_string($dbh,$up_no);
        $up_no = trim($up_no);

        $user = htmlentities($_GET['user']);
        $user = mysqli_real_escape_string($dbh,$user);
        $user = trim($user);
            
        $file = mysqli_fetch_array(mysqli_query($dbh, 'SELECT * FROM uploads WHERE user = "'.$user.'" AND up_no = "'.$up_no.'"'));

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
        <?php
            if($file['user']!=$_SESSION['userid'])
                {
                    echo '
                        <script src="../jquery-2.1.0.js"></script> 
                        <script type="text/javascript">
                            function count(fuser, file)
                            {
                                $.post("ajax/count.php", { user : fuser, fno : file });
                            }
                        </script>';
                }
        ?>
    </head>
    <body>

        <h1>Public downloads</h1><br/><br/>

        <h3>File details:</h3>
        <?php
                        
            echo '<b>Name:</b> <a '.(($file['user']!=$_SESSION['userid'])?'onclick = "count(\''.$file['user'].'\', \''.$file['up_no'].'\')" ':'').'href="uploads/'.$file['user'].'/'.$file['folder'].$file['filename'].'" class="filelink">'.$file['filename'].'</a><br/>';
            echo '<b>Size:</b> '.round($file['filesize']/(1024*1024),2).' MB<br/>';
            echo '<b>Upload Time:</b> '.$file['time'].'<br/>';
            echo '<b>No. of downloads:</b> '.$file['downloads'].'<br/>';


        ?>

        <h3>Comments</h3>
        <?php
            
            $comments = mysqli_query($dbh, 'SELECT * FROM comments WHERE uploader = "'.$user.'" AND up_no = "'.$up_no.'"');

            while($comment = mysqli_fetch_array($comments, MYSQL_ASSOC))
            {
                echo '<small><i>'.$comment['user'].' commented on '.$comment['time'].'</i></small><br>';
                echo $comment['comment'];
                echo '<br/><hr style="width: 25%;"/><br/>';
            }
        ?>

        <form action="back/comment.php" method="post">
            <textarea name="comment" placeholder="Enter your comment here..."></textarea>
            <input type="hidden" name="uploader" value="<?php echo $user; ?>"/>
            <input type="hidden" name="un" value="<?php echo $up_no; ?>"/><br/>
            <input type="submit" value="Comment"/>
        </form>

        <br/><br/><a href="back/logout.php">Logout</a><br/>
        <a href="profile.php">Profile Page</a><br/>
    </body>
</html>
<?php
    }
else
    echo 'Invalid request!';
?>
