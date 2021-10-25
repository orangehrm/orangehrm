$(document).ready(function() {

    $('#btnSave').click(function() {
         $('#frmOpenIdProvider').submit();
        $('#oauth_client_update').val( '');
    });


    $('#oauth_client_update').hide();

    $("#resultTable tr").click(function(){
        $(this).addClass('selected').siblings().removeClass('selected');
        setUpdateValues($(this));

    });

    $("#resultTable tr td:first-child").not($("td:has(input)")).parent().find('a').css({
        "text-decoration": "none",
        "cursor": "default"
    });

    $('#btnCancel').click(function() {
        $('#openid').hide();
        $('.top').show();
        $('#btnDelete').show();
        $('.checkbox-col').show();
        $('#resultTable td:nth-child(1)').show();
        validator.resetForm();
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

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });

    $('#entitlements_employee_empName').result(function(event, item) {
        $('#entitlements_employee_empId').val(item.id)
            .data('item.name', item.name)
            .valid();
    });

    $('#oAuthClientRegistrationForm').validate({
        ignore: [],
        rules: {
            'oauth[client_id]' : {
                required:true,
                maxlength: 80
            },
            'oauth[client_secret]' : {
                required:true,
                maxlength: 80
            },
            'oauth[redirect_uri]' : {
                maxlength: 2000
            }

        },
        messages: {
            'oauth[client_id]': {
                required: lang_required,
                maxlength: lang_LengthExceeded80

            },
            'oauth[client_secret]': {
                required: lang_required,
                maxlength: lang_LengthExceeded80
            },
            'oauth[redirect_uri]': {
                maxlength: lang_LengthExceeded2000
            }

        }

    });



});

function setUpdateValues(selectedTableRow){

    $(selectedTableRow).find('td').each (function( column, td) {

        if(column == 0) {
            var clientId =  $(td).html();
            $('#oauth_client_id').val($(clientId).val());
        }
        if(column == 2) {
            $('#oauth_client_secret').val( $(td).html());
        }
        if(column == 3) {
            $('#oauth_redirect_uri').val( $(td).html());
        }

    });

    $('#oauth_client_update').val( 'update');


}

function resetFields(){
    $('#oauth_client_id').val('');
    $('#oauth_client_secret').val('');
    $('#oauth_redirect_uri').val('');
}