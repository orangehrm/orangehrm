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

    if ($('#buzzRightBar').length) {
        $(window).scroll(function() {
            makeRightBarSticky()
        });

        var header = $('#buzzRightBar');
        var sticky = header.offset().top - 20;

        makeRightBarSticky();

        function makeRightBarSticky() {
            if (window.pageYOffset > sticky) {
                header.addClass('sticky');
            } else {
                header.removeClass('sticky');
            }
        }
    }

});
