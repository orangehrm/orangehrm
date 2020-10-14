$(document).ready(function () {

    $('#btnAdd').click(function () {
        $('#divAddLanguagePackage').show();
        $('form div.top').hide();
        $(window).scrollTop(0);
    });

    $('#btnSave').click(function () {
        $('#frmAddLanguagePackage').submit();
    });

    $('#btnCancel').click(function () {
        location.reload();
    });

    $("#frmAddLanguagePackage").validate({
        rules: {
            'addLanguagePackage[name]': {
                required: true
            },
        },
        messages: {
            'addLanguagePackage[name]': {
                required: lang_Required
            },
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});
