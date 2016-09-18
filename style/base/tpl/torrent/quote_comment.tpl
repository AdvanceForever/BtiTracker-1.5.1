<form name='comment' method='post' action='edit_comment.php?do=comments&amp;action=confirm&amp;id={$quote_id}'multipart/form-data'>
<input type='hidden' name='info_hash' value='{$quote_info_hash}' />				   
<table class='lista' width='100%' align='center'>
<tr>
   <td class='header' align='right'>{$langq_username}</td>
   <td class='lista'><input type='text' name='user' value='{$quote_username}' size='40' maxlength='60' disabled; readonly /></td>
</tr>
<tr>
   <td class='header' align='right'>{$langq_comment}</td>
   <td class='lista' align='left' style='padding: 0px'>{$quote_comment}</td>
</tr>
<tr>
   <td class='lista' colspan='2' align='center'><input type='submit' name='confirm' value='{$langq_confirm}' />&nbsp;&nbsp;&nbsp;<input type='submit' name='confirm' value='{$langq_cancel}' /></td>
</tr>
</table>
</form>
