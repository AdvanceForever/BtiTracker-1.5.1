{literal}
<script type='text/javascript' language='JavaScript'>
   function ShowHide(id,id1) {
      obj = document.getElementsByTagName('div');
      if (obj[id].style.display == 'block'){
         obj[id].style.display = 'none';
         obj[id1].style.display = 'block';
      } else {
         obj[id].style.display = 'block';
         obj[id1].style.display = 'none';
      }
   }
</script>
{/literal}

<div align='center'>
<table class='lista' width='100%' border='0' cellspacing='1' cellpadding='5'>
<tr>
   <td align='right' class='header'>{$lang.filename}

   {if $space}
      <br />&nbsp;&nbsp;
   {/if}

   {if $can_edit}
      <a href='edit.php?info_hash={$info_hash}&amp;returnto={$info_hash2}'>{$edit_button}</a>&nbsp;&nbsp;
   {/if}

   {if $can_delete}
      <a href='delete.php?info_hash={$info_hash}&amp;returnto={$redirect_tor_page}'>{$delete_button}</a>
   {/if}

   </td>
   <td class='lista' align='left'>{$filename}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.torrent}:</td>
   <td class='lista' align='left'><a href='download.php?id={$info_hash}&amp;f={$filename2}.torrent'>{$filename}</a></td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.info_hash}:</td>
   <td class='lista' align='left'>{$info_hash}</td>
</tr>

{if $nuked_requested_on}
<tr>
   <td align='right' class='header'>{$lang.requested}:</td>
   <td align='left' class='lista'>{$requested}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.nuked}:</td>
   <td align='left' class='lista'>{$nuked}</td>
</tr>

{if $is_nuked}
<tr>
   <td align='right' class='header'>{$lang.nuked_reason}:</td>
   <td align='left' class='lista'>{$nuked_reason}</td>
</tr>
{/if}
{/if}

{if $image_link_on}
{if $has_image}
<tr>
   <td align='right' class='header'>Image:</td>
   <td class='lista' align='left'><img src='{$torrent_image}' width='200'></a></td>
</tr>
{else}
<tr>
   <td align='right' class='header'>Image:</td>
   <td class='lista' align='left'>No Image Provided!</a></td>
</tr>
{/if}
{/if}

{if $has_description}
<tr>
   <td align='right' class='header'>{$lang.description}:</td>
   <td align='left' class='lista'>{$description}</td>
</tr>
{/if}

{if $has_category}
<tr>
   <td align='right' class='header'>{$lang.category}:</td>
   <td class='lista' align='left'>{$category}</td>
</tr>
{else}
<tr>
   <td align='right' class='header'>{$lang.category}:</td>
   <td class='lista' align='left'>(None)</td>
</tr>
{/if}

<tr>
   <td align='right' class='header'>{$lang.rating}:</td>
   <td class='lista' align='left'>{$rating}</td>
</tr>
<tr>
   <td align=right class='header'>{$lang.size}:</td>
   <td class='lista' align='left'>{$size}</td>
</tr>

{if $has_url}
<tr>
   <td align='right' class='header' valign='top'><a name='#expand' href='#expand' onclick="javascript:ShowHide('files', 'msgfile');">Show/Hide Files: </td>
   <td align='left' class='lista'>
      <div name='files' style='display:none' id='files'>
      <table class='lista'>
      <tr>
        <td align='center' class='header'>{$lang.filename}</td>
        <td align='center' class='header'>{$lang.size}</td>
      </tr>
{$nfiles}
      </table>
      </div>
      <div name='msgfile' style='display:block' id='msgfile' align='left'>{$files_count}{$show_files}</div>
   </td>
</tr>
{/if}

<tr>
   <td align='right' class='header'>{$lang.added}:</td>
   <td class='lista' align='left'>{$added}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.uploader}:</td>
   <td class='lista' align='left'>{$uploader}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.speed}:</td>
   <td class='lista' align='left'>{$speed}</td>
</tr>

{if $is_external_no}
<tr>
   <td align='right' class='header'>{$lang.downloaded}:</td>
   <td class='lista' align='left'><a href='torrent_history.php?id={$info_hash}'>{$finished}</a> {$lang.x_times}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.peers}:</td>
   <td class='lista' align='left'>{$lang.seeders}: <a href='peers.php?id={$info_hash}'>{$seeders}</a>, {$lang.leechers}: <a href='peers.php?id={$info_hash}'>{$leechers}</a> = <a href='peers.php?id={$info_hash}'>{$peers}</a> {$lang.peers}</td>
</tr>
{else}
<tr>
   <td align='right' class='header'>{$lang.downloaded}:</td>
   <td class='lista' align='left'>{$finished} {$lang.x_times}</td>
</tr>
<tr>
   <td align='right' class='header'>{$lang.peers}:</td>
   <td class='lista' align='left'>{$lang.seeders}: {$seeders}, {$lang.leechers}: {$leechers} = {$peers} {$lang.peers}</td>
</tr>
{/if}

{if $is_external_yes}
<tr>
   <td valign='middle' align='left' class='header'><a href='details.php?act=update&id={$info_hash}&amp;surl={$announce_url2}'>{$lang.update}</a></td>
   <td class='lista' align='center'><b>EXTERNAL</b><br />{$announce_url}</td>
</tr>
<tr>
   <td valign='middle' align='left' class='header'>{$lang.last_update}</td>
   <td class='lista' align='center'>{$last_update}</td>
</tr>
{/if}

</table>

<a name='comments' /></a>
<br />
{$comments}

</div><br /><center><a href='javascript: history.go(-1);'>{$lang.back}</a></center>
