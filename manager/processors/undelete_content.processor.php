<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('delete_document')) {	
	$e->setError(3);
	$e->dumpError();	
}

$id=$_REQUEST['id'];

// check permissions on the document
include_once "./processors/user_documents_permissions.class.php";
$udperms = new udperms();
$udperms->user = $modx->getLoginUserID();
$udperms->document = $id;
$udperms->role = $_SESSION['mgrRole'];

if(!$udperms->checkPermissions()) {
	include "header.inc.php";
	?><div class="sectionHeader"><?php echo $_lang['access_permissions']; ?></div>
	<div class="sectionBody">
	<p><?php echo $_lang['access_permission_denied']; ?></p>
	<?php
	include("footer.inc.php");
	exit;	
}

// get the timestamp on which the document was deleted.
$sql = "SELECT deletedon FROM " . $modx->getFullTableName('site_content') . " 
WHERE id=$id AND deleted=1";

$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);

if($limit!=1) {
	echo "Couldn't find document to determine its date of deletion!";
	exit;
} else {
	$row = $modx->db->getRow($rs);
	$deltime = $row['deletedon'];
}

$children = array();
getChildren($id);

if(count($children)>0) {
	$docs_to_undelete = implode(" ,", $children);

	$sql = "UPDATE " . $modx->getFullTableName('site_content') . " 
	SET deleted=0, deletedby=0, deletedon=0 WHERE id IN($docs_to_undelete)";

	$modx->db->query($sql);
}

//'undelete' the document.
$sql = "UPDATE " . $modx->getFullTableName('site_content') . " 
SET deleted=0, deletedby=0, deletedon=0 WHERE id=$id";

$modx->db->query($sql);

// empty cache
include_once "cache_sync.class.processor.php";

$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); 

$header="Location: index.php?r=1&a=7";
header($header);


function getChildren($parent) {
	global $modx;
	global $children;
	global $deltime;
	
	$db->debug = true;
	
	$sql = "SELECT id FROM " . $modx->getFullTableName('site_content') . " 
	WHERE parent=$parent AND deleted=1 AND deletedon=$deltime";

	$rs = $modx->db->query($sql);
	$limit = $modx->db->getRecordCount($rs);

	if($limit>0) {
		// the document has children documents, we'll need to delete those too
		for($i=0;$i<$limit;$i++) {
		$row=$modx->db->getRow($rs);
		
			$children[] = $row['id'];
			getChildren($row['id']);
		}
	}
}

?>
