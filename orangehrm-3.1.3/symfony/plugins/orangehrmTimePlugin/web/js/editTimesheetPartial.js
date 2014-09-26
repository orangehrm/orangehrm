
$(document).ready(function() {
    $msgDelayTime = 3000; // time that set for msg fading...
    $("#extraRows").hide();
    var status;
    $('#btnAddRow').click(function(){
        $("#extraRows").append(addRow(rows-1, startDate, endDate, employeeId, timesheetId));
        $('#extraRows table tr').insertBefore('#extraRows');
        $('#newRow').remove();
        rows = rows + 1;
    });

    $("#submitRemoveRows").click(function(){
        if(!isRowsSelected()){
            _showMessage('warning', lang_noRecords);
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
                    var token = $('#defaultList__csrf_token').val();
                    var r = $.ajax({
                        type: 'POST',
                        url: linkToDeleteRow,
                        data: "timesheetId="+timesheetId+"&activityId="+activityId+"&projectId="+projectId+"&employeeId="+employeeId+"&t="+token,
                        async: false,
                        success: function(state){
                            status=state;
                        }
                    });
                }
            });
            if(status){
                _showMessage('success', lang_removeSuccess);
                $('form#timesheetForm').submit();
            }
            else{
                _showMessage('warning', lang_noChagesToDelete);
            }
        }
        else{
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
                        _showMessage('warning', lang_noChagesToDelete);
                    }
                    else if((array=="") && ($(".toDelete").size()!=1)){
                        $(".messageBalloon_warning").remove();
                        _showMessage('success', lang_removeSuccess);
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
                        _showMessage('success', lang_removeSuccess);
                        $('form#timesheetForm').submit();
                    }
                }
            });
        }
    }); //submitRemoveRows-click
});

function addRow(num, startDate, endDate, employeeId, timesheetId) {
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

function displayMessages(messageType, message) {
    $('#msgDiv').remove();
    if (messageType != 'reset') {
        $divClass = 'message '+messageType;
        $msgDivContent = "<div id='msgDiv' class=' " + $divClass + "' >" + message + 
            "<a class='messageCloseButton' href='#'>"+closeText+"</a>" + "</div>";
        $('#validationMsg').append($msgDivContent);
    }
//    $('#msgDiv').fadeOut($msgDelayTime, function(){
//        $('#msgDiv').remove();
//    });
}

function _showMessage(messageType, message) {  
    _clearMessage();
    $('#validationMsg').append('<div class="message ' + messageType + '" id="divMessageBar" generated="true">'+ message + 
        "<a class='messageCloseButton' href='#'>"+closeText+"</a>" +  '</div>');
}

function _clearMessage() {
    $('#validationMsg div[generated="true"]').remove();
}

function _showMessage(messageType, message) {  
    _clearMessage();
    $('#validationMsg').append('<div class="message ' + messageType + '" id="divMessageBar" generated="true">'+ message + 
        "<a class='messageCloseButton' href='#'>"+closeText+"</a>" +  '</div>');
}

function _clearMessage() {
    $('#validationMsg div[generated="true"]').remove();
}
