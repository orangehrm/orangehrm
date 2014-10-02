$(document).ready(function() {
    
    
    $('#resultTable tbody td:first-child').each(function(){
      //  $(this).html($(this).text());
    }
    );
        
    //$('#btnAdd').hide();
    
    $('#commentSave').hide();
    $('.dialogInvoker').click(function(){
        var id = $(this).parent().siblings('input[id^="hdnTrackLog_"]').val();
        var comment = $('#hdnTrackLogComment-' + id).val(); 
        $('#trackLogComment').val(comment);
        $("#trackLogComment").attr("disabled","disabled");
        $('#commentDialog').modal();
        
    });
});


function resetMultipleSelectBoxes(){ 
    
    $('#addPerformanceTracker_assignedEmp')[0].options.length = 0;
    $('#addPerformanceTracker_availableEmp')[0].options.length = 0;

    for(var i=0; i<employeeList.length; i++){
        $('#addPerformanceTracker_availableEmp').
        append($("<option></option>").
            attr("value",employeeList[i].id).
            text(employeeList[i].name)); 
    }
}





