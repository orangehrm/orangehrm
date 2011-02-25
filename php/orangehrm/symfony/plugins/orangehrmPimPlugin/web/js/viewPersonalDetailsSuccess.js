$(document).ready(function() {

    daymarker.bindElement("#personal_txtLicExpDate", function() {});
    $('#licExpDateBtn').click(function(){
        daymarker.show("#personal_txtLicExpDate");
    });

    daymarker.bindElement("#personal_DOB", function() {});
    $('#dateOfBirthBtn').click(function(){
        daymarker.show("#personal_DOB");
    });

    //form validation
    $("#frmEmpPersonalDetails").validate({
        rules: {
            'personal[txtEmpFirstName]': {required: true },
            'personal[txtEmpLastName]': { required: true },
            'personal[DOB]': { validdate: true },
            'personal[txtLicExpDate]': { validdate: true },
            'personal[optGender]': { required: true }
        },
        messages: {
            'personal[txtEmpFirstName]': { required: lang_firstNameRequired },
            'personal[txtEmpLastName]': { required: lang_lastNameRequired },
            'personal[DOB]': {validdate: lang_invalidDateOfBirth },
            'personal[txtLicExpDate]': { validdate: lang_invalidLicenseExpDate },
            'personal[optGender]': { required: lang_selectGender }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));
            error.insertAfter(element.parent().parent().next(".clear"));
        }
    });

    //on form loading
    var list = new Array('.formInputText', '#licExpDateBtn', '#dateOfBirthBtn', '#personal_optGender_1', '#personal_optGender_2', '#personal_chkSmokeFlag');
    for(i=0; i < list.length; i++) {
        $(list[i]).attr("disabled", "disabled");
    }

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            for(i=0; i < list.length; i++) {
                $(list[i]).removeAttr("disabled");
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

function validateDate(day, month, year) {
    var days31 = new Array(1,3,5,7,8,10,12);

    if(month > 12 || month < 1) {
        return false;
    }

    if(day == 29 && month == 2) {
        if(year % 4 == 0) {
            return true;
        }
    }

    if(month == 2 && day < 29) {
        return true;
    }
    if(day < 32 && month != 2) {
        if(day == 31) {
            flag = false;
            for(i=0; i < days31.length; i++) {
                if(days31[i] == month) {
                    flag = true;
                    break;
                }
            }
            return flag;
        }
        return true;
    }
    return false;
}