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
            ?>
            <form name='commentsedit' method='post' action="edit_comment.php?do=comments&action=write&id=<?php echo (int)$subrow['id']; ?>"multipart/form-data">
            <input type='hidden' name='info_hash' value='<?php echo security::html_safe($subrow['info_hash']); ?>' />
	    <table class='lista' width='100%' align='center'>
            <tr>
            <td class='header' align='right'><?php echo USER_NAME; ?></td>
            <td class='lista'><input type='text' name='user' value='<?php echo security::html_safe($subrow['user']); ?>' size='40' maxlength='60' disabled; readonly /></td>
            </tr>
            <?php
            print("<tr><td class='header' align='right'>" . COMMENT_1 . "</td><td class='lista' align='left' style='padding: 0px'>");
            textbbcode('commentsedit', 'text', security::html_safe(unesc($subrow['text'])));
            print("</td></tr>");
            ?>
            <tr>
            <td class='lista' colspan='2' align='center'><input type='submit' name='write' value='<?php echo FRM_CONFIRM ?>' />&nbsp;&nbsp;&nbsp;
            <input type='submit' name='write' value='<?php echo FRM_CANCEL ?>' /></td>
            </tr>
            </table>
            </form>
            <?php
            block_end();
            print("<br />");
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
        ?>
        <form name='comment' method='post' action="edit_comment.php?do=comments&action=confirm&id=<?php echo (int)$subrow['id']; ?>"multipart/form-data">
        <input type='hidden' name='info_hash' value='<?php echo security::html_safe($subrow['info_hash']); ?>' />				   
	<table class='lista' width='100%' align='center'>
        <tr>
        <td class='header' align='right'><?php echo USER_NAME; ?></td>
        <td class='lista'><input type='text' name='user' value='<?php echo user::$current['username']; ?>' size='40' maxlength='60' disabled; readonly /></td>
        </tr>
        <?php
        print("<tr><td class='header' align='right'>" . COMMENT_1 . "</td><td class='lista' align='left' style='padding: 0px'>");
	textbbcode('comment', 'text', ($quote ? (('[quote=' . security::html_safe($subrow['user']) . ']' . security::html_safe(unesc($subrow['text'])) . '[/quote]')) : ''));
        print("</td></tr>");
        ?>
        <tr>
        <td class='lista' colspan='2' align='center'><input type='submit' name='confirm' value='<?php echo FRM_CONFIRM ?>' />&nbsp;&nbsp;&nbsp;
        <input type='submit' name='confirm' value='<?php echo FRM_CANCEL ?>' /></td>
        </tr>
        </table>
        </form>
        <?php
        block_end();
        print('<br />');
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

        redirect('details.php?id=' . StripSlashes($_POST['info_hash']) . '#comments');
    } else
    redirect('details.php?id=' . StripSlashes($_POST['info_hash']) . '#comments');
}

stdfoot();

?>
