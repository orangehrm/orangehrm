$(document).ready(function () {

    var loggedInEmpNum = -1;

    function isAccess() {

        $.getJSON(getAccessUrl, {}, function (data) {
            if (loggedInEmpNum == -1) {
                loggedInEmpNum = data.empNum;
            } else if (loggedInEmpNum != data.empNum) {
                if (!$('.modal').is(":visible")) {
                    location.reload();
                }
            }

            if (data.state === 'loged') {

            } else {
                Redirect();
            }
        });
    }

    function Redirect() {
        window.location = loginpageURL;
    }

    function hideSuccessModal() {
        $("#successDataModal").modal('hide');
    }

    $("#spinner").bind("ajaxSend", function () {

    }).bind("ajaxStop", function () {
        $(this).hide();
    }).bind("ajaxError", function () {
        $(this).hide();
    });

    var shownImgId;
    var shownImgPostId;

    if ((window.location.href).includes(buzzURL)) {
        $(document).on("click", ".postPhoto", function (e) {
            $(".modal").modal('hide');
            var shareId = parseInt(e.target.id.split("_")[1]);
            var imageId = parseInt(e.target.id.split("_")[0]);


            $(".postPhotoPrev").hide();
            $(".imagePrevBtn").attr("disabled", 'true');
            $(".imageNextBtn").attr("disabled", 'true');
            $("#showPhotos" + shareId).modal();
            modalVisible = true;
            shownImgId = imageId;
            shownImgPostId = shareId;
            $("#img_" + shownImgId + "_" + shownImgPostId).show();
            if ($("#img_" + '2' + "_" + shownImgPostId).length > 0) {
                $("#imageNextBtn" + shownImgPostId).removeAttr("disabled");
            }

            if ($("#img_" + '2' + "_" + shownImgPostId).length > 0) {
                $("#imagePrevBtn" + shownImgPostId).removeAttr("disabled");
            }
        });
    } else {
        $(document).on("mouseover", ".postPhoto:not(.postPhotoUnclickable)", function (e) {
            $(e.target).addClass('postPhotoUnclickable');
        });
    }

    $(document).on("click", ".imageNextBtn", function (e) {
        if ($("#img_" + parseInt(shownImgId + 1) + "_" + shownImgPostId).length <= 0) {
            $("#img_" + shownImgId + "_" + shownImgPostId).hide();
            shownImgId = 1;
            $("#img_" + shownImgId + "_" + shownImgPostId).show();
        } else {
            $("#img_" + shownImgId + "_" + shownImgPostId).hide();
            shownImgId = shownImgId + 1;
            $("#img_" + shownImgId + "_" + shownImgPostId).show();
        }

        $("#imagePrevBtn" + shownImgPostId).removeAttr("disabled");

    });

    $(document).on("click", ".imagePrevBtn", function (e) {
        if ($("#img_" + parseInt(shownImgId - 1) + "_" + shownImgPostId).length <= 0) {
            if ($("#img_" + '5' + "_" + shownImgPostId).length > 0) {
                $("#img_" + shownImgId + "_" + shownImgPostId).hide();
                shownImgId = 5;
                $("#img_" + shownImgId + "_" + shownImgPostId).show();
            } else if ($("#img_" + '4' + "_" + shownImgPostId).length > 0) {
                $("#img_" + shownImgId + "_" + shownImgPostId).hide();
                shownImgId = 4;
                $("#img_" + shownImgId + "_" + shownImgPostId).show();
            } else if ($("#img_" + '3' + "_" + shownImgPostId).length > 0) {
                $("#img_" + shownImgId + "_" + shownImgPostId).hide();
                shownImgId = 3;
                $("#img_" + shownImgId + "_" + shownImgPostId).show();
            } else if ($("#img_" + '2' + "_" + shownImgPostId).length > 0) {
                $("#img_" + shownImgId + "_" + shownImgPostId).hide();
                shownImgId = 2;
                $("#img_" + shownImgId + "_" + shownImgPostId).show();
            }
        } else {
            $("#img_" + shownImgId + "_" + shownImgPostId).hide();
            shownImgId = shownImgId - 1;
            $("#img_" + shownImgId + "_" + shownImgPostId).show();
        }

        $("#imageNextBtn" + shownImgPostId).removeAttr("disabled");

    });

    /**
     * share post save
     */
    $(document).on("click", ".btnShare", function (e) {
        var fromProfile = false;
        if (window.location.href.indexOf("viewProfile") > -1) {
            fromProfile = true;
        }

        if (!fromProfile) {
            var idValue = e.target.id;
            $("#posthide_" + idValue.split("_")[1]).modal('hide');
            $(".modal").modal('hide');
            $("#loadingDataModal").modal();

            var postId = idValue.split("_")[2];

            var shareId = idValue.split("_")[1];
            var share = $("[id=shareBox_" + idValue.split("_")[1] + ']').val();

            $('#deleteOrEditShareForm_shareId').val(postId);
            $('#deleteOrEditShareForm_textShare').val(share);

            $.ajax({
                url: shareShareURL,
                type: 'POST',
                data: $('#deleteOrEditShareForm').serialize(),
                success: function (data) {
                    $('#deleteOrEditShareForm_shareId').val('');
                    $('#deleteOrEditShareForm_textShare').val('');
                    $("#posthide_" + shareId).modal("hide");
                    $("#posthidePopup_" + shareId).modal("hide");
                    $('#buzz').prepend(data);
                    $("#loadingDataModal").modal('hide');
                    $("#successBodyShare").show();
                    $("#successBodyEdit").hide();
                    $("#successBodyDelete").hide();
                    $("#successDataModal").modal();
                    $('#postShareno_' + shareId).hide();
                    $('#postShareyes_' + shareId).show();
                    setTimeout(hideSuccessModal, 3000);
                }
            });
        }
    });

    /**
     * share post save
     */
    $(".btnShareOnPreview").on("click", function (e) {
        var idValue = e.target.id;
        $("#posthide_" + idValue.split("_")[1]).hide();
        $(".modal").hide();
        $("#loadingDataModal").modal();
        var shareId = idValue.split("_")[1];
        var share = $("[id=share1Box_" + idValue.split("_")[1] + ']').val();

        var postId = idValue.split("_")[2];

        $('#deleteOrEditShareForm_shareId').val(postId);
        $('#deleteOrEditShareForm_textShare').val(share);

        $.ajax({
            url: shareShareURL,
            type: 'POST',
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#deleteOrEditShareForm_shareId').val('');
                $('#deleteOrEditShareForm_textShare').val('');
                $("#posthide_" + shareId).modal("hide");
                $("#posthidePopup_" + shareId).modal("hide");
                $('#buzz').prepend(data);
                $("#loadingDataModal").modal('hide');
                $("#successBodyDelete").hide();
                $("#successBodyShare").show();
                $("#successBodyEdit").hide();
                $("#successDataModal").modal();
                setTimeout(hideSuccessModal, 3000);
            }
        });
    });

    $(document).on("click", ".viewMoreComment", function (e) {
        var idValue = e.target.id;
        var commentId = idValue.split("_")[1];
        $('#commentBasic_' + commentId).hide();
        $('#commentFull_' + commentId).show();
    });

    /**
     * share post popup window view
     */
    $(document).on("click", ".postShare", function (e) {
        var idValue = e.target.id;
        $("#posthide_" + idValue.split("_")[1]).modal();

    });

    /**
     * share post popup window in original post popup
     */
    $(".postShareOnOriginalPostPopup").on("click", function (e) {
        var idValue = e.target.id;
        $("#posthidePopupOnOriginalPost_" + idValue.split("_")[1]).modal();

    });

    /**
     * share post popup window view in PopUp
     */
    $(document).on("click", ".postSharePopup", function (e) {
        var idValue = e.target.id;
        $("#posthidePopup_" + idValue.split("_")[1]).modal();

    });

    $(document).on("click", ".editComment", function (e) {
        var commentId = e.target.id.split("_")[1];
        var contentOfTheCommentBox = $("#editcommentBoxNew2_" + commentId).val();
        if (contentOfTheCommentBox.length == 0) {
            var content = $.trim($("#commentContentNew_" + commentId).text());
            if (content.length != 0) {
                $("#editcommentBoxNew2_" + commentId).val(content);
            }
        }
        $("#editcommenthideNew2_" + commentId).modal();
    });

    /**
     * view liked employee list for post
     */
    $(document).on("click", ".postNoofLikesTooltip", function (e) {
        var idValue = e.target.id;
        var shareId = idValue.split("_")[1];
        var likeLabelId = "#noOfLikes_" + idValue.split("_")[1];
        var existingLikes = parseInt($(likeLabelId).html());
        $("#postlikehidebody").html("");
        if (existingLikes > 0) {
            var action = "post";

            $('#likedOrSharedEmployeeForm_id').val(shareId);
            $('#likedOrSharedEmployeeForm_type').val(action);

            console.log($('#likedOrSharedEmployeeForm'));

            $.ajax({
                url: viewLikedEmployees,
                type: 'POST',
                data: $('#likedOrSharedEmployeeForm').serialize(),
                success: function (data) {
                    $('#likedOrSharedEmployeeForm_id').val('');
                    $('#likedOrSharedEmployeeForm_type').val('');
                    ;
                    $("#postlikehidebody").html(data);
                }
            });

            $("#postlikehide").modal();
        }
    });
    /**
     * hide liked employee list
     */
    $(".btnBackHide").on("click", function (e) {
        var idValue = e.target.id;
        var shareId = idValue.split("_")[1];
        $("#postlikehide_" + shareId).modal('hide');
    });

    /**
     * view like employee list for comment
     */
    $(document).on("click", ".commentNoofLikesTooltip", function (e) {
        var idValue = e.target.id;
        var commentId = idValue.split("_")[1];
        var likeLabelId = "#commentNoOfLikes_" + idValue.split("_")[1];
        var existingLikes = parseInt($(likeLabelId).html().split(" ")[0]);

        if (existingLikes > 0) {
            var action = "comment";

            $('#likedOrSharedEmployeeForm_id').val(commentId);
            $('#likedOrSharedEmployeeForm_type').val(action);

            $.ajax({
                url: viewLikedEmployees,
                type: 'POST',
                data: $('#likedOrSharedEmployeeForm').serialize(),
                success: function (data) {
                    $('#likedOrSharedEmployeeForm_id').val('');
                    $('#likedOrSharedEmployeeForm_type').val('');
                    $("#postlikehidebody_" + commentId).replaceWith(data);
                }
            });
            $("#postlikehide_" + commentId).modal();
        }
    });

    $(document).on('mouseenter', ".postNoofLikesTooltip", function (e) {
        isAccess();
        const target = e.target;
        var idValue = e.target.id;
        var shareId = idValue.split("_")[1];

        $('#likedOrSharedEmployeeForm_id').val(shareId);
        $('#likedOrSharedEmployeeForm_type').val('post');

        $.ajax({
            url: getLikedEmployeeListURL,
            type: "POST",
            data: $('#likedOrSharedEmployeeForm').serialize(),
            success: function (data) {
                $('#likedOrSharedEmployeeForm_id').val('');
                $('#likedOrSharedEmployeeForm_type').val('');
                $(target).attr('title', '');
                $(target).attr('title', data);
            }
        });
    });

    $(document).on('mouseenter', ".postNoofSharesTooltip", function (e) {
        isAccess();
        const target = e.target;
        var idValue = e.target.id;
        var shareId = idValue.split("_")[1];

        $('#likedOrSharedEmployeeForm_id').val(shareId);
        $('#likedOrSharedEmployeeForm_type').val('post');
        $('#likedOrSharedEmployeeForm_event').val('hover');

        $.ajax({
            url: getSharedEmployeeListURL,
            type: "POST",
            data: $('#likedOrSharedEmployeeForm').serialize(),
            success: function (data) {
                $('#likedOrSharedEmployeeForm_id').val('');
                $('#likedOrSharedEmployeeForm_type').val('');
                $('#likedOrSharedEmployeeForm_event').val('');
                $(target).attr('title', '');
                $(target).attr('title', data);
            }
        });
    });

    $(document).on("click", ".postNoofSharesTooltip", function (e) {
        isAccess();
        var idValue = e.target.id;
        var shareId = idValue.split("_")[1];

        $('#likedOrSharedEmployeeForm_id').val(shareId);
        $('#likedOrSharedEmployeeForm_type').val('post');
        $('#likedOrSharedEmployeeForm_event').val('click');

        $("#postsharehidebody").html("");
        $.ajax({
            url: getSharedEmployeeListURL,
            type: "POST",
            data: $('#likedOrSharedEmployeeForm').serialize(),
            success: function (data) {
                $('#likedOrSharedEmployeeForm_id').val('');
                $('#likedOrSharedEmployeeForm_type').val('');
                $('#likedOrSharedEmployeeForm_event').val('');
                $("#postsharehidebody").html(data);
            }
        });
        $("#postsharehide").modal();
    });

    $(document).on('mouseenter', ".commentNoofLikesTooltip", function (e) {
        isAccess();
        const target = e.target;
        var idValue = e.target.id;
        var commentId = idValue.split("_")[1];

        $('#likedOrSharedEmployeeForm_id').val(commentId);
        $('#likedOrSharedEmployeeForm_type').val('comment');

        $.ajax({
            url: getLikedEmployeeListURL,
            type: "POST",
            data: $('#likedOrSharedEmployeeForm').serialize(),
            success: function (data) {
                $('#likedOrSharedEmployeeForm_id').val('');
                $('#likedOrSharedEmployeeForm_type').val('');
                $(target).attr('title', '');
                $(target).attr('title', data);
            }
        });
    });


    $(document).on("click", ".commentSubmitBtn", function (e) {
        isAccess();

        var elementId = "#" + e.target.id;
        var value = $(elementId).val();

        var value;
        var formName;
        var loadingSpinner;
        var commentId;
        var elementSplitted = elementId.split("_");

        if (elementSplitted[1] == 'popShareId') {
            var element = '#commentBoxnew_txt_popShareId_' + elementSplitted[2];
            value = $(element).val();
            formName = '#formCreateComment_' + elementSplitted[1] + elementSplitted[2];
            commentId = elementSplitted[2];
        } else if (elementSplitted[1] == 'popPhotoId') {
            var element = '#commentBoxnew_txt_popPhotoId_' + elementSplitted[2];
            value = $(element).val();
            formName = '#formCreateComment_' + elementSplitted[1] + elementSplitted[2];
            commentId = elementSplitted[2];
        } else {
            value = $(elementId).val();
            formName = '#formCreateComment_' + elementId.split("_")[1];
            loadingSpinner = '#commentLoadingBox' + elementId.split("_")[1];
            commentId = elementId.split("Id")[1];
        }

        $("#commentListContainer_" + elementId.split("Id")[1]).css("display", "block");
        if (trim(value).length > 0) {
            $('#commentLoadingBox' + elementId.split("_")[1]).show();
            $.ajax({
                url: addBuzzCommentURL,
                type: 'POST',
                data: $(formName).serialize(),
                success: function (data) {
                    $("#comment-text-width-analyzer").html("");

                    $("#commentListNew_popPostId" + commentId).append(data);
                    $("#commentListNew_popPostId" + commentId + " " + "#modalEdit").replaceWith(' ');
                    $("#commentListNew_popPostId" + commentId + " li .addNewCommentBody " + "#modatLikeWindow").replaceWith(' ');

                    $("#commentListNew_popShareId" + commentId).append(data);
                    $("#commentListNew_popShareId" + commentId + " " + "#modalEdit").replaceWith(' ');
                    $("#commentListNew_popShareId" + commentId + " li .addNewCommentBody " + "#modatLikeWindow").replaceWith(' ');

                    $("#commentListNew_popPhotoId" + commentId).append(data);
                    $("#commentListNew_popPhotoId" + commentId + " " + "#modalEdit").replaceWith(' ');
                    $("#commentListNew_popPhotoId" + commentId + " li .addNewCommentBody " + "#modatLikeWindow").replaceWith(' ');

                    $("#commentListNew_listId" + commentId).append(data);
                    $("#commentListContainer_" + commentId).css("display", "block");
                    $('.commentLoadingBox').hide();
                    $(".commentBox").val('');
                }
            });
        }
    });

    $(document).on("click", ".hideModalPopUp", function (e) {
        var id = e.target.id;
        $("#" + id).modal('hide');
    });
});
