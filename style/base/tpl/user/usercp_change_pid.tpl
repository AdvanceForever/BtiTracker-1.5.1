<form method='post' name='pid' action='usercp.php?do=pid_c&action=post&uid={$pid_uid}'>
<table class='lista' width='100%' align='center'>
<tr>
    <td class='header'>{$lang_pid}:</td>
    <td class='lista'>{$pid}</td>
</tr>
<tr>
    <td class='header' align='center' colspan='2'><input type='submit' name='confirm' value='Reset PID'/>&nbsp;&nbsp;&nbsp;<input type='submit' name='confirm' value='{$lang_pid_cancel}'/></td>
</tr>
</table>
</form>
<br />
