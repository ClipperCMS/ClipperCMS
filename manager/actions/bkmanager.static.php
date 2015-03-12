<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('bk_manager')) {
	$e->setError(3);
	$e->dumpError();
}

// Get table names (alphabetical)
$tbl_event_log    = $modx->getFullTableName('event_log');

// Backup Manager by Raymond:

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';

if ($mode=='backup') {
	$tables = isset($_POST['chk']) ? $_POST['chk'] : '';
	if (!is_array($tables)) {
		echo '<html><body>'.
		     '<script>alert(\'Please select a valid table from the list below\');</script>'.
		     '</body></html>';
		exit;
	}

	/*
	 * Code taken from Ralph A. Dahlgren MySQLdumper Snippet - Etomite 0.6 - 2004-09-27
	 * Modified by Raymond 3-Jan-2005
	 * Modified by TimGS for Clipper Dec 2012
	 */
	require_once('clipper_sql_dumper.inc.php');
	@set_time_limit(120); // set timeout limit to 2 minutes
	$dumper = new ClipperSqlDumper($modx);
	$dumper->setDBtables($tables);
	$dumper->setDroptables((isset($_POST['droptables']) ? true : false));
	$dump_output = $dumper->createDump();
	if($dump_output) {
		$today = date("d_M_y");
		$today = strtolower($today);
		if(!headers_sent()) {
			header('Expires: 0');
		    header('Cache-Control: private');
		    header('Pragma: cache');
			header('Content-type: application/download');
			header('Content-Disposition: attachment; filename='.$modx->db->getDBname().'_'.$today.'_database_backup.sql');
		}
		echo $dump_output;
		exit;
	} else {
		$e->setError(1, 'Unable to Backup Database');
		$e->dumpError();
		exit;
	}

} else {
	require('header.inc.php');  // start normal header
}

?>
<script>
	function selectAll() {
		var f = document.forms['frmdb'];
		var c = f.elements['chk[]'];
		for(i=0;i<c.length;i++){
			c[i].checked=f.chkselall.checked;
		}
	}
	function submitForm(){
		var f = document.forms['frmdb'];
		f.mode.value='backup';
		f.target='fileDownloader';
		f.submit();
		return false;
	}

</script>
<h1><?php echo $_lang['bk_manager']?></h1>

<?php
require_once('db_info.class.inc.php');
$db_info = new DbInfo($dbase);
$db_info->output(93, true);
?>

<!-- This iframe is used when downloading file backup file -->
<iframe name="fileDownloader" width="1" height="1" style="display:none; width:1px; height:1px;"></iframe>

<?php include_once "footer.inc.php"; // send footer ?>
