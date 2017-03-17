<?php
/* Smarty version 3.1.29, created on 2016-04-14 22:37:43
  from "E:\www\yinxingjnhweb2\template\item\itemlist.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_570fab375b62d2_55734418',
  'file_dependency' => 
  array (
    '151eafb1c9ad411ec1bc2680b61e6c6858e4c6ca' => 
    array (
      0 => 'E:\\www\\yinxingjnhweb2\\template\\item\\itemlist.html',
      1 => 1460644662,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:common/header.html' => 1,
    'file:common/left.html' => 1,
    'file:common/footer.html' => 1,
  ),
),false)) {
function content_570fab375b62d2_55734418 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once 'E:\\www\\yinxingjnhweb2\\common\\smarty\\plugins\\modifier.date_format.php';
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:common/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<!-- common content-banner-->
<div class="common" id="nei-banner"><?php echo $_smarty_tpl->tpl_vars['ItemPosterSeat']->value;?>
</div>
<!-- common content-banner -->
<!-- About_box -->
<div id="about_box">
   <table width="1000" border="0" cellpadding="0" cellspacing="0">
      <tr>
         <td align="left" valign="top" id="about_box_left">
            <dl class="about_dl">
               <dt>I</dt>
               <dd class="about_dl_dd1">项目中心</dd>
               <dd class="about_dl_dd2">ITEM CENTER</dd>
            </dl>
            <ul class="about_ul">
               <?php
$_from = $_smarty_tpl->tpl_vars['GetItemTypeList']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['value']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$__foreach_value_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
               <li><a href="./?act=item&m=index&typeid=<?php echo $_smarty_tpl->tpl_vars['value']->value['Type_ID'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['Type_Name'];?>
</a></li>
               <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>

            </ul>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:common/left.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

         </td>
         <td align="left" valign="top" id="about_box_right">
            <dl id="box_right_tit">
               <dt>项目中心</dt>
               <dd>ITEM CENTER</dd>
            </dl>
            <div id="about_name"><a href="./">首页</a>  ->  <a href="./?act=item">项目中心</a><?php echo $_smarty_tpl->tpl_vars['NavSeat']->value;?>
</div>
            <div id="NEWS_right_box">
               <?php
$_from = $_smarty_tpl->tpl_vars['ItemList']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_1_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['value']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$__foreach_value_1_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
               <dl class="right_box_dl1">
                  <dt class="NEWS_right_box_dt1">
                     <?php if ($_smarty_tpl->tpl_vars['value']->value['Item_Type'] == 1) {?>
                     <span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['Item_TypeName'];?>
]</span>
                     <?php } else { ?>
                     <a href="./?act=item&typeid=<?php echo $_smarty_tpl->tpl_vars['value']->value['Item_Type'];?>
"><span>[<?php echo $_smarty_tpl->tpl_vars['value']->value['Item_TypeName'];?>
]</span></a>
                     <?php }?>
                     <a href="./?act=item&m=view&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['Item_ID'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['Item_Title'];?>
</a></dt>
                  <dt class="NEWS_right_box_dt2">
                     <?php if (!empty($_smarty_tpl->tpl_vars['value']->value['Item_Time'])) {?>
                     <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value['Item_Time'],"Y-m-d");?>

                     <?php }?>
                  </dt>
               </dl>
               <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved_local_item;
}
if (!$_smarty_tpl->tpl_vars['value']->_loop) {
?>
               <?php
}
if ($__foreach_value_1_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved_item;
}
?>
               <div id="fenye">
                  共有:<font color="#22943a"> <?php echo $_smarty_tpl->tpl_vars['CountList']->value;?>
 </font>记录  当前:第<font color="#22943a"> <?php echo $_smarty_tpl->tpl_vars['p']->value;?>
 </font>/<font color="#22943a"><strong> <?php echo $_smarty_tpl->tpl_vars['TotalPageCount']->value;?>
 </strong></font>页
                  <?php echo $_smarty_tpl->tpl_vars['ShowPage']->value;?>

               </div>
            </div>
         </td>
      </tr>
   </table>
</div>
</div>
<!-- header begin -->
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:common/footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
