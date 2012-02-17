
$(document).ready(function() {
	
	$("#frmRecAttachment").data('add_mode', true);
	
	$('#btnCommentOnly').hide();
	$('#btnAddAttachment').click(function(){
		$('h3#attachmentSubHeading').text(lang_AddAttachmentHeading);
		$('.editLink').hide();
		$("label.error[generated='true']").css('display', 'none');
		$('#recruitmentAttachment_ufile').val('');
		if (clearAttachmentMessages) {
			$("#attachmentsMessagebar").text("").attr('class', "");
		}
		$('#btnCommentOnly').hide();
		$('#recruitmentAttachment_recruitmentId').val('');
		$('#attachmentEditNote').text('');
		$('#recruitmentAttachment_comment').val('');
		$('#addPaneAttachments').show();
		$('#btnAddAttachment').hide();
		$('#btnDeleteAttachment').hide();
		$('#actionButtons').show();
	});
	
	$('#athCancelButton').click(function(){
		validator.resetForm();		
		$('.editLink').show();
		$('#addPaneAttachments').hide();
		$('#btnAddAttachment').show();
		$('#btnDeleteAttachment').show();
		$('#actionButtons').hide();
	});

	//if check all button clicked
	$("#attachmentsCheckAll").click(function() {
		$("table#tblAttachments tbody input.checkboxAtch").removeAttr("checked");
		if($("#attachmentsCheckAll").attr("checked")) {
			$("table#tblAttachments tbody input.checkboxAtch").attr("checked", "checked");
		}
	});

	//remove tick from the all button if any checkbox unchecked
	$("table#tblAttachments tbody input.checkboxAtch").click(function() {
		$("#attachmentsCheckAll").removeAttr('checked');
		if($("table#tblAttachments tbody input.checkboxAtch").length == $("table#tblAttachments tbody input.checkboxAtch:checked").length) {
			$("#attachmentsCheckAll").attr('checked', 'checked');
		}
	});

	$('#btnSaveAttachment').click(function(){
		$('#recruitmentAttachment_vacancyId').val(id);
		$("#frmRecAttachment").data('add_mode', true);
		//		if(isValidForm()){
		$('#frmRecAttachment').submit();
	//		}
	});

	$('#btnDeleteAttachment').click(function() {

		var checked = $('#frmRecDelAttachments input:checked').length;

		if ( checked == 0 )
		{
			$("#attachmentsMessagebar").attr('class', 'messageBalloon_notice').text(lang_SelectAtLeastOneAttachment);
		}
		else
		{
			$('#frmRecDelAttachments').submit();
		}
	});

	$('#frmRecDelAttachments a.editLink').click(function(event) {
		
		event.preventDefault();

		if (clearAttachmentMessages) {
			$("#attachmentsMessagebar").text("").attr('class', "");
		}
		validator.resetForm();
		var row = $(this).closest("tr");
		var seqNo = row.find('input.checkboxAtch:first').val();
		var fileName = row.find('a.fileLink').text();
		var description = row.find("td:nth-child(5)").text();
		description = jQuery.trim(description);
	
		$('#recruitmentAttachment_recruitmentId').val(seqNo);
		$('#attachmentEditNote').html(lang_EditAttachmentReplaceFile + ' <b>' + fileName + '</b> ' + lang_EditAttachmentWithNewFile);
		$('#recruitmentAttachment_ufile').removeAttr("disabled");
		$('#recruitmentAttachment_comment').val(description);
		$('#btnCommentOnly').show();
		$('#addPaneAttachments').show();
		$('#btnAddAttachment').hide();
		$('#btnDeleteAttachment').hide();
		$('#actionButtons').show();
		// hide validation error messages
		$('#attachmentActions').hide();
		$("table#tblAttachments input.checkboxAtch").hide();

		$('h3#attachmentSubHeading').text(lang_EditAttachmentHeading);
		$('#addPaneAttachments').show();
	});

	$('#btnCommentOnly').click(function() {
		$('#recruitmentAttachment_commentOnly').val('1');
		$("#frmRecAttachment").data('add_mode', false);
		//		if(isValidForm()){
		$('#frmRecAttachment').submit();
	//		}
	});

	$.validator.addMethod("attachment", function(value, element, params) {

		var addMode = $("#frmRecAttachment").data('add_mode');
		if (!addMode) {
			return true;
		} else {
			var file = $('#recruitmentAttachment_ufile').val();
			return file != "";
		}
	});

	var validator = $("#frmRecAttachment").validate({

		rules: {
			'recruitmentAttachment[ufile]' : {
				attachment:true
			},
			'recruitmentAttachment[comment]': {
				maxlength: 250
			}
		},
		messages: {
			'recruitmentAttachment[ufile]': lang_PleaseSelectAFile,
			'recruitmentAttachment[comment]': {
				maxlength: lang_CommentsMaxLength
			}
		},

		errorPlacement: function(error, element) {

			error.insertBefore(element.next(".clear"));
			//error.appendTo(element.next('div.errorHolder'));

		}

	});


});
