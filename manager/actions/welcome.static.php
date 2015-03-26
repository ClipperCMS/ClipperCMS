<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

unset($_SESSION['itemname']); // clear this, because it's only set for logging purposes

$script = <<<JS
    <script>
    function hideConfigCheckWarning(key){
        jQuery.ajax({
            url: 'index.php?a=118',
            type: 'post',
            data: { action: 'setsetting', key: '_hide_configcheck_'+key, value: '1' },
            success: function(){
                $('#' + key + '_warning_wrapper').parent().parent().hide();
            }
        });
    }
    </script>
JS;
$modx->regClientScript($script);

// set placeholders
$modx->setPlaceholder('theme',$manager_theme ? $manager_theme : '');
$modx->setPlaceholder('home', $_lang["home"]);
$modx->setPlaceholder('logo_slogan',$_lang["logo_slogan"]);
$modx->setPlaceholder('site_name',$site_name);
$modx->setPlaceholder('welcome_title',$_lang['welcome_title']);
$modx->setPlaceholder('cms_version_info', CMS_NAME.' '.CMS_RELEASE_VERSION.' '.CMS_RELEASE_NAME);

// setup icons
if($modx->hasPermission('new_user')||$modx->hasPermission('edit_user')) {
    $icon = '<a class="hometblink" href="index.php?a=75"><img src="'.$_style['icons_security_large'].'" alt="'.$_lang['user_management_title'].'" /><br />'.$_lang['security'].'</a>';
    $modx->setPlaceholder('SecurityIcon',$icon);
}
if($modx->hasPermission('new_web_user')||$modx->hasPermission('edit_web_user')) {
    $icon = '<a class="hometblink" href="index.php?a=99"><img src="'.$_style['icons_webusers_large'].'" alt="'.$_lang['web_user_management_title'].'" /><br />'.$_lang['web_users'].'</a>';
    $modx->setPlaceholder('WebUserIcon',$icon);
}
if($modx->hasPermission('new_module') || $modx->hasPermission('edit_module')) {
    $icon = '<a class="hometblink" href="index.php?a=106"><img src="'.$_style['icons_modules_large'].'" alt="'.$_lang['manage_modules'].'" /><br />'.$_lang['modules'].'</a>';
    $modx->setPlaceholder('ModulesIcon',$icon);
}
if($modx->hasPermission('new_template') || $modx->hasPermission('edit_template') || $modx->hasPermission('new_snippet') || $modx->hasPermission('edit_snippet') || $modx->hasPermission('new_plugin') || $modx->hasPermission('edit_plugin')) {
    $icon = '<a class="hometblink" href="index.php?a=76"><img src="'.$_style['icons_resources_large'].'" alt="'.$_lang['element_management'].'" /><br />'.$_lang['elements'].'</a>';
    $modx->setPlaceholder('ResourcesIcon',$icon);
}
if($modx->hasPermission('bk_manager')) {
    $icon = '<a class="hometblink" href="index.php?a=93"><img src="'.$_style['icons_backup_large'].'" alt="'.$_lang['bk_manager'].'" /><br />'.$_lang['backup'].'</a>';
    $modx->setPlaceholder('BackupIcon',$icon);
}

// do some config checks
if (($modx->config['warning_visibility'] == 0 && $_SESSION['mgrRole'] == 1) || $modx->config['warning_visibility'] == 1) {
    include_once "config_check.inc.php";
    $modx->setPlaceholder('settings_config',$_lang['settings_config']);
    $modx->setPlaceholder('configcheck_title',$_lang['configcheck_title']);
    if($config_check_results != $_lang['configcheck_ok']) {
        $modx->setPlaceholder('config_check_results',$config_check_results);
        $modx->setPlaceholder('config_display','block');
    }
    else {
        $modx->setPlaceholder('config_display','none');
    }
} else {
     $modx->setPlaceholder('config_display','none');
}

