<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('delete_document')) {
	$e->setError(3);
	$e->dumpError();
}

// reduce SQL statement complexity
$content_table = $modx->getFullTableName('site_content');
$groups_table = $modx->getFullTableName('document_groups');
$tv_content_table = $modx->getFullTableName('site_tmplvar_contentvalues');

$sql = "SELECT id FROM $content_table WHERE deleted=1";

$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);

$ids = array();

if($limit>0) {
	for($i=0; $i<$limit; $i++) {
		$row=$modx->db->getRow($rs);
		array_push($ids, @$row['id']);
	}
}

// invoke OnBeforeEmptyTrash event
$modx->invokeEvent("OnBeforeEmptyTrash",
						array(
							"ids"=>$ids
						));

// remove the document groups link
$sql = "DELETE $groups_table
		FROM $groups_table INNER JOIN $content_table 
		ON $content_table.id = $groups_table.document
		WHERE $content_table.deleted=1";
		
$modx->db->query($sql);

// remove the TV content values.
$sql = "DELETE $tv_content_table
		FROM $tv_content_table INNER JOIN $content_table 
		ON $content_table.id = $tv_content_table.contentid
		WHERE $content_table.deleted=1";

$modx->db->query($sql);

//delete the document.
$sql = "DELETE FROM $content_table WHERE deleted=1;";

$modx->db->query($sql);

// invoke OnEmptyTrash event
$modx->invokeEvent("OnEmptyTrash",
					array(
						"ids"=>$ids
					));

// empty cache
include_once "cache_sync.class.processor.php";
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache
// finished emptying cache - redirect
$header="Location: index.php?r=1&a=7";
header($header);
?>
