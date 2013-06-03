<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

    $theme = $manager_theme ? "$manager_theme/":"";

    function constructLink($action, $img, $text, $allowed) {
        if($allowed==1) { ?>
            <div class="menuLink" onclick="menuHandler(<?php echo $action ; ?>); hideMenu();">
        <?php } else { ?>
            <div class="menuLinkDisabled">
        <?php } ?>
                <img src="<?php echo $img; ?>" /><?php echo $text; ?>
            </div>
        <?php
    }
    $mxla = $modx_lang_attribute ? $modx_lang_attribute : 'en';
?>
<!doctype html>
<html lang="<?php echo $mxla?>"<?php ($modx_textdir ? ' dir="rtl"' : '') ?>>

<head>
	<meta charset="<?php echo $modx_manager_charset; ?>">
	<title>Document Tree</title>
    <link rel="stylesheet" href="media/style/<?php echo $manager_theme ? "$manager_theme/":""; ?>style.css" />
    <?php 
    	echo $modx->getJqueryTag();
    ?>
    
    <script>
	    
	     $.noConflict();
	     
    </script>
    
    
    <script>
    jQuery(window).on('load', function () {
		resizeTree();
		restoreTree();
	});
    
    jQuery(window).resize(function(){
    	resizeTree();
    });

    // preload images
    var i = new Image(18,18);
    i.src="<?php echo $_style["tree_page"]?>";
    i = new Image(18,18);
    i.src="<?php echo $_style["tree_globe"]?>";
    i = new Image(18,18);
    i.src="<?php echo $_style["tree_minusnode"]?>";
    i = new Image(18,18);
    i.src="<?php echo $_style["tree_plusnode"]?>";
    i = new Image(18,18);
    i.src="<?php echo $_style["tree_folderopen"]?>";
    i = new Image(18,18);
    i.src="<?php echo $_style["tree_folder"]?>";


    var rpcNode = null;
    var ca = "open";
    var selectedObject = 0;
    var selectedObjectDeleted = 0;
    var selectedObjectName = "";
    var _rc = 0; // added to fix onclick body event from closing ctx menu

<?php
    echo  "var openedArray = new Array();\n";
    if (isset($_SESSION['openedArray'])) {
            $opened = explode("|", $_SESSION['openedArray']);

            foreach ($opened as $item) {
                 printf("openedArray[%d] = 1;\n", $item);
            }
    }
