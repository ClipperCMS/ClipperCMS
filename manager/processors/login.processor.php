<?php
//
// WARNING: This file is accessed directly, not via manager/index.php
//

require_once(strtr(realpath(dirname(__FILE__)), '\\', '/').'/../includes/protect.inc.php');

set_include_path(get_include_path() . PATH_SEPARATOR . "../includes/");

define("IN_MANAGER_MODE", "true");  // we use this to make sure files are accessed through
                                    // the manager instead of seperately.
                                    
// include the database configuration file and the DBAPI
require_once('config.inc.php');

// start session
startCMSSession();

// initiate the content manager class
require_once('document.parser.class.inc.php');
$modx = new DocumentParser;
$modx->loadExtension('DBAPI');
$modx->db->connect();
$modx->loadExtension('ManagerAPI');
$modx->getSettings();
$etomite = &$modx; // for backward compatibility

// include version info
include_once "version.inc.php";

// include the logger
include_once "log.class.inc.php";

// include the hashing classes
require('hash.inc.php');

// Initialize System Alert Message Queque
if (!isset($_SESSION['SystemAlertMsgQueque'])) $_SESSION['SystemAlertMsgQueque'] = array();
$SystemAlertMsgQueque = &$_SESSION['SystemAlertMsgQueque'];

// include_once the error handler
include_once "error.class.inc.php";
$e = new errorHandler;

$LoginData = array();
$LoginData['username'] = $modx->db->escape($_REQUEST['username']);
$LoginData['givenPassword'] = $modx->db->escape($_REQUEST['password']);
$LoginData['captcha_code'] = $_REQUEST['captcha_code']; //NOT USE
$LoginData['rememberme'] = $_REQUEST['rememberme'];
$LoginData['failed_allowed'] = $modx->config["failed_login_attempts"];

// invoke OnBeforeManagerLogin event
$modx->invokeEvent("OnBeforeManagerLogin",
    array(
        "username"      => $LoginData['username'],
        "userpassword"  => $LoginData['givenPassword'],
        "rememberme"    => $LoginData['rememberme']
    ));

$table = array(
    'manager_users' => $modx->getFullTableName('manager_users'),
    'user_attributes' => $modx->getFullTableName('user_attributes'),
    'user_settings' => $modx->getFullTableName("user_settings"),
    'user_roles' => $modx->getFullTableName("user_roles")
);

$rs = $modx->db->query("SELECT * FROM {$table['manager_users']} as mu,{$table['user_attributes']} as ua  WHERE BINARY mu.username = '".$LoginData['username']."' AND ua.internalKey=mu.id;");
$limit = $modx->db->getRecordCount($rs);

if($limit==0 || $limit>1) {
    jsAlert($e->errors[900]);
    return;
}
/*
 * internalKey, hashtype, salt, password=hash, failedlogincount=failedlogins, blocked,
 * blockeduntil=blockeduntildate, blockedafter=blockedafterdate, sessionid=registeredsessionid,
 * role, lastlogin, logincount=nrlogins, fullname, email
 */
$userData = $modx->db->getRow($rs);
$registeredsessionid    = $userData['sessionid']; //NOT USE

// get the user settings from the database
$rs = $modx->db->query("SELECT setting_name, setting_value FROM {$table['user_settings']} WHERE user='".$userData['internalKey']."' AND setting_value!=''");
$userSettings = array();
while ($row = $modx->db->getRow($rs)) {
    $userSettings[$row['setting_name']] = $row['setting_value'];
}
// blocked due to number of login errors.
$failed_allowed = isset($userSettings['failed_allowed']) ? $userSettings['failed_allowed'] : $LoginData['failed_allowed'];

$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

$date = getdate();
$day = $date['wday']+1;

