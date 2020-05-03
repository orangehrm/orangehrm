$(document).ready(function () {

    var searchParams = new URLSearchParams(window.location.search);

    if (searchParams.has('postId')) {
        const postItemId = '#postInList' + searchParams.get('postId');
        const headerOffset = 100;

        if ($(postItemId).length > 0) {

            $(postItemId).addClass('post-selected');
            $(postItemId + ' #postBody').addClass('post-body-selected');
            $([document.documentElement, document.body]).animate({
                scrollTop: $(postItemId).offset().top - headerOffset
            }, 500);
        }
    }

    var noOfPhotosPreviewed = 1;
    $("#photofile").change(function () {
        if (noOfPhotosPreviewed > 5) {
            alert("No more!");
        }
        var files = $("#photofile")[0].files;
        var imagesChoosed = $("#photofile")[0].files.length;
        for (var i = 1; i <= imagesChoosed; i++) {
            readURL(files[i - 1], noOfPhotosPreviewed);
            noOfPhotosPreviewed++;
        }

    });

    $("#frmUploadImage").on("submit", function (e) {
        e.preventDefault();
        var imageFiles = $("#photofile")[0].files;
        var photoText = $("#phototext").val();
        var formData = new FormData();
        var str = "";
        if (imageFiles.length > 5) {
            //Handel proper validation here...
            alert("Max");
            return;
        }
        $.each(imageFiles, function (k, v) {
            formData.append(k, v);
        });
        formData.append('postContent', photoText);

        $.ajax({
            url: uploadImageURL,
            type: "POST",
            data: formData,
            processData: false, // Don't process the files
            contentType: false,
            success: function (data) {
                $('#buzz').prepend(data);
            }
        });
    });

    function reload() {

        $('#loadMorePostsForm_lastPostId').val($('#profileBuzz .lastLoadedPost').last().attr('id'));
        $('#loadMorePostsForm_profileUserId').val(trim($('#profileUserId').html()));

        $.ajax({
            url: refreshPageURL,
            type: "POST",
            data: $('#loadMorePostsForm').serialize(),
            success: function (data) {
                $('#loadMorePostsForm_lastPostId').val('');
                $('#loadMorePostsForm_profileUserId').val('');
                $('#profileBuzz').replaceWith(data);
            }
        });
    }

    $(".loadMorePostsLink").on("click", function (e) {

        $('#loadMorePostsForm_lastPostId').val($('#profileBuzz .lastLoadedPost').last().attr('id'));
        $('#loadMorePostsForm_profileUserId').val(trim($('#profileUserId').html()));

        $.ajax({
            url: loadNextSharesURL,
            type: "POST",
            data: $('#loadMorePostsForm').serialize(),
            success: function (data) {
                $('#loadMorePostsForm_lastPostId').val('');
                $('#loadMorePostsForm_profileUserId').val('');
                $('#profileBuzz').append(data);
            }
        });

    });

    $(document).on("click", ".postViewMoreCommentsLink", function (e) {
        var postId = e.target.id.split("_")[1];
        $("#" + e.target.id).hide(100);
        $("." + postId).filter(function () {
            return ($(this).css("display") == "none")
        }).show(80);

    });


    $(".nfPostCommentLink").on("click", function (e) {
        $("#nfPostCommentTextBox" + e.target.id).toggle(300);
    });



    $(".nfShowComments").on("click", function (e) {
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

    $(document).on('mouseenter',".commentNoofLikesTooltip", function (e) {
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

    function hideSuccessModal() {
        $("#successDataModal").modal('hide');
    }

    /**
     * share post save 
     */
    $(document).on("click", ".btnShare", function (e) {
        $("#loadingDataModal").modal();
        var idValue = e.target.id;
        $("#posthide_" + idValue.split("_")[1]).modal('hide');
        var shareId = idValue.split("_")[1];

        var postId = idValue.split("_")[2];
        var share = $("#shareBox_" + idValue.split("_")[1]).val();

        $('#deleteOrEditShareForm_shareId').val(postId);
        $('#deleteOrEditShareForm_textShare').val(share);

        $.ajax({
            url: shareShareURL,
            type: 'POST',
            data: $('#deleteOrEditShareForm').serialize(),
            success: function (data) {
                $('#deleteOrEditShareForm_shareId').val('');
                $('#deleteOrEditShareForm_textShare').val('');
                $("#shareViewMoreMod1_" + shareId).modal("hide");
                $("#posthidePopup_" + shareId).modal("hide");
                $('#buzz').prepend(data);
                reload();
                $("#loadingDataModal").modal('hide');
                $("#successDataModal").modal();
                $("#successBodyShare").show();
                $("#successBodyEdit").hide();
                $("#successBodyDelete").hide();
                $("#successDataModal").modal();
                setTimeout(hideSuccessModal, 3000);
                setTimeout(refresh, 10000);
            }
        });

    });

}
);

$(window).scroll(function ()
{

    if ($(window).scrollTop() >= ($(document).height() - $(window).height()))
    {
        var sharesLoadedCount = parseInt($('#buzzProfileSharesLoadedCount').html());
        var allSharesCount = parseInt($('#buzzProfileAllSharesCount').html());
        var sharesInceasingCount = parseInt($('#buzzProfileSharesInceasingCount').html());

        if ($('.loadMoreBox').css('display') == 'none') {
            if (allSharesCount > sharesLoadedCount) {
                sharesLoadedCount = sharesLoadedCount + sharesInceasingCount;
                $('#buzzProfileSharesLoadedCount').html(sharesLoadedCount);

                $('.loadMoreBox').show();

                $('#loadMorePostsForm_lastPostId').val($('#profileBuzz .lastLoadedPost').last().attr('id'));
                $('#loadMorePostsForm_profileUserId').val(trim($('#profileUserId').html()));

                $.ajax({
                    url: loadNextSharesURL,
                    type: "POST",
                    data: $('#loadMorePostsForm').serialize(),
                    success: function (data) {
                        $('#loadMorePostsForm_lastPostId').val('');
                        $('#loadMorePostsForm_profileUserId').val('');
                        $('#profileBuzz').append(data);
                        $('.loadMoreBox').hide();
                    }
                });
            }
        }

    }
});
