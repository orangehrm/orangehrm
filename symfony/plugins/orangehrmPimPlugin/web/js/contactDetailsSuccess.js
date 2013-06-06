$(document).ready(function() {
    
    //form validation
    $("#frmEmpContactDetails").validate({
        rules: {
            'contact[emp_hm_telephone]': {phone: true},
            'contact[emp_mobile]': {phone: true},
            'contact[emp_work_telephone]': {phone: true},
            'contact[emp_work_email]' : {
                email: true,
                uniqueWorkEmail: true,
                onkeyup: 'if_invalid'
            },
            'contact[emp_oth_email]': {
                email: true,
                uniqueOtherEmail: true,
                onkeyup: 'if_invalid'
            }

        },
        messages: {
            'contact[emp_hm_telephone]': {phone: invalidHomePhoneNumber},
            'contact[emp_mobile]' : {phone: invalidMobilePhoneNumber},
            'contact[emp_work_telephone]' : {phone: invalidWorkPhoneNumber},
            'contact[emp_work_email]' : {
                email: incorrectWorkEmail,
                uniqueWorkEmail: lang_emailExistmsg
            },
            'contact[emp_oth_email]': {
                email: incorrectOtherEmail,
                uniqueOtherEmail: lang_emailExistmsg
            }
        }
    });

    $.validator.addMethod("phone", function(value, element) {
        return (checkPhone(element));
    });
    
    $.validator.addMethod("uniqueWorkEmail", function(value, element, params) {
        var temp = true;
        var i;
        var currentEmp;
        var empNo = parseInt(empNumber,10);
        var emailCount = emailList.length;
        for (var j=0; j < emailCount; j++) {
            if(empNo == emailList[j].empNo){
                currentEmp = j;
            }
        }
        
        workEmail = $.trim($('#contact_emp_work_email').val()).toLowerCase();
        otherEmail = $.trim($('#contact_emp_oth_email').val()).toLowerCase();
        for (i=0; i < emailCount; i++) {
            if(workEmail != '') {
                if(emailList[i].workEmail) {
                    arrayName1 = emailList[i].workEmail.toLowerCase();
                    if (workEmail == arrayName1) {
                        temp = false
                        break;
                    }
                }
                if(emailList[i].othEmail) {
                    arrayName2 = emailList[i].othEmail.toLowerCase();
                    if (workEmail == arrayName2) {
                        temp = false
                        break;
                    }
                }
                if(workEmail == otherEmail) {
                    temp = false
                    break;
                }
            }
        }
        if(currentEmp != null){
            if(emailList[currentEmp].workEmail != null) {
                if(workEmail == emailList[currentEmp].workEmail.toLowerCase()){
                    temp = true;
                }
            }
        }		
        return temp;
    });
    $.validator.addMethod("uniqueOtherEmail", function(value, element, params) {
        var temp = true;
        var i;
        var currentEmp;
        var empNo = parseInt(empNumber,10);
        var emailCount = emailList.length;
        for (var j=0; j < emailCount; j++) {
            if(empNo == emailList[j].empNo){
                currentEmp = j;
            }
        }
        otherEmail = $.trim($('#contact_emp_oth_email').val()).toLowerCase();
        workEmail = $.trim($('#contact_emp_work_email').val()).toLowerCase();
        for (i=0; i < emailCount; i++) {
            if(otherEmail != '') {
                if(emailList[i].workEmail) {
                    arrayName1 = emailList[i].workEmail.toLowerCase();
                    if (otherEmail == arrayName1) {
                        temp = false
                        break;
                    }
                }
                if(emailList[i].othEmail) {
                    arrayName2 = emailList[i].othEmail.toLowerCase();
                    if (otherEmail == arrayName2) {
                        temp = false
                        break;
                    }
                }
                if(workEmail == otherEmail) {
                    temp = false
                    break;
                }               

            }
        }
        if(currentEmp != null){
            if(emailList[currentEmp].othEmail != null) {
                if(otherEmail == emailList[currentEmp].othEmail.toLowerCase()){
                    temp = true;
                }
            }
        }		
        return temp;
    });
    
    //on form loading
    $("form#frmEmpContactDetails .formInputText").attr("disabled", "disabled");
    setCountryState();

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            $(".formInputText").removeAttr("disabled");
            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            $("#frmEmpContactDetails").submit();
        }
    });

    //on changing of country
    $("#contact_country").change(function() {
        setCountryState();
    });

    function setCountryState() {
        var hide = "display:none;";
        var show = "display:block;";

        $("#contact_state").hide();
        $("#contact_province").show();

        if($("#contact_country").attr('value') == 'US') {
            $("#contact_state").show();
            $("#contact_province").hide();
        }
    }

});