$(document).ready(function() {

    //Load default Mask if empty

    var licenseExpiryDate = $("#personal_txtLicExpDate");

    if(trim(licenseExpiryDate.val()) == ''){
        licenseExpiryDate.val(dateDisplayFormat);
    }

    var dateOfBirth = $("#personal_DOB");

    if(trim(dateOfBirth.val()) == ''){
        dateOfBirth.val(dateDisplayFormat);
    }

    //form validation
    $("#frmEmpPersonalDetails").validate({
        rules: {
            'personal[txtEmpFirstName]': {required: true },
            'personal[txtEmpLastName]': { required: true },
            'personal[DOB]': { required: false, valid_date: function(){ return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false} } },
            'personal[txtLicExpDate]': { required: false, valid_date: function(){ return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false} } },
            'personal[optGender]': { required: true }
        },
        messages: {
            'personal[txtEmpFirstName]': { required: lang_firstNameRequired },
            'personal[txtEmpLastName]': { required: lang_lastNameRequired },
            'personal[DOB]': { valid_date: lang_invalidDate },
            'personal[txtLicExpDate]': { valid_date: lang_invalidDate },
            'personal[optGender]': { required: lang_selectGender }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));
            error.insertAfter(element.parent().parent().next(".clear"));
        }
    });

    daymarker.bindElement("#personal_txtLicExpDate",
        {onSelect: function(date){
            $("#personal_txtLicExpDate").valid();
            },
        dateFormat:jsDateFormat
        });

    $('#licExpDateBtn').click(function(){
        daymarker.show("#personal_txtLicExpDate");
    });

    daymarker.bindElement("#personal_DOB",
        {onSelect: function(date){
            $("#personal_DOB").valid();
            },
        dateFormat:jsDateFormat
        });

    $('#dateOfBirthBtn').click(function(){
        daymarker.show("#personal_DOB");
    });

//on form loading
    var list = new Array('form#frmEmpPersonalDetails .formInputText', '#licExpDateBtn', '#dateOfBirthBtn', '#personal_optGender_1', '#personal_optGender_2', '#personal_chkSmokeFlag');
    for(i=0; i < list.length; i++) {
        $(list[i]).attr("disabled", "disabled");
    }

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            for(i=0; i < list.length; i++) {
                $(list[i]).removeAttr("disabled");
            }

            //making readonly fields for ESS users
            if(readonlyFlag) {
                $("#personal_txtEmployeeId").attr('disabled', 'disabled');
                $("#personal_txtNICNo").attr('disabled', 'disabled');
                $("#personal_txtSINNo").attr('disabled', 'disabled');
                $("#personal_txtLicenNo").attr('disabled', 'disabled');
                $("#personal_DOB").attr('disabled', 'disabled');
                $('#dateOfBirthBtn').attr('disabled', 'disabled');
            }

            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            $("#frmEmpPersonalDetails").submit();
        }
    });

    $.validator.addMethod("validdate", function(value, element) {
        if(value == "") {
            return true;
        }
        var dt = value.split("-");
        return validateDate(parseInt(dt[2], 10), parseInt(dt[1], 10), parseInt(dt[0], 10));
    });
});