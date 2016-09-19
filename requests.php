<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Requests');

begin_frame(MAKE_REQUEST);

$where = "WHERE userid = " . user::$current['uid'];
$res2 = $db->query("SELECT * FROM requests " . $where) or sqlerr();
$num2 = $res2->num_rows;

$smarty->assign('lang_search', SEARCH);
$smarty->assign('lang_torrent', TORRENT);
$smarty->assign('lang_add_requests', ADD_REQUESTS);
$smarty->assign('lang_torrent_file', TORRENT_FILE);
$smarty->assign('lang_category', CATEGORY);
$smarty->assign('lang_description', DESCRIPTION);
$smarty->assign('lang_confirm', FRM_CONFIRM);

$smarty->assign('search_string', security::html_safe($searchstr));

$cats = Cached::genrelist();
$catdropdown = '';

foreach ($cats as $cat) {
         $catdropdown .= "<option value='" . (int)$cat['id'] . "'";
         if ($cat['id'] == $_GET['cat'])
             $catdropdown .= " selected='selected'";

         $catdropdown .= ">" . security::html_safe($cat['name']) . "</option>\n";
}

$deadchkbox = "<input type='checkbox' name='active' value='0'";

if ($_GET['active'])
    $deadchkbox .= " checked='checked'";

$deadchkbox .= " /> " . INC_DEAD . "\n";

$smarty->assign('category', $catdropdown);
$smarty->assign('dead_checkbox', $deadchkbox);

$res2 = $db->query("SELECT id, name FROM categories ORDER BY name");
$num = $res2->num_rows;

$catdropdown2 = '';

for ($i = 0; $i < $num; ++$i) {
     $cats2 = $res2->fetch_assoc();  
     $catdropdown2 .= "<option value='" . (int)$cats2['id'] . "'";
     $catdropdown2 .= ">" . security::html_safe($cats2['name']) . "</option>\n";
}

$smarty->assign('category2', $catdropdown2);
$smarty->assign('description', textbbcode2('request', 'description'));

$smarty->display($STYLEPATH . '/tpl/tracker/make_request.tpl');

block_end();
stdfoot();

?>
