<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
// SeedBonus Mod by CobraCRK   -   original ideea by TvRecall...
//cobracrk[at]yahoo.com
//www.extremeshare.org
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

$id = (int)$_GET['id'];

if (is_null($id) || !is_numeric($id) || user::$current['view_torrents'] == 'no') {
    standardheader('Not allowed');
    err_msg(ERROR, 'What the hell do you want?');
    stdfoot();
    exit;
}

$r = $db->query("SELECT * FROM bonus WHERE id = " . $id);
$p = mysqli_result($r, 0, 'points');
$t = mysqli_result($r, 0, 'traffic');
$uid = user::$current['uid'];
$r = $db->query("SELECT seedbonus FROM users WHERE id = " . $uid);
$u = mysqli_result($r, 0, 'seedbonus');
if ($u < $p) {
    standardheader('ERROR');
    err_msg(ERROR, "You don't have enough points");
    stdfoot();
    exit;
} else {
    @$db->query("UPDATE users SET uploaded = uploaded + " . (int)$t . ",seedbonus = seedbonus - " . $p . " WHERE id = " . $uid);
    MCached::del('main::user::toolbar::stats' . user::$current['uid']);
    header('Location: seedbonus.php');
}

?>
