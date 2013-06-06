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
            'personal[txtEmpFirstName]':{required: true },
            'personal[txtEmpLastName]': { required: true }
        },
        messages: {
            'personal[txtEmpFirstName]':{
                required: firstNameRequired
            },
            'personal[txtEmpLastName]':{
                required: lastNameRequired
            }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
        }
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
            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            $("#frmEmpPersonalDetails").submit();
        }
    });
});
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
            'personal[txtEmpFirstName]':{required: true },
            'personal[txtEmpLastName]': { required: true }
        },
        messages: {
            'personal[txtEmpFirstName]':{
                required: firstNameRequired
            },
            'personal[txtEmpLastName]':{
                required: lastNameRequired
            }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
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
});