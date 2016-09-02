<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');
require_once(CLASS_PATH . 'class.Bencode.php');

function_exists('sha1') or die('<font color="red">' . NOT_SHA . '</font></body></html>');

dbconn();

standardheader('Uploads');

if (!user::$current || user::$current['can_upload'] == 'no') {
    stderr(ERROR . NOT_AUTHORIZED_UPLOAD, SORRY . "...");
}

block_begin(MNU_UPLOAD);

print("<table class='lista' border='0' width='100%'>\n");
print("<tr><td align='center'>");

if (isset($_FILES['torrent'])) {
    if ($_FILES['torrent']['error'] != 4) {
        $fd = fopen($_FILES['torrent']['tmp_name'], 'rb') or die(FILE_UPLOAD_ERROR_1);
        is_uploaded_file($_FILES['torrent']['tmp_name']) or die(FILE_UPLOAD_ERROR_2);
        $length = filesize($_FILES['torrent']['tmp_name']);
        if ($length)
            $alltorrent = fread($fd, $length);
        else {
            err_msg(ERROR, FILE_UPLOAD_ERROR_3);
            print("</td></tr></table>");
            block_end();
            stdfoot();
            exit();
        }
        
        //uTorrent v3.x.x fix
        $alltorrent = preg_replace("/file-mediali(.*?)ee(.*?):/i", "file-mediali0ee$2:", $alltorrent);
        $alltorrent = preg_replace("/file-durationli(.*?)ee(.*?):/i", "file-durationli0ee$2:", $alltorrent);
        
        $array = bencdec::decode($alltorrent, bencdec::OPTION_EXTENDED_VALIDATION);
        if (!isset($array)) {
            echo "<font color='red'>" . ERR_PARSER . "</FONT>";
            endOutput();
            exit;
        }
        if (!$array) {
            echo "<font color='red'>" . ERR_PARSER . "</FONT>";
            endOutput();
            exit;
        }

        if (in_array($array['announce'], $TRACKER_ANNOUNCEURLS) && $DHT_PRIVATE) {
            $array['info']['private'] = 1;
            $hash  = sha1(bencdec::encode($array['info']));
        } else {
            $hash = sha1(bencdec::encode($array['info']));
        }
        fclose($fd);
    }
    
    if (isset($_POST['filename']))
        $filename = $db->real_escape_string(htmlspecialchars($_POST['filename']));
    else
        $filename = $db->real_escape_string(htmlspecialchars($_FILES['torrent']['name']));

    if (isset($_POST['image']))
        $image = $db->real_escape_string(AddSlashes($_POST['image']));
    else
        $image = '';
    
    if (isset($hash) && $hash)
        $url = $TORRENTSDIR . '/' . $hash . '.btf';
    else
        $url = 0;
    
    if (isset($_POST['info']) && $_POST['info'] != '')
        $comment = $db->real_escape_string($_POST['info']);
    else {
        err_msg(ERROR, 'You must enter a description!');
        print("</td></tr></table>");
        block_end();
        stdfoot();
        exit();
    }
    
    if (strlen($filename) == 0 && isset($array['info']['name']))
        $filename = $db->real_escape_string(htmlspecialchars($array['info']['name']));
    
    if (isset($array['comment']))
        $info = $db->real_escape_string(utf8::is_utf8($array['comment']));
    else
        $info = '';
    
    if (isset($array['info']) && $array['info'])
        $upfile = $array['info'];
    else
        $upfile = 0;
    
    if (isset($upfile['length'])) {
        $size = floatval($upfile['length']);
    } else if (isset($upfile['files'])) {
        // multifiles torrent
        $size = 0;
        foreach ($upfile['files'] as $file) {
            $size += floatval($file['length']);
        }
    } else
        $size = 0;
    
    if (!isset($array['announce'])) {
        err_msg(ERROR, 'Announce is empty !');
        print("</td></tr></table>");
        block_end();
        stdfoot();
        exit();
    }
    
    $categoria = intval(0 + $_POST['category']);
    $announce  = $array['announce'];
    $anonyme   = sqlesc($_POST['anonymous']);
    $curuid    = user::$current['uid'];

    $req = trim($_POST['requested']);
    $nuke = trim($_POST['nuked']);

    if ($nuke == 'true') {
        $nuke_reason = $db->real_escape_string($_POST['nuked_reason']);
    } else {
        $nuke_reason = '';
    }
    
    if ($categoria == 0) {
        err_msg(ERROR, WRITE_CATEGORY);
        print("</td></tr></table>");
        block_end();
        stdfoot();
        exit();
    }
    
    if ((strlen($hash) != 40) || !verifyHash($hash)) {
        echo ("<center><font color='red'>" . ERR_HASH . "</font></center>");
        endOutput();
    }

    if (!in_array($announce, $TRACKER_ANNOUNCEURLS) && $EXTERNAL_TORRENTS == false) {
        err_msg(ERROR, ERR_EXTERNAL_NOT_ALLOWED);
        unlink($_FILES['torrent']['tmp_name']);
        print("</td></tr></table>");
        block_end();
        stdfoot();
        exit();
    }

    if (in_array($announce, $TRACKER_ANNOUNCEURLS))
        $query = "INSERT INTO namemap (info_hash, filename, image, url, info, category, data, size, comment, uploader, anonymous, genre, requested, nuked, nuke_reason) VALUES ('" . $hash . "', '" . $filename . "', '" . $image . "', '" . $url . "', '" . $info . "', 0 + " . $categoria . ", NOW(), '" . $size . "', '" . $comment . "', " . $curuid . ", " . $anonyme . ", '" . $db->real_escape_string($_POST['genre']) . "', '" . $req . "', '" . $nuke . "', '" . $nuke_reason . "')";
    else
        $query = "INSERT INTO namemap (info_hash, filename, image, url, info, category, data, size, comment, external, announce_url, uploader, anonymous, genre, requested, nuked, nuke_reason) VALUES ('" . $hash . "', '" . $filename . "', '" . $image . "', '" . $url . "', '" . $info . "', 0 + " . $categoria . ", NOW(), '" . $size . "', '" . $comment . "', 'yes', '" . $announce . "', " . $curuid . ", " . $anonyme . ", '" . $db->real_escape_string($_POST['genre']) . "', '" . $req . "', '" . $nuke . "', '" . $nuke_reason . "')";
	
    $status = makeTorrent($hash, true);
    quickQuery($query);
	
    if ($status) {
        move_uploaded_file($_FILES['torrent']['tmp_name'], $TORRENTSDIR . '/' . $hash . '.btf') or die(ERR_MOVING_TORR);

        if (!in_array($announce, $TRACKER_ANNOUNCEURLS)) {
            require_once(INCL_PATH . 'getscrape.php');
            scrape($announce, $hash);
            print("<center>" . MSG_UP_SUCCESS . "<br /><br />\n");
            write_log("Uploaded new torrent " . $filename . " - EXT (" . $hash . ")", "add");
        } else {
            if ($DHT_PRIVATE) {
                $alltorrent = bencdec::encode($array);
                $fd         = fopen($TORRENTSDIR . "/" . $hash . ".btf", "rb+");
                fwrite($fd, $alltorrent);
                fclose($fd);
            }
            // with pid system active or private flag (dht disabled), tell the user to download the new torrent
            write_log("Uploaded new torrent " . $filename . " (" . $hash . ")", "add");
            print("<center>" . MSG_UP_SUCCESS . "<br /><br />\n");
            if ($PRIVATE_ANNOUNCE || $DHT_PRIVATE)
                print(MSG_DOWNLOAD_PID . "<br /><a href='download.php?id=" . $hash . "&f=" . urlencode($filename) . ".torrent'>" . DOWNLOAD . "</a><br /><br />");
        }
        print("<a href='torrents.php'>" . RETURN_TORRENTS . "</a></center>");
        print("</td></tr></table>");
        block_end();
    } else {
        err_msg(ERROR, ERR_ALREADY_EXIST);
        unlink($_FILES['torrent']['tmp_name']);
        print("</td></tr></table>");
        block_end();
        stdfoot();
    }
} else
    endOutput();

