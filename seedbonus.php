<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
// SeedBonus Mod by CobraCRK   -   original ideea by TvRecall...
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('Seed Bonus');

if (user::$current['view_torrents'] == 'no') {
    err_msg(ERROR, NOT_AUTH_VIEW_NEWS);
    stdfoot();
    exit;
} else {
    block_begin('Seed Bonus');
	
    $r = $db->query('SELECT seedbonus FROM users WHERE id = ' . user::$current['uid']);
    $cc = mysqli_result($r, 0, 'seedbonus');

    $smarty->assign('language_info1', BONUS_INFO1);
    $smarty->assign('language_info2', BONUS_INFO2);
    $smarty->assign('language_option', OPTION);
    $smarty->assign('language_about', WHAT_ABOUT);
    $smarty->assign('language_points', POINTS);
    $smarty->assign('language_exchange', EXCHANGE);
    $smarty->assign('language_desc', BONUS_DESC);
    $smarty->assign('language_info3', BONUS_INFO3);

    $smarty->assign('points_cc', $cc);
	
    $uid = user::$current['uid'];
    $r = $db->query('SELECT * FROM users WHERE id = ' . $uid);
    $c = mysqli_result($r, 0, 'seedbonus');

    $smarty->assign('c', $c);
	
    $r = $db->query('SELECT * FROM bonus');
    while ($row = $r->fetch_array(MYSQLI_BOTH)) {
           $row['id'] = (int)$row['id'];
           $row['name'] = security::html_safe($row['name']);
           $row['gb'] = (int)$row['gb'];
           $row['points'] = (int)$row['points'];

           $seedbonus[] = $row;
    }
    $smarty->assign('show_seedbonus', $seedbonus);
    unset($seedbonus);
	
    $smarty->display($STYLEPATH . '/tpl/tracker/seedbonus.tpl');

    block_end();
}

stdfoot();

?>
