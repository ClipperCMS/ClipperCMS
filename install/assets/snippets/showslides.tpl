//<?php
/**
 * ShowSlides
 *
 * Output slideshow chunk if set, else nothing
 *
 * @category 	snippet
 * @version 	2.0
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal	@properties
 * @internal	@modx_category Demo Content
 * @internal    @installset sample
 * @name ShowSlides
 * @version 1.0 beta 
 * @author Keith Penton (kp52)
 *
 * @param &switch The TV used to switch the slideshow on and off
 * @param &chunk The chunk to display if &switch is on
 *
 * @license Public Domain, use as you like.
 *
 * @example [!ShowSlides? &switch=`[*show-slideshow*]` &chunk=`slideshow` !]
 *
 */

if ($switch == 1) {
    $output = $modx->getChunk($chunk);
} else {
    $output = null;
}
return $output;
