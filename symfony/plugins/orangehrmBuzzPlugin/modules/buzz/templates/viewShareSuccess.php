<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewBuzzSuccess'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewBuzzSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewBuzzSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewBuzzSuccessComment'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/tooltip_css/jquery.qtip.min'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewBuzzSuccess'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/tooltip_js/jquery.qtip.min'));
?>
<div class="shareView" id='<?php echo 'shareViewContent1_' . $postId ?>'>
    <div id="photoPage" >
        <div id="postBody" style="margin-top: 0px">

            <div id="postBodyFirstRow">
                <div id="postFirstRowColumnOne">
                    <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $employeeID); ?>" border="0" id="empPic" />
                </div>
                <div id="postFirstRowColumnTwo">
                    <div id="postEmployeeName" >
                        <?php if ($postSharerDeleted) { ?>
                            <label class="name">
                                <?php echo $postEmployeeName; ?>
                            </label>
                        <?php } else { ?>
                            <label class="name">
                                <?php echo $postEmployeeName; ?>
                            </label>
                        <?php } ?>
                    </div>
                    <div id="postEmloyeeJobTitle" style="margin-bottom: 0px;margin-top: 0px">
                        <?php echo $postEmployeeJobTitle; ?>
                    </div>
                    <div id="postDateTime" style="margin-top: 0px">
                        <div id="postDate">
                            <?php echo $postDate; ?>
                        </div>
                        <div id="postTime">
                            <?php echo $postTime; ?>
                        </div>
                    </div>                        
                </div>

                <div id="postFirstRowColumnThree">
                    <?php if (($employeeID == $loggedInUser) || ($loggedInUser == '')) { ?>
                        <div id="postOptionWidget">
                            <div class="dropdown" style="margin: -70px -22px 0 0;">
                                <a class="account"  id=<?php echo $postId ?> ></a>
                                <div class="submenu" id=<?php echo 'submenu' . $postId ?>>
                                    <ul class = "root">
                                        <li ><a href = "javascript:void(0)" class="editShare" id=<?php echo 'editShare_' . $postId ?> ><?php echo __("Edit"); ?></a></li>
                                        <li ><a href = "javascript:void(0)" class="deleteShare" id=<?php echo 'deleteShare_' . $postId ?>><?php echo __("Delete"); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>


            <div id="postBodySecondRowPop"  >
                <div class="postContent" id='<?php echo 'postContent_' . $postId ?>'>
                    <?php echo BuzzTextParserService::parseText($postContent); ?>
                    <?php
                    if ($postType == '1') {
                        ?>
                        <!--SUB POST START-->
                        <div id="sharedPostBody">

                            <div id="postBodyFirstRow">
                                <div id="postFirstRowColumnOne">
                                    <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $originalPostEmpNumber); ?>" border="0" id="empPic"/>
                                </div>
                                <div id="postFirstRowColumnTwo">
                                    <div id="postEmployeeName" >
                                        <?php if ($originalPostSharerDeleted) { ?>
                                            <label class="originalPostView">
                                                <?php echo $originalPostSharerName; ?>
                                            </label>
                                        <?php } else { ?>
                                            <label class="originalPostView">
                                                <?php echo $originalPostSharerName; ?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div id="postDateTime">
                                        <div id="postDate">
                                            <?php echo $originalPostDate; ?>
                                        </div>
                                        <div id="postTime">
                                            <?php echo $originalPostTime; ?>
                                        </div>
                                    </div>                        
                                </div>
                            </div>

                            <div id="postBodySecondRowPop">
                                <div id="postContent">
                                    <?php echo BuzzTextParserService::parseText($originalPostContent); ?>
                                </div>
                            </div>
                        </div>
                        <!--SUB POST END-->

                        <?php
                    } else {
                        echo BuzzTextParserService::parseText($originalPostContent);
                    }
                    ?>
                </div>
            </div>

            <?php if ($originalPost && count($originalPost->getLinks()) > 0) { ?>
                <?php foreach ($originalPost->getLinks() as $link) { ?>
                    <?php if ($link->getType() == 1) { ?>
                        <iframe src="<?php echo $link->getLink(); ?>" width="100%" height="250" style="margin-top: 5px " frameborder="0" allowfullscreen></iframe >

                    <?php } ?>
                    <?php if ($link->getType() == 0) { ?>
                        <div id="postBodySecondRow">
                            <div id="postContent">
                                <p>
                                    <a id="linkTitle" href="<?php echo $link->getLink(); ?>">
                                        <?php echo $link->getTitle(); ?></a> 
                                </p>
                                <p>
                                <div id="linkText"><?php echo BuzzTextParserService::parseText($link->getDescription()); ?></div>
                                </p>

                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>    
            <?php } ?>  

            <?php
            $originalPost = $sf_data->getRaw('originalPost');
            $photos = null;
            if ($originalPost) {
                $photos = $originalPost->getPhotos();
            }
            $imgCount = 1;
            ?>

            <?php
            if (count($photos) > 0) {

                include_component('buzz', 'photoTilling', array('photos' => $photos, 'originalPost' => $originalPost, 'postId' => $postId));
            }
            ?>


            <div id="postBodyThirdRow">
                <div id="noOfLikesLinknewPopUp" >
                    <?php
                    if ($postNoOfLikes > 0) {
                        $tooltipClass = "postNoofLikesTooltip";
                    } else {
                        $tooltipClass = "postNoofLikesTooltip disabledLinks";
                    }
                    ?>
                    <a class="<?php echo $tooltipClass; ?>" href="javascript:void(0)" id='<?php echo 'postNoOfLikes_' . $postId ?>' >
                        <span id="<?php echo 'noOfLikes_' . $postId; ?>"><?php echo $postNoOfLikes; ?></span>
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like-this.png"); ?>" border="0" id='<?php echo 'commentLike_' . $postId ?>' 
                              height="16" width="16"/> <?php echo __("this"); ?>
                    </a>
                </div>
                <div id="noOfSharesLinknewPopUp" >
                    <a class="postNoofSharesTooltip" href="javascript:void(0)" id='<?php echo 'postNoOfShares_' . $postId ?>' >
                        <span id="<?php echo 'noOfShares_' . $postId; ?>"><?php echo $postShareCount; ?></span>
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/share2.png"); ?>" border="0"  id='<?php echo 'commentLike_' . $postId ?>' 
                              height="16" width="16"/> <?php echo __("this"); ?>
                    </a>
                </div>
                <div id="noOfUnLikesLinknewPopUp" >
                    <a class="postNoofLikesTooltip disabledLinks" href="javascript:void(0)" id='<?php echo 'postNoOfUnlikes_' . $postId ?>' >
                        <span id="<?php echo 'noOfUnLikes_' . $postId; ?>"><?php echo $postUnlike; ?></span>
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/unlike2.png"); ?>" border="0" id='<?php echo 'commentLike_' . $commentId ?>' 
                              height="16" width="16"/> <?php echo __("this"); ?>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- start share post popup window-->
    <div class="modal hide sharePostPopUpModal"   id='<?php echo 'posthidePopup_' . $postId ?>'>

        <div class="modal-body originalPostModal-body" >
            <div class="hideModalPopUp" id='<?php echo 'posthidePopup_' . $postId ?>'
                 ><img 
                    class="hideModalPopUp" id='<?php echo 'posthidePopup_' . $postId ?>' 
                    src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                    /></div>
            <div class="sharePageForm">
                <form id="frmCreateComment" method="" action="" style="margin-top: 10px;"
                      enctype="multipart/form-data">
                          <?php
                          $placeholder = __("What's on your mind");
                          echo $commentForm['comment']->render(array('id' => "shareBox_" . $postId,
                              'class' => 'shareBox sharePostText', 'rows' => '2', 'placeholder' => $placeholder));
                          ?>

                </form>
                <div id="sharedPostBody">

                    <div id="postBodyFirstRow">
                        <div id="postFirstRowColumnOne">
                            <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $originalPostEmpNumber); ?>" border="0" id="empPic"/>
                        </div>
                        <div id="postFirstRowColumnTwo">
                            <div id="postEmployeeName" >
                                <?php if ($originalPostSharerDeleted) { ?>
                                    <label class="name">
                                        <?php echo $originalPostSharerName; ?>
                                    </label>
                                <?php } else { ?>
                                    <a class="name" href="javascript:void(0);">
                                        <?php echo $originalPostSharerName; ?>
                                    </a>
                                <?php } ?>
                            </div>
                            <div id="postDateTime">
                                <div id="postDate">
                                    <?php echo $originalPostDate; ?>
                                </div>
                                <div id="postTime">
                                    <?php echo $postTime; ?>
                                </div>
                            </div>                        
                        </div>
                    </div>

                    <div id="postBodySecondRow">
                        <div id="postContent">

                            <?php
                            if (strlen($originalPostContent) > 500) {
                                echo BuzzTextParserService::parseText(substr($originalPostContent, 0, 500) . '...');
                            } else {
                                echo BuzzTextParserService::parseText($originalPostContent);
                            }
                            ?>
                        </div>
                    </div>
                    <?php if ($originalPost && count($originalPost->getLinks()) > 0) { ?>
                        <?php foreach ($originalPost->getLinks() as $link) { ?>
                            <?php if ($link->getType() == 1) { ?>
                                <div class="sharePageIframe">
                                    <iframe src="<?php echo $link->getLink(); ?>" width="100%" height="150" style="margin-top: 5px;margin: 0 auto; " frameborder="0" allowfullscreen></iframe >
                                </div>
                            <?php } ?>
                            <?php if ($link->getType() == 0) { ?>
                                <div id="postBodySecondRow">
                                    <div id="postContent" >
                                        <p>
                                            <a id="linkTitle" href="<?php echo $link->getLink(); ?>">
                                                <?php echo $link->getTitle(); ?></a> 
                                        </p>
                                        <p>
                                        <div id="linkText"><?php echo BuzzTextParserService::parseText($link->getDescription()); ?></div>
                                        </p>

                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>    
                    <?php } ?>  

                    <?php
                    $originalPost = $sf_data->getRaw('originalPost');
                    $photos = null;
                    if ($originalPost) {
                        $photos = $originalPost->getPhotos();
                    }
                    $imgCount = 1;
                    if (count($photos) > 0) {
                        ?>
                        <div class="sharePagePhotoComponent">
                            <?php include_component('buzz', 'photoTilling', array('photos' => $photos, 'originalPost' => $originalPost, 'postId' => $postId)); ?>
                        </div>
                    <?php } ?>

                    <input type="button" class="btnShare" name="btnSaveDependent" id='<?php echo 'btnShare_' . $postId . "_" . $originalPostId ?>' value="<?php echo __attr("Share"); ?>" />

                </div>
            </div>
        </div>
    </div>
    <!-- end share post pop up window-->

    <div id="photoPageComment" >
        <div id="postBodyFirstRow photo" class="photoViewEmp">
            <div id="postFirstRowColumnOneViewShare" >
                <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $employeeID); ?>" border="0" id="empPic-popUp" />
            </div>
            <div id="postFirstRowColumnTwo" >
                <div id="postEmployeeName" >
                    <?php if ($postSharerDeleted) { ?>
                        <label class="name">
                            <?php echo $postEmployeeName; ?>
                        </label>
                    <?php } else { ?>
                        <label class="name">
                            <?php echo $postEmployeeName; ?>
                        </label>
                    <?php } ?>
                </div>
                <div id="postEmloyeeJobTitle" style="margin-bottom: 0px;margin-top: 0px">
                    <?php echo $postEmployeeJobTitle; ?>
                </div>
                <div id="postDateTime">
                    <div id="postDate">
                        <?php echo $postDate; ?>
                    </div>
                    <div id="postTime">
                        <?php echo $postTime; ?>
                    </div>
                </div>                        
            </div>
        </div>
        <!--new Code of like, unlike and share buttons-->
        <div id="postBodyThirdRowNew">
            <div class="likeLinknew"  id="<?php echo 'postLikebody_' . $postId ?>" > 
                <?php if ($isLike == 'Unlike') { ?>
                    <a href="javascript:void(0)" class="<?php echo $isLike . ' postLike'; ?> tiptip" title="<?php echo __('Like')?>"  id='<?php echo 'postLikeyes_' . $postId ?>'> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/icons.png"); ?>" border="0" id='<?php echo 'postLike_' . $postId ?>'
                              class="<?php echo $isLike . ' postLike'; ?>" height="30" width="30"/></a>
                    <a style="display:none;" href="javascript:void(0)" class="<?php echo $isLike . ' postLike'; ?> tiptip" title="<?php echo __('Like')?>" id='<?php echo 'postLikeno_' . $postId ?>'> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/like.png"); ?>" border="0" id='<?php echo 'postLike_' . $postId ?>'
                              class="<?php echo $isLike . ' postLike'; ?>" height="30" width="30"/></a>
                        <?php
                    } else {
                        ?>
                    <a style="display:none;" href="javascript:void(0)" class="<?php echo $isLike . ' postLike'; ?> tiptip" title="<?php echo __('Like')?>"  id='<?php echo 'postLikeyes_' . $postId ?>'> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/icons.png"); ?>" border="0" id='<?php echo 'postLike_' . $postId ?>'
                              class="<?php echo $isLike . ' postLike'; ?>" height="30" width="30"/></a>
                    <a href="javascript:void(0)" class="<?php echo $isLike . ' postLike'; ?> tiptip" title="<?php echo __('Like')?>"  id='<?php echo 'postLikeno_' . $postId ?>'> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/like.png"); ?>" border="0" id='<?php echo 'postLike_' . $postId ?>'
                              class="<?php echo $isLike . ' postLike'; ?>" height="30" width="30"/></a>
                        <?php
                    }
                    ?>
                <img style="display:none;" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/loading2.gif"); ?>" border="0" id='<?php echo 'postLikeLoading_' . $postId ?>'
                     class="<?php echo $isLike . ' postLike'; ?>" height="30" width="30"/>
            </div>
            <div class="unlikeLinknew" id='<?php echo 'postUnLikebody_' . $postId ?>' >
                <?php if ($isUnlike == 'yes') { ?>
                    <a style="display:none;" href="javascript:void(0)" class="postUnlike2 tiptip" title="<?php echo __('Unlike')?>"  id=<?php echo 'postUnlikeno_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/unlike.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'  height="30" width="30"/></a>
                    <a  href="javascript:void(0)" class="postUnlike2 tiptip" title="<?php echo __('Unlike')?>" id=<?php echo 'postUnlikeyes_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/unlike2.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'  height="30" width="30"/></a>
                    <?php } else {
                        ?>
                    <a href="javascript:void(0)" class="postUnlike2  tiptip" title="<?php echo __('Unlike')?>" id=<?php echo 'postUnlikeno_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/unlike.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'  height="30" width="30"/></a>
                    <a  style="display:none;" href="javascript:void(0)" class="postUnlike2  tiptip" title="<?php echo __('Unlike')?>" id=<?php echo 'postUnlikeyes_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/unlike2.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'  height="30" width="30"/></a>
                        <?php
                    }
                    ?>
                <img style="display:none;" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/loading2.gif"); ?>" border="0" id='<?php echo 'postUnLikeLoading_' . $postId ?>'
                     class="<?php echo $isLike . ' postLike'; ?>" height="30" width="30"/>
            </div>

            <div class="shareLinknew" id='<?php echo 'postSharebody_' . $postId ?>' >
                <?php if ($postShareCount > 0) { ?>
                    <a href="javascript:void(0)" class="postSharePopup tiptip" title="<?php echo __('Share')?>" id=<?php echo 'postShareyes_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/share2.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'height="30" width="30"/></a>
                    <a style="display:none;" href="javascript:void(0)" class="postSharePopup tiptip" title="<?php echo __('Share')?>" id=<?php echo 'postShareno_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/share.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'height="30" width="30"/></a>

                <?php } else { ?>
                    <a style="display:none;" href="javascript:void(0)" class="postSharePopup tiptip" title="<?php echo __('Share')?>" id=<?php echo 'postShareyes_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/share2.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'height="30" width="30"/></a>
                    <a href="javascript:void(0)" class="postSharePopup tiptip" title="<?php echo __('Share')?>" id=<?php echo 'postShareno_' . $postId ?>> 
                        <img  src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/like/share.png"); ?>" 
                              border="0" id='<?php echo 'postLike_' . $postId ?>'height="30" width="30"/></a>
                        <?php
                    }
                    ?>
            </div>
        </div>
        <?php include_component('buzz', 'commentPreview', array('commentList' => $commentList, 'editForm' => $editForm, 'loggedInUser' => $loggedInUser, 'postId' => $postId, 'commentForm' => $commentForm, 'commentBoxId' => 'popShareId')); ?>

    </div>
</div>
