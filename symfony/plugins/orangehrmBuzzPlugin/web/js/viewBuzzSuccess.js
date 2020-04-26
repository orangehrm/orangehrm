var modalVisible = false;
var fileInput = $("#photofile");

$(document).ready(function () {

    /**
     * Submitting a new post
     */
    $("#postSubmitBtn").on("click", function () {
        isAccess();
        var x = $("#createPost_content").val();
        if (x === null || trim(x).length < 1) {

        } else {
            $('.postLoadingBox').show();

            $.ajax({
                url: addBuzzPostURL,
                type: "POST",
                data: $('#frmPublishPost').serialize(),
                success: function (data) {
                    $('#buzz').prepend(data);
                    $('.postLoadingBox').hide();
                    $("#postLinkData").hide();
                    $("#postLinkState").html('no');
                    $("#createPost_content").val('');
                }
            });

        }
    });

    function getResizedImage(image) {
        var sourceWidth = image.naturalWidth;
        var sourceHeight = image.naturalHeight;
        var destImageWidth;
        var destImageHeight;

        var sourceAspectRatio = sourceWidth / sourceHeight;
        var destAspectRatio = imageMaxWidth / imageMaxHeight;
        if (sourceWidth <= imageMaxWidth && sourceHeight <= imageMaxHeight) {
            destImageWidth = sourceWidth;
            destImageHeight = sourceHeight;
        } else if (destAspectRatio > sourceAspectRatio) {
            destImageWidth = Math.floor(imageMaxHeight * sourceAspectRatio);
            destImageHeight = imageMaxHeight;
        } else {
            destImageWidth = imageMaxWidth;
            destImageHeight = Math.floor(imageMaxWidth / sourceAspectRatio);
        }


        var canvas = document.createElement("canvas");
        canvas.width = destImageWidth;
        canvas.height = destImageHeight;

        var ctx = canvas.getContext("2d");
        ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

        return canvas.toDataURL("image/jpeg");
    }

    function convertDataURI2Blob(uri) {
        var byteString = atob(uri.split(',')[1]);

        var mimeString = uri.split(',')[0].split(':')[1].split(';')[0]

        var ab = new ArrayBuffer(byteString.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < byteString.length; i++)
        {
            ia[i] = byteString.charCodeAt(i);
        }

        var bb = new Blob([ab], {"type": mimeString});
        return bb;
    }

    var formData = new FormData();
    var imageList = {};

    function readURL(file, thumbnailDivId) {
        var deferredObject = $.Deferred();

        var reader = new FileReader();
        reader.readAsDataURL(file);
        imageList[thumbnailDivId] = file;
        reader.onload = function (e) {

            var image = new Image();
            image.onload = function () {
                var x = '<div class="imageDefinition"><a class="img_del" id="img_del_' + thumbnailDivId + '"></a>' +
                        '<img height="70px" class="imgThumbnailView" id="thumb' + thumbnailDivId + '" src="' +
                        getResizedImage(image) + '" alt="your image" /></div>';
                $("#imageThumbnails").append(x);
                deferredObject.resolve();
            };
            image.src = e.target.result;
        };

        return deferredObject.promise();
    }

    $("#image-upload-button").on("click", function () {
        if (noOfPhotosStacked > 5) {
            $("#imageUploadError").modal();
            $("#maxImageErrorBody").show();
            $("#invalidTypeImageErrorBody").hide();
            return;
        }
        $("#photofile").click();
    });

    var noOfPhotosPreviewed = 1;
    var noOfPhotosStacked = 1;
    $("#photofile").change(function () {

        var files = $("#photofile")[0].files;
        var imagesChoosed = $("#photofile")[0].files.length;
        var currentNumberOfPhotosInTheStack = noOfPhotosStacked - 1;
        var allImagesCount = currentNumberOfPhotosInTheStack + imagesChoosed;
        if (imagesChoosed > 5 || allImagesCount > 5) {
            $("#imageUploadError").modal();
            $("#maxImageErrorBody").show();
            $("#invalidTypeImageErrorBody").hide();
            $("#phototext").val('');
            $("#photofile").replaceWith($("#photofile").val('').clone(true));
            return;
        }
        for (var i = 1; i <= imagesChoosed; i++) {
            var ext = files[i - 1].name.split(".").pop().toLowerCase();
            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                $("#imageUploadError").modal();
                $("#invalidTypeImageErrorBody").show();
                $("#maxImageErrorBody").hide();
            } else {
                var file = files[i - 1];
                var promises = [];

                // Disable upload button
                $('#image-upload-button').prop('disabled', true);

                if (file) {
                    promises.push(readURL(file, noOfPhotosPreviewed));
                    noOfPhotosPreviewed++;
                    noOfPhotosStacked++;
                }

                // Enable upload/publish button when all images have loaded.
                $.when.apply($, promises).then(function () {
                    $('#image-upload-button').prop('disabled', false);
                });
            }
        }

    });

    $(document).on('click', ".img_del", function () {
        var id = $(this).attr('id').split("_")[2];
        $('#thumb' + id).attr('hidden', true);
        $('#thumb' + id).hide();
        $('#thumb' + id).attr('src', null);
        delete imageList[id];
//        $(this).attr('hidden', 'true');
        $(this).hide();
        noOfPhotosStacked--;
    });

    $(".hidePhotoPopUp").click(function (e) {
        var id = e.target.id;
        $("#showPhotos" + id.split("_")[1]).modal('hide');
    });

    $("#frmUploadImage").on("submit", function (e) {
        isAccess();


        noOfPhotosPreviewed = 1;
        noOfPhotosStacked = 1;
        e.preventDefault();
        var photoText = $("#phototext").val();
        if (true) {
            activateTab('page1');
            $("#tabLink1").attr("class", "tabButton tb_one tabSelected");
            $("#tabLink2").removeClass("tabSelected");
            var str = "";

            $('.postLoadingBox').show();

            for (var key in imageList) {
                if (imageList.hasOwnProperty(key)) {
                    // Get thumbnail src and file name
                    var blob = convertDataURI2Blob($("#thumb" + key).attr('src'));
                    formData.append(key, blob, imageList[key].name);
                }
            }
            formData.append('postContent', photoText);
            
            var csrfToken = $('#imageUploadForm__csrf_token').val();
            formData.append('csrfToken', csrfToken);
            
            $.ajax({
                url: uploadImageURL,
                type: "POST",
                data: formData,
                processData: false, // Don't process the files
                contentType: false,
                success: function (data) {
                    $('#buzz').prepend(data);
                    clearImageUpload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    clearImageUpload();
                }
            });
        }
    });

    function clearImageUpload() {
        $('#imageThumbnails').html('');
        $('.postLoadingBox').hide();
        $(".imgThumbnailView").hide();
        $("#phototext").val('');
        $("#photofile").replaceWith($("#photofile").val('').clone(true));
        $(".img_del").hide();
        imageList = {};
        formData = new FormData();
    }

    $("#gotoProfile").click(function () {
        var id = $('#searchChatter_emp_name_empId').val();
        if (id.length <= 0) {

        } else {
            window.location = profilePage + id;
        }
    });

    var windowTitle = "OrangeBuzz";
    var newlyAddedPostCount = 0;
    var noOfPostsWhenLeavingWindow = 0;
    var isWindowFocussed = true;

    $(window).focus(function () {
        isWindowFocussed = true;
    });

    $(window).blur(function () {
        isWindowFocussed = false;
        noOfPostsWhenLeavingWindow = $('.singlePost').length;
    });



    function reload() {
        isAccess();
        var params = {
            'timestamp': $('#buzzLastTimestamp').text()
        };
        $.ajax({
            url: refreshPageURL,
            type: "GET",
            data: params,
            dataType: 'html',
            success: function (data) {
                var result = $(data);
                var newTimestamp = result.find('#new_timestamp').text();
                $('#buzzLastTimestamp').text(newTimestamp);

                result.find('#changed_shares li.singlePost').each(function () {
                    var id = $(this).prop('id');
                    var existing = $('#' + id);
                    if (existing.length) {
                        existing.html($(this).html());
                    } else {
                        $('#buzz').prepend($(this).html());
                    }
                });

                var noOfPostsNow = $('.singlePost').length;
                if (!isWindowFocussed) {
                    newlyAddedPostCount = noOfPostsNow - noOfPostsWhenLeavingWindow;
                    if (newlyAddedPostCount > 0) {
                        $(document).prop('title', windowTitle + "(" + newlyAddedPostCount + ")");
                    }
                }
            }
        });

        refreshStatComponent();
    }

    function refreshStatComponent() {
        isAccess();

        $('#refreshStatusForm_profileUserId').val($('#profileUserId').html());
        $.ajax({
            url: refreshStatsURL,
            type: "POST",
            data: $('#refreshStatsForm').serialize(),
            success: function (data) {
                $('#refreshStatusForm_profileUserId').val();
                $('#statTable').replaceWith(data);
            }
        });
    }

    $(".loadMorePostsLink").on("click", function (e) {
        isAccess();

        $('#loadMorePostsForm_lastPostId').val($('#buzz .lastLoadedPost').last().attr('id'));

        $.ajax({
            url: loadNextSharesURL,
            type: "POST",
            data: $('#loadMorePostsForm').serialize(),
            success: function (data) {
                $('#loadMorePostsForm_lastPostId').val('');
                $('#buzz').append(data);
            }
        });

    });
    $(document).on("click", ".hideModalPopUp", function (e) {
        var id = e.target.id;
        $("#" + id).modal('hide');
    });

    $(".postViewMoreCommentsLink").on("click", function (e) {
        isAccess();
        var postId = e.target.id.split("_")[1];
        $("#" + e.target.id).hide(100);
        $("." + postId).filter(function () {
            return ($(this).css("display") == "none")
        }).show(80);

    });


    $(".nfPostCommentLink").on("click", function (e) {
        isAccess();
        $("#nfPostCommentTextBox" + e.target.id).toggle(300);
    });

    /**
     * Liking a share
     */
    $(".nfShowComments").on("click", function (e) {
        isAccess();
        var idValue = e.target.id;
        if ($("#" + idValue).html() == "Show Comments") {
            $("#" + idValue).html("Hide Comments");
        } else {
            $("#" + idValue).html("Show Comments");
        }
        idValue = idValue.split("_")[1];
        $("#commentList" + idValue).toggle(300);

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

    function getCaret(el) {
        if (el.selectionStart) {
            return el.selectionStart;
        } else if (document.selection) {
            el.focus();

            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }

            var re = el.createTextRange(),
                    rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    }

    /**
     * Option widget
     */

    $(document).on("click", ".account", function (e)
    {
        var elementId = "#submenu" + e.target.id;

        $(elementId).toggle(100);
    });

    $(document).on("click", ".commentAccount", function (e)
    {
        var elementId = "submenu" + e.target.id;
        $('[id=' + elementId + "]").toggle(100);
    });

//Mouse click on sub menu
    $(".submenu").on("mouseup", function ()
    {
        return false;
    });

//Mouse click on my account link
    $(document).on("mouseup", ".account", function ()
    {
        return false;
    });
    $(document).on("mouseup", ".commentAccount", function ()
    {
        return false;
    });


//Document Click
    $(document).on("mouseup", function ()
    {
        $(".submenu").hide();
        //$(".account").attr('id', '');
    });


    var idOfThePostToDelete = -1;
    $(document).on("click", ".deleteShare", function (e) {
        $(".delete-share-message-box").modal();
        idOfThePostToDelete = e.target.id.split("_")[1];
    });

    $("#delete_confirm").on("click", function () {
        $(".delete-share-message-box").modal('hide');
        $("#loadingDataModal").modal();

        $('#deleteOrEditShareForm_shareId').val(idOfThePostToDelete);

        $.ajax({
            url: shareDeleteURL,
            type: "POST",
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#deleteShareForm_shareId').val('');
                $("#postInList" + idOfThePostToDelete).hide(1000);
                $("#loadingDataModal").modal('hide');
                idOfThePostToDelete = -1;
                $("#successBodyDelete").show();
                $("#successBodyShare").hide();
                $("#successBodyEdit").hide();
                $("#successDataModal").modal();
                setTimeout(hideSuccessModal, 2000);
                setTimeout(refresh, 10000);
            }
        });
    });

    $("#delete_discard").on("click", function () {
        $(".delete-share-message-box").modal('hide');
    });

    /**
     * Edit share form pop up 
     */
    $(document).on("click", ".editShare", function (e) {
        var shareId = e.target.id.split("_")[1];
        $("#editposthide_" + shareId).modal();
    });

    var nextBtn;
    var prevBtn;
    var shownImgId;
    var shownImgPostId;
    /**
     * Show images in modal..
     */
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

    $(document).on("click", ".imageNextBtn", function (e) {
//        alert(e.target.id);
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
     * save edited form
     */
    $(document).on("click", ".btnEditShare", function (e) {
        var shareId = e.target.id.split("_")[1];
        var content = $("#editshareBox_" + shareId).val();
        $("#editposthide_" + shareId).modal('hide');
        $("#loadingDataModal").modal();

        $('#deleteOrEditShareForm_shareId').val(shareId);
        $('#deleteOrEditShareForm_textShare').val(content);

        $.ajax({
            url: shareEditURL,
            type: "POST",
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#deleteOrEditShareForm_shareId').val('');
                $('#deleteOrEditShareForm_textShare').val('');
                $("#postContent_" + shareId).replaceWith(data);
                reload();
                $("#loadingDataModal").modal('hide');

                $("#successBodyShare").hide();
                $("#successBodyEdit").show();
                $("#successBodyDelete").hide();
                $("#successDataModal").modal();
                setTimeout(hideSuccessModal, 3000);
                setTimeout(refresh, 10000);
            }
        });


    });

    function hideSuccessModal() {
        $("#successDataModal").modal('hide');
    }

    $(document).on("click", ".deleteComment", function (e) {
        var commentId = e.target.id.split("_")[1];
        $("#loadingDataModal").modal();

        $('#deleteOrEditCommentForm_commentId').val(commentId);

        $.ajax({
            url: commentDeleteURL,
            type: "POST",
            data: $('#deleteOrEditCommentForm').serialize(),
            success: function (data) {
                $('#deleteOrEditCommentForm_commentId').val('');
                $("[id=commentInPost_" + commentId + ']').hide(1000);
                $("[id=commentInPost_" + commentId + ']').remove();
                $("#loadingDataModal").modal('hide');
            }
        });
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

    $(document).on("click", ".viewMoreShare", function (e) {
        var idValue = e.target.id;
        var shareId = idValue.split("_")[1];
        $("#loadingDataModal").modal();

        $('#deleteOrEditShareForm_shareId').val(shareId);

        $.ajax({
            url: viewMoreShare,
            type: "POST",
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#deleteOrEditShareForm_shareId').val('');
                $('#shareViewContent1_' + shareId).replaceWith(data);

                $("#loadingDataModal").modal('hide');
                $('#shareViewMoreMod1_' + shareId).modal();
            }
        });
    });

    $(".viewMoreComment").on("click", function (e) {
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
    $(document).on("click", ".btnEditCommentNew", function (e) {
        var commentId = e.target.id.split("_")[1];
        var content = $("#editcommentBoxNew2_" + commentId).val();

        $("#editcommenthideNew2_" + commentId).modal('hide');
        $("#loadingDataModal").modal();

        $('#deleteOrEditCommentForm_commentId').val(commentId);
        $('#deleteOrEditCommentForm_textComment').val(content);

        $.ajax({
            url: commentEditURL,
            type: 'POST',
            data: $('#deleteOrEditCommentForm').serialize(),
            success: function (data) {
                $('#deleteOrEditCommentForm_commentId').val('');
                $('#deleteOrEditCommentForm_textComment').val('');
                var newDataId = $(data).attr('id');
                if ($("[id=commentContentNew_" + commentId + ']').length) {
                    $("[id=commentContentNew_" + commentId + ']').replaceWith(data);
                } else if ($("#"+newDataId).length) {
                    $("[id=" + newDataId + ']').replaceWith(data);
                }
                $("#loadingDataModal").modal('hide');

            }
        });


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
    /**
     * hide liked employee list for comment
     */

    $("#spinner").bind("ajaxSend", function () {
        //$(this).show();
    }).bind("ajaxStop", function () {
        $(this).hide();
    }).bind("ajaxError", function () {
        $(this).hide();
    });

    /**
     * original post view
     */
//    $(".originalPostView").live("click", function (e) {
//        var idValue = e.target.id;
//        var shareId = idValue.split("_")[1];
//        var postId = idValue.split("_")[2];
//
//        $("#loadingDataModal").modal();
//
//        $('#deleteOrEditShareForm_shareId').val(postId);
//
//        $.ajax({
//            url: viewOriginalPost,
//            type: "POST",
//            data: $('#deleteOrEditShareForm').serialize(),
//            success: function (data) {
//                $('#deleteOrEditShareForm_shareId').val('');
//                $('#postViewContent_' + shareId).replaceWith(data);
//                $("#loadingDataModal").modal('hide');
//                $('#postViewOriginal_' + shareId).modal();
//            }
//        });
//    });

    $(".postCommentBox").on('click', function (e) {
        var idValue = e.target.className;
        $("#commentBoxNew_listId" + idValue).focus();
    });

    var refreshTime = trim($("#refreshTime").text());
    var lastActivityTime = new Date().getTime();

    $(document.body).bind("mousemove keypress", function (e) {
        lastActivityTime = new Date().getTime();
    });

    window.setTimeout(function () {
        refresh(this);
    }, refreshTime);

    function refresh() {
        $(document).prop('title', windowTitle);

        if (new Date().getTime() - lastActivityTime >= refreshTime) {
            if (!$('.modal').is(":visible")) {
                reload();
            }
        }

        window.setTimeout(function () {
            refresh(this);
        }, refreshTime);
    }

    var loggedInEmpNum = -1;
    function isAccess() {

        $.getJSON(getAccessUrl, {}, function (data) {
            if (loggedInEmpNum == -1) {
                loggedInEmpNum = data.empNum;
            } else if (loggedInEmpNum != data.empNum) {
                if (!$('.modal').is(":visible")) {
                    location.reload();
                }
            } else if (loggedInEmpNum == null) {
                //location.reload();
            }

            if (data.state === 'loged') {

            } else {
                Redirect();
            }
        });
    }
    function Redirect()
    {
        window.location = loginpageURL;
    }


    // Clicking the tabs make it selected.
    $(".tabButton").on("click", function () {
        $(".tabButton").removeClass('tabSelected');
        $(this).addClass('tabSelected');
    });

    $(".likeRaw").on("click", function (e) {
        var id = e.target.id;
        var postId = id.split("_")[1];

        $('#deleteOrEditShareForm_shareId').val(postId);

        $.ajax({
            url: viewMoreShare,
            type: "POST",
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#deleteOrEditShareForm_shareId').val('');
                $('#shareViewContent3_').html(data);
                $('#shareViewMoreMod3_').modal();
            }
        });
    });

    function viewport() {
        var e = window, a = 'inner';
        if (!('innerWidth' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return {width: e[a + 'Width'], height: e[a + 'Height']};
    }

    $(window).scroll(function ()
    {

        if ($(window).scrollTop() + viewport().height >= $(document).height())
        {
            var sharesLoadedCount = parseInt($('#buzzSharesLoadedCount').html());
            var allSharesCount = parseInt($('#buzzAllSharesCount').html());
            var sharesInceasingCount = parseInt($('#buzzSharesInceasingCount').html());

            if ($('.loadMoreBox').css('display') == 'none') {
                if (allSharesCount > sharesLoadedCount) {
                    sharesLoadedCount = sharesLoadedCount + sharesInceasingCount;
                    $('#buzzSharesLoadedCount').html(sharesLoadedCount);

                    $('.loadMoreBox').show();
                    
                    var lastPostId = $('#buzz .lastLoadedPost').last().attr('id');
                    $('#loadMorePostsForm_lastPostId').val(lastPostId);
                    
                    $.ajax({
                        url: loadNextSharesURL,
                        type: "POST",
                        data: $('#loadMorePostsForm').serialize(),
                        success: function (data) {
                            $('#loadMorePostsForm_lastPostId').val('');
                            $('.loadMoreBox').hide();
                            $('#buzz').append(data);
                        }
                    });
                }
            }
        }
    });
}
);

/**
 * Activates the clicked tab
 * @param {type} pageId
 * @returns {undefined}
 */
function activateTab(pageId) {
    var tabCtrl = document.getElementById('tabCtrl');
    var pageToActivate = document.getElementById(pageId);
    for (var i = 0; i < tabCtrl.childNodes.length; i++) {
        var node = tabCtrl.childNodes[i];
        if (node.nodeType == 1) { /* Element */
            node.style.display = (node == pageToActivate) ? 'block' : 'none';
            if (pageId === 'page1') {
                $("#status_icon").attr("src", imageFolderPath + "status2.png");
                $("#status-tab-label").css("color", "#f07c00");
                $("#images-tab-label").css("color", "#5d5d5d");
                $("#video-tab-label").css("color", "#5d5d5d");
                $("#img_upld_icon").attr("src", imageFolderPath + "img.png");
                $("#vid_upld_icon").attr("src", imageFolderPath + "vid.png");
            } else if (pageId === 'page2') {
                $("#status_icon").attr("src", imageFolderPath + "status.png");
                $("#status-tab-label").css("color", "#5d5d5d");
                $("#images-tab-label").css("color", "#f07c00");
                $("#video-tab-label").css("color", "#5d5d5d");
                $("#img_upld_icon").attr("src", imageFolderPath + "img2.png");
                $("#vid_upld_icon").attr("src", imageFolderPath + "vid.png");
            } else {
                $("#status_icon").attr("src", imageFolderPath + "status.png");
                $("#status-tab-label").css("color", "#5d5d5d");
                $("#images-tab-label").css("color", "#5d5d5d");
                $("#video-tab-label").css("color", "#f07c00");
                $("#img_upld_icon").attr("src", imageFolderPath + "img.png");
                $("#vid_upld_icon").attr("src", imageFolderPath + "vid2.png");
            }
        }
    }
}
