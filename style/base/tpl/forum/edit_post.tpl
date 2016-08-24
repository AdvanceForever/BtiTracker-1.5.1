<form name='edit' method='post' action='?action=editpost&postid={$epostid}'>
<input type='hidden' name='returnto' value='{$ereferer}'>  
<p align='center'>
<table align='center' border='1' cellspacing='0' cellpadding='4'>
<tr>
   <td class='header'>{$lang_body}</td>
   <td class='lista' align='center'>{$ebody}</td>
</tr>
<tr>
   <td align='center' colspan='2'><input type='submit' value='{$lang_confirm}' class='btn'></td>
</tr>
</table>
</p>
</form>
