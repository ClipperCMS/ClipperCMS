<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

include_once ("browsercheck.inc.php");
$browser = $client->property('browser');
$_SESSION['browser'] = $browser;
$version = $client->property('version');
$_SESSION['browser_version'] = $version;
$mxla = $modx_lang_attribute ? $modx_lang_attribute : 'en';
?>
<!doctype html>
<html lang="<?php echo $mxla?>"<?php ($modx_textdir ? ' dir="rtl"' : '') ?>>
<head>
	<meta charset="<?php echo $modx_manager_charset; ?>">
	<title><?php echo $site_name.' - '.CMS_NAME; ?> manager</title>
</head>
<frameset rows="80,*" border="0">
	<frame name="mainMenu" src="index.php?a=1&amp;f=menu" scrolling="no" frameborder="0" noresize="noresize">
<?php if (!$modx_textdir) {
	// Left-to-Right reading (sidebar on left)
	?>
	<frameset cols="260,*" border="3" frameborder="3" framespacing="3" bordercolor="#ffffff">
		<frame name="tree" src="index.php?a=1&amp;f=tree" scrolling="no" frameborder="0" onresize="top.tree.resizeTree();">
		<frame name="main" src="index.php?a=2"  scrolling="auto" frameborder="0" onload="if (top.mainMenu.stopWork()) top.mainMenu.stopWork();">
<?php } else {
	// Right-to-Left reading (sidebar on right)
	?>
    	<frameset cols="*,260" border="3" frameborder="3" framespacing="3" bordercolor="#ffffff">
		<frame name="main" src="index.php?a=2" scrolling="auto" frameborder="0" onload="if (top.mainMenu.stopWork()) top.mainMenu.stopWork();">
		<frame name="tree" src="index.php?a=1&amp;f=tree" scrolling="no" frameborder="0" onresize="top.tree.resizeTree();">
<?php } ?>
	</frameset>
</frameset>
<noframes>This software requires a browser with support for frames.</noframes>
</html>
