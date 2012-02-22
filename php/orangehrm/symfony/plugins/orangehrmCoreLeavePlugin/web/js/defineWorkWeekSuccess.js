$(document).ready(function() {
    $('.formSelect[name^="WorkWeek[day_length"]').attr('disabled', true);

    $('#saveBtn').click(function() {                
        if($(this).val() == lang_Edit) {
            $('.formSelect').attr('disabled', false);
            $(this).val(lang_Save);
            return false;
        }

        if($(this).val() == lang_Save) {
            $('#frmWorkWeek').submit();
        }
    });

});