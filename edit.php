<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('Edit Torrents');

$scriptname = security::esc_url($_SERVER['PHP_SELF']);
$link = unesc($_GET['returnto']);

if ($link == '')
    $link = 'details.php?id=' . stripslashes($row['info_hash']);

if ((isset($_POST['comment'])) && (isset($_POST['name']))) {
    if ($_POST['action'] == FRM_CONFIRM) {
        if ($_POST['name'] == '') {
            err_msg('Error!', 'You must specify torrent name.');
            stdfoot();
            exit;
        }

        if ($_POST['comment'] == '') {
            err_msg('Error!', 'You must specify description.');
            stdfoot();
            exit;
        }

        $fname = sqlesc(security::html_safe($_POST['name']));
        $image = sqlesc(security::html_safe($_POST['image']));
        $torhash = AddSlashes($_POST['info_hash']);
        write_log('Modified torrent ' . $fname . ' (' . $torhash . ')', 'modify');
        echo '<center>' . PLEASE_WAIT . '</center>';

        if ($GLOBALS['nuked_requested'] == 'yes') {
            $req = trim($_POST['request']);
            $nuked = trim($_POST['nuke']);
            $nuke_reason = $db->real_escape_string($_POST['nuked_reason']);

            if ($_POST['nuke'] == 'false')	{
                $db->query("UPDATE namemap SET requested = '" . $req . "', nuked = '" . $nuked . "' WHERE info_hash = '" . $torhash . "'");
                $db->query("UPDATE namemap SET nuke_reason = NULL WHERE info_hash = '" . $torhash . "'");
            } else {
                $db->query("UPDATE namemap SET requested = '" . $req . "', nuked = '" . $nuked . "', nuke_reason = '" . $nuke_reason . "' WHERE info_hash = '" . $torhash . "'");
            }
        }

        //Golden Torrents by CobraCRK
        $free = unesc($_POST['free']);
        if (is_null($free)) {
            $fr = "free = 'no', ";
        } else {
            if ($free == 1) {
	        $fr = "free = 'yes', ";
            }
        }

        if ($GLOBALS['freeleech'] == 'yes') {
            $freeleech = $fr;
        } else {
            $freeleech = '';
        }

        if ($GLOBALS['torrent_genre'] == 'yes') {
            $ugenre = "genre = '" . $db->real_escape_string(AddSlashes($_POST['genre'])) . "',";
        } else {
            $ugenre = '';
        }

        $db->query("UPDATE namemap SET " . $ugenre . " " . $freeleech . " filename = " . $fname . ", image = " . $image . ", comment = '" . $db->real_escape_string(AddSlashes($_POST["comment"])) . "', category = " . intval($_POST["category"]) . " WHERE info_hash = '" . $torhash . "'");

        MCached::del('torrent::details::' . $torhash);
        MCached::del('torrent::details::image::' . $torhash);
        MCached::del('is::freeleech::' . $torhash);
        MCached::del('torrent::details::nuked::requested::' . $torhash);

	    print("<script language='javascript'>window.location.href='" . $link . "'</script>");
        exit();
    } else {
        print("<script language='javascript'>window.location.href='" . $link . "'</script>");
        exit();
    }
}

if ($GLOBALS['torrent_genre'] == 'yes') {
    $genre = 'namemap.genre,';
} else {
    $genre = '';
}

if ($GLOBALS['freeleech'] == 'yes') {
    $freel = 'namemap.free AS free,';
} else {
    $freel = '';
}

