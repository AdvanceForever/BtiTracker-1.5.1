<center>
<form enctype='multipart/form-data' name='comment' method='post'>
<input type='hidden' name='info_hash' value='{$id}' />
<table class='lista' border='0' cellpadding='10'>
<tr>
   <td align='left' class='header'>{$lang_username}:</td>
   <td class='lista' align='left'><input name='user' type='text'  value='{$username}' size='20' maxlength='100' disabled; readonly></td>
</tr>
<tr>
   <td align='left' class='header'>{$lang_comment}:</td>
   <td class='lista' align='left'>{$comment}</td>
</tr>
<tr>
   <td class='header' colspan='2' align='center'><input type='submit' name='confirm' value='{$lang_confirm}' />&nbsp;&nbsp;&nbsp;<input type='submit' name='confirm' value='{$lang_preview}' /></td>
</tr>
</table>
</form>
</center>