?>

    // return window dimensions in array
    function getWindowDimension() {
        var width  = 0;
        var height = 0;

        if ( typeof( window.innerWidth ) == 'number' ){
            width  = window.innerWidth;
            height = window.innerHeight;
        }else if ( document.documentElement &&
                 ( document.documentElement.clientWidth ||
                   document.documentElement.clientHeight ) ){
            width  = document.documentElement.clientWidth;
            height = document.documentElement.clientHeight;
        }
        else if ( document.body &&
                ( document.body.clientWidth || document.body.clientHeight ) ){
            width  = document.body.clientWidth;
            height = document.body.clientHeight;
        }

        return {'width':width,'height':height};
    }
    
    function resizeTree() {
        var winW = jQuery(window).width();
        var winH = jQuery(window).height();

        // set tree height
        var tree = jQuery('#treeHolder');
        var tmnu = jQuery('#treeMenu');
        
        var treeOffset = tree.offset();
        
        tree.width(winW - 20);
        tree.height(winH - treeOffset.top - 6);
        tree.css({'overflow': 'auto'});

    }
    
    function getScrollY() {
      var scrOfY = 0;
      if( typeof( window.pageYOffset ) == 'number' ) {
        //Netscape compliant
        scrOfY = window.pageYOffset;
      } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
        //DOM compliant
        scrOfY = document.body.scrollTop;
      } else if( document.documentElement &&
          (document.documentElement.scrollTop ) ) {
        //IE6 standards compliant mode
        scrOfY = document.documentElement.scrollTop;
      }
      return scrOfY;
    }

    function showPopup(id,title,e){
        var x,y
        var mnu = jQuery('#mx_contextmenu');
        var bodyHeight = parseInt(jQuery('body').height());
        x = e.clientX>0 ? e.clientX:e.pageX;
        y = e.clientY>0 ? e.clientY:e.pageY;
        y = getScrollY()+(y/2);
        if (y+mnu.height() > bodyHeight) {
            // make sure context menu is within frame
            y = y - ((y+mnu.height())-bodyHeight+5);
        }
        itemToChange=id;
        selectedObjectName= title;
        dopopup(x+5,y);
        e.cancelBubble=true;
        return false;
    };

    function dopopup(x,y) {
        if(selectedObjectName.length>20) {
            selectedObjectName = selectedObjectName.substr(0, 20) + "...";
        }
        
        var h,context = jQuery('#mx_contextmenu');
               
        context.css({'left': x + (<?php echo $modx_textdir ? '-190' : 0;?>)});
        context.css({'top': y});
        
        var elm = jQuery("#nameHolder");
        
        elm.html(selectedObjectName);

        context.css({'visibility':'visible'});
        
        _rc = 1;
        setTimeout("_rc = 0;",100);
    }

    function hideMenu() {
        if (_rc) return false;
        jQuery('#mx_contextmenu').css({'visibility':'hidden'});
    }

    function toggleNode(node,indent,parent,expandAll,privatenode) {
		
		privatenode = (!privatenode || privatenode == '0') ? privatenode = '0' : privatenode = '1';
        
        rpcNode = jQuery(node).parent().children().last();
        
        var rpcNodeText;
        var loadText = "<?php echo $_lang['loading_doc_tree'];?>";

        var signImg = jQuery("#s"+parent);
        var folderImg = jQuery("#f"+parent);
        
        if (rpcNode.css('display') != 'block') {
            // expand
            if(signImg && signImg.attr('src').indexOf('media/style/<?php echo $manager_theme ? "$manager_theme/":""; ?>images/tree/plusnode.gif')>-1) {
                signImg.attr('src', '<?php echo $_style["tree_minusnode"]; ?>');
                folderImg.attr('src', (privatenode == '0') ? '<?php echo $_style["tree_folderopen"]; ?>' :'<?php echo $_style["tree_folderopen_secure"]; ?>');
            }

            rpcNodeText = rpcNode.html();

            if (rpcNodeText=="" || rpcNodeText.indexOf(loadText)>0) {
                
                var i, spacer='';
                for(i=0;i<=indent+1;i++) spacer+='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                rpcNode.css({'display':'block'});
                //Jeroen set opened
                openedArray[parent] = 1 ;
                //Raymond:added getFolderState()
                var folderState = getFolderState();
                rpcNode.innerHTML = "<span class='emptyNode' style='white-space:nowrap;'>"+spacer+"&nbsp;&nbsp;&nbsp;"+loadText+"...<\/span>";
                
                jQuery.get('index.php?a=1&f=nodes&indent='+indent+'&parent='+parent+'&expandAll='+expandAll+folderState, function(data) {
					rpcLoadData(data);
				});
                
            } else {
                
                rpcNode.css({'display':'block'});
                //Jeroen set opened
                openedArray[parent] = 1 ;
            }
        }
        else {
            // collapse
            if(signImg && signImg.attr('src').indexOf('media/style/<?php echo $manager_theme ? "$manager_theme/":""; ?>images/tree/minusnode.gif')>-1) {
                signImg.attr('src','<?php echo $_style["tree_plusnode"]; ?>');
                folderImg.attr('src', (privatenode == '0') ? '<?php echo $_style["tree_folder"]; ?>' : '<?php echo $_style["tree_folder_secure"]; ?>');
            }
            rpcNode.css({'display':'none'});
            openedArray[parent] = 0 ;
        }
    }

    function rpcLoadData(response) {
        
        
        if(rpcNode != null && response !='savestateonly'){
            if( typeof response=='object' ){
	            jQuery(rpcNode).html(response.responseText);//TODO: could not get to this
            }else{
	            jQuery(rpcNode).html(response);
            }
            jQuery(rpcNode).css({'display':'block'});
            rpcNode.loaded = true;
            if(top.mainMenu !== undefined){
	            var elm = jQuery('#buildText', top.mainMenu.document);
	            
	            if (elm.length) {
	                elm.empty();
	                elm.css({'display':'none'})
	            }
            }
            // check if bin is full
            if(rpcNode.attr('id') == 'treeRoot') {
                var e = jQuery('#binFull');
                if(e.length) 
                	showBinFull();
                else 
                	showBinEmpty();
            }
            // check if our payload contains the login form :)
            e = jQuery('mx_loginbox');
            if(e.length) {
                // yep! the seession has timed out
                rpcNode.empty();
                top.location = 'index.php';
            }
        }
    }

    function expandTree() {
        rpcNode = jQuery('#treeRoot');
        jQuery.get('index.php?a=1&f=nodes&indent=1&parent=0&expandAll=1', function(data) {
			rpcLoadData(data);
		});
    }

    function collapseTree() {
        rpcNode = jQuery('#treeRoot');
        jQuery.get('index.php?a=1&f=nodes&indent=1&parent=0&expandAll=0', function(data) {
			rpcLoadData(data);
		});
    }

    // new function used in body onload
    function restoreTree() {
        rpcNode = jQuery('#treeRoot');
		jQuery.get('index.php?a=1&f=nodes&indent=1&parent=0&expandAll=2', function(data) {
			rpcLoadData(data);
		});
    }

    function setSelected(elSel) {
        var all = document.getElementsByTagName( "SPAN" );
        var l = all.length;
        for ( var i = 0; i < l; i++ ) {
            el = all[i]
            cn = el.className;
            if (cn=="treeNodeSelected") {
                el.className="treeNode";
            }
        }
        elSel.className="treeNodeSelected";
    };

    function setHoverClass(el, dir) {
    	el = jQuery(el);
    	if(!el.hasClass('treeNodeSelected')){
	    	if(dir == 1){
		    	el.attr('class', 'treeNodeHover')
	    	}else{
		    	el.attr('class', 'treeNode')
	    	}
    	}else{
	    	//ntd
    	}
    };

    // set Context Node State
    function setCNS(n, b) {
    	n = jQuery(n);
        if(b==1) {
            n.css('background-color', 'beige');
        } else {
            n.css('background-color', 'transparent');
        }
    };

    function updateTree() {
        rpcNode = jQuery('#treeRoot');
        
        treeParams = 'a=1&f=nodes&indent=1&parent=0&expandAll=2&dt=' + document.sortFrm.dt.value + '&tree_sortby=' + document.sortFrm.sortby.value + '&tree_sortdir=' + document.sortFrm.sortdir.value;
        
        jQuery.get('index.php?'+treeParams, function(data) {
			rpcLoadData(data);
		});
    }

    function emptyTrash() {
        if(confirm("<?php echo $_lang['confirm_empty_trash']; ?>")==true) {
            top.main.document.location.href="index.php?a=64";
        }
    }

    currSorterState="none";
    function showSorter() {
        if(currSorterState=="none") {
            currSorterState="block";
            document.getElementById('floater').style.display=currSorterState;
        } else {
            currSorterState="none";
            document.getElementById('floater').style.display=currSorterState;
        }
    }

    function treeAction(id, name) {
    	
        if(ca=="move") {
            try {
                parent.main.setMoveValue(id, name);
            } catch(oException) {
                alert('<?php echo $_lang['unable_set_parent']; ?>');
            }
        }
        if(ca=="open" || ca=="") {
            if(id==0) {
                // do nothing?
                parent.main.location.href="index.php?a=2";
            } else {
                // parent.main.location.href="index.php?a=3&id=" + id + getFolderState(); //just added the getvar &opened=
                parent.main.location.href="index.php?a=<?php echo (!empty($modx->config['tree_page_click']) ? $modx->config['tree_page_click'] : '27'); ?>&id=" + id; // edit as default action
            }
        }
        if(ca=="parent") {
            try {
                parent.main.setParent(id, name);
            } catch(oException) {
                alert('<?php echo $_lang['unable_set_parent']; ?>');
            }
        }
        if(ca=="link") {
            try {
                parent.main.setLink(id);
            } catch(oException) {
                alert('<?php echo $_lang['unable_set_link']; ?>');
            }
        }
    }

    //Raymond: added getFolderState,saveFolderState
    function getFolderState(){
        if (openedArray != [0]) {
                oarray = "&opened=";
                for (key in openedArray) {
                   if (openedArray[key] == 1) {
                      oarray += key+"|";
                   }
                }
        } else {
                oarray = "&opened=";
        }
        return oarray;
    }
    
    function saveFolderState() {
        var folderState = getFolderState();
        jQuery.get('index.php?a=1&f=nodes&savestateonly=1'+folderState, function(data) {
			rpcLoadData(data);
		});
    }
    
    // show state of recycle bin
    function showBinFull() {
        var a = jQuery('#Button10');
        var title = '<?php echo $_lang['empty_recycle_bin']; ?>';
        if (a.length) {
	        a.attr('title', title);
	        a.html('<?php echo $_style['empty_recycle_bin']; ?>');
	        a.removeClass('treeButtonDisabled').addClass('treeButton');
	        a.click(function(){
		        emptyTrash();
	        })
        }
    }

	function showBinEmpty() {
        var a = jQuery('#Button10');
        var title = '<?php echo addslashes($_lang['empty_recycle_bin_empty']); ?>';
	    if (a.length) {
	        a.attr('title', title);
	        a.html('<?php echo $_style['empty_recycle_bin_empty']; ?>');
	        a.removeClass('treeButton').addClass('treeButtonDisabled');
	        a.click(function(e){
		        e.preventDefault();
	        })
	    }
    }

