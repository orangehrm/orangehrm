$(document).ready(function(){

    $('#saveButton').click(function(){
        $('#frmLeaveType').submit();
    });

    function validate() {
        if ($.trim(element.val()) == '') {
            errorCount++;
            errorMessage = lang_leave_type_name;
            showErrorMessages(element, errorMessage);
        }
    }

    function showErrorMessages(element, errorMessage) {

        errorDisplay = element.siblings('div');
        errorDisplay.append(errorMessage);

    }

});

