$(document).ready(function () {

    $("#notification").click(function () {
        const notificationIconPosition = $("div.notification").position();
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

    $("div.notification-row").click(function (event) {
        const target = event.target;
        var url = null;
        if ($(target).is('div.notification-row')) {
            url = $(target).data('href');
        } else {
            url = $($(target).closest('div.notification-row')).data('href');
        }

        var win = window.open(url, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popups');
        }
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
});