</script>

</head>
<body onClick="hideMenu(1);" class="treeframebody<?php echo $modx_textdir ? ' rtl':''?>">

<div id="treeSplitter"></div>

<table id="treeMenu" width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td><a href="#" class="treeButton" id="Button1" onClick="expandTree();" title="<?php echo $_lang['expand_tree']; ?>"><?php echo $_style['expand_tree']; ?></a></td>
            <td><a href="#" class="treeButton" id="Button2" onClick="collapseTree();" title="<?php echo $_lang['collapse_tree']; ?>"><?php echo $_style['collapse_tree']; ?></a></td>
            <?php if ($modx->hasPermission('new_document')) { ?>
                <td><a href="#" class="treeButton" id="Button3a" onClick="top.main.document.location.href='index.php?a=4';" title="<?php echo $_lang['add_resource']; ?>"><?php echo $_style['add_doc_tree']; ?></a></td>
                <td><a href="#" class="treeButton" id="Button3c" onClick="top.main.document.location.href='index.php?a=72';" title="<?php echo $_lang['add_weblink']; ?>"><?php echo $_style['add_weblink_tree']; ?></a></td>
            <?php } ?>
            <td><a href="#" class="treeButton" id="Button4" onClick="top.mainMenu.reloadtree();" title="<?php echo $_lang['refresh_tree']; ?>"><?php echo $_style['refresh_tree']; ?></a></td>
            <td><a href="#" class="treeButton" id="Button5" onClick="showSorter();" title="<?php echo $_lang['sort_tree']; ?>"><?php echo $_style['sort_tree']; ?></a></td>
            <?php if ($modx->hasPermission('empty_trash')) { ?>
                <td><a href="#" id="Button10" class="treeButtonDisabled" title="<?php echo $_lang['empty_recycle_bin_empty'] ; ?>"><?php echo $_style['empty_recycle_bin_empty'] ; ?></a></td>
            <?php } ?>
            </tr>
        </table>
    </td>
    <td align="right">
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td><a href="#" class="treeButton" id="Button6" onClick="top.mainMenu.hideTreeFrame();" title="<?php echo $_lang['hide_tree']; ?>"><?php echo $_style['hide_tree']; ?></a></td>
            </tr>
        </table>
    </td>
  </tr>
