<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('save_plugin')) {
	$e->setError(3);
	$e->dumpError();
}

if($manager_theme) {
    $useTheme = $manager_theme . '/';
} else {
    $useTheme = '';
}

$basePath = $modx->config['base_path'];
$siteURL = $modx->config['site_url'];

$updateMsg = '';

if(isset($_POST['listSubmitted'])) {
    $updateMsg .= "<span class=\"warning\" id=\"updated\">Updated!<br /><br /> </span>";
	$tbl = $dbase.'.`'.$table_prefix.'site_plugin_events`';

	foreach ($_POST as $listName=>$listValue) {
        if ($listName == 'listSubmitted') continue;
    	$orderArray = explode(',', $listValue);
    	$listName = ltrim($listName, 'list_');
    	if (count($orderArray) > 0) {
	    	foreach($orderArray as $key => $item) {
	    		if ($item == '') continue;
	    		$pluginId = ltrim($item, 'item_');
	    		$sql = "UPDATE $tbl set priority=".$key." WHERE pluginid=".$pluginId." and evtid=".$listName;
	    		$modx->db->query($sql);
	    	}
    	}
    }
    // empty cache
	include_once ($basePath.'manager/processors/cache_sync.class.processor.php');
	$sync = new synccache();
	$sync->setCachepath($basePath.'/assets/cache/');
	$sync->setReport(false);
	$sync->emptyCache(); // first empty the cache
}

$sql = "
	SELECT sysevt.name as 'evtname', sysevt.id as 'evtid', pe.pluginid, plugs.name, pe.priority
	FROM $dbase.`".$table_prefix."system_eventnames` sysevt
	INNER JOIN $dbase.`".$table_prefix."site_plugin_events` pe ON pe.evtid = sysevt.id
	INNER JOIN $dbase.`".$table_prefix."site_plugins` plugs ON plugs.id = pe.pluginid
	WHERE plugs.disabled=0
	ORDER BY sysevt.name,pe.priority
";

$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);

$insideUl = 0;
$preEvt = '';
$evtLists = '';
$sortables = array();
if($limit>1) {
    for ($i=0;$i<$limit;$i++) {
        $plugins = $modx->db->getRow($rs);
        if ($preEvt !== $plugins['evtid']) {
            $sortables[] = $plugins['evtid'];
            $evtLists .= $insideUl? '</ul><br />': '';
            $evtLists .= '<strong>'.$plugins['evtname'].'</strong><br /><ul id="'.$plugins['evtid'].'" class="sortableList plugin-execution-order">';
            $insideUl = 1;
        }
        $evtLists .= '<li id="item_'.$plugins['pluginid'].'" class="ui-state-default">'.$plugins['name'].'</li>';
        $preEvt = $plugins['evtid'];
    }
}

$evtLists .= '</ul>';

require('header.inc.php');

$header = '

<h1>'.$_lang['plugin_priority_title'].'</h1>

<div id="actions">
   <ul class="actionButtons">
       	<li><a href="#" onclick="save();"><img src="'.$_style["icons_save"].'" /> '.$_lang['save'].'</a></li>
		<li><a href="#" onclick="document.location.href=\'index.php?a=76\';"><img src="'.$_style["icons_cancel"].'" /> '.$_lang['cancel'].'</a></li>
	</ul>
</div>

<div class="sectionHeader">'.$_lang['plugin_priority'].'</div>
<div class="sectionBody">
<p>'.$_lang['plugin_priority_instructions'].'</p>
';

echo $header;

echo $updateMsg . "<span class=\"warning\" style=\"display:none;\" id=\"updating\">Updating...<br /><br /> </span>";

echo $evtLists;

echo '<form action="" method="post" name="sortableListForm" style="display: none;">
            <input type="hidden" name="listSubmitted" value="true" />';
            
foreach ($sortables as $list) {
	echo '<input type="text" id="list_'.$list.'" name="list_'.$list.'" value="" />';
}
            
echo '	</form>
	</div>
	<script>
        //TODO: think about a more generic form of submitting
        function save() {
        	setTimeout("document.sortableListForm.submit()",1000);
    	}
    </script>
	
	
';
?>
