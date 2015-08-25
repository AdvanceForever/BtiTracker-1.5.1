<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

require_once(INCL_PATH . 'functions.php');
require_once(INCL_PATH . 'blocks.php');

global $tpl, $db, $STYLEPATH;

$key = 'main::user::toolbar::stats' . user::$current['uid'];
$rowuser = MCached::get($key);
if ($rowuser === MCached::NO_RESULT) {
    $resuser = $db->query("SELECT uploaded, downloaded FROM users WHERE id = " . user::$current['uid']);
    $rowuser = $resuser->fetch_array(MYSQLI_BOTH);
    MCached::add($key, $rowuser, 1800);
}

#PM...
$resmail = $db->query("SELECT COUNT(*) FROM messages WHERE readed = 'no' AND receiver = " . user::$current['uid']);
if ($resmail && $resmail->num_rows > 0) {
    $mail = $resmail->fetch_row();
    if ($mail[0] > 0)
        $pm .= "<a href='usercp.php?uid=" . user::$current["uid"] . "&do=pm&action=list'><img src='images/pm_received.png' title='You have " . (int)$mail[0] . " new Message(s)' width='24' height='24'></a><span id='new-message'> <b>" . (int)$mail[0] . "</b></span>";
    else
        $pm .= "<a href='usercp.php?uid=" . user::$current["uid"] . "&do=pm&action=list'><img src='images/pm.png' title='Messages' width='24' height='24'></a>";
} else
    $pm .= "<a href='usercp.php?uid=" . user::$current["uid"] . "&do=pm&action=list'><img src='images/pm.png' title='Messages' width='24' height='24'></a>";

#User Menu...
$user_pm = $pm;
$tpl->assign('user_pm', $user_pm);

$user_uploaded = misc::makesize((float)$rowuser['uploaded']);
$tpl->assign('user_uploaded', $user_uploaded);

$user_downloaded = misc::makesize((float)$rowuser['downloaded']);
$tpl->assign('user_downloaded', $user_downloaded);

$user_ratio = ((int)$rowuser['downloaded'] > 0 ? number_format((float)$rowuser['uploaded'] / (float)$rowuser['downloaded'], 2) : '&infin;');
$tpl->assign('user_ratio', $user_ratio);

#Main Menu...
#Lang...
$lang_welcome_guest = WELCOME . ' ' . GUEST;
$tpl->assign('lang_welcome_guest', $lang_welcome_guest);

$lang_login = LOGIN;
$tpl->assign('lang_login', $lang_login);

$lang_welcome = WELCOME;
$tpl->assign('lang_welcome', $lang_welcome);

$lang_welcome_back = WELCOME_BACK;
$tpl->assign('lang_welcome_back', $lang_welcome_back);

$lang_logout = LOGOUT;
$tpl->assign('lang_logout', $lang_logout);

$lang_index = MNU_INDEX;
$tpl->assign('lang_index', $lang_index);

$lang_torrents = MNU_TORRENT;
$tpl->assign('lang_torrents', $lang_torrents);

$lang_stats = MNU_STATS;
$tpl->assign('lang_stats', $lang_stats);

$lang_upload = MNU_UPLOAD;
$tpl->assign('lang_upload', $lang_upload);

$lang_members = MNU_MEMBERS;
$tpl->assign('lang_members', $lang_members);

$lang_news = MNU_NEWS;
$tpl->assign('lang_news', $lang_news);

$lang_forum = MNU_FORUM;
$tpl->assign('lang_forum', $lang_forum);

#Vars...
$is_registered = (user::$current['uid'] > 1);
$tpl->assign('is_registered', $is_registered);

$admincp_access = (user::$current['admin_access'] == 'yes');
$tpl->assign('admincp_access', $admincp_access);

$cur_id = user::$current['uid'];
$tpl->assign('cur_id', $cur_id);

$random = user::$current['random'];
$tpl->assign('random', $random);

$is_guest = (!user::$current);
$tpl->assign('is_guest', $is_guest);

$is_guest1 = (user::$current['uid'] == 1);
$tpl->assign('is_guest1', $is_guest1);

$cur_username = user::$current['prefixcolor'] . user::$current['username'] . user::$current['suffixcolor'];
$tpl->assign('cur_username', $cur_username);

$logout_salt = md5("R45eOMs15mNd3yV" . user::$current['username']);
$tpl->assign('logout_salt', $logout_salt);

$view_torrents = (user::$current['view_torrents'] == 'yes');
$tpl->assign('view_torrents', $view_torrents);

$can_upload = (user::$current['can_upload'] == 'yes');
$tpl->assign('can_upload', $can_upload);

$view_users = (user::$current['view_users'] == 'yes');
$tpl->assign('view_users', $view_users);

$view_news = (user::$current['view_news'] == 'yes');
$tpl->assign('view_news', $view_news);

$view_forum = (user::$current['view_forum'] == 'yes');
$tpl->assign('view_forum', $view_forum);

$header = $tpl->draw($STYLEPATH . '/tpl/header', $return_string = true);
echo $header;

?>
