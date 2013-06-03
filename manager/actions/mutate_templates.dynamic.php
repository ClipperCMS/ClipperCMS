<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

switch((int) $_REQUEST['a']) {
  case 16:
    if(!$modx->hasPermission('edit_template')) {
      $e->setError(3);
      $e->dumpError();
    }
    break;
  case 19:
    if(!$modx->hasPermission('new_template')) {
      $e->setError(3);
      $e->dumpError();
    }
    break;
  default:
    $e->setError(3);
    $e->dumpError();
}

if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    // check to see the template editor isn't locked
    $sql = "SELECT internalKey, username FROM $dbase.`".$table_prefix."active_users` WHERE $dbase.`".$table_prefix."active_users`.action=16 AND $dbase.`".$table_prefix."active_users`.id=$id";
    $rs = $modx->db->query($sql);
    $limit = $modx->db->getRecordCount($rs);
    if($limit>1) {
        for ($i=0;$i<$limit;$i++) {
            $lock = $modx->db->getRow($rs);
            if($lock['internalKey']!=$modx->getLoginUserID()) {
                $msg = sprintf($_lang["lock_msg"],$lock['username'],"template");
                $e->setError(5, $msg);
                $e->dumpError();
            }
        }
    }
    // end check for lock
} else {
    $id='';
}

$content = array();
if(isset($_REQUEST['id']) && $_REQUEST['id']!='' && is_numeric($_REQUEST['id'])) {
    $sql = "SELECT * FROM $dbase.`".$table_prefix."site_templates` WHERE $dbase.`".$table_prefix."site_templates`.id = $id;";
    $rs = $modx->db->query($sql);
    $limit = $modx->db->getRecordCount($rs);
    if($limit>1) {
        echo "Oops, something went terribly wrong...<p>";
        print "More results returned than expected. Which sucks. <p>Aborting.";
        exit;
    }
    if($limit<1) {
        echo "Oops, something went terribly wrong...<p>";
        print "No database record has been found for this template. <p>Aborting.";
        exit;
    }
    $content = $modx->db->getRow($rs);
    $_SESSION['itemname']=$content['templatename'];
    if($content['locked']==1 && $_SESSION['mgrRole']!=1) {
        $e->setError(3);
        $e->dumpError();
    }
} else {
    $_SESSION['itemname']="New template";
}

$content = array_merge($content, $_POST);

// Template lists
$allowed_child_templates = explode(',', $content['allowed_child_templates']);
$output_act = '<label><input type="checkbox" value="0" name="allowed_child_templates[]" '.(in_array(0, $allowed_child_templates) ? ' checked="checked"' : '').'/>(blank)</label>';
$output_dct = '<option value="0">'.$_lang['default_child_template_use_system_setting'].' ('.$modx->db->getValue('SELECT templatename FROM '.$modx->getFullTableName('site_templates').' WHERE id = '.$modx->config['default_template']).')</option>';
$rs_templates = $modx->db->select('id,templatename', $modx->getFullTablename('site_templates'), null, 'templatename ASC');
while ($row_templates = $modx->db->getRow($rs_templates)) {
    $output_act .= '<label><input type="checkbox" value="'.$row_templates['id'].'"'.(in_array($row_templates['id'], $allowed_child_templates) ? ' checked="checked"' : '').' name="allowed_child_templates[]" >'.$row_templates['templatename'].'</label>';
    $output_dct .= '<option value="'.$row_templates['id'].'"'.(isset($content['default_child_template']) && $content['default_child_template'] == $row_templates['id'] ? ' selected="selected"' : '').'>'.$row_templates['templatename'].'</option>';
}
?>
<script>
function duplicaterecord(){
    if(confirm("<?php echo $_lang['confirm_duplicate_record'] ?>")==true) {
        documentDirty=false;
        document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=96";
    }
}

function deletedocument() {
    if(confirm("<?php echo $_lang['confirm_delete_template']; ?>")==true) {
        documentDirty=false;
        document.location.href="index.php?id=" + document.mutate.id.value + "&a=21";
    }
}

</script>

<form name="mutate" method="post" action="index.php">
<?php
    // invoke OnTempFormPrerender event
    $evtOut = $modx->invokeEvent("OnTempFormPrerender",array("id" => $id));
    if(is_array($evtOut)) echo implode("",$evtOut);
