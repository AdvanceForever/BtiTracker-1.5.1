<?php

if (!user::$current || user::$current['admin_access'] == 'no' || user::$current['edit_users'] == 'no') {
    standardheader("Get a freakin' life and stop trying to hack the tracker !");
    block_begin(ERROR);
    err_msg("Error", "Piss off !!! Staff only !");
    print ('<br>');
    block_end();
} else {
    block_begin(DISABLED_USERS);

    //Per Page Listing Limitation Start
    $numdisabled = $db->query("SELECT COUNT(*) FROM users WHERE disabled = 'yes'") or die($db->error);
    $row = $numdisabled->fetch_array(MYSQLI_BOTH);
    $count = (int)$row[0];

    $perpage = (int)$GLOBALS['disableppage'];
    list($pagertop, $limit) = misc::pager($perpage, $count, 'admincp.php?user=' . user::$current['uid'] . '&code=' . user::$current['random'] . '&do=disabledu&' . $addparams);
    //Per Page Listing Limitation Stop

    $res = $db->query("SELECT username, id, warns, disabledby, disabledon, disabledreason FROM users WHERE disabled = 'yes' ORDER BY warns DESC " . $limit) or sqlerr();
    $num = $res->num_rows;

    print("<table border='0' width='100%' cellspacing='2' cellpadding='0'>");

    //Per Page Listing Limitation Start
    if ($count > $perpage)
        print("<tr><td class='lista' align='center' colspan='10'><br>" . $pagertop . "</td></tr>");
    //Per Page Listing Limitation Stop

    //Checkbox Remove Start - 10:03 30.07.2006
    print("<script type='text/javascript'>
    <!--
    function SetAllCheckBoxes(FormName, FieldName, CheckValue){
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
    //Checkbox Remove Stop

    //Checkbox Remove start
    print("<form method='post' name='deleteall' action='warn.php?action=admincpremovedisabled'>");
    //Checkbox Remove Stop

    print("<tr><td class='header' align='center'>" . WARNED_USERNAME . "</td><td class='header' align='center'>" . DISABLED_WARNS . "</td><td class='header' align='center'>" . WARNED_REASON . "</td><td class='header' align='center'>" . DISABLED_ON . "</td><td class='header' align='center'>" . DISABLED_BY . "</td><td class='header' align='center'>" . DISABLED_ACTIVE . "</td><td class='header' align='center'><input type='checkbox' name='all' onclick=\"SetAllCheckBoxes('deleteall','remdisabled[]',this.checked)\" /></td></tr>");

    if ($num == 0)
	print("<tr><td class='lista' align='center' colspan='10'>" . DISABLED_NO_USERS . "</td></tr>");
    else {
        while ($arr = $res->fetch_array(MYSQLI_BOTH)) {
	    if ($arr['warns'] == 0)
	        $total_warns = (int)$arr['warns'];
	    else
	        $total_warns = "<a href='listwarns.php?uid=" . (int)$arr['id'] . "'>" . (int)$arr['warns'] . "</a>";

	    if ($arr['disabled'] == 'no')
	        $active = "<font color='green'><b>" . NO . "</b></font>";
	    else
	        $active = "<font color='red'><b>" . YES . "</b></font>";

	    $get_staffname = $db->query("SELECT username FROM users WHERE id = '" . (int)$arr['disabledby'] . "'");
	    $warnedby = $get_staffname->fetch_array(MYSQLI_BOTH);

            print("<tr><td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['id'] . "'>" . security::html_safe($arr['username']) . "</a></td><td class='lista' align='center'>" . $total_warns . "</td><td class='lista' align='center'>" . security::html_safe($arr['disabledreason']) . "</td><td class='lista' align='center'>" . unesc($arr['disabledon']) . "</td><td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['disabledby'] . "'>" . security::html_safe($warnedby['username']) . "</a></td><td class='lista' align='center'>" . $active . "</td><td class='lista' align='center'><input type='checkbox' name='remdisabled[]' value='" . (int)$arr['id'] . "' /></td></tr>");
        }
    }
    print("<tr><td class='header' align='center' colspan='6'><a href='javascript: history.go(-1);'>" . BACK . "</a></td><td class='header' align='center' colspan='1'><input type='submit' value='" . DELETE . "'></td></tr>");
    print ("</form>");

    //Per Page Listing Limitation Start
    if ($count > $perpage)
        print("<tr><td class='lista' align='center' colspan='10'><br>" . $pagerbottom . "</td></tr>");
    //Per Page Listing Limitation Stop

    print('</table>');
    block_end();
}

?>
