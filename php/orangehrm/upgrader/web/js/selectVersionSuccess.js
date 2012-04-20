$(document).ready(function(){
    if ($("#versionInfo_version").val() != '-1') {
        $("#selectVersionSubmit").removeAttr('disabled');
    } else {
        $("#selectVersionSubmit").attr('disabled', 'disabled');
    }
    
    $("#versionInfo_version").change(function() {
        if ($(this).val() != '-1') {
            $("#selectVersionSubmit").removeAttr('disabled');
        } else {
            $("#selectVersionSubmit").attr('disabled', 'disabled');
        }
    });
});