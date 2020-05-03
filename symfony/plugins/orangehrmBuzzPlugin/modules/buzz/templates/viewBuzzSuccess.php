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
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewShareSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewBuzzSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/transitions'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/buzzNew'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/jquerycollagePlus'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/jqueryremoveWhitespace'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewBuzzSuccess'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/buzzCommon'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/buzzNew'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/jquery.autosize.min.js'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/getSharedEmployeeListSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewPostComponent'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/photoTiling'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/messageBoxStyles'));
ini_set("memory_limit", '-1');
?>

<div id="dashBoardBuzz">

    <div class="delete-share-message-box modal hide" id="delete-share">
        <div class="hideModalPopUp"
             ><img 
                class="hideModalPopUp" id="delete-share"
                src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                />
        </div>
        <div class="modal-body message-box-modal-body">
            <?php include_component('buzz', 'messageBox', array('messageType' => 'delete')); ?>
        </div>
    </div>

    <div class="image-upload-error-message modal hide" id='imageUploadError'>

        <div class="modal-body originalPostModal-body" >
            <div class="hideModalPopUp" id='imageUploadError'
                 ><img 
                    class="hideModalPopUp" id='imageUploadError' 
                    src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                    />
            </div>
            <div class="modal-body message-box-modal-body">
                <div class="mb-heading ac_over">
                    <?php echo __("Sorry!"); ?>
                </div>
                <br>
                <div id="maxImageErrorBody">
                    <?php echo __("Only five images are allowed in a single post!"); ?>
                </div>
                <div id="invalidTypeImageErrorBody">
                    <?php echo __("Only 'gif', 'png', 'jpg', 'jpeg' type images are allowed!"); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="buzzRightBar"></div>
    <div id="buzzRightBar" class="buzzRightBar">
        <!--Start anniversary Component-->
        <div id="anniversaryComponent">
            <?php include_component('buzz', 'viewAnniversaries', array()); ?>
        </div>
        <!--End anniversary Component-->
        <!--Start Most Liked Shares Component-->
        <div id="statisticsComponent">
            <?php include_component('buzz', 'mostLikedShares', array()); ?>
        </div>
        <!--End Most Liked Shares Component-->
    </div>

    <div id="refreshTime" style="display:none;" ><?php echo $refeshTime; ?></div>
    <div id="buzzContainer">
        <div id="spinner" class="spinner">
            <img id="img-spinner"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/loading2.gif"); ?>" height="70"   />
        </div>
        <div id="postTextBox">
            <ul id="tabLinks">
                <li id="tabLink1" onclick="activateTab('page1');" class="tabButton tb_one tabSelected">
                    <div>
                        <img id="status_icon" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/status2.png"); ?>" height="25"   />
                        <span><a id="status-tab-label" class="tabLabel" href="javascript:void(0)"/><?php echo __('Update Status'); ?></a></span>
                    </div>
                </li>
                <li id="tabLink2" onclick="activateTab('page2');" class="tabButton tb_two">
                    <img id="img_upld_icon"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/img.png"); ?>" height="25"   />
                    <!--This line was previously commented. This is the new button to activate the image uploading tab which is created below-->
                    <span><a id="images-tab-label" class="tabLabel" href="javascript:void(0)"/><?php echo __('Upload Images'); ?></a></span>
                </li>
                <li id="tabLink3" onclick="activateTab('page3');" class="tabButton">
                    <img id="vid_upld_icon"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/vid.png"); ?>" height="25"   />
                    <!--This line was previously commented. This is the new button to activate the image uploading tab which is created below-->
                    <span><a id="video-tab-label" class="tabLabel" href="javascript:void(0)"/><?php echo __('Share Video'); ?></a></span>
                </li>
            </ul>
            <div id="tabCtrl">
                <div id="page1">
                    <form id="frmPublishPost" method="" action="" 
                          enctype="multipart/form-data">
                        <fieldset>
                            <ol>
                                <?php echo $postForm->render(); ?>            
                            </ol>
                        </fieldset>
                    </form>
                    <div id="postLinkData">
                        <div id="postBodySecondRow"  >
                            <a class="closeFeed">x</a>
                            <div id="postLinkState" style="display:none;">not</div>
                            <div id="postLinkAddress" style="display:none;"></div>
                            <p>
                                <a id="linkTitle" ></a> 
                            </p>
                            <p>
                            <div id="linkText"></div>
                            </p>
                        </div>
                    </div>
                    <p>
                        <input id="postSubmitBtn" class="submitBtn" type="button" value="<?php echo __("Post"); ?>" />
                    </p>
                </div>
                <div id="page2">
                    <!--Image uploading tab-->
                    <form id="frmUploadImage" method="POST" action="" 
                          enctype="multipart/form-data">
                        <fieldset>
                            <ol>
                                <?php echo $uploadImageForm->render(); ?>
                            </ol>
                        </fieldset>
                        <input id="image-upload-button" class="submitBtn" type="button" value="<?php echo __("Upload Images"); ?>" />
                        <div id="imageThumbnails"></div>
                        <p id="imgUpBtnPara">
                            <input id="imageUploadBtn" class="submitBtn" type="submit" value="<?php echo __("Post"); ?>" />
                        </p>
                    </form>
                </div>
                <div id="page3">
                    <!--Image uploading tab-->
                    <div style="display:none;" id="loadVideo">
                        <img id="img-spinner"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/vload.gif"); ?>" 
                             height="20"  />
                    </div>
                    <form id="frmUploadVideo" method="POST" action="" 
                          enctype="multipart/form-data">
                        <fieldset>
                            <ol>
                                <?php echo $videoForm->render(); ?>            
                            </ol>
                        </fieldset>
                        <p>
                        </p>
                    </form>
                    <div id="videoPostArea">
                    </div>
                </div>
            </div>



            <div class="modal hide deleteConfirmationModal" id='postViewOriginal'>

                <div class="modal-body originalPostModal-body" >
                    <div class="hideModalPopUp" id='<?php echo 'postViewOriginal' ?>'
                         ><img 
                            class="hideModalPopUp" id='<?php echo 'postViewOriginal' ?>' 
                            src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                            />
                    </div>
                    <div class="modal-body">
                        <div id="deleteConfirmationHeading">
                            <?php echo __("Do you really want to delete this?"); ?>
                        </div>
                        <input id="delete_confirm" type="button" class="btn" value="<?php echo __("Yes"); ?>" />
                        <input id="delete_discard" type="button" class="btn cancel" value="<?php echo __("No"); ?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="postLoadingBox">
            <div id="postBody">
                <img id="img-spinner"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/loading2.gif"); ?>" height="70" />
            </div>
        </div>

        <ul id="buzz">
            <div class="jason"></div>

            <?php
            foreach ($postList as $post) {
                include_component('buzz', 'viewPost', array('post' => $post, 'loggedInUser' => $loggedInUser));
            }
            ?> 
        </ul>

        <div class="loadMoreBox">
            <div id="lodingGif">
                <img id="img-spinner"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/loading2.gif"); ?>" height="70" />
            </div>
        </div>

        <!-- pop up-->
        <div class="modal hide modalPopUP"  id='<?php echo 'shareViewMoreMod3_' ?>'>
            <div class="modal-body modalPopUP-body" >
                <div class="hideModalPopUp" id='<?php echo 'shareViewMoreMod3_' ?>'
                     ><img class="hideModalPopUp" id='<?php echo 'shareViewMoreMod3_' ?>' 
                      src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                      /></div>

                <div class="shareView" id='<?php echo 'shareViewContent3_' ?>'>
                </div>
            </div>
        </div>

        <form id="actionValidateForm" method="POST" action="" 
              enctype="multipart/form-data">
            <fieldset>
                <ol>
                    <?php echo $actionValidateForm->render(); ?>            
                </ol>
            </fieldset>

        </form>

        <form id="refreshStatsForm" method="POST" action="" >
            <?php echo $refreshStatsForm->render(); ?>
        </form>

        <form id="likedOrSharedEmployeeForm" method="POST" action="" >
            <?php echo $likedOrSharedEmployeeForm->render(); ?>
        </form>

        <form id="loadMorePostsForm" method="POST" action="" >
            <?php echo $loadMorePostsForm->render(); ?>
        </form>

        <form id="deleteOrEditShareForm" method="POST" action="" >
            <?php echo $deleteOrEditShareForm->render(); ?>
        </form>

        <form id="deleteOrEditCommentForm" method="POST" action="" >
            <?php echo $deleteOrEditCommentForm->render(); ?>
        </form>

        <form id="imageUploadForm" method="POST" action="" >
            <?php echo $imageUploadForm->render(); ?>
        </form>

        <div style="display:none;" id="buzzLastTimestamp" ><?php echo $timestamp; ?></div>
        <div style="display:none;" id="buzzAllSharesCount" ><?php echo $allShareCount; ?></div>
        <div style="display:none;" id="buzzSharesLoadedCount"><?php echo $shareCount; ?></div>
        <div style="display:none;" id="buzzSharesInceasingCount"><?php echo $shareCount; ?></div>
        <div id="loggedInUserId" style="display:none;"><?php echo $loggedInUser; ?></div>

        <script  type="text/javascript">
            var getAccessUrl = '<?php echo url_for('buzz/getLogedToBuzz'); ?>';
            var loginpageURL = '<?php echo url_for('auth/login'); ?>';
            var addNewVideo = '<?php echo url_for('buzz/addNewVideo'); ?>';
            var viewMoreShare = '<?php echo url_for('buzz/viewShare'); ?>';
            var viewLikedEmployees = '<?php echo url_for('buzz/viewLikedEmployees'); ?>';
            var addBuzzPostURL = '<?php echo url_for('buzz/addNewPost'); ?>';
            var addBuzzCommentURL = '<?php echo url_for('buzz/addNewComment'); ?>';
            var shareLikeURL = '<?php echo url_for('buzz/likeOnShare'); ?>';
            var shareShareURL = '<?php echo url_for('buzz/shareAPost'); ?>';
            var commentLikeURL = '<?php echo url_for('buzz/likeOnComment'); ?>';
            var shareDeleteURL = '<?php echo url_for('buzz/deleteShare'); ?>';
            var shareEditURL = '<?php echo url_for('buzz/editShare'); ?>';
            var commentDeleteURL = '<?php echo url_for('buzz/deleteComment'); ?>';
            var commentEditURL = '<?php echo url_for('buzz/editComment'); ?>';
            var loadNextSharesURL = '<?php echo url_for('buzz/loadNextShares'); ?>';
            var getLikedEmployeeListURL = '<?php echo url_for('buzz/getLikedEmployeeList'); ?>';
            var refreshPageURL = '<?php echo url_for('buzz/getUpdatedPosts'); ?>';
            var uploadImageURL = '<?php echo url_for('buzz/uploadImage'); ?>';
            var refreshStatsURL = '<?php echo url_for('buzz/viewStatistics'); ?>';
            var getSharedEmployeeListURL = '<?php echo url_for('buzz/getSharedEmployeeList'); ?>';
            var imageFolderPath = '<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/"); ?>';
            var logoutActionURL = '<?php echo url_for('auth/logout'); ?>';
            var imageMaxWidth = <?php echo $imageMaxDimension; ?>;
            var imageMaxHeight = <?php echo $imageMaxDimension; ?>;
            var writeYourComment = "<?php echo __js('Write your comment...'); ?>";
        </script>

    </div>

</div>


