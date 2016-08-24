<?php
// SeedBonus Mod by CobraCRK   -   original ideea by TvRecall...
//cobracrk[at]yahoo.com
//www.extremeshare.org
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

standardheader('Seed Bonus');

if (user::$current["view_torrents"] == "no") {
    err_msg(ERROR, NOT_AUTH_VIEW_NEWS);
    stdfoot();
    exit;
} else {
    block_begin("Seed Bonus");
	
    print("<style type='text/css'>
    <!--
    .style1 {
	color: #000000;
	font-size: x-large;
    }
    .style2 {font-size: x-large}
    -->
    </style>");

    print("<p align='center'>");
	
    $r = $db->query("SELECT seedbonus FROM users WHERE id = " . user::$current['uid']);
    $cc = mysqli_result($r, 0, "seedbonus");
	
    print("<br><center><h1>" . BONUS_INFO1 . " " . $cc . ").<br />" . BONUS_INFO2 . "</h1></center>");

    print("</p>
        <p>&nbsp;</p>
        <table width='474' border='1' align='center' cellpadding='2' cellspacing='0'>
        <tr>
            <td width='26'>" . OPTION . "</td>
            <td width='319'>" . WHAT_ABOUT . "</td>
            <td width='41'>" . POINTS . "</td>
            <td width='62'>" . EXCHANGE . "</td>
        </tr>");
	
    $uid = user::$current['uid'];
    $r = $db->query("SELECT * FROM users WHERE id = " . $uid);
    $c = mysqli_result($r, 0, "seedbonus");
	
    $r = $db->query("SELECT * FROM bonus");
    while ($row = $r->fetch_array(MYSQLI_BOTH)) {
        if ($c < $row['points']) {
		    $enb = "disabled";
		}
		
        print("<form action='seedbonus_exchange.php?id=" . (int)$row['id'] . "' method='post'>
		    <tr>
                <td><h1><center>" . security::html_safe($row['name']) . "</center></h1></td>
                <td><b>" . (int)$row['gb'] . " GB Upload</b><br />" . BONUS_DESC . "</td>
                <td>" . (int)$row['points'] . "</td>
                <td><input type='submit' name='submit' value='" . EXCHANGE . "!' " . $enb . "></td>
            </tr>
			</form>");
    }
	
    print("</table>
        <p align='center' class='style1'>&nbsp;</p>
        <p class='style2'><center><h1> " .BONUS_INFO3 . "</h1></center></p>");
    block_end();
}

stdfoot();

?>
