<table width='700' class='lista' align='center' border='0' cellspacing='2' cellpadding='3'>
<tr>
   <td align='left' class='header'>{$lang_reqdetails.request}:</td>
   <td class='lista' width='70%' align='left'>{$request}

   {if $can_edit}
      &nbsp;&nbsp;&nbsp;<{$edit_link}><b>[Edit]</b></a></td></tr>
   {else}
   {/if}
   </td>
</tr>

{if $has_description}
<tr>
   <td align='left' class='header'>{$lang_reqdetails.info}:</td>
  <td class='lista' width='70%' align='left'>{$description}</td>
</tr>
{/if}

<tr>
   <td align='left' class='header'>{$lang_reqdetails.added}:</td>
   <td class='lista' width='70%' align='left'>{$added}</td>
</tr>
<tr>
   <td align='left' class='header'>Added By:</td>
   <td class='lista' align='left'><a href='userdetails.php?id={$userid}'>{$username}</td>
</tr>

{if $is_filled}
<tr>
   <td align='left' class='header'>{$lang_reqdetails.vote_for}</td>
   <td class='lista' width='50%' align='left'><a href='addrequest.php?id={$id}'><b>{$lang_reqdetails.vote}</b></a></td>
</tr>

{if $can_upload}
<tr>
   <td class='header' align='left' width='30%'>How To Fill the Request?</td>
   <td class='lista' align='left' width='70%'>Type full torrent URL, i.e. http://www.mysite.com/details.php?info_hash=1a750aff2e92... (you can only copy/paste from another window) or modify existing URL of torrent ID...</td>
</tr>
<tr>
   <td class='lista' align='center' width='100%' colspan='2'>
      <form method='get' action='reqfilled.php'>
	     <input type='text' size='80' name='filledurl' value='TYPE-DIRECT-TORRENT-URL-HERE'>
         <br />
         <input type='submit' value='{$lang_reqdetails.send}'>
	     <input type='hidden' value='{$id}' name='requestid'>
	  </form>
	  <hr>
      <form method='get' action='requests.php#add'><input type='submit' value='{$lang_reqdetails.add_request}'></form>
   </td>
</tr>
{/if}
{else}
<tr>
   <td class='lista' align='center' width='100%' colspan='2'><form method='get' action='requests.php#add'><input type='submit' value='{$lang_reqdetails.add_request}'></form></td>
</tr>
{/if}
</table>