// view torrent's details
if (isset($_GET['info_hash'])) {
    $query = "SELECT " . $genre . " " . $freel . " namemap.requested, namemap.nuked, namemap.nuke_reason, namemap.info_hash, namemap.filename, namemap.image, namemap.url, UNIX_TIMESTAMP(namemap.data) AS data, namemap.size, namemap.comment, namemap.category AS cat_name, summary.seeds, summary.leechers, summary.finished, summary.speed, namemap.uploader FROM namemap LEFT JOIN categories ON categories.id = namemap.category LEFT JOIN summary ON summary.info_hash = namemap.info_hash WHERE namemap.info_hash = '" . AddSlashes($_GET['info_hash']) . "'";
    $res = $db->query($query) or die(CANT_DO_QUERY);
    $results = $res->fetch_array(MYSQLI_BOTH);

    if (!$results)
        err_msg(ERROR, TORRENT_EDIT_ERROR);
    else {
        block_begin(EDIT_TORRENT);

        if (!user::$current || (user::$current['edit_torrents'] == 'no' && user::$current['uid'] != $results['uploader'])) {
            err_msg(ERROR, CANT_EDIT_TORR);
            block_end();
            stdfoot();
            exit();
        }

        #Vars...
        $smarty->assign('scriptname', $scriptname);
        $smarty->assign('link', $link);
        $smarty->assign('filename', security::html_safe($results['filename']));
        $smarty->assign('image', security::html_safe($results['image']));
        $smarty->assign('genre', security::html_safe($results['genre']));
        $smarty->assign('info_hash', security::html_safe($results['info_hash']));
        $smarty->assign('description', textbbcode2('edit', 'comment', security::html_safe(unesc($results['comment']))));
        $smarty->assign('categories', categories2($results['cat_name']));

        #If's...
        $smarty->assign('image_link', ($GLOBALS['image_link'] == 'yes'));
        $smarty->assign('torrent_genre', ($GLOBALS['torrent_genre'] == 'yes'));
        $smarty->assign('is_freeleech', (user::$current['edit_torrents'] == 'yes' && $GLOBALS['freeleech'] == 'yes'));
        $smarty->assign('nuked_requested', ($GLOBALS['nuked_requested'] == 'yes'));

        #Lang...
        $smarty->assign('lang_filename', FILE_NAME);
        $smarty->assign('lang_genre', GENRE);
        $smarty->assign('lang_infohash', INFO_HASH);
        $smarty->assign('lang_description', DESCRIPTION);
        $smarty->assign('lang_category', CATEGORY_FULL);
        $smarty->assign('lang_requested', TORRENT_REQUESTED);
        $smarty->assign('lang_yes', YES);
        $smarty->assign('lang_no', NO);
        $smarty->assign('lang_nuked', TORRENT_NUKED);
        $smarty->assign('lang_size', SIZE);
        $smarty->assign('lang_added', ADDED);
        $smarty->assign('lang_downloaded', DOWNLOADED);
        $smarty->assign('lang_peers', PEERS);
        $smarty->assign('lang_seeders', SEEDERS);
        $smarty->assign('lang_leechers', LEECHERS);
        $smarty->assign('lang_confirm', FRM_CONFIRM);
        $smarty->assign('lang_cancel', FRM_CANCEL);

        if ($results['requested'] == 'true') {
            $selected = " selected='selected'";
        } else {
            $selected = '';
        }
        $smarty->assign('selectedr', $selected);

        if ($results['nuked'] == 'true') {
            $selected = " selected='selected'";
        } else {
            $selected = '';
        }
        $smarty->assign('selectedn', $selected);
        $smarty->assign('nuked_reason', security::html_safe($results['nuke_reason']));

        //Golden Torrents by CobraCRK
        if ($results['free'] == 'yes') {
            $chk = " checked='checked' ";
        }
        $smarty->assign('checkedf', $chk);
		
        include(INCL_PATH . 'offset.php');

        $smarty->assign('size', misc::makesize((int)$results['size']));
        $smarty->assign('added', date('d/m/Y H:m:s', $results['data'] - $offset));
        $smarty->assign('downloaded', (int)$results['finished'] . ' ' . X_TIMES);
        $smarty->assign('seeders', (int)$results['seeds']);
        $smarty->assign('leechers', (int)$results['leechers']);
        $smarty->assign('peers', ((int)$results['leechers'] + (int)$results['seeds']));

        $smarty->display($STYLEPATH . '/tpl/torrent/edit.tpl');
    }

    block_end();
}

stdfoot();

?>
