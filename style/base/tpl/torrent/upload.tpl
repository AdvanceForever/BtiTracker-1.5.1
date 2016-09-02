</center>
<center>{$insert_data}<br /><br />
{$announce_url}<br /><b>
{$tracker_announce_url}<br />
</b><br />
</center>

<form name='upload' method='post' enctype='multipart/form-data'>
<table class='lista' align='center'>
<tr>
   <td class='header'>{$torrent_file}</td>
   <td class='lista' align='left'>
   {if $sha1_exists}
       <input type='file' name='torrent'>
   {else}
       <i>{$no_sha1}</i>
   {/if}
   </td>
</tr>
<tr>
   <td class='header'>{$category}</td>
   <td class='lista' align='left'>{$categories}</td>
</tr>
<tr>
   <td class='header'>{$filename}</td>
   <td class='lista' align='left'><input type='text' name='filename' size='50' maxlength='200' /></td>
</tr>
{if $image_on}
<tr>
   <td class='header'>{$image_link}</td>
   <td class='lista' align='left'><input type='text' name='image' size='50' maxlength='500' /></td>
</tr>
{/if}
{if $torrent_genre}
<tr>
   <td class='header'>{$genre}</td>
   <td class="lista" ><input type='text' name='genre' size='20' maxlength='50' /></td>
</tr>
{/if}
{if $nuked_requested}
<tr>
   <td class='header'>{$torrent_requested}</td>
   <td class='lista'>
      <select name='requested' size='1'>
         <option value='false' selected='selected'>{$no}</option>
         <option value='true'>{$yes}</option>
      </select>
   </td>
</tr>
<tr>
   <td class='header'>{$torrent_nuked}</td>
   <td class='lista'>
      <select name='nuked' size='1'>
         <option value='false' selected='selected'>{$no}</option>
         <option value='true'>{$yes}</option>
      </select>
      &nbsp;<input type='text' name='nuked_reason' size='43' maxlength='100'>
   </td>
</tr>
{/if}
<tr>
   <td class='header' valign='top'>{$description}</td>
   <td class='lista' align='left'>{$description_body}</td>
</tr>
<tr>
   <td colspan='2'><input type='hidden' name='user_id' size='50' value='{$user_id}' /></td>
</tr>
<tr>
   <td class='header'>{$anonymous}</td>
   <td class='lista'>&nbsp;&nbsp;{$no}<input type='radio' name='anonymous' value='false' checked />&nbsp;&nbsp;{$yes}<input type='radio' name='anonymous' value='true' /></td>
</tr>

{if $sha1_exists}
<tr>
   <td class='lista' align='center' colspan='2'><input type='checkbox' name='autoset' value='enabled' disabled checked />{$torrent_check}</td>
</tr>
{/if}

<tr>
   <td align='right'><input type='submit' value='{$send}' /></td>
   <td align='left'><input type='reset' value='{$reset}' /></td>
</tr>
</table>
</form>

</td>
</tr>
</table>
