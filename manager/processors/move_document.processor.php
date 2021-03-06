<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('edit_document')) {
	$e->setError(3);
	$e->dumpError();
}

// ok, two things to check.
// first, document cannot be moved to itself
// second, new parent must be a folder. If not, set it to folder.
if($_REQUEST['id']==$_REQUEST['new_parent']) {
		$e->setError(600);
		$e->dumpError();
}
if($_REQUEST['id']=="") {
		$e->setError(601);
		$e->dumpError();
}
if($_REQUEST['new_parent']=="") {
		$e->setError(602);
		$e->dumpError();
}

$sql = "SELECT parent FROM " . $modx->getFullTableName('site_content') 
. " WHERE id=" . $_REQUEST['id'] . ";";

$rs = $modx->db->query($sql);

$row = $modx->db->getRow($rs);

$oldparent = $row['parent'];
$newParentID = $_REQUEST['new_parent'];

// check user has permission to move document to chosen location

if ($use_udperms == 1) {

	if ($oldparent != $newParentID) {
		require_once('user_documents_permissions.class.php');
		$udperms = new udperms();
		$udperms->user = $modx->getLoginUserID();
		$udperms->document = $newParentID;
		$udperms->role = $_SESSION['mgrRole'];

		 if (!$udperms->checkPermissions()) {
		 include ("header-jquery.inc.php");
		 ?><script>parent.tree.ca = '';</script>
		 <br /><br /><div class="sectionHeader"><?php echo $_lang['access_permissions']; ?></div><div class="sectionBody">
        <p><?php echo $_lang['access_permission_parent_denied']; ?></p>
        <?php
        include ("footer.inc.php");
        exit;
		 }
	}
}

function allChildren($currDocID) {
	global $modx;
	$children= array();
	$sql = "SELECT id FROM " . $modx->getFullTableName('site_content') 
	. " WHERE parent = $currDocID;";

	$rs = $modx->db->query($sql);
	
	if ($numChildren = $modx->db->getRecordCount($rs)) {
			while ($child= $modx->db->getRow($rs)) {
				$children[]= $child['id'];
				$nextgen= array();
				$nextgen= allChildren($child['id']);
				$children= array_merge($children, $nextgen);
			}
		}
	return $children;
}

$children= allChildren($_REQUEST['id']);

if (!array_search($newParentID, $children)) {

	$sql = "UPDATE " . $modx->getFullTableName('site_content') 
	. "SET isfolder=1 WHERE id=" . $_REQUEST['new_parent'] . ";";

	$rs = $modx->db->query($sql);
	
	$sql = "UPDATE " . $modx->getFullTableName('site_content') 
	. "SET parent=" . $_REQUEST['new_parent'] 
	. ", editedby=" . $modx->getLoginUserID() 
	.", editedon=" . time() 
	. " WHERE id=" . $_REQUEST['id'] . ";";

	$rs = $modx->db->query($sql);
	
	// finished moving the document, now check to see if the old_parent should no longer be a folder.
	$sql = "SELECT count(*) FROM " . $modx->getFullTableName('site_content') 
	. " WHERE parent=$oldparent;";

	$rs = $modx->db->query($sql);

	$row = $modx->db->getRow($rs);
	$limit = $row['count(*)'];

	if(!$limit>0) {
		$sql = "UPDATE " . $modx->getFullTableName('site_content') 
		. "SET isfolder=0 WHERE id=$oldparent;";

		$rs = $modx->db->query($sql);
	}

	// empty cache & sync site
	include_once "cache_sync.class.processor.php";
	$sync = new synccache();
	$sync->setCachepath(MODX_BASE_PATH . "assets/cache/");
	$sync->setReport(false);
	$sync->emptyCache(); // first empty the cache

	$header="Location: index.php?r=1&id=$id&a=7";
	header($header);
} else {
	echo "You cannot move a document to a child document!";
}
?>
