$(document).ready(function() {

    $('#btnSave').click(function() {
        $('#frmMembership').submit();
    });

    $('#membership').hide();

    $('#btnAdd').click(function() {
        $('#membership').show();
        $('.top').hide();
        $('#membership_name').val('');
        $('#membership_membershipId').val('');
        $('#membershipHeading').html(lang_addMembership);
        $(".messageBalloon_success").remove();
    });

    $('#btnCancel').click(function() {
        $('#membership').hide();
        $('.top').show();
        $('#btnDelete').show();
        validator.resetForm();
    });

    $('a[href="javascript:"]').click(function(){
        var row = $(this).closest("tr");
        var statId = row.find('input').val();
        var url = membershipInfoUrl+statId;
        $('#membershipHeading').html(lang_editMembership);
        getMembershipInfo(url);

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

    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentStatus;
        var id = $('#membership_membershipId').val();
        var memCount = membershipList.length;
        for (var j=0; j < memCount; j++) {
            if(id == membershipList[j].id){
                currentStatus = j;
            }
        }
        var i;
        var name = $.trim($('#membership_name').val()).toLowerCase();
        for (i=0; i < memCount; i++) {

            arrayName = membershipList[i].name.toLowerCase();
            if (name == arrayName) {
                temp = false
                break;
            }
        }
        if(currentStatus != null){
            if(name == membershipList[currentStatus].name.toLowerCase()){
                temp = true;
            }
        }

        return temp;
    });

    var validator = $("#frmMembership").validate({

        rules: {
            'membership[name]' : {
                required:true,
                maxlength: 50,
                uniqueName: true
            }
        },
        messages: {
            'membership[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors,
                uniqueName: lang_uniqueName
            }

        }
        
    });
});

function getMembershipInfo(url){

    $.getJSON(url, function(data) {
        $('#membership_membershipId').val(data.id);
        $('#membership_name').val(data.name);
        $('#membership').show();
        $(".messageBalloon_success").remove();
        $('.top').hide();
    });
}