</table>

<div id="floater">
<?php
if(isset($_REQUEST['tree_sortby'])) {
    $_SESSION['tree_sortby'] = $_REQUEST['tree_sortby'];
}

if(isset($_REQUEST['tree_sortdir'])) {
    $_SESSION['tree_sortdir'] = $_REQUEST['tree_sortdir'];
}
?>
<form name="sortFrm" id="sortFrm" action="menu.php">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="padding-left: 10px;padding-top: 1px;" colspan="2">
        <select name="sortby">
            <option value="isfolder" <?php echo $_SESSION['tree_sortby']=='isfolder' ? "selected='selected'" : "" ?>><?php echo $_lang['folder']; ?></option>
            <option value="pagetitle" <?php echo $_SESSION['tree_sortby']=='pagetitle' ? "selected='selected'" : "" ?>><?php echo $_lang['pagetitle']; ?></option>
            <option value="id" <?php echo $_SESSION['tree_sortby']=='id' ? "selected='selected'" : "" ?>><?php echo $_lang['id']; ?></option>
            <option value="menuindex" <?php echo $_SESSION['tree_sortby']=='menuindex' ? "selected='selected'" : "" ?>><?php echo $_lang['resource_opt_menu_index'] ?></option>
            <option value="createdon" <?php echo $_SESSION['tree_sortby']=='createdon' ? "selected='selected'" : "" ?>><?php echo $_lang['createdon']; ?></option>
            <option value="editedon" <?php echo $_SESSION['tree_sortby']=='editedon' ? "selected='selected'" : "" ?>><?php echo $_lang['editedon']; ?></option>
        </select>
    </td>
  </tr>
  <tr>
    <td width="99%" style="padding-left: 10px;padding-top: 1px;">
        <select name="sortdir">
            <option value="DESC" <?php echo $_SESSION['tree_sortdir']=='DESC' ? "selected='selected'" : "" ?>><?php echo $_lang['sort_desc']; ?></option>
            <option value="ASC" <?php echo $_SESSION['tree_sortdir']=='ASC' ? "selected='selected'" : "" ?>><?php echo $_lang['sort_asc']; ?></option>
        </select>
        <input type='hidden' name='dt' value='<?php echo $_REQUEST['dt']; ?>' />
    </td>
    <td width="1%"><a href="#" class="treeButton" id="button7" style="text-align:right" onClick="updateTree();showSorter();" title="<?php echo $_lang['sort_tree']; ?>"><?php echo $_lang['sort_tree']; ?></a></td>
  </tr>
