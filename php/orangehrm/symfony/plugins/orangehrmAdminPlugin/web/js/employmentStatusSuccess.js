$(document).ready(function() {
   
    //$('#addCustomer_customerId').val(customerId);
    $('#btnSave').click(function() {
        
        $('#frmEmpStatus').submit();
    });
    
    $('#empStatus').hide();
    
    $('#btnAdd').click(function() {
        $('#empStatus').show();
        $('#btnAdd').hide();
        $('#empStatus_name').val('');
        $('#empStatus_empStatusId').val('');
        $('#empStatusHeading').html(lang_addEmpStatus);
        $(".messageBalloon_success").remove();
    });
    
    $('#btnCancel').click(function() {
        $('#empStatus').hide();
        $('#btnAdd').show();
        validator.resetForm();
    });
    
    $('a[href="javascript:"]').click(function(){
		var row = $(this).closest("tr");
		var statId = row.find('input').val();
		var url = empStatusInfoUrl+statId;
        $('#empStatusHeading').html(lang_editEmpStatus);
		getEmploymentInfo(url);

	});
    
        $('#btnDelete').attr('disabled', 'disabled');

        
    $("#ohrmList_chkSelectAll").click(function() {
        if($(":checkbox").length == 1) {
            $('#btnDelete').attr('disabled','disabled');
        }
        else {
            if($("#ohrmList_chkSelectAll").is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        }
    });
    
    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
            $('#btnDelete').removeAttr('disabled');
        } else {
            $('#btnDelete').attr('disabled','disabled');
        }
    });

    $('#btnDelete').click(function(){
        $('#frmList_ohrmListComponent').submit(function(){
            $('#deleteConfirmation').dialog('open');
            return false;
        });
    });

    $("#deleteConfirmation").dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle',
        open: function() {
            $('#dialogCancelBtn').focus();
        }
    });

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });
    
    var validator = $("#frmEmpStatus").validate({

        rules: {
            'empStatus[name]' : {
                required:true,
                maxlength: 50
            }
        },
        messages: {
            'empStatus[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors       
            }

        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
});

function getEmploymentInfo(url){
    
    $.getJSON(url, function(data) {
		$('#empStatus_empStatusId').val(data.id);
		$('#empStatus_name').val(data.name);
		$('#empStatus').show();
		$(".messageBalloon_success").remove();
		$('#btnAdd').hide();
	});
}