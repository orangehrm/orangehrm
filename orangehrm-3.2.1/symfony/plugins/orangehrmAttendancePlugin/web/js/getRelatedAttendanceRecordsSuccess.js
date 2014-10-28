var global = 1
$(document).ready(function() {
    $('#btnDelete').attr('disabled', 'disabled');

    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
            $('#btnDelete').removeAttr('disabled');
        } else {
            $('#btnDelete').attr('disabled','disabled');
        }
    });

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
    
    $("input:checkbox[name='chkSelectRow[]']").each(function(){
        if($(this).val() == '') {
            $(this).remove();
        }
    });

    $("#okBtn").click(function() {
        $("input:checkbox[name='chkSelectRow[]']").each(function(){
            element = $(this)
            if($(element).is(':checked')){
                var id = $(element).val();
                if(deleteAttendanceRecords(id)){           
                    $(element).parent().parent().remove();
                }
                else{
                    alert("Delete not done properly");
                }
            }
        });
        $("#reportForm").submit();
    });

    $("#btnEdit").click(function(){
        $('form#frmList_ohrmListComponent').attr({
            action:linkToEdit+"?employeeId="+employeeId+"&date="+date+"&actionRecorder="+actionRecorder
        });
        $('form#frmList_ohrmListComponent').submit();
    });

    $(".punch").click(function(){
        $('form#frmList_ohrmListComponent').attr({
            action:linkForProxyPunchInOut+"?employeeId="+employeeId+"&date="+date+"&actionRecorder="+actionRecorder
        });
        $('form#frmList_ohrmListComponent').submit();
    });
});

function deleteAttendanceRecords(id){
    var r = $.ajax({
        type: 'POST',
        url: linkToDeleteRecords,
        data: {
            id:id,
            'defaultList[_csrf_token]': $('#defaultList__csrf_token').val()
        },
        async: false,
        success: function(status){
            stt=status;
        }
    });
    return stt;
}

function getRelatedAttendanceRecords(employeeId,date,actionRecorder){
    $.post(
        linkForGetRecords,
        {
            employeeId: employeeId,
            date: date,
            actionRecorder:actionRecorder
        },
        function(data, textStatus) {
            if( data != ''){
                $("#recordsTable").show();
                $('#recordsTable1').html(data);    
            }
        });
    return false;
}