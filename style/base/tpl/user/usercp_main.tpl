<table class='lista' width='100%'>
<tr>
    <td class='header'>{$lang_usercp.username}</td>
    <td class='lista'>{$usercp_username}</td>

    {if $usercp_has_avatar}
        <td class='lista' align='center' valign='middle' rowspan='4'><img border='0' width='138' src='{$usercp_avatar}' /></td>
    {/if}
</tr>

{if $usercp_is_staff}
    <tr>
        <td class='header'>{$lang_usercp.email}</td>
        <td class='lista'>{$usercp_email}</td>
    </tr>
    <tr>
        <td class='header'>{$lang_usercp.ip}</td>
        <td class='lista'>{$usercp_ip}</td>
    </tr>
    <tr>
        <td class='header'>{$lang_usercp.rank}</td>
        <td class='lista'>{$usercp_rank}</td>
    </tr>
{else}
    <tr>
        <td class='header'>{$lang_usercp.rank}</td>
        <td class='lista'>{$usercp_rank}</td>
    </tr>
{/if}

<tr>
    <td class='header'>{$lang_usercp.joined}</td>
    <td class='lista'{$usercp_colspan}>{$usercp_joined}</td>
</tr>
<tr>
    <td class='header'>{$lang_usercp.lastaccess}</td>
    <td class='lista'{$usercp_colspan}>{$usercp_lastaccess}</td>
</tr>
<tr>
    <td class='header'>{$lang_usercp.country}</td>
    <td class='lista' colspan='2'>{$usercp_country}</td>
</tr>
<tr>
    <td class='header'>{$lang_usercp.downloaded}</td>
    <td class='lista' colspan='2'>{$usercp_downloaded}</td>
</tr>
<tr>
    <td class='header'>{$lang_usercp.uploaded}</td>
    <td class='lista' colspan='2'>{$usercp_uploaded}</td>
</tr>
<tr>
    <td class='header'>{$lang_usercp.ratio}</td>
    <td class='lista' colspan='2'>{$usercp_ratio}</td>
</tr>
<tr>
    <td class='header'>{$lang_usercp.posts}:</td>
    <td class='lista' colspan='2'>{$usercp_forum_posts}</td>
</tr>
</table>
