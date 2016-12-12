<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

global $db, $STYLEPATH, $smarty;

if (!user::$current || user::$current['view_users'] == 'no') {
    // do nothing
} else {
    //lastest member
    block_begin('Latest Member');
	   
    $key = 'latest::member';
    $a = MCached::get($key);
    if ($a === MCached::NO_RESULT) {
        $a = @$db->query('SELECT id, username FROM users WHERE id_level <> 1 AND id_level <> 2 ORDER BY id DESC LIMIT 1');
	    $a = @$a->fetch_assoc();
        MCached::add($key, $a, 9600);
    }
	
    if ($a) {
        if (user::$current['view_users'] == 'yes') {
            $latestuser = "<a href='userdetails.php?id=" . (int)$a['id'] . "'>" . security::html_safe($a['username']) . "</a>" . Warn_disabled($a['id']);
        } else {
            $latestuser = security::html_safe($a['username']);
        }

        $smarty->assign('latest_user', $latestuser);

        $smarty->display($STYLEPATH . '/tpl/blocks/lastmember_block.tpl');
    }
    block_end();
    
} // end if user can view

?>
