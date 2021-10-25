$(document).ready(function() {

    $("#btnSave").attr('disabled', 'disabled');

    $("#photofile").change(function(){
        if ($(this).val() != '') {
            $("#btnSave").removeAttr("disabled");
        }
    });

    if(showDeteleButton == 0) {
        $("#btnDelete").hide();
    }

    $("#btnSave").click(function() {
        $("#frmPhoto").submit();
    });

    //form validation
    $("#frmPhoto").validate({
        rules: {
            'photofile': {required: true}},
        messages: {
            'photofile': {required: lang_photoRequired }},

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next().next(".clear"));

        }
    });

    $.validator.addMethod("fileformat", function(value, element) {
        var dotPosition = value.lastIndexOf(".");
        var strLength = value.length;
        var fileExtension = value.substr(dotPosition, (strLength - dotPosition));

        if(fileExtension != ".jpeg" && fileExtension != ".jpg" && fileExtension != ".png" && fileExtension != ".gif") {

            return false;
        }
        return true;
    });

    $("#btnYes").click(function() {
        window.location.href = deleteUrl;
    });
});