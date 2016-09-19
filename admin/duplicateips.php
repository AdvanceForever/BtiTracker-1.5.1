<?php

standardheader("Duplicate IP's");

if (!user::$current || user::$current["delete_torrents"] != "yes") {
    err_msg(ERROR, NOT_ADMIN_CP_ACCESS);
    stdfoot();
    exit;
}

block_begin("Duplicate IP's");

$res = $db->query("SELECT lip FROM users GROUP BY lip HAVING COUNT(*) > 1") or sqlerr();
$num = $res->num_rows;

if ($num == 0) {
   err_msg(ERROR, 'No Duplicates IPs Found !');
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

        $uploaded = misc::makesize((int)$arr["uploaded"]);
        $downloaded = misc::makesize((int)$arr["downloaded"]);
        $added = substr($arr['joined'], 0, 10);
        $last_access = substr($arr['lastconnect'], 0, 10);
        $ip = long2ip($arr['lip']);

        $arr['id'] = (int)$arr['id'];
        $arr['username'] = security::html_safe($arr['username']);
        $arr['email'] = unesc($arr['email']);
        $arr['joined'] = $added;
        $arr['lastconnect'] = $last_access;
        $arr['downloaded'] = $downloaded;
        $arr['uploaded'] = $uploaded;
        $arr['ip'] = $ip;

        $duplicates[] = $arr;
    }
	$smarty->assign('show_duplicates', $duplicates);
	unset($duplicates);
}

$smarty->display($STYLEPATH . '/tpl/admin/duplicate_ips.tpl');

block_end();
stdfoot();

?>
