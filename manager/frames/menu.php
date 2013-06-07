<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

$mxla = $modx_lang_attribute ? $modx_lang_attribute : 'en';
?>

<!doctype html>
<html lang="<?php echo $mxla?>"<?php ($modx_textdir ? ' dir="rtl"' : '') ?>>
<head>
	<meta charset="<?php echo $modx_manager_charset; ?>">
	<title>nav</title>
	<link rel="stylesheet" href="media/style/<?php echo $manager_theme?>/style.css" />

	<script>
    	
    	var lang = {
    		show_tree: '<?php echo str_replace("'", "\'", $_lang['show_tree']); ?>',
    		icons_loading_doc_tree : '<?php echo $_style['icons_loading_doc_tree']; ?>',
    		loading_doc_tree : '<?php echo str_replace("'", "\'", $_lang['loading_doc_tree']); ?>',
    		loading_menu : '<?php echo str_replace("'", "\'", $_lang['loading_menu']); ?>',
    		working : '<?php echo str_replace("'", "\'", $_lang['working']); ?>',
    		confirm_remove_locks : '<?php echo str_replace("'", "\'", $_lang['confirm_remove_locks']); ?>'
    	}
    	
    	var style = {
    		show_tree: '<?php echo $_style['show_tree'] ?>',
    		icons_working: '<?php echo $_style['icons_working'] ?>'
    	}
    	
    	var currentFrameState = 'open';
		var defaultFrameWidth = '<?php echo !$modx_textdir ? '260,*' : '*,260'?>';
		var userDefinedFrameWidth = '<?php echo !$modx_textdir ? '260,*' : '*,260'?>';
	
		var workText;
		var buildText;

		var modx_textdir = '<?php echo $modx_textdir ?>';
		var manager_layout = '<?php echo $manager_layout?>';

    </script>
    
	<?php
	echo $modx->getJqueryTag();
	echo $modx->getJqueryPluginTag('jquery-ui-custom-clippermanager', 'jquery-ui-custom-clippermanager.min.js');
	echo $modx->getJqueryPluginTag('jquery-ui-timepicker', 'jquery-ui-timepicker-addon.js');
	?>
    <script src="media/script/keep-session-alive.js"></script>
    <script src="media/script/main-menu.js"></script>

</head>
<body id="topMenu" class="<?php echo $modx_textdir ? 'rtl':'ltr'?>">

<div id="tocText"<?php echo $modx_textdir ? ' class="tocTextRTL"' : '' ?>></div>
<div id="topbar">
	<div id="topbar-container">
		
		<div id="statusbar">
			<span id="buildText"></span>
			<span id="workText"></span>
		</div>
		
	<div id="supplementalNav">
	<?php
	echo $modx->getLoginUserName(). ($modx->hasPermission('change_password') ? ': <a onclick="this.blur();" href="index.php?a=28" target="main">'.$_lang['change_password'].'</a>'."\n" : "\n");
	if($modx->hasPermission('help')) { ?>
		| <a href="index.php?a=9" target="main"><?php echo $_lang['help']?></a>
	<?php } ?>
		| <a href="index.php?a=8" target="_top"><?php echo $_lang['logout']?></a>
		| <span title="<?php echo $site_name ?> &ndash; <?php echo CMS_FULL_APPNAME; ?>"><?php echo $modx_version ?></span>&nbsp;
		<!-- close #supplementalNav --></div>
	</div>
</div>

