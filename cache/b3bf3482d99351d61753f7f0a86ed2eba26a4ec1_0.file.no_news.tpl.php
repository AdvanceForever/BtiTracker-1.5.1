<?php
/* Smarty version 3.1.32-dev-45, created on 2018-04-11 22:50:48
  from '/home/romi/Documente/html/style/base/tpl/tracker/no_news.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_5ace6718944412_87238721',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b3bf3482d99351d61753f7f0a86ed2eba26a4ec1' => 
    array (
      0 => '/home/romi/Documente/html/style/base/tpl/tracker/no_news.tpl',
      1 => 1507166324,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ace6718944412_87238721 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['has_output']->value) {?>
<center><?php echo $_smarty_tpl->tpl_vars['lang_no_news']->value;?>
...<br />
<?php if ($_smarty_tpl->tpl_vars['can_edit_news']->value) {?>
<br />
<a href='news.php'>
   <img border='0' alt='<?php echo $_smarty_tpl->tpl_vars['lang_add']->value;?>
' src='images/new.gif'>
</a>
<br />
</center>
<?php }
}
}
}
