<div align='center'>
<form action='{$scriptname}?returnto={$link}' method='post' name='edit'>
<table class='lista'>
<tr>
   <td align='right' class='header'>{$lang_filename}:</td>
   <td class='lista'><input type='text' name='name' value='{$filename}' size='60' /></td>
</tr>

{if $image_link}
<tr>
   <td align='right' class='header'>Image Link:</td>
   <td class='lista'><input type='text' name='image' value='{$image}' size='60' /></td>
</tr>
{/if}

{if $torrent_genre}
<tr>
   <td align='right' class='header'>{$lang_genre}:</td>
   <td class='lista'><input type='text' name='genre' value='{$genre}' size='60' /></td>
</tr>
{/if}

<tr>
   <td align='right' class='header'>{$lang_infohash}:</td>
   <td class='lista'>{$info_hash}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang_description}:</td>
   <td class='lista'>{$description}</td>
 </tr>
<tr>
   <td align='right' class='header'>{$lang_category}:</td>
   <td class='lista' align='left'>{$categories}</td>
</tr>

{if $nuked_requested}
<tr>
   <td class='header' align='right'>{$lang_requested}:</td>
   <td class='lista' align='left'>
      <select name='request' size='1'>
         <option value='false'{$selectedr}>{$lang_no}</option>
         <option value='true'{$selectedr}>{$lang_yes}</option>
      </select>
   </td>
</tr>
<tr>
   <td class='header' align='right'>{$lang_nuked}:</td>
   <td class='lista' align='left'>
      <select name='nuke' size='1'>
         <option value='false'{$selectedn}>{$lang_no}</option>
         <option value='true'{$selectedn}>{$lang_yes}</option>
      </select>&nbsp;<input type='text' name='nuked_reason' value='{$nuked_reason}' size='43' maxlength='100'>
   </td>
</tr>
{/if}

{if $is_freeleech}
<tr>
   <td class='header' align='right'>Freeleech:</td>
   <td class='lista'><input type='checkbox' name='free'{$checkedf} value='1' /> Free download (only upload stats are recorded)</td>
</tr>
{/if}

<tr>
   <td align='right' class='header'>{$lang_size}:</td>
   <td class='lista'>{$size}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang_added}:</td>
   <td class='lista'>{$added}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang_downloaded}:</td>
   <td class='lista'>{$downloaded}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang_peers}:</td>
   <td class='lista'>{$lang_seeders}: {$seeders}, {$lang_leechers}: {$leechers} = {$peers} {$lang_peers}</td>
</tr>
<tr>
   <td><input type='hidden' name='info_hash' size='40' value='{$info_hash}'></td><td></td>
</tr>
<tr>
   <td align='right'></td>
</table>
<table>
   <td align='right'><input type='submit' value='{$lang_confirm}' name='action' /></td>
   <td><input type='submit' value='{$lang_cancel}' name='action' /></td>
</form>
</table>
</tr>
</div>
