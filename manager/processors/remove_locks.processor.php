<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('remove_locks')) {
	$e->setError(3);
	$e->dumpError();
}

// Remove locks
$sql = "TRUNCATE " . $modx->getFullTableName('active_users');

$modx->db->query($sql);

$header="Location: index.php?a=7";
	header($header);
?>
