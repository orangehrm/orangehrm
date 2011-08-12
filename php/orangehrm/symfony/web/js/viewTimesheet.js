$(document).ready(function(){

    $("#commentDialog").dialog({
                autoOpen: false,
                width: 350,
                height: 225
        });

    $("#btnEdit").click(function(){
        $('form#timesheetFrm').attr({
            action:linkForEditTimesheet+"?employeeId="+employeeId+"&timesheetId="+timesheetId+"&actionName="+actionName
        });
        $('form#timesheetFrm').submit();
    });

    $("#btnSubmit").click(function(){
        $('form#timesheetFrm').attr({
            //action:linkForViewTimesheet+"?state=SUBMITTED"+"&date="+date
            action:linkForViewTimesheet+"?state="+submitNextState+"&timesheetStartDate="+date+"&employeeId="+employeeId+"&submitted="+true+"&updateActionLog="+true
            });
        $('form#timesheetFrm').submit();
    });

    $("#btnReject").click(function(){
       
          if(validateComment()){

        $('form#timesheetFrm').attr({
            //action:linkForViewTimesheet+"?state=REJECTED"+"&date="+date
            action:linkForViewTimesheet+"?state="+rejectNextState+"&timesheetStartDate="+date+"&employeeId="+employeeId+"&updateActionLog="+true
            });
        $('form#timesheetFrm').submit();
          }
    });

    $("#btnReset").click(function(){
        $('form#timesheetFrm').attr({
            //action:linkForViewTimesheet+"?state=SUBMITTED"+"&date="+date
            action:linkForViewTimesheet+"?state="+resetNextState+"&timesheetStartDate="+date+"&employeeId="+employeeId+"&updateActionLog="+true+"&resetAction="+true
            });
        $('form#timesheetFrm').submit();
    });

    $("#btnApprove").click(function(){
        if(validateComment()){
        $('form#timesheetFrm').attr({
            //action:linkForViewTimesheet+"?state=APPROVED"+"&date="+date
            action:linkForViewTimesheet+"?state="+approveNextState+"&timesheetStartDate="+date+"&employeeId="+employeeId+"&updateActionLog="+true
            });
        $('form#timesheetFrm').submit();
        }
    });
     $("#commentCancel").click(function() {
                $("#commentDialog").dialog('close');
        });


//$('#txtComment').change(function() {
//
//
//    var flag= validateComment();
//
//    if(!flag) {
//        $('#btnApprove').attr('disabled', 'disabled');
//        $('#btnReject').attr('disabled', 'disabled');
//        $('#validationMsg').attr('class', "messageBalloon_failure");
//    }
//    else{
//        $('#btnApprove').removeAttr('disabled');
//        $('#btnReject').removeAttr('disabled');
//        $("#txtComment").removeAttr('style');
//    }
//
//
//});


$(".icon").click(function(){

      $("#timeComment").attr("disabled","disabled");
        //removing errors message in the comment box
        $("#commentError").html("");
        var array = ($(this).attr('id')).split('##');
        timesheetItemId = array[0];

        var comment = getComment(timesheetItemId);
         var decoded = $("<div/>").html(comment).text();
        $("#timeComment").val(decoded);
        $("#commentProjectName").text(":"+" "+array[1]);
        $("#commentActivityName").text(":"+" "+array[2]);
        $("#commentDate").text(":"+" "+date);
        $("#commentDialog").dialog('open');


});



});


var timesheetItemId;
var question;
//var date;
var close= "close";

function clicked(dropdown){

    var selectedIndex = document.getElementById('startDates').value;
    var dateString = document.getElementById('startDates').options[selectedIndex].text;
    var dates = dateString.split(" ");

    location.href = linkForViewTimesheet+"?timesheetStartDateFromDropDown="+dates[0]+"&selectedIndex="+selectedIndex+"&employeeId="+employeeId;
    //document.getElementById('startDates').value
}

function getComment(timesheetItemId){
    var r = $.ajax({
        type: 'POST',
        url: linkToViewComment,
        data: "timesheetItemId="+timesheetItemId,
        async: false,

        success: function(msg){
            var array = msg.split('##');
            question = array[0];
            date = array[1];

        }
    });
    return question;
}

function viewComment(e){

        $("#timeComment").attr("disabled","disabled");
        //removing errors message in the comment box
        $("#commentError").html("");
        var array = ($(e.target).attr('id')).split('##');
        timesheetItemId = array[0];
        var comment = getComment(timesheetItemId);
        $("#timeComment").val(comment);
        $("#commentProjectName").text(":"+" "+array[1]);
        $("#commentActivityName").text(":"+" "+array[2]);
        $("#commentDate").text(":"+" "+date);
        $("#commentDialog").dialog('open');

}








function validateComment(){

    errFlag1 = false; 
    $('#btnApprove').removeAttr('disabled');
        $('#btnReject').removeAttr('disabled');
        $("#txtComment").removeAttr('style');
    // $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");

    var errorStyle = "background-color:#FFDFDF;";

    if ($("#txtComment").val().length > 250) {
        $('#btnReject').attr('disabled', 'disabled');
        $('#btnApprove').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(erorrMessageForInvalidComment);
        $("#txtComment").attr('style', errorStyle);

        errFlag1 = true;
    }

    return !errFlag1;

}