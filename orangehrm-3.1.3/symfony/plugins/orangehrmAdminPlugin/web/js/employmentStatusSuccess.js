$(document).ready(function() {
   
    $('#btnSave').click(function() {
        
        $('#frmEmpStatus').submit();
    });
    
    $('#empStatus').hide();
    
    $('#btnAdd').click(function() {
        $('.top').hide();
        
        $('#empStatus').show();
        $('#empStatus_name').val('');
        $('#empStatus_empStatusId').val('');
        $('#empStatusHeading').html(lang_addEmpStatus);
        $(".messageBalloon_success").remove();
    });
    
    $('#btnCancel').click(function() {
        $('.top').show();
        
        $('#empStatus').hide();
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
        var id = $('#empStatus_empStatusId').val();
        var vcCount = empStatusList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == empStatusList[j].id){
                currentStatus = j;
            }
        }
        var i;
        vcName = $.trim($('#empStatus_name').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = empStatusList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentStatus != null){
            if(vcName == empStatusList[currentStatus].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });
    
    var validator = $("#frmEmpStatus").validate({

        rules: {
            'empStatus[name]' : {
                required:true,
                maxlength: 50,
                uniqueName: true
            }
        },
        messages: {
            'empStatus[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors,
                uniqueName: lang_uniqueName
            }

        }

    });
});

function getEmploymentInfo(url){
    
    $.getJSON(url, function(data) {
        $('#empStatus_empStatusId').val(data.id);
        $('#empStatus_name').val(data.name);
        $('#empStatus').show();
        $(".messageBalloon_success").remove();
        $('.top').hide();
    });
}