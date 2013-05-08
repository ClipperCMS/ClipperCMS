<?php
/**
 * Resource Manager Module - english.inc.php
 * 
 * Purpose: Contains the language strings for use in the module.
 * Author: Garry Nutting
 * For: MODx CMS (www.modxcms.com)
 * Date:29/09/2006 Version: 1.6
 *
 */

//-- ENGLISH LANGUAGE FILE

//-- titles
$_lang['RM_module_title'] = 'Resource Manager';
$_lang['RM_action_title'] = 'Select an action';
$_lang['RM_range_title'] = 'Specify a Range of Resource IDs';
$_lang['RM_tree_title'] = 'Select Resources from the tree';
$_lang['RM_update_title'] = 'Update Completed';
$_lang['RM_sort_title'] = 'Menu Index Editor';

//-- tabs
$_lang['RM_doc_permissions'] = 'Resource Permissions';
$_lang['RM_template_variables'] = 'Template Variables';
$_lang['RM_sort_menu'] = 'Sort Menu Items';
$_lang['RM_change_template'] = 'Change Template';
$_lang['RM_publish'] = 'Publish/Unpublish';
$_lang['RM_other'] = 'Other Properties';

//-- buttons
$_lang['RM_close'] = 'Close Res Manager';
$_lang['RM_cancel'] = 'Go Back';
$_lang['RM_go'] = 'Go';
$_lang['RM_save'] = 'Save';
$_lang['RM_sort_another'] = 'Sort Another';

//-- templates tab
$_lang['RM_tpl_desc'] = 'Choose the required template from the table below and then specify the Resource IDs which need to be changed. ';
$_lang['RM_tpl_no_templates'] = 'No Templates Found';
$_lang['RM_tpl_column_id'] = 'ID';
$_lang['RM_tpl_column_name'] = 'Name';
$_lang['RM_tpl_column_description'] ='Description';
$_lang['RM_tpl_blank_template'] = 'Blank Template';

$_lang['RM_tpl_results_message']= 'Use the Back button if you need make more changes. The Site Cache has automatically been cleared.';

//-- template variables tab
$_lang['RM_tv_desc'] = 'Choose the required template from the table and the associated template variables will be loaded. Select the required Template Variables and set their values. Specify resources to be updated and submit for processing.';
$_lang['RM_tv_template_mismatch'] = 'This resource does not use the chosen template.';
$_lang['RM_tv_doc_not_found'] = 'This resource was not found in the database.';
$_lang['RM_tv_no_tv'] = 'No Template Variables found for the template.';
$_lang['RM_tv_no_docs'] = 'No resources selected for updating.';
$_lang['RM_tv_no_template_selected'] = 'No template has been selected.';
$_lang['RM_tv_loading'] = 'Template Variables are loading ...';
$_lang['RM_tv_ignore_tv'] = 'Ignore these Template Variables (comma-separated values):';
$_lang['RM_tv_ajax_insertbutton'] = 'Insert';

//-- resource permissions tab
$_lang['RM_doc_desc'] = 'Choose the required Resource Group from the table below and whether you wish to add or remove the group. Then specify the Resource IDs which need to be changed.';
$_lang['RM_doc_no_docs'] = 'No Resource Groups Found';
$_lang['RM_doc_column_id'] = 'ID';
$_lang['RM_doc_column_name'] = 'Name';
$_lang['RM_doc_radio_add'] = 'Add a Resource Group';
$_lang['RM_doc_radio_remove'] = 'Remove a Resource Group';

$_lang['RM_doc_skip_message1'] = 'Resource with ID';
$_lang['RM_doc_skip_message2'] = 'is already part of the selected resource group (skipping)';

//-- sort menu tab
$_lang['RM_sort_pick_item'] = 'Please click the site root or parent resource that you\'d like to sort from the MAIN RESOURCE TREE.';
$_lang['RM_sort_updating'] = 'Updating ...';
$_lang['RM_sort_updated'] = 'Updated';
$_lang['RM_sort_nochildren'] = 'Parent does not have any children';
$_lang['RM_sort_noid']='No resource has been selected. Please go back and select a resource.';

//-- other tab
$_lang['RM_other_header'] = 'Miscellaneous Resource Settings';
$_lang['RM_misc_label'] = 'Available Settings:';
$_lang['RM_misc_desc'] = 'Please pick a setting from the dropdown menu and then the required option. Please note that only one setting can be changed at a time.';

$_lang['RM_other_dropdown_publish'] = 'Publish/Unpublish';
$_lang['RM_other_dropdown_show'] = 'Show/Hide in Menu';
$_lang['RM_other_dropdown_search'] = 'Searchable/Unsearchable';
$_lang['RM_other_dropdown_cache'] = 'Cacheable/Uncacheable';
$_lang['RM_other_dropdown_richtext'] = 'Richtext/No Richtext Editor';
$_lang['RM_other_dropdown_delete'] = 'Delete/Undelete';

