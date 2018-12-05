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
    $('.installBtn').click(function () {
        installId = $(this).attr('addid');
    });
    $('.uninstallBtn').click(function () {
        uninstallId = $(this).attr('addid');
    });
    $('#modal_confirm_install').click(function () {
        console.log(installId);
        $('#installButton' + installId).attr('value', 'Installing');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {installAddonID: installId},
            url: installUrl, success: function (result) {
                if (result === '"Success"') {
                    $('#disable-screen').attr('class', '');
                    $('#loading').attr('class', '');
                    $('#installButton' + installId).attr({
                        'class': 'buttons delete uninstallBtn',
                        'value': 'Uninstall',
                        'id': 'uninstallButton' + installId
                    });
                } else {
                    $('#disable-screen').attr('class', '');
                    $('#loading').attr('class', '');
                }
            }
        });
    });
    $('#modal_confirm_uninstall').click(function () {
        $.ajax({
            method: "POST",
            data: {uninstallAddonID: uninstallId},
            url: uninstallUrl, success: function (result) {
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
