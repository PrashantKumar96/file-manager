<?php

error_reporting(0);

require '../incs/db_connect.php';
require '../incs/validation.php';

if(!isset($_POST['fname']))
    {
        echo 'Please come through \'Your Uploads\' page. <br/> Redirecting in 2 seconds.<br/>';
        echo '<meta http-equiv=\'refresh\' content=\'2;../view.php\' />';
        die();
    }

if(isset($_SESSION['userid']))
    {
        $xml = simplexml_load_file('../logs/error_log.xml');

        $xml->addChild('log');
        $n=count($xml);
        $xml->log[$n-1]->addAttribute('id', $n);
        $xml->log[$n-1]->addChild('title','Folder Create');
        $xml->log[$n-1]->addChild('user',$_SESSION['userid']);
        $xml->log[$n-1]->addChild('time',date('d/m/y h:i:s',time()));

        $folder = htmlentities($_POST['fname']);
        $folder = mysqli_real_escape_string($dbh,$folder);
        $folder = strip_tags($folder);
        $folder = stripslashes($folder);
        $folder = str_ireplace('/','_',$folder);
        $folder = trim($folder);

        $dirs = scandir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$_SESSION['cur_dir'],1);

        $i=0;
        while($dirs[$i]!='..')
            {
                if($dirs[$i]===$folder)
                    {
                        $xml->log[$n-1]->addChild('event');
                        $e=count($xml->log[$n-1])-3;
                        $xml->log[$n-1]->event[$e-1]->addChild('id','FW1');
                        $xml->log[$n-1]->event[$e-1]->addChild('detail','WARNING: Folder name:'.$folder.' already exist in directory '.$_SESSION['cur_dir']);
                        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','yellow');
                        $xml->log[$n-1]->event[$e-1]->addChild('checked');
                        $xml->asXML('../logs/error_log.xml');
                        die('Folder already exists!!<br/> Go back and try another name.');
                    }
                $i++;
            }
        if(!mkdir(__DIR__.'/uploads/'.$_SESSION['userid'].'/'.$_SESSION['cur_dir'].$folder))
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','FE1');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in creating dir:'.$folder.' in '.$_SESSION['cur_dir']);
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
            }
        else
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','FI1');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success creating folder:'.$folder.' in directory '.$_SESSION['cur_dir']);
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                $xml->log[$n-1]->event[$e-1]->addChild('checked');
            }

        if(!mysqli_query($dbh, 'INSERT INTO uploads (user, filename, type, folder) VALUES (\''.$_SESSION['userid'].'\',\''.$folder.'\',\'folder\', \''.$_SESSION['cur_dir'].'\');'))
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','FE2');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in database entry. Entering data user='.$_SESSION['userid'].', filename='.$folder.', type=folder and folder='.$_SESSION['cur_dir']);
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                $xml->log[$n-1]->event[$e-1]->addChild('checked','No');
            }
        else
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','FI2');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Success adding entry to database');
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                $xml->log[$n-1]->event[$e-1]->addChild('checked');

            }

        $xml->asXML('../logs/error_log.xml');
        header('Location: ../view.php?folder='.$folder);
    }


?>