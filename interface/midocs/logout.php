<?php
session_start();

if(isset($_GET['mode']) && $_GET['mode'] == "provider"){
    $username = $_GET['username'];
    $file = fopen("../main/session/providers/$username.txt","w");
    fwrite($file,"");
    fclose($file);
    unlink("../main/session/providers/$username.txt");
    session_destroy();
    header('Location: ../login/login.php?site=default');
}

if(isset($_GET['mode']) && $_GET['mode'] == "other"){
    $username = $_GET['username'];
    $file = fopen("../main/session/other/$username.txt","w");
    fwrite($file,"");
    fclose($file);
    unlink("../main/session/other/$username.txt");
    session_destroy();
    header('Location: ../login/login.php?site=default');
}
