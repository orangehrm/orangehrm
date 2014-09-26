$(document).ready(function(){
    $('#errorDisplay').hide();
    $('#logContainer').hide();
    $('#dbChangeProceedBtn').hide();
    
    $('#errorDisplay span a').click(function (event) {
        event.preventDefault();
        showErrorLog();
    });
    
    $('#dbChangeStartBtn').click(function () {
        $('#dbChangeStartBtn').attr('disabled', "disabled");
        $('<label id="processingLabel">Processing...</label>').insertAfter('#spanProgressPercentage');
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
                $('#errorDisplay').show();
                $('#processingLabel').remove();
                status = false;
            }
        });
    }
});

function showErrorLog() {
    $('#logContainer').show();
    $.get(showLogUrl, function(data) {
        var myObject = JSON.parse(data);
        $('#logContainer textarea').html(myObject.log);
    });
}

function changeProceedButtonStatus(status) {
    if (status) {
        $('#dbChangeStartBtn').hide();
        $('#processingLabel').remove();
        $('#dbChangeProceedBtn').show();
    }
}

function displayProgress(percentage) {
    percentage = (Math.round(percentage)).toFixed(2)
    $("#divProgressBarContainer span span").width(percentage+'%');
    $("#spanProgressPercentage").html(percentage+'%');
}