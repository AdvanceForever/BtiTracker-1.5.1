<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
global $tpl, $STYLEPATH;

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
$is_guest = (!user::$current);
$tpl->assign('is_guest', $is_guest);

$is_guest1 = (user::$current['uid'] == 1);
$tpl->assign('is_guest1', $is_guest1);

$cur_username = user::$current['username'];
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

#Draw...
if (user::$current['style'] == 1) {
    $main_menu = $tpl->draw($STYLEPATH . '/tpl/header', $return_string = true);
    echo $main_menu;
} else {
    $main_menu = $tpl->draw($STYLEPATH . '/tpl/main_menu', $return_string = true);
    echo $main_menu;
}

?>