$jsAlert = '';
$sessionDestroy = false;
switch(true){
    case ($userData['failedlogincount']>=$tmp && $userData['blockeduntil']>time()):{
        $sessionDestroy = true;
        $jsAlert = $e->errors[902];
        break;
    }
    case ($userData['blocked']=="1"):{ // this user has been blocked by an admin, so no way he's loggin in!
        $sessionDestroy = true;
        $jsAlert = $e->errors[903];
        break;
    }
    case ($userData['blockeduntil']>time()):{ // blockuntil: this user has a block until date
        $sessionDestroy = true;
        $jsAlert = "You are blocked and cannot log in! Please try again later.";
        break;
    }
    case ($userData['blockedafter']>0 && $userData['blockedafter']<time()):{ // blockafter: this user has a block after date
        $sessionDestroy = true;
        $jsAlert = "You are blocked and cannot log in! Please try again later.";
        break;
    }
    case (($hostname != $_SERVER['REMOTE_ADDR']) && (gethostbyname($hostname) != $_SERVER['REMOTE_ADDR'])):{ // allowed ip
        $jsAlert = "Your hostname doesn't point back to your IP!";
        break;
    }
    case (!empty($userSettings['allowed_ip']) && !in_array($_SERVER['REMOTE_ADDR'], explode(',',str_replace(' ','',$userSettings['allowed_ip'])))):{
        $jsAlert = "You are not allowed to login from this location.";
        break;
    }
    case (!empty($userSettings['allowed_days']) && !in_array($day, explode(",", $userSettings['allowed_days']))):{ // allowed days
        $jsAlert = "You are not allowed to login at this time. Please try again later.";
        break;
    }
}

// blocked due to number of login errors, but get to try again
if($userData['failedlogincount']>=$failed_allowed && $userData['blockeduntil']<time()) {
    $rs = $modx->db->query("UPDATE {$table['user_attributes']} SET failedlogincount='0', blockeduntil='".(time()-1)."' where internalKey=".$userData['internalKey']);
}

if(!empty($jsAlert)){
    if($sessionDestroy){
        @session_destroy();
        session_unset();
    }
    jsAlert($jsAlert);
    return;
}

// invoke OnManagerAuthentication event
$rt = $modx->invokeEvent("OnManagerAuthentication",
    array(
        "userid"        => $userData['internalKey'],
        "username"      => $LoginData['username'],
        "userpassword"  => $LoginData['givenPassword'],
        "savedpassword" => $userData['password'],
        "rememberme"    => $LoginData['rememberme']
    )
);

$hashtype = isset($userSettings['hashtype']) ? $userSettings['hashtype'] : $userData['hashtype'];
$newloginerror = false;
// check if plugin authenticated the user
if (!$rt||(is_array($rt) && !in_array(TRUE,$rt))) {
    // check user password - local authentication
    $HashHandler = new HashHandler($hashtype, $modx);
    if(!$HashHandler->check($LoginData['givenPassword'], $userData['salt'], $userData['password'])) {
            jsAlert($e->errors[901]);
            $newloginerror = true;
    }
}

if($newloginerror) {
	//increment the failed login counter
    $userData['failedlogincount'] += 1;
    $modx->db->query("UPDATE {$table['user_attributes']} SET failedlogincount='".$userData['failedlogincount']."' where internalKey=".$userData['internalKey']);
    if($userData['failedlogincount']>=$failed_allowed) {
		//block user for too many fail attempts
        $modx->db->query("UPDATE {$table['user_attributes']} SET blockeduntil='".(time()+($blocked_minutes*60))."' where internalKey=".$userData['internalKey']);
    } else {
		//sleep to help prevent brute force attacks
        $sleep = (int)$userData['failedlogincount']/2;
        if($sleep>5) $sleep = 5;
        sleep($sleep);
    }
	@session_destroy();
	session_unset();
    return;
}

$currentsessionid = session_id();

$_SESSION['usertype'] = 'manager'; // user is a backend user

