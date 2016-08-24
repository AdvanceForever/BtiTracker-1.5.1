{if="$is_registered"}

<div class='logo'>
    <img src='style/base/images/logo.png' alt='Logo' class='logo-image'>
    <div class='stats'>
    {$lang_welcome_back} <b>{$cur_username}</b> -- <a href='logout.php?check_hash={$logout_salt}'><font color='#413F36'>Logout</font></a>{if="$seedbonus_enabled"}&nbsp; &nbsp;<a href='seedbonus.php'><font color='#ADD8E6'>Bonus:</font></a> {$user_bonus}{/if} {if="$hitandrun_enabled"}&nbsp;&nbsp;<font color='#ADD8E6'>Hit&amp;Run's:</font> {$hit_and_runs}{/if}
    <br />
    <font color='yellow'>Ratio:</font> {$user_ratio}&nbsp; &nbsp;<font color='lime'>U:</font> {$user_uploaded}&nbsp; &nbsp;<font color='orange'>D:</font> {$user_downloaded}
    
    {if="$admincp_access"}
       &nbsp; -- &nbsp;<a href='admincp.php?user={$cur_id}&amp;code={$random}'><font color='#614051'>AdminCP</font></a>
    {/if}

    &nbsp; -- &nbsp;<a href='usercp.php?uid={$cur_id}'><font color='#614051'>Profile</font></a>
    &nbsp; -- &nbsp;{$user_pm}
</div>
</div>

<div id='pages'>
    <div id='pages-inside'>
	<ul class='nav superfish'>
	<li class='current_page_item'><a href='index.php'>{$lang_index}</a></li>
        {if="$view_torrents"}		
	   <li class='page_item'><a href='torrents.php'>{$lang_torrents}</a></li>
           <li class='page_item'><a href='extra-stats.php'>{$lang_stats}</a></li>
        {/if}

        {if="$can_upload"}
           <li class='page_item'><a href='upload.php'>{$lang_upload}</a></li>
        {/if}

        {if="$requests_enabled"}
           <li class='page_item'><a href='viewrequests.php'>Requests</a></li>
        {/if}

        {if="$view_users"}
           <li class='page_item'><a href='users.php'>{$lang_members}</a></li>
        {/if}

        {if="$view_forum"}
           <li class='page_item'><a href='forum.php'>{$lang_forum}</a></li>
        {/if}

</ul>
</div>

<!--Donation-->
<span style='float: right; margin-right: 20px; margin-top: -8px;'>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
   <input type="hidden" name="cmd" value="_s-xclick">
   <input type="hidden" name="hosted_button_id" value="Z2CDWYNVAJ2VE">
   <input type="image" src="images/donate.png" title='Donate' width='36' border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
</form>

</span>
</div>
<div style='clear: both;'></div>

<div id='bodywrap'></div>

<table width='100%' height='100%' border='0'>
<tr>
    <td valign='top'>
<div id='wrapper2'>

{/if}