</table>
</form>
</div>

<div id="treeHolder">
    <div><?php echo $_style['tree_showtree']; ?>&nbsp;<span class="rootNode" onClick="treeAction(0, '<?php echo addslashes($site_name); ?>');"><b><?php echo $site_name; ?></b></span><div id="treeRoot"></div></div>
</div>

<script>
// Set 'treeNodeSelected' class on document node when editing via Context Menu
function setActiveFromContextMenu( doc_id ){
    jQuery('.treeNodeSelected').removeClass('treeNodeSelected');
    jQuery('#node'+doc_id+'>span').addClass('treeNodeSelected');
}

// Context menu stuff
function menuHandler(action) {
    switch (action) {
        case 1 : // view
            setActiveFromContextMenu( itemToChange );
            top.main.document.location.href="index.php?a=3&id=" + itemToChange;
            break
        case 2 : // edit
            setActiveFromContextMenu( itemToChange );
            top.main.document.location.href="index.php?a=27&id=" + itemToChange;
            break
        case 3 : // new Resource
            top.main.document.location.href="index.php?a=4&pid=" + itemToChange;
            break
        case 4 : // delete
            if(selectedObjectDeleted==0) {
                if(confirm("'" + selectedObjectName + "'\n\n<?php echo $_lang['confirm_delete_resource']; ?>")==true) {
                    top.main.document.location.href="index.php?a=6&id=" + itemToChange;
                }
            } else {
                alert("'" + selectedObjectName + "' <?php echo $_lang['already_deleted']; ?>");
            }
            break
        case 5 : // move
            top.main.document.location.href="index.php?a=51&id=" + itemToChange;
            break
        case 6 : // new Weblink
            top.main.document.location.href="index.php?a=72&pid=" + itemToChange;
            break
        case 7 : // duplicate
            if(confirm("<?php echo $_lang['confirm_resource_duplicate'] ?>")==true) {
                   top.main.document.location.href="index.php?a=94&id=" + itemToChange;
               }
            break
        case 8 : // undelete
            if(selectedObjectDeleted==0) {
                alert("'" + selectedObjectName + "' <?php echo $_lang['not_deleted']; ?>");
            } else {
                if(confirm("'" + selectedObjectName + "' <?php echo $_lang['confirm_undelete']; ?>")==true) {
                    top.main.document.location.href="index.php?a=63&id=" + itemToChange;
                }
            }
            break
        case 9 : // publish
            if(confirm("'" + selectedObjectName + "' <?php echo $_lang['confirm_publish']; ?>")==true) {
                top.main.document.location.href="index.php?a=61&id=" + itemToChange;
            }
            break
        case 10 : // unpublish
            if (itemToChange != <?php echo $modx->config['site_start']?>) {
                if(confirm("'" + selectedObjectName + "' <?php echo $_lang['confirm_unpublish']; ?>")==true) {
                    top.main.document.location.href="index.php?a=62&id=" + itemToChange;
                }
            } else {
                alert('Document is linked to site_start variable and cannot be unpublished!');
            }
            break
        case 12 : // preview	
            window.open(selectedObjectUrl,'previeWin'); //re-use 'new' window
            break

        default :
            alert('Unknown operation command.');
    }
}