?>
<input type="hidden" name="a" value="20">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
<input type="hidden" name="mode" value="<?php echo (int) $_REQUEST['a'];?>">

    <h1><?php echo $_lang['template_title']; ?></h1>

    <div id="actions">
          <ul class="actionButtons">
              <li id="Button1">
                <a href="#" onclick="documentDirty=false; document.mutate.save.click();saveWait('mutate');">
                  <img src="<?php echo $_style["icons_save"]?>" /> <?php echo $_lang['save']?>
                </a>
                  <span class="and"> + </span>
                <select id="stay" name="stay">
                  <option id="stay1" value="1" <?php echo $_REQUEST['stay']=='1' ? ' selected=""' : ''?> ><?php echo $_lang['stay_new']?></option>
                  <option id="stay2" value="2" <?php echo $_REQUEST['stay']=='2' ? ' selected="selected"' : ''?> ><?php echo $_lang['stay']?></option>
                  <option id="stay3" value=""  <?php echo $_REQUEST['stay']=='' ? ' selected=""' : ''?>  ><?php echo $_lang['close']?></option>
                </select>
              </li>
              <?php
                if ($_REQUEST['a'] == '16') { ?>
              <li id="Button2"><a href="#" onclick="duplicaterecord();"><img src="<?php echo $_style["icons_resource_duplicate"] ?>" /> <?php echo $_lang["duplicate"]; ?></a></li>
              <li id="Button3" class="disabled"><a href="#" onclick="deletedocument();"><img src="<?php echo $_style["icons_delete_document"]?>" /> <?php echo $_lang['delete']?></a></li>
              <?php } else { ?>
              <li id="Button3"><a href="#" onclick="deletedocument();"><img src="<?php echo $_style["icons_delete_document"]?>" /> <?php echo $_lang['delete']?></a></li>
              <?php } ?>
              <li id="Button5"><a href="#" onclick="documentDirty=false;document.location.href='index.php?a=76';"><img src="<?php echo $_style["icons_cancel"]?>" /> <?php echo $_lang['cancel']?></a></li>
          </ul>
    </div>

