var global = 1
$(document).ready(function()
{
    $('#btnDelete').attr('disabled', 'disabled');
    
    $('.toDelete').click(function() {
        if($('.toDelete').is(':checked')) {
            $('#btnDelete').removeAttr('disabled');
        } else {
            $('#btnDelete').attr('disabled','disabled');
        }
    });
    
    $("#dialogOk").click(function() {
        $(".toDelete").each(function(){
            element = $(this)
            if($( element).is(':checked')){
      
                var id=$(element).attr('id');
                  
                if(deleteAttendanceRecords(id)){           
                    $(element).parent().parent().remove();
                }
               
                else{
                    alert("Delete not done properly");
                   
                }
            }
        });
        getRelatedAttendanceRecords(employeeId,date,actionRecorder);  
    });
    
    $(".edit").click(function(){
        $('form#employeeRecordsForm').attr({
            action:linkToEdit+"?employeeId="+employeeId+"&date="+date+"&actionRecorder="+actionRecorder
        });
        $('form#employeeRecordsForm').submit();
        
    });
     
    $("#btnDelete").click(function(){
        if(!isRowsSelected()){
            $('#msg').attr('class', "messageBalloon_warning");
            $('#msg').html(lang_noRowsSelected);

        }
        else{                
            $("#dialogBox").modal();
        }
        
    });
   
    $(".punch").click(function(){
        $('form#employeeRecordsForm').attr({
            action:linkForProxyPunchInOut+"?employeeId="+employeeId+"&date="+date+"&actionRecorder="+actionRecorder
        });
        $('form#employeeRecordsForm').submit();
        
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

function isRowsSelected(){
    var count=0;
    var errFlag=false;
   
    $(".toDelete").each(function(){
        element = $(this)
    
        if($( element).is(':checked')){
            count=count+1;
        }

    });

    if(count==0){
        errFlag=true;


    }
    return !errFlag;

}

function getRelatedAttendanceRecords(employeeId,date,actionRecorder) {
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