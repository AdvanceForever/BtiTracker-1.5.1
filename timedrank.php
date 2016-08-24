<?php
/////////////////////////////////////////////////////////////////////////////////////
// Timed promote / demote system by DiemThuy June 2009
// Addapted for BtitTracker by Yupy
////////////////////////////////////////////////////////////////////////////////////
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'functions.php');

dbconn();

#Additional Staff Check
$tr = new allowed_staff;
if (!$tr->check('timedrank'))
    die();
#Additional Staff Check End

if (user::$current['admin_access'] == 'no' && user::$current['edit_users'] == 'no')
    die('Unauthorized Access!');

$id = (int)$_GET['id'];

quickQuery("UPDATE `users` SET  `old_rank` = `id_level` WHERE `id` = " . $id);

$dt2 = 'yes';
$dt3 = $db->real_escape_string($_POST['level']);
$dt1 = rank_expiration(mktime(date('H') + 2, date('i'), date('s'), date('m'), date('d') + addslashes($_POST['t_days']), date('Y')));

$returnto = security::html_safe($_POST['returnto']);

$res4 = $db->query("SELECT level FROM users_level WHERE id = '" . $dt3 . "'") or sqlerr(__FILE__, __LINE__);
$arr4 = $res4->fetch_assoc();

$newrank = security::html_safe($arr4['level']);

$res5 = $db->query("SELECT old_rank FROM users WHERE id = '" . $id . "'") or sqlerr(__FILE__, __LINE__); 
$arr5 = $res5->fetch_assoc();

$res6 = $db->query("SELECT level FROM users_level WHERE id = '" . (int)$arr5['old_rank'] . "'") or sqlerr(__FILE__, __LINE__);
$arr6 = $res6->fetch_assoc();

$oldrank = security::html_safe($arr6['level']);

function rank_expiration($timestamp = 0) {
    return gmdate('Y-m-d H:i:s', $timestamp);
}

$subj = sqlesc("Your rank is changed!");
$msg = sqlesc("Your rank is changed to ".$newrank."\n\n This is a timed rank and it will expire " . $dt1 . "\n\n After that you will get your old " . $oldrank . " rank back\n\n [color=red]This is a automatic system message, Do not reply![/color]");

$db->query("UPDATE `users` SET `timed_rank` = '" . $dt1 . "', `rank_switch` = '" . $dt2 . "', `id_level` = '" . $dt3 . "' WHERE `id` = " . $id) or sqlerr(__FILE__, __LINE__); 
$db->query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES(1, " . $id . ", UNIX_TIMESTAMP(), " . $subj . ", " . $msg . ")") or sqlerr(__FILE__, __LINE__); 

header('Location: '  .  $returnto);
die();

?>
