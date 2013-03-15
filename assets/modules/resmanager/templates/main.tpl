<!doctype html>
<html>
	<head>
		<title>[+lang.RM_module_title+]</title>
		
		<link rel="stylesheet" href="media/style/common/style.css" />
		[+style.css+]
		[+manager.css+]
		
		[+jquery+]
		[+jquery.ui+]
		[+jquery.timepicker+]
		
		<script>
	
			$(document).ready(function($) {
			
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
				
				$("#resmanager-main-tabs").tabs();
				
				
				//tabs and tab history
				(function ($) {
					$("#resmanager-main-tabs").tabs({
						activate: function( event, ui ) {
							//set session storage with the latest selected tab
							var tabsId = $(this).attr('id');
							var panelId = $(ui.newPanel).attr('id');
							if(tabsId && panelId){
								sessionStorage.setItem(tabsId, panelId);
							}
						}
					});
					
					//check if tabs are present and higlight the selected value in session storage
					if($("#resmanager-main-tabs").length){
						var tabsId = $("#resmanager-main-tabs").attr('id');
						//get session storage
						var savedPanelId = sessionStorage.getItem(tabsId);
						if(savedPanelId){
							var index = $('#'+tabsId+' a[href="#'+savedPanelId+'"]').parent().index(); 
							$("#resmanager-main-tabs").tabs("option", "active", index);
						}
					}
				}(jQuery));
				
			});
			
		</script>

		<script type="text/javascript" src="../assets/modules/resmanager/js/resmanager.js"></script>
		
		<script type="text/javascript">
			function loadTemplateVars(tplId) {
				$('#tvloading').show();
				//$('tvloading').style.display = 'block';
				
				jQuery.ajax({
			        type: 'POST',
			        url: '[+ajax.endpoint+]',
			        data: { theme: "[+theme+]", tplID: tplId },
			        success: function(data) {
			            $('#results').html(data);
			            $('#tvloading').hide();
			        }
			    });
				
			}
			
			function save() {
				//submit it
				$('form[name="newdocumentparent"]').submit()
				//document.newdocumentparent.submit();
			}	

			function setMoveValue(pId, pName) {
			  if (pId==0 || checkParentChildRelation(pId, pName)) {
				document.newdocumentparent.new_parent.value=pId;
				$('#parentName').html("Parent: <strong>" + pId + "</strong> (" + pName + ")");
				//$('parentName').innerHTML = "Parent: <strong>" + pId + "</strong> (" + pName + ")";
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
		
		<div id="resmanager-main-tabs" class="js-tabs">
		
			<ul>
				<li><a href="#tabTemplates">[+lang.RM_change_template+]</a></li>
				<li><a href="#tabTemplateVariables">[+lang.RM_template_variables+]</a></li>
				<li><a href="#tabDocPermissions">[+lang.RM_doc_permissions+]</a></li>
				<li><a href="#tabSortMenu">[+lang.RM_sort_menu+]</a></li>
				<li><a href="#tabOther">[+lang.RM_other+]</a></li>
			</ul>
			
			<div id="tabTemplates">
				[+view.templates+]
			</div>
			
			<div id="tabTemplateVariables">
				[+view.templatevars+]
			</div>
			
			<div id="tabDocPermissions">
				[+view.documentgroups+]
			</div>
			
			<div id="tabSortMenu">
				[+view.sort+]
			</div>
			
			<div id="tabOther">
				[+view.misc+]
				[+view.changeauthors+]
			</div>
		</div>
		
	</div>
	[+view.documents+]
	</body>
</html>