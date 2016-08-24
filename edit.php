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

$scriptname = security::esc_url($_SERVER["PHP_SELF"]);
$link = unesc($_GET["returnto"]);

if ($link == "")
    $link = "details.php?id=" . $row["info_hash"];

if ((isset($_POST["comment"])) && (isset($_POST["name"]))) {
    if ($_POST["action"] == FRM_CONFIRM) {
        if ($_POST["name"] == '') {
            err_msg("Error!", "You must specify torrent name.");
            stdfoot();
            exit;
        }

        if ($_POST["comment"] == '') {
            err_msg("Error!","You must specify description.");
            stdfoot();
            exit;
        }

        $fname = sqlesc(security::html_safe($_POST["name"]));
        $image = sqlesc(security::html_safe($_POST["image"]));
        $torhash = AddSlashes($_POST["info_hash"]);
        write_log("Modified torrent " . $fname . " (" . $torhash . ")", "modify");
        echo "<center>".PLEASE_WAIT."</center>";

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
if (isset($_GET["info_hash"])) {
    $query = "SELECT " . $genre . " " . $freel . " namemap.info_hash, namemap.filename, namemap.image, namemap.url, UNIX_TIMESTAMP(namemap.data) AS data, namemap.size, namemap.comment, namemap.category AS cat_name, summary.seeds, summary.leechers, summary.finished, summary.speed, namemap.uploader FROM namemap LEFT JOIN categories ON categories.id = namemap.category LEFT JOIN summary ON summary.info_hash = namemap.info_hash WHERE namemap.info_hash = '" . AddSlashes($_GET["info_hash"]) . "'";
    $res = $db->query($query) or die(CANT_DO_QUERY);
    $results = $res->fetch_array(MYSQLI_BOTH);

    if (!$results)
        err_msg(ERROR, TORRENT_EDIT_ERROR);
    else {
        block_begin(EDIT_TORRENT);

        if (!user::$current || (user::$current["edit_torrents"] == "no" && user::$current["uid"] != $results["uploader"])) {
            err_msg(ERROR, CANT_EDIT_TORR);
            block_end();
            stdfoot();
            exit();
        }
        ?>
        
        <div align='center'>
        <form action='<?php echo $scriptname . '?returnto=' . $link; ?>' method='post' name='edit'>
        <table class='lista'>
        <tr>
        <td align='right' class='header'><?php echo FILE_NAME; ?>:</td><td class='lista'><input type='text' name='name' value='<?php echo security::html_safe($results["filename"]); ?>' size='60' /></td>
        </tr>

        <?php if ($GLOBALS['image_link'] == 'yes') { ?>
        <tr>
        <td align='right' class='header'>Image Link:</td><td class='lista'><input type='text' name='image' value='<?php echo security::html_safe($results['image']); ?>' size='60' /></td>
        </tr>
        <?php } ?>

        <?php if ($GLOBALS['torrent_genre'] == 'yes') { ?>
        <tr><td align='right' class='header'><?php echo GENRE; ?>:</td><td class='lista'><input type='text' name='genre' value='<?php echo security::html_safe($results['genre']); ?>' size='60' /></td></tr>
<?php } ?>

	<tr>
        <td align='right' class='header'><?php echo INFO_HASH;?>:</td><td class='lista'><?php echo security::html_safe($results["info_hash"]);  ?></td>
        </tr><tr>
        <td align='right' class='header'><?php echo DESCRIPTION; ?>:</td><td class='lista'><?php textbbcode("edit", "comment", security::html_safe(unesc($results["comment"]))) ?></td>
        </tr>
		<tr>
         
        <?php
        echo "<td align='right' class='header'>".CATEGORY_FULL.":</td><td class='lista' align='left'>";
        
        categories($results['cat_name']);
        
        echo "</td>";

        //Golden Torrents by CobraCRK
        if (user::$current['edit_torrents'] == 'yes' && $GLOBALS['freeleech'] == 'yes') {
            if ($results['free'] == 'yes') {
                $chk = " checked='checked' ";
            }
            print("<tr><td class='header'>Freeleech:</td><td class='lista'><input type='checkbox' name='free'" . $chk . " value='1' /> Free download (only upload stats are recorded)</td></tr>");
        }
		
        include(INCL_PATH . 'offset.php');
        
        ?>
        </tr>
	<tr>
        <td align='right' class='header'><?php echo SIZE; ?>:</td><td class='lista'><?php echo misc::makesize((int)$results["size"]); ?></td>
        </tr>
	<tr>
        <td align='right' class='header'><?php echo ADDED; ?>:</td><td class='lista'><?php echo date("d/m/Y H:m:s", $results["data"] - $offset); ?></td>
        </tr>
	<tr>
        <td align='right' class='header'><?php echo DOWNLOADED; ?>:</td><td class='lista'><?php echo (int)$results["finished"] . " " . X_TIMES; ?></td>
        </tr>
	<tr>
        <td align='right' class='header'><?php echo PEERS; ?>:</td><td class='lista'><?php echo SEEDERS .": " . (int)$results["seeds"] . ", " . LEECHERS .": " . (int)$results["leechers"] . " = " . ((int)$results["leechers"] + (int)$results["seeds"]) . " " . PEERS; ?></td>
        </tr>
        <tr>
	<td><input type='hidden' name='info_hash' size='40' value='<?php echo security::html_safe($results["info_hash"]);  ?>'></td><td></td>
	</tr>
        <tr>
	<td align='right'></td>
        </table>
        <table>
	<td align='right'>
        <input type='submit' value='<?php echo FRM_CONFIRM; ?>' name='action' />
        </td>
        <td>
        <input type='submit' value='<?php echo FRM_CANCEL; ?>' name='action' /></td>
        </form>
        </table>
        </tr>
        </div>
        
        <?php
    }

block_end();
}

stdfoot();

?>
