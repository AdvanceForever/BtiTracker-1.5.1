<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

$maxallowed = $max_req_allowed;
$res3 = $db->query("SELECT * FROM requests WHERE userid = " . user::$current['uid']) or $db->error;
$arr3 = $res3->num_rows;
$numreqs = $arr3;

if ($numreqs >= $maxallowed) {
    standardheader('Make a Request');
    err_msg(ERROR, 'Sorry, You just reached your max number of Requests: <b>' . $maxallowed . '</b>');
    print('<br>');
    stdfoot(false);
    exit;
}

if (!user::$current || user::$current['view_torrents'] == 'no') {
    standardheader('Not Authorized !');
    block_begin(ERROR);
    err_msg(ERROR, ERR_NOT_AUTH);
    print('<br>');
    block_end();
    stdfoot();
    exit();
} else {
    $requesttitle = $_POST['requesttitle'];
    $descr = $_POST['description'];
    $cat = intval(0 + $_POST['category']);

    if (!$requesttitle) {
        standardheader('Requests');
        block_begin('Requests');
        err_msg(ERROR, 'No title added !');
        print('<br>');
        block_end();
        stdfoot(false);
        exit;
    }

    $cat = intval(0 + $_POST['category']);
    if ($cat == 0) {
        standardheader('Requests');
        block_begin('Requests');
        err_msg(ERROR, 'You must choose a Category !');
        print('<br>');
        block_end();
        stdfoot(false);
        exit;
    }

    $descr = $_POST['description'];
    if (!$descr) {
        standardheader('Requests');
        block_begin('Requests');
        err_msg(ERROR, 'No Description added !');
        print('<br>');
        block_end();
        stdfoot(false);
        exit;
    }

    $request = sqlesc($requesttitle);
    $descr = sqlesc($descr);
    $cat = sqlesc($cat);

    $db->query("INSERT INTO requests (hits, userid, cat, request, descr, added) VALUES(1, " . user::$current['uid'] . ", " . $cat . ", " . $request . ", " . $descr . ", NOW())") or sqlerr(__FILE__,__LINE__);

    $id = $db->insert_id;

    @$db->query("INSERT INTO addedrequests VALUES(0, " . $id . ", " . user::$current['uid'] . ")") or sqlerr();

    write_log($request . ' was added to the Request Section !');

    header('Refresh: 0; url=viewrequests.php');
}

?>
