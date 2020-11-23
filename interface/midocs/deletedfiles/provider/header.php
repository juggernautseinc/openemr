<?php
session_start();
$username = $_GET['username'];
$path =  "../../main/session/providers/$username.txt";
$file = $path;
if(file_exists($file)){
    $contents = file_get_contents($file);
    session_decode($contents);
}

if(!isset($_SESSION['username']) || !isset($_GET['username']) || !$_GET['username'] == $_SESSION['username'] || !file_exists($file)){
    header('Location: ../../login/login.php?site=default');
}

