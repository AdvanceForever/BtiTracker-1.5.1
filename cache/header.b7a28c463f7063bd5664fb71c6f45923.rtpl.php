<?php if(!class_exists('raintpl')){exit;}?><?php if( $is_registered ){ ?>


<div class='logo'>
    <img src='style/base/images/logo.png' alt='Logo' class='logo-image'>
    <div class='stats'>
    <?php echo $lang_welcome_back;?> <b><?php echo $cur_username;?></b> -- <a href='logout.php?check_hash=<?php echo $logout_salt;?>'><font color='#413F36'>Logout</font></a><?php if( $seedbonus_enabled ){ ?>&nbsp; &nbsp;<a href='seedbonus.php'><font color='#ADD8E6'>Bonus:</font></a> <?php echo $user_bonus;?><?php } ?> <?php if( $hitandrun_enabled ){ ?>&nbsp;&nbsp;<font color='#ADD8E6'>Hit&amp;Run's:</font> <?php echo $hit_and_runs;?><?php } ?>

    <br />
    <font color='yellow'>Ratio:</font> <?php echo $user_ratio;?>&nbsp; &nbsp;<font color='lime'>U:</font> <?php echo $user_uploaded;?>&nbsp; &nbsp;<font color='orange'>D:</font> <?php echo $user_downloaded;?>

    
    <?php if( $admincp_access ){ ?>

       &nbsp; -- &nbsp;<a href='admincp.php?user=<?php echo $cur_id;?>&amp;code=<?php echo $random;?>'><font color='#614051'>AdminCP</font></a>
    <?php } ?>


    &nbsp; -- &nbsp;<a href='usercp.php?uid=<?php echo $cur_id;?>'><font color='#614051'>Profile</font></a>
    &nbsp; -- &nbsp;<?php echo $user_pm;?>

</div>
</div>

<div id='pages'>
    <div id='pages-inside'>
	<ul class='nav superfish'>
	<li class='current_page_item'><a href='index.php'><?php echo $lang_index;?></a></li>
        <?php if( $view_torrents ){ ?>		
	   <li class='page_item'><a href='torrents.php'><?php echo $lang_torrents;?></a></li>
           <li class='page_item'><a href='extra-stats.php'><?php echo $lang_stats;?></a></li>
        <?php } ?>


        <?php if( $can_upload ){ ?>

           <li class='page_item'><a href='upload.php'><?php echo $lang_upload;?></a></li>
        <?php } ?>


        <?php if( $requests_enabled ){ ?>

           <li class='page_item'><a href='viewrequests.php'>Requests</a></li>
        <?php } ?>


        <?php if( $view_users ){ ?>

           <li class='page_item'><a href='users.php'><?php echo $lang_members;?></a></li>
        <?php } ?>


        <?php if( $view_forum ){ ?>

           <li class='page_item'><a href='forum.php'><?php echo $lang_forum;?></a></li>
        <?php } ?>


</ul>
</div>

<!--Donation-->
<span style='float: right; margin-right: 20px; margin-top: -8px;'>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
   <input type="hidden" name="cmd" value="_s-xclick">
   <input type="hidden" name="hosted_button_id" value="Z2CDWYNVAJ2VE">
   <input type="image" src="./style/base/tpl/images/donate.png" title='Donate' width='36' border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
</form>

</span>
</div>
<div style='clear: both;'></div>

<div id='bodywrap'></div>

<table width='100%' height='100%' border='0'>
<tr>
    <td valign='top'>
<div id='wrapper2'>

<?php } ?>

