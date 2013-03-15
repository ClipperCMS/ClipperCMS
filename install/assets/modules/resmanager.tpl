// <?php 
/**
 * Res Manager
 * 
 * Quickly perform bulk updates to the Documents in your site including templates, publishing details, and permissions
 * 
 * @category	module
 * @version 	1.1
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal	@properties
 * @internal	@guid 	
 * @internal	@shareparams 1
 * @internal	@dependencies requires files located at /assets/modules/resmanager/
 * @internal	@modx_category Manager and Admin
 * @internal    @installset base, sample
 */

include_once(MODX_BASE_PATH.'assets/modules/resmanager/classes/resmanager.class.php');
include_once(MODX_BASE_PATH.'assets/modules/resmanager/classes/rm_frontend.class.php');
include_once(MODX_BASE_PATH.'assets/modules/resmanager/classes/rm_backend.class.php');

$rm = new ResManager($modx);
$rmf = new ResManagerFrontend($rm, $modx);
$rmb = new ResManagerBackend($rm, $modx);

$rm->ph = $rm->getLang();
$rm->ph['theme'] = $rm->getTheme();
$rm->ph['ajax.endpoint'] = MODX_SITE_URL.'assets/modules/resmanager/tv.ajax.php';
$rm->ph['datepicker.year_range'] = $modx->config['datepicker_year_range'];
$rm->ph['date.format'] = $modx->config['date_format'];
$rm->ph['time.format'] = $modx->config['time_format'];

$rm->ph['style.css'] = '';
if (is_file(MODX_MANAGER_PATH."media/style/$manager_theme/style.css")) {
    $rm->ph['style.css'] = '<link rel="stylesheet" href="media/style/'.$manager_theme."/style.css\" />\n";
}

$rm->ph['manager.css'] = '';
if (is_file(MODX_MANAGER_PATH."media/style/$manager_theme/manager.css")) {
    $rm->ph['manager.css'] =  '<link rel="stylesheet" href="media/style/'.$manager_theme."/manager.css\" />\n";
}

$rm->ph['jquery'] = $modx->getJqueryTag();
$rm->ph['jquery.ui'] = $modx->getJqueryPluginTag('jquery-ui-custom-clippermanager', 'jquery-ui-custom-clippermanager.min.js');
$rm->ph['jquery.timepicker'] =$modx->getJqueryPluginTag('jquery-ui-timepicker', 'jquery-ui-timepicker-addon.js');

if (isset($_POST['tabAction'])) {

    $rmb->handlePostback();
} else {

    $rmf->getViews();
    echo $rm->parseTemplate('main.tpl', $rm->ph);
}
