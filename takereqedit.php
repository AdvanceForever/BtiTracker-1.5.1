<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn(true);
		
$id2 = (int)$_POST['id'];

$res = $db->query("SELECT * FROM requests WHERE id = " . $id2);
$row = $res->fetch_array(MYSQLI_BOTH);

if (user::$current['uid'] == $row['userid'] || user::$current['id_level'] >= '7') {
    $id = (int)$_POST['id'];
    $requesttitle = $_POST['requesttitle'];
    $descr = $_POST['description'];
    $cat = $_POST['category'];
	
    if ($requesttitle == '' || $cat == 0 || $descr == '') {
	standardheader('Edit');
	block_begin(ERROR);
	err_msg(ERROR, ERR_MISSING_DATA);
	print("<br>");
	block_end();
	stdfoot();
	exit();
    }
	
    $request = sqlesc($requesttitle);
    $descr = sqlesc($descr);
    $cat = sqlesc($cat);
	
    $db->query("UPDATE requests SET cat = " . $cat . ", request = " . $request . ", descr = " . $descr . " WHERE id = " . $id);
	
    $id = $db->insert_id;
	
    header('Refresh: 0; url=viewrequests.php');
} else {
    standardheader('Edit');
    block_begin(ERROR);
    err_msg(ERROR, ERR_NOT_AUTH);
    print("<br>");
    block_end();
    stdfoot();
    exit();
}

?>
