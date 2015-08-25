{if="$is_registered"}
<table width='100%' height='100%'  border='0'>
<tr>
    <td>
       <div class='row'>
       <div id='navContainer'>
       <div id='navRepeatPar'>
       <div id='bgRepeat'>
          <img src='style/base/images/menu_back.png' height='100%' width='100%'>
       </div>
       <div class="logo text-center" id="logo">
          <img src='style/base/images/tracker_logo.png'>
          <span>{$lang_welcome_back} <b>{$cur_username}</b></span>
       </div>

       <!--The Menu-->
       <div id='mainNavigation'>
          <ul id="menu-main-navigation">
             <li><a href='./'>{$lang_index}</a></li>
             {if="$view_torrents"}
                 <li><a href='torrents.php'>{$lang_torrents}</a></li>
                 <li><a href='extra-stats.php'>{$lang_stats}</a></li>
             {/if}

             {if="$can_upload"}
                 <li><a href='upload.php'>{$lang_upload}</a></li>
             {/if}

             {if="$view_users"}
                 <li><a href='users.php'>{$lang_members}</a></li>
             {/if}

             {if="$view_forum"}
                 <li><a href='forum.php'>{$lang_forum}</a></li>
             {/if}
             
             <!--User Stats-->
             <br style='line-height: 4px;'>
             <span style='font-size: 11px; position: absolute; margin-left: 35%;'><img src='images/quote.png' title='Ratio'></span>
             <span style='font-size: 11px; position: absolute; margin-left: 41%; margin-top: 8px; color: yellow;'>{$user_ratio}</span>
             <span style='font-size: 11px; margin-left: -40px;'><font color='lime'>Upped:</font> {$user_uploaded}</span>
             <br />
             <span style='font-size: 11px; margin-left: -40px;'><font color='red'>Downed:</font> {$user_downloaded}</span>
             <br />
             <br />
             {if="$admincp_access"}
                 <span style='font-size: 11px; margin-left: -40px;'><a href='admincp.php?user={$cur_id}&amp;code={$random}'><img src='images/admincp.png' title='Admin Panel'></a></span>
             {/if}
             <span style='font-size: 11px; margin-left: 7px;'><a href='usercp.php?uid={$cur_id}'><img src='images/usercp.png' hight='24' width='24' title='Edit Profile'></a></span>
<span style='font-size: 11px; margin-left: 7px;'>{$user_pm}</span>
             <!--End User Stats-->
          </ul>
       </div>
       <!--Menu End-->

       </div>
       <div id='navArrowImg'>
          <span style='position:absolute;'><img style='margin-top:50px; margin-left: 103px;' src='images/logout.png' title='Logout' alt='Logout'></span>
          <img src='style/base/images/menu_down.png' height='130' width='100%'>
       </div>
    </td>
</tr>

<table width='100%' height='100%' border='0'>
<tr>
    <td valign='top'><div style='min-height: 370px;' class='columns container back right'>
{/if}
