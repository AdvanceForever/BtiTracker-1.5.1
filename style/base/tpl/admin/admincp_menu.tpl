<table class='lista' width='100%' align='center'>
<tr>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=config&amp;action=read'>{$lang_settings}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=banip&amp;action=read'>{$lang_ban_ip}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=category&amp;action=read'>{$lang_categories}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=level&amp;action=read'>{$lang_user_group}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=language&amp;action=read'>{$lang_languages}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=polls&amp;action=read'>{$lang_polls}</a></td>
</tr>
<tr>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=style&amp;action=read'>{$lang_styles}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=forum&amp;action=read'>{$lang_forum}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=badwords&amp;action=read'>{$lang_censured}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=blocks&amp;action=read'>{$lang_blocks}</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=dbutil'>Mysql Database<br />Stats/Utils</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=masspm&amp;action=write'>Mass PM</a></td>
</tr>
<tr>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=prunet'>Prune Torrents</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=pruneu'>Prune Users</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=logview'>View Sitelog</a></td>
   <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=searchdiff'>Search Diff.</a></td>

   {if $duplicate_ips}
      <td class='header' align='center'><a href='admin.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;action=duplicateips'>Duplicate IP's</a></td>
   {/if}

   {if $hit_and_run}
      <td class='header' align='center'><a href='admin.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;action=hitrun'>Hit &amp; Run Settings</a></td>
   {/if}

   {if $warn_system}
      <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=warnedu'>{$lang_warnedu}</a></td>
      <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=prevwarnedu'>{$lang_prevwarnedu}</a></td>
      <td class='header' align='center'><a href='admincp.php?user={$admincpmenu_uid}&amp;code={$admincpmenu_random}&amp;do=disabledu'>{$lang_disabledu}</a></td>
   {/if}
</tr>
</table>
