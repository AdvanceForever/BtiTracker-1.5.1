<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Fill Request');

if (user::$current['can_upload'] == 'no') {
    // do nothing
} else {
   begin_frame('Request Filled !');

   $filledurl = $db->real_escape_string($_GET['filledurl']);
   $requestid = (int)$_GET['requestid'];
   $filldate =  date('Y-m-d H:i:s');

   $res = $db->query("SELECT users.username, requests.userid, requests.request FROM requests INNER JOIN users ON requests.userid = users.id WHERE requests.id = " . $requestid) or sqlerr();
   $arr = $res->fetch_assoc();

   $res2 = $db->query("SELECT username FROM users WHERE id = " . user::$current['uid']) or sqlerr();
   $arr2 = $res2->fetch_assoc();


   $msg = REQUEST . ": [url=$BASEURL/reqdetails.php?id=" . $requestid . "][b]" . $arr['request'] . "[/b][/url], is filled by [url=$BASEURL/userdetails.php?id=" . user::$current['uid'] . "][b]" . $arr2['username'] . "[/b][/url].

   Torrent can be downloaded from the following link:
   [url=" . $filledurl. "][b]" . $filledurl. "[/b][/url]

   Do not forget to add credits to the uploader.
   If for some reason thi is not what you want, please reset this by clicking [url=$BASEURL/reqreset.php?requestid=" . $requestid . "][b]THIS!!![/b][/url].

   [b]DO NOT[/b] click the link unless you are sure you want it.";
   $subject = "Your Torrent Request !";

   $db->query("UPDATE requests SET filled = '" . $filledurl ."', fulfilled = '" . $filldate . "', filledby = " . user::$current['uid'] . " WHERE id = " . $requestid) or sqlerr();

   $db->query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES(" . user::$current['uid'] . ", " . (int)$arr['userid'] . ", UNIX_TIMESTAMP(), " . sqlesc($subject) . ", " . sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);

   $smarty->assign('request', security::html_safe($arr['request']));
   $smarty->assign('filled_url', $filledurl);
   $smarty->assign('userid', (int)$arr['userid']);
   $smarty->assign('username', security::html_safe($arr['username']));
   $smarty->assign('request_id', $requestid);

   $smarty->display($STYLEPATH . '/tpl/tracker/request_filled.tpl');
}

block_end();
stdfoot();

?>
