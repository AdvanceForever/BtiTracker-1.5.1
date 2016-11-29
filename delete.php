<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('Delete Torrents');

$id = $db->real_escape_string($_GET['info_hash']);

if (!isset($id) || !$id)
    die('Error ID');

$row = MCached::get('delete::query::' . $id);
if ($row === MCached::NO_RESULT) {
    $res = $db->query("SELECT namemap.info_hash, namemap.uploader, namemap.filename, namemap.url, UNIX_TIMESTAMP(namemap.data) AS data, namemap.size, namemap.comment, categories.name AS cat_name, summary.seeds, summary.leechers, summary.finished, summary.speed FROM namemap LEFT JOIN categories ON categories.id = namemap.category LEFT JOIN summary ON summary.info_hash = namemap.info_hash WHERE namemap.info_hash = '" . $id . "'");
    $row = $res->fetch_array(MYSQLI_BOTH);
    MCached::add('delete::query::' . $id, $row, 1800);
}

if (user::$current['delete_torrents'] != 'yes' && user::$current['uid'] != $row['uploader']) {
    err_msg(SORRY, CANT_DELETE_TORRENT);
    stdfoot();
    exit();
}

$scriptname = security::esc_url($_SERVER['PHP_SELF']);

$link = urlencode($_GET['returnto']);
$hash = AddSlashes($_GET['info_hash']);

if ($link == '')
    $link = 'torrents.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == DELETE) {
        $ris = $db->query("SELECT info_hash, filename, url FROM namemap WHERE info_hash = '" . $hash . "'");
        if ($ris->num_rows == 0) {
            err_msg('Sorry!', 'Torrent ' . $hash . ' not found.');
            exit();
        } else {
            list($torhash, $torname, $torurl) = $ris->fetch_array(MYSQLI_BOTH);
        }

        write_log('Deleted torrent ' . $torname . ' (' . $torhash . ')', 'delete');
        
        @$db->query("DELETE FROM summary WHERE info_hash = '" . $hash . "'");
        @$db->query("DELETE FROM namemap WHERE info_hash = '" . $hash . "'");
        @$db->query("DELETE FROM timestamps WHERE info_hash = '" . $hash . "'");
        @$db->query("DELETE FROM comments WHERE info_hash = '" . $hash . "'");
        @$db->query("DELETE FROM ratings WHERE infohash = '" . $hash . "'");
        @$db->query("DELETE FROM peers WHERE infohash = '" . $hash . "'");
        @$db->query("DELETE FROM history WHERE infohash = '" . $hash . "'");
        
        MCached::del('torrent::details::' . $hash);
        MCached::del('torrent::details::image::' . $hash);
        MCached::del('torrent::details::nuked::requested::' . $hash);
        MCached::del('delete::query::' . $hash);
        
        unlink($TORRENTSDIR . "/" . $hash . ".btf");
        
        print("<script language='javascript'>window.location.href='" . $link . "'</script>");
        exit();
    } else {
        print("<script language='javascript'>window.location.href='" . $link . "'</script>");
        exit();
    }
}

block_begin(DELETE_TORRENT);

#Lang...
$smarty->assign('lang',
                 array('file_name' => FILE_NAME,
                       'info_hash' => INFO_HASH,
                       'description' => DESCRIPTION,
                       'category' => CATEGORY_FULL,
                       'size' => SIZE,
                       'added' => ADDED,
                       'speed' => SPEED,
                       'downloaded' => DOWNLOADED,
                       'peers' => PEERS,
                       'seeders' => SEEDERS,
                       'leechers' => LEECHERS,
                       'delete' => DELETE,
                       'cancel' => FRM_CANCEL
                      )
               );

#Vars...
$smarty->assign('filename', security::html_safe($row['filename']));
$smarty->assign('info_hash', security::html_safe($row['info_hash']));
$smarty->assign('description', format_comment(unesc($row['comment'])));
$smarty->assign('category', security::html_safe($row['cat_name']));
$smarty->assign('size', misc::makesize((int)$row['size']));
$smarty->assign('added', date('d/m/Y H:m:s', $row['data']));
$smarty->assign('finished', (int)$row['finished']);
$smarty->assign('seeders', (int)$row['seeds']);
$smarty->assign('leechers', (int)$row['leechers']);
$smarty->assign('peers', ((int)$row['leechers'] + (int)$row['seeds']));
$smarty->assign('scriptname', $scriptname);
$smarty->assign('id', $id);
$smarty->assign('link', $link);

#If's...
$smarty->assign('has_description', (!empty($row['comment'])));
$smarty->assign('has_category', (isset($row['cat_name'])));

if ($row['speed'] < 0) {
    $speed = 'N/A';
} else if ($row['speed'] > 2097152) {
    $speed = round((int)$row['speed'] / 1048576, 2) . ' MiB per sec';
} else {
    $speed = round((int)$row['speed'] / 1024, 2) . ' KiB per sec';
}
$smarty->assign('speed', $speed);

$smarty->display($STYLEPATH . '/tpl/torrent/delete.tpl');

block_end();
stdfoot();

?>
