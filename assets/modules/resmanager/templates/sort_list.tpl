<!doctype html>
<html>
<head>
    <title>[+lang.RM_module_title+]</title>
    
    <link rel="stylesheet" href="media/style/common/clipper-jquery-ui.css" />
    <link rel="stylesheet" href="media/style/common/style.css" />
	[+style.css+]
	[+manager.css+]
    
    [+jquery+]
	[+jquery.ui+]
	[+jquery.timepicker+]
    
    <script src="../assets/modules/resmanager/js/resmanager.js"></script>
    <script>
	    function save() { 
		    //populateHiddenVars(); 
		    setTimeout("document.sortableListForm.submit()",1000); 
	    }
	    
	    function reset() {
	       document.resetform.submit();
	    }
	    
	    $(document).ready(function($) {
	    
	    	$('#sortlist').sortable({
				placeholder: "ui-state-highlight",
				axis: "y",
				stop: function( event, ui ) {
				
					var parent = $(ui.item).parent()
					//make list to be send to form field
					var list = [];
					$(parent).find('li').each(function(i){
					   list.push($(this).attr('id'));
					});
					
					$('#list').val(list.join(';'));
				}
			});
	    
	    
	    	if ([+sort.disable_tree_select+] == true) {
	           parent.tree.ca = '';
	        }
	       
	    });
   
	    parent.tree.updateTree();
	    
    </script>
    <style>        
        li.sort {
            padding-left: 30px;
        }
        li.noChildren {
            background-image: url(media/style[+theme+]/images/tree/page.gif);
            background-repeat: no-repeat;
            background-position: 5px center;
        }
        li.hasChildren {
            background-image: url(media/style[+theme+]/images/tree/folder.gif);
            background-repeat: no-repeat;
            background-position: 5px center;
        }
    </style>
</head>
<body>
    <h1>[+lang.RM_module_title+]</h1>
    <form action="" method="post" name="resetform" style="display: none;">
        <input name="actionkey" type="hidden" value="0" />
    </form>
    <div id="actions">
        <ul class="actionButtons">
            <li id="Button1"><a href="#" onclick="reset();"><img src="media/style[+theme+]/images/icons/stop.png" align="absmiddle"> [+lang.RM_close+]</a></li>
            <li id="Button2" style="display:[+sort.save+]"><a href="#" onclick="save();"><img src="media/style[+theme+]/images/icons/save.png" align="absmiddle"> [+lang.RM_save+]</a></li>
            <li id="Button4"><a href="#" onclick="reset();"><img src="media/style[+theme+]/images/icons/cancel.png" align="absmiddle"> [+lang.RM_cancel+]</a></li>
        </ul>
    </div>
        
    <div class="sectionHeader">&nbsp;</div>
    <div class="sectionBody">
        [+sort.message+]
        <ul id="sortlist" class="sortableList">
            [+sort.options+]
        </ul>
	    <form action="" method="post" name="sortableListForm" style="display: none;">
            <input type="hidden" name="tabAction" value="sortList" />
            <input type="text" id="list" name="list" value="" />
        </form>
    </div>
</body>
</html>
