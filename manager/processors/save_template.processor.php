<?php
if (!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if (!$modx->hasPermission('save_template')) {	
	$e->setError(3);
	$e->dumpError();	
}

$id = intval($_POST['id']);
$template = $modx->db->escape($_POST['post']);
$templatename = $modx->db->escape(trim($_POST['templatename']));
$description = $modx->db->escape($_POST['description']);
$locked = $_POST['locked']=='on' ? 1 : 0 ;

$default_child_template = intval($_POST['default_child_template']);
$restrict_children = $_POST['restrict_children'] ? 1 : 0;
$tmp_array = is_array($_POST['allowed_child_templates']) ? $_POST['allowed_child_templates'] : array();
$allowed_child_templates = '';
foreach($tmp_array as $val) {
    $allowed_child_templates .= ','.intval($val);
}
$allowed_child_templates = substr($allowed_child_templates, 1);

if (empty($_POST['newcategory']) && $_POST['categoryid'] > 0) {
    $categoryid = $modx->db->escape($_POST['categoryid']);
} elseif (empty($_POST['newcategory']) && $_POST['categoryid'] <= 0) {
    $categoryid = 0;
} else {
    include_once "categories.inc.php";
    $catCheck = checkCategory($modx->db->escape($_POST['newcategory']));
    if ($catCheck) {
        $categoryid = $catCheck;
    } else {
        $categoryid = newCategory($_POST['newcategory']);
    }
}

if (empty($templatename)) {
	$templatename = "Untitled template";
}

switch ($_POST['mode']) {
    case '19':
		$modx->invokeEvent("OnBeforeTempFormSave",
								array(
									"mode"	=> "new",
									"id"	=> $id
							));	
							
		// disallow duplicate names for new templates
		$sql = "SELECT COUNT(id) FROM " . $modx->getFullTableName('site_templates') . " 				 WHERE templatename = '$templatename'";

		$rs = $modx->db->query($sql);
		$count = $modx->db->getValue($rs);

		if($count > 0) {
			$modx->event->alert(sprintf($_lang['duplicate_name_found_general'], $_lang['template'], $templatename));

			// prepare a few request/post variables for form redisplay...
			$_REQUEST['a'] = '19';
			$_POST['locked'] = isset($_POST['locked']) && $_POST['locked'] == 'on' ? 1 : 0;
			$_POST['category'] = $categoryid;
			$_GET['stay'] = $_POST['stay'];
			require('header.inc.php');
			include(dirname(dirname(__FILE__)).'/actions/mutate_templates.dynamic.php');
			include 'footer.inc.php';
			
			exit;
		}

		$sql = "INSERT INTO " . $modx->getFullTableName('site_templates') . " 
			(templatename, description, content, locked, category, default_child_template, restrict_children, allowed_child_templates) 
			VALUES('$templatename', '$description', '$template', '$locked', $categoryid, $default_child_template, $restrict_children, '$allowed_child_templates')";

		$modx->db->query($sql);

		$newid = $modx->db->getInsertId();

		$modx->invokeEvent("OnTempFormSave",
								array(
									"mode"	=> "new",
									"id"	=> $newid
							));				

		// empty cache
		include_once "cache_sync.class.processor.php";
		$sync = new synccache();
		$sync->setCachepath("../assets/cache/");
		$sync->setReport(false);
		$sync->emptyCache(); 

		if($_POST['stay']!='') {
			$a = ($_POST['stay']=='2') ? "16&id=$newid":"19";
			$header="Location: index.php?a=".$a."&r=2&stay=".$_POST['stay'];
			header($header);
		} else {
			$header="Location: index.php?a=76&r=2";
			header($header);
		}
        break;

    case '16':
		$modx->invokeEvent("OnBeforeTempFormSave",
								array(
									"mode"	=> "upd",
									"id"	=> $id
							));	   
		
		// disallow duplicate names for new templates
		$sql = "SELECT COUNT(id) FROM " . $modx->getFullTableName('site_templates') . " 
		WHERE templatename = '$templatename' AND id != '$id'";

		$rs = $modx->db->query($sql);
		$count = $modx->db->getValue($rs);

		if ($count > 0) {
			$modx->event->alert(sprintf($_lang['duplicate_name_found_general'], $_lang['template'], $templatename));

			// prepare a few request/post variables for form redisplay...
			$_REQUEST['a'] = '16';
			$_POST['locked'] = isset($_POST['locked']) && $_POST['locked'] == 'on' ? 1 : 0;
			$_POST['category'] = $categoryid;
			$_GET['stay'] = $_POST['stay'];
			require('header.inc.php');
			include(dirname(dirname(__FILE__)).'/actions/mutate_templates.dynamic.php');
			include 'footer.inc.php';
			
			exit;
		}
							
		$sql = "UPDATE " . $modx->getFullTableName('site_templates') . " 
			SET templatename='$templatename', description='$description', 
			content='$template', locked='$locked', category=$categoryid,
			default_child_template = $default_child_template,
			restrict_children = $restrict_children,
			allowed_child_templates = '$allowed_child_templates'
			WHERE id=$id;";

		$modx->db->query($sql);

		$modx->invokeEvent("OnTempFormSave",
								array(
									"mode"	=> "upd",
									"id"	=> $id
							));	    		

		// first empty the cache		
		include_once "cache_sync.class.processor.php";
		$sync = new synccache();
		$sync->setCachepath("../assets/cache/");
		$sync->setReport(false);
		$sync->emptyCache(); 		

		if($_POST['stay']!='') {
			$a = ($_POST['stay']=='2') ? "16&id=$id":"19";
			$header="Location: index.php?a=".$a."&r=2&stay=".$_POST['stay'];
			header($header);
		} else {
			$header="Location: index.php?a=76&r=2";
			header($header);
		}
}
?>
