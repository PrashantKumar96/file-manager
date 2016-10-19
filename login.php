<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>User login</title>
        <style type="text/css">
            body{
                text-align: center;
                font-family: 'Segoe UI';
            }
        </style>
        <link type="text/javascript" href="valid.js" />
    </head>
    <body>
        <h1>User login</h1>
        <form method="post">
            Username: <input type="text" name="username" autocomplete="off" /><br /><br />
            Password: <input type="password" name="pass" autocomplete="off" /><br /><br />
            <input type="submit" value="Submit" name="submit" />
        </form>
        <br/><br/>
<?php

error_reporting(0);

$user=$_POST["username"];
$password=$_POST["pass"];

if(isset($user))
{
    
require 'incs/db_connect.php';

if(!(isset($_SESSION))) session_start();

if(isset($_SESSION["userid"]))
    {
        die("Please logout first!<br/> Go to <a href='profile.php'>Profile Page</a> page and click on Logout button.");
    }

$user = mysqli_real_escape_string($dbh, $user);
$password = mysqli_real_escape_string($dbh, $password);

$e_user=mysqli_query($dbh, "SELECT user FROM users");

$i=0;

while($row = mysqli_fetch_array($e_user, MYSQL_ASSOC))
{
    if($row[user]===$user)
        $i=1;
}

if($i==0)
    die('Username invalid!');

$normal_p=mysqli_query($dbh, "SELECT pass FROM users WHERE user='$user'") or die(mysql_error());
$apass=mysqli_fetch_array($normal_p);

if ($password==$apass[pass])
    {
        echo "Login successful! <br/> Redirecting to User area in 2 seconds!<br/>";
        $_SESSION["userid"] = $user;
        $_SESSION["start"] = time();
        echo "<meta http-equiv='refresh' content='2;profile.php' />";
    }
else
    {
        echo "Username/password incorrect!";
    }
}

?>
<a href='profile.php'>Profile Page</a><br/>

</body> 
</html>
