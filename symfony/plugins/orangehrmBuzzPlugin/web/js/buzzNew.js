$(document).ready(function () {
    var processing = false;
    $(document).on("click", ".postLike", function (e) {
        if (!processing) {
            processing = true;
            isAccess();
            var idValue = e.target.id;
            var id = "postLikebody_" + trim(idValue.split("_")[1]);
            var div = document.getElementById(id);
            var id2 = "postUnLikebody_" + trim(idValue.split("_")[1]);
            var div2 = document.getElementById(id2);
            var likeLabelId = "#noOfLikes_" + idValue.split("_")[1];
            var existingLikes = parseInt($(likeLabelId).html());
            var UnlikeLabelId = "#noOfUnLikes_" + idValue.split("_")[1];
            var existingUnLikes = parseInt($(UnlikeLabelId).html());
            var action = "like";
            $("[id=postLikeyes_" + idValue.split("_")[1] + ']').hide();
            $("[id=postLikeno_" + idValue.split("_")[1] + ']').hide();
            $("[id=postLikeLoading_" + idValue.split("_")[1] + ']').show();

            var CSRFToken = $('#actionValidatingForm__csrf_token').val();
            var shareId = idValue.split("_")[1];
            $.post(shareLikeURL, {shareId: shareId, likeAction: action, CSRFToken: CSRFToken}, function (data) {
                if (data.states === 'savedLike') {
                    var likes = trim($('#postLiketext_' + idValue.split("_")[1]).html());
                    likes++;
                    $("[id=noOfLikes_" + idValue.split("_")[1] + ']').html(data.likeCount);
                    $("[id=postNoOfLikes_" + idValue.split("_")[1] + ']').removeClass("disabledLinks");
                    $('[id=postLiketext_' + idValue.split("_")[1] + ']').html(likes);

                    //div.style.backgroundColor = 'orange';
                }
                if (data.deleted === 'yes') {
                    $("[id=noOfUnLikes_" + idValue.split("_")[1] + ']').html(data.unlikeCount);
                    var likes = trim($('#postUnLiketext_' + idValue.split("_")[1]).html());
                    likes--;
                    //$('#postUnLiketext_' + idValue.split("_")[1]).html(likes);
                }
                $("[id=postLikeLoading_" + idValue.split("_")[1] + ']').hide();
                $("[id=postUnlikeno_" + idValue.split("_")[1] + ']').show();
                $("[id=postUnlikeyes_" + idValue.split("_")[1] + ']').hide();
                $("[id=postLikeyes_" + idValue.split("_")[1] + ']').show();
                $("[id=postLikeno_" + idValue.split("_")[1] + ']').hide();
                processing = false;

            }, "json");
        }
    });
    $(document).on("click", ".postUnlike2", function (e) {
        if (!processing) {
            processing = true;
            isAccess();
            var idValue = e.target.id;
            var id = "postLikebody_" + trim(idValue.split("_")[1]);
            var div = document.getElementById(id);
            var id2 = "postUnLikebody_" + trim(idValue.split("_")[1]);
            var div2 = document.getElementById(id2);
            var likeLabelId = "#noOfLikes_" + idValue.split("_")[1];
            var existingLikes = parseInt($(likeLabelId).html());
            var UnlikeLabelId = "#noOfUnLikes_" + idValue.split("_")[1];
            var existingUnLikes = parseInt($(UnlikeLabelId).html());
            $("[id=postUnLikeLoading_" + idValue.split("_")[1] + ']').show();
            $("[id=postUnlikeyes_" + idValue.split("_")[1] + ']').hide();
            $("[id=postUnlikeno_" + idValue.split("_")[1] + ']').hide()
            var action = "unlike";
            var CSRFToken = $('#actionValidatingForm__csrf_token').val();
            var shareId = idValue.split("_")[1];
            $.post(shareLikeURL, {shareId: shareId, likeAction: action, CSRFToken: CSRFToken}, function (data) {
                if (data.deleted === 'yes') {
                    $("[id=noOfLikes_" + idValue.split("_")[1] + ']').html(data.likeCount);
                }
                if (data.likeCount == 0) {
                    $("[id=postNoOfLikes_" + idValue.split("_")[1] + ']').addClass("disabledLinks");
                }
                if (data.states === 'savedUnLike') {
                    $("[id=postUnLiketext_" + idValue.split("_")[1] + ']').html(data.unlikeCount);
                    $("[id=noOfUnLikes_" + idValue.split("_")[1] + ']').html(data.unlikeCount);
                }
                $("[id=postUnLikeLoading_" + idValue.split("_")[1] + ']').hide();
                $("[id=postLikeno_" + idValue.split("_")[1] + ']').show();
                $("[id=postLikeyes_" + idValue.split("_")[1] + ']').hide();
                $("[id=postUnlikeyes_" + idValue.split("_")[1] + ']').show();
                $("[id=postUnlikeno_" + idValue.split("_")[1] + ']').hide();
                processing = false;

            }, "json");
        }
    });
//    });
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
//            if ($.browser.chrome || $.browser.mozilla) {            
//                alert("ASDa");
//                url = (e.originalEvent || e).clipboardData.getData('text/plain') || prompt('Paste something..');
//            }
//            if (window.clipboardData) {
//                urlForIe = window.clipboardData.getData('text/plain') || prompt('Paste something..');
//            }
        if (url == "") {
            url = urlForIe;
        }
//        var url = 'https://www.youtube.com/watch?v=oNGvfuQI1Fw';
        var data = {
            'url': url,
            'actions': 'paste',
            'text': 'no'
        };

//document.domain = "92.168.1.176";
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
//                    alert(addNewVideo);
//                    alert(JSON.stringify(error));
                }
            });
        }
    }

    $("#createVideo_content").on('paste', function (e) {

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
    $("#createPost_content").bind({paste: function (e) {
            //e.preventDefault();
//            alert(window.clipboardData.getData('text/plain'));
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
//            if ($.browser.chrome || $.browser.mozilla) {            
//                alert("ASDa");
//                url = (e.originalEvent || e).clipboardData.getData('text/plain') || prompt('Paste something..');
//            }
//            if (window.clipboardData) {
//                urlForIe = window.clipboardData.getData('text/plain') || prompt('Paste something..');
//            }
            if (url == "") {
                url = urlForIe;
            }
//            var text = $("#createPost_content").val() + url;
//            $("#createPost_content").val(text);
            $("#postLinkState").html('no');
            $.ajax({
                url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/lookup?v=1.0&num=10&callback=?&q=' + encodeURIComponent(url),
                dataType: 'json',
                success: function (data) {
                    if (data.responseData) {
                        var feedurl = data.responseData.url;
                        $.ajax({
                            url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent(feedurl),
                            dataType: 'json',
                            success: function (data) {
                                if (data.responseData) {
                                    if (data.responseData.feed && data.responseData.feed.entries) {
                                        var char = 1;
                                        $.each(data.responseData.feed.entries, function (i, e) {
                                            if (char === 1) {
                                                $("#linkTitle").html(e.title);
                                                $("#createPost_linkTitle").val(e.title);
                                                $("#linkText").html(e.contentSnippet);
                                                $("#createPost_linkText").val(e.contentSnippet);
                                                $("#createPost_linkAddress").val(url);
                                                $("#postLinkData").show();
                                                $("#postLinkState").html('yes');
                                                char = 2;
                                            }
                                        });
                                    }
                                } else {
                                    $("#postLinkData").hide();
                                    $("#postLinkState").html('no');
                                }
                            }
                        });
                    } else {
                        $("#postLinkData").hide();
                        $("#postLinkState").html('no');
                    }
                }
            });
        }
    });
    
    $(document).on("click", ".commentLike", function (e) {
        isAccess();
        var idValue = e.target.id;
        var id = "commentLikebody_" + trim(idValue.split("_")[1]);
        var div = document.getElementById(id);
        var id2 = "commentUnLikebody_" + trim(idValue.split("_")[1]);
        var div2 = document.getElementById(id2);
        var likeLabelId = "#commentNoOfLikes_" + idValue.split("_")[1];
        var action = "like";
        var commentId = idValue.split("_")[1];
        var CSRFToken = $('#actionValidatingForm__csrf_token').val();
        $.post(commentLikeURL, {commentId: commentId, likeAction: action, CSRFToken: CSRFToken}, function (data) {
            if (data.states === 'savedLike') {
                var likes = trim($('#commentNoOfLiketext_' + idValue.split("_")[1]).html());
                likes++;

                $('[id=commentNoOfLiketext_' + idValue.split("_")[1] + ']').html(likes);
                $("[id=commentNoOfLikes_" + idValue.split("_")[1] + ']').html(likes);
                $("[id=cmntNoOfLikes_" + idValue.split("_")[1] + ']').removeClass("disabledLinks");
                $("[id=commentLikeyes_" + idValue.split("_")[1] + ']').show();
                $("[id=commentLikeno_" + idValue.split("_")[1] + ']').hide();
            }
            if (data.deleted === 'yes') {
                var likes = trim($('#commentNoOfUnLiketext_' + idValue.split("_")[1]).html());
                likes--;
                $('[id=commentNoOfUnLiketext_' + idValue.split("_")[1] + ']').html(likes);
                $('[id=commentNoOfUnLikes_' + idValue.split("_")[1] + ']').html(likes);
                $("[id=commentUnLikeno_" + idValue.split("_")[1] + ']').show();
                $("[id=commentUnLikeyes_" + idValue.split("_")[1] + ']').hide();
            }
        }, "json");
    });
    
    $(document).on("click", ".commentUnlike2", function (e) {

        isAccess();
        var idValue = e.target.id;
        var id = "commentLikebody_" + trim(idValue.split("_")[1]);
        var div = document.getElementById(id);
        var id2 = "commentUnLikebody_" + trim(idValue.split("_")[1]);
        var div2 = document.getElementById(id2);
        var likeLabelId = "#commentNoOfLikes_" + idValue.split("_")[1];
        var action = "unlike";
        var commentId = idValue.split("_")[1];
        var CSRFToken = $('#actionValidatingForm__csrf_token').val();
        $.post(commentLikeURL, {commentId: commentId, likeAction: action, CSRFToken: CSRFToken}, function (data) {
            ;
            if (data.deleted === 'yes') {
                var likes = trim($('#commentNoOfLiketext_' + idValue.split("_")[1]).html());
                likes--;
                if (likes === 0) {
                    $("[id=cmntNoOfLikes_" + idValue.split("_")[1] + ']').addClass("disabledLinks");
                }
                $('[id=commentNoOfLiketext_' + idValue.split("_")[1] + ']').html(likes);
                $("[id=commentNoOfLikes_" + idValue.split("_")[1] + ']').html(likes);
                $("[id=commentLikeno_" + idValue.split("_")[1] + ']').show();
                $("[id=commentLikeyes_" + idValue.split("_")[1] + ']').hide();
            }
            if (data.states === 'savedUnLike') {
                var likes = trim($('#commentNoOfUnLiketext_' + idValue.split("_")[1]).html());
                likes++;
                $('[id=commentNoOfUnLiketext_' + idValue.split("_")[1] + ']').html(likes);
                $('[id=commentNoOfUnLikes_' + idValue.split("_")[1] + ']').html(likes);
                $("[id=commentUnLikeyes_" + idValue.split("_")[1] + ']').show();
                $("[id=commentUnLikeno_" + idValue.split("_")[1] + ']').hide();
            }
        }, "json");
    });
    var modalVisible = false;
    $(".closeFeed").on("click", function (e) {
        $("#postLinkData").hide();
        $("#postLinkState").html('no');
    });
    function isAccess() {
        $.getJSON(getAccessUrl, {}, function (data) {

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
});