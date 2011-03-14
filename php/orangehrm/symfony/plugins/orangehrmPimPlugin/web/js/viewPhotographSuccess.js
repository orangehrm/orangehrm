$(document).ready(function() {

    $(".formInputText").attr('disabled', 'disabled');

    var imgHeight = $("#empPic").attr("height");
    var imgWidth = $("#empPic").attr("width");
    var newHeight = 0;
    var newWidth = 0;

    //algorithm for image resizing
    //resizing by width - assuming width = 150,
    //resizing by height - assuming height = 180

    var propHeight = Math.floor((imgHeight/imgWidth) * 150);
    var propWidth = Math.floor((imgWidth/imgHeight) * 180);

    if(propHeight <= 180) {
        newHeight = propHeight;
        newWidth = 150;
    }

    if(propWidth <= 150) {
        newWidth = propWidth;
        newHeight = 180;
    }

    if(fileModified == 1) {
        newWidth = newImgWidth;
        newHeight = newImgHeight;
    }

    $("#empPic").attr("height", newHeight);
    $("#empPic").attr("width", newWidth);

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