<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('User Details');

block_begin(USER_DETAILS);

$id = intval(0 + $_GET['id']);

if (!isset($_GET['returnto']))
    $_GET["returnto"] = '';

$link = rawurlencode($_GET['returnto']);

if (user::$current['view_users'] != 'yes') {
    err_msg(ERROR, NOT_AUTHORIZED.' '.MEMBERS);
    block_end();
    stdfoot();
    die();
}

if ($id > 1) {
    $res = $db->query("SELECT users.id_level, users.old_rank, users.timed_rank, users.custom_title, users.warns, users.disabled, users.disabledby, users.disabledon, users.disabledreason, users.warnremovedby, users.cip, UNIX_TIMESTAMP(users.lastconnect) AS lastconnect, users_level.level, users_level.prefixcolor, users_level.suffixcolor, countries.name, countries.flagpic, users.pid, users.time_offset FROM users INNER JOIN users_level ON users_level.id = users.id_level LEFT JOIN countries ON users.flag = countries.id WHERE users.id = " . $id);
    $num = $res->num_rows;

    #Stats Upped, Downed, etc...
    $user_stats = MCached::get('user::stats::' . $id);
    if ($user_stats === MCached::NO_RESULT) {
        $stats = $db->query('SELECT uploaded, downloaded FROM users WHERE id = ' . $id);
        $user_stats = $stats->fetch_assoc();
        $user_stats['uploaded'] = (float)$user_stats['uploaded'];
        $user_stats['downloaded'] = (float)$user_stats['downloaded'];
        MCached::add('user::stats::' . $id, $user_stats, 1800);
    }

    #Other...
    $get_user = MCached::get('user::profile::' . $id);
    if ($get_user === MCached::NO_RESULT) {
        $user = $db->query('SELECT avatar, email, username, UNIX_TIMESTAMP(joined) AS joined, flag FROM users WHERE id = ' . $id);
        $get_user = $user->fetch_assoc();
        $get_user['avatar'] = (string)$get_user['avatar'];
        $get_user['email'] = (string)$get_user['email'];
        $get_user['username'] = (string)$get_user['username'];
        $get_user['flag'] = (string)$get_user['flag'];
        MCached::add('user::profile::' . $id, $get_user, 21600);
    }

    if ($num == 0) {
        err_msg(ERROR, BAD_ID);
        block_end();
        stdfoot();
        die();
    } else {
        $row = $res->fetch_array(MYSQLI_BOTH);
    }
} else {
    err_msg(ERROR, BAD_ID);
    block_end();
    stdfoot();
    die();
}

$utorrents = user::$current["torrentsperpage"];

print("<table class='lista' width='100%'>\n");
print("<tr>\n<td class='header'>" . USER_NAME . "</td>\n<td class='lista'>" . security::html_safe(unesc($get_user["username"])) . " " . Warn_disabled($id) . "&nbsp;&nbsp;&nbsp;");

if (user::$current["uid"] > 1 && $id != user::$current["uid"])
    print("<a href='usercp.php?do=pm&amp;action=edit&amp;uid=" . user::$current["uid"] . "&amp;what=new&amp;to=" . urlencode(unesc($get_user["username"])) . "'>" . image_or_link($STYLEPATH . "/pm.png","","PM") . "</a>\n");

if (user::$current["edit_users"] == "yes" && $id != user::$current["uid"])
    print("\n&nbsp;&nbsp;&nbsp<a href='account.php?act=mod&amp;uid=" . $id . "&amp;returnto=userdetails.php?id=" . $id . "'>".image_or_link($STYLEPATH."/edit.png","",EDIT)."</a>");

if (user::$current["delete_users"] == "yes" && $id != user::$current["uid"])
  print("\n&nbsp;&nbsp;&nbsp<a onclick='return confirm('".AddSlashes(DELETE_CONFIRM)."')' href='account.php?act=del&uid=".$id."&returnto=users.php'>".image_or_link($STYLEPATH."/delete.png","",DELETE)."</a>");

print("</td>");

if ($get_user["avatar"] && $get_user["avatar"] != '') {
   print("<td class='lista' align='center' valign='middle' rowspan='4'><img border='0' width='138' src='".security::html_safe($get_user["avatar"])."' /></td>");
} else {
   print("<td class='lista' align='center' valign='middle' rowspan='4'><img border='0' width='138' src='images/default_avatar.png' /></td>");
}

print("</tr>");

