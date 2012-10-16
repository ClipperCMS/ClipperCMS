<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('new_snippet')) {
	$e->setError(3);
	$e->dumpError();
}
?>
<?php

$id=$_GET['id'];

// duplicate Snippet
	$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_snippets` (name, description, snippet, properties, category)
			SELECT CONCAT('Duplicate of ',name) AS 'name', description, snippet, properties, category
			FROM $dbase.`" . $table_prefix . "site_snippets` WHERE id=$id;";
	$rs = $modx->db->query($sql);

if ($rs) {
	$newid = $modx->db->getInsertId();
} 
else {
	echo "A database error occured while trying to duplicate snippet: <br /><br />" . $modx->db->getLastError();
	exit;
}


// finish duplicating - redirect to new snippet
$header="Location: index.php?r=2&a=22&id=$newid";
header($header);
?>
