$(document).ready(function($) {

	$('#dob').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRangeType: 'c-90:c+90',
		dateFormat: config.date_format
	});

	if($('#blockeduntil').length) {
		$( '#blockeduntil, #blockedafter').datetimepicker({
			changeMonth: true,
			changeYear: true,
			yearRangeType: 'c-'+config.datepicker_year_range+':c+'+config.datepicker_year_range,
			dateFormat: config.date_format,
			timeFormat: config.time_format
		});
	}

	$( '#pub_date, #unpub_date, input[id^="tv"].DatePicker, #datefrom, #dateto').datetimepicker({
		changeMonth: true,
		changeYear: true,
		yearRangeType: 'c-'+config.datepicker_year_range+':c+'+config.datepicker_year_range,
		dateFormat: config.date_format,
		timeFormat: config.time_format
	});

	//tabs and tab history
	(function ($) {

		//see on document load for the way tabs are remebered

		$(".js-tabs").tabs({
			activate: function( event, ui ) {
				//set session storage with the latest selected tab
				var tabsId = $(this).attr('id');
				var panelId = $(ui.newPanel).attr('id');
				if(tabsId && panelId && config.remember_last_tab){
					sessionStorage.setItem(tabsId, panelId);
				}else{
					sessionStorage.removeItem(tabsId);
				}
			}
		});

		//check if tabs are present and higlight the selected value in session storage if remember_last_tab is configured
		if($(".js-tabs").length){
			var tabsId = $(".js-tabs").attr('id');
			//get session storage
			var savedPanelId = 0;

			if(config.remember_last_tab != '0'){
				savedPanelId = sessionStorage.getItem(tabsId);
			} else {
			    savedPanelId = null;
			}

			if(savedPanelId){
				//activate if only exists, other plugins like MM will like to try activate tabs created by them
				if($('#'+tabsId+' a[href="#'+savedPanelId+'"]').length){
					var index = $('#'+tabsId+' a[href="#'+savedPanelId+'"]').parent().index();
					$(".js-tabs").tabs("option", "active", index);
				}

			}
		}

	}(jQuery));

	$('#search-documents').dataTable({
		"bJQueryUI": true,
		"aoColumns": [
			{"bSortable": false },
			null,
			null,
			null,
			{"bSortable": false}
	     ]
	});

	$('#schedule-all-events').dataTable({
		"bJQueryUI": true
	});

	$('#schedule-publish-events').dataTable({
		"bJQueryUI": true,
		"aaSorting": [[ 2, "asc" ]]
	});
	$('#schedule-unpublish-events').dataTable({
		"bJQueryUI": true,
		"aaSorting": [[ 2, "asc" ]]
	});

	$('#onlineusers').dataTable({
		"bJQueryUI": true
	});

	$('.tooltip').tooltip();

	//TODO: change to datatable ajax pagination - remove old type pagination
	$('#manager-logs').dataTable({
		"bPaginate" : false,
		"bFilter": false,
		"bInfo": false,
		"bJQueryUI": true
	});

	$('.js-confirm-delete, .js-confirm-duplicate').click(function(e){

		var message = '';

		e.preventDefault();

		if($(this).hasClass('js-confirm-delete')){
			message = temp_lang.confirm_delete;
		}

		if($(this).hasClass('js-confirm-duplicate')){
			message = temp_lang.confirm_duplicate;
		}

		if(confirm(message) === true) {
			window.location.href= $(this).attr('href');
			return true;
		}
		return false;
	});

	$('.plugin-execution-order').sortable({
		placeholder: "ui-state-highlight",
		stop: function( event, ui ) {
			var parent = $(ui.item).parent();
			var parentId = parent.attr('id');

			//make list to be send to form field
			var list = [];
			$(parent).find('li').each(function(i){
			   list.push($(this).attr('id'));
			});
			$('#list_' + parentId).val(list.join(','));
		}
	});

	$('#tv-sort-order').sortable({
		placeholder: "ui-state-highlight",
		stop: function( event, ui ) {

			var parent = $(ui.item).parent();
			//make list to be send to form field
			var list = [];
			$(parent).find('li').each(function(i){
			   list.push($(this).attr('id'));
			});

			$('#list').val(list.join(';'));
		}
	});



});

$(window).load(function () {

	//called on load because MM added tabs will never get remebered because they are added after manager.js is called


});
