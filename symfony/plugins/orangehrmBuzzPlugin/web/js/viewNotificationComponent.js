$(document).ready(function () {

    $("#notification").click(function () {
        const notificationIconPosition = $("#notification").position();
        const iconSize = 30;
        const modalTop = notificationIconPosition.top + iconSize;
        const modalRight = $(document).width() - notificationIconPosition.left - iconSize;

        $('#notificationModal').css('position', 'absolute');
        $('#notificationModal').css('top', modalTop);
        $('#notificationModal').css('left', 'auto');
        $('#notificationModal').css('right', modalRight);
        $('#notificationModal').css('margin', 0);

        if (!$('#notificationModal').is(':visible')) {
            $.post(ClickOnNotificationIconURL, function (data) {
            });
        }

        $('#notificationModal').modal('toggle');

        $("#notificationBadge").addClass('hide-notification-badge');
    });

    $("div.notification-row").click(function (e) {
        const target = e.target;
        var shareId = null;
        if ($(target).is('div.notification-row')) {
            shareId = $(target).data('shareid');
        } else {
            shareId = $($(target).closest('div.notification-row')).data('shareid');
        }

        $('#deleteOrEditShareForm_shareId').val(shareId);

        $.ajax({
            url: viewMoreShare,
            type: "POST",
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#notificationModal').modal('toggle');

                $('#deleteOrEditShareForm_shareId').val('');
                $('#notificationShareView').find('.shareView').replaceWith(data);
                $('#notificationShareViewMoreModal').modal();
            }
        });
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('#notification').length && !$(e.target).is('#notification') && !$(e.target).closest('#notificationModal').length) {
            // Close notification modal when click outside
            $('#notificationModal').modal('hide');
        }
    });

    $('#clearNotificationsLink').click(function (e) {
        $.post(ClearNotificationURL)
            .done(function (data) {
                $('#clearNotificationsLink').slideUp();
                $('div.notification-container').slideUp(function () {
                    $('#notificationsMessages').text(lang_NoNewNotifications);
                    $('#notificationsMessages').addClass('empty-notifications-message');
                    $('#notificationsMessages').slideDown();
                });
            })
            .fail(function (data) {
                $('#notificationsMessages').text(lang_NotificationClearFailed);
                $('#notificationsMessages').slideDown();
                setTimeout(function () {
                    $('#notificationsMessages').slideUp();
                }, 2000);
            });
    });

    $('#notificationShareViewMoreModal').on("click", ".notification-hide-modal-popup", function (e) {
        $("#notificationShareViewMoreModal").modal('hide');
    });
});
