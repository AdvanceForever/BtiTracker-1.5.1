<form method='post' name='compose' action='?action=post'>
{if $is_new_topic}
    <input type='hidden' name='forumid' value='{$compose_id}'>
{else}
    <input type='hidden' name='topicid' value='{$compose_id}'>
{/if}

{$begin_table}
    
{if $is_new_topic}
    <tr>
        <td class='header'>{$lang_subject}</td>
        <td class='lista' align='left' style='padding: 0px'><input type='text' size='50' maxlength='{$maxsubjectlength}' name='subject' style='border: 0px; height: 19px'></td>
    </tr>
{/if}

<tr>
    <td class='header'>{$lang_body}</td>
    <td class='lista' align='left' style='padding: 0px'>{$compose_body}</td>
</tr>
<tr>
    <td colspan='2' align='center'><input type='submit' class='btn' value='{$lang_confirm}'></td>
</tr>

{$end_table}
    
</form>
