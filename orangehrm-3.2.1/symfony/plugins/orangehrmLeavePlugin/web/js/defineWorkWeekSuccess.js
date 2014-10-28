$(document).ready(function() {
   

    $('#saveBtn').click(function() {                
        if($(this).val() == lang_Edit) {
            $('#frmWorkWeek select').removeAttr('disabled');
            $(this).val(lang_Save);
            return false;
        }

        if($(this).val() == lang_Save) {
            $('#frmWorkWeek').submit();
        }
    });

});