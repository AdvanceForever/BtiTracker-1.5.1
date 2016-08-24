<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */

global $STYLEPATH;

standardheader('Hit &amp; Run Settings');

block_begin('Hit &amp; Run Settings');

if (!user::$current || user::$current['admin_access'] != 'yes') {
    err_msg(ERROR, ERR_NOT_AUTH);
    block_end();
    stdfoot();
    exit;
}

// Get the DO and ID
if (isset($_GET['do'])) {
    $do = security::html_safe($_GET['do']);
} else {
    $do = '';
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    $id = '';
}

// Here we will update data into db
if ($do == 'update') {
    $DT1 = $db->real_escape_string($_POST['reward'] ? 'yes' : 'no');
    $DT2 = (int) $_POST['min_download_size'];
    $DT3 = (float) $_POST['min_ratio'];
    $DT4 = (int) $_POST['min_seed_hours'];
    $DT5 = (int) $_POST['tolerance_days'];
    $DT6 = (int) $_POST['upload_punishment'];
    
    $db->query("UPDATE `anti_hit_run` SET `reward` = '" . $DT1 . "', `min_download_size` = '" . $DT2 . "', `min_ratio` = '" . $DT3 . "' , `min_seed_hours` = '" . $DT4 . "', `tolerance_days_before_punishment` = '" . $DT5 . "' , `upload_punishment` = '" . $DT6 . "' WHERE `id_level` = '" . $id . "'") or sqlerr();
    
    print("<br /><br /><div align='center'><font color='red'><h3>Updated !</h3></font><br /><br /><a href='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun'>Go back</a><br /><br /></div>");
} else if ($do == "add") {
    //Get/post reward
    $DT1 = $db->real_escape_string($_POST["reward"] ? "yes" : "no");
    //Get/post min downloaded size
    $DT2 = (int) $_POST['min_download_size'];
    //Get/post min ratio
    $DT3 = (float) $_POST['min_ratio'];
    //Get/post min seed hours
    $DT4 = (int) $_POST['min_seed_hours'];
    // Get/post tolerance days
    $DT5 = (int) $_POST['tolerance_days'];
    // Get/post upload punishment
    $DT6 = (int) $_POST['upload_punishment'];
    // Get/post id
    $DT7 = (int) $_POST['id_level'];
    
    $check = $db->query("SELECT * FROM anti_hit_run WHERE id_level = '" . $DT7 . "'") or sqlerr();
    $checkres = $check->num_rows;
    
    if ($checkres > 0) {
        print("<br /><br /><div align='center'><font color='red'><h3>You can't add 2 rules for one Group !</h3></font><br /><br /><a href='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun'>Go back</a><br><br></div>");
    } else {
        $db->query("INSERT INTO anti_hit_run (reward, min_download_size, min_ratio, min_seed_hours, tolerance_days_before_punishment, upload_punishment, id_level) VALUES ('" . $DT1 . "', '" . $DT2 . "', '" . $DT3 . "', '" . $DT4 . "', '" . $DT5 . "', '" . $DT6 . "', '" . $DT7 . "')") or sqlerr();
        
        print("<br /><br /><div align='center'><font color='red'><h3>Added !</h3></font><br /><br /><a href='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun'>Go back</a><br><br></div>");
    }
} else if ($do == "new") {
    print("<form name='hitrun' action='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun&do=add' method='post'>");
    print("<table align='center' width='85%' cellspacing='0' cellpadding='4'>");
    print("<tr>");
    print("<td class='header' align='center' colspan='2'>Hit &amp; Run Settings</td>");
    print("</tr>");
    print("<tr><td class='header' width='60%'>ID Level<br /><small>ID level is the ID of the Group you want to apply this rules for.</small></td><td class='lista'><input type='text' size='3' name='id_level' maxlength='5' /></td></tr>");
    print("<tr><td class='header' width='60%'>Min Download Size<br /><small>Min Download Size is the Minimum Download in order to apply the punishment in MiB</small></td><td class='lista'><input type='text' size='3' name='min_download_size' maxlength='5' /></td></tr>");
    print("<tr><td class='header' width='60%'>Min Ratio<br /><small>Min Ratio is the Minimum Ratio of the Torrent in order to avoid the punishment (like 1.00 )</small></td><td class='lista'><input type='text' size='4' name='min_ratio' maxlength='6' /></td></tr>");
    print("<tr><td class='header' width='60%'>Min Seed Hours<br /><small>Min Seed Hours is the Minimum Seeding Hours in order to avoid the punishment (In case that the Ratio is not reaching the Min Ratio you set)</small></td><td class='lista'><input type='text' size='4' name='min_seed_hours' maxlength='6' /></td></tr>");
    print("<tr><td class='header' width='60%'>Tolerance Days<br /><small>Tolerance Days is the Maximum Tolerance in Days for Completed Torrents before applying the punishment (like 14 = two weeks)</small></td><td class='lista'><input type='text' size='4' name='tolerance_days' maxlength='6' /></td></tr>");
    print("<tr><td class='header' width='60%'>Upload Punishment<br><small>Upload Punishment is the Amount of Decrement in MiB from the Total Upload Amount if punishment applied</small></td><td class='lista'><input type='text' size='4' name='upload_punishment' maxlength='6' /></td></tr>");
    print("<tr><td class='header' width='60%'>Reward<br /><small>Reward represents if this Group is eligible for restore lost Amount after Seeding according to the conditions</small></td><td class='lista'><input type='checkbox' name='reward' /></td></tr>");
    print("<tr><td colspan='2' class='lista' style='text-align:center;'><input type='submit' class='mini ui button' name='action' value='Add' /></td></tr>");
    print("</table></form>");
} else if ($do == "edit") {
    $subres = $db->query("SELECT id_level, min_download_size, min_ratio, min_seed_hours, tolerance_days_before_punishment, upload_punishment, reward FROM anti_hit_run WHERE id_level = '" . $id . "' ORDER BY id_level DESC");
    
    while ($subrow = $subres->fetch_array(MYSQLI_BOTH)) {
        print("<form name='hitrun' action='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun&do=update&id=" . (int) $subrow["id_level"] . "' method='post'>");
        print("<table align='center' width='85%' cellspacing='0' cellpadding='4'>");
        print("<tr>");
        print("<td class='header' align='center' colspan='2'>Hit &amp; Run Settings</td>");
        print("</tr>");
        print("<tr><td class='header' width='60%'>ID Level<br /><small>ID level is the ID of the Group you want to apply this rules for</small></td><td class='lista'><input type='text' size='3' name='id_level' maxlength='5' value='" . (int) $subrow["id_level"] . "' disabled='disabled' /></td></tr>");
        print("<tr><td class='header' width='60%'>Min Download Size<br /><small>Min Download size is the Minimum Download in order to apply the punishment in MiB</small></td><td class='lista'><input type='text' size='3' name='min_download_size' maxlength='5' value='" . (int) $subrow["min_download_size"] . "' /></td></tr>");
        print("<tr><td class='header' width='60%'>Min Ratio<br /><small>Min Ratio is the Minimum Ratio of the Torrent in order to avoid the punishment (like 1.00 )</small></td><td class='lista'><input type='text' size='4' name='min_ratio' maxlength='6' value='" . (float) $subrow["min_ratio"] . "' /></td></tr>");
        print("<tr><td class='header' width='60%'>Min Seed Hours<br /><small>Min Seed Hours is the Minimum Seeding Hours in order to avoid the punishment (In case that the Ratio is not reaching the Min Ratio you set)</small></td><td class='lista'><input type='text' size='4' name='min_seed_hours' maxlength='6' value='" . (int) $subrow["min_seed_hours"] . "' /></td></tr>");
        print("<tr><td class='header' width='60%'>Tolerance Days<br /><small>Tolerance Days is the Maximum Tolerance in Days for completed Torrents before applying the punishment (like 14 = two weeks)</small></td><td class='lista'><input type='text' size='4' name='tolerance_days' maxlength='6' value='" . (int) $subrow["tolerance_days_before_punishment"] . "' /></td></tr>");
        print("<tr><td class='header' width='60%'>Upload Punishment<br /><small>Upload Punishment is the Amount of Decrement in MiB from the Total Upload Amount if punishment applied</small></td><td class='lista'><input type='text' size='4' name='upload_punishment' maxlength='6' value='" . (int) $subrow["upload_punishment"] . "' /></td></tr>");
        
        if ($subrow["reward"] == "yes") {
            $chk = " checked='checked' ";
        }
        
        print("<tr><td class='header' width='60%'>Reward<br /><small>Reward represents if this Group is eligible for restore lost Amount after Seeding according to the conditions</small></td><td class='lista'><input type='checkbox' name='reward'" . $chk . " value='1' /></td></tr>");
        print("<tr><td colspan='2' class='lista' style='text-align:center;'><input type='submit' class='mini ui button' name='action' value='Update' /></td></tr>");
        print("</table></form>");
    }
} else if ($do == "delete") {
    $db->query("DELETE FROM anti_hit_run WHERE id_level = '" . $id . "'") or sqlerr();
    
    print("<br /><br /><div align='center'><font color='red'><h3>Deleted !</h3></font><br /><br /><a href='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun'>Go back</a><br /><br /></div>");
} else {
    print("<br />");
    print("<center>Here you can see all rules for the Hit and Run !</center>");
    print("<table align='center' width='85%'>");
    print("<tr><td><form name='add' action='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun&do=new' method='post'><input type='submit' class='mini ui button' name='new' value='Add' /></form></td></tr>");
    
    print("<tr>");
    print("<td class='block' align='center'>ID Level</td>");
    print("<td class='block' align='center'>User Group</td>");
    print("<td class='block' align='center'>Min Download</td>");
    print("<td class='block' align='center'>Min Ratio</td>");
    print("<td class='block' align='center'>Min Seed Time</td>");
    print("<td class='block' align='center'>Tolerance Days</td>");
    print("<td class='block' align='center'>Upload Punishment</td>");
    print("<td class='block' align='center'>Reward</td>");
    print("<td class='block' align='center'>Edit</td>");
    print("<td class='block' align='center'>Delete</td>");
    print("</tr>");
    
    //Here we will select the data from the table hit and run
    $res = $db->query("SELECT anti_hit_run.id_level, users_level.id, users_level.level, anti_hit_run.min_download_size, anti_hit_run.min_ratio, anti_hit_run.min_seed_hours, anti_hit_run.tolerance_days_before_punishment, anti_hit_run.upload_punishment, anti_hit_run.reward FROM anti_hit_run INNER JOIN users_level ON anti_hit_run.id_level = users_level.id ORDER BY anti_hit_run.id_level ASC") or sqlerr();
    $res2 = $db->query("SELECT * FROM anti_hit_run ORDER BY id_level") or sqlerr();
    $num = $res2->num_rows;
    
    if ($num == "0")
        print("<tr><td class='lista' align='center' colspan='9'>Could not find any Rules !</td></tr>");
    else {
        while ($row = $res->fetch_array(MYSQLI_BOTH)) {
            print("<tr>");
            print("<td class='lista' align='center'>" . (int) $row["id_level"] . "</td>");
            print("<td class='lista' align='center'><a href='users.php?searchtext=&level=" . (int) $row["id_level"] . "'>" . security::html_safe($row["level"]) . "</a></td>");
            print("<td class='lista' align='center'>" . (int) $row["min_download_size"] . "</td>");
            print("<td class='lista' align='center'>" . (float) $row["min_ratio"] . "</td>");
            print("<td class='lista' align='center'>" . (int) $row["min_seed_hours"] . "</td>");
            print("<td class='lista' align='center'>" . (int) $row["tolerance_days_before_punishment"] . "</td>");
            print("<td class='lista' align='center'>" . (int) $row["upload_punishment"] . "</td>");
            print("<td class='lista' align='center'>" . unesc($row["reward"]) . "</td>");
            print("<td class='lista' align='center'><a href='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun&do=edit&id=" . (int) $row["id_level"] . "'><img src='" . $STYLEPATH . "/edit.png'></a></td>");
            print("<td class='lista' align='center'><a href='admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=hitrun&do=delete&id=" . (int) $row["id_level"] . "'><img src='" . $STYLEPATH . "/delete.png'></a></td>");
            print("</tr>");
        }
    }
    print("</table><br />");
}

block_end();
stdfoot();

?>
