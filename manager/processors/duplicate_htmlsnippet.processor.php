<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('new_chunk')) {
	$e->setError(3);
	$e->dumpError();
}
?>
<?php

$id=$_GET['id'];

// duplicate htmlsnippet
$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_htmlsnippets` (name, description, snippet, category)
		SELECT CONCAT('Duplicate of ',name) AS 'name', description, snippet, category
		FROM $dbase . `" . $table_prefix . "site_htmlsnippets` WHERE id=$id;";
$rs = $modx->db->query($sql);

if ($rs) {
	$newid = $modx->db->getInsertId();	
} 
else {
	echo "A database error occured while trying to duplicate variable: <br /><br />" . $modx->db->getLastError();
	exit;
}

// finish duplicating - redirect to new chunk
$header="Location: index.php?r=2&a=78&id=$newid";
header($header);
?>
