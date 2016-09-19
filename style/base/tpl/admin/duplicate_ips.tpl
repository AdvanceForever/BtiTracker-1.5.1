<table class='lista' align='center' cellspacing='0' cellpadding='4'>
<tr align='center'>
   <td class='header' width='90'>Username</td>
   <td class='header'>Email</td>
   <td class='header'>Registered</td>
   <td class='header'>Last access</td>
   <td class='header'>Downloaded</td>
   <td class='header'>Uploaded</td>
   <td class='header'>IP</td>
</tr>

{foreach item=duplicates from=$show_duplicates}
<tr>
   <td align='left' class='lista'><a href='userdetails.php?id={$duplicates.id}'>{$duplicates.username}</a></td>
   <td align='center' class='lista'>{$duplicates.email}</td>
   <td align='center' class='lista'>{$duplicates.joined}</td>
   <td align='center' class='lista'>{$duplicates.lastconnect}</td>
   <td align='center' class='lista'>{$duplicates.downloaded}</td>
   <td align='center' class='lista'>{$duplicates.uploaded}</td>
   <td align='center' class='lista'>{$duplicates.ip}</td>
</tr>
{/foreach}
</table>
