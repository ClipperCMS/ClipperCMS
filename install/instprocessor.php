<?php
global $moduleName;
global $moduleVersion;
global $moduleSQLBaseFile;
global $moduleSQLDataFile;

global $moduleChunks;
global $moduleTemplates;
global $moduleSnippets;
global $modulePlugins;
global $moduleModules;
global $moduleTVs;

global $errors;

$create = false;

// set timout limit
@ set_time_limit(120); // used @ to prevent warning when using safe mode?

echo "<p>{$_lang['setup_database']}</p>\n";

$installMode= intval($_POST['installmode']);
$installData = $_POST['installdata'] == "1" ? 1 : 0;

$database_server = $_POST['databasehost'];
$database_user = $_SESSION['databaseloginname'];
$database_password = $_SESSION['databaseloginpassword'];
$database_collation = $_POST['database_collation'];
$database_charset = substr($database_collation, 0, strpos($database_collation, '_'));
$database_connection_charset = $_POST['database_connection_charset'];
$database_connection_method = $_POST['database_connection_method'];
$dbase = "`" . $_POST['database_name'] . "`";
$table_prefix = $_POST['tableprefix'];
$adminname = $_POST['cmsadmin'];
$adminemail = $_POST['cmsadminemail'];
$adminpass = $_POST['cmspassword'];
$managerlanguage = $_POST['managerlanguage'];

// set session name variable
if (!isset ($site_sessionname)) {
    $site_sessionname = 'SN' . uniqid('');
}

// get base path and url
$a = explode("install", str_replace("\\", "/", dirname($_SERVER["PHP_SELF"])));
if (count($a) > 1)
    array_pop($a);
$url = implode("install", $a);
reset($a);
$a = explode("install", str_replace("\\", "/", realpath(dirname(__FILE__))));
if (count($a) > 1)
    array_pop($a);
$pth = implode("install", $a);
unset ($a);
$base_url = $url . (substr($url, -1) != "/" ? "/" : "");
$base_path = $pth . (substr($pth, -1) != "/" ? "/" : "");

// connect to the database
echo "<p>". $_lang['setup_database_create_connection'];

if (! $install->db->test_connect($database_server, '', $database_user, $database_password)) {
    echo "<span class=\"notok\">".$_lang["setup_database_create_connection_failed"]."</span></p><p>".$_lang['setup_database_create_connection_failed_note']."</p>";
    return;
} else {
    echo "<span class=\"ok\">".$_lang['ok']."</span></p>";
}

// select database
echo "<p>".$_lang['setup_database_selection']. str_replace("`", "", $dbase) . "`: ";
if (! $install->db->test_connect($database_server, $dbase, $database_user, $database_password)) {
//  It is pointless to try to create database here - the name has been lost. Need to start again.
//    echo "<span class=\"notok\" style='color:#707070'>".$_lang['setup_database_selection_failed']."</span>".$_lang['setup_database_selection_failed_note']."</p>";
//    $create = true;
    echo "<span class=\"notok\" style='color:#707070'>".$_lang['setup_database_selection_failed']."</span></p>";
	$install->db->messageQuit($_lang["setup_database_creation_failed_note2"]);
//	return;
} else {
	$install->db->config['charset'] = $database_connection_charset;
	$install->db->config['connection_method'] = $database_connection_method;
	$install->db->connect($database_server, $dbase, $database_user, $database_password);
    echo "<span class=\"ok\">".$_lang['ok']."</span></p>";
}

/*
// try to create the database
if ($create) {
    echo "<p>".$_lang['setup_database_creation']. str_replace("`", "", $dbase) . "`: ";
    if (! $install->db->query("CREATE DATABASE $dbase DEFAULT CHARACTER SET $database_charset COLLATE $database_collation")) {
        echo "<span class=\"notok\">".$_lang['setup_database_creation_failed']."</span>".$_lang['setup_database_creation_failed_note']."</p>";
        $errors += 1;
?>
        <pre>
        database charset = <?php $database_charset ?>
        database collation = <?php $database_collation ?>
        </pre>
        <p><?php echo $_lang['setup_database_creation_failed_note2']?></p>
<?php

        return;
    } else {
        echo "<span class=\"ok\">".$_lang['ok']."</span></p>";
    }
}
*/
// check table prefix
if ($installMode == 0) {
    echo "<p>" . $_lang['checking_table_prefix'] . $table_prefix . "`: ";

	$prefix_used = $install->db->tables_present($_POST['tableprefix']);
	if ($prefix_used) {
        echo "<span class=\"notok\">" . $_lang['failed'] . "</span>" . $_lang['table_prefix_already_inuse'] . "</p>";
        $errors += 1;
        echo "<p>" . $_lang['table_prefix_already_inuse_note'] . "</p>";
        return;
    } else {
        echo "<span class=\"ok\">" . $_lang['ok'] . "</span></p>";
    }
}

