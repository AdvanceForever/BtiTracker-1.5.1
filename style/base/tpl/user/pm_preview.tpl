<table width='100%' align='center' class='lista'>
<tr>
    <td class='lista' align='center'>{$pmpreview_message}</td>
</tr>
</table>

<br />

<form method='post' name='edit' action='usercp.php?do={$pmpreview_do}&action=post&uid={$pmpreview_uid}&what={$pmpreview_what}'>
<table class='lista' align='center' cellpadding='2'>
<tr>
    <td class='header'>{$lang_pmpreview.receiver}:</td>
    <td class='header'><input type='text' name='receiver' value='{$pmpreview_receiver}' size='40' maxlength='40' />&nbsp;&nbsp;{$pmpreview_find_user}</td>
</tr>
<tr>
    <td class='header'>{$lang_pmpreview.subject}:</td>
    <td class='header'><input type='text' name='subject' value='{$pmpreview_subject}' size='40' maxlength='40' /></td>
</tr>
<tr>
   <td colspan='2'>{$pmpreview_compose}</td>
</tr>
</table>

<br />

<table class='lista' width='100%' align='center'>
<tr>
   <td class='lista' align='center'><input type='submit' name='confirm' value='{$lang_pmpreview.confirm}' /></td>
   <td class='lista' align='center'><input type='submit' name='confirm' value='{$lang_pmpreview.preview}' /></td>
   <td class='lista' align='center'><input type='submit' name='confirm' value='{$lang_pmpreview.cancel}' /></td>
</tr>
</table>
</form>
