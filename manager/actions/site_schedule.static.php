<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

/* if(!$modx->hasPermission('edit_document')) {
    $e->setError(3);
    $e->dumpError();
} */
?>

<h1><?php echo $_lang["site_schedule"]?></h1>

<div class="sectionHeader"><?php echo $_lang["publish_events"]?></div><div class="sectionBody" id="lyr1">
<?php
$sql = "SELECT id, pagetitle, pub_date FROM $dbase.`".$table_prefix."site_content` WHERE pub_date > ".time()." ORDER BY pub_date ASC";
$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);
if($limit<1) {
    echo "<p>".$_lang["no_docs_pending_publishing"]."</p>";
} else {
?>
  <table id="schedule-publish-events">
    <thead>
      <tr>
        <th><b><?php echo $_lang['resource'];?></b></th>
        <th width="5%"><b><?php echo $_lang['id'];?></b></th>
        <th><b><?php echo $_lang['publish_date'];?></b></th>
      </tr>
    </thead>
    <tbody>
<?php
    for ($i=0;$i<$limit;$i++) {
        $row = $modx->db->getRow($rs);
?>
    <tr>
      <td><a href="index.php?a=3&id=<?php echo $row['id'] ;?>"><?php echo $row['pagetitle']?></a></td>
      <td><?php echo $row['id'] ;?></td>
      <td><?php echo $modx->toDateFormat($row['pub_date']+$server_offset_time)?></td>
    </tr>
<?php
    }
?>
    </tbody>
</table>
<?php
}
?>

</div>


<div class="sectionHeader"><?php echo $_lang["unpublish_events"];?></div><div class="sectionBody" id="lyr2"><?php
//$db->debug = true;
$sql = "SELECT id, pagetitle, unpub_date FROM $dbase.`".$table_prefix."site_content` WHERE unpub_date > ".time()." ORDER BY unpub_date ASC";
$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);
if($limit<1) {
    echo "<p>".$_lang["no_docs_pending_unpublishing"]."</p>";
} else {
?>
  <table id="schedule-unpublish-events">
    <thead>
      <tr>
        <th><b><?php echo $_lang['resource'];?></b></th>
        <th width="5%"><b><?php echo $_lang['id'];?></b></th>
        <th><b><?php echo $_lang['unpublish_date'];?></b></th>
      </tr>
    </thead>
    <tbody>
<?php
    for ($i=0;$i<$limit;$i++) {
        $row = $modx->db->getRow($rs);
?>
    <tr>
      <td><a href="index.php?a=3&id=<?php echo $row['id'] ;?>"><?php echo $row['pagetitle'] ;?></a></td>
      <td width="5%"><?php echo $row['id'] ;?></td>
      <td><?php echo $modx->toDateFormat($row['unpub_date']+$server_offset_time) ;?></td>
    </tr>
<?php
    }
?>
    </tbody>
</table>
<?php
}
?>

</div>


<div class="sectionHeader"><?php echo $_lang["all_events"];?></div><div class="sectionBody"><?php
$sql = "SELECT id, pagetitle, pub_date, unpub_date FROM $dbase.`".$table_prefix."site_content` WHERE pub_date > 0 OR unpub_date > 0 ORDER BY pub_date DESC";
$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);
if($limit<1) {
    echo "<p>".$_lang["no_docs_pending_pubunpub"]."</p>";
} else {
?>
  <table id="schedule-all-events">
    <thead>
      <tr>
        <th><b><?php echo $_lang['resource'];?></b></th>
        <th width="5%"><b><?php echo $_lang['id'];?></b></th>
        <th width="20%"><b><?php echo $_lang['publish_date'];?></b></th>
        <th width="20%"><b><?php echo $_lang['unpublish_date'];?></b></th>
      </tr>
    </thead>
    <tbody>
<?php
    for ($i=0;$i<$limit;$i++) {
        $row = $modx->db->getRow($rs);
?>
    <tr>
    <td><a href="index.php?a=3&id=<?php echo $row['id']?>"><?php echo $row['pagetitle']?></a></td>
    <td><?php echo $row['id']?></td>
    <td><?php echo $row['pub_date']==0 ? "" : $modx->toDateFormat($row['pub_date']+$server_offset_time)?></td>
    <td><?php echo $row['unpub_date']==0 ? "" : $modx->toDateFormat($row['unpub_date']+$server_offset_time)?></td>
    </tr>
<?php
    }
?>
    </tbody>
</table>
<?php
}
?>
</div>
