<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('exec_module')) {	
	$e->setError(3);
	$e->dumpError();	
}

if(isset($_REQUEST['id'])) {
	$id = intval($_REQUEST['id']);
} else {
	$id=0;
}

// make sure the id's a number
if(!is_numeric($id)) {
	echo "Passed ID is NaN!";
	exit;
}

// check if user has access permission, except admins
if($_SESSION['mgrRole']!=1){
	$sql = "SELECT sma.usergroup,mg.member " .
		"FROM ".$modx->getFullTableName("site_module_access")." sma " .
		"LEFT JOIN ".$modx->getFullTableName("member_groups")." mg ON mg.user_group = sma.usergroup AND member='".$modx->getLoginUserID()."'".
		"WHERE sma.module = '$id'";
	$rs = $modx->dbQuery($sql);

	//initialize permission to -1, if it stays -1 no permissions
	//attached so permission granted
	$permissionAccessInt = -1;

	while ($row = $modx->fetchRow($rs)) {
		if($row["usergroup"] && $row["member"]) {
			//if there are permissions and this member has permission, ofcourse
			//this is granted
			$permissionAccessInt = 1;
		} elseif ($permissionAccessInt==-1) {
			//if there are permissions but this member has no permission and the
			//variable was still in init state we set permission to 0; no permissions
			$permissionAccessInt = 0;
		}
	}

	if($permissionAccessInt==0) {
		echo "<script type='text/javascript'>" .
		"function jsalert(){ alert('You do not sufficient privileges to execute this module.');" .
		"window.location.href='index.php?a=106';}".
		"setTimeout('jsalert()',100)".
		"</script>";
		exit;
	}
}

// get module data
$sql = "SELECT * " .
		"FROM ".$modx->getFullTableName("site_modules")." " .
		"WHERE id = $id;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
	echo "<script type='text/javascript'>" .
			"function jsalert(){ alert('Multiple modules sharing same unique id $id. Please contact the Site Administrator');" .
			"window.location.href='index.php?a=106';}".
			"setTimeout('jsalert()',100)".
			"</script>";
	exit;
}
if($limit<1) {
	echo "<script type='text/javascript'>" .
			"function jsalert(){ alert('No record found for id $id');" .
			"window.location.href='index.php?a=106';}" .
			"setTimeout('jsalert()',100)".
			"</script>";
	exit;
}
$content = mysql_fetch_assoc($rs);
if($content['disabled']) {
	echo "<script type='text/javascript'>" .
			"function jsalert(){ alert('This module is disabled and cannot be executed.');" .
			"window.location.href='index.php?a=106';}" .
			"setTimeout('jsalert()',100)".
			"</script>";
	exit;
}

// load module configuration
$parameter = array();
if(!empty($content["properties"])){
	$tmpParams = explode("&",$content["properties"]);
	for($x=0; $x<count($tmpParams); $x++) {
		$pTmp = explode("=", $tmpParams[$x]);
		$pvTmp = explode(";", trim($pTmp[1]));
		if ($pvTmp[1]=='list' && $pvTmp[3]!="") $parameter[$pTmp[0]] = $pvTmp[3]; //list default
		else if($pvTmp[1]!='list' && $pvTmp[2]!="") $parameter[$pTmp[0]] = $pvTmp[2];
	}
}

// Set the item name for logger
$_SESSION['itemname'] = $content['name'];

// Run module
// (this was the evalModule() function in MODx <1.1)

$modx->event->params = &$parameter; // store params inside event object
if(is_array($parameter)) {
	extract($parameter, EXTR_SKIP);
}

$modx->set_error_handler();

ob_start();
	$mod = eval($content['modulecode']);
	$msg = ob_get_contents();
ob_end_clean();

unset($modx->event->params); 
echo $mod.$msg;