if(!function_exists('parseProperties')) {
    // parses a resource property string and returns the result as an array
    // duplicate of method in documentParser class
    function parseProperties($propertyString) {
        $parameter= array ();
        if (!empty ($propertyString)) {
            $tmpParams= explode("&", $propertyString);
            for ($x= 0; $x < count($tmpParams); $x++) {
                if (strpos($tmpParams[$x], '=', 0)) {
                    $pTmp= explode("=", $tmpParams[$x]);
                    $pvTmp= explode(";", trim($pTmp[1]));
                    if ($pvTmp[1] == 'list' && $pvTmp[3] != "")
                        $parameter[trim($pTmp[0])]= $pvTmp[3]; //list default
                    else
                        if ($pvTmp[1] != 'list' && $pvTmp[2] != "")
                            $parameter[trim($pTmp[0])]= $pvTmp[2];
                }
            }
        }
        return $parameter;
    }
}

// check status of Inherit Parent Template plugin
$auto_template_logic = 'parent';

if ($installMode != 0) {
    $rs = $install->db->select('properties, disabled', "`{$table_prefix}site_plugins`", "name='Inherit Parent Template'");
    $row = $install->db->getRow($rs, 'num');
    if(!$row) {
        // not installed
        $auto_template_logic = 'system';
    } else {
        if($row[1] == 1) {
            // installed but disabled
            $auto_template_logic = 'system';
        } else {
            // installed, enabled .. see how it's configured
            $properties = parseProperties($row[0]);
            if(isset($properties['inheritTemplate'])) {
                if($properties['inheritTemplate'] == 'From First Sibling') {
                    $auto_template_logic = 'sibling';
                }
            }
        }
    }
}

// open db connection
$setupPath = realpath(dirname(__FILE__));
include "{$setupPath}/setup.info.php";
// include "{$setupPath}/sqlParser.class.php"; // moved to Install class

$install->mode = ($installMode < 1) ? "new" : "upd";
$install->prefix = $table_prefix;
$install->adminname = $adminname;
$install->adminemail = $adminemail;
$install->adminpass = $adminpass;
$install->managerlanguage = $managerlanguage;
$install->autoTemplateLogic = $auto_template_logic;
$install->ignoreDuplicateErrors = true;

if ($installMode != 1) {
	$install->table_options = 'ENGINE='.$_POST['tableengine'];
} else {
	$install->table_options = 'ENGINE='.$install->db->table_engine($table_prefix.'site_content');
}

$install->table_options .= ' CHARSET='.$database_connection_charset.($database_collation ? ' COLLATE='.$database_collation : '');

// install/update database
echo "<p>" . $_lang['setup_database_creating_tables'];
if ($moduleSQLBaseFile) {
    $install->process($moduleSQLBaseFile);

    // display database results
    if ($install->installFailed == true) {
        $errors += 1;
        echo "<span class=\"notok\"><b>" . $_lang['database_alerts'] . "</span></p>";
        echo "<p>" . $_lang['setup_couldnt_install'] . "</p>";
        echo "<p>" . $_lang['installation_error_occured'] . "<br /><br />";
        for ($i = 0; $i < count($install->mysqlErrors); $i++) {
            echo "<em>" . $install->mysqlErrors[$i]["error"] . "</em>" . $_lang['during_execution_of_sql'] . "<span class='mono'>" . strip_tags($install->mysqlErrors[$i]["sql"]) . "</span>.<hr />";
        }
        echo "</p>";
        echo "<p>" . $_lang['some_tables_not_updated'] . "</p>";
        return;
    } else {
        echo "<span class=\"ok\">".$_lang['ok']."</span></p>";
    }
}

// Add new columns for salted password hashing and set admin user
$rs_mu = $install->db->select('*', $dbase . '.' . $table_prefix . 'manager_users', '', '', '1');

