<div align='center'>
<form action='news.php' name='news' method='post'>
<table border='0' class='lista'>
<tr>
   <td><input type='hidden' name='action' value='{$action}' /></td>
</tr>
<tr>
   <td><input type='hidden' name='id' value='{$id}' /></td>
</tr>
<tr>
   <td align='center' colspan='2' class='header'>{$lang_insert}:<br /></td>
</tr>
<tr>
   <td align='left' class='lista' style='font-size:10pt'>{$lang_title}</td>
   <td align='left' class='lista'><input type='text' name='title' size='40' maxlength='40' value='{$title}' /></td>
</tr>
<tr>
   <td align='left' class='lista' valign='top' style='font-size:10pt'>{$lang_news}</td>
   <td align='left' class='lista'>{$news}</td>
</tr>
<tr>
   <td align='left' class='header'><input type='submit' name='conferma' value='{$lang_confirm}' /></td>
   <td align='left' class='header'><input type='submit' name='conferma' value='{$lang_cancel}' /></td>
</tr>
</table>
</form>
</div>
