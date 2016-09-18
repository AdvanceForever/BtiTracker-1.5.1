<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('Comments');

if (isset($_GET['do']))
    $do = security::html_safe($_GET['do']);
else
    $do = '';

if (isset($_GET['action']))
    $action = security::html_safe($_GET['action']);

if (user::$current['view_torrents'] == 'no') {
    err_msg(ERROR, NOT_ADMIN_CP_ACCESS);
    stdfoot();
    exit;
}

if ($do == 'comments' && $action == 'edit') {
    $id = (int)$_GET['id'];

    $subres = $db->query("SELECT * FROM comments WHERE id = '" . $id . "' ORDER BY added DESC");

    if ($subrow = $subres->fetch_array(MYSQLI_BOTH)) {
	    if ($subrow['user'] == user::$current['username'] || user::$current['mod_access'] == 'yes' || user::$current['edit_forum'] == 'yes' || user::$current['delete_torrents'] == 'yes') {
            block_begin(EDIT);

            $smarty->assign('id', (int)$subrow['id']);
            $smarty->assign('info_hash', security::html_safe($subrow['info_hash']));
            $smarty->assign('username', security::html_safe($subrow['user']));
            $smarty->assign('comment', textbbcode2('commentsedit', 'text', security::html_safe(unesc($subrow['text']))));

            $smarty->assign('lang_username', USER_NAME);
            $smarty->assign('lang_comment', COMMENT_1);
            $smarty->assign('lang_confirm', FRM_CONFIRM);
            $smarty->assign('lang_cancel', FRM_CANCEL);

            $smarty->display($STYLEPATH . '/tpl/torrent/edit_comment.tpl');

            block_end();
	    } else {
	        block_begin('Access Denied !');
	        err_msg(ERROR, 'You do not have permission to access this page !');
	        block_end();
	        stdfoot();
	        exit();
        }		
    }			   
} elseif ($do == 'comments' && $action == 'quote') {
    $id = (int)$_GET['id'];
    $quote = (int)$_GET['id'];

    $subres = $db->query("SELECT * FROM comments WHERE id = '" . $id . "' ORDER BY added DESC");

    if ($subrow = $subres->fetch_array(MYSQLI_BOTH)) {			
        block_begin(QUOTE);

        $smarty->assign('quote_id', (int)$subrow['id']);
        $smarty->assign('quote_info_hash', security::html_safe($subrow['info_hash']));
        $smarty->assign('quote_username', user::$current['username']);
        $smarty->assign('quote_comment', textbbcode2('comment', 'text', ($quote ? (('[quote=' . security::html_safe($subrow['user']) . ']' . security::html_safe(unesc($subrow['text'])) . '[/quote]')) : '')));

        $smarty->assign('langq_username', USER_NAME);
        $smarty->assign('langq_comment', COMMENT_1);
        $smarty->assign('langq_confirm', FRM_CONFIRM);
        $smarty->assign('langq_cancel', FRM_CANCEL);

        $smarty->display($STYLEPATH . '/tpl/torrent/quote_comment.tpl');

        block_end();
    }
} elseif ($do == 'comments' && $action == 'write') {
    if ($_POST['write'] == FRM_CONFIRM) {
        $id = intval($_GET['id']);
        $text = sqlesc($_POST['text']);

        $db->query("UPDATE comments SET text = " . $text . ", editedby = " . user::$current['uid'] . ", editedat = UNIX_TIMESTAMP() WHERE id = " . $id) or sqlerr();

        print(COMMENT_MOD);
    }
    redirect('details.php?id=' . StripSlashes($_POST['info_hash']) . '#comments');
} elseif ($do == 'comments' && $action == 'confirm') {
    if ($_POST['confirm'] == FRM_CONFIRM) {
        $comment = $db->real_escape_string(addslashes($_POST['text']));
        $user = user::$current['username'];

        if ($user == '')
            $user = 'Guest';

        $userid = user::$current['uid'];

        if ($userid == '')
            $userid = 0;

        @$db->query("INSERT INTO comments (added, text, ori_text, user, info_hash) VALUES (NOW(), '" . $comment . "', '" . $comment . "', '" . $user . "', '" . $db->real_escape_string(StripSlashes($_POST['info_hash'])) . "')");

        redirect('details.php?id=' . stripslashes($_POST['info_hash']) . '#comments');
    } else
        redirect('details.php?id=' . stripslashes($_POST['info_hash']) . '#comments');
}

stdfoot();

?>
