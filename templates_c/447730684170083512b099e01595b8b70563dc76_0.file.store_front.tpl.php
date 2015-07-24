<?php /* Smarty version 3.1.27, created on 2015-07-24 21:09:10
         compiled from "W:\domains\simpleshop\presentation\templates\store_front.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:3167855b27f46c18917_56504571%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '447730684170083512b099e01595b8b70563dc76' => 
    array (
      0 => 'W:\\domains\\simpleshop\\presentation\\templates\\store_front.tpl',
      1 => 1437686958,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3167855b27f46c18917_56504571',
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55b27f46eb60e7_56272327',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55b27f46eb60e7_56272327')) {
function content_55b27f46eb60e7_56272327 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '3167855b27f46c18917_56504571';
?>

<?php  Smarty_Internal_Extension_Config::configLoad($_smarty_tpl, "site.conf", null, 'local');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <title><?php echo $_smarty_tpl->getConfigVariable( 'site_title');?>
</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" rel="stylesheet" href="styles/simple.css" />
  </head>
  <body>
    <div id="doc" class="yui-t2">
      <div id="bd">
        <div id="yui-main">
          <div class="yui-b">
            <div id="header" class="yui-g">
              <a href="index.php">
                <img src="images/tshirtshop.png"
                 alt="tshirtshop logo" />
              </a>
            </div>
            <div id="contents" class="yui-g">
              Place content here
            </div>
          </div>
        </div>
        <div class="yui-b">
					Place list of department
        </div>			
      </div>
    </div>
  </body>
</html>
<?php }
}
?>