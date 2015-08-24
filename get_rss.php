<?php    
//Torrent RSS by DiemThuy ( jul 2012 ) TBDEV conversion with some improvements; Adapated by Yupy for BtiTracker... 
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

if (user::$current['view_torrents'] == 'yes') {
    standardheader('RSS');

    $res = $db->query("SELECT id, name, image FROM categories ORDER BY name");
    while ($cat = $res->fetch_assoc())
       if ($cat['image'] == '') {
           $catoptions .= ''; 
       } else {    
           $catoptions .= "<a href='torrents.php?category=" . $cat['id'] . "'>" . image_or_link(($cat['image'] == ''?'':"$STYLEPATH/images/categories/" . $cat['image']), '', security::html_safe($cat['name']))."</a><input type='checkbox' name='cat[]' value='" . (int)$cat['id'] . "' "  . (strpos($CURUSER['notifs'], "[cat$cat[id]]") !== false ? " checked" : '') . "/>";
       }
        
       if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           if (empty($_POST['cat']))
               stderr('Error', 'You need to chose at least one Category !');

           if (empty($_POST['feed']))
               stderr('Error', 'You need to chose a feed type !');

           $link = $BASEURL . '/rss_torrents.php';
           if ($_POST['feed'] == 'dl')
               $query[] = 'feed=dl';
           foreach($_POST['cat'] as $cat)
               $query[] = 'cat[]=' . $cat;
      
           $query[] = 'pid=' . user::$current['pid'];
           $queries = implode('&', $query);
           if ($queries)
               $link .= '?' . $queries;
            
           if ($_POST['feed'] == 'dl') {          
	       err_msg('RSS Link', 'Use the following url in your RSS reader:<br><b>' . $link . '</b><br>');
               stdfoot();
               exit();
           } else
               header('Refresh: 0; url=' . $link);        
      }

    block_begin('Get RSS');
    ?>
    <form method='POST' action='get_rss.php'>
    <table class='header' width='80%' align='center'>
    <tr>
       <td class='header' width='30%'>Categories:</td>
       <td class='lista' width='50%'><?php echo $catoptions; ?></td>
    </tr>
    <tr>
       <td width='30%'><br /></td>
    </tr>
    <tr>
       <td class='header'>Feed Type:</td>
       <td>
          <input type='radio' name='feed' value='web' />Web Link
          <input type='radio' name='feed' value='dl' />Download Link
          <br />
       </td>
    </tr> 
    <tr>
       <td style="text-align:center"><br><button type="submit">Get RSS<br></button></td>
    </tr>
    </table>
    </form>
    <?php
    block_end();
    stdfoot();
}
?>

