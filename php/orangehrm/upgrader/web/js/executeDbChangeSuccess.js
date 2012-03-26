$(document).ready(function(){
    $('#dbChangeProceedBtn').hide();
    
    $('#dbChangeStartBtn').click(function () {
        $('#dbChangeStartBtn').attr('disabled', "disabled");
        startChangeDb();
    });
    
    var count;
    var status = true;
    
    function startChangeDb () {
        count = 0;
        getJsonResponse(count, tasks.length);
    }
    
    function getJsonResponse(count, length) {
        $.get(upgraderControllerUrl+'?task='+tasks[count], function(data) {
            var myObject = JSON.parse(data);
            if((myObject.progress == 100)) {
                displayProgress(((count + 1)*100)/length);
                if((count + 1) < length) {
                    getJsonResponse(count+1, length);
                } else {
                    changeProceedButtonStatus(status);
                }
            } else {
                alert(lang_failedToUpdate);
                status = false;
            }
        });
    }
});


function changeProceedButtonStatus(status) {
    if (status) {
        $('#dbChangeStartBtn').hide();
        $('#dbChangeProceedBtn').show();
    }
}

function displayProgress(percentage) {
    $("#divProgressBarContainer span span").width(percentage+'%');
    $("#spanProgressPercentage").html(percentage+'%');
}