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

    $smarty->assign('request', security::html_safe($row['request']));

    $s = "<select name='category'>\n";

    $cats = Cached::genrelist();
    foreach ($cats as $subrow) {
             $s .= "<option value='" . (int)$subrow['id'] . "'";
             if ($subrow['id'] == $row['cat'])
                 $s .= " selected='selected'";
             $s .= ">" . security::html_safe($subrow['name']) . "</option>\n";
    }

    $s .= "</select>\n";

    $smarty->assign('category', $s);
    $smarty->assign('description', textbbcode2('edit', 'description', unesc($row['descr'])));
    $smarty->assign('id2', $id2);

    $smarty->assign('lang_torrent_file', TORRENT_FILE);
    $smarty->assign('lang_description', DESCRIPTION);

    $smarty->display($STYLEPATH . '/tpl/tracker/request_edit.tpl');

    block_end();
    stdfoot();
} else {
    block_begin("You're not the owner!");
    err_msg(ERROR, 'Or you are not authorized or this is a bug, report it pls...');
    block_end();
    stdfoot();
    exit;
}

?>
