<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

unset($_SESSION['itemname']); // clear this, because it's only set for logging purposes

$searchform_title = !empty($_REQUEST['pagetitle']) ? htmlentities($_REQUEST['pagetitle'], ENT_QUOTES, $modx_manager_charset) : '';
$searchform_longtitle = !empty($_REQUEST['longtitle']) ? htmlentities($_REQUEST['longtitle'], ENT_QUOTES, $modx_manager_charset) : '';
$searchform_content = !empty($_REQUEST['content']) ? htmlentities($_REQUEST['content'], ENT_QUOTES, $modx_manager_charset) : '';
?>

<h1><?php echo $_lang['search_criteria']; ?></h1>

<div class="sectionBody">

<form action="index.php?a=71" method="post" name="searchform">
<table>
  <tr>
    <td><?php echo $_lang['search_criteria_id']; ?></td>
    <td>&nbsp;</td>
    <td><input name="searchid" type="text" value="" /></td>
	<td><?php echo $_lang['search_criteria_id_msg']; ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang['search_criteria_title']; ?></td>
    <td>&nbsp;</td>
    <td><input name="pagetitle" type="text" value="<?php echo $searchform_title; ?>" /></td>
	<td><?php echo $_lang['search_criteria_title_msg']; ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang['search_criteria_longtitle']; ?></td>
    <td>&nbsp;</td>
    <td><input name="longtitle" type="text" value="<?php echo $searchform_longtitle; ?>" /></td>
	<td><?php echo $_lang['search_criteria_longtitle_msg']; ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang['search_criteria_content']; ?></td>
    <td>&nbsp;</td>
    <td><input name="content" type="text" value="<?php echo $searchform_content; ?>" /></td>
	<td><?php echo $_lang['search_criteria_content_msg']; ?></td>
  </tr>
  <tr>
  	<td colspan="4">
		<ul class="actionButtons">
		    <li><a href="#" onclick="document.searchform.submitok.click();"><img src="<?php echo $_style["icons_save"] ?>" alt="Save" /> <?php echo $_lang['search'] ?></a></li>
		    <li><a href="index.php?a=2"><img src="<?php echo $_style["icons_cancel"] ?>" alt="Cancel" /> <?php echo $_lang['cancel'] ?></a></li>
		</ul>
	</td>
  </tr>
</table>

<input type="submit" value="Search" name="submitok" style="display:none" />
</form>
</div>


<?php
if(isset($_REQUEST['submitok'])) {
	$searchid = !empty($_REQUEST['searchid']) ? intval($_REQUEST['searchid']) : 0;
	$searchtitle = htmlentities($_POST['pagetitle'], ENT_QUOTES, $modx_manager_charset);
	$searchcontent = $modx->db->escape($_REQUEST['content']);
	$searchlongtitle = $modx->db->escape($_REQUEST['longtitle']);

$sqladd .= $searchid!="" ? " AND $dbase.`".$table_prefix."site_content`.id='$searchid' " : "" ;
$sqladd .= $searchtitle!="" ? " AND $dbase.`".$table_prefix."site_content`.pagetitle LIKE '%$searchtitle%' " : "" ;
$sqladd .= $searchlongtitle!="" ? " AND $dbase.`".$table_prefix."site_content`.longtitle LIKE '%$searchlongtitle%' " : "" ;
$sqladd .= $searchcontent!="" ? " AND $dbase.`".$table_prefix."site_content`.content LIKE '%$searchcontent%' " : "" ;

$sql = "SELECT id, contenttype, pagetitle, description, deleted, published, isfolder, type FROM $dbase.`".$table_prefix."site_content` where 1=1 ".$sqladd." ORDER BY id;";
$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);
?>
<div class="sectionHeader"><?php echo $_lang['search_results']; ?></div><div class="sectionBody">
<?php
if($limit<1) {
	echo $_lang['search_empty'];
} else {
	printf('<p>'.$_lang['search_results_returned_msg'].'</p>', $limit);
?>

  <table id="search-documents"> 
    <thead> 
      <tr> 
		<th class="doc-view"></th>
        <th class="doc-id"><?php echo $_lang['search_results_returned_id']; ?></th> 
        <th class="doc-title"><?php echo $_lang['search_results_returned_title']; ?></th> 
        <th class="doc-desc"><?php echo $_lang['search_results_returned_desc']; ?></th>
		<th class="doc-icon"></th>
      </tr> 
    </thead> 
    <tbody>
     <?php
    // icons by content type
    $icons = array(
        'application/rss+xml' => $_style["tree_page_rss"],
        'application/pdf' => $_style["tree_page_pdf"],
        'application/vnd.ms-word' => $_style["tree_page_word"],
        'application/vnd.ms-excel' => $_style["tree_page_excel"],
        'text/css' => $_style["tree_page_css"],
        'text/html' => $_style["tree_page_html"],
        'text/plain' => $_style["tree_page"],
        'text/xml' => $_style["tree_page_xml"],
        'text/javascript' => $_style["tree_page_js"],
        'image/gif' => $_style["tree_page_gif"],
        'image/jpg' => $_style["tree_page_jpg"],
        'image/png' => $_style["tree_page_png"]
    );

	for ($i = 0; $i < $limit; $i++) {
		$logentry = $modx->db->getRow($rs);

		// figure out the icon for the document...
		$icon = '';
		$alt = '';
		if ($logentry['type']=='reference') {
			$icon .= $_style["tree_linkgo"];
			$alt = 'reference';
		} elseif ($logentry['isfolder'] == 0) {
			$icon .= isset($icons[$logentry['contenttype']]) ? $icons[$logentry['contenttype']] : $_style["tree_page_html"];
			$alt = $logentry['contenttype'];
		} else {
			$icon .= $_style['tree_folder'];
			$alt = 'folder';
		}

		$tdClass = "";
		if($logentry['published'] == 0) {
			$tdClass .= ' class="unpublishedNode"';
		}
		if($logentry['deleted'] == 1) {
			$tdClass .= ' class="deletedNode"';
		}
?>
    <tr>
      <td><a href="index.php?a=3&amp;id=<?php echo $logentry['id']; ?>" title="<?php echo $_lang['search_view_docdata']; ?>"><img src="<?php echo $_style['icons_resource_overview']; ?>" alt="<?php echo $_lang['search_view_docdata']; ?>" /></a></td> 
      <td><?php echo $logentry['id']; ?></td> 
	  <?php if (function_exists('mb_strlen') && function_exists('mb_substr')) {?>
		<td<?php echo $tdClass; ?>><?php echo mb_strlen($logentry['pagetitle'], $modx_manager_charset)>20 ? mb_substr($logentry['pagetitle'], 0, 20, $modx_manager_charset)."..." : $logentry['pagetitle'] ; ?></td> 
		<td<?php echo $tdClass; ?>><?php echo mb_strlen($logentry['description'], $modx_manager_charset)>35 ? mb_substr($logentry['description'], 0, 35, $modx_manager_charset)."..." : $logentry['description'] ; ?></td>
	  <?php } else { ?>
		<td<?php echo $tdClass; ?>><?php echo strlen($logentry['pagetitle'])>20 ? substr($logentry['pagetitle'], 0, 20)."..." : $logentry['pagetitle'] ; ?></td> 
		<td<?php echo $tdClass; ?>><?php echo strlen($logentry['description'])>35 ? substr($logentry['description'], 0, 35)."..." : $logentry['description'] ; ?></td>
	  <?php } ?>
      <td><img src="<?php echo $icon; ?>" alt="<?php echo $alt; ?>" /></td>
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
<?php
}
?>