if (user::$current["edit_users"] == "yes" || user::$current["admin_access"] == "yes") {
    print("<tr>\n<td class='header'>".EMAIL."</td>\n<td class='lista'><a href='mailto:".unesc($get_user["email"])."'>".unesc($get_user["email"])."</a></td></tr>\n");
    print("<tr>\n<td class='header'>".LAST_IP."</td>\n<td class='lista'>".($row["cip"])."</td></tr>\n");
    print("<tr>\n<td class='header'>".USER_LEVEL."</td>\n<td class='lista'>".security::html_safe($row['level'])."</td></tr>\n");

    $colspan = " colspan='2'";
} else {
  print("<tr>\n<td class='header'>".USER_LEVEL."</td>\n<td class='lista'>".security::html_safe($row['level'])."</td></tr>\n");
 
  $colspan = '';
}

//Custom Title System Hack Start
if ($GLOBALS['custom_title'] == 'yes') {
    if (!$row['custom_title'])
        $title = '<i>' . NO_CUSTOM_TITLE . '</i>';
    else
        $title = unesc($row['custom_title']);

    print("<tr>\n<td class='header'>" . CUSTOM_TITLE . "</td>\n<td class='lista'" . $colspan . ">" . $title . "</td></tr>\n");
}
//Custom Title System Hack Stop

print("<tr>\n<td class='header'>".USER_JOINED."</td>\n<td class='lista'".$colspan.">".($get_user["joined"] == 0 ? "N/A" : get_date_time($get_user["joined"]))."</td></tr>\n");
print("<tr>\n<td class='header'>".USER_LASTACCESS."</td>\n<td class='lista'".$colspan.">".($row["lastconnect"] == 0 ? "N/A" : get_date_time($row["lastconnect"]))."</td></tr>\n");
print("<tr>\n<td class='header'>".PEER_COUNTRY."</td>\n<td class='lista' colspan='2'>".($get_user["flag"] == 0 ? "" : unesc($row['name']))."&nbsp;&nbsp;<img src='images/flag/".(!$row["flagpic"] || $row["flagpic"] == "" ? "unknown.gif" : $row["flagpic"])."' alt='".($get_user["flag"] == 0 ? "Unknown" : unesc($row['name']))."' /></td></tr>\n");

if (date('I', vars::$timestamp) == 1) {
    $tzu = (date('Z', vars::$timestamp) - 3600);
} else {
    $tzu = date('Z', vars::$timestamp);
}

$offsetu = $tzu - ($row["time_offset"] * 3600);

print("<tr>\n<td class='header'>".USER_LOCAL_TIME."</td>\n<td class='lista' colspan='2'>".date("d/m/Y H:i:s", vars::$timestamp - $offsetu)."&nbsp;(GMT".($row["time_offset"] > 0 ? " + ".$row["time_offset"] : ($row["time_offset"] == 0 ? "" : " ".$row["time_offset"])).")</td></tr>\n");
print("<tr>\n<td class='header'>".DOWNLOADED."</td>\n<td class='lista' colspan='2'>".misc::makesize($user_stats["downloaded"])."</td></tr>\n");
print("<tr>\n<td class='header'>".UPLOADED."</td>\n<td class='lista' colspan='2'>".misc::makesize($user_stats["uploaded"])."</td></tr>\n");

if ($user_stats["downloaded"] > 0) {
    $sr = $user_stats["uploaded"] / $user_stats["downloaded"];

    if ($sr >= 4)
        $s = "images/smilies/smilebig.svg";
    else if ($sr >= 2)
        $s = "images/smilies/smilegrin.svg";
    else if ($sr >= 1)
        $s = "images/smilies/smile.svg";
    else if ($sr >= 0.5)
        $s = "images/smilies/plain.svg";
    else if ($sr >= 0.25)
        $s = "images/smilies/sad.svg";
    else
        $s = "images/smilies/crying.svg";

    $ratio = number_format($sr, 2)."&nbsp;&nbsp;<img src='".$s."'>";
} else
    $ratio = "&infin;";

print("<tr>\n<td class='header'>".RATIO."</td>\n<td class='lista' colspan='2'>".$ratio."</td></tr>\n");

// Only show if forum is internal
if ($GLOBALS["FORUMLINK"] == '' || $GLOBALS["FORUMLINK"] == 'internal') {
    $posts = MCached::get('user::forum::posts::' . $id);
    if ($posts === MCached::NO_RESULT) {
        $sql = $db->query("SELECT * FROM posts INNER JOIN users ON posts.userid = users.id WHERE users.id = " . $id);
        $posts = $sql->num_rows;
        MCached::add('user::forum::posts::' . $id, $posts, 43200);
    }
    $memberdays = max(1, round((vars::$timestamp - $row['joined']) / 86400 ));
    $posts_per_day = number_format(round($posts / $memberdays, 2), 2);

    print("<tr>\n<td class='header'>".FORUM." ".POSTS."</td>\n<td class='lista' colspan='2'>" . $posts . " &nbsp; [" . sprintf(POSTS_PER_DAY, $posts_per_day) . "]</td></tr>\n");
}

