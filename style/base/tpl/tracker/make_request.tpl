<table align='center' border='0' width='100%' cellspacing='0' cellpadding='3'>
<tr>
   <td class='header' align='center'>{$lang_search} {$lang_torrent}</td>
</tr>
<tr>
   <td align='center' class='lista'>
      <form method='get' action='torrents.php'>
         <input type='text' name='search' size='40' value='{$search_string}' />
         in
         <select name='category'>
            <option value='0'>(Select)</option>
            {$category}
         </select>
         {$dead_checkbox}
         <input type='submit' value='{$lang_search}' />
      </form>
   </td>
</tr>
</table>
<br />
<hr>
<br />
<table class='lista' align='center' width='550' cellspacing='2'>
<form name='request' method='post' action='takerequest.php'>
<a name='add' id='add'></a>
<tr>
   <td class='header' align='center' width='100%' colspan='2'>{$lang_add_requests}</td>
</tr>
<tr>
   <td class='header' align='left' width='30%'>{$lang_torrent_file}</td>
   <td class='lista' align='left' width='70%'><input type='text' size='40' name='requesttitle'></td>
</tr>
<tr>
   <td class='header' align='left' width='30%'>{$lang_category}</td>
   <td class='lista' align='left' width='70%'>
      <select name='category'>
         <option value='0'>(Select)</option>
         {$category2}
      </select>
   </td>
</tr>
<tr>
   <td class='header' align='left' width='30%'>{$lang_description}</td>
   <td class='lista' align='left' width='70%'>{$description}</td>
</tr>
<tr>
   <td class='lista' align='center' width='100%' colspan='2'><input type='submit' value='{$lang_confirm}'></td>
</tr>
</form>
</table>
