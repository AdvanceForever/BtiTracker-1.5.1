<form name='edit' method='post' action='takereqedit.php'>
<a name='edit' id='edit'></a>
<table class='lista' align='center' width='550' cellspacing='2' cellpadding='0'>
<br />
<tr>
   <td align='left' class='header'>{$lang_torrent_file}</td>
   <td class='lista' align='left'><input type='text' size='60' name='requesttitle' value='{$request}'></td>
</tr>
<tr>
   <td align='center' class='header'>Category:</td>
   <td align='left' class='lista'>{$category}</td>
</tr>
<tr>
   <td align='left' class='header'>{$lang_description}</td>
   <td align='left' class='lista'>{$description}</td>
</tr>
<input type='hidden' name='id' value='{$id2}'>
<tr>
   <td colspan='2' align='center' class='lista'><input type='submit' value='Submit'></td>
</tr>
</table>
</form>
