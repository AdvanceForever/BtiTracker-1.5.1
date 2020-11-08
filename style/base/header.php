<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2020 Btiteam.org
*/
require_once(INCL_PATH . 'functions.php');
require_once(INCL_PATH . 'blocks.php');

global $smarty, $db, $STYLEPATH;

$key = 'main::user::toolbar::stats' . user::$current['uid'];
$rowuser = MCached::get($key);
if ($rowuser === MCached::NO_RESULT) {
    $resuser = $db->query("SELECT uploaded, downloaded, seedbonus FROM users WHERE id = " . user::$current['uid']);
    $rowuser = $resuser->fetch_array(MYSQLI_BOTH);
    MCached::add($key, $rowuser, 1800);
}

#Hit and Run...
if ($GLOBALS['hit_and_run'] == 'yes') {
    $chnr = MCached::get('hit::and::runs::' . user::$current['uid']);
    if ($chnr === MCached::NO_RESULT) {
        $hnr = $db->query("SELECT COUNT(*) FROM history WHERE hit = 'yes' AND uid = " . user::$current['uid']);
        $chnr = $hnr->fetch_row();
        MCached::add('hit::and::runs::' . user::$current['uid'], $chnr, 1800);
    }
}

#PM...
$resmail = $db->query("SELECT COUNT(*) FROM messages WHERE readed = 'no' AND receiver = " . user::$current['uid']);
if ($resmail && $resmail->num_rows > 0) {
    $mail = $resmail->fetch_row();
    if ($mail[0] > 0)
        $pm .= "<a href='usercp.php?uid=" . user::$current["uid"] . "&do=pm&action=list'><font color='#614051'>Messages</font></a><span id='new-message'> <b>" . (int)$mail[0] . "</b></span>";
    else
        $pm .= "<a href='usercp.php?uid=" . user::$current["uid"] . "&do=pm&action=list'><font color='#614051'>Messages</font></a>";
} else
    $pm .= "<a href='usercp.php?uid=" . user::$current["uid"] . "&do=pm&action=list'><font color='#614051'>Messages</font></a>";

#User Menu...
$smarty->assign('user_pm', $pm);
$smarty->assign('user_uploaded', misc::makesize((float)$rowuser['uploaded']));
$smarty->assign('user_downloaded', misc::makesize((float)$rowuser['downloaded']));
$smarty->assign('user_bonus', unesc($rowuser['seedbonus']));
$smarty->assign('user_ratio', ((int)$rowuser['downloaded'] > 0 ? number_format((float)$rowuser['uploaded'] / (float)$rowuser['downloaded'], 2) : '&infin;'));

#Main Menu...
#Lang...
$smarty->assign('lang_welcome_guest', WELCOME . ' ' . GUEST);
$smarty->assign('lang_login', LOGIN);
$smarty->assign('lang_welcome', WELCOME);
$smarty->assign('lang_welcome_back', WELCOME_BACK);
$smarty->assign('lang_logout', LOGOUT);
$smarty->assign('lang_index', MNU_INDEX);
$smarty->assign('lang_torrents', MNU_TORRENT);
$smarty->assign('lang_stats', MNU_STATS);
$smarty->assign('lang_upload', MNU_UPLOAD);
$smarty->assign('lang_members', MNU_MEMBERS);
$smarty->assign('lang_news', MNU_NEWS);
$smarty->assign('lang_forum', MNU_FORUM);

#Vars...
$smarty->assign('is_registered', (user::$current['uid'] > 1));
$smarty->assign('hit_and_runs', (int)$chnr[0]);
$smarty->assign('admincp_access', (user::$current['admin_access'] == 'yes'));
$smarty->assign('seedbonus_enabled', ($GLOBALS['seed_bonus'] == 'yes'));
$smarty->assign('hitandrun_enabled', ($GLOBALS['hit_and_run'] == 'yes'));
$smarty->assign('requests_enabled', ($GLOBALS['requests'] == 'yes'));
$smarty->assign('cur_id', user::$current['uid']);
$smarty->assign('random', user::$current['random']);
$smarty->assign('is_guest', (!user::$current));
$smarty->assign('is_guest1', (user::$current['uid'] == 1));
$smarty->assign('cur_username', user::$current['prefixcolor'] . user::$current['username'] . user::$current['suffixcolor'] . Warn_disabled(user::$current['uid']));
$smarty->assign('logout_salt', md5("R45eOMs15mNd3yV" . user::$current['username']));
$smarty->assign('view_torrents', (user::$current['view_torrents'] == 'yes'));
$smarty->assign('can_upload', (user::$current['can_upload'] == 'yes'));
$smarty->assign('view_users', (user::$current['view_users'] == 'yes'));
$smarty->assign('view_news', (user::$current['view_news'] == 'yes'));
$smarty->assign('view_forum', (user::$current['view_forum'] == 'yes'));

$smarty->display($STYLEPATH . '/tpl/header.tpl');

//User Warning System Hack Start
if ($GLOBALS['warn_system'] == 'yes') {
    $rowuser = MCached::get('disabled::' . user::$current['uid']);
    if ($rowuser === MCached::NO_RESULT) {
        $resuser = $db->query("SELECT disabled FROM users WHERE id = " . user::$current['uid']);
        $rowuser = $resuser->fetch_array(MYSQLI_BOTH);
        MCached::add('disabled::' . user::$current['uid'], $rowuser, 120);
    }

    if ($rowuser['disabled'] == 'yes') {
        $logout_salt = md5('R45eOMs15mNd3yV' . user::$current['username']);
        redirect('logout.php?check_hash=' . $logout_salt); #Kick the evil person from the Tracker...
    } else {
        #Do Nothing...
    }
}
//User Warning System Hack Stop

?>
