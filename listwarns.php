<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

if (isset($_GET["uid"]))
    $userid = max(0, (int)$_GET["uid"]);
else
    $userid = "Error, Bad ID";

$warnsql = $db->query("SELECT username FROM users WHERE id = '" . $userid . "'");

if (!$warnsql->num_rows) {
    standardheader("Invalid Warning List !");
    block_begin("Invalid Warning List !");
    err_msg(ERROR, "Invalid User !");
    exit();
}

$warnname = $warnsql->fetch_array(MYSQLI_BOTH);

standardheader(security::html_safe($warnname['username']) . "'s Warning List");

block_begin(security::html_safe($warnname['username']) . "'s Warning List");

$showsql = $db->query("SELECT warnings.*, username FROM warnings LEFT JOIN users ON warnings.addedby = users.id WHERE userid = " . $userid);

if (!user::$current || user::$current["admin_access"] == "yes" || user::$current["edit_users"] == "yes" || user::$current["uid"] == $userid) {
    print("<table border='0' width='100%' cellspacing='0' cellpadding='4'>");
    print("<tr><td class='header' align='center'>" . WARNED_ID . "</td><td class='header' align='center'>" . WARNED_DATE_ADDED . "</td><td class='header' align='center'>" . WARNED_EXPIRATION . "</td><td class='header' align='center'>" . WARNED_DURATION . "</td><td class='header' align='center'>" . WARNED_REASON . "</td><td class='header' align='center'>" . WARNED_BY . "</td><td class='header' align='center'>" . WARNED_ACTIVE . "</td></tr>");

    if (!$showsql->num_rows)
        print("<tr><td class='lista' align='center' colspan='7'>" . WARNED_USER_NOTWARNED . "</td></tr>");
    else {
        while ($arr = $showsql->fetch_array(MYSQLI_BOTH)) {
            if ($arr["warnedfor"] == 0)
                $duration = WARNED_UNLIMITED;
            elseif ($arr['warnedfor'] == 1)
                $duration = $arr['warnedfor'] . "" . WARNED_WEEK;
            else
                $duration = $arr['warnedfor'] . "" . WARNED_WEEKS;

            if ($arr["active"]=="no")
                $active = "<font color='green'>" . NO . "</font>";
            else
                $active = "<font color='red'>" . YES . "</font>";

            print("<tr><td class='lista' align='center'>" . (int)$arr['id'] . "</td><td class='lista' align='center'>" . security::html_safe($arr['added']) . "</td><td class='lista' align='center'>" . security::html_safe($arr['expires']) . "</td><td class='lista' align='center'>" . $duration . "</td><td class='lista' align='center'>" . security::html_safe(unesc($arr['reason'])) . "</td><td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['addedby'] . "'>" . security::html_safe($arr['username']) . "</a></td><td class='lista' align='center'>" . $active . "</td></tr>");
        }
    }

    print("<tr><td class='header' align='center' colspan='7'><a href='javascript: history.go(-1);'>" . BACK . "</a></td></tr>");
    print("</table>");
} else {
    err_msg(ERROR, ERR_NOT_AUTH);
}

block_end();
stdfoot(false);

?>
