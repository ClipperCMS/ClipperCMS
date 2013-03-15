<!doctype html>
<html lang="<?php echo $_lang['language_code']?>">
<head>
	<title><?php echo $_lang['modx_install']?></title>
	<meta charset="<?php echo $_lang['encoding']?>">
    <link rel="stylesheet" href="style.css" media="screen" />
</head>

<body<?php echo $modx_textdir ? ' id="rtl"':''?>>
<!-- start install screen-->
<div id="header">
    <div class="container_12">
        <span class="help"><a href="<?php echo $_lang["help_link"] ?>" target="_blank" title="<?php echo $_lang["help_title"] ?>"><?php echo $_lang["help"] ?></a></span>
		<span class="version"><a href="http://<?php echo CMS_DOMAIN; ?>"><?php echo $moduleName.'</a> '.$moduleVersion.' ('.($modx_textdir?'&rlm;':'').CMS_RELEASE_DATE; ?>)</span>
        <div id="mainheader">
        	<h1 class="pngfix" id="logo"><span><?php echo CMS_NAME; ?></span></h1>
        </div>
    </div>
</div>
<!-- end header -->

<div id="contentarea">
    <div class="container_12">        
        <!-- start content -->
