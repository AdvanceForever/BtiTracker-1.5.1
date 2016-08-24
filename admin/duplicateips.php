<?php

standardheader("Duplicate IP's");

if (!user::$current || user::$current["delete_torrents"] != "yes") {
    err_msg(ERROR, NOT_ADMIN_CP_ACCESS);
    stdfoot();
    exit;
}

block_begin("Duplicate IP's");

print("<table class='lista' align='center' cellspacing='0' cellpadding='4'>
<tr align='center'>
<td class='header' width='90'>Username</td>
<td class='header'>Email</td>
<td class='header'>Registered</td>
<td class='header'>Last access</td>
<td class='header'>Downloaded</td>
<td class='header'>Uploaded</td>
<td class='header'>Ratio</td>
<td class='header'>IP</td>
</tr>\n");

$res = $db->query("SELECT lip FROM users GROUP BY lip HAVING COUNT(*) > 1") or sqlerr();
$num = $res->num_rows;

if ($num == 0) {
   print("<tr><td align='center' colspan='8'>No duplicate IP's found !</td></tr></table>");
   block_end();
   stdfoot();
   exit;
}

while($r = $res->fetch_assoc()) {
    $ros = $db->query("SELECT * FROM users WHERE lip = '" . $db->real_escape_string($r['lip']) . "' ORDER BY lip") or sqlerr();
    $num2 = $ros->num_rows;

    while($arr = $ros->fetch_assoc()) {
        if ($arr['joined'] == '0000-00-00 00:00:00')
            $arr['joined'] = '-';
		
        if ($arr['lastconnect'] == '0000-00-00 00:00:00')
            $arr['lastconnect'] = '-';
	    
        if ($arr["downloaded"] != 0)
            $ratio = number_format((int)$arr["uploaded"] / (int)$arr["downloaded"], 3);
        else
            $ratio = "&infin;";

        $ratio = "<font color='red'>" . $ratio . "</font>";
        $uploaded = misc::makesize((int)$arr["uploaded"]);
        $downloaded = misc::makesize((int)$arr["downloaded"]);
        $added = substr($arr['joined'], 0, 10);
        $last_access = substr($arr['lastconnect'], 0, 10);
        $ip = long2ip($arr['lip']);
		
        print("<tr><td align='left' class='lista'><a href='userdetails.php?id=" . (int)$arr['id'] . "'>" . security::html_safe($arr['username']) . "</a></td>
        <td align='center' class='lista'>" . unesc($arr['email']) . "</td>
        <td align='center' class='lista'>" . $added . "</td>
        <td align='center' class='lista'>" . $last_access . "</td>
        <td align='center' class='lista'>" . $downloaded . "</td>
        <td align='center' class='lista'>" . $uploaded . "</td>
        <td align='center' class='lista'>" . $ratio . "</td>
        <td align='center' class='lista'>" . $ip . "</td></tr>\n");
    }
} 

print("</table>");

block_end();
stdfoot();

?>
