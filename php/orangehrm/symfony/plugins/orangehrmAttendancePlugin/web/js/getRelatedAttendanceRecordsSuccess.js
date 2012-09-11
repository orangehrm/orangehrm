var global = 1
$(document).ready(function()
    {
        $('#msg').removeAttr('class');
        $('#msg').html("");
        $("#dialogBox").dialog({
            autoOpen: false,
            width: 300,
            height: 50
        });

        $("input[name=chkSelectRow[]]").each(function(){
            if($(this).val() == '') {
                $(this).remove();
            }
        });

        $(".cancelBtn").click(function() {
            $(".dialogBox").dialog('close');
        });
    
        $(".okBtn").click(function() {
            $("input[name=chkSelectRow[]]").each(function(){
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
            }
            );
            $(".dialogBox").dialog('close');
//            getRelatedAttendanceRecords(employeeId,date,actionRecorder);
            $("#reportForm").submit();
                
        });
    
        $(".edit").click(function(){
            $('form#employeeRecordsForm').attr({
                action:linkToEdit+"?employeeId="+employeeId+"&date="+date+"&actionRecorder="+actionRecorder
            });
            $('form#employeeRecordsForm').submit();
        
        });
     
        $("#btnDelete").click(function(){
            $('#msg').removeAttr('class');
            $('#msg').html("");
            if(!isRowsSelected()){

                $('#msg').attr('class', "messageBalloon_warning");
                $('#msg').html(lang_noRowsSelected);

            }
            else{
                
                $("#dialogBox").dialog('open');
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
            id:id
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
   
    $("input[name=chkSelectRow[]]").each(function(){
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