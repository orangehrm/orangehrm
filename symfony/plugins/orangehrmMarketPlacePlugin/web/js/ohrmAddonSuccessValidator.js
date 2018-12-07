$(document).ready(function () {
    $('form[id="frmBuyNow"]').validate({
        rules: {
            email: {
                required: true,
                email: true,
                maxlength: 200,
            },
            contactNumber: {
                required: true,
                phone: true,
                maxlength: 30
            },
            organization: {
                required: true,
                maxlength: 100,
            }
        },
        messages: {
            email: {
                required: emailRequired,
                email: emailValidation,
                maxlength: emailValidation,
            },
            contactNumber: {
                required: contactRequired,
                phone: contactValidation,
                maxlength: contactValidation,
            },
            organization: {
                required: organizationRequired,
                maxlength: organizationValidation,
            },
        }
    });
});
