<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Request Details');

$id = (int)$_GET['id'];

$res = $db->query("SELECT * FROM requests WHERE id = " . $id) or sqlerr();
$num = $res->fetch_array(MYSQLI_BOTH);

$s = security::html_safe($num['request']);

block_begin('Request: ' . $s);

$url = 'reqedit.php?id=' . $id;

if (isset($_GET['returnto'])) {
    $addthis = '&amp;returnto=' . urlencode($_GET['returnto']);
    $url .= $addthis;
    $keepget .= $addthis;
}

$editlink = "a href='" . $url . "'";

#Lang...
$smarty->assign('lang_reqdetails',
                 array('request' => REQUEST,
                       'info' => INFO,
                       'added' => ADDED,
                       'vote_for' => VOTE_FOR_THIS,
                       'vote' => Vote,
                       'send' => SEND,
                       'add_request' => ADD_REQUESTS
                      )
               );

#Vars...
$smarty->assign('request', security::html_Safe($num['request']));
$smarty->assign('edit_link', $editlink);
$smarty->assign('description', format_comment(unesc($num['descr'])));
$smarty->assign('added', unesc($num['added']));

#If's...
$smarty->assign('can_edit', (user::$current['uid'] == $num['userid'] || user::$current['edit_torrents'] == 'yes'));
$smarty->assign('has_description', (!$num['desc']));
$smarty->assign('is_filled', ($num['filled'] == NULL));
$smarty->assign('can_upload', (user::$current['can_upload'] == 'yes'));

$cres = $db->query("SELECT username FROM users WHERE id = " . (int)$num['userid']);
if ($cres->num_rows == 1) {
    $carr = $cres->fetch_assoc();
    $username = security::html_safe($carr['username']);
}

#Vars 2...
$smarty->assign('userid', (int)$num['userid']);
$smarty->assign('username', $username);
$smarty->assign('id', $id);

$smarty->display($STYLEPATH . '/tpl/tracker/request_details.tpl');

block_end();
stdfoot();
die;

?>
