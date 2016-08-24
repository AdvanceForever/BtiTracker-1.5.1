<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();


if (!user::$current || user::$current['edit_users'] != 'yes') {
    standardheader('Get a freakin life and stop trying to hack the tracker !');
    block_begin(ERROR);
    err_msg(ERROR, 'Staff Only !');
    print ('<br>');
    block_end();
    stdfoot(false);
} else {
    standardheader('Change Custom Title');

    if (isset($_POST['title']))
        $custom = $db->real_escape_string($_POST['title']);
    else
        $custom = '';

    if (isset($_GET['returnto']))
        $url = security::html_safe($_GET['returnto']);

    $userid = max(0, (int)$_GET['uid']);
    $user = $db->real_escape_string($_POST['username']);


    if ($custom == '') {
        $db->query("UPDATE users SET custom_title = NULL WHERE id = '" . $userid . "' AND username = '" . $user . "'") or sqlerr(__FILE__, __LINE__);
    } else {
        $db->query("UPDATE users SET custom_title = '" . security::html_safe($custom) . "' WHERE id = '" . $userid . "' AND username = '" . $user . "'") or sqlerr(__FILE__, __LINE__);
    }
    MCached::del('forum::poster::details::' . $userid);
    redirect($url);
}

?>
