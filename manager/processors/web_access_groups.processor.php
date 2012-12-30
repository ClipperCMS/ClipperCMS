<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('web_access_permissions')) {
	$e->setError(3);
	$e->dumpError();
}

// web access group processor.

$tbl_document_groups     = $modx->getFullTableName('document_groups');
$tbl_documentgroup_names = $modx->getFullTableName('documentgroup_names');
$tbl_web_groups          = $modx->getFullTableName('web_groups');
$tbl_webgroup_access     = $modx->getFullTableName('webgroup_access');
$tbl_webgroup_names      = $modx->getFullTableName('webgroup_names');

$updategroupaccess = false;
$operation = $_REQUEST['operation'];

switch ($operation) {
	case "add_user_group" :
		$newgroup = $_REQUEST['newusergroup'];
		if(empty($newgroup)) {
			echo "no group name specified";
			exit;
		} else {
			$sql = "INSERT IGNORE INTO " . $tbl_webgroup_names . " (name) 
			VALUES(' " . $modx->db->escape($newgroup). "')"; // Temporary solution pending DBAPI::replace
			$modx->db->query($sql);
			
			if(!$modx->db->getAffectedRows()) {
				echo "Failed to insert new group. Possible duplicate group name?";
				exit;
			}

			$id = $modx->db->getInsertId();

			$modx->invokeEvent('OnWebCreateGroup', array(
				'groupid'   => $id,
				'groupname' => $newgroup,
			));
		}
	break;

	case "add_document_group" :
		$newgroup = $_REQUEST['newdocgroup'];

		if(empty($newgroup)) {
			echo "no group name specified";
			exit;
		} else {
			$sql = "INSERT INTO " . $tbl_documentgroup_names . " (name) 
			VALUES('" . $modx->db->escape($newgroup) . "')";

			$modx->db->query($sql);

			$id = $modx->db->getInsertId();

			$modx->invokeEvent('OnCreateDocGroup', array(
				'groupid'   => $id,
				'groupname' => $newgroup,
			));
		}
	break;

	case "delete_user_group" :
		$updategroupaccess = true;
		$usergroup = intval($_REQUEST['usergroup']);

		if(empty($usergroup)) {
			echo "No user group name specified for deletion";
			exit;
		} else {
			$sql = "DELETE FROM " . $tbl_webgroup_names . " WHERE id=$usergroup";
			$modx->db->query($sql);

			$sql = "DELETE FROM " . $tbl_webgroup_access . " WHERE webgroup=$usergroup";
			$modx->db->query($sql);

			$sql = "DELETE FROM " . $tbl_web_groups . " WHERE webuser=$usergroup";
			$modx->db->query($sql);
		}
	break;

	case "delete_document_group" :
		$group = intval($_REQUEST['documentgroup']);
		if(empty($group)) {
			echo "No document group name specified for deletion";
			exit;
		} else {
			$sql = "DELETE FROM " . $tbl_documentgroup_names . " WHERE id=$group";
			$modx->db->query($sql);

			$sql = "DELETE FROM " . $tbl_webgroup_access . " WHERE documentgroup=$group";
			$modx->db->query($sql);

			$sql = "DELETE FROM " . $tbl_document_groups . " WHERE document_group=$group";
			$modx->db->query($sql);
		}
	break;

	case "rename_user_group" :
		$newgroupname = $modx->db->escape($_REQUEST['newgroupname']);
		if(empty($newgroupname)) {
			echo "no group name specified";
			exit;
		}
		$groupid = intval($_REQUEST['groupid']);
		if(empty($groupid)) {
			echo "No group id specified";
			exit;
		}
		$sql = "UPDATE " . $tbl_webgroup_names . " SET name='" . $newgroupname . "' 
		WHERE id=$groupid LIMIT 1";
		$modx->db->query($sql);
	break;

	case "rename_document_group" :
		$newgroupname = $modx->db->escape($_REQUEST['newgroupname']);
		if(empty($newgroupname)) {
			echo "no group name specified";
			exit;
		}
		$groupid = intval($_REQUEST['groupid']);
		if(empty($groupid)) {
			echo "No group id specified";
			exit;
		}
		$sql = "UPDATE " . $tbl_documentgroup_names . " SET name='" . $newgroupname . "' 
		WHERE id=$groupid LIMIT 1";
		$modx->db->query($sql);
	break;

	case "add_document_group_to_user_group" :
		$updategroupaccess = true;
		$usergroup = intval($_REQUEST['usergroup']);
		$docgroup = intval($_REQUEST['docgroup']);
		
		$sql = "SELECT COUNT(*) FROM " . $tbl_webgroup_access . " 
		WHERE webgroup=$usergroup AND documentgroup=$docgroup";
		
		$limit = $modx->db->getValue($sql);
		
		if($limit<=0) {
			$sql = "INSERT INTO " . $tbl_webgroup_access . " 
			(webgroup, documentgroup) 
			VALUES($usergroup, $docgroup)";
			
			$modx->db->query($sql);
		} else {
			//alert user that coupling already exists?
		}
	break;

	case "remove_document_group_from_user_group" :
		$updategroupaccess = true;
		$coupling = intval($_REQUEST['coupling']);

		$sql = "DELETE FROM " . $tbl_webgroup_access . " WHERE id=$coupling";

		$modx->db->query($sql);
	break;

	default :
		echo "No operation set in request.";
		exit;
}

// secure web documents - flag as private
if($updategroupaccess==true){
	include $base_path."manager/includes/secure_web_documents.inc.php";
	secureWebDocument();

	// Update the private group column
	$sql = "UPDATE " . $tbl_documentgroup_names . " AS dgn 
	       LEFT JOIN " . $tbl_webgroup_access . " AS wga ON wga.documentgroup = dgn.id 
	       SET dgn.private_webgroup = (wga.webgroup IS NOT NULL)";

	$modx->db->query($sql);
}

$header = "Location: index.php?a=91";
header($header);
?>
