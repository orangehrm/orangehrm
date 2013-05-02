$(document).ready(function() {

    $('#btnSave').click(function() {
        $('#frmSubscriber').submit();
    });

    $('#subscriber').hide();

    $('#btnAdd').click(function() {
        $('#subscriber').show();
        $('.top').hide();
        $('#subscriber_name').val('');
        $('#subscriber_email').val('');
        $('#subscriber_subscriberId').val('');
        $('#subscriberHeading').html(lang_addSubscriber);
        $(".messageBalloon_success").remove();
    });

    $('#btnBack').click(function() {
        window.location.replace(backBtnUrl);
    });

    $('#btnCancel').click(function() {
        $('#subscriber').hide();
        $('.top').show();
        validator.resetForm();
    });

    $('a[href="javascript:"]').click(function(){
        var row = $(this).closest("tr");
        var statId = row.find('input').val();
        var url = subscriberInfoUrl+statId;
        $('#subscriberHeading').html(lang_editSubscriber);
        getSubscriberInfo(url);

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

    $.validator.addMethod("uniqueEmail", function(value, element, params) {
        var temp = true;
        var currentStatus;
        var id = $('#subscriber_subscriberId').val();
        var subsCount = subscriberList.length;
        for (var j=0; j < subsCount; j++) {
            if(id == subscriberList[j].id){
                currentStatus = j;
            }
        }
        var i;
        var email = $.trim($('#subscriber_email').val()).toLowerCase();
        for (i=0; i < subsCount; i++) {

            var arrayName = subscriberList[i].email.toLowerCase();
            if (email == arrayName) {
                temp = false
                break;
            }
        }
        if(currentStatus != null){
            if(email == subscriberList[currentStatus].email){
                temp = true;
            }
        }

        return temp;
    });

    var validator = $("#frmSubscriber").validate({

        rules: {
            'subscriber[name]' : {
                required:true,
                maxlength: 100
            },
            'subscriber[email]' : {
                required:true,
                maxlength: 100,
                uniqueEmail:true,
                email:true,
                onkeyup: 'if_invalid'                
            }
        },
        messages: {
            'subscriber[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors
            },
            'subscriber[email]' : {
                required: lang_EmailRequired,
                maxlength: lang_exceed50Charactors,
                uniqueEmail:lang_uniqueEmail,
                email: lang_validEmail
            }

        }

    });
});

function getSubscriberInfo(url){

    $.getJSON(url, function(data) {
        $('#subscriber_subscriberId').val(data.id);
        $('#subscriber_name').val(data.name);
        $('#subscriber_email').val(data.email);
        $('#subscriber').show();
        $(".messageBalloon_success").remove();
        $('.top').hide();
    });
}