//User Warning System Hack Start
if ($GLOBALS['warn_system'] == 'yes') {
    if (user::$current['uid'] == $id || user::$current['edit_users'] == 'yes' || user::$current['admin_access'] == 'yes') {
        $get_warns_stats = $db->query("SELECT * FROM warnings WHERE userid = " . $id . " AND active = 'yes'");
        $warnings = $get_warns_stats->fetch_array(MYSQLI_BOTH);
        $warningsnum = $get_warns_stats->num_rows;

        $get_latest_reason = $db->query("SELECT reason FROM warnings WHERE userid = " . $id . " ORDER BY added DESC LIMIT 1");
        $warn_reason = $get_latest_reason->fetch_assoc();

        $get_staff_username_stats = $db->query("SELECT username FROM users WHERE id = '" . (int)$warnings['addedby'] . "'");
        $warnedby = $get_staff_username_stats->fetch_array(MYSQLI_BOTH);

        $get_disabled_username_stats = $db->query("SELECT id, username FROM users WHERE id = " . (int)$row['disabledby']);
        $disabledby = $get_disabled_username_stats->fetch_array(MYSQLI_BOTH);

        $remover = $db->query("SELECT username FROM users WHERE id = " . (int)$row['warnremovedby']);
        $warnremovedby = $remover->fetch_array(MYSQLI_BOTH);

        //begin warning stats
        print("<tr>\n<td class='header' align='center' colspan='3'><b><font color='red'>Warning Stats</font></b></td></tr>\n");

        //don't show link to warns page if user has no warns
        if ($row['warns'] == 0)
            $total_warns = (int)$row['warns'];
        else
            $total_warns = "<a href='listwarns.php?uid=" . $id . "'>" . (int)$row['warns'] . "</a>";

        //show the total number of warns
        print("<tr>\n<td class='header'>Total # of Warnings</td>\n<td class='lista' colspan='2'>" . $total_warns . "</td></tr>\n");

        //don't show warn stats if user has no warns
        if ($warningsnum == 0) {
            if ($row['warnremovedby'] == 0) {
            } else {
                //latest warning removed by start
                print("<tr>\n<td class='header'>Latest Warning removed by</td>\n<td class='lista' colspan='2'><a href='userdetails.php?id=" . (int)$row['warnremovedby'] . "'>" . security::html_safe($warnremovedby['username']) . "</a></td></tr>\n");

                //latest warning reason
                print("<tr>\n<td class='header'>Latest Warning reason</td><td class='lista' colspan='2'>" . security::html_safe($warn_reason['reason']) . "</td></tr>\n");
            }
        } else {
            //received warning duration
            if ($warnings['warnedfor'] == 0)
                $duration = 'Unlimited Warn';
            elseif ($warnings['warnedfor'] == 1)
                $duration = $warnings['warnedfor'] . ' week warn';
            else
                $duration = $warnings['warnedfor'] . ' weeks warn';

            print("<tr>\n<td class='header'>Current Warning duration</td>\n<td class='lista' colspan='2'>" . $duration . "</td></tr>\n");

            //received warning time-frame
            if ($warnings['warnedfor'] == 0)
                $period = "This type of warning doesn't have a time frame. It's permanent";
            else
                $period = $warnings['added'] . ' - ' . $warnings['expires'];

            print("<tr>\n<td class='header'>Current Warning period</td>\n<td class='lista' colspan='2'>" . $period . "</td></tr>\n");

            //shows warn reason
            print("<tr>\n<td class='header'>Current Warning reason</td>\n<td class='lista' colspan='2'>" . security::html_safe($warnings['reason']) . "</td></tr>\n");

            //shows who added the warn
            print("<tr>\n<td class='header'>Current Warn by</td>\n<td class='lista' colspan='2'><a href='userdetails.php?id=" . (int)$warnings['addedby'] . "'>" . security::html_safe($warnedby['username']) . "</a></td></tr>\n");
        }

        //check to see if account is disabled
        if ($row['disabled'] == 'yes') {
            print("<tr>\n<td class='header'>Account Disabled</td>\n<td class='lista' colspan='2'><font color='red'>Yes</font></td></tr>\n");
            print("<tr>\n<td class='header'>Disabled by</td>\n<td class='lista' colspan='2'><a href='userdetails.php?id=" . (int)$disabledby['id'] . "'>" . security::html_safe($disabledby['username']) . "</a></td></tr>\n");
            print("<tr>\n<td class='header'>Disabled on</td>\n<td class='lista' colspan='2'>" . $row['disabledon'] . "</td></tr>\n");
            print("<tr>\n<td class='header'>Disable Reason</td>\n<td class='lista' colspan='2'>" . security::html_safe($row['disabledreason']) . "</td></tr>\n");
        } else
            print("<tr>\n<td class='header'>Account Disabled</td>\n<td class='lista' colspan='2'><font color='green'>No</font></td></tr>\n");
    } else {
        #Do Nothing...
    }

    //display the WarnLevel
    $prgsf = (int)$row['warns'];

    //display percentage of warn level
    $tmp = 0 + $warntimes;
    if ($tmp > 0) {
        $wcurr = (0 + (int)$row['warns']);
        $prgs = ($wcurr / $tmp) * 100;
        $prgsfs = floor($prgs);
    } else
        $prgsfs = 0;
        $prgsfs .= '%';

    $wl1 = $warntimes / 4;
    $wl2 = $warntimes / 3;
    $wl3 = $warntimes / 2;
    $wl4 = $warntimes;

    if ($prgsf == 0)
        $warnlevel = image_or_link('images/progress-0.gif', 'title=' . $prgsfs . '');
    if ($prgsf > 0 && $prgsf <= $wl1)
        $warnlevel = image_or_link('images/progress-1.gif', 'title=' . $prgsfs . '');
    if ($prgsf > $wl1 && $prgsf <= $wl2)
        $warnlevel = image_or_link('images/progress-2.gif', 'title=' . $prgsfs . '');
    if ($prgsf > $wl2 && $prgsf <= $wl3)
        $warnlevel = image_or_link('images/progress-3.gif', 'title=' . $prgsfs . '');
    if ($prgsf > $wl3 && $prgsf < $wl4)
        $warnlevel = image_or_link('images/progress-4.gif', 'title=' . $prgsfs . '');
    if ($prgsf >= $wl4)
        $warnlevel = image_or_link('images/progress-5.gif', 'title=' . $prgsfs . '');

    print("<tr><td class='header'>Warn Level</td><td class='lista' colspan='2'>" . $warnlevel . "</td>");
}
//user Warning System Hack Stop

