<?php
/*
$xml = simplexml_load_file('error_log_test.xml');

foreach($xml->log as $log)
    {
        echo $log[@id].'<br>';
        echo $log->title.'<br>';
        echo $log->user.'<br>';
        echo $log->time.'<br>';
        foreach($log->event as $event)
            {
                echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$event->id.'<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$event->detail.'<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$event->checked.'<br>';
            }  
        echo count($xml->log[0]);          
        echo '<hr>';    
    }
/*
$xml->addChild('log');
$n=count($xml);
$xml->log[$n-1]->addAttribute('id', $n);
$xml->log[$n-1]->addChild('title','Third Log');
$xml->log[$n-1]->addChild('user','Kshitij');
$xml->log[$n-1]->addChild('time','ABCD');
$xml->log[$n-1]->addChild('event');
$xml->log[$n-1]->event->addChild('id','1');
$xml->log[$n-1]->event->addChild('detail','viyuolnu');
$xml->log[$n-1]->event->addChild('checked','Yes');


$xml->asXML('error_log.xml');


$s1 = 'FOL_abcd_avbj';
$s2 = 'FILE_gioh.com';

$e = explode('_',$s1);
echo $e[1];
*/

require 'incs/db_connect.php';

if ($a=mysqli_query($dbh, "SELECT user, filename FROM uploads"))
{
    echo 'Success';
}
else 
{
    print_r(mysqli_error_list($dbh));
}

?>