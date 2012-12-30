<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if (!$modx->hasPermission('save_snippet')) {
	$e->setError(3);
	$e->dumpError();	
}

$id = intval($_POST['id']);
$name = $modx->db->escape(trim($_POST['name']));
$description = $modx->db->escape($_POST['description']);
$locked = $_POST['locked']=='on' ? 1 : 0 ;
$snippet = trim($modx->db->escape($_POST['post']));
// strip out PHP tags from snippets
if ( strncmp($snippet, "<?", 2) == 0 ) {
    $snippet = substr($snippet, 2);
    if ( strncmp( $snippet, "php", 3 ) == 0 ) $snippet = substr($snippet, 3);
    if ( substr($snippet, -2, 2) == '?>' ) $snippet = substr($snippet, 0, -2);
}
$properties = $modx->db->escape($_POST['properties']);
$moduleguid = $modx->db->escape($_POST['moduleguid']);
$sysevents = $_POST['sysevents'];

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

if (empty($name)) {
	$name = "Untitled snippet";
} 

switch ($_POST['mode']) {
    case '23':
		$modx->invokeEvent("OnBeforeSnipFormSave",
								array(
									"mode"	=> "new",
									"id"	=> $id
								));
								
		// disallow duplicate names for new snippets
		$sql = "SELECT COUNT(id) FROM " . $modx->getFullTableName('site_snippets') . " 
		WHERE name = '$name'";

		$rs = $modx->db->query($sql);

		$count = $modx->db->getValue($rs);

		if ($count > 0) {
			$modx->event->alert(sprintf($_lang['duplicate_name_found_general'], $_lang["snippet"], $name));

			// prepare a few variables prior to redisplaying form...
			$_REQUEST['id'] = 0;
			$_REQUEST['a'] = '23';
			$_GET['a'] = '23';
			$content = array();
			$content['id'] = 0;
			$content = array_merge($content, $_POST);
			$content['locked'] = $content['locked'] == 'on' ? 1: 0;
			$content['category'] = $_POST['categoryid'];
			$content['snippet'] = preg_replace("/^\s*\<\?php/m", '', $_POST['post']);
			$content['snippet'] = preg_replace("/\?\>\s*/m", '', $content['snippet']);

			include 'header.inc.php';
			include(dirname(dirname(__FILE__)).'/actions/mutate_snippet.dynamic.php');
			include 'footer.inc.php';
			
			exit;
		}

		$sql = "INSERT INTO " . $modx->getFullTableName('site_snippets') . " (name, description, snippet, moduleguid, locked, properties, category) 
		VALUES('$name', '$description', '$snippet', '$moduleguid', '$locked', '$properties', '$categoryid');";

		$modx->db->query($sql);
		
		$newid = $modx->db->getInsertId();

		$modx->invokeEvent("OnSnipFormSave",
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
			$a = ($_POST['stay']=='2') ? "22&id=$newid":"23";
			$header="Location: index.php?a=".$a."&r=2&stay=".$_POST['stay'];
			header($header);
		} else {
			$header="Location: index.php?a=76&r=2";
			header($header);
		}
        break;

    case '22':
		$modx->invokeEvent("OnBeforeSnipFormSave",
								array(
									"mode"	=> "upd",
									"id"	=> $id
								));	
								
		$sql = "UPDATE " . $modx->getFullTableName('site_snippets') . " 
			SET name='$name', description='$description', snippet='$snippet', moduleguid='$moduleguid', locked='$locked', properties='$properties', category='$categoryid'  
			WHERE id='".$id."';";

		$modx->db->query($sql);

		$modx->invokeEvent("OnSnipFormSave",
								array(
									"mode"	=> "upd",
									"id"	=> $id
								));	

		// empty cache
		include_once "cache_sync.class.processor.php";
		$sync = new synccache();
		$sync->setCachepath("../assets/cache/");
		$sync->setReport(false);
		$sync->emptyCache();
		
		if ($_POST['runsnippet']) {
		 	run_snippet($snippet);	
		}
		
		if ($_POST['stay'] != '') {
			$a = ($_POST['stay']=='2') ? "22&id=$id":"23";
			$header="Location: index.php?a=".$a."&r=2&stay=".$_POST['stay'];
			header($header);
		} else {
			$header="Location: index.php?a=76&r=2";
			header($header);
		}
        break;
}
?>
