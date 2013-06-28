<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('delete_template')) {	
	$e->setError(3);
	$e->dumpError();	
}
?>

<?php

echo '<div class="sectionBody">';

$id=intval($_GET['id']);

$doDelete = TRUE;

// delete the template, but first check it doesn't have any documents using it
$sql = "SELECT id, pagetitle FROM $dbase.`".$table_prefix."site_content` WHERE $dbase.`".$table_prefix."site_content`.template=".$id." and $dbase.`".$table_prefix."site_content`.deleted=0;";
$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);
if($limit>0) {
	echo "<b>This template is in use. Please set the documents using the template to another template. Documents using this template:</b><br />";
	for ($i=0;$i<$limit;$i++) {
		$row = $modx->db->getRow($rs);
		echo $row['id']." - ".$row['pagetitle']."<br />\n";
	}	
	$doDelete = FALSE;
}

if($id==$default_template) {
	echo "<b>This template is set as the default template. Please choose a different default template in the configuration before deleting this template.</b><br />";
	$doDelete = FALSE;
}



if($doDelete){
	
	// invoke OnBeforeTempFormDelete event
	$modx->invokeEvent("OnBeforeTempFormDelete",
							array(
								"id"	=> $id
							));
							
	//ok, delete the document.
	$sql = "DELETE FROM $dbase.`".$table_prefix."site_templates` WHERE $dbase.`".$table_prefix."site_templates`.id=".$id.";";
	$rs = $modx->db->query($sql);
	
	if(!$rs) {
		echo "Something went wrong while trying to delete the template...";
	} else {
		$sql = "DELETE FROM $dbase.`".$table_prefix."site_tmplvar_templates` WHERE $dbase.`".$table_prefix."site_tmplvar_templates`.templateid=".$id.";";
		$rs = $modx->db->query($sql);
		// invoke OnTempFormDelete event
		$modx->invokeEvent("OnTempFormDelete",array("id" => $id));
	
		// empty cache
		include_once "cache_sync.class.processor.php";
		$sync = new synccache();
		$sync->setCachepath("../assets/cache/");
		$sync->setReport(false);
		$sync->emptyCache(); // first empty the cache
		
		echo "<b>Template deleted!</b>";
	}

}else{
	
	//ntd - the template should not be deleted
	
}

echo '</div>';

?>