// include rss feeds for important forum topics
include_once "rss.inc.php";

// modx news
$modx->setPlaceholder('modx_news',$_lang["modx_news_tab"]);
$modx->setPlaceholder('modx_news_title',$_lang["modx_news_title"]);
$modx->setPlaceholder('modx_news_content',$feedData['modx_news_content']);

// security notices
$modx->setPlaceholder('modx_security_notices',$_lang["security_notices_tab"]);
$modx->setPlaceholder('modx_security_notices_title',$_lang["security_notices_title"]);
$modx->setPlaceholder('modx_security_notices_content',$feedData['modx_security_notices_content']);

// recent document info
$html = $_lang["activity_message"].'<br /><br /><ul>';
$sql = "SELECT id, pagetitle, description FROM $dbase.`".$table_prefix."site_content` WHERE $dbase.`".$table_prefix."site_content`.deleted=0 AND ($dbase.`".$table_prefix."site_content`.editedby=".$modx->getLoginUserID()." OR $dbase.`".$table_prefix."site_content`.createdby=".$modx->getLoginUserID().") ORDER BY editedon DESC LIMIT 10";
$rs = $modx->db->query($sql);
$limit = $modx->db->getRecordCount($rs);
if($limit<1) {
    $html .= '<li>'.$_lang['no_activity_message'].'</li>';
} else {
    for ($i = 0; $i < $limit; $i++) {
        $content = $modx->db->getRow($rs);
        if($i==0) {
            $syncid = $content['id'];
        }
        $html.='<li><span style="width: 40px; text-align:right;">'.$content['id'].'</span> - <span style="width: 200px;"><a href="index.php?a=3&amp;id='.$content['id'].'">'.$content['pagetitle'].'</a></span>'.($content['description']!='' ? ' - '.$content['description'] : '').'</li>';
    }
}
$html.='</ul>';
$modx->setPlaceholder('recent_docs',$_lang['recent_docs']);
$modx->setPlaceholder('activity_title',$_lang['activity_title']);
$modx->setPlaceholder('RecentInfo',$html);

// user info
$modx->setPlaceholder('info',$_lang['info']);
$modx->setPlaceholder('yourinfo_title',$_lang['yourinfo_title']);
$html = '
    <p>'.$_lang["yourinfo_message"].'</p>
    <table>
      <tr>
        <td>'.$_lang["yourinfo_username"].'</td>
        <td>&nbsp;</td>
        <td><b>'.$modx->getLoginUserName().'</b></td>
      </tr>
      <tr>
        <td>'.$_lang["yourinfo_role"].'</td>
        <td>&nbsp;</td>
        <td><b>'.$_SESSION['mgrPermissions']['name'].'</b></td>
      </tr>
      <tr>
        <td>'.$_lang["yourinfo_previous_login"].'</td>
        <td>&nbsp;</td>
        <td><b>'.$modx->toDateFormat($_SESSION['mgrLastlogin']+$server_offset_time).'</b></td>
      </tr>
      <tr>
        <td>'.$_lang["yourinfo_total_logins"].'</td>
        <td>&nbsp;</td>
        <td><b>'.($_SESSION['mgrLogincount']+1).'</b></td>
      </tr>
    </table>
';
$modx->setPlaceholder('UserInfo',$html);

