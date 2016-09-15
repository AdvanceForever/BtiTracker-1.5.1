<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

$requestid = (int)$_GET['requestid'];

$res2 = $db->query("SELECT COUNT(addedrequests.id) FROM addedrequests INNER JOIN users ON addedrequests.userid = users.id INNER JOIN requests ON addedrequests.requestid = requests.id WHERE addedrequests.requestid = " . $requestid) or die($db->error);
$row = $res2->fetch_array(MYSQLI_BOTH);

$count = (int)$row[0];

$perpage = 50;

list($pagertop, $limit) = misc::pager($perpage, $count, security::esc_url($_SERVER['PHP_SELF']) . '?');

$res = $db->query("SELECT users.id AS userid, users.username, users.downloaded, users.uploaded, requests.id AS requestid, requests.request FROM addedrequests INNER JOIN users ON addedrequests.userid = users.id INNER JOIN requests ON addedrequests.requestid = requests.id WHERE addedrequests.requestid = " . $requestid . " " . $limit) or sqlerr(__FILE__, __LINE__);

standardheader('Votes');

$res2 = $db->query("SELECT request FROM requests WHERE id = " . $requestid);
$arr2 = $res2->fetch_assoc();

block_begin(VOTES . ": <a href='reqdetails.php?id=" . $requestid . "'>" . security::html_safe($arr2['request']) . "</a>");

print("<p align='center'>" . VOTE_FOR_THIS . " <a href='addrequest.php?id=" . $requestid . "'><b>" . REQUEST . "</b></a></p>");

if ($res->num_rows == 0)
    print("<p align='center'><b>" . NOTHING_FOUND . "</b></p>\n");
else {
    print("<center><table width=99% class=lista align=center cellpadding=3>\n");
    print("<tr><td class='header'>" . USER_NAME . "</td><td class='header' align='left'>" . UPLOADED . "</td><td class='header' align='left'>" . DOWNLOADED . "</td>"."<td class='header' align='left'>" . RATIO . "</td>\n");

    while ($arr = $res->fetch_assoc()) {
       if ($arr['downloaded'] > 0) {
           $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
       } else
           if ($arr['uploaded'] > 0)
               $ratio = '&infin;';
           else
               $ratio = '---';

           $uploaded = misc::makesize((float)$arr['uploaded']);
           $downloaded = misc::makesize((float)$arr['downloaded']);
           $joindate = $arr['added'] . ' (' . get_elapsed_time(sql_timestamp_to_unix_timestamp($arr['added'])) . ' ago)';

           if ($arr['enabled'] == 'no')
               $enabled = "<font color='red'>No</font>";
           else
               $enabled = "<font color='green'>Yes</font>";

           print("<tr><td class='lista'><a href='userdetails.php?id=" . (int)$arr['userid'] . "'><b>" . security::html_safe($arr['username']) . "</b></a></td><td align='left' class='lista'>" . $uploaded . "</td><td align='left' class='lista'>" . $downloaded . "</td><td align='left' class='lista'>" . $ratio . "</td></tr>\n");
    }
    print("</table></center><br />\n");
}

block_end();
stdfoot();

?>
