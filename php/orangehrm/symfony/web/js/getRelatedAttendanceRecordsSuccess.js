$(document).ready(function()
    {
         $('#msg').removeAttr('class');
        $('#msg').html("");
        $("#dialogBox").dialog({
            autoOpen: false,
            width: 350,
            height: 235
        });

        $("#cancel").click(function() {
            $("#dialogBox").dialog('close');
        });
    
        $("#ok").click(function() {
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
            }
                    

            );
            alert("hi")
            $("#dialogBox").dialog('close');
         //   getRelatedAttendanceRecords(employeeId,date);
                
        });
    
        $(".edit").click(function(){
            $('form#employeeRecordsForm').attr({
                action:linkToEdit+"?employeeId="+employeeId+"&date="+date+"&actionName="+actionName
            });
            $('form#employeeRecordsForm').submit();
        
        });
     
        $(".delete").click(function(){
            $('#msg').removeAttr('class');
            $('#msg').html("");
            if(!isRowsSelected()){

                $('#msg').attr('class', "messageBalloon_warning");
                $('#msg').html("No Rows Selected");

            }
            else{
                
                $("#dialogBox").dialog('open');
            }
        
        });
   
    $(".punch").click(function(){
            $('form#employeeRecordsForm').attr({
                action:linkForProxyPunchInOut+"?employeeId="+employeeId+"&date="+date+"&actionName="+actionName
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

function getRelatedAttendanceRecords(employeeId,date){
        
    $.post(
        linkForGetRecords,
        {
            employeeId: employeeId,
            date: date
        },
        
        function(data, textStatus) {
                      
            if( data != ''){
                $("#recordsTable").show();
                $('#recordsTable1').html(data);    
            }
                    
        });
                    
    return false;
        
}