//Admin Controls Hack start
if (user::$current['edit_users'] == 'yes' || user::$current['admin_access'] == 'yes') {
   print("<tr>\n<td class='header' align='center' colspan='3'><b>Admin Controls</b></td></tr>\n");

   //Custom Title Hack Start
   if ($GLOBALS['custom_title'] == 'yes') {
       if (!$row['custom_title'])
           $custom = '';
       else
           $custom = security::html_safe($row['custom_title']);

       print("<tr>\n<td class='header'>" . CUSTOM_TITLE . "</td>\n<td class='lista' colspan='1'>");
       print("<form method='post' action='title.php?action=changetitle&uid=" . unesc($id) . "&returnto=userdetails.php?id=" . unesc($id) . "'>");
       print("<input type='text' name='title' size='36' maxlength='50' value='" . unesc($custom) . "'><input type='hidden' name='username' size='4' value='" . security::html_safe($get_user['username']) . "' readonly></td>");
       print("<td class='lista' align='center'><input type='submit' value='" . FRM_CONFIRM . "'>&nbsp;&nbsp;<input type='reset' value='" . FRM_RESET . "'>");
       print("</form>");
       print("</td></tr>\n");
   }
   //Custom Title Hack Stop
   //User Warning System Hack Start
   if ($GLOBALS['warn_system'] == 'yes') {
       if ($row['warns'] >= 1)
           print("<tr>\n<td class='header'>Reset Warn Level</td>\n<td class='lista' colspan='2'>" . REPORT_CLICK . " <a onclick=\"return confirm('" . AddSlashes(WARN_LEVEL_RESET) . "')\" href='warn.php?action=resetwarnlevel&uid=" . unesc($id) . "&returnto=userdetails.php?id=" . unesc($id) . "'>" . HERE . "</a></td>\n");

       if ($warningsnum == 0) {
           print("<tr>\n<td class='header'>Warn this User<br><font size='1' color='green'>Warn period and Reason</font></td>\n<td class='lista' colspan='1'>");
           print("<form method=post action=warn.php?action=warn&id=".unesc($id)."&returnto=userdetails.php?id=".unesc($id).">\n");
           print("<select name='warnfor' size='1'>\n");
           print("<option value='7' selected='selected'>" . ONE_WEEK . "</option>\n");
           print("<option value='14'>" . TWO_WEEKS . "</option>\n");
           print("<option value='21'>" . THREE_WEEKS . "</option>\n");
           print("<option value='28'>" . FOUR_WEEKS . "</option>\n");
           print("<option value='0'>" . PERMANENTLY . "</option>\n");
           print("</select>&nbsp;<input type='hidden' name='username' size='10' value='" . security::html_safe($get_user['username']) . "' readonly><input type='text' name='reason' size='14' maxlength='250'></td><td class='lista' align='center'><input type='submit' value='" . FRM_CONFIRM . "' onclick=\"return confirm('" . AddSlashes(WARN_CONFIRM) . "')\">&nbsp;&nbsp;<input type='reset' value='" . FRM_RESET . "'>\n");
           print("</form>\n");
           print("</td></tr>\n");
       } else
           print("<tr>\n<td class='header'>Remove Warn</td>\n<td class='lista' colspan='2'>" . REPORT_CLICK . " <a onclick=\"return confirm('" . AddSlashes(WARN_REMOVE) . "')\" href='warn.php?action=removewarn&id=" . $id . "&username=" . unesc($get_user['username']) . "&remover=" . user::$current['uid'] . "&returnto=userdetails.php?id=" . unesc($id) . "'>" . HERE . "</a></td></tr>\n");
       //User Warning System Hack Stop
       //Disable Start...
       print("<form method='post' action='warn.php?action=disable&returnto=userdetails.php?id=" . $id . "'>\n");
       ?>
       <tr>
          <td class='header'><?php echo DISABLE_ACCOUNT ?></td>
          <td class='lista'>
             <select name='disable' size='1'>
                <option value="yes"<?php if ($row['disabled'] == 'yes') echo ' selected'?>><?php echo WARN_TRUE; ?></option><option value="no"<?php if ($row['disabled'] == 'no') echo ' selected' ?>><?php echo WARN_FALSE; ?></option>
             </select>
          <?php
          if ($row['disabled'] == 'no') {
              print("<input type='text' name='reason' size='20'>\n");
              print("<input type='hidden' name='name' value='" . security::html_safe($get_user['username']) . "'>\n");
              print("<input type='hidden' name='id' value='" . $id . "'></td>\n");
          }
          ?>
          <td class='lista' align='center'><input type='submit' value="<?php echo FRM_CONFIRM ?>" onclick=\"return confirm(<?php echo "'" . AddSlashes(WARN_DISABLE_ACCOUNT) . "')" ?>\"><input type='reset' value="<?php echo FRM_RESET ?>"></td>
       </tr>
       <?php
       print("</form>\n");
   }
   //Account Disable Hack Stop...

} else {
  #Do Nothing...
}
//Admin Controls Hack stop

