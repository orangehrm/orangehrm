$(document).ready(function() {

    $("#photofile").attr('disabled', 'disabled');

    if(showDeteleButton == 0) {
        $("#btnDelete").hide();
    }

    $("#btnSave").click(function() {

        //remove any status msg
        $("#messagebar").text("");
        $("#messagebar").removeAttr("class");

        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            $("#photofile").removeAttr("disabled");
            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            $("#frmPhoto").submit();
        }
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