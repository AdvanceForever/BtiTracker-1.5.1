<form name='commentsedit' method='post' action='edit_comment.php?do=comments&amp;action=write&amp;id={$id}'multipart/form-data'>
<input type='hidden' name='info_hash' value='{$info_hash}' />
<table class='lista' width='100%' align='center'>
<tr>
   <td class='header' align='right'>{$lang_username}</td>
   <td class='lista'><input type='text' name='user' value='{$username}' size='40' maxlength='60' disabled; readonly /></td>
</tr>
<tr>
   <td class='header' align='right'>{$lang_comment}</td>
   <td class='lista' align='left' style='padding: 0px'>{$comment}</td>
</tr>
<tr>
   <td class='lista' colspan='2' align='center'><input type='submit' name='write' value='{$lang_confirm}' />&nbsp;&nbsp;&nbsp;<input type='submit' name='write' value='{$lang_cancel}' /></td>
</tr>
</table>
</form>
