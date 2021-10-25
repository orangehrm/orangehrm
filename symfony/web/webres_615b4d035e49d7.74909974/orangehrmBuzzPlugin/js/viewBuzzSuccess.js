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

        if (Object.keys(imageList).length > 0) {
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
    }

    $(document).on("click", ".postViewMoreCommentsLink", function(e) {
        isAccess();
        var postId = e.target.id.split("_")[1];
        $("#" + e.target.id).hide(100);
        $("." + postId).filter(function () {
            return ($(this).css("display") == "none")
        }).show(80);

    });

    /**
     * Option share edit/delete widget
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

    $(document).on('click', ".btnSaveVideo", function (e) {
        var code = trim($("#yuoutubeVideoId").html());
        var text = $("#shareVideo").val();
        $('.postLoadingBox').show();
        var data = {
            'url': code,
            'actions': 'save',
            'text': text
        };
        $.ajax({
            url: addNewVideo,
            type: "POST",
            data: $('#frmSaveVideo').serialize(),
            success: function (data) {
                $('#tempVideoBlock').remove();
                $('#buzz').prepend(data);
                $('.postLoadingBox').hide();
                $("#frmUploadVideo").show();
                $("#createVideo_content").val('');
            }
        });
    });

    function videoUrlPaste(e) {

        var url = "";
        var urlForIe = "";

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf('MSIE ');
        var trident = ua.indexOf('Trident/');

        if (msie > 0) {
            urlForIe = window.clipboardData.getData('text') || prompt('Paste something..');
        }

        else if (trident > 0) {
            urlForIe = window.clipboardData.getData('text') || prompt('Paste something..');
        } else {
            url = (e.originalEvent || e).clipboardData.getData('text/plain') || prompt('Paste something..');
        }
        if (url == "") {
            url = urlForIe;
        }
        var data = {
            'url': url,
            'actions': 'paste',
            'text': 'no'
        };

        if (url != '') {
            $.ajax({
                url: addNewVideo,
                type: "GET",
                data: data,
                success: function (data) {
                    $("#frmUploadVideo").hide();
                    $("#loadVideo").hide();
                    $('#videoPostArea').replaceWith(data);
                },
                error: function (error) {
                }
            });
        }
    }

    $("#page3").on('paste', "#createVideo_content", function (e) {

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf('MSIE ');
        var trident = ua.indexOf('Trident/');

        if (msie > 0) {
            setTimeout(videoUrlPaste, 100);
        }

        else if (trident > 0) {
            setTimeout(videoUrlPaste, 100);
        } else {
            setTimeout(videoUrlPaste(e), 100);
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
