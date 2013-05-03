<?php



//---------------------------------------------------------------------------------
// mm_renameTab
// Rename a tab
//--------------------------------------------------------------------------------- 
function mm_renameTab($tab, $newname, $roles='', $templates='') {

	global $modx;
	$e = &$modx->Event;
			
	// if the current page is being edited by someone in the list of roles, and uses a template in the list of templates
	if ($e->name == 'OnDocFormRender' && useThisRule($roles, $templates)){
		
		
		$output = " // ----------- Rename tab -------------- \n";
		
			switch ($tab) {
			
				case 'general': 
					$output .= '$("[href=#tabGeneral]").text("'.jsSafe($newname).'");' . "\n";
				break;
				
				case 'settings': 
					$output .= '$("[href=#tabSettings]").text("'.jsSafe($newname).'");' . "\n";
				break;				

			} // end switch
			$e->output($output . "\n");
	}	// end if
} // end function




//---------------------------------------------------------------------------------
// mm_hideTabs
// Hide a tab
//---------------------------------------------------------------------------------
function mm_hideTabs($tabs, $roles='', $templates='') {

	global $modx;
	$e = &$modx->Event;
	
	// if we've been supplied with a string, convert it into an array 
	$tabs = makeArray($tabs);
			
	// if the current page is being edited by someone in the list of roles, and uses a template in the list of templates
	if ($e->name == 'OnDocFormRender' && useThisRule($roles, $templates)){

	$output = " // ----------- Hide tabs -------------- \n";
	
	
		foreach($tabs as $tab) {
	
			switch ($tab) {
			
				case 'general': 
					$output = '
						var mmTabs = $(".js-tabs");
						var mmHideTab = mmTabs.find("[aria-controls=tabGeneral]");
						var mmHideTabIdx = mmHideTab.index();
						var mmActiveTabId = mmTabs.find(".ui-state-active").index();
						mmHideTab.hide();
						//activate first visible tab
						if(mmActiveTabId == mmHideTabIdx){
							var firstVisibleTabIdx = mmTabs.find("li:visible:first").index();
							mmTabs.tabs("option", "active", firstVisibleTabIdx);
						}
					';
				break;
				
				case 'settings': 
					$output = '
						var mmTabs = $(".js-tabs");
						var mmHideTab = mmTabs.find("[aria-controls=tabSettings]");
						var mmHideTabIdx = mmHideTab.index();
						var mmActiveTabId = mmTabs.find(".ui-state-active").index();
						mmHideTab.hide();
						//activate first visible tab
						if(mmActiveTabId == mmHideTabIdx){
							var firstVisibleTabIdx = mmTabs.find("li:visible:first").index();
							mmTabs.tabs("option", "active", firstVisibleTabIdx);
						}
					';
				break;
				
			} // end switch
			$e->output($output . "\n");
		} // end foreach
	}	// end if
} // end function










//---------------------------------------------------------------------------------
// mm_createTab
// Create a new tab
//--------------------------------------------------------------------------------- 
function mm_createTab($name, $id, $roles='', $templates='', $intro='', $width='100%') {

	global $modx;
	$e = &$modx->Event;
			
	// if the current page is being edited by someone in the list of roles, and uses a template in the list of templates
	if ((($e->name == 'OnDocFormRender') || ($e->name == 'OnPluginFormRender')) && useThisRule($roles, $templates)){
	
		// Plugin page tabs use a differen name for the tab object
		$js_tab_object = ($e->name == 'OnPluginFormRender') ? 'tpSnippet' : 'tpSettings';

		$output = " // ----------- Create tab -------------- \n";
		
		$output .= '
			var mmTabs = $(".js-tabs");
			var mmTabsId = $(".js-tabs").attr("id");
			mmTabs.find(".ui-tabs-nav").append("<li><a href=\"#tab'.$id.'\">'.$name.'</a></li>");
			mmTabs.append("<div id=\"tab'.$id.'\"><div class=\"tabIntro\">'.$intro.'</div><table width=\"'.$width.'\" border=\"0\" cellspacing=\"5\" cellpadding=\"0\" id=\"table-'.$id.'\"></table></div>");
			mmTabs.tabs("refresh");
			
			if(config.remember_last_tab != 0){
				savedPanelId = sessionStorage.getItem(mmTabsId);
			}
			
			if(savedPanelId !== undefined && savedPanelId == "tab'.$id.'"){
				var index = $("#"+mmTabsId+"a[href=#"+savedPanelId+"]").parent().index();
				mmTabs.tabs("option", "active", index);
			}
			
		';
	
		$e->output($output . "\n");

	}	// end if
} // end function





?>