<div class="sectionBody">

	<?php if ($_REQUEST['a'] == '16') { ?>
	<div id="mutate-templates-tabs" class="js-tabs">
		
		<ul>
			<li><a href="#tabTemplate"><?php echo $_lang["template_edit_tab"] ?></a></li>
			<li><a href="#tabTemplateCode"><?php echo $_lang["template_code"] ?></a></li>
			<li><a href="#tabAssignedTVs"><?php echo $_lang["template_assignedtv_tab"] ?></a></li>
		</ul>
		
		<div id="tabTemplate">
		
	<?php } ?>
			
			<?php echo "\t" . $_lang['template_msg']; ?>
		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td align="left"><img src="<?php echo $_style['tx']; ?>" width="100" height="1" /></td>
		        <td align="left">&nbsp;</td>
		      </tr>
		      <tr>
		        <td align="left"><?php echo $_lang['template_name']; ?>:&nbsp;&nbsp;</td>
		        <td align="left"><input name="templatename" type="text" maxlength="100" value="<?php echo htmlspecialchars($content['templatename']);?>" class="inputBox" style="width:150px;" onChange='documentDirty=true;'><span class="warning" id='savingMessage'></span></td>
		      </tr>
		        <tr>
		        <td align="left"><?php echo $_lang['template_desc']; ?>:&nbsp;&nbsp;</td>
		        <td align="left"><input name="description" type="text" maxlength="255" value="<?php echo htmlspecialchars($content['description']);?>" class="inputBox" style="width:300px;" onChange='documentDirty=true;'></td>
		      </tr>
		      <tr>
		        <td align="left"><?php echo $_lang['existing_category']; ?>:&nbsp;&nbsp;</td>
		        <td align="left"><select name="categoryid" style="width:300px;" onChange='documentDirty=true;'>
		                <option>&nbsp;</option>
		                <?php
		                    include_once "categories.inc.php";
		                    $ds = getCategories();
		                    if($ds) foreach($ds as $n=>$v){
		                        echo "<option value='".$v['id']."'".($content["category"]==$v["id"]? " selected='selected'":"").">".htmlspecialchars($v["category"])."</option>";
		                    }
		                ?>
		            </select>
		        </td>
		      </tr>
		      <tr>
		        <td align="left"><?php echo $_lang['new_category']; ?>:</td>
		        <td align="left"><input name="newcategory" type="text" maxlength="45" value="<?php echo isset($content['newcategory']) ? $content['newcategory'] : '' ?>" class="inputBox" style="width:300px;" onChange='documentDirty=true;'></td>
		      </tr>
		      <tr>
		        <td align="left"><?php echo $_lang['default_child_template']; ?>:</td>
		        <td align="left">
		            <select name="default_child_template" class="template-list" onChange='documentDirty=true;'>
                        <?php echo $output_dct; ?>
		            </select>
		        </td>
		      </tr>
		      <tr>
		        <td align="left"><?php echo $_lang['restrict_children']; ?>:</td>
		        <td align="left">
		            <select name="restrict_children" onChange="documentDirty=true; document.getElementById('allowed_child_templates_section').style.visibility = this.value == '1' ? 'visible' : 'collapse';">
		                <option value="0"<?php if (!@$content['restrict_children']) echo ' selected="selected"'; ?>><?php echo $_lang['no']; ?></option>
		                <option value="1"<?php if (@$content['restrict_children']) echo ' selected="selected"'; ?>><?php echo $_lang['yes']; ?></option>
		            </select>
		      </tr>
		      <tr id="allowed_child_templates_section" style="visibility: <?php echo @$content['restrict_children'] ? 'visible' : 'collapse'; ?>">
		        <td align="left"><?php echo $_lang['allowed_child_templates']; ?>:</td>
		        <td align="left">
		        	<fieldset class="template-list">
		        	    <?php echo $output_act; ?>
		            </fieldset>
		        </td>
		      </tr>
		      <tr>
		        <td align="left" colspan="2"><input name="locked" type="checkbox" <?php echo $content['locked']==1 ? "checked='checked'" : "" ;?> class="inputBox"> <?php echo $_lang['lock_template']; ?> <span class="comment"><?php echo $_lang['lock_template_msg']; ?></span></td>
		      </tr>
		    </table>
		</div><!-- tabTemplate -->
		
		<?php if ($_REQUEST['a'] == '19') { ?><div class="sectionBody"><?php } ?>
			<div id="tabTemplateCode">
			
				<!-- HTML text editor start -->
			    <div style="width:100%;position:relative">
	                <h2 class="editor-heading"><?php echo $_lang['template_code']; ?></h2>
	                <textarea dir="ltr" name="post" class="phptextarea" style="height: 370px; width: 98%;" onChange='documentDirty=true;'><?php echo isset($content['post']) ? htmlspecialchars($content['post']) : htmlspecialchars($content['content']); ?></textarea>
			    </div>
			    <!-- HTML text editor end -->
				
				<input type="submit" name="save" style="display:none">
				
				<div class="help-box">
	                <?php include(dirname(__FILE__).'/../help/includes/tag_syntax.inc.php'); ?>
		        </div>
		        
			</div><!-- tabTemplateCode -->
		<?php if ($_REQUEST['a'] == '19') { ?></div><?php } ?>
		
		<?php if ($_REQUEST['a'] == '16') { ?>

		<div id="tabAssignedTVs">
			
			<?php
			$sql = "SELECT tv.name as 'name', tv.id as 'id', tr.templateid, tr.rank, if(isnull(cat.category),'".$_lang['no_category']."',cat.category) as category
			    FROM ".$modx->getFullTableName('site_tmplvar_templates')." tr
			    INNER JOIN ".$modx->getFullTableName('site_tmplvars')." tv ON tv.id = tr.tmplvarid
			    LEFT JOIN ".$modx->getFullTableName('categories')." cat ON tv.category = cat.id
			    WHERE tr.templateid='{$id}' ORDER BY tr.rank, tv.rank, tv.id";
			
			
			$rs = $modx->db->query($sql);
			$limit = $modx->db->getRecordCount($rs);
			?>
			
			<p><?php if ($limit > 0) echo $_lang['template_tv_msg']; ?></p>
	        <p><?php if($modx->hasPermission('save_template') && $limit > 1) { ?><a href="index.php?a=117&amp;id=<?php echo $_REQUEST['id'] ?>"><?php echo $_lang['template_tv_edit']; ?></a><?php } ?></p>
			<?php
			$tvList = '';
			
			if($limit>0) {
			    for ($i=0;$i<$limit;$i++) {
			        $row = $modx->db->getRow($rs);
			        if ($i == 0 ) $tvList .= '<br /><ul>';
			        $tvList .= '<li><strong>'.$row['name'].'</strong> ('.$row['category'].')</li>';
			    }
			    $tvList .= '</ul>';
			
			} else {
				echo $_lang['template_no_tv'];
			}
			echo $tvList;
			?>

		</div> <!-- tabAssignedTVs -->
		
        <?php } ?>

	</div> <!-- tabs -->
	
<?php
// invoke OnTempFormRender event
$evtOut = $modx->invokeEvent("OnTempFormRender",array("id" => $id));
if(is_array($evtOut)) echo implode("",$evtOut);
?>
</form>
</div>
