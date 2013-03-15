<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!($modx->hasPermission('new_module')||$modx->hasPermission('edit_module')||$modx->hasPermission('exec_module'))) {
	$e->setError(3);
	$e->dumpError();
}
$theme = $manager_theme ? "$manager_theme/":"";

// initialize page view state - the $_PAGE object
$modx->manager->initPageViewState();

// get and save search string
if($_REQUEST['op']=='reset') {
	$query = '';
	$_PAGE['vs']['search']='';
}
else {
	$query = isset($_REQUEST['search'])? $_REQUEST['search']:$_PAGE['vs']['search'];
	$sqlQuery = $modx->db->escape($query);
	$_PAGE['vs']['search'] = $query;
}

// get & save listmode
$listmode = isset($_REQUEST['listmode']) ? $_REQUEST['listmode']:$_PAGE['vs']['lm'];
$_PAGE['vs']['lm'] = $listmode;

?>
<script>
	
	var temp_lang = {
		//setup temp generic values used in js e.g. confirm_delete_user -> confirm_delete so we can use only one js function
		confirm_delete: '<?php echo $_lang['confirm_delete_module']; ?>',
		confirm_duplicate: '<?php echo $_lang['confirm_duplicate_record']; ?>'
	}
	
</script>

<h1><?php echo $_lang['module_management']; ?></h1>

<div class="sectionBody">
	<!-- load modules -->
	<p><?php echo $_lang['module_management_msg']; ?></p>

	<div id="actions">
		<ul class="actionButtons">
			<li><a href="index.php?a=107"><img src="<?php echo $_style["icons_save"] ?>" /> <?php echo $_lang['new_module'] ?></a></li>
		</ul>
	</div>

	<div>
	<?php
	$action_col = '';
	if($modx->hasPermission('exec_module')){
		$action_col .= "<a href='index.php?a=112&id=[+id+]' title='".$_lang['run_module']."'><img width='16' align='absmiddle' height='16' src='media/style/$manager_theme/images/icons/module.gif'></a>";
	}else{
		$action_col .= "<img width='16' align='absmiddle' height='16' src='media/style/$manager_theme/images/icons/module.gif' class='disabled-action'>";
	}
	
	if($modx->hasPermission('edit_module')){
		$action_col .= "<a href='index.php?a=108&id=[+id+]' title='".$_lang['edit']."'><img width='16' align='absmiddle' height='16' src='".$_style['icons_edit_document']."'></a>";
	}else{
		$action_col .= "<img width='16' align='absmiddle' height='16' src='".$_style['icons_edit_document']."' class='disabled-action'>";
	}
	
	if($modx->hasPermission('new_module')){
		$action_col .= "<a href='index.php?a=111&id=[+id+]' class='js-confirm-duplicate' title='".$_lang['duplicate']."'><img width='16' align='absmiddle' height='16' src='".$_style['icons_resource_duplicate']."'></a>";
	}else{
		$action_col .= "<img width='16' align='absmiddle' height='16' src='".$_style['icons_resource_duplicate']."' class='disabled-action'>";
	}
	
	if($modx->hasPermission('delete_module')){
		$action_col .= "<a href='index.php?a=110&id=[+id+]' class='js-confirm-delete' title='".$_lang['delete']."'><img width='16' align='absmiddle' height='16' src='".$_style['icons_delete']."'></a>";
	}else{
		$action_col .= "<img width='16' align='absmiddle' height='16' src='".$_style['icons_delete']."' class='disabled-action'>";
	}


	$sql = "SELECT id,name,description,IF(locked,'Yes','-') as 'locked',IF(disabled,'".$_lang['yes']."','-') as 'disabled',IF(icon<>'',icon,'".$_style['icons_modules']."') as'icon' " .
			"FROM ".$modx->getFullTableName("site_modules")." ".
			(!empty($sqlQuery) ? " WHERE (name LIKE '%$sqlQuery%') OR (description LIKE '%$sqlQuery%')":"")." ".
			"ORDER BY name";
	$ds = $modx->db->query($sql);
	include_once $base_path."manager/includes/controls/datagrid.class.php";
	$grd = new DataGrid('',$ds,$number_of_results); // set page size to 0 t show all items
	$grd->noRecordMsg = $_lang["no_records_found"];
	$grd->cssClass="grid";
	$grd->columnHeaderClass="gridHeader";
	$grd->itemClass="gridItem";
	$grd->altItemClass="gridAltItem";
	$grd->fields="icon,name,description,locked,disabled";
	$grd->columns=$_lang["icon"]." ,".$_lang["name"]." ,".$_lang["description"]." ,".$_lang["locked"]." ,".$_lang["disabled"].','. $_lang['actions'];
	$grd->colWidths="34,,,60,60,100";
	$grd->colAligns="center,,,center,center,left";
	$grd->colTypes="template:<img src='[+value+]' width='32' height='32' />||template:<a href='index.php?a=108&id=[+id+]' title='".$_lang["module_edit_click_title"]."'>[+value+]</a>||template:[+description+]||template:[+locked+]||template:[+disabled+]||template:" . $action_col;
	if($listmode=='1') $grd->pageSize=0;
	if($_REQUEST['op']=='reset') $grd->pageNumber = 1;
	// render grid
	echo $grd->render();
	?>
	</div>
</div>
