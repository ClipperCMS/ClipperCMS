<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('save_template')) {
    $e->setError(3);
    $e->dumpError();
}

if (!is_numeric($_REQUEST['id'])) {
    echo 'Template ID is NaN';
    exit;
}

$tbl_site_templates         = $modx->getFullTableName('site_templates');
$tbl_site_tmplvar_templates = $modx->getFullTableName('site_tmplvar_templates');
$tbl_site_tmplvars          = $modx->getFullTableName('site_tmplvars');

$basePath = $modx->config['base_path'];
$siteURL = $modx->config['site_url'];

$updateMsg = '';

if(isset($_POST['listSubmitted'])) {
    $updateMsg .= '<span class="warning" id="updated">Updated!<br /><br /></span>';
    foreach ($_POST as $listName=>$listValue) {
        if ($listName == 'listSubmitted') continue;
        $orderArray = explode(';', rtrim($listValue, ';'));
        foreach($orderArray as $key => $item) {
            if (strlen($item) == 0) continue; 
            $tmplvar = ltrim($item, 'item_');
            $sql = 'UPDATE '.$tbl_site_tmplvar_templates.' SET rank='.$key.' WHERE tmplvarid='.$tmplvar.' AND templateid='.$_REQUEST['id'];
            $modx->db->query($sql);
        }
    }
    // empty cache
    include_once ($basePath.'manager/processors/cache_sync.class.processor.php');
    $sync = new synccache();
    $sync->setCachepath($basePath.'/assets/cache/');
    $sync->setReport(false);
    $sync->emptyCache(); // first empty the cache
}

$sql = 'SELECT tv.name AS `name`, tv.id AS `id`, tr.templateid, tr.rank, tm.templatename '.
       'FROM '.$tbl_site_tmplvar_templates.' AS tr '.
       'INNER JOIN '.$tbl_site_tmplvars.' AS tv ON tv.id = tr.tmplvarid '.
       'INNER JOIN '.$tbl_site_templates.' AS tm ON tr.templateid = tm.id '.
       'WHERE tr.templateid='.(int)$_REQUEST['id'].' ORDER BY tr.rank, tv.rank, tv.id';

$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);

if($limit>1) {
    for ($i=0;$i<$limit;$i++) {
        $row = $modx->db->getRow($rs);
        if ($i == 0) $evtLists .= '<strong>'.$row['templatename'].'</strong><br /><ul id="tv-sort-order" class="sortableList">';
        $evtLists .= '<li id="item_'.$row['id'].'" class="sort ui-state-default">'.$row['name'].'</li>';
    }
}

$evtLists .= '</ul>';

require('header.inc.php');

$header .= '

<h1>'.$_lang["template_tv_edit_title"].'</h1>

<div id="actions">
    <ul class="actionButtons">
        <li><a href="#" onclick="save();"><img src="'.$_style["icons_save"].'" /> '.$_lang['save'].'</a></li>
        <li><a href="#" onclick="document.location.href=\'index.php?a=16&amp;id='.$_REQUEST['id'].'\';"><img src="'.$_style["icons_cancel"].'"> '.$_lang['cancel'].'</a></li>
    </ul>
</div>

<div class="sectionHeader">'.$_lang['template_tv_edit'].'</div>
<div class="sectionBody">
<p>'.$_lang["template_tv_edit_message"].'</p>';

echo $header;

echo $updateMsg . "<span class=\"warning\" style=\"display:none;\" id=\"updating\">Updating...<br /><br /> </span>";

echo $evtLists;

echo '
</div>
<form action="" method="post" name="sortableListForm" style="display: none;">
            <input type="hidden" name="listSubmitted" value="true" />
            <input type="text" id="list" name="list" value="" />
</form>
	<script>
        //TODO: think about a more generic form of submitting
        function save() {
        	setTimeout("document.sortableListForm.submit()",1000);
    	}
    </script>
	
	

';


?>
