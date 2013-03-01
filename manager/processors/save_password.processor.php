<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('save_password')) {
	$e->setError(3);
	$e->dumpError();
}

$id = $_POST['id'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];

if($pass1!=$pass2){
	echo "passwords don't match!";
	exit;
}

if(strlen($pass1)<6){
	echo "Password is too short. Please specify a password of at least 6 characters.";
	exit;
}

require ('hash.inc.php');
$HashHandler = new HashHandler(CLIPPER_HASH_PREFERRED, $modx);
$Hash = $HashHandler->generate($pass1);

$sql = "UPDATE " . $modx->getFullTableName('manager_users') 
. " SET hashtype=" . CLIPPER_HASH_PREFERRED . ", salt='" . $modx->db->escape($Hash->salt) . "', password='" . $modx->db->escape($Hash->hash) . "' WHERE id=" . ($userid = $modx->getLoginUserID());

$modx->db->query($sql);

$_SESSION['mgrHashtype'] = CLIPPER_HASH_PREFERRED;

$modx->invokeEvent('OnManagerChangePassword', array (
	'userid' => $userid,
	'username' => $modx->getLoginUserName(),
	'userpassword' => $pass1
	));

$header="Location: index.php?a=7";
header($header);

