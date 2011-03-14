$(document).ready(function() {

    $(".formInputText").attr('disabled', 'disabled');

    if(showDeteleButton == 0) {
        $("#btnDelete").hide();
    }

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            $(".formInputText").removeAttr("disabled");
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
            'photofile': {required: lang_photoRequired}},

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));

        }
    });

    $("#deleteConfirmation").dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle'
    });

    $("#btnDelete").click(function() {
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