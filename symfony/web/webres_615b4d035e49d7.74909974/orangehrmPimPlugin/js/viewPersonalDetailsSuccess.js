$(document).ready(function() {

    //form validation
    $("#frmEmpPersonalDetails").validate({
        rules: {
            'personal[txtEmpFirstName]': {required: true },
            'personal[txtEmpLastName]': { required: true },
            'personal[DOB]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} } },
            'personal[txtLicExpDate]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} } }
        },
        messages: {
            'personal[txtEmpFirstName]': { required: lang_firstNameRequired },
            'personal[txtEmpLastName]': { required: lang_lastNameRequired },
            'personal[DOB]': { valid_date: lang_invalidDate },
            'personal[txtLicExpDate]': { valid_date: lang_invalidDate }
        }
    });

    $(".editable").each(function(){
        $(this).attr("disabled", "disabled");
    });
    
    // Disable calendar elements
    $(".editable.calendar").datepicker('disable');
    
    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            
            $("#pdMainContainer .editable").each(function(){
                $(this).removeAttr("disabled");
            });            
            
            // Enable calendar elements that are not in readOnlyFields array
            $(".editable.calendar").each(function() {
                var fieldId = $(this).attr('id');
                
                if (fieldId.indexOf('personal_') == 0) {
                    var idWithoutPrefix = fieldId.slice(9);
                    if (-1 == jQuery.inArray(idWithoutPrefix, readOnlyFields)) {
                        $(this).datepicker('enable');
                    }
                }
            });
            
            
            // handle read only fields                
            for (var j = 0; j < readOnlyFields.length; j++) {
                var fieldId = '#personal_' + readOnlyFields[j];
                var field = $(fieldId);
                var fieldName = 'personal['+ readOnlyFields[j]+']';
                
                $('input[name="' + fieldName + '"]').attr('disabled', 'disabled');
                field.attr('disabled', 'disabled');
            }

            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            if ($("#frmEmpPersonalDetails").valid()) {
                $("#btnSave").val(lang_processing);
            }
            $("#frmEmpPersonalDetails").submit();
        }
    });
    });
