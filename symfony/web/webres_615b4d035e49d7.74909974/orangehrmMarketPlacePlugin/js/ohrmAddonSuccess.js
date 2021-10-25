var installId;
var updateId;
var uninstallId;
var buyNowId;
var isRenew = false;
var renewId;
$(document).ready(function () {
    var acc = document.getElementsByClassName("accordion");
    var i;
    $('#MP_link').addClass('current');
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
    /*$('.accordion').click(function () {
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
    });*/
    $(document).on('click', '.installBtn', function (event) {
        installId = $(this).attr('addid');

        $.ajax({
            method: "POST",
            data: {addonID: installId},
            url: prerequisiteVerificationUrl,
            success: function (result) {
                var notInstalledPrerequisites = JSON.parse(result);
                if (notInstalledPrerequisites.length != 0) {
                    $('#prerequisitesNotMetModal').modal('toggle');
                    $("#prerequisitesNotMet").text("Prerequisites:- OrangeHRM requires below prerequisites in order for the add on to work successfully. Please install " + notInstalledPrerequisites.toString());
                } else {
                    $('#installConfModal').modal('toggle');
                }
            }
        });
    });
    $(document).on('click', '.updateBtn', function (event) {
        updateId = $(this).attr('addid');
        $('#updateConfModal').modal('toggle');
    });
    $(document).on('click', '.uninstallBtn', function () {
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
                    var intallBtn = $('#installButton' + installId).attr({
                        'class': 'buttons delete uninstallBtn',
                        'value': 'Uninstall',
                        'id': 'uninstallButton' + installId,
                        'data-target': '#deleteConfModal'
                    });
                    if (paidAddons.indexOf(installId) > -1) {
                        intallBtn.attr('value', 'Installed').prop("disabled", true).addClass('requested');
                    }
                    window.location.reload();
                } else {
                    $('#disable-screen').removeClass();
                    $('#loading').removeClass();
                    $('#installButton' + installId).attr('value', 'Install');
                    var errorcode = 'e' + result;
                    if (errorcode in installErrorMessage) {
                        $("#addon_div").text(installErrorMessage[errorcode] + messaegeInFail)
                    } else {
                        $("#addon_div").text(messaegeInFail);
                    }
                }
            }
        });
    });
    $('#modal_confirm_update').click(function (event) {
        var updateBtn = $('#updateButton' + updateId);
        updateBtn.attr('value', 'Updating');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {updateAddonID: updateId},
            url: updateUrl, success: function (result) {
                if (result === "true") {
                    $('#loading').removeClass();
                    $('#disable-screen').removeClass();
                    $('#message_body').text(meassageInUpdateSuccess);
                    $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    if (paidTypeAddonIds.indexOf(parseInt(updateId)) > -1) {
                        updateBtn.attr('value', 'Installed').prop("disabled", true).addClass('requested');
                    } else {
                        updateBtn.remove();
                    }
                    window.location.reload();
                } else {
                    $('#disable-screen').removeClass();
                    $('#loading').removeClass();
                    updateBtn.attr('value', 'Update');
                    var errorcode = 'e' + result;
                    if (errorcode in installErrorMessage) {
                        $("#addon_div").text(installErrorMessage[errorcode] + meassageInUpdateSuccess)
                    } else {
                        $("#addon_div").text(meassageInUpdateSuccess);
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
                    var uninstallBtn = $('#uninstallButton' + uninstallId);
                    uninstallBtn.attr({
                        'class': 'buttons installBtn',
                        'value': 'Install',
                        'id': 'installButton' + uninstallId,
                        'data-target': '#installConfModal'
                    });
                    if ($('#requestRenewButton' + uninstallId).length > 0) {
                        uninstallBtn.attr('value', 'Request');
                        $('#requestRenewButton' + uninstallId).remove();
                    }
                    window.location.reload();
                } else {
                    $('#disable-screen').removeClass();
                    $('#loading').removeClass();
                    var errorcode = 'e' + result;
                    if (errorcode in installErrorMessage) {
                        $("#addon_div").text(uninstallErrorMessage[errorcode] + meassageUninFail)
                    } else {
                        $("#addon_div").text(meassageUninFail);
                    }
                }
            }
        });
    });
    $('.buyBtn').click(function () {
        buyNowId = $(this).attr('addid');
        isRenew = $(this).attr('isRenew');
        $.ajax({
            method: "POST",
            data: {addonID: buyNowId},
            url: prerequisiteVerificationUrl,
            success: function (result) {
                var notInstalledPrerequisites = JSON.parse(result);
                $('#buyNowModal').modal('toggle');
                $("#prerequisites").text("");
                if (notInstalledPrerequisites.length != 0) {
                    $("#prerequisites").text("Prerequisites:- OrangeHRM requires below prerequisites in order for the add on to work successfully. Please install " + notInstalledPrerequisites.toString());
                }
            }
        });
    });

    $('.requestRenewBtn').click(function() {
        buyNowId = $(this).attr('addid');
        isRenew = $(this).attr('isRenew');
        $('#renewModal').modal('toggle');

    });

    $('.renewBtn').click(function() {
        renewId = $(this).attr('addid');
        $('#renewButton' + renewId).attr('value', 'Renewing');
        $('#disable-screen').attr('class', 'overlay');
        $('#loading').attr('class', 'loading-class');
        $.ajax({
            method: "POST",
            data: {addonID: renewId},
            url: renewUrl,
            success: function (result) {
                if (result) {
                    $('#loading').removeClass();
                    $('#disable-screen').removeClass();
                    $('#message_body').text(renewSuccess);
                    $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    $('#renewButton' + renewId).attr('value', 'Installed').prop("disabled", true).addClass('requested');
                } else {
                    $('#disable-screen').removeClass();
                    $('#loading').removeClass();
                    $('#renewButton' + renewId).attr('value', 'Renew');
                    $("#addon_div").text(renewFail);
                }
            }
        });

    });

    $('#modal_confirm_buy, #modal_confirm_renew').click(function () {
        var form = isRenew ? $("#frmRenewNow") : $("#frmBuyNow");
        if (form.valid()) {
            var cusEmail = $('#email', form).val();
            var contactNum = $('#contactNumber', form).val();
            var comName = $('#organization', form).val();
            if (isRenew) {
                $('#renewModal').modal('toggle');
            } else {
                $('#buyNowModal').modal('toggle');
            }
            $('#disable-screen').attr('class', 'overlay');
            $('#buyBtn' + buyNowId).attr('value', 'Requesting');
            $('#loading').attr('class', 'loading-class');
            $.ajax({
                method: "POST",
                data: {buyAddonID: buyNowId, companyName: comName, contactEmail: cusEmail, contactNumber: contactNum, isRenew: isRenew},
                url: buyNowUrl, success: function (result) {
                    if (result === '"Success"') {
                        $('#message_body').text(buyNowReqSuccess);
                        $('#loading').removeClass();
                        if(!isRenew) {
                            $('#buyBtn' + buyNowId).attr('value', 'Requested').prop("disabled", true).addClass('requested');
                        } else {
                            $('#requestRenewButton' + buyNowId).attr('value', 'Renew Requested').prop("disabled", true).addClass('requested');
                        }
                        $('#disable-screen').removeClass();
                        $('#messege_div').show(0).delay(2000).fadeOut(1000);
                    } else if (result === '3000') {
                        $('#disable-screen').removeClass();
                        $('#loading').removeClass();
                        $("#addon_div").text(networkErrMessage);
                    } else if (result === '1') {
                        $('#loading').removeClass();
                        $('#disable-screen').removeClass();
                        $("#addon_div").text(buyNowReqFail);
                    }
                }
            });
        }
    })
});
