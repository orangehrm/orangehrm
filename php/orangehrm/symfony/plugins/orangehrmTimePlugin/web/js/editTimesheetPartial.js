
$(document).ready(function() {
    var status;

    $('#btnAddRow').click(function(){
        $("#extraRows").append(addRow(rows-1,startDate,endDate,employeeId,timesheetId));
        rows = rows + 1;
    });

    $("#submitRemoveRows").click(function(){

        if(!isRowsSelected()){

            $('#validationMsg').attr('class', "messageBalloon_warning");
            $('#validationMsg').html(lang_noRecords);

        }
        else if(isDeleteAllRows()){
                
            $(".toDelete").each(function(){
                element = $(this)

                if($( element).is(':checked')){
      
                    var array=$(element).parent().attr('id').split("_");
                    
                 
                    var projectId=array[0];
                    var activityId=array[1];
                    var timesheetId=array[2];
                    var employeeId=array[3];

                    var r = $.ajax({
                        type: 'POST',
                        url: linkToDeleteRow,
                        data: "timesheetId="+timesheetId+"&activityId="+activityId+"&projectId="+projectId+"&employeeId="+employeeId,
                        async: false,

                        success: function(state){
                            
                            status=state;

                        }
                    });

                
                }

            });
            if(status){
              
            
                $('form#timesheetForm').submit();
            }
            else{
                $('#validationMsg').attr('class', "messageBalloon_warning");
                $('#validationMsg').html(lang_noChagesToDelete);
            }

        }

        else{


            $(".messageBalloon_warning").remove();
            $('#validationMsg').html("");

            $(".toDelete").each(function(){
                element = $(this)


 
                if($( element).is(':checked')){
      


                    var array=$(element).parent().attr('id').split("_");
                    if((array!="") && ($(".toDelete").size()==1)){
                        var projectId=array[0];
                        var activityId=array[1];
                        var timesheetId=array[2];
                        var employeeId=array[3];

                        var r = $.ajax({
                            type: 'POST',
                            url: linkToDeleteRow,
                            data: "timesheetId="+timesheetId+"&activityId="+activityId+"&projectId="+projectId+"&employeeId="+employeeId,
                            async: false,

                            success: function(state){

                            }
                        });

                        $('form#timesheetForm').submit();


                    }

                    else if((array=="") && ($(".toDelete").size()==1)){
   

                        $('#validationMsg').attr('class', "messageBalloon_warning");
                        $('#validationMsg').html("No changes to delete");
                    //error message
                    // $(element).parent().parent().remove();
                    //$('form#timesheetForm').submit();
                    }

                    else if((array=="") && ($(".toDelete").size()!=1)){

                        $(".messageBalloon_warning").remove();
                        $('#validationMsg').html("");
                        $('#validationMsg').attr('class', "messageBalloon_success");
                        $('#validationMsg').html(lang_removeSuccess);
                        $(element).parent().parent().remove();



                    }

                    else if((array!="") && ($(".toDelete").size()!=1)){


                        var projectId=array[0];
                        var activityId=array[1];
                        var timesheetId=array[2];
                        var employeeId=array[3];


                        var r = $.ajax({
                            type: 'POST',
                            url: linkToDeleteRow,
                            data: "timesheetId="+timesheetId+"&activityId="+activityId+"&projectId="+projectId+"&employeeId="+employeeId,
                            async: false,

                            success: function(state){
                        
                            }


                        });
                        //     $('#validationMsg').attr('class', "messageBalloon_success");
                        //		$('#validationMsg').html("Successfully removed");
                        //  $(element).parent().parent().remove();
                        //action:linkForViewTimesheet+"?state=REJECTED"+"&date="+date
                        $('form#timesheetForm').submit();


                    }

                }
            });
        }
    });



});

function addRow(num,startDate,endDate,employeeId,timesheetId) {

    var r = $.ajax({
        type: 'GET',
        url: link ,
        data: "num="+num+"&startDate="+startDate+"&endDate="+endDate+"&employeeId="+employeeId+"&timesheetId="+timesheetId,
        async: false
    }).responseText;
    return r;

}

function isRowsSelected(){
    var count=0;
    var errFlag=false;
    //alert($(".toDelete").size());
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

function isDeleteAllRows(){
    var count=0;
    $(".toDelete").each(function(){
        element = $(this)
    

        if($( element).is(':checked')){
            count=count+1;
        }

        
    });

    
    if($(".toDelete").size()==count){
        
        return true;
        
    }
    else{
        return false;
    }
    
}