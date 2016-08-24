<p align='center'>
<table class='main' border='0' cellspacing='0' cellpadding='0'>
<tr>
   <td class='embedded'>
      <form method='get' action='?'>
         <input type='hidden' name='action' value='viewunread'>
         <input type='submit' value='{$lang_view_unread}' class='btn'>
      </form>
   </td>
    
   {if $maypost}
   <td class='embedded' style='padding-left: 10px'>
      <form method='get' action='?'>
         <input type='hidden' name='action' value='reply'>
         <input type='hidden' name='topicid' value='{$rtopic_id}'>
         <input type='submit' value='{$lang_add_reply}' class='btn'>
      </form>
   </td>
   {/if}
</tr>
</table>
</p>
