<?php

error_reporting(0);

require 'incs/db_connect.php';
require 'incs/validation.php';

define('UPLOAD_QUOTA', 2);

if(isset($_SESSION['userid']))
    {
        if (empty($_SESSION['cur_dir']) || empty($_GET['folder']) || $_GET['folder']=='' || $_SESSION['level'] == 1) 
        {
            $_SESSION['cur_dir'] = '';
            $_SESSION['parent'] = '';
            $_SESSION['level'] = 1;
        }
        
        if (isset($_GET['folder']) && $_GET['folder']!='')
        {
            if($_GET['folder']===basename($_SESSION['cur_dir']) && $_SESSION['level']===$_GET['s'])
                {}
            elseif($_GET['folder']===basename($_SESSION['parent']))
                {
                $_SESSION['level']--;
                $_SESSION['cur_dir'] = dirname($_SESSION['cur_dir']).'/';
                $_SESSION['parent'] = dirname($_SESSION['parent']);
                }
            else
                {
                $_SESSION['level']++;
                $_SESSION['parent'] = $_SESSION['cur_dir'];
                $_SESSION['cur_dir'] = $_SESSION['parent'].$_GET['folder'].'/';
                }
        }

        if($_SESSION['cur_dir'] == './')
            $_SESSION['cur_dir'] = '';

            
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
        <script type="text/javascript">
            function create() {
                document.getElementById("create").innerHTML = '<form action="back/folder.php" method="post"> <input type="text" id="fn" name="fname"/>&nbsp;<input type="submit" value="Create"/>';
                document.getElementById("fn").focus();
            }
        </script>
    </head>
    <body onload="document.getElementById('search').focus();">
        
        <h1>Your Uploads till now</h1>
        
        User ID: <?php echo $_SESSION['userid'];?><br/>
        <?php
        $files = mysqli_query($dbh, 'SELECT * FROM uploads WHERE user="'.$_SESSION['userid'].'" AND folder="'.$_SESSION['cur_dir'].'";');
        $num = mysqli_fetch_array(mysqli_query($dbh, 'SELECT * FROM users where user = "'.$_SESSION['userid'].'";'));
        echo 'No. of uploads till now: '.$num['uploads'].'<br/>';
        echo 'No. of downloads till now: '.$num['downloads'].'<br/><br/>';
        
        echo '<br/>';
        echo 'Your quota:<br>';
        echo '<progress value='.$num['score'].' max='.(UPLOAD_QUOTA*1024*1024)/(1000).'></progress>';
        echo '<br/>Space used: '.round(($num['score'])/1024,2).' MB/2 MB';
        echo '<br/><br/>';
        ?>
        <form action="search.php" method="post">
            <input type="text" id="search" name="term"/>&nbsp;
            <input type="submit" value="Search"/>
        </form>
        <br/>

        <h3>Your files:</h3>
        <form action="back/delete.php" method="post">
            <table border="1" style="margin-left: auto; margin-right: auto;">
                <tr>
                    <th></th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Time Uploaded</th>
                    <th>Public</th>
                    <th>Downloads</th>
                </tr>
                <?php
                $i=1;
                $fol=0;

                $dirs = scandir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$_SESSION['cur_dir'],1);

                echo '<b>Current Folder: '.((basename($_SESSION['cur_dir'])=='')?'Home':basename($_SESSION['cur_dir'])).'</b><br>';
                /*
                print_r($_GET);
                echo '<br>CUR_DIR: '.$_SESSION['cur_dir'].'<br>';
                echo 'PARENT: '.$_SESSION['parent'].'<br>';
                echo 'LEVEL: '.$_SESSION['level'].'<br>';
                echo 'GET_FOLDER: '.$_GET['folder'].'<br>';
                echo 'GET_S: '.$_GET['s'].'<br>';
                */

                if($_SESSION['cur_dir'] != '')
                {
                    echo '<tr>
                            <td></td>
                            <td></td>
                            <td><a href="view.php?folder='.basename($_SESSION['parent']).'&s='.($_SESSION['level']-1).'">..</a></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>';
                }

                while($file = mysqli_fetch_array($files, MYSQL_ASSOC))
                    {
                        if(in_array($file['filename'], $dirs))
                        {
                            if($file['type']=='folder')
                            {
                                echo '<tr>';
                                echo '<td><input type="checkbox" name="FOL_'.$file['filename'].'" />&nbsp;</td>';
                                echo '<td><img src="resources/fol.png" alt="Folder"/></td>';
                                echo '<td><a href="view.php?folder='.$file['filename'].'&s='.($_SESSION['level']+1).'">'.$file['filename'].'</a><br/></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '</tr>';
                                $fol++;
                            }
                        }
                    }

                $files = mysqli_query($dbh, 'SELECT * FROM uploads WHERE user="'.$_SESSION['userid'].'";');

                while($file = mysqli_fetch_array($files, MYSQL_ASSOC))
                    {
                        if(in_array($file['filename'], $dirs))
                        {
                            if($file['type']=='file' && $file['folder']==$_SESSION['cur_dir'])
                            {
                                echo '<tr>';
                                echo "<td><input type='checkbox' name='FIL_$i' />&nbsp;</td>";
                                echo '<td></td>';
                                echo '<td><a target="_blank" href="uploads/'.$_SESSION['userid'].'/'.$_SESSION['cur_dir'].$file['filename'].'">'.$file['filename'].'</a><br/></td>';
                                echo '<td>'.round($file['filesize']/(1024*1024),2).' MB</td>';
                                echo '<td>'.$file['time'].'</td>';
                                echo '<td>'.(($file['public']=='on')?'Yes':'No').'</td>';
                                echo '<td>'.$file['downloads'].'</td>';
                                echo '</tr>';
                                $i++;
                            }
                        }
                    }
                ?>
            </table>

            <div id="create">
                <input type="button" value="Create a Folder" onclick="create()">
            </div>

            <?php
                if($i==1 && $fol==0)
                    echo 'No files uploaded!';
                else
                    {
                    echo '<input type="submit" value="Delete" />';
                    }
            ?>
        </form>
        <br/><br/>
        <h4><u>Upload your files</u></h4><br/>
        <div id="upload">
            <form method="post" action="back/up.php" enctype="multipart/form-data">
                <input type="file" name="file" id="file" /><br/>
                Public: <input type="checkbox" name="public" /><br/>
                <input type="submit" value="Upload" />
            </form>

            <br/><br/><b>Note:</b> 1. Allowed file types are-<br/>JPG, GIF, PNG, DOC(X), PPT(X), XLS(X), PDF, TXT, RTF, ZIP, RAR.<br/> 2. File size restriction is of 20 MB.
        </div>

        <br/><br/><a href="back/logout.php">Logout</a><br/>
        <a href="profile.php">Profile Page</a><br/>

    </body>
</html>
<?php
    }

?>