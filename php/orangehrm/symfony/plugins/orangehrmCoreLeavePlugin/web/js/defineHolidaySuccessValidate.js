$(document).ready(function() {
        
    //Validation
    $("#frmHoliday").validate({
        rules: {
            'holiday[date]': {
                required: true,
                valid_date: function(){
                    return {
                        format:datepickerDateFormat,
                        required: true,
                        displayFormat:displayDateFormat
                    }
                }
            },
            'holiday[description]': {
                required: true, 
                maxlength: 200
            }
        },
        messages: {
            'holiday[date]':{
                required:  lang_Required,
                valid_date: lang_DateFormatIsWrong
            },
            'holiday[description]':{
                required: lang_Required,
                maxlength: lang_NameIsOverLimit
            }
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
            error.appendTo(element.next().next('.errorContainer'));
            error.appendTo(element.next('.errorContainer'));
        },
        invalidHandler: function(form, validator) {
            clearTemplateMessages();
        }
    });
});