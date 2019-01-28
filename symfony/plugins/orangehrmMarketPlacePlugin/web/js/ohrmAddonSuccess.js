var installId;
var uninstallId;
var buyNowId;
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
                if (result === '3000') {
                    $("#addon_div").text(networkErrMessage);
                } else if (result === '1') {
                    $("#addon_div").text(marketpalceErrMessage);
                } else {
                    $("#des" + addId).html(result);
                }
            }
        });
    });
    $('.installBtn').live('click', function (event) {
        installId = $(this).attr('addid');
    });
    $('.uninstallBtn').live('click', function () {
        uninstallId = $(this).attr('addid');
    });
    $('#modal_confirm_install').click(function (event) {
        $('#installButton' + installId).attr('value', 'Installing');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {installAddonID: installId},
            url: installUrl, success: function (result) {
                if (result === "true") {
                    $('#loading').removeClass();
                    $('#disable-screen').removeClass();
                    $('#message_body').text(meassageInSuccess);
                    $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    $('#installButton' + installId).attr({
                        'class': 'buttons delete uninstallBtn',
                        'value': 'Uninstall',
                        'id': 'uninstallButton' + installId,
                        'data-target': '#deleteConfModal'
                    });
                }
                else {
                    $('#disable-screen').removeClass();
                    $('#loading').removeClass();
                    $('#installButton' + installId).attr('value', 'Install');
                    var errorcode = 'e' + result;
                    if (errorcode in installErrorMessage) {
                        $("#addon_div").text(installErrorMessage[errorcode] + messaegeInFail)
                    }
                    else {
                        $("#addon_div").text(messaegeInFail);
                    }
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
                if (result === "true") {
                    $('#loading').removeClass();
                    $('#disable-screen').removeClass();
                    $('#message_body').text(meassageUninSuccess);
                    $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    $('#uninstallButton' + uninstallId).attr({
                        'class': 'buttons installBtn',
                        'value': 'Install',
                        'id': 'installButton' + uninstallId,
                        'data-target': '#installConfModal'
                    });
                } else {
                    $('#disable-screen').removeClass();
                    $('#loading').removeClass();
                    var errorcode = 'e' + result;
                    if (errorcode in installErrorMessage) {
                        $("#addon_div").text(uninstallErrorMessage[errorcode]+ meassageUninFail)
                    }
                    else {
                        $("#addon_div").text(meassageUninFail);
                    }
                }
            }
        });
    });
    $('.buyBtn').click(function () {
        buyNowId = $(this).attr('addid');
    });
    $('#modal_confirm_buy').click(function () {
        if ($("#frmBuyNow").valid()) {
            var cusEmail = $('#email').val();
            var contactNum = $('#contactNumber').val();
            var comName = $('#organization').val();
            $('#buyNowModal').modal('toggle');
            $('#disable-screen').attr('class', 'overlay');
            $('#buyBtn' + buyNowId).attr('value', 'Requesting');
            $('#loading').attr('class', 'loading-class');
            $.ajax({
                method: "POST",
                data: {buyAddonID: buyNowId, companyName: comName, contactEmail: cusEmail, contactNumber: contactNum},
                url: buyNowUrl, success: function (result) {
                    if (result === '"Success"') {
                        $('#message_body').text(buyNowReqSuccess);
                        $('#loading').removeClass();
                        $('#buyBtn' + buyNowId).attr('value', 'Requested').prop("disabled", true).css('background-color', '#808080');
                        $('#disable-screen').removeClass();
                        $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    }
                    else if (result === '3000') {
                        $('#disable-screen').removeClass();
                        $('#loading').removeClass();
                        $("#addon_div").text(networkErrMessage);
                    }
                    else if (result === '1') {
                        $('#loading').removeClass();
                        $('#disable-screen').removeClass();
                        $("#addon_div").text(buyNowReqFail);
                    }
                }
            });
        }
    })
});
