<?php

if (!user::$current || user::$current['admin_access'] == 'no' || user::$current['edit_users'] == 'no') {
    block_begin(ERROR);
    err_msg("Error", "Get a freakin' life and stop trying to hack the tracker !<br />Piss off !!! Staff only !");
    print('<br>');
    block_end();
    exit();
} else {
    block_begin(PREV_WARNED_USERS);

    //Per Page Listing Limitation Start
    $numwarns = $db->query("SELECT COUNT(*) FROM users LEFT JOIN warnings ON users.id = warnings.id WHERE warnings.active = 'no'");
    $row = $numwarns->fetch_array(MYSQLI_BOTH);
    $count = (int)$row[0];

    $perpage = (int)$GLOBALS['warnsppage'];
    list($pagertop, $limit) = misc::pager($perpage, $count, 'admincp.php?user=' . user::$current['uid'] . '&code=' . user::$current['random'] . '&do=prevwarnedu&' . $addparams);
    //Per Page Listing Limitation Stop

    $res = $db->query("SELECT id, username, warns FROM users WHERE warns > '0' ORDER BY warns DESC " . $limit) or sqlerr();
    $num = $res->num_rows;

    print("<table border='0' width='100%' cellspacing='2 cellpadding='0'>");

    //Per Page Listing Limitation Start
    if ($count > $perpage)
        print("<tr><td class='lista' align='center' colspan='10'><br>" . $pagertop . "</td></tr>");
    //Per Page Listing Limitation Stop

    print("<tr><td class='header' align='center'>" . WARNED_USERNAME . "</td><td class='header' align='center'>" . WARNED_TOTAL_WARNS . "</td><td class='header' align='center'>" . PREV_REACHED_MAX . "</td></tr>");

    if ($num == 0)
        print("<tr><td class='lista' align='center' colspan='10'>" . PREV_NO_USERS . "</td></tr>");
    else {
        while ($arr = $res->fetch_array(MYSQLI_BOTH)) {
            $res2 = $db->query("SELECT active FROM warnings WHERE userid = '" . (int)$arr['id'] . "'") or sqlerr();
            $arr2 = $res2->fetch_array(MYSQLI_BOTH);

            if ($arr2['active'] == 'yes') {
                #Do Nothing...
            } else {
		if ($arr['warns'] >= $warntimes)
		    $active = "<font color='red'><b>" . YES . "</b></font>";
		else
		    $active = "<font color='green'><b>" . NO . "</b></font>";

       		print("<tr><td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['id'] . "'>" . security::html_safe($arr['username']) . "</a></td><td class='lista' align='center'><a href='listwarns.php?uid=" . (int)$arr['id'] . "'>" . (int)$arr['warns'] . "</a></td><td class='lista' align='center'>" . $active . "</td></tr>");
            }
        }
    }

    print("<tr><td class='header' align='center' colspan='3'><a href='javascript: history.go(-1);'>" . BACK . "</a></td></tr>");

    //Per Page Listing Limitation Start
    if ($count > $perpage)
        print("<tr><td class='lista' align='center' colspan='10'><br>" . $pagertop . "</td></tr>");
    //Per Page Listing Limitation Stop

    print('</table>');
    block_end();
}

?>
