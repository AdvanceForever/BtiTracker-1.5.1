<table class='lista' width='100%'>
<tr>
   {if $is_guest}
       <td class='header' align='center'>{$lang_welcome_guest}&nbsp&nsbp<a href='login.php'>({$lang_login})</a></td>
   {elseif $is_guest1}
       <td class='header' align='center'>{$lang_welcome} {$cur_username} <a href='login.php'>({$lang_login})</a></td>
   {else}
       <td class='header' align='center'>{$lang_welcome_back} {$cur_username} <a href='logout.php?check_hash={$logout_salt}'>({$lang_logout})</a></td>
   {/if}

   <td class='header' align='center'><a href='./'>{$lang_index}</a></td>

   {if $view_torrents}
       <td class='header' align='center'><a href='torrents.php'>{$lang_torrents}</a></td>
       <td class='header' align='center'><a href='extra-stats.php'>{$lang_stats}</a></td>
   {/if}

   {if $can_upload}
       <td class='header' align='center'><a href='upload.php'>{$lang_upload}</a></td>
   {/if}

   {if $view_users}
       <td class='header' align='center'><a href='users.php'>{$lang_members}</a></td>
   {/if}

   {if $view_news}
       <td class='header' align='center'><a href='viewnews.php'>{$lang_news}</a></td>
   {/if}

   {if $view_forum}
       <td class='header' align='center'><a href='forum.php'>{$lang_forum}</a></td>
   {/if}
</tr>
</table>
