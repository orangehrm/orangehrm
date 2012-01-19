$(document).ready(function() {

    // Add button
    $('#btnAdd').click(function(){
        window.location.href = defineHolidayUrl;
    });

    /* Delete button */
    $('#btnDelete').click(function(){

        var checked = $('#frmList_ohrmListComponent input:checked').length;

        $("#messagebar").text("").attr('class', "");

        // Confirm if multiple holidays selected.
        if (checked >= 1) {
            $('#deleteConfirmation').dialog('open');
        } else {
            $("#messagebar").attr('class', "messageBalloon_notice")
                            .text(lang_SelectHolidayToDelete);
        }
        
        return false;

    });
    
    $("#deleteConfirmation").dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 20,
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

