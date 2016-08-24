<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Request Details');

$id = (int)$_GET['id'];

$res = $db->query("SELECT * FROM requests WHERE id = " . $id) or sqlerr();
$num = $res->fetch_array(MYSQLI_BOTH);

$s = security::html_safe($num['request']);

block_begin('Request: ' . $s);

print("<center><table width='550' border='0' cellspacing='0' cellpadding='3'>\n");

//Edit request by RippeR change by miskotes
$url = 'reqedit.php?id=' . $id;
if (isset($_GET['returnto'])) {
    $addthis = '&amp;returnto=' . urlencode($_GET['returnto']);
    $url .= $addthis;
    $keepget .= $addthis;
}
$editlink = "a href='" . $url . "'";

print("<table class='lista' align='center' width='550' cellspacing='2' cellpadding='0'>\n");
print("<br><tr><td align='left' class='header'>" . REQUEST . ":</td><td class='lista' width='70%' align='left'>" . security::html_Safe($num['request']));

if (user::$current['uid'] == $num['userid'] || user::$current['edit_torrents'] == 'yes') {
    print("&nbsp;&nbsp;&nbsp;<" . $editlink . "><b>[Edit]</b></a></td></tr>");
} else {
}

print("</td></tr>");

if ($num['desc']) {
    print("<tr><td align='left' class='header'>" . INFO . ":</td><td class='lista' width='70%' align='left'>" . format_comment(unesc($num['descr'])) . "</td></tr>");
}
print("<tr><td align='left' class='header'>" . ADDED  . ":</td><td class='lista' width='70%' align='left'>" . unesc($num['added']) . "</td></tr>");

$cres = $db->query("SELECT username FROM users WHERE id = " . (int)$num['userid']);
if ($cres->num_rows == 1) {
    $carr = $cres->fetch_assoc();
    $username = security::html_safe($carr['username']);
}

print("<tr><td align='left' class='header'>Added By:</td><td class='lista' align='left'><a href='userdetails.php?id=" . (int)$num['userid'] . "'>" . $username . "</td></tr>");

if ($num['filled'] == NULL) {
    print("<tr><td align='left' class='header'>" . VOTE_FOR_THIS . "</td><td class='lista' width='50%' align='left'><a href='addrequest.php?id=" . $id . "'><b>" . Vote . "</b></a></td></tr>");

    if (user::$current['can_upload'] == 'yes') {
	print("<tr><td class='header' align='left' width='30%'>How To Fill the Request?</td><td class='lista' align='left' width='70%'>Type full torrent URL, i.e. http://www.mysite.com/details.php?id=1a750aff2e92... (you can only copy/paste from another window) or modify existing URL of torrent ID...</td></tr>");
	print("<tr><td class='lista' align='center' width='100%' colspan='2'><form method='get' action='reqfilled.php'>");
	print("<input type='text' size='80' name='filledurl' value='TYPE-DIRECT-TORRENT-URL-HERE'><br><input type='submit' value='" . SEND . "'>");
	print("<input type='hidden' value='" . $id . "' name='requestid'>");
	print("</form>");
	print("<hr><form method='get' action='requests.php#add'><input type='submit' value='" . ADD_REQUESTS . "'></form></td></tr>");
    }
}
else
    print("<tr><td class='lista' align='center' width='100%' colspan='2'><form method='get' action='requests.php#add'><input type='submit' value='" . ADD_REQUESTS . "'></form></td></tr>");

print("</table>");

block_end();
stdfoot();
die;

?>
