<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Requests Page');

if (!user::$current || user::$current['view_torrents'] == 'no') {
    err_msg(ERROR, NEED_TO_BE_AN_MEMBER);
    stdfoot();
    exit;
} else {
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
            </script>
            ");

    block_begin(REQUESTS);
    if ($REQUESTSON) {
        $maxallowed = $max_req_allowed;
        $res3 = $db->query("SELECT * FROM requests AS reqcount WHERE userid = " . user::$current['uid']) or $db->error;
        $arr3    = $res3->num_rows;
        $numreqs = $arr3;
        $reqrem  = $maxallowed - $numreqs;
        
        print("<div align='center'>Available Requests for <b>" . user::$current['username'] . ": " . $maxallowed . "</b> | Posted Requests: <b>" . $arr3 . "</b> | Remaining: <b>" . $reqrem . "</b></div><br>");
        
        print("<div align='right'><a href='requests.php'>Add New Request</a> | <a href='viewrequests.php?requestorid=" . user::$current['uid'] . "'>View My Requests</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        print("<br><br><a href='" . security::esc_url($_SERVER['PHP_SELF']) . "?category=" . (int)$_GET['category'] . "&sort=" . security::html_safe($_GET['sort']) . "&filter=true'><b>Hide Filled Requests</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div>");
        
        
        $categ       = security::html_safe($_GET['category']);
        $requestorid = (int)$_GET['requestorid'];
        $sort        = security::html_safe($_GET['sort']);
        $search      = security::html_safe($_GET['search']);
        $filter      = unesc($_GET['filter']);
        
        $search = " AND requests.request like '%" . $search . "%' ";

        if ($sort == "votes")
            $sort = " order by hits desc ";
        else if ($sort == "request")
            $sort = " order by request ";
        else
            $sort = " order by added desc ";
        
        if ($filter == "true")
            $filter = " AND requests.filledby = 0 ";
        else
            $filter = "";
        
        
        if ($requestorid <> NULL) {
            if (($categ <> NULL) && ($categ <> 0))
                $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
            else
                $categ = "WHERE requests.userid = " . $requestorid;
        }
        else if ($categ == 0)
            $categ = '';
        else
            $categ = "WHERE requests.cat = " . $categ;

        $res = $db->query("SELECT COUNT(requests.id) FROM requests INNER JOIN categories ON requests.cat = categories.id INNER JOIN users ON requests.userid = users.id " . $categ . " " . $filter . " " . $search) or die($db->error);
        $row   = $res->fetch_array(MYSQLI_BOTH);
        $count = (int)$row[0];
        
        $perpage = 15;
        
        list($pagertop, $limit) = misc::pager($perpage, $count, security::esc_url($_SERVER["PHP_SELF"]) . "?" . "category=" . security::html_safe($_GET["category"]) . "&sort=" . security::html_safe($_GET["sort"]) . "&");
        
        $res = $db->query("SELECT users.downloaded, users.uploaded, users.username, requests.filled, requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, categories.image AS catimg, categories.id AS catid, categories.name AS cat FROM requests INNER JOIN categories ON requests.cat = categories.id INNER JOIN users ON requests.userid = users.id  " . $categ . " " . $filter . " " . $search . " " . $sort . " " . $limit) or sqlerr();
        $num = $res->num_rows;
        
        print("<br><br><center><form method='get' action='viewrequests.php'>");
        print("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' size='30' name='search'>");
        print(" <input type='submit' align='center' value='" . SEARCH . "' style='height: 18.5px'>\n");
        print("</form></center><br>");
        
        echo $pagertop;
        
        echo "<table border='0' width='99%' align='center' cellspacing='0' cellpadding='0'><tr><td width='49.5%' align='left'>";
        
        print("<p>" . SORT_BY . " <a href='" . security::esc_url($_SERVER['PHP_SELF']) . "?category=" . security::html_safe($_GET['category']) . "&filter=" . security::html_safe($_GET['filter']) . "&sort=votes'>" . VOTES . "</a> - <a href=" . security::esc_url($_SERVER['PHP_SELF']) . "?category=" . security::html_safe($_GET['category']) . "&filter=" . security::html_safe($_GET['filter']) . "&sort=request> Name </a> - <a href=" . security::esc_url($_SERVER['PHP_SELF']) . "?category=" . security::html_safe($_GET['category']) . "&filter=" . security::html_safe($_GET['filter']) . "&sort=added> Date </a></p>");
        
        print("<form method='get' action='viewrequests.php'>");
?>
</td><td width=100% align=right>
<select name="category">
<option value="0"><?php
        print("----\n");
?></option>
<?php
        
        $cats        = Cached::genrelist();
        $catdropdown = "";
        foreach ($cats as $cat) {
            $catdropdown .= "<option value='" . (int)$cat["id"] . "'";
            $catdropdown .= ">" . security::html_safe($cat['name']) . "</option>\n";
        }
        
        echo $catdropdown;

?>
</select>
<?php
        print("<input type='submit' align='center' value='" . DISPLAY . "'>\n");
        print("</form></td></tr></table>");
        if ($num == "0") {
            print("<table width='100%' align='center' cellspacing='1' cellpadding='0'><tr><td class='lista' align='center'><br><br><b>No results that match your search criteria were found...</b><br><br></td></tr></table>\n");
        } else {
            print("<form name='deleteall' method='post' action='takedelreq.php'>");
            print("<table width='99%' align='center' cellpadding='3' cellspacing='1' class='lista'>\n");
            print("<tr><td class='header' align='center'>" . REQUEST . "</td><td class='header' align='center'>" . TYPE . "</td><td class='header' align='center' width='150'>" . DATE_ADDED . "</td><td class='header' align='center'>" . ADDED_BY . "</td><td class='header' align='center'>" . FILLED . "</td><td class='header' align='center'>" . FILLED_BY . "</td><td class='header' align='center'>" . VOTES . "</td>\n");
            
            if (!user::$current || user::$current["admin_access"] == "yes")
                print("<td class='header' align='center'><input type=\"checkbox\" name=\"all\" onclick=\"SetAllCheckBoxes('deleteall','delreq[]',this.checked)\" /></td></tr>\n");
            
            
            for ($i = 0; $i < $num; ++$i) {
                $arr = $res->fetch_assoc();
                
                $privacylevel = unesc($arr["privacy"]);
                
                if ($arr["downloaded"] > 0) {
                    $ratio = number_format((float)$arr["uploaded"] / (float)$arr["downloaded"], 2);
                } else if ($arr["uploaded"] > 0)
                    $ratio = "Inf.";
                else
                    $ratio = "---";

                $res2 = $db->query("SELECT username FROM users WHERE id = " . (int)$arr['filledby']);
                $arr2 = $res2->fetch_assoc();
                if ($arr2['username'])
                    $filledby = security::html_safe($arr2['username']);
                else
                    $filledby = '';
                
                if (!user::$current || user::$current["delete_torrents"] == "no") {
                    if (!user::$current || user::$current["view_users"] == "yes") {
                        $addedby = "<td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['userid'] . "'><b>" . security::html_safe($arr['username']) . " (" . $ratio . ")</b></a></td>";
                    } else {
                        $addedby = "<td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['userid'] . "'><b>" . security::html_safe($arr['username']) . " (----)</b></a></td>";
                    }
                } else {
                    $addedby = "<td class='lista' align='center'><a href='userdetails.php?id=" . (int)$arr['userid'] . "'><b>" . security::html_safe($arr['username']) . " (" . $ratio . ")</b></a></td>";
                }
                
                $filled = unesc($arr['filled']);
                if ($filled) {
                    $filled       = "<a href='" . $filled . "'><font color=green><b>Yes</b></font></a>";
                    $filledbydata = "<a href='userdetails.php?id=" . (int)$arr['filledby'] . "'><b>" . security::html_safe($arr2['username']) . "</b></a>";
                } else {
                    $filled       = "<a href='reqdetails.php?id=" . (int)$arr['id'] . "'><font color=red><b>No</b></font></a>";
                    $filledbydata = "<i>Nobody</i>";
                }
                
                print("<tr><td class='lista' align='left'><a href='reqdetails.php?id=" . (int)$arr['id'] . "'><b>" . security::html_safe($arr['request']) . "</b></a></td>" . "<td class='lista' align='center'>" . image_or_link(($arr['catimg'] == '' ? '' : 'images/categories/' . $arr['catimg']), ' title="' . security::html_safe($arr['cat']) . '"', security::html_safe($arr['cat'])) . "</td><td class='lista' align='center'>" . unesc($arr['added']) . "</td>" . $addedby . "<td class='lista' align='center'>" . $filled . "</td><td class='lista'>" . $filledbydata . "</td><td class='lista' align='center'><a href='votesview.php?requestid=" . (int)$arr['id'] . "'><b>" . (int)$arr['hits'] . "</b></a></td>\n");
                
                if (!user::$current || user::$current["admin_access"] == "yes")
                    print("<td class='lista' align='center'><input type=\"checkbox\" name=\"delreq[]\" value=\"" . (int)$arr['id'] . "\" /></td></tr>\n");
            }
            
            print("</table>\n");
            
            if (!user::$current || user::$current["admin_access"] == "yes")
                print("<table width='99%'><td align='right'><input type='submit' value='GO'></td></table>");
            print("</form>");
        }
        
        //echo $pagerbottom;
    } else {
        echo REQUESTS_OFFLINE;
    }

    block_end();
}

stdfoot();
die;

?>
