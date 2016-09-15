<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(INCL_PATH . 'functions.php');

dbconn();

if (!user::$current || user::$current["admin_access"] == "no" || user::$current["edit_users"] == "no") {
	block_begin(ERROR);
	err_msg("Error", "Get a freakin' life and stop trying to hack the tracker !");
	block_end();
	stdfoot(false);
} else {
    block_begin(WARNED_USERS);

    $count = MCached::get('warnings::count');
    if ($count === MCached::NO_RESULT) {
        $numwarns = $db->query("SELECT COUNT(*) FROM warnings WHERE active = 'yes'");
        $row = $numwarns->fetch_array(MYSQLI_BOTH);
        $count = (int)$row[0];
	MCached::add('warnings::count', $count, 300);
    }
	
    $perpage = (int)$GLOBALS["warnsppage"];
    list($pagertop, $limit) = misc::pager($perpage, $count,  "admin.php?user=" . user::$current["uid"] . "&code=" . user::$current["random"] . "&action=warnedusers&" . $addparams);

    $res = $db->query("SELECT * FROM warnings WHERE active = 'yes' ORDER BY warns DESC " . $limit);
    $num = $res->num_rows;

    print("<table border='0' width='100%' cellspacing='0' cellpadding='4'>");

    if ($count > $perpage)
        print("<tr><td class='lista' align='center' colspan='10'><br />" . $pagertop . "</td></tr>");

    //Checkbox Remove Start
    print("<script type=\"text/javascript\">
    <!--
    function SetAllCheckBoxes(FormName, FieldName, CheckValue)
    {
    if(!document.forms[FormName])
    return;
    var objCheckBoxes = document.forms[FormName].elements[FieldName];
    if(!objCheckBoxes)
    return;
    var countCheckBoxes = objCheckBoxes.length;
    if(!countCheckBoxes)
    objCheckBoxes.checked = CheckValue;
    else
    // set the check value for all check boxes
    for(var i = 0; i < countCheckBoxes; i++)
    objCheckBoxes[i].checked = CheckValue;
    }
    -->
    </script>");

    print("<form method='post' name='deleteall' action='warn.php?action=admincpremovewarn'>");
    //Checkbox Remove Stop

    print("<tr><td class='header' align='center'>" . WARNED_ID . "</td><td class='header' align='center'>" . WARNED_USERNAME . "</td><td class='header' align='center'>" . WARNED_TOTAL_WARNS . "</td><td class='header' align='center'>" . WARNED_DATE_ADDED . "</td><td class='header' align='center'>" . WARNED_EXPIRATION . "</td><td class='header' align='center'>" . WARNED_DURATION . "</td><td class='header' align='center'>" . WARNED_REASON . "</td><td class='header' align='center'>" . WARNED_BY . "</td><td class='header' align='center'>" . WARNED_ACTIVE . "</td><td class='header' align='center'><input type=\"checkbox\" name=\"all\" onclick=\"SetAllCheckBoxes('deleteall','remwarn[]',this.checked)\" /></td></tr>");

    if ($num == '0')
	    print("<tr><td class='lista' align='center' colspan='10'>" . WARNED_NO_USERS . "</td></tr>");
    else {
        while ($arr = $res->fetch_array(MYSQLI_BOTH)) {
	       if ($arr['warnedfor'] == 0)
	           $duration = WARNED_UNLIMITED;
	       elseif ($arr['warnedfor'] == 1)
	           $duration = (int)$arr['warnedfor'] . "" . WARNED_WEEK;
	       else
	           $duration = (int)$arr['warnedfor'] . "" . WARNED_WEEKS;

	       if ($arr['active'] == 'no')
	           $active = "<font color='green'><b>" . NO . "</b></font>";
	       else
	           $active = "<font color='red'><b>" . YES . "</b></font>";

	       $get_staffname = $db->query("SELECT username FROM users WHERE id = " . (int)$arr['addedby']);
	       $warnedby = $get_staffname->fetch_array(MYSQLI_BOTH);

	       $get_username = $db->query("SELECT username, warns FROM users WHERE id = " . (int)$arr['userid']);
	       $warned = $get_username->fetch_array(MYSQLI_BOTH);

           print("<tr>
                     <td class='lista' align='center'>" . (int)$arr['id'] . "</td>
                     <td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['userid'] . "'>" . security::html_safe($warned['username']) . "</a></td>
                     <td class='lista' align='center'><a href='listwarns.php?uid=" . (int)$arr['userid'] . "'>" . (int)$warned['warns'] . "</a></td>
                     <td class='lista' align='center'>" . $arr['added'] . "</td>
                     <td class='lista' align='center'>" . $arr['expires'] . "</td>
                     <td class='lista' align='center'>" . $duration . "</td>
                     <td class='lista' align='center'>" . unesc($arr['reason']) . "</td>
                     <td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['addedby'] . "'>" . security::html_safe($warnedby['username']) . "</a></td>
                     <td class='lista' align='center'>" . $active . "</td><td class='lista' align='center'><input type='checkbox' name='remwarn[]' value='" . (int)$arr['userid'] . "' /></td>
           </tr>");
        }

    }

    print("<tr><td class='header' align='center' colspan='9'><a href='javascript: history.go(-1);'>" . BACK . "</a></td><td class='header' align='center' colspan='1'><input type='submit' value='" . DELETE . "'></td></tr>");
    print("</form>");

    if ($count > $perpage)
        print("<tr><td class='lista' align='center' colspan='10'><br>" . $pagertop . "</td></tr>");

    print("</table>");

    block_end();
    stdfoot(false);
}

?>
