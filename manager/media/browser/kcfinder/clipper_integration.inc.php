<?php
/**
 * This file is for the integration of KCFinder with ClipperCMS and must be required at the start of core/autoload.php
 */

// CLIPPERCMS INTEGRATION
list($base_url,) = explode('/manager/', $_SERVER['REQUEST_URI']);
$base_url .= '/';
define('MODX_BASE_URL', $base_url);
require_once('../../../includes/config.inc.php');
startCMSSession();
if(!isset($_SESSION['mgrValidated'])) {
    exit();
}

// CLIPPERCMS SETTINGS
if (!defined('IN_MANAGER_MODE')) define('IN_MANAGER_MODE', 'true');
require_once('../../../includes/document.parser.class.inc.php');
$modx = new DocumentParser;

$modx->getSettings();

// disable upload according to the type[images,files,flash,media] and the user settings
$modx->config['kcfinder_access_files_enabled'] = true;// default is access allowed
$modx->config['kcfinder_access_dirs_enabled'] = true;// default is access allowed
if($_GET['type'] == 'images' || $_GET['type'] == 'image'){//not sure 'image' is still a valid value
    if (empty($modx->config['upload_images'])) {
        $modx->config['kcfinder_access_files_enabled'] = false;
        $modx->config['kcfinder_access_dirs_enabled'] = false;
    }
}
if($_GET['type'] == 'files' || $_GET['type'] == 'file'){//not sure 'file' is still a valid value
    if (empty($modx->config['upload_files'])) {
        $modx->config['kcfinder_access_files_enabled'] = false;
        $modx->config['kcfinder_access_dirs_enabled'] = false;
    }
}
if($_GET['type'] == 'flash'){
    if (empty($modx->config['upload_flash'])) {
        $modx->config['kcfinder_access_files_enabled'] = false;
        $modx->config['kcfinder_access_dirs_enabled'] = false;
    }
}
if($_GET['type'] == 'media'){
    if (empty($modx->config['upload_media'])) {
        $modx->config['kcfinder_access_files_enabled'] = false;
        $modx->config['kcfinder_access_dirs_enabled'] = false;
    }
}

// USE CLIPPERCMS MANAGER LANGUAGE
require_once('../../../includes/get_manager_language.inc.php');
if (isset($modx_lang_attribute) && ctype_alpha($modx_lang_attribute) && strlen($modx_lang_attribute) == 2) {
    $_GET['langCode'] = $_REQUEST['langCode'] = $modx_lang_attribute;
}
