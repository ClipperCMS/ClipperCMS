<?php

$host = $_POST['host'];
$uid = $_POST['uid'];
$pwd = $_POST['pwd'];
$database_collation = htmlentities($_POST['database_collation']);

// include DBAPI and timer functions
require_once ('../manager/includes/extenders/dbapi.mysql.class.inc.php');
require_once ('includes/install.class.inc.php');

$install = new Install();
@$install->db = new DBAPI($install);

$output = '<select id="database_collation" name="database_collation">
<option value="'.$database_collation.'" selected >'.$database_collation.'</option></select>';

if ($install->db->testConnect($host, '', $uid, $pwd)) {
    // get collation
    $getCol = $install->db->testConnect($host, '', $uid, $pwd, "SHOW COLLATION");

    if ($install->db->getRecordCount($getCol) > 0) {
        $output = '<select id="database_collation" name="database_collation">';

        while ($row = $install->db->getRow($getCol, 'num')) {
            $collation = htmlentities($row[0]);
            $selected = ( $collation==$database_collation ? ' selected' : '' );
            $output .= '<option value="'.$collation.'"'.$selected.'>'.$collation.'</option>';
        }

        $output .= '</select>';
    }
}
echo $output;
?>