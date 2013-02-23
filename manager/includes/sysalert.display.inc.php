<?php

	/**
	 *	System Alert Message Queue Display file
	 *	Written By Raymond Irving, April, 2005
	 *
	 *	Used to display system alert messages inside the browser
	 *
	 */

	require_once(dirname(__FILE__).'/protect.inc.php');

	$sysMsgs = "";
	$limit = count($SystemAlertMsgQueque);
	for($i=0;$i<$limit;$i++) {
		$sysMsgs .= $SystemAlertMsgQueque[$i]."<hr sys/>";
	}
	// reset message queque
	unset($_SESSION['SystemAlertMsgQueque']);
	$_SESSION['SystemAlertMsgQueque'] = array();
	$SystemAlertMsgQueque = &$_SESSION['SystemAlertMsgQueque'];

	if($sysMsgs!="") {
?>

<script type="text/javascript">
$(function() {
	var sysAlert = $('<div id="sysAlert"><?php echo $modx->db->escape($sysMsgs);?></div>')
		.appendTo('body')
		.dialog({
			modal: true,
			autoOpen: true,
			title: "<?php echo $_lang['sys_alert']; ?>",
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
});
</script>
<?php
	}
?>
