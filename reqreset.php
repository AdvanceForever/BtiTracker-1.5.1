<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Reset Request');
block_begin('Reset');

$requestid = (int)$_GET['requestid'];

$res = $db->query("SELECT userid, filledby FROM requests WHERE id = " . $requestid) or sqlerr();
$arr = $res->fetch_assoc();

if ((user::$current['uid'] == $arr['userid']) || (user::$current['uid'] == $arr['filledby'])) {
    @$db->query("UPDATE requests SET filled = '', filledby = 0 WHERE id = " . $requestid) or sqlerr();
 
    print("Request " . security::html_safe($arr['request']) . " successfuly reseted.");
}
else
    print("Sorry, cannot reset a request when you are not the Owner !");

block_end();
stdfoot();

?>
