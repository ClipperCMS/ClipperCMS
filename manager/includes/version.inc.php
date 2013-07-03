<?php
define('CMS_DOMAIN', 'clippercms.com');
define('CMS_NAME', 'ClipperCMS');

define('CMS_RELEASE_VERSION', '1.2');
define('CMS_RELEASE_NAME', 'Leander');
define('CMS_RELEASE_DATE', '5 July 2013');

define('CMS_FULL_APPNAME', CMS_NAME.' '.CMS_RELEASE_NAME.' '.CMS_RELEASE_NAME.' ('.CMS_RELEASE_DATE.')');

// For backwards compatability
// ---------------------------

$modx_version = CMS_RELEASE_VERSION;
$modx_release_date = CMS_RELEASE_DATE;
$modx_branch = CMS_NAME;

$modx_full_appname = CMS_FULL_APPNAME;

