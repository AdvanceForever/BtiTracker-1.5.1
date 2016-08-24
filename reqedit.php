<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Edit Request');

$id2 = (int)$_GET['id'];

$res = $db->query("SELECT * FROM requests WHERE id = " . $id2);
$row = $res->fetch_array(MYSQLI_BOTH);

if (user::$current['uid'] == $row['userid'] || user::$current['edit_torrents'] == 'yes') {
    if (!$row)
        die();

    block_begin('Edit Request: ' . security::html_safe($row['request']));

    $where = "WHERE userid = " . user::$current['id'];
    $res2 = $db->query("SELECT * FROM requests " . $where) or sqlerr();
    $num2 = $res2->num_rows;

    print("<form name='edit' method='post' action='takereqedit.php'><a name='edit' id='edit'></a>");
    print("<table class='lista' align='center' width='550' cellspacing='2' cellpadding='0'>\n");
    print("<br><tr><td align='left' class='header'>". TORRENT_FILE ."</td><td class='lista' align='left'><input type='text' size='60' name='requesttitle' value='" . security::html_safe($row['request']) . "'></td></tr>");

    print("<tr><td align='center' class='header'>Category:</td><td align='left' class='lista'>\n");

    $s = "<select name='category'>\n";

    $cats = Cached::genrelist();
    foreach ($cats as $subrow) {
             $s .= "<option value='" . (int)$subrow['id'] . "'";
             if ($subrow['id'] == $row['cat'])
                 $s .= " selected='selected'";
             $s .= ">" . security::html_safe($subrow['name']) . "</option>\n";
    }

    $s .= "</select>\n";
    print($s . "</td></tr>\n");

    print("<tr><td align='left' class='header'>" . DESCRIPTION . "</td><td align='left' class='lista'>");
    print(textbbcode('edit', 'description', unesc($row['descr'])));
    print("</td></tr>");
    print("<input type='hidden' name='id' value='" . $id2 . "'>\n");
    print("<tr><td colspan='2' align='center' class='lista'><input type='submit' value='Submit'>\n");
    print("</form>\n");
    print("</table>\n");

    block_end();
    stdfoot();
} else {
    block_begin("You're not the owner!");
    err_msg(ERROR, 'Or you are not authorized or this is a bug, report it pls...');
    print("<br />");
    block_end();
    stdfoot();
    exit;
}

?>
