<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('edit_template')) {
	$e->setError(3);
	$e->dumpError();
}
?>
<?php

$id=$_GET['id'];

// duplicate TV
$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_tmplvars` (type, name, caption, description, default_text, elements, rank, display, display_params, category)
		SELECT type, CONCAT('Duplicate of ',name) AS 'name', caption, description, default_text, elements, rank, display, display_params, category
		FROM $dbase.`" . $table_prefix . "site_tmplvars` WHERE id=$id;";
/* Adds "Duplicate" to name rather than caption 
Old version: SELECT type, name, CONCAT('Duplicate of ',caption) AS 'caption', description, default_text, elements, rank, display, display_params, category
*/

$rs = $modx->db->query($sql);

if ($rs) {
	$newid = $modx->db->getInsertId();
}
else {
	echo "A database error occured while trying to duplicate TV: <br /><br />" . $modx->db->getLastError();
	exit;
}


// duplicate TV Template Access Permissions
$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_tmplvar_templates` (tmplvarid, templateid)
		SELECT $newid, templateid
		FROM $dbase.`" . $table_prefix . "site_tmplvar_templates` WHERE tmplvarid=$id;";
$rs = $modx->db->query($sql);

if (!$rs) {
	echo "A database error occured while trying to duplicate TV template access: <br /><br />" . $modx->db->getLastError();
	exit;
}


// duplicate TV Access Permissions
	$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_tmplvar_access` (tmplvarid, documentgroup)
			SELECT $newid, documentgroup
			FROM $dbase.`" . $table_prefix . "site_tmplvar_access` WHERE tmplvarid=$id;";
	$rs = $modx->db->query($sql);

if (!$rs) {
	echo "A database error occured while trying to duplicate TV Access Permissions: <br /><br />".$modx->db->getLastError();
	exit;
}

// finish duplicating - redirect to new variable
$header="Location: index.php?r=2&a=301&id=$newid";
header($header);
?>
