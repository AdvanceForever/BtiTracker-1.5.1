<?php
/* Smarty version 3.1.32-dev-45, created on 2018-04-11 22:50:40
  from '/home/romi/Documente/html/style/base/tpl/tracker/tracker_version.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_5ace6710714e69_63586756',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'abcfeac81194cd44784d6500453904508960f088' => 
    array (
      0 => '/home/romi/Documente/html/style/base/tpl/tracker/tracker_version.tpl',
      1 => 1507166324,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ace6710714e69_63586756 (Smarty_Internal_Template $_smarty_tpl) {
?><p align='center'>
<?php if ($_smarty_tpl->tpl_vars['print_debug']->value) {?>
   <small>[ Execution Time: <?php echo $_smarty_tpl->tpl_vars['execution_time']->value;?>
 sec. ] - [Memcached Queries: <?php echo $_smarty_tpl->tpl_vars['memcached_queries_count']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['memcached_queries_time']->value;?>
 sec.)] - [Memory Usage: <?php echo $_smarty_tpl->tpl_vars['memory_usage']->value;?>
]</small>
   <br />
<?php }?>
   BtiTracker (<?php echo $_smarty_tpl->tpl_vars['tracker_version']->value;
echo $_smarty_tpl->tpl_vars['tracker_revision']->value;?>
) by <a href='https://github.com/Yupy/BtiTracker-1.5.1' target='_blank'>Yupy</a> & <a href='http://www.btiteam.org' target='_blank'>Btiteam</a>
</p>
<?php }
}
