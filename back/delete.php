<?php

error_reporting(0);

require '../incs/db_connect.php';
require '../incs/validation.php';

$xml = simplexml_load_file('../logs/error_log.xml');

$xml->addChild('log');
$n=count($xml);
$xml->log[$n-1]->addAttribute('id', $n);
$xml->log[$n-1]->addChild('title','File/Folder Delete');
$xml->log[$n-1]->addChild('user',$_SESSION['userid']);
$xml->log[$n-1]->addChild('time',date('d/m/y h:i:s',time()));

function del_content($dir,$parent)
    {
        $files = scandir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$parent.$dir);

        foreach($files as $file)
            {
                if($file=='.' || $file=='..')
                    continue;

                if(is_dir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$parent.$dir.'/'.$file))
                    {
                        del_content($file,$parent.$dir.'/');
                        rmdir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$parent.$dir.'/'.$file);
                        $abcd = $dir.'/';
                    }
                else
                    {
                        unlink(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$parent.$dir.'/'.$file);
                    }
            }
    }

$size=0;

$data = array_keys($_POST);

foreach($data as $ele)
    {
        if(substr($ele,0,3) == 'FIL')
            {
                $ele = substr($ele,4);
                $file = mysqli_fetch_array(mysqli_query($dbh, 'SELECT * FROM uploads WHERE user = \''.$_SESSION['userid'].'\' AND up_no = \''.$ele.'\';'));
                if(unlink('../uploads/'.$_SESSION['userid'].'/'.$_SESSION['cur_dir'].'/'.$file['filename']))
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI1');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success deleting file '.$file['filename'].' from '.$_SESSION['cur_dir']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                    }
                else
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE1');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in deleting file '.$file['filename'].' from '.$_SESSION['cur_dir']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
                    }
                $size = $size + $file['filesize'];
                if(mysqli_query($dbh, 'DELETE FROM uploads WHERE user = \''.$_SESSION['userid'].'\' AND up_no = \''.$ele.'\';'))
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI2');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success database entry deletion of '.$file['filename']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                    }
                else
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE2');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error deleting database entry of user '.$_SESSION['userid'].' and up_no '.$ele);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
                    }

                if(mysqli_query($dbh, 'DELETE FROM comments WHERE uploader = \''.$_SESSION['userid'].'\' AND up_no = \''.$ele.'\';'))
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI8');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Deleted comments related to file with up_no '.$ele.' for user '.$_SESSION['userid']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                    }
                else
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE8');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in deleteing comments related to file with up_no '.$ele.' for user '.$_SESSION['userid']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
                    }
            }
        elseif(substr($ele,0,3) == 'FOL') 
            {
                $ele = substr($ele,4);
                $file = mysqli_fetch_array(mysqli_query($dbh, 'SELECT * FROM uploads WHERE user = \''.$_SESSION['userid'].'\' AND filename = \''.$ele.'\' AND type = \'folder\';'));
                del_content($ele,$_SESSION['cur_dir']);
                if(rmdir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$_SESSION['cur_dir'].$ele))
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI3');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success deleting folder '.$ele.' from '.$_SESSION['cur_dir']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                    }
                else
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE3');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in deleting folder '.$ele.' from '.$_SESSION['cur_dir']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
                    }
                if(mysqli_query($dbh, 'DELETE FROM uploads WHERE user = \''.$_SESSION['userid'].'\' AND folder LIKE \''.$ele.'/%\';'))
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI4');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success database entry deletion of all subfolders/files in '.$ele);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                    }
                else
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE4');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error deleting database entry of subfolders/files of user'.$_SESSION['userid'].' and folder '.$ele);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
                    }
                
                if(mysqli_query($dbh, 'DELETE FROM uploads WHERE user = \''.$_SESSION['userid'].'\' AND filename = \''.$ele.'\' AND type = \'folder\' AND folder = \''.$_SESSION['cur_dir'].'\';'))
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI5');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success database entry deletion of folder '.$ele);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                    }
                else
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE5');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error deleting database entry of folder '.$ele);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
                    }

            }
    }

$files=mysqli_query($dbh, 'SELECT filename FROM uploads WHERE user=\''.$_SESSION['userid'].'\' AND type!=\'folder\';');
$no=mysqli_num_rows($files);

$x=1;

while($file = mysqli_fetch_array($files, MYSQL_ASSOC))
    {
        mysqli_query($dbh, 'UPDATE uploads SET up_no=\''.$x.'\' WHERE user=\''.$_SESSION['userid'].'\' AND filename=\''.$file['filename'].'\';');
        $x++;
    }

if(mysqli_query($dbh, 'UPDATE users SET uploads=\''.$no.'\' WHERE user=\''.$_SESSION['userid'].'\';'))
    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DI6');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Updates number of uploads in database for user '.$_SESSION['userid']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
    }
else
    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','DE6');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in updating number of uploads to '.$no.' for user '.$_SESSION['userid']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
    }
$qsize = mysqli_query($dbh, 'SELECT * FROM users where user = \''.$_SESSION['userid'].'\';');
$asize = mysqli_fetch_array($qsize);

$score = $asize['score'] - ($size/1000);

if(mysqli_query($dbh, 'UPDATE users SET score=\''.$score.'\' WHERE user=\''.$_SESSION['userid'].'\';'))
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','DI7');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Updates score in database for user '.$_SESSION['userid']);
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
        $xml->log[$n-1]->event[$e-1]->addChild('checked');
    }
else
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','DE7');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in updating score to '.$score.' for user '.$_SESSION['userid']);
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
        $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
    }

$xml->asXML('../logs/error_log.xml');

echo 'Delete success!<br/>Redirecting to Profile Page in 2 seconds!<br/>';
echo '<meta http-equiv="refresh" content="2;../view.php?folder='.basename($_SESSION['cur_dir']).'">';

?>