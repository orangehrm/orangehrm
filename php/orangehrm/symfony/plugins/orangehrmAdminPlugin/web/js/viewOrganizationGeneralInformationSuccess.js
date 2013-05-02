$(document).ready(function() {

    $.validator.addMethod("phone", function(value, element) {
        return (checkPhone(element));
    });

    $("#frmGenInfo").validate({ 
        rules: {
            'organization[name]': {
                required: true
            },
            'organization[phone]': {
                phone: true
            },
            'organization[fax]': {
                phone: true
            },
            'organization[email]' : {
                email: true,
                onkeyup: 'if_invalid'
            },
            'organization[note]' : {
                maxlength: 250
            }
        },
        messages: {
            'organization[name]': {
                required: nameRequired
            },
            'organization[phone]' : {
                phone: invalidPhoneNumber
            },
            'organization[fax]' : {
                phone: invalidFaxNumber
            },
            'organization[email]' : {
                email: incorrectEmail
            },
            'organization[note]' : {
                maxlength: lang_exceed255Chars
            }
        }
    });
    
    $('#btnSaveGenInfo').click(function() {

        //if user clicks on Edit make all fields editable
        if($("#btnSaveGenInfo").attr('value') == edit) {
            $("#btnSaveGenInfo").attr('value', save)
        }
        else {
            $("#frmGenInfo").submit();
        }
    });
    
});
