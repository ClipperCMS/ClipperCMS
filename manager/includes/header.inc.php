<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

$mxla = $modx_lang_attribute ? $modx_lang_attribute : 'en';

// invoke OnManagerRegClientStartupHTMLBlock event
$evtOut = $modx->invokeEvent('OnManagerMainFrameHeaderHTMLBlock');
$onManagerMainFrameHeaderHTMLBlock = is_array($evtOut) ? '<div id="onManagerMainFrameHeaderHTMLBlock">' . implode('', $evtOut) . '</div>' : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo  $mxla . '" lang="' .  $mxla . '"' . ($modx_textdir ? ' dir="rtl"' : ''); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $modx_manager_charset; ?>" />
	<title><?php echo CMS_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="media/style/common/style.css" />
    <?php
    if (is_file(MODX_MANAGER_PATH."media/style/$manager_theme/style.css")) {
    	echo '<link rel="stylesheet" type="text/css" href="media/style/'.$manager_theme."/style.css\" />\n";
    }
    if (is_file(MODX_MANAGER_PATH."media/style/$manager_theme/manager.css")) {
    	echo '<link rel="stylesheet" type="text/css" href="media/style/'.$manager_theme."/manager.css\" />\n";
    }

	echo $modx->getJqueryTag();
	echo $modx->getJqueryPluginTag('jquery-ui-custom-clippermanager', 'jquery-ui-custom-clippermanager.min.js');
	echo $modx->getJqueryPluginTag('jquery-ui-timepicker', 'jquery-ui-timepicker-addon.js');
	?>
	<script src="media/script/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="media/script/manager.js" type="text/javascript"></script>

	<?php
	// Include after JQuery, so this block can output code using jQuery.
	echo $onManagerMainFrameHeaderHTMLBlock;
	?>

	<script type="text/javascript">
		var config = {
			date_format: '<?php echo $modx->config['date_format']; ?>',
			time_format: '<?php echo $modx->config['time_format']; ?>',
			datepicker_year_range: '<?php echo $modx->config['datepicker_year_range']; ?>'
		}
	</script>

	<script type="text/javascript">
	
		//TODO: organize these js function better, maybe in a separate file or manager.js
	
		$(window).on('load', function () {
		  document_onload();
		});
		
		$(window).on('beforeunload', function () {
		  document_onunload();
		});

	</script>
	
	<script type="text/javascript">
	
		function document_onload() {
			stopWorker();
			hideLoader();
			<?php echo isset($_REQUEST['r']) ? " doRefresh(".$_REQUEST['r'].");" : "" ;?>;
		};

		function reset_path(elementName) {
	  		document.getElementById(elementName).value = document.getElementById('default_' + elementName).innerHTML;
		}

		var dontShowWorker = false;
		function document_onunload() {
			if(!dontShowWorker) {
				if(top.mainMenu !== undefined){
					top.mainMenu.work();
				}
			}
		};

		// set tree to default action.
		if (parent.tree) parent.tree.ca = "open";
		
		function stopWorker() {
			try {
				parent.mainMenu.stopWork();
			} catch(oException) {
				ww = window.setTimeout('stopWorker()',500);
			}
		}

		function doRefresh(r) {
			try {
				rr = r;
				top.mainMenu.startrefresh(rr);
			} catch(oException) {
				vv = window.setTimeout('doRefresh()',1000);
			}
		}
		var documentDirty=false;

		function checkDirt(evt) {
			if(documentDirty==true) {
				var message = "<?php echo $_lang['warning_not_saved']; ?>";
				if (typeof evt == 'undefined') {
					evt = window.event;
			}
				if (evt) {
					evt.returnValue = message;
		}
				return message;
			}
		}

		function saveWait(fName) {
			document.getElementById("savingMessage").innerHTML = "<?php echo $_lang['saving']; ?>";
			for(i = 0; i < document.forms[fName].elements.length; i++) {
				document.forms[fName].elements[i].disabled='disabled';
			}
		}

		var managerPath = "";

		function hideLoader() {
			document.getElementById('preLoader').style.display = "none";
		}

		hideL = window.setTimeout("hideLoader()", 1500);

		// add the 'unsaved changes' warning event handler
		if( window.addEventListener ) {
			window.addEventListener('beforeunload',checkDirt,false);
		} else if ( window.attachEvent ) {
			window.attachEvent('onbeforeunload',checkDirt);
		} else {
			window.onbeforeunload = checkDirt;
		}

	</script>
</head>
<body ondragstart="return false"<?php echo $modx_textdir ? ' class="rtl"':''?>>

<div id="preLoader"><table width="100%" border="0" cellpadding="0"><tr><td align="center"><div class="preLoaderText"><?php echo $_style['ajax_loader']; ?></div></td></tr></table></div>
