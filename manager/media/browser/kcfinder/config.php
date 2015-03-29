<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

// IMPORTANT!!! Do not remove uncommented settings in this file even if
// you are using session configuration.
// See http://kcfinder.sunhater.com/install for setting descriptions

$_CONFIG = array(

    'disabled' => false,
    'denyZipDownload' => false,
    'denyUpdateCheck' => false,
    'denyExtensionRename' => false,

    'theme' => "oxygen",

    'uploadURL' => MODX_BASE_URL  . $GLOBALS['modx']->config['rb_base_url'],
    'uploadDir' => $GLOBALS['modx']->config['rb_base_dir'],
    'siteURL' => $GLOBALS['site_url'],
    'assetsURL' => rtrim($GLOBALS['modx']->config['rb_base_url'],'/'),
    'dirPerms' => intval($GLOBALS['modx']->config['new_folder_permissions'],8),
    'filePerms' => intval($GLOBALS['modx']->config['new_file_permissions'],8),

    'maxfilesize' => $GLOBALS['modx']->config['upload_maxsize'],

    'access' => array(
        
        'files' => array(
            'upload' => $GLOBALS['modx']->config['kcfinder_access_files_enabled'],
            'delete' => $GLOBALS['modx']->config['kcfinder_access_files_enabled'],
            'copy' => $GLOBALS['modx']->config['kcfinder_access_files_enabled'],
            'move' => $GLOBALS['modx']->config['kcfinder_access_files_enabled'],
            'rename' => $GLOBALS['modx']->config['kcfinder_access_files_enabled']
        ),

        'dirs' => array(
            'create' => $GLOBALS['modx']->config['kcfinder_access_dirs_enabled'],
            'delete' => $GLOBALS['modx']->config['kcfinder_access_dirs_enabled'],
            'rename' => $GLOBALS['modx']->config['kcfinder_access_dirs_enabled']
        )
    ),

    'deniedExts' => "exe com msi bat php phps phtml php3 php4 cgi pl",

    'types' => array(

        // CKEditor & FCKEditor types
        'files'   =>  str_replace(',', ' ', $GLOBALS['modx']->config['upload_files']),
        'flash'   =>  str_replace(',', ' ', $GLOBALS['modx']->config['upload_flash']),
        'images'  =>  str_replace(',', ' ', $GLOBALS['modx']->config['upload_images']) /* was "*img" */ ,

        // TinyMCE types
        'file'    =>  str_replace(',', ' ', $GLOBALS['modx']->config['upload_files']),
        'media'   =>  str_replace(',', ' ', $GLOBALS['modx']->config['upload_media']),
        'image'   =>  str_replace(',', ' ', $GLOBALS['modx']->config['upload_images']) /* was "*img" */ ,
    ),

    'filenameChangeChars' => array(
            "а"=>"a",
            "б"=>"b",
            "в"=>"v",
            "г"=>"g",
            "д"=>"d",
            "е"=>"e",
            "ё"=>"yo",
            "ж"=>"zh",
            "з"=>"z",
            "и"=>"i",
            "й"=>"j",
            "к"=>"k",
            "л"=>"l",
            "м"=>"m",
            "н"=>"n",
            "о"=>"o",
            "п"=>"p",
            "р"=>"r",
            "с"=>"s",
            "т"=>"t",
            "у"=>"u",
            "ф"=>"f",
            "х"=>"h",
            "ц"=>"c",
            "ч"=>"ch",
            "ш"=>"sh",
            "щ"=>"shh",
            "ы"=>"i",
            "э"=>"e",
            "ю"=>"yu",
            "я"=>"ya",
            "А"=>"A",
            "Б"=>"B",
            "В"=>"V",
            "Г"=>"G",
            "Д"=>"D",
            "Е"=>"E",
            "Ё"=>"Yo",
            "Ж"=>"Zh",
            "З"=>"Z",
            "И"=>"I",
            "Й"=>"J",
            "К"=>"K",
            "Л"=>"L",
            "М"=>"M",
            "Н"=>"N",
            "О"=>"O",
            "П"=>"P",
            "Р"=>"R",
            "С"=>"S",
            "Т"=>"T",
            "У"=>"U",
            "Ф"=>"F",
            "Х"=>"H",
            "Ц"=>"C",
            "Ч"=>"Ch",
            "Ш"=>"Sh",
            "Щ"=>"Shh",
            "Ы"=>"I",
            "Э"=>"E",
            "Ю"=>"Yu",
            "Я"=>"Ya",
            "ь"=>"",
            "Ь"=>"",
            "ъ"=>"",
            "Ъ"=>"",
            " "=>"-",
            "№"=>"N",
            "+"=>"-",
            ":"=>"-",
            ";"=>"-",
            "!"=>"-",
            "?"=>"-",
            "&"=>"and",
            "\'" =>"",
            "Ä" => "Ae",
            "Ö" => "Oe",
            "Ü" => "Ue",
            "ä" => "ae",
            "ö" => "oe",
            "ü" => "ue",
            "è" => "e",
            "é" => "e",
            "à" => "a",
            "ß" => "ss"),

    'dirnameChangeChars' => array(
            "а"=>"a",
            "б"=>"b",
            "в"=>"v",
            "г"=>"g",
            "д"=>"d",
            "е"=>"e",
            "ё"=>"yo",
            "ж"=>"zh",
            "з"=>"z",
            "и"=>"i",
            "й"=>"j",
            "к"=>"k",
            "л"=>"l",
            "м"=>"m",
            "н"=>"n",
            "о"=>"o",
            "п"=>"p",
            "р"=>"r",
            "с"=>"s",
            "т"=>"t",
            "у"=>"u",
            "ф"=>"f",
            "х"=>"h",
            "ц"=>"c",
            "ч"=>"ch",
            "ш"=>"sh",
            "щ"=>"shh",
            "ы"=>"i",
            "э"=>"e",
            "ю"=>"yu",
            "я"=>"ya",
            "А"=>"A",
            "Б"=>"B",
            "В"=>"V",
            "Г"=>"G",
            "Д"=>"D",
            "Е"=>"E",
            "Ё"=>"Yo",
            "Ж"=>"Zh",
            "З"=>"Z",
            "И"=>"I",
            "Й"=>"J",
            "К"=>"K",
            "Л"=>"L",
            "М"=>"M",
            "Н"=>"N",
            "О"=>"O",
            "П"=>"P",
            "Р"=>"R",
            "С"=>"S",
            "Т"=>"T",
            "У"=>"U",
            "Ф"=>"F",
            "Х"=>"H",
            "Ц"=>"C",
            "Ч"=>"Ch",
            "Ш"=>"Sh",
            "Щ"=>"Shh",
            "Ы"=>"I",
            "Э"=>"E",
            "Ю"=>"Yu",
            "Я"=>"Ya",
            "ь"=>"",
            "Ь"=>"",
            "ъ"=>"",
            "Ъ"=>"",
            " "=>"-",
            "№"=>"N",
            "+"=>"-",
            ":"=>"-",
            ";"=>"-",
            "!"=>"-",
            "?"=>"-",
            "&"=>"and",
            "\'" =>"",
            "Ä" => "Ae",
            "Ö" => "Oe",
            "Ü" => "Ue",
            "ä" => "ae",
            "ö" => "oe",
            "ü" => "ue",
            "è" => "e",
            "é" => "e",
            "à" => "a",
            "ß" => "ss"),

    'mime_magic' => "",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'thumbsDir' => "thumbs",

    'jpegQuality' => 90,

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',

    // THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION CONFIGURATION
    '_check4htaccess' => false,
    '_tinyMCEPath' => MODX_BASE_URL . "assets/plugins/tinymce/jscripts/tiny_mce",

    '_sessionVar' => &$_SESSION['KCFINDER'],
    //'_sessionLifetime' => 30,
    //'_sessionDir' => "/full/directory/path",

    //'_sessionDomain' => ".mysite.com",
    //'_sessionPath' => "/my/path",
);

?>