function endOutput() {
    global $BASEURL, $user_id, $smarty, $STYLEPATH, $TRACKER_ANNOUNCEURLS;

    $smarty->assign('insert_data', INSERT_DATA);
    $smarty->assign('announce_url', ANNOUNCE_URL);

    foreach ($TRACKER_ANNOUNCEURLS as $taurl)
         $smarty->assign('tracker_announce_url', $taurl);

    $smarty->assign('torrent_file', TORRENT_FILE);
    $smarty->assign('genre', GENRE);
    $smarty->assign('image_link', IMAGE_URL);
    $smarty->assign('torrent_genre', ($GLOBALS['torrent_genre'] == 'yes'));
    $smarty->assign('image_on', ($GLOBALS['image_link'] == 'yes'));
    $smarty->assign('sha1_exists', (function_exists('sha1')));
    $smarty->assign('no_sha1', NO_SHA_NO_UP);
    $smarty->assign('category', CATEGORY_FULL);
    $smarty->assign('categories', categories2($category[0]));
    $smarty->assign('filename', FILE_NAME);
    $smarty->assign('description', DESCRIPTION);
    $smarty->assign('description_body', textbbcode2('upload', 'info'));
    $smarty->assign('user_id', $user_id);
    $smarty->assign('anonymous', TORRENT_ANONYMOUS);
    $smarty->assign('no', NO);
    $smarty->assign('yes', YES);
    $smarty->assign('torrent_check', TORRENT_CHECK);
    $smarty->assign('send', FRM_SEND);
    $smarty->assign('reset', FRM_RESET);
    $smarty->assign('torrent_requested', TORRENT_REQUESTED);
    $smarty->assign('torrent_nuked', TORRENT_NUKED);
    $smarty->assign('nuked_requested', ($GLOBALS['nuked_requested'] == 'yes'));

    $smarty->display($STYLEPATH . '/tpl/torrent/upload.tpl');

    block_end();
}

stdfoot();

?>
