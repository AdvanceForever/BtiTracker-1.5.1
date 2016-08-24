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
    echo "<br><p>You've already voted for this request, only 1 vote for each request is allowed</p><p>Back to <a href=viewrequests.php><b>Requests</b></a></p><br><br>";
} else {
    $db->query("UPDATE requests SET hits = hits + 1 WHERE id = " . $requestid) or sqlerr();
    @$db->query("INSERT INTO addedrequests VALUES(0, " . $requestid . ", " . $userid . ")") or sqlerr();

    print("<br><p>Successfully voted for request " . $requestid . "</p><p>Back to <a href=viewrequests.php><b>Requests</b></a></p><br><br>");
}

end_frame();
stdfoot();

?>
