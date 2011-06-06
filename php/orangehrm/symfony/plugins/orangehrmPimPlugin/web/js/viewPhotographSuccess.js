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

        //remove any status msg
        $("#messagebar").text("");
        $("#messagebar").removeAttr("class");

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

    $("#deleteConfirmation").dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle'
    });

    $("#btnDelete").click(function() {
        //remove any status msg
        $("#messagebar").text("");
        $("#messagebar").removeAttr("class");

        $("#deleteConfirmation").dialog('open');
    });

    $("#btnNo").click(function() {
        $("#deleteConfirmation").dialog('close');
    });

    $("#btnYes").click(function() {
        $("#deleteConfirmation").dialog('close');
        navigateUrl(deleteUrl);
    });
});