//-- radio button text
$_lang['RM_other_publish_radio1'] = 'Publish'; 
$_lang['RM_other_publish_radio2'] = 'Unpublish';
$_lang['RM_other_show_radio1'] = 'Hide in Menu'; 
$_lang['RM_other_show_radio2'] = 'Show in Menu';
$_lang['RM_other_search_radio1'] = 'Searchable'; 
$_lang['RM_other_search_radio2'] = 'Unsearchable';
$_lang['RM_other_cache_radio1'] = 'Cacheable'; 
$_lang['RM_other_cache_radio2'] = 'Uncacheable';
$_lang['RM_other_richtext_radio1'] = 'Richtext'; 
$_lang['RM_other_richtext_radio2'] = 'No Richtext';
$_lang['RM_other_delete_radio1'] = 'Delete'; 
$_lang['RM_other_delete_radio2'] = 'Undelete';

//-- adjust dates 
$_lang['RM_adjust_dates_header'] = 'Set Resource Dates';
$_lang['RM_adjust_dates_desc'] = 'Any of the following Resource date settings can be changed. Click in the field to view the calendar.';
$_lang['RM_view_calendar'] = 'View Calendar';
$_lang['RM_clear_date'] = 'Clear Date';

//-- adjust authors
$_lang['RM_adjust_authors_header'] = 'Set Authors';
$_lang['RM_adjust_authors_desc'] = 'Use the dropdown lists to set new authors for the resources.';
$_lang['RM_adjust_authors_createdby'] = 'Created By:';
$_lang['RM_adjust_authors_editedby'] = 'Edited By:';
$_lang['RM_adjust_authors_noselection'] = 'No change';

 //-- labels
$_lang['RM_date_pubdate'] = 'Publish Date:';
$_lang['RM_date_unpubdate'] = 'Unpublish Date:';
$_lang['RM_date_createdon'] = 'Created On Date:';
$_lang['RM_date_editedon'] = 'Edited On Date:';
//$_lang['RM_date_deletedon'] = 'Deleted On Date';

$_lang['RM_date_notset'] = ' (not set)';
//deprecated
$_lang['RM_date_dateselect_label'] = 'Select a Date: ';

//-- resource select section
$_lang['RM_select_submit'] = 'Submit';
$_lang['RM_select_range'] = 'Switch back to setting a Resource ID Range';
$_lang['RM_select_range_text'] = '<p><strong>Key (where n is a resource ID	number):</strong><br /><br />
							  n* - Change setting for this resource and immediate children<br />
							  n** - Change setting for this resource and ALL children<br />
							  n-n2 - Change setting for this range of resources<br />
							  n - Change setting for a single resource</p>
							  <p>Example: 1*,4**,2-20,25 - This will change the selected setting
						      for resources 1 and its children, resource 4 and all children, resources 2
						      through 20 and resource 25.</p>';
$_lang['RM_select_tree'] ='View and select resources using the Resource Tree';

//-- process tree/range messages
$_lang['RM_process_noselection'] = 'No selection was made. ';
$_lang['RM_process_novalues'] = 'No values have been specified.';
$_lang['RM_process_limits_error'] = 'Upper limit less than lower limit:';
$_lang['RM_process_invalid_error'] = 'Invalid Value:';
$_lang['RM_process_update_success'] = 'Update was completed successfully, with no errors.';
$_lang['RM_process_update_error'] = 'Update has completed but encountered errors:';
$_lang['RM_process_back'] = 'Back';

//-- manager access logging
$_lang['RM_log_template'] = 'Resource Manager: Templates changed.';
$_lang['RM_log_templatevariables'] = 'Resource Manager: Template variables changed.';
$_lang['RM_log_docpermissions'] ='Resource Manager: Resource Permissions changed.';
$_lang['RM_log_sortmenu']='Resource Manager: Menu Index operation completed.';
$_lang['RM_log_publish']='Resource Manager: Resource Manager: Resources Published/Unpublished settings changed.';
$_lang['RM_log_hidemenu']='Resource Manager: Resources Hide/Show in Menu settings changed.';
$_lang['RM_log_search']='Resource Manager: Resources Searchable/Unsearchable settings changed.';
$_lang['RM_log_cache']='Resource Manager: Resources Cacheable/Uncacheable settings changed.';
$_lang['RM_log_richtext']='Resource Manager: Resources Use Richtext Editor settings changed.';
$_lang['RM_log_delete']='Resource Manager: Resources Delete/Undelete settings changed.';
$_lang['RM_log_dates']='Resource Manager: Resources Date settings changed.';
$_lang['RM_log_authors']='Resource Manager: Resources Author settings changed.';

?>