</script>

<!-- Contextual Menu Popup Code -->
<div id="mx_contextmenu" onselectstart="return false;">
    <div id="nameHolder">&nbsp;</div>
    <?php
    constructLink(3, $_style["icons_new_document"], $_lang["create_resource_here"], $modx->hasPermission('new_document')); // new Resource
    constructLink(2, $_style["icons_save"], $_lang["edit_resource"], $modx->hasPermission('edit_document')); // edit
    constructLink(5, $_style["icons_move_document"] , $_lang["move_resource"], $modx->hasPermission('save_document')); // move
    constructLink(7, $_style["icons_resource_duplicate"], $_lang["resource_duplicate"], $modx->hasPermission('new_document')); // duplicate
    ?>
    <div class="seperator"></div>
    <?php
    constructLink(9, $_style["icons_publish_document"], $_lang["publish_resource"], $modx->hasPermission('publish_document')); // publish
    constructLink(10, $_style["icons_unpublish_resource"], $_lang["unpublish_resource"], $modx->hasPermission('publish_document')); // unpublish
    constructLink(4, $_style["icons_delete"], $_lang["delete_resource"], $modx->hasPermission('delete_document')); // delete
    constructLink(8, $_style["icons_undelete_resource"], $_lang["undelete_resource"], $modx->hasPermission('delete_document')); // undelete
    ?>
    <div class="seperator"></div>
    <?php
    constructLink(6, $_style["icons_weblink"], $_lang["create_weblink_here"], $modx->hasPermission('new_document')); // new Weblink
    ?>
    <div class="seperator"></div>
    <?php
    constructLink(1, $_style["icons_resource_overview"], $_lang["resource_overview"], $modx->hasPermission('view_document')); // view
    constructLink(12, $_style["icons_preview_resource"], $_lang["preview_resource"], 1); // preview
    ?>
</div>

</body>
</html>
