var installId;
var uninstallId;
$(document).ready(function () {
    var acc = document.getElementsByClassName("accordion");
    var i;
    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
    $('.accordion').click(function () {
        var addId = $(this).attr('addonid');
        $.ajax({
            method: "POST",
            data: {addonID: addId},
            url: descriptionUrl, success: function (result) {
                $("#des" + addId).html(result);
            }
        });
    });
    $('.installBtn').live('click', function () {
        installId = $(this).attr('addid');
    });
    $('.uninstallBtn').live('click', function () {
        uninstallId = $(this).attr('addid');
    });
    $('#modal_confirm_install').click(function () {
        $('#installButton' + installId).attr('value', 'Installing');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {installAddonID: installId},
            url: installUrl, success: function (result) {
                if (result === '"Success"') {
                    $('#loading').attr('class', '')
                    $('#disable-screen').attr('class', '');
                    $('#message_body').text(meassageInSuccess);
                    $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    $('#installButton' + installId).attr({
                        'class': 'buttons delete uninstallBtn',
                        'value': 'Uninstall',
                        'id': 'uninstallButton' + installId,
                        'data-target': '#deleteConfModal'
                    });
                } else {
                    $('#message_body').text(messaegeInFail);
                    $('#installButton' + installId).attr('value', 'Install');
                    $('#messege_div').attr('class', 'message error').show(0).delay(2000).fadeOut(1000);
                    $('#disable-screen').attr('class', '');
                    $('#loading').attr('class', '');
                }
            }
        });
    });
    $('#modal_confirm_uninstall').click(function () {
        $('#uninstallButton' + uninstallId).attr('value', 'Uninstalling');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {uninstallAddonID: uninstallId},
            url: uninstallUrl, success: function (result) {
                if (result === '"Success"') {
                    $('#loading').attr('class', '');
                    $('#disable-screen').attr('class', '');
                    $('#message_body').text(meassageUninSuccess);
                    $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    $('#uninstallButton' + uninstallId).attr({
                        'class': 'buttons installBtn',
                        'value': 'Install',
                        'id': 'installButton' + uninstallId,
                        'data-target': '#installConfModal'
                    });
                } else {
                    $('#message_body').text(meassageUninFail);
                    $('#uninstallButton' + uninstallId).attr('value', 'Uninstall');
                    $('#messege_div').attr('class', 'message error').show(0).delay(2000).fadeOut(1000);
                    $('#disable-screen').attr('class', '');
                    $('#loading').attr('class', '');
                }

            }
        });
    });
});
