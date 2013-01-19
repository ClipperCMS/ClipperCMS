<?php

$host = $_POST['host'];
$uid = $_POST['uid'];
$pwd = $_POST['pwd'];
$installMode = $_POST['installMode'];

require_once("lang.php");

// include DBAPI and timer functions
require_once ('../manager/includes/extenders/dbapi.mysql.class.inc.php');
require_once ('includes/install.class.inc.php');

$install = new Install();
@$install->db = new DBAPI($install);

$output = $_lang["status_checking_database"];
if (! $install->db->testConnect($host, '', $uid, $pwd)) {
    $output .= '<span id="database_fail" style="color:#FF0000;">'.$_lang['status_failed'].'</span>';
}
else {
    if (version_compare(phpversion(), "5.3") < 0) {
        if(get_magic_quotes_gpc()) {
            $_POST['database_name'] = stripslashes($_POST['database_name']);
            $_POST['tableprefix'] = stripslashes($_POST['tableprefix']);
            $_POST['database_collation'] = stripslashes($_POST['database_collation']);
            $_POST['database_connection_method'] = stripslashes($_POST['database_connection_method']);
        }
    }
    $database_name = $install->db->escape($_POST['database_name']);
    $database_name = str_replace("`", "", $database_name);
    $tableprefix = $install->db->escape($_POST['tableprefix']);
    $database_collation = $install->db->escape($_POST['database_collation']);
    $database_connection_method = $install->db->escape($_POST['database_connection_method']);

	if ($install->db->testConnect($host, $database_name, $uid, $pwd)) {
	// Prefix test. Requires MySQL 5.0+
		$sql = "SELECT COUNT(*) FROM information_schema.tables
		WHERE `table_schema` = '$database_name' AND `table_name` = '" . $_POST['tableprefix'] . "site_content' ";
		$install->db->connect($host, $database_name, $uid, $pwd);
		$prefix_used = $install->db->getValue($sql);
	}

    if (! $install->db->testConnect($host, $database_name, $uid, $pwd)) {
        // create database
        $database_charset = substr($database_collation, 0, strpos($database_collation, '_'));
		
        $query = "CREATE DATABASE `$database_name` CHARACTER SET " . $database_charset." COLLATE " . $database_collation;

        if (! $install->db->testConnect($host, '', $uid, $pwd, $query)) {
            $output .= '<span id="database_fail" style="color:#FF0000;">'.$_lang['status_failed_could_not_create_database'].'</span>';
        }
        else {
            $output .= '<span id="database_pass" style="color:#80c000;">'.$_lang['status_passed_database_created'].'</span>';
        }
    }

    elseif ($installMode == 0 && $prefix_used > 0) {
			$output .= '<span id="database_fail" style="color:#FF0000;">'.$_lang['status_failed_table_prefix_already_in_use'].'</span>';
    }

    elseif (($database_connection_method != 'SET NAMES') && ($rs = $install->db->query("SHOW VARIABLES LIKE 'collation_database'")) && ($row = $install->db->getRow($rs, 'num')) && ($row[1] != $database_collation)) {
        $output .= '<span id="database_fail" style="color:#FF0000;">'.sprintf($_lang['status_failed_database_collation_does_not_match'], $row[1]).'</span>';
    }

    else {
        $output .= '<span id="database_pass" style="color:#80c000;">'.$_lang['status_passed'].'</span>';
    }
}

echo $output;
?>