<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2020 Btiteam.org
*/
global $smarty, $STYLEPATH;

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
$smarty->assign('is_guest', (!user::$current));
$smarty->assign('is_guest1', (user::$current['uid'] == 1));
$smarty->assign('cur_username', user::$current['prefixcolor'] . user::$current['username'] . user::$current['suffixcolor']);
$smarty->assign('logout_salt', md5("R45eOMs15mNd3yV" . user::$current['username']));
$smarty->assign('view_torrents', (user::$current['view_torrents'] == 'yes'));
$smarty->assign('can_upload', (user::$current['can_upload'] == 'yes'));
$smarty->assign('view_users', (user::$current['view_users'] == 'yes'));
$smarty->assign('view_news', (user::$current['view_news'] == 'yes'));

$view_forum = (user::$current['view_forum'] == 'yes');
$smarty->assign('view_forum', (user::$current['view_forum'] == 'yes'));

#Draw...
if (user::$current['style'] == 1) {
    $main_menu = $smarty->display($STYLEPATH . '/tpl/header.tpl', $return_string = true);
    echo $main_menu;
} else {
    $main_menu = $smarty->display($STYLEPATH . '/tpl/main_menu.tpl', $return_string = true);
    echo $main_menu;
}

?>