// online users
$modx->setPlaceholder('online',$_lang['online']);
$modx->setPlaceholder('onlineusers_title',$_lang['onlineusers_title']);
    $timetocheck = (time()-(60*20));//+$server_offset_time;

    include_once "actionlist.inc.php";

    $sql = "SELECT * FROM $dbase.`".$table_prefix."active_users` WHERE $dbase.`".$table_prefix."active_users`.lasthit>'$timetocheck' ORDER BY username ASC";
    $rs = $modx->db->query($sql);
    $limit = $modx->db->getRecordCount($rs);
    if($limit<1) {
        $html = "<p>".$_lang['no_active_users_found']."</p>";
    } else {
        $html = $_lang["onlineusers_message"].'<b>'.strftime('%H:%M:%S', time()+$server_offset_time).'</b>):<br /><br />
                <table id="onlineusers" class="table">
                  <thead>
                    <tr>
                      <th>'.$_lang["onlineusers_user"].'</th>
                      <th>'.$_lang["onlineusers_userid"].'</th>
                      <th>'.$_lang["onlineusers_ipaddress"].'</th>
                      <th>'.$_lang["onlineusers_lasthit"].'</th>
                      <th>'.$_lang["onlineusers_action"].'</th>
                    </tr>
                  </thead>
                  <tbody>
        ';
        for ($i = 0; $i < $limit; $i++) {
            $activeusers = $modx->db->getRow($rs);
            $currentaction = getAction($activeusers['action'], $activeusers['id']);
            $webicon = ($activeusers['internalKey']<0)? "<img src='media/style/{$manager_theme}/images/tree/globe.gif' alt='Web user' />":"";
            $html.= "<tr><td><b>".$activeusers['username']."</b></td><td>$webicon&nbsp;".abs($activeusers['internalKey'])."</td><td>".$activeusers['ip']."</td><td>".strftime('%H:%M:%S', $activeusers['lasthit']+$server_offset_time)."</td><td>$currentaction</td></tr>";
        }
        $html.= '
                </tbody>
                </table>
        ';
    }
$modx->setPlaceholder('OnlineInfo',$html);

// invoke event OnManagerWelcomePrerender
$evtOut = $modx->invokeEvent('OnManagerWelcomePrerender');
if(is_array($evtOut)) {
    $output = implode("",$evtOut);
    $modx->setPlaceholder('OnManagerWelcomePrerender', $output);
}

// invoke event OnManagerWelcomeHome
$evtOut = $modx->invokeEvent('OnManagerWelcomeHome');
if(is_array($evtOut)) {
    $output = implode("",$evtOut);
    $modx->setPlaceholder('OnManagerWelcomeHome', $output);
}

// invoke event OnManagerWelcomeRender
$evtOut = $modx->invokeEvent('OnManagerWelcomeRender');
if(is_array($evtOut)) {
    $output = implode("",$evtOut);
    $modx->setPlaceholder('OnManagerWelcomeRender', $output);
}

// load template file
$customWelcome = $base_path.'manager/media/style/'.$modx->config['manager_theme'] .'/welcome.html'; // WARNING: Retained for backwards compatability but DEPRACATED.
if (is_readable($customWelcome)) {
	$tplFile = $customWelcome;
} else {
	$tplFile = $base_path.'manager/media/style/'.$modx->config['manager_theme'].'/html/welcome.html'; // Moved out of assets/templates/manager (TimGS)
}

$handle = fopen($tplFile, "r");
$tpl = fread($handle, filesize($tplFile));
fclose($handle);
$modx->setPlaceholder('manager_theme_url', "media/style/{$modx->config['manager_theme']}/");

if (!$config_check_warningspresent) {
    // Remove configuration warnings section(s)
    $tpl = preg_replace('/\[\+config_check_fail\+\].*?\[\+end_config_check_fail\+\]/s', '', $tpl);
}

// merge placeholders
$tpl = $modx->mergePlaceholderContent($tpl);
$tpl = preg_replace('~\[\+(.*?)\+\]~', '', $tpl); //cleanup
if ($js= $modx->getRegisteredClientScripts()) {
	$tpl .= $js;
}

require('hash.inc.php');
if ($_SESSION['mgrHashtype'] != CLIPPER_HASH_PREFERRED) {
	echo '<p style="padding: 2em; color: #dd1d1d" class="warning"><strong>We recommend that you <a href="index.php?a=28">change your password now</a> to take advantage of our security improvements.</p>';
}

echo $tpl;
?>
