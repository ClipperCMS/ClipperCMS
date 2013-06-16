<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('logs')) {
	$e->setError(3);
	$e->dumpError();
}
?>
<h1><?php echo $_lang["view_sysinfo"]; ?></h1>

<script>
	function viewPHPInfo() {
		dontShowWorker = true; // prevent worker from being displayed
		window.location.href="index.php?a=200";
	};
</script>

<!-- server -->
<div class="sectionHeader">Server</div><div class="sectionBody" id="lyr2">

		<table border="0" cellspacing="2" cellpadding="2">
		  <tr>
			<td width="150"><?php echo $_lang['modx_version']?></td>
			<td width="20">&nbsp;</td>
			<td><b><?php echo CMS_RELEASE_VERSION.' '.CMS_RELEASE_NAME; ?></b><?php echo $newversiontext ?></td>
		  </tr>
		  <tr>
			<td width="150"><?php echo $_lang['release_date']?></td>
			<td width="20">&nbsp;</td>
			<td><b><?php echo CMS_RELEASE_DATE; ?></b></td>
		  </tr>
		  <tr>
			<td>phpInfo()</td>
			<td>&nbsp;</td>
			<td><b><a href="#" onclick="viewPHPInfo();return false;"><?php echo $_lang['view']; ?></a></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['access_permissions']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo $use_udperms==1 ? $_lang['enabled'] : $_lang['disabled']; ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['servertime']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo strftime('%H:%M:%S', time()); ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['localtime']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo strftime('%H:%M:%S', time()+$server_offset_time); ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['serveroffset']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo $server_offset_time/(60*60) ?></b> h</td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['database_name']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo str_replace('`','',$dbase) ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['database_server']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo $database_server ?></b></td>
		  </tr>
		  <tr>
		    <td><?php echo $_lang['database_version']?></td>
		    <td>&nbsp;</td>
		    <td><strong><?php echo $modx->db->getVersion(); ?></strong></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['database_charset']?></td>
			<td>&nbsp;</td>
			<td><strong><?php
	$sql1 = "show variables like 'character_set_database'";
    $res = $modx->db->query($sql1);
    $charset = $modx->db->getRow($res, 'num');
    echo $charset[1];
			?></strong></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['database_collation']?></td>
			<td>&nbsp;</td>
			<td><strong><?php
    $sql2 = "show variables like 'collation_database'";
    $res = $modx->db->query($sql2);
    $collation = $modx->db->getRow($res, 'num');
    echo $collation[1];
            ?></strong></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['table_prefix']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo $table_prefix ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['cfg_base_path']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo MODX_BASE_PATH ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['cfg_base_url']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo MODX_BASE_URL ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['cfg_manager_url']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo MODX_MANAGER_URL ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['cfg_manager_path']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo MODX_MANAGER_PATH ?></b></td>
		  </tr>
		  <tr>
			<td><?php echo $_lang['cfg_site_url']?></td>
			<td>&nbsp;</td>
			<td><b><?php echo MODX_SITE_URL ?></b></td>
		  </tr>
		</table>

   </div>


<!-- recent documents -->
<div class="sectionHeader"><?php echo $_lang["activity_title"]; ?></div><div class="sectionBody" id="lyr1">
		<?php echo $_lang["sysinfo_activity_message"]; ?><p>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table">
			<thead>
			<tr>
				<td><b><?php echo $_lang["id"]; ?></b></td>
				<td><b><?php echo $_lang["resource_title"]; ?></b></td>
				<td><b><?php echo $_lang["sysinfo_userid"]; ?></b></td>
				<td><b><?php echo $_lang["datechanged"]; ?></b></td>
			</tr>
			</thead>
			<tbody>
		<?php
		$sql = "SELECT id, pagetitle, editedby, editedon FROM $dbase.`".$table_prefix."site_content` WHERE $dbase.`".$table_prefix."site_content`.deleted=0 ORDER BY editedon DESC LIMIT 20";
		$rs = $modx->db->query($sql);
		$limit = $modx->db->getRecordCount($rs);
		if($limit<1) {
			echo "<p>".$_lang["no_edits_creates"]."</p>";
		} else {
			for ($i = 0; $i < $limit; $i++) {
				$content = $modx->db->getRow($rs);
				$sql = "SELECT username FROM $dbase.`".$table_prefix."manager_users` WHERE id=".$content['editedby'];
				$rs2 = $modx->db->query($sql);
				$limit2 = $modx->db->getRecordCount($rs2);
				if($limit2==0) $user = '-';
				else {
					$r = $modx->db->getRow($rs2);
					$user = $r['username'];
				}
				$bgcolor = ($i % 2) ? 'odd' : 'even';
				echo "<tr class='".$bgcolor."'><td>".$content['id']."</td><td><a href='index.php?a=3&id=".$content['id']."'>".$content['pagetitle']."</a></td><td>".$user."</td><td>".$modx->toDateFormat($content['editedon']+$server_offset_time)."</td></tr>";
			}
		}
		?>
		</tbody>
         </table>
   </div>

<?php
require_once('db_info.class.inc.php');
$db_info = new DbInfo($dbase);
$db_info->output(53, false);
?>

<!-- online users -->
<div class="sectionHeader"><?php echo $_lang['onlineusers_title']; ?></div><div class="sectionBody" id="lyr5">

		<?php
		$html = $_lang["onlineusers_message"].'<b>'.strftime('%H:%M:%S', time()+$server_offset_time).'</b>):<br /><br />
                <table border="0" cellpadding="1" cellspacing="1" width="100%" class="table">
                  <thead>
                    <tr>
                      <td><b>'.$_lang["onlineusers_user"].'</b></td>
                      <td><b>'.$_lang["onlineusers_userid"].'</b></td>
                      <td><b>'.$_lang["onlineusers_ipaddress"].'</b></td>
                      <td><b>'.$_lang["onlineusers_lasthit"].'</b></td>
                      <td><b>'.$_lang["onlineusers_action"].'</b></td>
                      <td><b>'.$_lang["onlineusers_actionid"].'</b></td>		
                    </tr>
                  </thead>
                  <tbody>
        ';
		
		$timetocheck = (time()-(60*20));

		include_once "actionlist.inc.php";

		$sql = "SELECT * FROM $dbase.`".$table_prefix."active_users` WHERE $dbase.`".$table_prefix."active_users`.lasthit>$timetocheck ORDER BY username ASC";
		$rs = $modx->db->query($sql);
		$limit = $modx->db->getRecordCount($rs);
		if($limit<1) {
			$html = "<p>".$_lang['no_active_users_found']."</p>";
		} else {
			for ($i = 0; $i < $limit; $i++) {
				$activeusers = $modx->db->getRow($rs);
				$currentaction = getAction($activeusers['action'], $activeusers['id']);
				$webicon = ($activeusers['internalKey']<0)? "<img align='absmiddle' src='media/style/{$manager_theme}/images/tree/globe.gif' alt='Web user'>":"";
				$bgcolor = ($i % 2) ? 'odd' : 'even';
				$html .= "<tr class='".$bgcolor."'><td><b>".$activeusers['username']."</b></td><td>$webicon&nbsp;".abs($activeusers['internalKey'])."</td><td>".$activeusers['ip']."</td><td>".strftime('%H:%M:%S', $activeusers['lasthit']+$server_offset_time)."</td><td>$currentaction</td><td align='right'>".$activeusers['action']."</td></tr>";
			}
		}
		echo $html;
		?>
		</tbody>
		</table>
</div>
