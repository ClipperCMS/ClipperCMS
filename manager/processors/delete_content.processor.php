<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('delete_document')) {
	$e->setError(3);
	$e->dumpError();
}

// check the document doesn't have any children
$id=intval($_GET['id']);
$deltime = time();
$children = array();

// check permissions on the document
require_once('user_documents_permissions.class.php');
$udperms = new udperms();
$udperms->user = $modx->getLoginUserID();
$udperms->document = $id;
$udperms->role = $_SESSION['mgrRole'];

if(!$udperms->checkPermissions()) {
	require ('header.inc.php');
	?><div class="sectionHeader"><?php echo $_lang['access_permissions']; ?></div>
	<div class="sectionBody">
	<p><?php echo $_lang['access_permission_denied']; ?></p>
	<?php
	include("footer.inc.php");
	exit;
}

$clpr_site_content = $modx->getFullTableName('site_content');

function getChildren($parent, $clpr_site_content) {

    // Using globals looks messy but makes this recursive function much
    // more efficient and much less likely to cause out-of-memory errors
    // when recursing deeply through many documents.
	global $modx;
    global $rs;
	global $children;

	$rs = $modx->db->query("SELECT id FROM $clpr_site_content WHERE parent=$parent AND deleted=0;");
	if($modx->db->getRecordCount($rs)) {
		// the document has children documents, we'll need to delete those too
        $ids = $modx->db->getColumn('id', $rs);
        $modx->db->freeResult($rs);
		foreach($ids as $id) {
			if($id==$modx->config['site_start']) {
				echo "The document you are trying to delete is a folder containing document ".$id.". This document is registered as the 'Site start' document, and cannot be deleted. Please assign another document as your 'Site start' document and try again.";
				exit;
			}
			if($id==$modx->config['site_unavailable_page']) {
				echo "The document you are trying to delete is a folder containing document ".$id.". This document is registered as the 'Site unavailable page' document, and cannot be deleted. Please assign another document as your 'Site unavailable page' document and try again.";
				exit;
			}
			$children[] = $id;
			getChildren($id, $clpr_site_content);
		}
	}
}

getChildren($id, $clpr_site_content);

// invoke OnBeforeDocFormDelete event
$modx->invokeEvent("OnBeforeDocFormDelete",
						array(
							"id"=>$id,
							"children"=>$children
						));

if(count($children)>0) {
	$docs_to_delete = implode(" ,", $children);
	$sql = "UPDATE $clpr_site_content SET deleted=1, deletedby=".$modx->getLoginUserID().", deletedon=$deltime WHERE id IN($docs_to_delete);";
	$rs = @$modx->db->query($sql);
	if(!$rs) {
		echo "Something went wrong while trying to set the document's children to deleted status...";
		exit;
	}
}

if($modx->config['site_start']==$id){
	echo "Document is 'Site start' and cannot be deleted!";
	exit;
}

if($modx->config['site_unavailable_page']==$id){
	echo "Document is used as the 'Site unavailable page' and cannot be deleted!";
	exit;
}

//ok, 'delete' the document.
$sql = "UPDATE $clpr_site_content SET deleted=1, deletedby=".$modx->getLoginUserID().", deletedon=$deltime WHERE id=$id;";
$rs = $modx->db->query($sql);
if(!$rs) {
	echo "Something went wrong while trying to set the document to deleted status...";
	exit;
} else {
	// invoke OnDocFormDelete event
	$modx->invokeEvent("OnDocFormDelete",
							array(
								"id"=>$id,
								"children"=>$children
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
}
?>
