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
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewBuzzSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewProfileSuccess'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewprofileSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/getSharedEmployeeListSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/buzzNew'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/buzzNew'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/messageBoxStyles'));
ini_set("memory_limit", '-1');
?>
<div id="profileFullContainer">
    <div class="buzzProfileRightBar">
        <?php
        include_component('buzz', 'viewProfileDetails', array('employee' => $employee, 'logedInUserId' => $loggedInUser));
        ?> 
    </div>
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

    <div class="modal hide" id="successDataModal" >

        <div class="modal-body" >
            <div class="mb-heading ac_over">
                <?php echo __("Success") . "!"; ?>
            </div>
            <!--<div id="successHeader" style="width: 100%;height: 20px;background-color: green;">Success</div>-->
            <div id="successBodyEdit" >
                <?php echo __("Successfully Saved"); ?>
            </div>
            <div id="successBodyShare" >
                <?php echo __("Successfully Shared"); ?>
            </div>
            <div id="successBodyDelete" >
                <?php echo __("Successfully Deleted"); ?>
            </div>

        </div>
    </div>

    <div id="dashBoardProfile">

        <div id="refreshTime" style="display:none;" ><?php echo $refeshTime ?></div>
        <div id="profileContainer">
            <ul id="profileBuzz">
                <div class="modal hide" id='deleteConfirmationModal'>

                    <div class="modal-body originalPostModal-body" >
                        <div class="hideModalPopUp" id='<?php echo 'postViewOriginal_' . $postId ?>'
                             ><img 
                                class="hideModalPopUp" id='<?php echo 'postViewOriginal_' . $postId ?>' 
                                src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                                />
                        </div>
                        <div class="modal-body">
                            <div id="maxImageErrorHeading">
                                <?php echo __("Do you really want to delete this?"); ?>
                            </div>
                            <input id="delete_confirm" type="button" class="btn" value="<?php echo __("Yes"); ?>" />
                            <input id="delete_discard" type="button" class="btn cancel" value="<?php echo __("No"); ?>" />
                        </div>
                    </div>
                </div>
                <div class="jason"></div>
                <?php
                foreach ($postList as $post) {
                    include_component('buzz', 'viewPost', array('post' => $post, 'loggedInUser' => $loggedInUser));
                }
                ?> 
            </ul>
            <div class="loadMoreBox">
                <div id="lodingGif">
                    <img id="img-spinner"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/loading2.gif"); ?>" height="70"/>
                </div>
            </div>

            <!--start loading window popup window-->
            <div class="modal hide" id="loadingDataModal" >
                <div class="modal-body loadingDataModal-body" >            
                    <div id="loadingModalBody" >
                        <img id="img-spinner-loading"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/vload.gif"); ?>" 
                             height="12"  />
                    </div>
                </div>
            </div>
            <!--end loading window pop up window-->

            <!-- start like window popup window-->
            <div class="modal hide modal-on-preview" id='<?php echo 'postsharehide' ?>'>
                <div id="modalHeader" >
                    <?php echo __("People who shared this post"); ?>
                </div>
                <div class="modal-body originalPostModal-body" >
                    <div class="hideModalPopUp" id='<?php echo 'postsharehide' ?>'
                         ><img 
                            class="hideModalPopUp" id='<?php echo 'postsharehide' ?>' 
                            src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                            /></div>
                    <div class=""  id='<?php echo 'postsharehidebody' ?>'></div>

                </div>
            </div>
            <!-- end like window pop up window-->

            <!-- start like window popup window-->
            <div class="modal hide modal-on-preview" id='<?php echo 'postlikehide' ?>'>
                <div id="modalHeader" >
                    <?php echo __("People who like this post"); ?>
                </div>
                <div class="modal-body originalPostModal-body" >
                    <div class="hideModalPopUp" id='<?php echo 'postlikehide' ?>'
                         ><img 
                            class="hideModalPopUp" id='<?php echo 'postlikehide' ?>' 
                            src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
                            /></div>
                    <div class=""  id='<?php echo 'postlikehidebody' ?>'></div>

                </div>
            </div>
            <!-- end like window pop up window-->

            <div style="display:none;" id="loadMorePosts">
                <a href="javascript:void(0)" class="loadMorePostsLink" id=<?php echo $postId ?>><?php echo __("Load more posts"); ?></a>
            </div> 
            <div id="profileUserId" style="display:none;"><?php echo $profileUserId; ?></div>
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

            <div style="display:none;" id="buzzProfileAllSharesCount" ><?php echo $allShareCount; ?></div>
            <div style="display:none;" id="buzzProfileSharesLoadedCount"><?php echo $shareCount; ?></div>
            <div style="display:none;" id="buzzProfileSharesInceasingCount"><?php echo $shareCount; ?></div>
            <script  type="text/javascript">

                var viewMoreShare = '<?php echo url_for('buzz/viewShare'); ?>';
                var profilePage = '<?php echo url_for('buzz/viewProfile?empNumber='); ?>';
                var loginpageURL = '<?php echo url_for('auth/login'); ?>';
                var addNewVideo = '<?php echo url_for('buzz/addNewVideo'); ?>';
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
                var loadNextSharesURL = '<?php echo url_for('buzz/loadMoreProfile'); ?>';
                var getLikedEmployeeListURL = '<?php echo url_for('buzz/getLikedEmployeeList'); ?>';
                var refreshPageURL = '<?php echo url_for('buzz/refreshProfile'); ?>';
                var uploadImageURL = '<?php echo url_for('buzz/uploadImage'); ?>';
                var getAccessUrl = '<?php echo url_for('buzz/getLogedToBuzz'); ?>';
                var refreshStatsURL = '<?php echo url_for('buzz/viewStatistics'); ?>';
                var viewMoreShare = '<?php echo url_for('buzz/viewShare'); ?>';
                var getSharedEmployeeListURL = '<?php echo url_for('buzz/getSharedEmployeeList'); ?>';
                var logoutActionURL = '<?php echo url_for('auth/logout'); ?>';
            </script>

        </div>
    </div>
</div>