if ($install->db->getRecordCount($rs_mu)) {
	$row_mu = $install->db->getRow($rs_mu);
	if (isset($row_mu['hashtype'])) {
		$mu_hashtype = true;
	} else {
		$mu_hashtype = $install->db->query('ALTER TABLE ' . $dbase . '.' . $table_prefix . 'manager_users 
		ADD COLUMN hashtype smallint NOT NULL DEFAULT 0 AFTER username');
	}

	if (isset($row_mu['salt'])) {
		$mu_salt = true;
	} else {
		$mu_salt = ($mu_hashtype && $install->db->query("ALTER TABLE 
		{$dbase}.{$table_prefix}manager_users 
		ADD COLUMN salt varchar(40) NOT NULL DEFAULT '' AFTER hashtype"));
	}

	if (!$mu_hashtype || !$mu_salt) {
		$errors += 1;
		echo '<span class="notok"><b>'.$_lang['database_alerts'].'</span></p>';
		echo '<p>'.$_lang['installation_error_occured'].'<br /><br /></p>';
		echo '<p>'.$_lang['some_tables_not_updated'].'</p>';
		echo '<p>'.$dbase.'.'.$table_prefix.'manager_users: columns hashtype and salt.</p>';
		return;
	}
}

if ($installMode == 0) {
	// Create admin user for new installations
	require_once('../manager/includes/hash.inc.php');

	$HashHandler = new HashHandler(CLIPPER_HASH_PREFERRED);
	$Hash = $HashHandler->generate($adminpass);
	$rs_hash = $install->db->query("REPLACE INTO {$dbase}.{$table_prefix}manager_users 
					(id, username, hashtype, salt, password)
					VALUES 
					(1, '$adminname', '" . (string)CLIPPER_HASH_PREFERRED . "', 
					'$Hash->salt', '$Hash->hash')"
					);
	if (!$rs_hash) {
		$errors += 1;
		echo '<span class="notok"><b>'.$_lang['database_alerts'].'</span></p>';
		echo '<p>'.$_lang['installation_error_occured'].'<br /><br /></p>';
		echo '<p>'.$_lang['some_tables_not_updated'].'</p>';
		echo '<p>'.$dbase.'.'.$table_prefix.'manager_users: admin user row not set.</p>';
		return;
	}
}

// Locales
$locale_string = '';
if ($_POST['locale_lc_all']) {
	$locale_string .= "setlocale(LC_ALL, \$locale_lc_all = '{$_POST['locale_lc_all']}');\n";
	if ($_POST['locale_lc_numeric']) {
		$locale_string .= "setlocale(LC_NUMERIC, \$locale_lc_numeric = '{$_POST['locale_lc_numeric']}');\n";
	}
}

// write the config.inc.php file if new installation
echo "<p>" . $_lang['writing_config_file'];
$configString = '<?php
/**
 * CMS Configuration file
 */
'.$locale_string.'
$database_type = \'mysql\';
$database_server = \'' . $database_server . '\';
$database_user = \'' . $install->db->escape($database_user) . '\';
$database_password = \'' . $install->db->escape($database_password) . '\';
$database_connection_charset = \'' . $database_connection_charset . '\';
$database_connection_method = \'' . $database_connection_method . '\';
$dbase = \'`' . str_replace("`", "", $dbase) . '`\';
$table_prefix = \'' . $table_prefix . '\';

if (version_compare(PHP_VERSION, \'5.3.0\') >= 0) {
	error_reporting(E_ALL & E_DEPRECATED & ~E_NOTICE);
} else {
	error_reporting(E_ALL & ~E_NOTICE);
}

$lastInstallTime = '.time().';

$site_sessionname = \'' . $site_sessionname . '\';
$https_port = \'443\';

// automatically assign base_path and base_url
if(empty($base_path)||empty($base_url)||$_REQUEST[\'base_path\']||$_REQUEST[\'base_url\']) {
    $sapi= \'undefined\';
    if (!strstr($_SERVER[\'PHP_SELF\'], $_SERVER[\'SCRIPT_NAME\']) && ($sapi= @ php_sapi_name()) == \'cgi\') {
        $script_name= $_SERVER[\'PHP_SELF\'];
    } else {
        $script_name= $_SERVER[\'SCRIPT_NAME\'];
    }
    $a= explode("/manager", str_replace("\\\\", "/", dirname($script_name)));
    if (count($a) > 1)
        array_pop($a);
    $url= implode("manager", $a);
    reset($a);
    $a= explode("manager", str_replace("\\\\", "/", dirname(__FILE__)));
    if (count($a) > 1)
        array_pop($a);
    $pth= implode("manager", $a);
    unset ($a);
    $base_url= $url . (substr($url, -1) != "/" ? "/" : "");
    $base_path= $pth . (substr($pth, -1) != "/" && substr($pth, -1) != "\\\\" ? "/" : "");
}
// assign site_url
$site_url= ((isset ($_SERVER[\'HTTPS\']) && strtolower($_SERVER[\'HTTPS\']) == \'on\') || $_SERVER[\'SERVER_PORT\'] == $https_port) ? \'https://\' : \'http://\';
$site_url .= $_SERVER[\'HTTP_HOST\'];
if ($_SERVER[\'SERVER_PORT\'] != 80)
    $site_url= str_replace(\':\' . $_SERVER[\'SERVER_PORT\'], \'\', $site_url); // remove port from HTTP_HOST  
$site_url .= ($_SERVER[\'SERVER_PORT\'] == 80 || (isset ($_SERVER[\'HTTPS\']) && strtolower($_SERVER[\'HTTPS\']) == \'on\') || $_SERVER[\'SERVER_PORT\'] == $https_port) ? \'\' : \':\' . $_SERVER[\'SERVER_PORT\'];
$site_url .= $base_url;

if (!defined(\'MODX_BASE_PATH\')) define(\'MODX_BASE_PATH\', $base_path);
if (!defined(\'MODX_BASE_URL\')) define(\'MODX_BASE_URL\', $base_url);
if (!defined(\'MODX_SITE_URL\')) define(\'MODX_SITE_URL\', $site_url);
if (!defined(\'MODX_MANAGER_PATH\')) define(\'MODX_MANAGER_PATH\', $base_path.\'manager/\');
if (!defined(\'MODX_MANAGER_URL\')) define(\'MODX_MANAGER_URL\', $site_url.\'manager/\');

// start cms session
if(!function_exists(\'startCMSSession\')) {
    function startCMSSession(){
        global $site_sessionname;
        session_name($site_sessionname);
        session_start();
        $cookieExpiration= 0;
        if (isset ($_SESSION[\'mgrValidated\']) || isset ($_SESSION[\'webValidated\'])) {
            $contextKey= isset ($_SESSION[\'mgrValidated\']) ? \'mgr\' : \'web\';
            if (isset ($_SESSION[\'modx.\' . $contextKey . \'.session.cookie.lifetime\']) && is_numeric($_SESSION[\'modx.\' . $contextKey . \'.session.cookie.lifetime\'])) {
                $cookieLifetime= intval($_SESSION[\'modx.\' . $contextKey . \'.session.cookie.lifetime\']);
            }
            if ($cookieLifetime) {
                $cookieExpiration= time() + $cookieLifetime;
            }
            if (!isset($_SESSION[\'modx.session.created.time\'])) {
              $_SESSION[\'modx.session.created.time\'] = time();
            }
        }
        setcookie(session_name(), session_id(), $cookieExpiration, MODX_BASE_URL);
    }
}';

$configString .= "\n?>";
$filename = '../manager/includes/config.inc.php';
$configFileFailed = false;

if (@ !$handle = fopen($filename, 'w')) {
    $configFileFailed = true;
}

// write $somecontent to our opened file.
if (@ fwrite($handle, $configString) === FALSE) {
    $configFileFailed = true;
}
@ fclose($handle);

// try to chmod the config file go-rwx (for suexeced php)
$chmodSuccess = @chmod($filename, 0600);

if ($configFileFailed == true) {
    echo "<span class=\"notok\">" . $_lang['failed'] . "</span></p>";
    $errors += 1;
?>
    <p><?php echo $_lang['cant_write_config_file']?><span class="mono">manager/includes/config.inc.php</span></p>
    <textarea style="width:400px; height:160px;">
    <?php echo $configString; ?>
    </textarea>
    <p><?php echo $_lang['cant_write_config_file_note']?></p>
<?php
    return;
} else {
    echo "<span class=\"ok\">" . $_lang['ok'] . "</span></p>";
}

// generate new site_id
if ($installMode == 0) {
    $siteid = uniqid('');
    $install->db->query("REPLACE INTO $dbase.`{$table_prefix}system_settings` (setting_name,setting_value) VALUES ('site_id','$siteid')");
} else {
    // update site_id if missing
    $ds = $install->db->select("setting_name, setting_value", "`{$table_prefix}system_settings`", 
	"setting_name='site_id'");

    if ($ds) {
        $r = $install->db->getRow($ds);
        $siteid = $r['setting_value'];

        if ($siteid == '' || $siteid = 'MzGeQ2faT4Dw06+U49x3') {
            $siteid = uniqid('');
            $install->db->query("REPLACE INTO $dbase.`{$table_prefix}system_settings` 
			(setting_name,setting_value) 
			VALUES('site_id','$siteid')");
        }
    }
}

// Install Templates
if (isset ($_POST['template']) || $installData) {
    echo "<h3>" . $_lang['templates'] . ":</h3> ";
    $selTemplates = $_POST['template'];
    foreach ($moduleTemplates as $k=>$moduleTemplate) {
        $installSample = in_array('sample', $moduleTemplate[6]) && $installData == 1;
        if(in_array($k, $selTemplates) || $installSample) {
            $name = $install->db->escape($moduleTemplate[0]);
            $desc = $install->db->escape($moduleTemplate[1]);
            $category = $install->db->escape($moduleTemplate[4]);
            $locked = $install->db->escape($moduleTemplate[5]);
            $filecontent = $moduleTemplate[3];
            if (!file_exists($filecontent)) {
                echo "<p>&nbsp;&nbsp;$name: <span class=\"notok\">" . $_lang['unable_install_template'] . " '$filecontent' " . $_lang['not_found'] . ".</span></p>";
            } else {
                // Create the category if it does not already exist
                $category_id = $install->getCreateDbCategory($category);

                // Strip the first comment up top
                $template = preg_replace("/^.*?\/\*\*.*?\*\/\s+/s", '', file_get_contents($filecontent), 1);
                $template = $install->db->escape($template);

                // See if the template already exists
                $rs = $install->db->select("*", "`{$table_prefix}site_templates`", "templatename='$name'");

                if ($install->db->getRecordCount($rs) > 0) {
                    $install->db->update("content='$template', description='$desc', category=$category_id, locked='$locked'",
                    "`{$table_prefix}site_templates`", "templatename='$name'");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['upgraded'] . "</span></p>";
                } else {
                    $install->db->insert("(templatename,description,content,category,locked) 
                        VALUES('$name','$desc','$template',$category_id,'$locked')",
                        "`{$table_prefix}site_templates`");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['installed'] . "</span></p>";
                }
            }
        }
    }
}

// Install Template Variables
if (isset ($_POST['tv']) || $installData) {
    echo "<h3>" . $_lang['tvs'] . ":</h3> ";
    $selTVs = $_POST['tv'];
    foreach ($moduleTVs as $k=>$moduleTV) {
        $installSample = in_array('sample', $moduleTV[12]) && $installData == 1;
        if(in_array($k, $selTVs) || $installSample) {
            $name = $install->db->escape($moduleTV[0]);
            $caption = $install->db->escape($moduleTV[1]);
            $desc = $install->db->escape($moduleTV[2]);
            $input_type = $install->db->escape($moduleTV[3]);
            $input_options = $install->db->escape($moduleTV[4]);
            $input_default = $install->db->escape($moduleTV[5]);
            $output_widget = $install->db->escape($moduleTV[6]);
            $output_widget_params = $install->db->escape($moduleTV[7]);
            $filecontent = $moduleTV[8];
            $assignments = $moduleTV[9];
            $category = $install->db->escape($moduleTV[10]);
            $locked = $install->db->escape($moduleTV[11]);

            // Create the category if it does not already exist
            $category_id = $install->getCreateDbCategory($category);
            
            $rs = $install->db->select("*", "`{$table_prefix}site_tmplvars`",  "name='$name'");

            if ($install->db->getRecordCount($rs) > 0) {
//                $insert = true; // not used
                while($row = $install->db->getRow($rs)) {
                    $install->db->update("type='$input_type', caption='$caption', description='$desc', category=$category_id, locked='$locked', elements='$input_options', display='$output_widget', display_params='$output_widget_params', default_text='$input_default'", 
                    "`{$table_prefix}site_tmplvars`", 
                    "id={$row['id']};");
                }
                echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['upgraded'] . "</span></p>";
            } else {
                $install->db->insert("(type,name,caption,description,category,locked,elements,display,display_params,default_text) 
                VALUES('$input_type','$name','$caption','$desc',$category_id,'$locked','$input_options','$output_widget','$output_widget_params','$input_default')", 
                "`{$table_prefix}site_tmplvars`");

                echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['installed'] . "</span></p>";
            }

            // add template assignments
            $assignments = explode(',', $assignments);

            if (count($assignments) > 0) {

                // remove existing tv -> template assignments
                $ds = $install->db->select("id", "`{$table_prefix}site_tmplvars`", "name='$name' AND description='$desc'");
                $row = $install->db->getRow($ds);
                $id = $row["id"];
                $install->db->delete("`{$table_prefix}site_tmplvar_templates`", "tmplvarid = $id");

                // add tv -> template assignments
                foreach ($assignments as $assignment) {
                    $template = $install->db->escape($assignment);
                    $ts = $install->db->select("id", "`{$table_prefix}site_templates`", "templatename='$template'");
                    if ($install->db->getRecordCount($ts) > 0) {
	                    if ($ds && $ts) {
	                        $tRow = $install->db->getRow($ts);
	                        $templateId = $tRow['id'];
	                        $install->db->insert("(tmplvarid, templateid) VALUES ($id, $templateId)", "`{$table_prefix}site_tmplvar_templates`");
	                   }
					}
                }
            }
        }
    }
}

// Install Chunks
if (isset ($_POST['chunk']) || $installData) {
    echo "<h3>" . $_lang['chunks'] . ":</h3> ";
    $selChunks = $_POST['chunk'];
    foreach ($moduleChunks as $k=>$moduleChunk) {
        $installSample = in_array('sample', $moduleChunk[5]) && $installData == 1;
        if(in_array($k, $selChunks) || $installSample) {

            $name = $install->db->escape($moduleChunk[0]);
            $desc = $install->db->escape($moduleChunk[1]);
            $category = $install->db->escape($moduleChunk[3]);
            $overwrite = $install->db->escape($moduleChunk[4]);
            $filecontent = $moduleChunk[2];

            if (!file_exists($filecontent))
                echo "<p>&nbsp;&nbsp;$name: <span class=\"notok\">" . $_lang['unable_install_chunk'] . " '$filecontent' " . $_lang['not_found'] . ".</span></p>";
            else {

                // Create the category if it does not already exist
                $category_id = $install->getCreateDbCategory($category);
                
                $chunk = preg_replace("/^.*?\/\*\*.*?\*\/\s+/s", '', file_get_contents($filecontent), 1);
                $chunk = $install->db->escape($chunk);
                $rs = $install->db->select("*", "`{$table_prefix}site_htmlsnippets`", "name='$name'");
                $count_original_name = $install->db->getRecordCount($rs);

                if($overwrite == 'false') {
                    $newname = $name . '-' . str_replace('.', '_', $modx_version);
                    $rs = $install->db->select("*", "`{$table_prefix}site_htmlsnippets`", "name='$newname'");
                    $count_new_name = $install->db->getRecordCount($rs);
                }

                if ($count_original_name > 0 && $overwrite == 'true') {
                    $install->db->update("snippet='$chunk', description='$desc', category=$category_id", 
                    "`{$table_prefix}site_htmlsnippets`", 
                    "name='$name'");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['upgraded'] . "</span></p>";
                } elseif($count_new_name == 0) {
                    if($count_original_name > 0 && $overwrite == 'false') {
                        $name = $newname;
                    }
                    $install->db->insert("(name,description,snippet,category) VALUES('$name','$desc','$chunk',$category_id)", 
                    "`{$table_prefix}site_htmlsnippets`");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['installed'] . "</span></p>";
                }
            }
        }
    }
}

// Install Modules
if (isset ($_POST['module']) || $installData) {
    echo "<h3>" . $_lang['modules'] . ":</h3> ";
    $selModules = $_POST['module'];
    foreach ($moduleModules as $k=>$moduleModule) {
        $installSample = in_array('sample', $moduleModule[7]) && $installData == 1;
        if(in_array($k, $selModules) || $installSample) {
            $name = $install->db->escape($moduleModule[0]);
            $desc = $install->db->escape($moduleModule[1]);
            $filecontent = $moduleModule[2];
            $properties = $install->db->escape($moduleModule[3]);
            $guid = $install->db->escape($moduleModule[4]);
            $shared = $install->db->escape($moduleModule[5]);
            $category = $install->db->escape($moduleModule[6]);
            if (!file_exists($filecontent))
                echo "<p>&nbsp;&nbsp;$name: <span class=\"notok\">" . $_lang['unable_install_module'] . " '$filecontent' " . $_lang['not_found'] . ".</span></p>";
            else {

                // Create the category if it does not already exist
                $category_id = $install->getCreateDbCategory($category);

                $module = end(preg_split("/(\/\/)?\s*\<\?php/", file_get_contents($filecontent), 2));
                // remove installer docblock
                $module = preg_replace("/^.*?\/\*\*.*?\*\/\s+/s", '', $module, 1);
                $module = $install->db->escape($module);

                $rs = $install->db->select("*", "`{$table_prefix}site_modules`", "name='$name'");

                if ($install->db->getRecordCount($rs) > 0) {
                    $row = $install->db->getRow($rs);
                    $props = propUpdate($properties,$row['properties']);

                    $install->db->update("modulecode='$module', description='$desc', properties='$props', enable_sharedparams='$shared'", "`{$table_prefix}site_modules`", "'$name'");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['upgraded'] . "</span></p>";
                } else {
                   $install->db->insert("(name,description,modulecode,properties,guid,enable_sharedparams,category) 
                   VALUES('$name','$desc','$module','$properties','$guid','$shared', $category_id)", 
                   "`{$table_prefix}site_modules`");

                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['installed'] . "</span></p>";
                }
            }
        }
    }
}

// Install Plugins
if (isset ($_POST['plugin']) || $installData) {
    echo "<h3>" . $_lang['plugins'] . ":</h3> ";
    $selPlugs = $_POST['plugin'];
    foreach ($modulePlugins as $k=>$modulePlugin) {
        $installSample = in_array('sample', $modulePlugin[8]) && $installData == 1;
        if(in_array($k, $selPlugs) || $installSample) {
            $name = $install->db->escape($modulePlugin[0]);
            $desc = $install->db->escape($modulePlugin[1]);
            $filecontent = $modulePlugin[2];
            $properties = $install->db->escape($modulePlugin[3]);
            $events = explode(",", $modulePlugin[4]);
            $guid = $install->db->escape($modulePlugin[5]);
            $category = $install->db->escape($modulePlugin[6]);
            $leg_names = '';
            if(array_key_exists(7, $modulePlugin)) {
                // parse comma-separated legacy names and prepare them for sql IN clause
                $leg_names = "'" . implode("','", preg_split('/\s*,\s*/', $install->db->escape($modulePlugin[7]))) . "'";
            }
            if (!file_exists($filecontent)) {
                echo "<p>&nbsp;&nbsp;$name: <span class=\"notok\">" . $_lang['unable_install_plugin'] . " '$filecontent' " . $_lang['not_found'] . ".</span></p>";
			}
            else {
                // disable legacy versions based on legacy_names provided
                if(!empty($leg_names)) {
                    $install->db->update("disabled=1", "`{$table_prefix}site_plugins`", "name IN ($leg_names)");
                }

                // Create the category if it does not already exist
                $category_id = $install->getCreateDbCategory($category);

                $plugin = end(preg_split("/(\/\/)?\s*\<\?php/", file_get_contents($filecontent), 2));
                // remove installer docblock
                $plugin = preg_replace("/^.*?\/\*\*.*?\*\/\s+/s", '', $plugin, 1);
                $plugin = $install->db->escape($plugin);

                $rs = $install->db->select("*", "`{$table_prefix}site_plugins`", "name='$name'");

                if ($install->db->getRecordCount($rs) > 0) {
                    $insert = true;
                    while($row = $install->db->getRow($rs)) {
                        $props = propUpdate($properties,$row['properties']);
                        if($row['description'] == $desc){
	                         $install->db->update("plugincode='$plugin', description='$desc', properties='$props'", 
							"`{$table_prefix}site_plugins`", "id={$row['id']};");	
	                         $insert = false;
	                     } else {
                            $install->db->update("disabled=1", "`{$table_prefix}site_plugins`", "id={$row['id']}");
                        }
                    }

                    if($insert === true) {
                        $install->db->insert("(name,description,plugincode,properties,moduleguid,disabled,category) 
						VALUES('$name','$desc','$plugin','$properties','$guid','0',$category_id)", 
						"`{$table_prefix}site_plugins`");
                    }
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['upgraded'] . "</span></p>";
                } else {
                    $install->db->insert("(name,description,plugincode,properties,moduleguid,category) 
					VALUES('$name','$desc','$plugin','$properties','$guid',$category_id)", 
					"`{$table_prefix}site_plugins`");

                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['installed'] . "</span></p>";
                }
                // add system events
                if (count($events) > 0) {
                    $ds = $install->db->select("id", "`{$table_prefix}site_plugins`", "name='$name' AND description='$desc'");
                    if ($install->db->getRecordCount($ds) > 0) {
                        $row = $install->db->getRow($ds);
                        $id = $row["id"];
                        // remove existing events
                        $install->db->delete("`{$table_prefix}site_plugin_events`", "pluginid='$id'");
                        // add new events
                        $install->db->query("INSERT INTO `{$table_prefix}site_plugin_events` 
                        (pluginid, evtid) SELECT '$id' as 'pluginid', se.id as 'evtid' 
                        FROM `{$table_prefix}system_eventnames` se 
                        WHERE name IN ('" . implode("','", $events) . "')");
                    }
                }
            }
        }
    }
}

// Install Snippets
if (isset ($_POST['snippet']) || $installData) {
    echo "<h3>" . $_lang['snippets'] . ":</h3> ";
    $selSnips = $_POST['snippet'];
    foreach ($moduleSnippets as $k=>$moduleSnippet) {
        $installSample = in_array('sample', $moduleSnippet[5]) && $installData == 1;
        if(in_array($k, $selSnips) || $installSample) {
            $name = $install->db->escape($moduleSnippet[0]);
            $desc = $install->db->escape($moduleSnippet[1]);
            $filecontent = $moduleSnippet[2];
            $properties = $install->db->escape($moduleSnippet[3]);
            $category = $install->db->escape($moduleSnippet[4]);
            if (!file_exists($filecontent))
                echo "<p>&nbsp;&nbsp;$name: <span class=\"notok\">" . $_lang['unable_install_snippet'] . " '$filecontent' " . $_lang['not_found'] . ".</span></p>";
            else {

                // Create the category if it does not already exist
                $category_id = $install->getCreateDbCategory($category);

                $snippet = end(preg_split("/(\/\/)?\s*\<\?php/", file_get_contents($filecontent)));
                // remove installer docblock
                $snippet = preg_replace("/^.*?\/\*\*.*?\*\/\s+/s", '', $snippet, 1);
                $snippet = $install->db->escape($snippet);
                $rs = $install->db->select("*", "`{$table_prefix}site_snippets`",  "name='$name'");
                if ($install->db->getRecordCount($rs) > 0) {
                    $row = $install->db->getRow($rs);
                    $props = propUpdate($properties,$row['properties']);
                    $install->db->update("snippet='$snippet', description='$desc', properties='$props'", 
                    "`{$table_prefix}site_snippets`", "name='$name'");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['upgraded'] . "</span></p>";
                } else {
                    $install->db->insert("(name,description,snippet,properties,category) 
                    VALUES('$name','$desc','$snippet','$properties',$category_id)",
                    "`{$table_prefix}site_snippets`");
                    echo "<p>&nbsp;&nbsp;$name: <span class=\"ok\">" . $_lang['installed'] . "</span></p>";
                }
            }
        }
    }
}

// install data
if ($installData && $moduleSQLDataFile) {
    echo "<p>" . $_lang['installing_demo_site'];
    $install->process($moduleSQLDataFile);
    // display database results
    if ($install->installFailed == true) {
        $errors += 1;
        echo "<span class=\"notok\"><b>" . $_lang['database_alerts'] . "</span></p>";
        echo "<p>" . $_lang['setup_couldnt_install'] . "</p>";
        echo "<p>" . $_lang['installation_error_occured'] . "<br /><br />";
        for ($i = 0; $i < count($install->mysqlErrors); $i++) {
            echo "<em>" . $install->mysqlErrors[$i]["error"] . "</em>" . $_lang['during_execution_of_sql'] . "<span class='mono'>" . strip_tags($install->mysqlErrors[$i]["sql"]) . "</span>.<hr />";
        }
        echo "</p>";
        echo "<p>" . $_lang['some_tables_not_updated'] . "</p>";
        return;
    } else {
        echo "<span class=\"ok\">".$_lang['ok']."</span></p>";
    }
}

// call back function
if ($callBackFnc != "")
    $callBackFnc($install);

// Setup the MODx API -- needed for the cache processor
define('MODX_API_MODE', true);
define('MODX_BASE_PATH', $base_path);
$database_type = 'mysql';
// initiate a new document parser
include_once('../manager/includes/document.parser.class.inc.php');
$modx = new DocumentParser;
$modx->db->connect();
// always empty cache after install
include_once "../manager/processors/cache_sync.class.processor.php";
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache

// try to chmod the cache go-rwx (for suexeced php)
$chmodSuccess = @chmod('../assets/cache/siteCache.idx.php', 0600);
$chmodSuccess = @chmod('../assets/cache/sitePublishing.idx.php', 0600);

// remove any locks on the manager functions so initial manager login is not blocked
$install->db->query("TRUNCATE TABLE `{$table_prefix}active_users`");

// close db connection
$install->db->disconnect();

// andrazk 20070416 - release manager access
if (file_exists('../assets/cache/installProc.inc.php')) {
    @chmod('../assets/cache/installProc.inc.php', 0755);
    unlink('../assets/cache/installProc.inc.php');
}

// setup completed!
echo "<p><b>" . $_lang['installation_successful'] . "</b></p>";
echo "<p>" . $_lang['to_log_into_content_manager'] . "</p>";
if ($installMode == 0) {
	
    echo "<p><img src=\"img/ico_info.png\" width=\"40\" height=\"42\" align=\"left\" style=\"margin-right:10px;\" />" . $_lang['installation_note'] . "</p>";
} else {
    echo "<p><img src=\"img/ico_info.png\" width=\"40\" height=\"42\" align=\"left\" style=\"margin-right:10px;\" />" . $_lang['upgrade_note'] . "</p>";
}

// Property Update function
function propUpdate($new,$old){
    // Split properties up into arrays
    $returnArr = array();
    $newArr = explode("&",$new);
    $oldArr = explode("&",$old);

    foreach ($newArr as $k => $v) {
        if(!empty($v)){
            $tempArr = explode("=",trim($v));
            $returnArr[$tempArr[0]] = $tempArr[1];
        }
    }
    foreach ($oldArr as $k => $v) {
        if(!empty($v)){
            $tempArr = explode("=",trim($v));
            $returnArr[$tempArr[0]] = $tempArr[1];
        }
    }

    // Make unique array
    $returnArr = array_unique($returnArr);

    // Build new string for new properties value
    foreach ($returnArr as $k => $v) {
        $return .= "&$k=$v ";
    }

    return $return;
}
