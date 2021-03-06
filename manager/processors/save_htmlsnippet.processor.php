<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('save_chunk')) {
	$e->setError(3);
	$e->dumpError();	
}
?>
<?php

$id = intval($_POST['id']);
$snippet = $modx->db->escape($_POST['post']);
$name = $modx->db->escape(trim($_POST['name']));
$description = $modx->db->escape($_POST['description']);
$locked = $_POST['locked']=='on' ? 1 : 0 ;

//Kyle Jaebker - added category support
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

if($name=="") $name = "Untitled chunk";

switch ($_POST['mode']) {
    case '77':

		// invoke OnBeforeChunkFormSave event
		$modx->invokeEvent("OnBeforeChunkFormSave",
								array(
									"mode"	=> "new",
									"id"	=> $id
								));

		// disallow duplicate names for new chunks
		$sql = "SELECT COUNT(id) FROM " . $modx->getFullTableName('site_htmlsnippets') . " 
		WHERE name = '{$name}'";
		
		$rs = $modx->db->query($sql);
		$count = $modx->db->getValue($rs);

		if($count > 0) {
			$modx->event->alert(sprintf($_lang['duplicate_name_found_general'], $_lang['chunk'], $name));

			// prepare a few variables prior to redisplaying form...
			$content = array();
			$_REQUEST['id'] = 0;
			$_REQUEST['a'] = '77';
			$_GET['stay'] = $_POST['stay'];
			$content['id'] = 0;
			$content['locked'] = $_POST['locked'] == 'on' ? 1 : 0;
			$content['category'] = $_POST['categoryid'];

			require('header.inc.php');
			include(dirname(dirname(__FILE__)).'/actions/mutate_htmlsnippet.dynamic.php');
			include 'footer.inc.php';
			
			exit;
		}
		//do stuff to save the new doc
		$sql = "INSERT INTO " . $modx->getFullTableName('site_htmlsnippets') . 
		" (name, description, snippet, locked, category) 
		VALUES('$name', '$description', '$snippet', '$locked', $categoryid)";

		$modx->db->query($sql);

		$newid = $modx->db->getInsertId();

		$modx->invokeEvent("OnChunkFormSave",
			array(
				"mode"	=> "new",
				"id"	=> $newid
			));

		// empty cache
		include_once "cache_sync.class.processor.php";
		$sync = new synccache();
		$sync->setCachepath("../assets/cache/");
		$sync->setReport(false);
		$sync->emptyCache(); // first empty the cache		
		
		// finished emptying cache - redirect
		if($_POST['stay']!='') {
			$a = ($_POST['stay']=='2') ? "78&id=$newid":"77";
			$header="Location: index.php?a=".$a."&r=2&stay=".$_POST['stay'];
			header($header);
		} else {
			$header="Location: index.php?a=76&r=2";
			header($header);
		}
		break;

    case '78':

		$modx->invokeEvent("OnBeforeChunkFormSave",
				array(
					"mode"	=> "upd",
					"id"	=> $id
				));

		$sql = "UPDATE " . $modx->getFullTableName('site_htmlsnippets') . " 
		SET name='$name', description='$description', snippet='$snippet', locked='$locked', category=$categoryid 
		WHERE id='$id'";

		$modx->db->query($sql);

		$modx->invokeEvent("OnChunkFormSave",
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

		// finished emptying cache - redirect	
		if($_POST['stay']!='') {
			$a = ($_POST['stay']=='2') ? "78&id=$id":"77";
			$header="Location: index.php?a=".$a."&r=2&stay=".$_POST['stay'];
			header($header);
		} else {
			$header="Location: index.php?a=76&r=2";
			header($header);
		}

        break;
}
?>
