<?php
/* Smarty version 3.1.32-dev-45, created on 2018-04-11 22:50:40
  from '/home/romi/Documente/html/style/base/tpl/torrent/upload.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_5ace6710702d63_91481304',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5306362a273f8e0805441d2bb46f39e7bb780b7a' => 
    array (
      0 => '/home/romi/Documente/html/style/base/tpl/torrent/upload.tpl',
      1 => 1507166324,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ace6710702d63_91481304 (Smarty_Internal_Template $_smarty_tpl) {
?></center>
<center><?php echo $_smarty_tpl->tpl_vars['insert_data']->value;?>
<br /><br />
<?php echo $_smarty_tpl->tpl_vars['announce_url']->value;?>
<br /><b>
<?php echo $_smarty_tpl->tpl_vars['tracker_announce_url']->value;?>
<br />
</b><br />
</center>

<form name='upload' method='post' enctype='multipart/form-data'>
<table class='lista' align='center'>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['torrent_file']->value;?>
</td>
   <td class='lista' align='left'>
   <?php if ($_smarty_tpl->tpl_vars['sha1_exists']->value) {?>
       <input type='file' name='torrent'>
   <?php } else { ?>
       <i><?php echo $_smarty_tpl->tpl_vars['no_sha1']->value;?>
</i>
   <?php }?>
   </td>
</tr>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</td>
   <td class='lista' align='left'><?php echo $_smarty_tpl->tpl_vars['categories']->value;?>
</td>
</tr>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['filename']->value;?>
</td>
   <td class='lista' align='left'><input type='text' name='filename' size='50' maxlength='200' /></td>
</tr>
<?php if ($_smarty_tpl->tpl_vars['image_on']->value) {?>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['image_link']->value;?>
</td>
   <td class='lista' align='left'><input type='text' name='image' size='50' maxlength='500' /></td>
</tr>
<?php }
if ($_smarty_tpl->tpl_vars['torrent_genre']->value) {?>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['genre']->value;?>
</td>
   <td class="lista" ><input type='text' name='genre' size='20' maxlength='50' /></td>
</tr>
<?php }
if ($_smarty_tpl->tpl_vars['nuked_requested']->value) {?>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['torrent_requested']->value;?>
</td>
   <td class='lista'>
      <select name='requested' size='1'>
         <option value='false' selected='selected'><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
</option>
         <option value='true'><?php echo $_smarty_tpl->tpl_vars['yes']->value;?>
</option>
      </select>
   </td>
</tr>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['torrent_nuked']->value;?>
</td>
   <td class='lista'>
      <select name='nuked' size='1'>
         <option value='false' selected='selected'><?php echo $_smarty_tpl->tpl_vars['no']->value;?>
</option>
         <option value='true'><?php echo $_smarty_tpl->tpl_vars['yes']->value;?>
</option>
      </select>
      &nbsp;<input type='text' name='nuked_reason' size='43' maxlength='100'>
   </td>
</tr>
<?php }?>
<tr>
   <td class='header' valign='top'><?php echo $_smarty_tpl->tpl_vars['description']->value;?>
</td>
   <td class='lista' align='left'><?php echo $_smarty_tpl->tpl_vars['description_body']->value;?>
</td>
</tr>
<tr>
   <td colspan='2'><input type='hidden' name='user_id' size='50' value='<?php echo $_smarty_tpl->tpl_vars['user_id']->value;?>
' /></td>
</tr>
<tr>
   <td class='header'><?php echo $_smarty_tpl->tpl_vars['anonymous']->value;?>
</td>
   <td class='lista'>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['no']->value;?>
<input type='radio' name='anonymous' value='false' checked />&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['yes']->value;?>
<input type='radio' name='anonymous' value='true' /></td>
</tr>

<?php if ($_smarty_tpl->tpl_vars['sha1_exists']->value) {?>
<tr>
   <td class='lista' align='center' colspan='2'><input type='checkbox' name='autoset' value='enabled' disabled checked /><?php echo $_smarty_tpl->tpl_vars['torrent_check']->value;?>
</td>
</tr>
<?php }?>

<tr>
   <td align='right'><input type='submit' value='<?php echo $_smarty_tpl->tpl_vars['send']->value;?>
' /></td>
   <td align='left'><input type='reset' value='<?php echo $_smarty_tpl->tpl_vars['reset']->value;?>
' /></td>
</tr>
</table>
</form>

</td>
</tr>
</table>
<?php }
}
