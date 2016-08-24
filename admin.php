<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2014 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');
require(CLASS_PATH . 'class.Allowed_Staff.php');

dbconn(false);

#Additional Staff Check
$rq = new allowed_staff;
if (!$rq->check('admincp'))
    die();
#Additional Staff Check End
  
$aid = intval($_GET["user"]);
$arandom = intval($_GET["code"]);
if (!$aid || empty($aid) || $aid == 0 || !$arandom || empty($arandom) || $arandom == 0) {
    standardheader('Access Denied');
    err_msg(ERROR, NOT_ADMIN_CP_ACCESS);
    stdfoot();
    exit;
}

if (!user::$current || user::$current["edit_torrents"] != "yes") {
    err_msg(ERROR, 'Move Along...');
    stdfoot();
    exit;
}

$action = isset($_GET["action"]) ? security::html_safe($_GET["action"]) : '';
    
$staff_actions = array('hitrun' => 'hitrun', 
                        'duplicateips' => 'duplicateips', 
                        'uploaders' => 'uploaders', 
                        'delacct'         => 'delacct', 
                        'testip'          => 'testip', 
                        'usersearch'      => 'usersearch', 
                        'mysql_overview'  => 'mysql_overview', 
                        'mysql_stats'     => 'mysql_stats', 
                        'categories'      => 'categories', 
                        'newusers'        => 'newusers', 
                        'resetpassword'   => 'resetpassword',
                        'docleanup'       => 'docleanup',
                        'log'             => 'log',
                        'news'            => 'news',
                        'forummanage'     => 'forummanage'
                        );
    
if (in_array($action, $staff_actions) AND file_exists("admin/{$staff_actions[$action]}.php")) {
    require_once ADMIN_PATH . "/{$staff_actions[$action]}.php";
} else {
    require_once "index.php";
}
    
?>
