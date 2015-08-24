<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

global $db;

if (!user::$current || user::$current["view_torrents"] == "no") {
    // do nothing
} else {
    global $SITENAME;
    
    block_begin(BLOCK_INFO);
    
    $torrents = MCached::get('tracker::info::total::torrents');
    if ($torrents === MCached::NO_RESULT) {
        $res = $db->query("SELECT COUNT(*) AS tot FROM namemap");
        if ($res) {
            $row      = $res->fetch_array(MYSQLI_BOTH);
            $torrents = (int)$row["tot"];
        } else
            $torrents = 0;
	
        MCached::add('tracker::info::total::torrents', $torrents, 300);
    }
    
    $users = MCached::get('tracker::info::total::users');
    if ($users === MCached::NO_RESULT) {
        $res = $db->query("SELECT COUNT(*) AS tot FROM users WHERE id > 1");
        if ($res) {
           $row   = $res->fetch_array(MYSQLI_BOTH);
           $users = (int)$row["tot"];
        } else
           $users = 0;
	
        MCached::add('tracker::info::total::users', $users, 300);
    }
    
    $res = $db->query("SELECT SUM(seeds) AS seeds, SUM(leechers) AS leechs FROM summary");
    if ($res) {
        $row      = $res->fetch_array(MYSQLI_BOTH);
        $seeds    = 0 + (int)$row["seeds"];
        $leechers = 0 + (int)$row["leechs"];
    } else {
        $seeds    = 0;
        $leechers = 0;
    }
    
    if ($leechers > 0)
        $percent = number_format(($seeds / $leechers) * 100, 0);
    else
        $percent = number_format($seeds * 100, 0);
    
    $peers = $seeds + $leechers;
	
    $row = MCached::get('tracker::info::total::traffic');
    if ($row === MCached::NO_RESULT) {
        $res = $db->query("SELECT SUM(downloaded) AS dled, SUM(uploaded) AS upld FROM users");
        $row = $res->fetch_array(MYSQLI_BOTH);
        MCached::add('tracker::info::total::traffic', $row, 300);
    }
	
    $dled    = 0 + (int)$row["dled"];
    $upld    = 0 + (int)$row["upld"];
    $traffic = misc::makesize($dled + $upld);
	
    
    print("<tr><td class='blocklist' align='center'>\n");
    print("<table width='100%' cellspacing='2' cellpading='2'>\n");
    print("<tr>\n<td colspan='2' align='center'><u>" . unesc($SITENAME) . "</u></td></tr>\n");
    print("<tr><td align='left'>" . MEMBERS . ":</td><td align='right'>" . $users . "</td></tr>\n");
    print("<tr><td align='left'>" . TORRENTS . ":</td><td align='right'>" . $torrents . "</td></tr>\n");
    print("<tr><td align='left'>" . SEEDERS . ":</td><td align='right'>" . $seeds . "</td></tr>\n");
    print("<tr><td align='left'>" . LEECHERS . ":</td><td align='right'>" . $leechers . "</td></tr>\n");
    print("<tr><td align='left'>" . PEERS . ":</td><td align='right'>" . $peers . "</td></tr>\n");
    print("<tr><td align='left'>" . SEEDERS . "/" . LEECHERS . ":</td><td align='right'>" . $percent . "%</td></tr>\n");
    print("<tr><td align='left'>" . TRAFFIC . ":</td><td align='right'>" . $traffic . "</td></tr>\n");
    print("</table>\n</td></tr>");
    block_end();

} // end if user can view

?>
