<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

?>

<h1><?php echo $_lang['help']; ?></h1>

<?php
$help = array();
if ($handle = opendir(dirname(__FILE__).'/../help/templates')) {
    while (false !== ($file = readdir($handle))) {
        if ($file[0] != '.' && substr($file, -1) != '~') {
            $help[] = $file;
        }
    }
    closedir($handle);
}


natcasesort($help);

$tabs = "";
$tabsContent = "";

foreach($help as $k=>$v) {

    $helpname =  substr($v, 0, strrpos($v, '.'));

    $prefix = substr($helpname, 0, 2);
    if(is_numeric($prefix)) {
        $helpname =  substr($helpname, 2, strlen($helpname)-1 );
    }

    $helpname = str_replace('_', ' ', $helpname);
    
    // Get each template in turn
    // Note that if a template returns an empty string or just whitespace, it will be omitted and it's tab will not appear.
    // 		This means that if conditionals in the template result in no output, the template will be omitted entirely and
    //		provides a mechanism whereby, for example, only admins can get to see certain help files.
    ob_start();
    include(dirname(__FILE__)."/../help/templates/$v");
    $help_content = ob_get_clean();
    
    if (trim($help_content)) {
    
    	$tabs .= '<li><a href="#tab'.$v.'Help">'.$helpname.'</a></li>';
    	$tabsContent .= '<div id="tab'.$v.'Help">' . $help_content . '</div>';

	}
    
    
    
}
?>


<div class="sectionBody">
	
	<div id="help-tabs" class="js-tabs">
		<ul>
			<?php echo $tabs; ?>
		</ul>
		<?php echo $tabsContent; ?>
	</div>

</div>

