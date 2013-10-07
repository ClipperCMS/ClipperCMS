<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if (isset($_GET['ma'])) return; // EARLY RETURN !! For ajax.

$mxla = $modx_lang_attribute ? $modx_lang_attribute : 'en';

// invoke OnManagerRegClientStartupHTMLBlock event
$evtOut = $modx->invokeEvent('OnManagerMainFrameHeaderHTMLBlock');
$onManagerMainFrameHeaderHTMLBlock = is_array($evtOut) ? '<div id="onManagerMainFrameHeaderHTMLBlock">' . implode('', $evtOut) . '</div>' : '';
?>
<!doctype html>
<html lang="<?php echo $mxla?>"<?php ($modx_textdir ? ' dir="rtl"' : '') ?>>

<head>
	<meta charset="<?php echo $modx_manager_charset; ?>">
	<title><?php echo CMS_NAME; ?></title>
    <link rel="stylesheet" href="media/style/common/clipper-jquery-ui.css" />
    <link rel="stylesheet" href="media/style/common/style.css" />
    <?php
    if (is_file(MODX_MANAGER_PATH."media/style/$manager_theme/style.css")) {
    	echo '<link rel="stylesheet" href="media/style/'.$manager_theme."/style.css\" />\n";
    }
    if (is_file(MODX_MANAGER_PATH."media/style/$manager_theme/manager.css")) {
    	echo '<link rel="stylesheet" href="media/style/'.$manager_theme."/manager.css\" />\n";
    }

	echo $modx->getJqueryTag();
	echo $modx->getJqueryPluginTag('jquery-ui-custom-clippermanager', 'jquery-ui-custom-clippermanager.min.js');
	echo $modx->getJqueryPluginTag('jquery-ui-timepicker', 'jquery-ui-timepicker-addon.js');
	echo $modx->getJqueryPluginTag('jquery-datatables', 'jquery.dataTables.min.js');
	
	//get appropiate language file for datepicker and timepicker
	if ($mxla != 'en' && $modx_manager_charset == 'UTF-8') {

		//look for appropiate datepicker language file
		$aDatepickerLang = glob('../assets/js/i18n/jquery.ui.datepicker-'.$mxla.'*.js');
		if(!empty($aDatepickerLang)){
			$path = explode('assets/js/', end($aDatepickerLang));
			echo $modx->getJqueryPluginTag('jquery.ui.datepicker.lang', $path[1]);
		}

		//look for appropiate timepicker language file
		$aTimepickerLang = glob('../assets/js/i18n/jquery-ui-timepicker-'.$mxla.'*.js');
		if(!empty($aTimepickerLang)){
			$path = explode('assets/js/', end($aTimepickerLang));
			echo $modx->getJqueryPluginTag('jquery-ui-timepicker.lang', $path[1]);
		}

    }

	?>
	<script src="media/script/manager.js"></script>

	<?php
	// Include after JQuery, so this block can output code using jQuery.
	echo $onManagerMainFrameHeaderHTMLBlock;
	?>

	<script>
		var config = {
			cms_name: '<?php echo CMS_NAME; ?>',
			cms_version: '<?php echo CMS_RELEASE_VERSION; ?>',
			lang_attribute: '<?php echo $mxla; ?>',
			date_format: '<?php echo $modx->config['date_format']; ?>',
			time_format: '<?php echo $modx->config['time_format']; ?>',
			datepicker_year_range: '<?php echo $modx->config['datepicker_year_range']; ?>',
			remember_last_tab: '<?php echo $modx->config['remember_last_tab']; ?>'
		}
	</script>

	<script>
	
		//TODO: organize these js function better, maybe in a separate file or manager.js
	
		$(window).on('load', function () {
		  document_onload();
		});
		
		$(window).on('beforeunload', function () {
		  document_onunload();
		});

	</script>
	
	<script>
	
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
			$('#savingMessage').html("<?php echo $_lang['saving']; ?>");
			for(i = 0; i < document.forms[fName].elements.length; i++) {
				document.forms[fName].elements[i].disabled='disabled';
			}
		}

		var managerPath = "";

		function hideLoader() {
			$('#preLoader').css({'display':'none'});
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

<div id="preLoader"><div class="preLoaderText"><?php echo $_style['ajax_loader']; ?></div></div>
