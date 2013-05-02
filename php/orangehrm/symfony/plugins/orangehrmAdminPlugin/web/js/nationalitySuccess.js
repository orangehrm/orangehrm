$(document).ready(function() {

    $('#btnSave').click(function() {
        $('#frmNationality').submit();
    });

    $('#nationality').hide();

    $('#btnAdd').click(function() {
        $('#nationality').show();
        $('.top').hide();
        $('#nationality_name').val('');
        $('#nationality_nationalityId').val('');
        $('#nationalityHeading').html(lang_addNationality);
        $(".messageBalloon_success").remove();
    });

    $('#btnCancel').click(function() {
        $('#nationality').hide();
        $('.top').show();
        $('#btnDelete').show();
        validator.resetForm();
    });

    $('a[href="javascript:"]').click(function(){
        var row = $(this).closest("tr");
        var statId = row.find('input').val();
        var url = nationalityInfoUrl+statId;
        $('#nationalityHeading').html(lang_editNationality);
        getNationalityInfo(url);

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
        var id = $('#nationality_nationalityId').val();
        var natCount = nationalityList.length;
        for (var j=0; j < natCount; j++) {
            if(id == nationalityList[j].id){
                currentStatus = j;
            }
        }
        var i;
        var name = $.trim($('#nationality_name').val()).toLowerCase();
        for (i=0; i < natCount; i++) {

            arrayName = nationalityList[i].name.toLowerCase();
            if (name == arrayName) {
                temp = false
                break;
            }
        }
        if(currentStatus != null){
            if(name == nationalityList[currentStatus].name.toLowerCase()){
                temp = true;
            }
        }

        return temp;
    });

    var validator = $("#frmNationality").validate({

        rules: {
            'nationality[name]' : {
                required:true,
                maxlength: 100,
                uniqueName: true
            }
        },
        messages: {
            'nationality[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors,
                uniqueName: lang_uniqueName
            }

        }

    });
});

function getNationalityInfo(url){

    $.getJSON(url, function(data) {
        $('#nationality_nationalityId').val(data.id);
        $('#nationality_name').val(data.name);
        $('#nationality').show();
        $(".messageBalloon_success").remove();
        $('.top').hide();
    });
}