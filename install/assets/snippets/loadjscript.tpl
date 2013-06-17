//<?php
/**
 * LoadJscript
 * 
 * (sample site) Load jQuery and other JS files via API (avoiding multiple-loads)
 *
 * @category 	snippet
 * @version 	1.0
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal	@properties
 * @internal	@modx_category Manager and Admin
 * @internal    @installset sample
 */


/*
 * @name LoadJscript
 * @version 1.0
 * @author Keith Penton (kp52)
 * 
 * @param &jQuery location of the jQuery library
 * @param &javascript location of further Javascript library
 * @param &settings site-specific Javascript
 * 
 * @license Public Domain, use as you like.
 * 
 * This snippet loads jQuery and other Javascript used by the sample site
 * safely, via the API rather than SCRIPT tags. The relevant tags will be inserted  
 * at the end of the HEAD section.
 */
    $modx->regClientStartupScript($jQuery);
    $modx->regClientStartupScript($javascript);
    $modx->regClientStartupScript($settings);
