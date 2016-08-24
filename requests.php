<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

standardheader('Requests');
begin_frame(MAKE_REQUEST );

print("<br />\n");

$where = "WHERE userid = " . user::$current['uid'];
$res2 = $db->query("SELECT * FROM requests " . $where) or sqlerr();
$num2 = $res2->num_rows;

?>

<table align='center' border='0' width='100%' cellspacing='0' cellpadding='3'>
<tr>
   <td class='header' align='center'><?php print(SEARCH . " " . TORRENT); ?></td>
</tr>
<tr>
   <td align='center' class='lista'><form method='get' action='torrents.php'>
   <input type='text' name="<?php print('search'); ?>" size='40' value="<?php echo security::html_safe($searchstr) ?>" />
   in
   <select name='category'>
   <option value='0'>(Select)</option>

   <?php

   $cats = Cached::genrelist();
   $catdropdown = '';
   foreach ($cats as $cat) {
            $catdropdown .= "<option value='" . (int)$cat['id'] . "'";
            if ($cat['id'] == $_GET['cat'])
                $catdropdown .= " selected='selected'";
            $catdropdown .= ">" . security::html_safe($cat['name']) . "</option>\n";
   }

   $deadchkbox = "<input type='checkbox' name='active' value='0'";
   if ($_GET['active'])
       $deadchkbox .= " checked='checked'";
   $deadchkbox .= " /> " . INC_DEAD . "\n";

   echo $catdropdown;
   print('</select>');
   echo $deadchkbox;
   print("<input type='submit' value='" . SEARCH . "' />
   </form>
   </td></tr></table><br><hr>");

   print("<br>\n");
   print("<table class='lista' align='center' width='550' cellspacing='2'><form name='request' method='post' action='takerequest.php'><a name='add' id='add'></a>");
   print("<tr><td class='header' align='center' width='100%' colspan='2'>" . ADD_REQUESTS . "</td></tr>");
   print("<tr><td class='header' align='left' width='30%'>". TORRENT_FILE . "</td><td class='lista' align='left' width='70%'><input type='text' size='40' name='requesttitle'></td></tr>");
   print("<tr><td class='header' align='left' width='30%'>" . CATEGORY . "</td><td class='lista' align='left' width='70%'>");

   print("<select name='category'><option value='0'>(Select)</option>");

   $res2 = $db->query("SELECT id, name FROM categories ORDER BY name");
   $num = $res2->num_rows;

   $catdropdown2 = '';
   for ($i = 0; $i < $num; ++$i) {
        $cats2 = $res2->fetch_assoc();  
        $catdropdown2 .= "<option value='" . (int)$cats2['id'] . "'";
        $catdropdown2 .= ">" . security::html_safe($cats2['name']) . "</option>\n";
   }

   echo $catdropdown2;

   print("</select>");

   print("<br>\n");

   print("<tr><td class='header' align='left' width='30%'>" . DESCRIPTION . "</td><td class='lista' align='left' width='70%'>");
   print(textbbcode('request', 'description'));
   print("</td></tr>");
   print("<tr><td class='lista' align='center' width='100%' colspan='2'><input type='submit' value='" . FRM_CONFIRM . "'></td></tr>");
   print("</form>\n");
   print("</table>\n");

   block_end();
   stdfoot();

?>
