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
        getDescription(addId);
    });
    $('.installBtn').live('click', function () {
        console.log('ins');
        installId = $(this).attr('addid');
    });
    $('.uninstallBtn').live('click', function () {
        console.log('unins');
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
                    $('#message_body').text(meassageInModal)
                    $('#successModal').show();
                    $('#installButton' + installId).attr({
                        'class': 'buttons delete uninstallBtn',
                        'value': 'Uninstall',
                        'id': 'uninstallButton' + installId,
                        'data-target': '#deleteConfModal'
                    });
                } else {
                    $('#disable-screen').attr('class', '');
                    $('#loading').attr('class', '');
                }
            }
        });
    });
    $('#success_install').click(function () {
        $('#disable-screen').attr('class', '');
        $('#successModal').hide()
    });
    $('#modal_confirm_uninstall').click(function () {
        console.log(uninstallId);
        $('#uninstallButton' + uninstallId).attr('value', 'Uinstalling');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {uninstallAddonID: uninstallId},
            url: uninstallUrl, success: function (result) {
                if (result === '"Success"') {
                    $('#loading').attr('class', '');
                    $('#message_body').text(meassageUnstallModal)
                    $('#successModal').show();
                    $('#uninstallButton' + uninstallId).attr({
                        'class': 'buttons installBtn',
                        'value': 'Install',
                        'id': 'installButton' + uninstallId,
                        'data-target': '#installConfModal'
                    });
                } else {
                    $('#disable-screen').attr('class', '');
                    $('#loading').attr('class', '');
                }

            }
        });
    });
});

function getDescription(id) {
    $.ajax({
        method: "POST",
        data: {addonID: id},
        url: descriptionUrl, success: function (result) {
            $(".panel").html(result);
        }
    });
}
