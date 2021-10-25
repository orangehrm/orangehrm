$(document).ready(function() {

    $('#btnSave').click(function() {
         $('#frmOpenIdProvider').submit();
    });
    $('#openid').hide();
    $('a[href="javascript:"]').click(function(){
        var row = $(this).closest("tr");
        var statId = row.find('input').val();
        var url = providerInfoUrl+statId;
        $('#openidHeading').html(lang_editProvider);
        getMembershipInfo(url);

    });
    
    if ($('#openIdProvider_type').val() == 2) {
        showAuthProvider();
    } else {
        hideAuthProvider();
    }

    $('#openIdProvider_type').change(function () {
        if (this.value == 2) {
            showAuthProvider();
        } else {
            hideAuthProvider();
        }
    });

     var validator = $("#frmOpenIdProvider").validate({

        rules: {
            'openIdProvider[name]' : {
                required:true,
                maxlength: 40,
                uniqueName: true
            },
            'openIdProvider[url]' : {
                required:true,
                url:true
            },
            'openIdProvider[clientId]': {
                validateRequired: true
            },
            'openIdProvider[clientSecret]': {
                validateRequired: true
            },
            'openIdProvider[developerKey]': {
                validateRequired: true
            }
        },
        messages: {
            'openIdProvider[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed40Charactors,
                uniqueName: lang_uniqueName
            },
            'openIdProvider[url]' : {
                required: lang_NameRequired,
                url: lang_url
            },
            'openIdProvider[clientId]': {
                validateRequired: lang_NameRequired
            },
            'openIdProvider[clientSecret]': {
                validateRequired: lang_NameRequired
            },
            'openIdProvider[developerKey]': {
                validateRequired: lang_NameRequired
            }


        }
        
    });
    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentStatus;
        var id = $('#openIdProvider_id').val();
        var memCount = providerList.length;
        for (var j=0; j < memCount; j++) {
            if(id == providerList[j].id){
                currentStatus = j;
            }
        }
        var i;
        var name = $.trim($('#openIdProvider_name').val()).toLowerCase();
        for (i=0; i < memCount; i++) {

            arrayName = providerList[i].name.toLowerCase();
            if (name == arrayName) {
                temp = false
                break;
            }
        }
        if(currentStatus != null){
            if(name == providerList[currentStatus].name.toLowerCase()){
                temp = true;
            }
        }

        return temp;
    });
    $.validator.addMethod("validateRequired", function (value, element) {
        if ($('#openIdProvider_type').val() == 2) {
            if (value.length > 0) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    });
    $.validator.addMethod("validateProviderRequired", function (value, element) {
        if ($('#openIdProvider_type').val() == 1) {
            if (value.length > 0) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    });
    
    $('#btnCancel').click(function() {
        $('#openid').hide();
        $('.top').show();
        $('#btnDelete').show();
        $('.checkbox-col').show();
        $('#resultTable td:nth-child(1)').show();
        validator.resetForm();
    });
    $('#btnAdd').click(function() {
        $('#openid').show();
        $('.top').hide();
         $('#openIdProvider_id').val('');
        $('#openIdProvider_status').val('');
        $('#openIdProvider_name').val('');
        $('#openIdProvider_url').val('');
        $('#openIdProvider_clientId').val('');
        $('#openIdProvider_clientSecret').val('');
        $('#openIdProvider_redirectUrl').val('');
        $('#openIdProvider_developerKey').val('');
        $('#membershipHeading').html(lang_addProvider);
        $(".messageBalloon_success").remove();
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
});
function getMembershipInfo(url){

    $.getJSON(url, function(data) {
        $('#openIdProvider_id').val(data.providerId);
        $('#openIdProvider_status').val(data.status);
        $('#openIdProvider_name').val(data.providerName);
        $('#openIdProvider_url').val(data.providerUrl);
        $('#openIdProvider_type').val(data.provider_type);
        if (data.provider_type == 2) {
            showAuthProvider();
            $("#openIdProvider_clientId").val(data.client_id);
            $("#openIdProvider_clientSecret").val(data.client_secret);
            $("#openIdProvider_developerKey").val(data.developer_key);
        } else {
            hideAuthProvider();
        }
        $('#openid').show();
        $(".messageBalloon_success").remove();
        $('.top').hide();
        $('.checkbox-col').hide();
        $('#resultTable td:nth-child(1)').hide();
        
    });
}

function showAuthProvider() {
    $("#openIdProvider_url").prev().html(lang_redirectUrlLabel);
    $("#openIdProvider_clientId").parent().show();
    $("#openIdProvider_clientSecret").parent().show();
    $("#openIdProvider_developerKey").parent().show();
}

function hideAuthProvider() {
    $("#openIdProvider_url").prev().html(lang_urlLabel);
    $("#openIdProvider_clientId").parent().hide();
    $("#openIdProvider_clientSecret").parent().hide();
    $("#openIdProvider_developerKey").parent().hide();
}