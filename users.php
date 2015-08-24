<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('Members');

if (user::$current['view_users'] == 'no') {
    err_msg(ERROR, NOT_AUTHORIZED.' '.MEMBERS.'!');
    stdfoot();
    exit;
} else {
    block_begin(MEMBERS_LIST);
    print_users();
    block_end();
}
	
stdfoot();

?>
