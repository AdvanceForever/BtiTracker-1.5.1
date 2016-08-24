<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

if (file_exists('install.me')) {
    if (dirname(security::esc_url($_SERVER['PHP_SELF'])) == '/' || dirname(security::esc_url($_SERVER['PHP_SELF'])) == '\\')
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/install/');
    else
        header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname(security::esc_url($_SERVER['PHP_SELF'])) . '/install/');
    exit;
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');
require_once(INCL_PATH . 'blocks.php');

dbconn(true);

if (user::$current['id'] == 1) {
    if ($_SERVER['REQUEST_URI'] == '/' || '/index.php') {
        redirect('login.php');
    } elseif ($_SERVER['REQUEST_URI'] == '/login.php') {
        redirect('login.php');
    }
}

if (user::$current['id']) {
    standardheader('Index', true, 0);

    center_menu();

    stdfoot();
}

?>