<form name="menuForm" action="l4mnu.php" class="clear">
	<input name="sessToken" id="sessTokenInput" value="<?php echo md5(session_id());?>" />
	<div id="Navcontainer">
		<div id="divNav">
			<ul id="nav">
				<?php
				// Concatenate menu items based on permissions
				//TODO: onclick="this.blur();" should be removed an replaced with something else if possible
				// Site Menu
				$sitemenu = array();
				// home
				$sitemenu[] = '<li><a onclick="this.blur();" href="index.php?a=2" target="main">'.$_lang['home'].'</a></li>';
				// preview
				$sitemenu[] = '<li><a onclick="this.blur();" href="../" target="_blank">'.$_lang['preview'].'</a></li>';
				// clear-cache
				$sitemenu[] = '<li><a onclick="this.blur();" href="index.php?a=26" target="main">'.$_lang['refresh_site'].'</a></li>';
				// search
				$sitemenu[] = '<li><a onclick="this.blur();" href="index.php?a=71" target="main">'.$_lang['search'].'</a></li>';
				if ($modx->hasPermission('new_document')) {
					// new-document
					$sitemenu[] = '<li><a onclick="this.blur();" href="index.php?a=4" target="main">'.$_lang['add_resource'].'</a></li>';
					// new-weblink
					$sitemenu[] = '<li><a onclick="this.blur();" href="index.php?a=72" target="main">'.$_lang['add_weblink'].'</a></li>';
				}
				
				// Elements Menu
				$resourcemenu = array();
				if($modx->hasPermission('new_template') || $modx->hasPermission('edit_template') || $modx->hasPermission('new_snippet') || $modx->hasPermission('edit_snippet') || $modx->hasPermission('new_chunk') || $modx->hasPermission('edit_chunk') || $modx->hasPermission('new_plugin') || $modx->hasPermission('edit_plugin')) {
					// Elements
					$resourcemenu[] = '<li><a onclick="this.blur();" href="index.php?a=76" target="main">'.$_lang['element_management'].'</a></li>';
				}
				if($modx->hasPermission('file_manager')) {
					// Manage-Files
					$resourcemenu[] = '<li><a onclick="this.blur();" href="index.php?a=31" target="main">'.$_lang['manage_files'].'</a></li>';
				}
				
				// Modules Menu Items
				$modulemenu = array();
				if($modx->hasPermission('new_module') || $modx->hasPermission('edit_module')) {
					// manage-modules
					$modulemenu[] = '<li><a onclick="this.blur();" href="index.php?a=106" target="main">'.$_lang['module_management'].'</a></li>';
				}
				if($modx->hasPermission('exec_module')) {
					// Each module
					if ($_SESSION['mgrRole'] != 1) {
						// Display only those modules the user can execute
						$rs = $modx->db->query('SELECT DISTINCT sm.id, sm.name, mg.member
								FROM '.$modx->getFullTableName('site_modules').' AS sm
								LEFT JOIN '.$modx->getFullTableName('site_module_access').' AS sma ON sma.module = sm.id
								LEFT JOIN '.$modx->getFullTableName('member_groups').' AS mg ON sma.usergroup = mg.user_group
								WHERE (mg.member IS NULL OR mg.member = '.$modx->getLoginUserID().') AND sm.disabled != 1');
					} else {
						// Admins get the entire list
						$rs = $modx->db->select('*', $modx->getFullTableName('site_modules'), 'disabled != 1');
					}
					while ($content = $modx->db->getRow($rs)) {
						$modulemenu[] = '<li><a onclick="this.blur();" href="index.php?a=112&amp;id='.$content['id'].'" target="main">'.$content['name'].'</a></li>';
					}
				}
				
				// Security menu items (users)
				$securitymenu = array();
				if($modx->hasPermission('edit_user')) {
					// manager-users
					$securitymenu[] = '<li><a onclick="this.blur();" href="index.php?a=75" target="main">'.$_lang['user_management_title'].'</a></li>';
				}
				if($modx->hasPermission('edit_web_user')) {
					// web-users
					$securitymenu[] = '<li><a onclick="this.blur();" href="index.php?a=99" target="main">'.$_lang['web_user_management_title'].'</a></li>';
				}
				if($modx->hasPermission('new_role') || $modx->hasPermission('edit_role') || $modx->hasPermission('delete_role')) {
					// roles
					$securitymenu[] = '<li><a onclick="this.blur();" href="index.php?a=86" target="main">'.$_lang['role_management_title'].'</a></li>';
				}
				if($modx->hasPermission('access_permissions')) {
					// manager-perms
					$securitymenu[] = '<li><a onclick="this.blur();" href="index.php?a=40" target="main">'.$_lang['manager_permissions'].'</a></li>';
				}
				if($modx->hasPermission('web_access_permissions')) {
					// web-user-perms
					$securitymenu[] = '<li><a onclick="this.blur();" href="index.php?a=91" target="main">'.$_lang['web_permissions'].'</a></li>';
				}
				
				// Tools Menu
				$toolsmenu = array();
				if($modx->hasPermission('bk_manager')) {
					// backup-mgr
					$toolsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=93" target="main">'.$_lang['bk_manager'].'</a></li>';
				}
				if($modx->hasPermission('remove_locks')) {
					// unlock-pages
					$toolsmenu[] = '<li><a onclick="this.blur();" href="javascript:removeLocks();">'.$_lang['remove_locks'].'</a></li>';
				}
				if($modx->hasPermission('import_static')) {
					// import-html
					$toolsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=95" target="main">'.$_lang['import_site'].'</a></li>';
				}
				if($modx->hasPermission('settings')) {
					// configuration
					$toolsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=17" target="main">'.$_lang['edit_settings'].'</a></li>';
				}
				
				// Reports Menu
				$reportsmenu = array();
				// site-sched
				$reportsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=70" target="main">'.$_lang['site_schedule'].'</a></li>';
				if($modx->hasPermission('view_eventlog')) {
					// eventlog
					$reportsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=114" target="main">'.$_lang['eventlog_viewer'].'</a></li>';
				}
				if($modx->hasPermission('logs')) {
					// manager-audit-trail
					$reportsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=13" target="main">'.$_lang['view_logging'].'</a></li>';
					// system-info
					$reportsmenu[] = '<li><a onclick="this.blur();" href="index.php?a=53" target="main">'.$_lang['view_sysinfo'].'</a></li>';
				}
				
				// Output Menus where there are items to show
				if (!empty($sitemenu)) {
					echo "\t",'<li id="limenu3" class="active"><a href="#menu3" onclick="new NavToggle(this); return false;">',$_lang['site'],'</a><ul class="subnav" id="menu3">',"\n\t\t",
						 implode("\n\t\t", $sitemenu),
						 "\n\t</ul></li>\n";
				}
				if (!empty($resourcemenu)) {
					echo "\t",'<li id="limenu5"><a href="#menu5" onclick="new NavToggle(this); return false;">',$_lang['elements'],'</a><ul class="subnav" id="menu5">',"\n\t\t",
						implode("\n\t\t", $resourcemenu),
						"\n\t</ul></li>\n";
				}
				if (!empty($modulemenu)) {
					echo "\t",'<li id="limenu9"><a href="#menu9" onclick="new NavToggle(this); return false;">',$_lang['modules'],'</a><ul class="subnav" id="menu9">',"\n\t\t",
						 implode("\n\t\t", $modulemenu),
						 "\n\t</ul></li>\n";
				}
				if (!empty($securitymenu)) {
					echo "\t",'<li id="limenu2"><a href="#menu2" onclick="new NavToggle(this); return false;">',$_lang['users'],'</a><ul class="subnav" id="menu2">',"\n\t\t",
						 implode("\n\t\t", $securitymenu),
						 "\n\t</ul></li>\n";
				}
				if (!empty($toolsmenu)) {
					echo "\t",'<li id="limenu1-1"><a href="#menu1-1" onclick="new NavToggle(this); return false;">',$_lang['tools'],'</a><ul class="subnav" id="menu1-1">',"\n\t\t",
						 implode("\n\t\t", $toolsmenu),
						 "\n\t</ul></li>\n";
				}
				if (!empty($reportsmenu)) {
					echo "\t",'<li id="limenu1-2"><a href="#menu1-2" onclick="new NavToggle(this); return false;">',$_lang['reports'],'</a><ul class="subnav" id="menu1-2">',"\n\t\t",
						 implode("\n\t\t", $reportsmenu),
						 "\n\t</ul></li>\n";
				}
				?>
			</ul>
		</div>
	</div>
</form>

</body>
</html>
