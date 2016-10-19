<?php

error_reporting(0);

require '../incs/db_connect.php';
require '../incs/validation.php';

$nou = mysqli_query($dbh, 'SELECT * FROM users where user="' . $_SESSION['userid'] . '";');
$no = mysqli_fetch_array($nou);
$no['uploads']++;

if(empty($_FILES))
    die('No file uploaded!');

$allowedExts = array('gif', 'jpeg', 'jpg', 'png', 'doc', 'docx', 'ppt', 'pptx', 'pdf', 'txt', 'rtf', 'xls', 'xlsx', 'zip', 'rar');
$temp = explode('.', $_FILES['file']['name']);
$extension = end($temp);
$extension = strtolower($extension);

$cur_score = $no['score'];

$xml = simplexml_load_file('../logs/../logs/error_log.xml');

$xml->addChild('log');
$n=count($xml);
$xml->log[$n-1]->addAttribute('id', $n);
$xml->log[$n-1]->addChild('title','File Upload');
$xml->log[$n-1]->addChild('user',$_SESSION['userid']);
$xml->log[$n-1]->addChild('time',date('d/m/y h:i:s',time()));


if (isset($_FILES['file']['name']))
{
    if(($cur_score + ($_FILES['file']['size']/1000))>((2*1024*1024)/(1000)))
        {
            $xml->log[$n-1]->addChild('event');
            $e=count($xml->log[$n-1])-3;
            $xml->log[$n-1]->event[$e-1]->addChild('id','UW1');
            $xml->log[$n-1]->event[$e-1]->addChild('detail','WARNING: File upload not allowed because size quota exceeded.');
            $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','yellow');
            $xml->log[$n-1]->event[$e-1]->addChild('checked');
            $xml->asXML('../logs/error_log.xml');
            die('File size limit exceeded! Please release some memory by deleting some files.');
        }

    if (in_array($extension, $allowedExts) && $_FILES['file']['size']<(20*1024*1024))
      {
      if ($_FILES['file']['error'] > 0)
        {
            $xml->log[$n-1]->addChild('event');
            $e=count($xml->log[$n-1])-3;
            $xml->log[$n-1]->event[$e-1]->addChild('id','UW2');
            $xml->log[$n-1]->event[$e-1]->addChild('detail','WARNING: Error in uploading file.');
            $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','yellow');
            $xml->log[$n-1]->event[$e-1]->addChild('checked');
            $xml->asXML('../logs/error_log.xml');
            die('Error: ' . $_FILES['file']['error'] . '<br>');
        }
      else
        {
        if(file_exists('uploads/' . $_SESSION['userid'] . '/' . $_SESSION['cur_dir'] . '/' . $_FILES['file']['name']))
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','UW3');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','WARNING: File upload not allowed because file already exists.');
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','yellow');
                $xml->log[$n-1]->event[$e-1]->addChild('checked');
                $xml->asXML('../logs/error_log.xml');
                die('File already exists! Go back and upload other file.');
            }
        if(move_uploaded_file($_FILES['file']['tmp_name'],'../uploads/' . $_SESSION['userid'] . '/' . $_SESSION['cur_dir'] . '/' . $_FILES['file']['name']))
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','UI1');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Uploaded file moved to '.$_SESSION['cur_dir']);
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
                $xml->log[$n-1]->event[$e-1]->addChild('checked');
            }
        else
            {
                $xml->log[$n-1]->addChild('event');
                $e=count($xml->log[$n-1])-3;
                $xml->log[$n-1]->event[$e-1]->addChild('id','UE1');
                $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in moving uploaded file');
                $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
                $xml->log[$n-1]->event[$e-1]->addChild('checked','no');
            }
        $upload=$_FILES['file']['name'];
        $size=$_FILES['file']['size'];
        }
      }
    else
      {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UW4');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','WARNING: File upload not allowed because of invalid file name or large file size');
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','yellow');
        $xml->log[$n-1]->event[$e-1]->addChild('checked');
        $xml->asXML('../logs/error_log.xml');
        die('Invalid file (check whether file type is allowed or not and file size is less than 20 MB).');
      }
}

if(mysqli_query($dbh, 'INSERT INTO uploads (user, filename, up_no, filesize, folder, time, public) VALUES (\''.$_SESSION['userid'].'\',\''.$upload.'\',\''.$no['uploads'].'\',\''.$size.'\', \''.trim($_SESSION['cur_dir']).'\', \''.date('d/m/y h:i:s',time()).'\', \''.$_POST['public'].'\');'))
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UI2');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Added entry to database');
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
        $xml->log[$n-1]->event[$e-1]->addChild('checked');
    }
else
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UE2');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in adding databse entry: user ='.$_SESSION['userid'].', filename='.$upload.', up_no='.$no['uploads'].', filesize='.$size.', folder='.$_SESSION['cur_dir'].', time='.date('d/m/y h:i:s',time()).', public='.$_POST['public']);
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
        $xml->log[$n-1]->event[$e-1]->addChild('checked','no');
    }

if(mysqli_query($dbh, 'UPDATE users SET uploads=\''.$no['uploads'].'\' WHERE user=\''.$_SESSION['userid'].'\';'))
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UI3');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Updated number of uploads in database');
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
        $xml->log[$n-1]->event[$e-1]->addChild('checked');
    }
else
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UE3');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in updating number of uploads to '.$no['uploads'].' in database for user '.$_SESSION['userid']);
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
        $xml->log[$n-1]->event[$e-1]->addChild('checked','no');
    }

$qsize = mysqli_query($dbh, 'SELECT * FROM users where user = \''.$_SESSION['userid'].'\';');
$asize = mysqli_fetch_array($qsize);

$score = $asize[score] + ($size/1000);

if(mysqli_query($dbh, 'UPDATE users SET score=\''.$score.'\' WHERE user=\''.$_SESSION['userid'].'\';'))
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UI4');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','INFO: Updated score in database');
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','green');
        $xml->log[$n-1]->event[$e-1]->addChild('checked');
    }
else
    {
        $xml->log[$n-1]->addChild('event');
        $e=count($xml->log[$n-1])-3;
        $xml->log[$n-1]->event[$e-1]->addChild('id','UE4');
        $xml->log[$n-1]->event[$e-1]->addChild('detail','ERROR: Error in updating score to '.$score.' for user '.$_SESSION['userid']);
        $xml->log[$n-1]->event[$e-1]->detail->addAttribute('status','red');
        $xml->log[$n-1]->event[$e-1]->addChild('checked','no');
    }

$xml->asXML('../logs/error_log.xml');

echo 'Upload success!<br/>Redirecting to Profile Page in 2 seconds!<br/>';
echo '<meta http-equiv="refresh" content="2;../view.php?folder='.basename($_SESSION['cur_dir']).'">';

?>