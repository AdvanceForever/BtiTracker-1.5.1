<table width='100%' class='lista' border='0' cellspacing='5' cellpadding='5'>
<tr>
   <td align='right' class='header'>{$lang.file_name}:</td>
   <td class='lista'>{$filename}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.info_hash}:</td>
   <td class='lista'>{$info_hash}</td>
</tr>

{if $has_description}
<tr>
   <td align='right' class='header'>{$lang.description}:</td>
   <td align='left' class='lista'>{$description}</td>
</tr>
{/if}

{if $has_category}
<tr>
   <td align='right' class='header'>{$lang.category}:</td>
   <td class='lista'>{$category}</td>
</tr>
{else}
<tr>
   <td align='right' class='header'>{$lang.category}:</td>
   <td class='lista'>(None)</td>
</tr>
{/if}

<tr>
   <td align='right' class='header'>{$lang.size}:</td>
   <td class='lista'>{$size}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.added}:</td>
   <td class='lista'>{$added}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.speed}:</td>
   <td class='lista'>{$speed}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.downloaded}:</td>
   <td class='lista'>{$finished}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.peers}:</td>
   <td class='lista'>{$lang.seeders}: {$seeders}, {$lang.leechers}: {$leechers} = {$peers} {$lang.peers}</td>
</tr>
</table>
<form action='{$scriptname}?info_hash={$id}&amp;returnto={$link}' name='delete' method='post'>
<center>
   <input type='submit' name='action' value='{$lang.delete}' />&nbsp;&nbsp;<input type='submit' name='action' value='{$lang.cancel}' />
</center>
</form>
