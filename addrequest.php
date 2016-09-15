<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Vote');
begin_frame(VOTES);

$requestid = (int)$_GET['id'];
$userid = user::$current['uid'];
$res = $db->query("SELECT * FROM addedrequests WHERE requestid = " . $requestid . " AND userid = " . $userid) or sqlerr();
$arr = $res->fetch_assoc();
$voted = $arr;

if ($voted) {
    $smarty->display($STYLEPATH . '/tpl/tracker/request_voted.tpl');
} else {
    $db->query("UPDATE requests SET hits = hits + 1 WHERE id = " . $requestid) or sqlerr();
    @$db->query("INSERT INTO addedrequests VALUES(0, " . $requestid . ", " . $userid . ")") or sqlerr();

    $smarty->assign('vrequest_id', $requestid);

    $smarty->display($STYLEPATH . '/tpl/tracker/vote_request.tpl');
}

end_frame();
stdfoot();

?>
