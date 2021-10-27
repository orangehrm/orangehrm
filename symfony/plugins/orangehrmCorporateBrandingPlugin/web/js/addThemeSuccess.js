$(document).ready(function () {
    var initialStyleUpdate = true;
    var _URL = window.URL || window.webkitURL;
    var logoValid = true;
    var bannerValid = true;

    function updatePreview() {
        var mainCssElement = document.getElementById('mainCssElement');
        if (!mainCssElement) {
            mainCssElement = document.createElement("style");
            mainCssElement.setAttribute("type", "text/css");
            document.getElementsByTagName('head')[0].appendChild(mainCssElement);
        }
        $('#disable-screen').addClass('overlay');
        $('#loading').addClass('loading-class');
        var colorVariables = [
            'primaryColor',
            'secondaryColor',
            'buttonSuccessColor',
            'buttonCancelColor'
        ];
        var data = {
            indexIncluded: location.href.indexOf('index.php') > -1
        };
        for (var i = 0; i < colorVariables.length; i++) {
            data[colorVariables[i]] = $('input#' + colorVariables[i]).val();
        }
        $.get(mainCssUrlAjax, data, function(res) {
            mainCssElement.innerHTML = res;
            if (initialStyleUpdate) {
                $('head link[href$="css/main.css"]').remove();
            }
            $('td#preview').show();
            $('#disable-screen').removeClass('overlay');
            $('#loading').removeClass('loading-class');
            initialStyleUpdate = false;
        });
    }

    $("#primaryColor").spectrum({
        showInput: true,
        preferredFormat: true,
        change: function (color) {
            $('#primaryColor').attr('value', color.toHexString());
            updatePreview();
        }
    });

    $("#secondaryColor").spectrum({
        showInput: true,
        preferredFormat: true,
        change: function (color) {
            $('#secondaryColor').attr('value', color.toHexString());
            updatePreview();
        }
    });

    $("#buttonSuccessColor").spectrum({
        showInput: true,
        preferredFormat: true,
        change: function (color) {
            $('#buttonSuccessColor').attr('value', color.toHexString());
            updatePreview();
        }
    });

    $("#buttonCancelColor").spectrum({
        showInput: true,
        preferredFormat: true,
        change: function (color) {
            $('#buttonCancelColor').attr('value', color.toHexString());
            updatePreview();
        }
    });

    $('#btnSave').click(function () {
        if (document.getElementById("socialMediaIcons").checked) {
            document.getElementById("socialMediaIconsHidden").disabled = true;
        }

        if ($('#frmAddTheme').valid() && logoValid && bannerValid) {
            $('#frmAddTheme').submit();
        }
    });

    $('#btnReset').click(function () {
        $('#hdnResetTheme').val('1');
        $('#btnSave').trigger('click');
    });

    $('#file').change(function () {
        $('span.custom-validation-error[for="file"]').remove();
        var file = $('#file')[0].files[0];
        logoValid = !file;
        var img = new Image();
        var maxWidth = 300;
        var maxHeight = 60;

        img.src = _URL.createObjectURL(file);
        img.onload = function() {
            if (this.width > maxWidth || this.height > maxHeight) {
                var message = clientLogoMessage;
                $("<span>").attr('for', 'file').text(message).addClass('custom-validation-error').insertAfter($('#file'));
            } else {
                logoValid = true;
            }
        };
    });

    $('#loginBanner').change(function () {
        $('span.custom-validation-error[for="loginBanner"]').remove();
        var file = $('#loginBanner')[0].files[0];
        bannerValid = !file;
        var img = new Image();
        var maxWidth = 1024;
        var maxHeight = 180;

        img.src = _URL.createObjectURL(file);
        img.onload = function() {
            if (this.width > maxWidth || this.height > maxHeight) {
                var message = loginBannerMessage;
                $("<span>").attr('for', 'loginBanner').text(message).addClass('custom-validation-error').insertAfter($('#loginBanner'));
            } else {
                bannerValid = true;
            }
        };
    });

    var pattern = '#[0-9a-f]{6}$';
    $("#frmAddTheme").validate({
        ignore: [],
        rules: {
            'primaryColor': {required: true, regex: pattern},
            'secondaryColor': {required: true, regex: pattern},
            'buttonSuccessColor': {required: true, regex: pattern},
            'buttonCancelColor': {required: true, regex: pattern},
        },
        messages: {
            'primaryColor': { required: langRequired, regex: langInvalidColor},
            'secondaryColor': { required: langRequired, regex: langInvalidColor},
            'buttonSuccessColor': { required: langRequired, regex: langInvalidColor},
            'buttonCancelColor': { required: langRequired, regex: langInvalidColor}
        }
    });
});
