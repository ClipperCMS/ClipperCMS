<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

// Get the manager $_lang array
// ----------------------------

// Check that $manager_language is valid
if(!isset($manager_language) || !ctype_alnum(str_replace('_', '', str_replace('-', '', $manager_language))) || !file_exists(MODX_MANAGER_PATH.'includes/lang/'.$manager_language.'.inc.php')) {
    $manager_language = 'english';
}

// Get the English fallbacks
$_lang = array();
require('lang/english.inc.php');
$length_eng_lang = count($_lang);

// Get non-English text
if ($manager_language != 'english') {
    require('lang/'.$manager_language.'.inc.php');
}
// Convert $_lang to modx_charset
$modx->convertLanguageArray($_lang, 'lang/english.inc.php', '_lang');

// Make manager charset match site charset to avoid editing issues
$modx_manager_charset = $modx->config['modx_charset'];


// include the country list language file
function get_manager_countries($manager_language) {
    global $modx;

	// Check that $manager_language is valid (should be superfluous)
	if (empty($manager_language) || !is_string($manager_language) || !ctype_alnum(str_replace('_', '', str_replace('-', '', $manager_language))) || !file_exists(MODX_MANAGER_PATH.'includes/lang/country/'.$manager_language.'_country.inc.php')) {
		$manager_language = 'english';
	}
	$_country_lang = array();
	require('lang/country/english_country.inc.php');
	if ($manager_language != 'english') {
		require('lang/country/'.$manager_language.'_country.inc.php');
	}
	$modx->convertLanguageArray($_country_lang, 'lang/country/english_country.inc.php', '_country_lang');
	
	return $_country_lang;
}

