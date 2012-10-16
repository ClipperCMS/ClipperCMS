<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('new_document') || !$modx->hasPermission('save_document')) {
	$e->setError(3);
	$e->dumpError();
}

// check the document doesn't have any children
$id=$_GET['id'];
$children = array();

// check permissions on the document
include_once "./processors/user_documents_permissions.class.php";
$udperms = new udperms();
$udperms->user = $modx->getLoginUserID();
$udperms->document = $id;
$udperms->role = $_SESSION['mgrRole'];
$udperms->duplicateDoc = true;

if(!$udperms->checkPermissions()) {
	include "header.inc.php";
	?><div class="sectionHeader"><?php echo $_lang['access_permissions']; ?></div>
	<div class="sectionBody">
	<p><?php echo $_lang['access_permission_denied']; ?></p>
	<?php
	include("footer.inc.php");
	exit;
}

// Run the duplicator
$id = duplicateDocument($id);

// finish cloning - redirect
$header="Location: index.php?r=1&a=3&id=$id";
header($header);

function duplicateDocument($docid, $parent=null, $_toplevel=0) {
	global $modx;

	$evtOut = $modx->invokeEvent('OnBeforeDocDuplicate', array(
		'id' => $docid
	));

	$myChildren = array();
	$userID = $modx->getLoginUserID();

	$tblsc = $modx->getFullTableName('site_content');

	// Grab the original document
	$rs = $modx->db->select('*', $tblsc, 'id='.$docid);
	$content = $modx->db->getRow($rs);

	unset($content['id']); // remove the current id.

	// Once we've grabbed the document object, start doing some modifications
	if ($_toplevel == 0) {
		$content['pagetitle'] = 'Duplicate of ' . $content['pagetitle'];
		$content['alias'] = null;
	} elseif($modx->config['friendly_urls'] == 0 || $modx->config['allow_duplicate_alias'] == 0) {
		$content['alias'] = null;
	}

	// change the parent accordingly
	if ($parent !== null) {
		$content['parent'] = $parent;
	}

	$content['createdby'] = $userID;
	$content['createdon'] = time();
	$content['editedby'] = 0;
	$content['editedon'] = 0;
	$content['deleted'] = 0;
	$content['deletedby'] = 0;
	$content['deletedon'] = 0;

    // Set the published status to unpublished by default
    $content['published'] = $content['pub_date'] = 0;

	// Escape the proper strings
	$content['pagetitle'] = $modx->db->escape($content['pagetitle']);
	$content['longtitle'] = $modx->db->escape($content['longtitle']);
	$content['description'] = $modx->db->escape($content['description']);
	$content['introtext'] = $modx->db->escape($content['introtext']);
	$content['content'] = $modx->db->escape($content['content']);
	$content['menutitle'] = $modx->db->escape($content['menutitle']);

	// Duplicate the Document
	$newparent = $modx->db->insert($content, $tblsc);

	// duplicate document's TVs & Keywords
	duplicateKeywords($docid, $newparent);
	duplicateTVs($docid, $newparent);
	duplicateAccess($docid, $newparent);
	
	$evtOut = $modx->invokeEvent('OnDocDuplicate', array(
		'id' => $docid,
		'new_id' => $newparent
	));

	// Start duplicating all the child documents that aren't deleted.
	$_toplevel++;
	$rs = $modx->db->select('id', $tblsc, 'parent='.$docid.' AND deleted=0', 'id ASC');
	if ($modx->db->getRecordCount($rs)) {
		while ($row = $modx->db->getRow($rs))
			duplicateDocument($row['id'], $newparent, $_toplevel);
	}

	// return the new doc id
	return $newparent;
}

// Duplicate Keywords
function duplicateKeywords($oldid,$newid) {
	global $modx;

	$tblkw = $modx->getFullTableName('keyword_xref');

	$modx->db->insert(
		array('content_id'=>'', 'keyword_id'=>''), $tblkw, 
		$newid . ', keyword_id', $tblkw, 'content_id=' . $oldid
	);
}

// Duplicate Document TVs
function duplicateTVs($oldid,$newid) {
	global $modx;

	$tbltvc = $modx->getFullTableName('site_tmplvar_contentvalues');

	$modx->db->insert(
		array('contentid'=>'', 'tmplvarid'=>'', 'value'=>''), $tbltvc, 
		$newid . ', tmplvarid, value', $tbltvc, 'contentid=' . $oldid 
	);
}

// Duplicate Document Access Permissions
function duplicateAccess($oldid,$newid) {
	global $modx;

	$tbldg = $modx->getFullTableName('document_groups');

		$modx->db->insert(
			array('document'=>'', 'document_group'=>''), $tbldg, 
			$newid . ', document_group', $tbldg, 'document=' . $oldid 
		);
}

?>
