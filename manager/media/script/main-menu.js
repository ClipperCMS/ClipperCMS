$(document).ready(function($) {
	
});


$(window).load(function () {
		
	if(top.__hideTree) {
		//display toc icon when tree frame is closed
		var elm = jQuery('#tocText');
		if(elm){
			elm.html("<a href='#' onclick='defaultTreeFrame();'><img src='"+style.show_tree+"' alt='"+lang.show_tree+"' width='16' height='16' /></a>");
		}
	}
	
});


		//hides tree frame and places the reopen icon
		function hideTreeFrame() {
			userDefinedFrameWidth = parent.document.getElementsByTagName("FRAMESET").item(1).cols;
			currentFrameState = 'closed';
			try {
				var elm = jQuery('#tocText');
				if(elm){
					elm.html("<a href='#' onclick='defaultTreeFrame();'><img src='"+style.show_tree+"' alt='"+lang.show_tree+"' width='16' height='16' /></a>");
				}
				
				var textdir;
				if(!modx_textdir){
					textdir = '0,*';
				}else{
					textdir = '*,0';
				}
				
				parent.document.getElementsByTagName("FRAMESET").item(1).cols = textdir;
				
				top.__hideTree = true;
			} catch(oException) {
				window.setTimeout('hideTreeFrame()', 1000);
			}
		}
		
		// opens tree frame and removes the reopen icon
		function defaultTreeFrame() {
			userDefinedFrameWidth = defaultFrameWidth;
			currentFrameState = 'open';
			try {
				var elm = jQuery('#tocText');
				if(elm){
					elm.empty();
				}
				parent.document.getElementsByTagName("FRAMESET").item(1).cols = defaultFrameWidth;
				top.__hideTree = false;
			} catch(oException) {
				window.setTimeout('defaultTreeFrame()', 1000);
			}
		}
		
		// GENERAL FUNCTIONS - Refresh
		// These functions are used for refreshing the tree or menu
		function reloadtree() {
			var elm = jQuery('#buildText');
			if (elm.length) {
				elm.html("&nbsp;&nbsp;<img src='"+lang.icons_loading_doc_tree+"' width='16' height='16' />&nbsp;"+lang.loading_doc_tree);
				elm.show();
			}
			top.tree.saveFolderState(); // save folder state
			setTimeout('top.tree.restoreTree()', 200);
		}
		
		
		function reloadmenu() {
			if(manager_layout==0) {
				var elm = jQuery('#buildText');
				if (elm.length) {
					elm.html("&nbsp;&nbsp;<img src='"+style.icons_working+"' width='16' height='16' />&nbsp;" + lang.loading_menu);
					elm.show();
				}
				parent.mainMenu.location.reload();
			}
		}
		
		function startrefresh(rFrame){
			
			if(rFrame==1){
				window.setTimeout('reloadtree()',500);
			}
			if(rFrame==2) {
				window.setTimeout('reloadmenu()',500);
			}
			if(rFrame==9) {
				window.setTimeout('reloadmenu()',500);
				window.setTimeout('reloadtree()',500);
			}
			if(rFrame==10) {
				window.top.location.href = "../manager";
			}
		}
		
		// GENERAL FUNCTIONS - Work
		// These functions are used for showing the user the system is working
		function work() {
			var elm = jQuery('#workText');
			if (elm.length) {
				elm.html("&nbsp;<img src='"+style.icons_working+"' width='16' height='16' />&nbsp;" + lang.working);
			}else{
				window.setTimeout('work()', 50);
			}
		}
	
		function stopWork() {
			var elm = jQuery('#workText');
			if (elm.length) { 
				elm.empty(); 
			}else{
				window.setTimeout('stopWork()', 50);
			}
		}
		
		// GENERAL FUNCTIONS - Remove locks
		// This function removes locks on documents, templates, parsers, and snippets
		function removeLocks() {
			if(confirm(lang.confirm_remove_locks)==true) {
				top.main.document.location.href="index.php?a=67";
			}
		}
	
		function showWin() {
			window.open('../');
		}
	
		function stopIt() {
			top.mainMenu.stopWork();
		}
	
		function openCredits() {
			parent.main.document.location.href = "index.php?a=18";
			window.setTimeout('stopIt()', 2000);
		}
		
		function NavToggle(element) {
			// This gives the active tab its look
			var navid = jQuery('#nav');
			var navs = jQuery('#nav li');
			var navsCount = navs.length;
			for(j = 0; j < navsCount; j++) {
				active = (navs[j].id == element.parentNode.id) ? "active" : "";
				navs[j].className = active;
			}
	
			// remove focus from top nav
			if(element) element.blur();
		}