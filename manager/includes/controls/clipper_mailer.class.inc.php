<?php
require_once('class.phpmailer.php');

class ClipperMailer extends PHPMailer {

    function __construct() {
        
        global $modx;

        if (is_callable('parent::__construct')) {
            parent::__construct();
        }
        
        $this->CharSet = $modx->config['modx_charset'];
        $this->From = 'ClipperCMS@'.php_uname('n');         // <<<< maybe to put into a config setting
        $this->FromName = $modx->config['site_name'];       // <<<< maybe to put into a config setting
        $this->AddReplyTo($modx->config['emailsender']);    // <<<< maybe to put into a config setting
        
    }

}
