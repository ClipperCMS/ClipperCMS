
function changeOtherLabels() {
   
   var choice1 = $('#choice_label_1');
   var choice2 = $('#choice_label_2');

   var miscSetting = $('select#misc').val();

   if (miscSetting == '1') {
		choice1.text($('#option1').val());
		choice2.text($('#option2').val());
   } else if (miscSetting == '2') {
		choice1.text($('#option3').val());
		choice2.text($('#option4').val());
   } else if (miscSetting == '3') {
		choice1.text($('#option5').val());
		choice2.text($('#option6').val());
   } else if (miscSetting == '4') {
		choice1.text($('#option7').val());
		choice2.text($('#option8').val());
   } else if (miscSetting == '5') {
		choice1.text($('#option9').val());
		choice2.text($('#option10').val());
   } else if (miscSetting == '6') {
		choice1.text($('#option11').val());
		choice2.text($('#option12').val());
   } else if (miscSetting == '0') {
       	choice1.text('-');
		choice2.text('-');
    }
}

function postForm() {

	//get active tab
	var tabActiveID = $("#resmanager-main-tabs").tabs('option', 'active');

	if (tabActiveID == '0' || tabActiveID == null) {
		
		//set tab action
		$('#tabaction').val('changeTemplate');
		
		//set template id to be changed
		var selectedTemplateId = $("form[name='template'] input[name='id']:checked").val();
		$('#newvalue').val(selectedTemplateId);

		//submit it
		$('#range').submit();
		
	} else if (tabActiveID == '1') {
	
		//get range of ids from "range" form and update "templatevariables" form field
		$('#pids_tv').val($('#pids').val());

	    //get selected TV
	    var selectedTvId = $("form[name='templatevariables'] input[name='tid']:checked").val();
	    $('#template_id').val(selectedTvId);

	    //submit it
		$('form[name="templatevariables"]').submit();
	    
	} else if (tabActiveID == '2') {
		
		//get action to be applied (add or remove)
	    var tabAction = $("form[name='docgroups'] input[name='tabAction']:checked").val();
		$('#tabaction').val(tabAction);
		
		//get doc group id if any defined
		var newvalue = $("form[name='docgroups'] input[name='docgroupid']:checked").val();
		$('#newvalue').val(newvalue);
		
		//submit it
		$('#range').submit();
		
	} else if (tabActiveID == '3') {
	   
	   /* handled separately using save() function */
	   
	} else if (tabActiveID == '4') {
	
		//set tab action
		$('#tabaction').val('changeOther');
		
		//get misc setting
		var miscSetting = $('select#misc').val();
		$('#setoption').val(miscSetting);
		
		//get misc setting option
		var miscSettingChoice = $("form[name='other'] input[name='choice']:checked").val();
		$('#newvalue').val(miscSettingChoice);

		var publishDate = $('#date_pubdate').val();
		$('#pubdate').val(publishDate);
		
		var unpubdate = $('#date_unpubdate').val();
		$('#unpubdate').val(unpubdate);
		
		var createdon = $('#date_createdon').val();
		$('#createdon').val(createdon);
		
		var editedon = $('#date_editedon').val();
		$('#editedon').val(editedon);
		
		var author_createdby = $('select[name="author_createdby"]').val();
		$('#author_createdby').val(author_createdby);

		var author_editedby = $('select[name="author_editedby"]').val();
		$('#author_editedby').val(author_editedby);

		//submit it
		$('#range').submit();

    }
}

function hideInteraction() {

    //get active tab
    var tabActive = $('#resmanager-main-tabs .ui-tabs-active'),
    	tabActiveID = tabActive.index();
    
    if (tabActiveID == '1') {
        $("#tvloading").hide();
    }
    
    if (tabActiveID == '3') {
    
        if($('#interaction').length) {
        	$('#interaction').hide();
        }
        
        if(parent.tree !== undefined){
        	parent.tree.ca = 'move';
        }
        	
    } else {
        
        $('#interaction').show();
        
        if(parent.tree !== undefined){
        	parent.tree.ca = '';
        }
    }
    
    return true;
}

$(document).ready(function($) {

    hideInteraction();

	$("#resmanager-main-tabs").tabs({
		activate: function( event, ui ) {
			
			hideInteraction();
			
			if (ui.newTab.index() == '3') {
				//hide it if sort menu items
				$('#interaction').hide();
			}else{
				$('#interaction').show();
			}
			
		}
	});

});







