$(document).ready(function() {

    $('#btnAdd').click(function(){
        window.location.href = defineLeaveTypeUrl;
    });

    $('#btnDelete').click(function(){

        var checked = $('#frmList_ohrmListComponent input:checked').length;

        $("#messagebar").text("").attr('class', "");

        // Confirm if multiple leave types selected.
        if (checked >= 1) {
            $('#deleteConfirmation').dialog('open');
        } else {
            $("#messagebar").attr('class', "messageBalloon_notice")
                            .text(lang_SelectLeaveTypeToDelete);
        }

        return false;
    });

    $("#deleteConfirmation").dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle',
        open: function() {
            $('#dialogCancelBtn').focus();
        }
    });

    $('#dialogDeleteBtn').click(function() {
        $('#frmList_ohrmListComponent').submit();
    });

    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });

});