// get permissions
$_SESSION['mgrShortname'] = $LoginData['username'];
$_SESSION['mgrFullname'] = $userData['fullname'];
$_SESSION['mgrEmail'] = $userData['email'];
$_SESSION['mgrValidated'] = 1;
$_SESSION['mgrHashtype'] = $hashtype;
$_SESSION['mgrInternalKey'] = $userData['internalKey'];
$_SESSION['mgrFailedlogins'] = $userData['failedlogincount'];
$_SESSION['mgrLastlogin'] = $userData['lastlogin'];
$_SESSION['mgrLogincount'] = $userData['logincount']; // login count
$_SESSION['mgrRole'] = $userData['role'];
$rs = $modx->db->query("SELECT * FROM {$table['user_roles']} WHERE id=".$userData['role']);
$_SESSION['mgrPermissions'] = $modx->db->getRow($rs);

// successful login so reset fail count and update key values
if(isset($_SESSION['mgrValidated'])) {
    $modx->db->query("update {$table['user_attributes']} SET failedlogincount=0, logincount=logincount+1, lastlogin=thislogin, thislogin=".time().", sessionid='{$currentsessionid}' where internalKey=".$userData['internalKey']);
}

// get user's document groups
$dg = array();
$rs = $modx->db->query("SELECT uga.documentgroup
    FROM {$modx->getFullTableName('member_groups')} ug
    INNER JOIN {$modx->getFullTableName('membergroup_access')} uga ON uga.membergroup=ug.user_group
    WHERE ug.member =".$userData['internalKey']
);
while ($row = $modx->db->getRow($rs, 'num')) $dg[]=$row[0];
$_SESSION['mgrDocgroups'] = empty($dg) ? "" : $dg;

if($LoginData['rememberme'] == '1') {
    $_SESSION['modx.mgr.session.cookie.lifetime']= intval($modx->config['session.cookie.lifetime']);
	
	// Set a cookie separate from the session cookie with the username in it. 
	// Are we using secure connection? If so, make sure the cookie is secure
	global $https_port;
	
	$secure = (  (isset ($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || $_SERVER['SERVER_PORT'] == $https_port);
	if ( version_compare(PHP_VERSION, '5.2', '<') ) {
		setcookie('modx_remember_manager', $_SESSION['mgrShortname'], time()+60*60*24*365, MODX_BASE_URL, '; HttpOnly' , $secure );
	} else {
		setcookie('modx_remember_manager', $_SESSION['mgrShortname'], time()+60*60*24*365, MODX_BASE_URL, NULL, $secure, true);
	}
} else {
    $_SESSION['modx.mgr.session.cookie.lifetime']= 0;
	
	// Remove the Remember Me cookie
	setcookie ('modx_remember_manager', "", time() - 3600, MODX_BASE_URL);
}

$log = new logHandler;
$log->initAndWriteLog("Logged in", $modx->getLoginUserID(), $_SESSION['mgrShortname'], "58", "-", CMS_NAME);

// invoke OnManagerLogin event
$modx->invokeEvent("OnManagerLogin",
    array(
        "userid"        => $userData['internalKey'],
        "username"      => $LoginData['username'],
        "userpassword"  => $LoginData['givenPassword'],
        "rememberme"    => $LoginData['rememberme']
     ));

// check if we should redirect user to a web page

$id = isset($userSettings['manager_login_startup']) ? (int)$userSettings['manager_login_startup'] : 0;
$header = 'Location: ';
$header .= ($id>0) ? $modx->makeUrl($id,'','','full') : $modx->config['site_url'].'manager/';

if(isset($_POST['ajax']) && $_POST['ajax'] == 1) echo $header;
else header($header);

// show javascript alert
function jsAlert($msg){
	global $modx;
    echo (isset($_POST['ajax']) && $_POST['ajax'] == 1) ? $msg."\n" : "<script>window.setTimeout(\"alert('".addslashes($modx->db->escape($msg))."')\",10);history.go(-1)</script>";
}
?>