#Timed Ranks by Diemthuy Adapted by Yupy for BtitTracker
if ($GLOBALS['timed_ranks'] == 'yes') {
    if (user::$current['admin_access'] == 'yes' && user::$current['edit_users'] == 'yes') {
        $res4 = $db->query("SELECT level, prefixcolor, suffixcolor FROM users_level WHERE id = '" . (int)$row['old_rank'] . "'") or sqlerr(__FILE__, __LINE__);
        $arr4 = $res4->fetch_assoc();

        $oldrank = unesc($arr4['prefixcolor']) . security::html_safe($arr4['level']) . unesc($arr4['sufixcolor']);

        $opts['name'] = 'level';
        $opts['complete'] = true;
        $opts['id'] = 'id';
        $opts['value'] = 'level';
        $opts['default'] = (int)$row['id_level'];
        $ranks = Cached::rank_list();

        print("<form method='post' action='timedrank.php?id=" . $id . "'>");
        print("<input type='hidden' name='returnto' value='userdetails.php?id=" . $id . "'>");
        print("<tr>
                 <td class='header' colspan='3' align='center'><b>Timed Rank Settings</b></td>
              </tr>");
        print("<tr>
                 <td class='header'>New Rank</td>
                 <td class='lista' colspan='2'>" . get_combodt($ranks, $opts) . "</td>
              </tr>");
        print("<tr>
                 <td class='header'>Old Rank</td>
                 <td class='lista' colspan='2'>" . $oldrank . "</td>
              </tr>");
        print("<tr>
                 <td class='header'>Expire Time</td>
                 <td class='lista'><select name='t_days'>
                     <option value='7'>1 Week</option>
                     <option value='35'>5 Week's</option>
                     <option value='70'>10 Week's</option>
                     <option value='140'>20 Week's</option>
                     <option value='210'>30 Week's</option>
                     <option value='280'>40 Week's</option>
                     <option value='350'>50 Weeks</option>
                     <option value='31'>1 Month</option>
                     <option value='182'>&frac12; Year</option>
                     <option value='365'>1 Year</option>
                     <option value='730'>2 Year's</option>
                 </select></td>
                 <td class='lista' valign='middle'><center><input type='submit' class='btn' value='Update'></center></td>
              </tr>");
        print("</form>");
    }
}

print("</table>");

#Uploaded Torrents
block_begin(UPLOADED." ".MNU_TORRENT);

$resuploaded = $db->query("SELECT namemap.info_hash FROM namemap INNER JOIN summary ON namemap.info_hash = summary.info_hash WHERE uploader = ".$id." AND namemap.anonymous = 'false' ORDER BY data DESC");
$numtorrent = $resuploaded->num_rows;

if ($numtorrent > 0) {
    list($pagertop, $limit) = misc::pager(($utorrents == 0 ? 15 : $utorrents), $numtorrent, security::esc_url($_SERVER["PHP_SELF"])."?id=".$id."&");
    print($pagertop);

    $resuploaded = $db->query("SELECT namemap.info_hash, namemap.filename, UNIX_TIMESTAMP(namemap.data) AS added, namemap.size, summary.seeds, summary.leechers, summary.finished FROM namemap INNER JOIN summary ON namemap.info_hash = summary.info_hash WHERE uploader = ".$id." AND namemap.anonymous = 'false' ORDER BY data DESC ".$limit);
}

?>
<table width='100%' class='lista'>
<!-- Column Headers  -->
<tr>
    <td align='center' class='header'><?php echo FILE; ?></td>
    <td align='center' class='header'><?php echo ADDED; ?></td>
    <td align='center' class='header'><?php echo SIZE; ?></td>
    <td align='center' class='header'><?php echo SHORT_S; ?></td>
    <td align='center' class='header'><?php echo SHORT_L; ?></td>
    <td align='center' class='header'><?php echo SHORT_C; ?></td>
</tr>

<?php

if ($resuploaded && $resuploaded->num_rows > 0) {
    while ($rest = $resuploaded->fetch_array(MYSQLI_BOTH)) {
        print("\n<tr>\n<td class='lista'><a href='details.php?id=".$rest{"info_hash"}."'>".security::html_safe(unesc($rest["filename"]))."</td>");
	
        include(INCL_PATH . 'offset.php');
        print("\n<td class='lista' align='center'>".date("d/m/Y H:m:s", $rest["added"] - $offset)."</td>");
        print("\n<td class='lista' align='center'>".misc::makesize((int)$rest["size"])."</td>");
        print("\n<td align='center' class='".linkcolor($rest["seeds"])."'><a href='peers.php?id=".$rest{"info_hash"}."'>".(int)$rest['seeds']."</td>");
        print("\n<td align='center' class='".linkcolor($rest["leechers"])."'><a href='peers.php?id=".$rest{"info_hash"}."'>".(int)$rest['leechers']."</td>");

        if ($rest["finished"] > 0)
            print("\n<td align='center' class='lista'><a href='torrent_history.php?id=".$rest["info_hash"]."'>".(int)$rest["finished"]."</a></td>");
        else
            print("\n<td align='center' class='lista'>---</td>");
    }
    print("\n</table>");
} else {
    print("<tr>\n<td class='lista' align='center' colspan='6'>".NO_TORR_UP_USER."</td>\n</tr>\n</table>");
}

block_end();
#End Uploaded Torrents

#Active Torrents - hack by petr1fied - modified by Lupin 20/10/05
block_begin("Active torrents");

?>
<table width='100%' class='lista'>
<!-- Column Headers  -->
<tr>
    <td align='center' class='header'><?php echo FILE; ?></td>
    <td align='center' class='header'><?php echo SIZE; ?></td>
    <td align='center' class='header'><?php echo PEER_STATUS; ?></td>
    <td align='center' class='header'><?php echo DOWNLOADED; ?></td>
    <td align='center' class='header'><?php echo UPLOADED; ?></td>
    <td align='center' class='header'><?php echo RATIO; ?></td>
    <td align='center' class='header'>S</td>
    <td align='center' class='header'>L</td>
    <td align='center' class='header'>C</td>
</tr>

<?php

if ($PRIVATE_ANNOUNCE)
    $anq = $db->query("SELECT peers.ip FROM peers INNER JOIN namemap ON namemap.info_hash = peers.infohash INNER JOIN summary ON summary.info_hash = peers.infohash WHERE peers.pid = '".$db->real_escape_string($row["pid"])."'");
else
    $anq = $db->query("SELECT peers.ip FROM peers INNER JOIN namemap ON namemap.info_hash = peers.infohash INNER JOIN summary ON summary.info_hash = peers.infohash WHERE peers.ip = '".$db->real_escape_string($row["cip"])."'");

if ($anq->num_rows > 0) {
    list($pagertop, $limit) = misc::pager(($utorrents == 0 ? 15 : $utorrents), $anq->num_rows, security::esc_url($_SERVER["PHP_SELF"])."?id=".$id."&", array("pagename" => "activepage"));

	if ($PRIVATE_ANNOUNCE)
        $anq = $db->query("SELECT peers.ip, peers.infohash, namemap.filename, namemap.size, peers.status, peers.downloaded, peers.uploaded, summary.seeds, summary.leechers, summary.finished
                    FROM peers INNER JOIN namemap ON namemap.info_hash = peers.infohash INNER JOIN summary ON summary.info_hash = peers.infohash
                    WHERE peers.pid = '".$db->real_escape_string($row["pid"])."' ORDER BY peers.status DESC ".$limit);
    else
        $anq = $db->query("SELECT peers.ip, peers.infohash, namemap.filename, namemap.size, peers.status, peers.downloaded, peers.uploaded, summary.seeds, summary.leechers, summary.finished
                    FROM peers INNER JOIN namemap ON namemap.info_hash = peers.infohash INNER JOIN summary ON summary.info_hash = peers.infohash
                    WHERE peers.ip = '".$db->real_escape_string($row["cip"])."' ORDER BY peers.status DESC ".$limit);

    print("<div align='center'>".$pagertop."</div>");

    while ($torlist = $anq->fetch_object()) {
        if ($torlist->ip != '') {
            print("\n<tr>\n<td class='lista'><a href='details.php?id=".$torlist->infohash."'>".security::html_safe(unesc($torlist->filename))."</td>");
            print("\n<td class='lista' align='center'>".misc::makesize((int)$torlist->size)."</td>");
            print("\n<td align='center' class='lista'>".unesc($torlist->status ? 'Seeder' : 'Leecher')."</td>");
            print("\n<td align='center' class='lista'>".misc::makesize((float)$torlist->downloaded)."</td>");
            print("\n<td align='center' class='lista'>".misc::makesize((float)$torlist->uploaded)."</td>");
	
            if ($torlist->downloaded > 0)
                $peerratio = number_format((float)$torlist->uploaded / (float)$torlist->downloaded, 2);
            else
                $peerratio = "&infin;";

            print("\n<td align='center' class='lista'>".unesc($peerratio)."</td>");
            print("\n<td align='center' class='".linkcolor($torlist->seeds)."'><a href='peers.php?id=".$torlist->infohash."'>".(int)$torlist->seeds."</td>");
            print("\n<td align='center' class='".linkcolor($torlist->leechers)."'><a href='peers.php?id=".$torlist->infohash."'>".(int)$torlist->leechers."</td>");
            print("\n<td align='center' class='lista'><a href='torrent_history.php?id=".$torlist->infohash."'>".(int)$torlist->finished."</td>\n</tr>");
        }
    }
    print("\n</table>");
}
else
    print("<tr>\n<td class='lista' align='center' colspan='9'>This user has no Active Torrents</td>\n</tr>\n</table>");

block_end();
#End Active Torrents

# History - completed torrents by this user
block_begin("History (Snatched Torrents)");

?>
<table width='100%' class='lista'>
<!-- Column Headers  -->
<tr>
    <td align='center' class='header'><?php echo FILE; ?></td>
    <td align='center' class='header'><?php echo SIZE; ?></td>
    <td align='center' class='header'><?php echo PEER_CLIENT; ?></td>
    <td align='center' class='header'><?php echo PEER_STATUS; ?></td>
    <td align='center' class='header'><?php echo DOWNLOADED; ?></td>
    <td align='center' class='header'><?php echo UPLOADED; ?></td>
    <td align='center' class='header'><?php echo RATIO; ?></td>

    <?php if ($GLOBALS['hit_and_run'] == 'yes') { ?>
    <td align='center' class='header'>Hit &amp; Run</td>
    <?php } ?>

    <?php if ($GLOBALS['seed_time'] == 'yes') { ?>
    <td align='center' class='header'><?php echo SEED_TIME; ?></td>
    <?php } ?>

    <td align='center' class='header'>S</td>
    <td align='center' class='header'>L</td>
    <td align='center' class='header'>C</TD>
</tr>

<?php

$anq->free();
$anq = $db->query("SELECT history.uid FROM history INNER JOIN namemap ON history.infohash = namemap.info_hash WHERE history.uid = ".$id." AND history.date IS NOT NULL ORDER BY date DESC");

if ($anq->num_rows > 0) {
    list($pagertop, $limit) = misc::pager(($utorrents == 0 ? 15 : $utorrents), $anq->num_rows, security::esc_url($_SERVER["PHP_SELF"])."?id=".$id."&", array("pagename" => "historypage"));
 
	$anq = $db->query("SELECT namemap.filename, namemap.size, namemap.info_hash, history.active, history.hit, history.seedtime, history.agent, history.downloaded, history.uploaded, summary.seeds, summary.leechers, summary.finished
    FROM history INNER JOIN namemap ON history.infohash = namemap.info_hash INNER JOIN summary ON summary.info_hash = namemap.info_hash WHERE history.uid = ".$id." AND history.date IS NOT NULL ORDER BY date DESC ".$limit);

	print("<div align='center'>".$pagertop."</div>");

	while ($torlist = $anq->fetch_object()) {
               print("\n<tr>\n<td class='lista'><a href='details.php?id=".$torlist->info_hash."'>".security::html_safe(unesc($torlist->filename))."</td>");
               print("\n<td class='lista' align='center'>".misc::makesize((int)$torlist->size)."</td>");
               print("\n<td class='lista' align='center'>".security::html_safe($torlist->agent)."</td>");
               print("\n<td align='center' class='lista'>".($torlist->active == 'yes' ? ACTIVATED : 'Stopped')."</td>");
               print("\n<td align='center' class='lista'>".misc::makesize((float)$torlist->downloaded)."</td>");
               print("\n<td align='center' class='lista'>".misc::makesize((float)$torlist->uploaded)."</td>");

               if ($torlist->downloaded > 0)
                   $peerratio = number_format((float)$torlist->uploaded / (float)$torlist->downloaded, 2);
               else
                   $peerratio = "&infin;";

               print("\n<td align='center' class='lista'>".unesc($peerratio)."</td>");

               if ($GLOBALS['hit_and_run'] == 'yes') {
                   print("\n<td align='center' class='lista'>".($torlist->hit == 'yes' ? 'Yes' : 'No')."</td>");
               }

              #Seeding Time By Yupy Start...
              if ($GLOBALS['seed_time'] == 'yes') {
                  if ($torlist->seedtime >= 36000) $seedtime = "<font color='lime'>";
                  else if ($torlist->seedtime >= 25200) $seedtime = "<font color='yellow'>";
                  else if ($torlist->seedtime >= 14400) $seedtime = "<font color='orange'>";
                  else if ($torlist->seedtime >= 3600) $seedtime = "<font color='red'>";
                  else if ($torlist->seedtime > 0) $seedtime = "<font color='limegreen'>";
                  else if ($torlist->seedtime==0) $seedtime = "<font color='black'>0";
                  else $seedtime = "<font color='black'>";

                  $mins = floor($torlist->seedtime / 60);
                  $hours = floor($mins / 60);
                  $mins -= $hours * 60;
                  $days = floor($hours / 24);
                  $hours -= $days * 24;
                  $weeks = floor($days / 7);
                  $days -= $weeks * 7;
                  $secs = number_format(((($torlist->seedtime / 60) - $mins) * 60 - $hours * 60 * 60 - $days * 24 * 60 * 60 - $weeks * 7 * 24 * 60 * 60), 0);

                  if ($weeks > 0) $seedtime .= " $weeks"."W ";
                  if ($days > 0) $seedtime .= " $days"."D ";
                  if ($hours > 0) $seedtime .= " $hours"."h";
                  if ($mins > 0) $seedtime .= " $mins"."m";
                  if ($secs > 0) $seedtime .= " $secs"."s";

                  $seedtime .= "</font>";
                  print("\n<td align='center' class='lista'>" . $seedtime . "</td>");
              }
              #Seeding Time By Yupy End (thanks to Petr1fied & CobraCrk for parts of code)

               print("\n<td align='center' class='".linkcolor($torlist->seeds)."'><a href='peers.php?id=".$torlist->info_hash."'>".(int)$torlist->seeds."</td>");
               print("\n<td align='center' class='".linkcolor($torlist->leechers)."'><a href='peers.php?id=".$torlist->info_hash."'>".(int)$torlist->leechers."</td>");
               print("\n<td align='center' class='lista'><a href='torrent_history.php?id=".$torlist->info_hash."'>".(int)$torlist->finished."</td>\n</tr>");
	}
    print("\n</table>");
} else
    print("<tr>\n<td class='lista' align='center' colspan='11'>No history for this user</td>\n</tr>\n</table>");

block_end();
#End Torrents History

print("<br /><br /><center><a href='javascript: history.go(-1);'>".BACK."</a></center><br />\n");

block_end();
stdfoot();

?>
