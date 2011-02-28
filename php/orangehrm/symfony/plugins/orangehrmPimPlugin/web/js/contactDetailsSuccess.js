$(document).ready(function() {

    //form validation
    $("#frmEmpContactDetails").validate({
        rules: {
            'contact[emp_hm_telephone]': {phone: true},
            'contact[emp_mobile]': {phone: true},
            'contact[emp_work_telephone]': {phone: true},
            'contact[emp_work_email]' : {email: true},
            'contact[emp_oth_email]': {email: true}

        },
        messages: {
            'contact[emp_hm_telephone]': {phone: invalidHomePhoneNumber},
            'contact[emp_mobile]' : {phone: invalidMobilePhoneNumber},
            'contact[emp_work_telephone]' : {phone: invalidWorkPhoneNumber},
            'contact[emp_work_email]' : {email: incorrectWorkEmail},
            'contact[emp_oth_email]': {email: incorrectOtherEmail}
        },

        errorElement : 'label',
        errorPlacement: function(error, element) {
            error.insertBefore(element.next(".clear"));
        }
    });

    $.validator.addMethod("phone", function(value, element) {
        return (checkPhone(element));
    });

    //on form loading
    $(".formInputText").attr("disabled", "disabled");
    $(".txtBox").attr("disabled", "disabled");
    $(".drpDown").attr("disabled", "disabled");
    $(".txtBoxSmall").attr("disabled", "disabled");

    setCountryState();

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            $(".formInputText").removeAttr("disabled");
            $(".txtBox").removeAttr("disabled");
            $(".drpDown").removeAttr("disabled");
            $(".txtBoxSmall").removeAttr("disabled");
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