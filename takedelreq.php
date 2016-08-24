<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Delete');

if (!user::$current || user::$current['edit_torrents'] == 'no') {
    // do nothing
} else {
    block_begin('Deleted');

    if (empty($_POST['delreq'])){
        print("<table border='0' width='100%' cellspacing='2' cellpadding='0'><tr><td class='lista' align='center'>You must select at least one request in order to delete.</td></tr></table>");
        block_end();
        stdfoot(false);
        die;
    }

    $do = "DELETE FROM requests WHERE id IN (" . implode(", ", $_POST['delreq']) . ")";
    $do2 = "DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST['delreq']) . ")";
    $res2 = $db->query($do2);
    $res = $db->query($do);

    print("<table border='0' width='100%' cellpadding='0'><tr><td class='lista' align='center'>Go Back to <a href='viewrequests.php'><b>REQUESTS</a></table>");

    block_end();
}

stdfoot();

?>
