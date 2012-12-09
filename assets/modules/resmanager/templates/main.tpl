<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>[+lang.RM_module_title+]</title>
        <link rel="stylesheet" type="text/css" href="media/style[+theme+]/style.css" />
        <link rel="stylesheet" type="text/css" href="media/style/[+theme+]/jquery-ui/jquery-ui-1.9.2.custom.min.css" />
        <script type="text/javascript" src="media/script/tabpane.js"></script>
        <!-- <script type="text/javascript" src="media/script/datefunctions.js"></script> -->
        <script type="text/javascript" src="media/script/mootools/mootools.js"></script>
        <script type="text/javascript" src="media/script/mootools/moodx.js"></script>
        <script type="text/javascript" src="../assets/modules/resmanager/js/resmanager.js"></script>
        <script type="text/javascript">
            function loadTemplateVars(tplId) {
			    $('tvloading').style.display = 'block';
			    new Ajax('[+ajax.endpoint+]', {
			        update: 'results',
			        method: 'post',
			        postBody: 'theme=[+theme+]&tplID=' + tplId,
			        evalScripts: true,
			        onComplete: function(r) {
			            $('tvloading').style.display = 'none';
			        }
			    
			    }).request();
			}
			
		    function save() {
                document.newdocumentparent.submit();
            }   

		    function setMoveValue(pId, pName) {
		      if (pId==0 || checkParentChildRelation(pId, pName)) {
		        document.newdocumentparent.new_parent.value=pId;
		        $('parentName').innerHTML = "Parent: <strong>" + pId + "</strong> (" + pName + ")";
		      }
		    }

			function checkParentChildRelation(pId, pName) {
			    var sp;
			    var id = document.newdocumentparent.id.value;
			    var tdoc = parent.tree.document;
			    var pn = (tdoc.getElementById) ? tdoc.getElementById("node"+pId) : tdoc.all["node"+pId];
			    if (!pn) return;
			        while (pn.p>0) {
			            pn = (tdoc.getElementById) ? tdoc.getElementById("node"+pn.p) : tdoc.all["node"+pn.p];
			            if (pn.id.substr(4)==id) {
			                alert("Illegal Parent");
			                return;
			            }
			        }
			    
			    return true;
			}
        </script>
        
        <script src="../assets/js/jquery.min.js" type="text/javascript"></script>
	    <script src="../assets/js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
	    <script src="../assets/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
	    
	    <script type="text/javascript">
	
	    	$.noConflict();
			jQuery(document).ready(function($) {
			
				$('#date_pubdate, #date_unpubdate, #date_createdon, #date_editedon, input.DatePicker').datetimepicker({
					changeMonth: true,
					changeYear: true,
					yearRangeType: 'c-'+[+datepicker.year_range+]+':c+'+[+datepicker.year_range+],
					dateFormat: '[+date.format+]',
					timeFormat: '[+time.format+]'
			    });
			    
			    $('input[id^=tv].DatePicker').live('focus', function(){
				    $(this).datetimepicker({
						changeMonth: true,
						changeYear: true,
						yearRangeType: 'c-'+[+datepicker.year_range+]+':c+'+[+datepicker.year_range+],
						dateFormat: '[+date.format+]',
						timeFormat: '[+time.format+]'
				    });
			    });	
			});
	        
        </script>

    </head>
    <body>
        <h1>[+lang.RM_module_title+]</h1>
        <div id="actions">
            <ul class="actionButtons">
                <li id="Button1"><a href="#" onclick="document.location.href='index.php?a=106';"><img src="media/style[+theme+]/images/icons/stop.png" /> [+lang.RM_close+]</a></li>
            </ul>
        </div>        
	    <div class="sectionHeader">[+lang.RM_action_title+]</div>
	    <div class="sectionBody"> 
	        <div class="tab-pane" id="resManagerPane"> 
	        <script type="text/javascript"> 
	            tpResources = new WebFXTabPane(document.getElementById('resManagerPane')); 
	        </script>
	        
	        <div class="tab-page" id="tabTemplates">  
	            <h2 class="tab">[+lang.RM_change_template+]</h2>  
	            <script type="text/javascript">tpResources.addTabPage(document.getElementById('tabTemplates'));</script>
	           [+view.templates+]
	        </div>
	   
	        <div class="tab-page" id="tabTemplateVariables">  
	            <h2 class="tab">[+lang.RM_template_variables+]</h2>  
	            <script type="text/javascript">tpResources.addTabPage(document.getElementById("tabTemplateVariables" ));</script> 
	           [+view.templatevars+]
	        </div>
	    
	        <div class="tab-page" id="tabDocPermissions">  
	            <h2 class="tab">[+lang.RM_doc_permissions+]</h2>  
	            <script type="text/javascript">tpResources.addTabPage(document.getElementById("tabDocPermissions"));</script> 
	           [+view.documentgroups+]
	        </div>
	      
	        <div class="tab-page" id="tabSortMenu">  
	            <h2 class="tab">[+lang.RM_sort_menu+] </h2>  
	            <script type="text/javascript">tpResources.addTabPage(document.getElementById("tabSortMenu"));</script> 
	           [+view.sort+]
	        </div>
	
	        <div class="tab-page" id="tabOther">  
	           <h2 class="tab">[+lang.RM_other+]</h2>  
	           <script type="text/javascript">tpResources.addTabPage(document.getElementById("tabOther"));</script>
	           [+view.misc+]
	           [+view.changeauthors+]
	        </div>
	    </div>
	</div>
	[+view.documents+]
    </body>
</html>