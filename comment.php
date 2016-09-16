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

if (!user::$current || user::$current['uid'] == 1) {
    err_msg(ERROR, ONLY_REG_COMMENT);
    stdfoot();
    exit();
}

$comment = $db->real_escape_string($_POST['comment']);
$id = $db->real_escape_string($_GET['id']);

if (isset($_GET['cid']))
    $cid = intval($_GET['cid']);
else
    $cid = 0;

//Links block in comments by Yupy...
if ($GLOBALS['block_links'] == 'yes') {
    if (preg_match ("/href|https|url|http|www|\.ro|\.ru|\.hu|\.it|\.eu
                    |\.bg|\.gr|\.us|\.co.uk|\.uk|\.es|\.tv|\.tr|\.com.ro|\.be|\.de|\.gr|
                    \.me|\.in|\.com|\.pirate|\.co|\.cc|\.net|\.info|\.org|\.ua|\.org/i", $comment))
    //Give error 0_o
    stderr(ERROR, LINKS_BLOCKED);
}
//Links block in comments by Yupy... 

function comment_form() {
    global $comment, $id, $cid, $smarty, $STYLEPATH;

    block_begin(NEW_COMMENT);
    
    $comment = str_replace('\r\n', '\n', $comment);

    #Vars...
    $smarty->assign('id', $id);
    $smarty->assign('username', security::html_safe($_GET['usern']));
    $smarty->assign('comment', textbbcode2('comment', 'comment', security::html_safe(unesc($comment))));

    #Lang...
    $smarty->assign('lang_username', USER_NAME);
    $smarty->assign('lang_comment', COMMENT_1);
    $smarty->assign('lang_confirm', FRM_CONFIRM);
    $smarty->assign('lang_preview', FRM_PREVIEW);

    $smarty->display($STYLEPATH . '/tpl/torrent/comment.tpl');

    block_end();
}

if (isset($_GET['action'])) {
    if (user::$current['admin_access'] == 'yes' && $_GET['action'] == 'delete') {
        @$db->query("DELETE FROM comments WHERE id = " . $cid);
        MCached::del('torrent::comments::count::' . $_POST['info_hash']);
        redirect('details.php?id=' . $id . '#comments');
        exit;
    }
}

if (isset($_POST['info_hash'])) {
    if ($_POST['confirm'] == FRM_CONFIRM) {
        $comment = $db->real_escape_string(addslashes($_POST['comment']));
        $user = AddSlashes(user::$current['username']);

        if ($user == '')
	        $user = 'Anonymous';

        @$db->query("INSERT INTO comments (added, text, ori_text, user, info_hash) VALUES (NOW(), '" . $comment . "', '" . $comment . "', '" . $user . "', '" . $db->real_escape_string(StripSlashes($_POST['info_hash'])) . "')");
        MCached::del('torrent::comments::count::' . $_POST['info_hash']);
        redirect('details.php?id=' . StripSlashes($_POST['info_hash']) . '#comments');
    }
	
    # Comment preview by miskotes
    #############################
    if ($_POST['confirm'] == FRM_PREVIEW) {
        block_begin(COMMENT_PREVIEW);
        
	    $comment = str_replace('\r\n', '\n', $comment);

        $smarty->assign('comment_preview', format_comment(unesc($comment)));
	
        $smarty->display($STYLEPATH . '/tpl/torrent/comment_preview.tpl');
		
        block_end();
        comment_form();
        stdfoot();
    #####################
    # Comment preview end
    } else
       redirect('details.php?id=' . StripSlashes($_POST['info_hash']) . '#comments');
} else {
   comment_form();
   stdfoot();
}

?>
