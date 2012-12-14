/**
 * mm_demo_rules
 * 
 * Default ManagerManager rules. Should be modified for your own sites.
 * 
 * @category	chunk
 * @version 	1.1
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal 	@modx_category Demo Content
 * @internal    @overwrite false
 * @internal    @installset base, sample
 */

// All users

// Uncomment if you don't use longtitle anywhere in your templates
//mm_hideFields('longtitle');

mm_hideFields('log');
mm_hideFields('parent');
mm_hideFields('link_attributes');
mm_hideFields('which_editor');
mm_hideFields('is_richtext');

mm_changeFieldHelp('pagetitle', 'Title of this page');
mm_changeFieldHelp('menutitle', 'An optional title for this page to be used in menus. Usually shorter if used.');

// Uncomment this if you use [*description*] in the description meta tag
//mm_changeFieldHelp('description', 'Description for search engines. Optional. Leave blank if unsure'); 

// Only displayed for admins
mm_hideFields('cacheable', '!1');
mm_hideFields('clear_cache', '!1');
mm_hideFields('is_folder', '!1');
mm_hideFields('content_dispo', '!1');
mm_hideFields('content_type', '!1');

// Always give a preview of Image TVs
mm_widget_showimagetvs();

