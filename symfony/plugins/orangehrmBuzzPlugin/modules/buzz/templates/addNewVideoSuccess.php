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
?>
<!--A single post-->
<div id="videoPostArea">
    <?php if ($isSuccessfullyPastedUrl) { ?>
        <div id="tempVideoBlock">
            <div id="postBody">
                <form id="frmSaveVideo" method="" action="" 
                      enctype="multipart/form-data">
                          <?php
                          $videoForm->setDefault('content', $url);
                          $videoForm->setDefault('linkAddress', $videoFeedUrl);
                          $placeholder = 'Write something about this video';
                          echo $videoForm['content']->render(array(
                              'class' => 'commentBox', 'rows' => '2', 'placeholder' => $placeholder));
                          echo $videoForm['linkAddress']->render();
                          echo $videoForm['_csrf_token']->render();
                          ?>

                </form>
                <div id="yuoutubeVideoId" style="display:none;"><?php echo $videoFeedUrl; ?></div>

                <div class="video-container add-new-video">
                    <iframe src="<?php echo $videoFeedUrl; ?>" width="80%" height="225px" frameborder="0" allowfullscreen></iframe >
                </div>

                <p>
                    <input id='<?php echo 'btnSaveVideo_', $videoFeedUrl; ?>' class="submitBtn btnSaveVideo" type="button" value="<?php echo __("Save Video"); ?>" />
                </p>
            </div>
        </div>

    <?php } else if ($isSuccessFullyPosted) { ?>
        <?php include_component('buzz', 'viewPost', array('post' => $postSaved, 'loggedInUser' => $loggedInUser)); ?>


    <?php } else if ($error == 'redirect') { ?>

    <?php } else if (!$isSuccessfullyPastedUrl) { ?>
        <div id="tempVideoBlock">
            <div id="postBody">
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
                <div id ='errorMessageDiv'>
                    <?php
                    include_partial('global/flash_messages');
                    ?>
                </div>
            </div>
        </div>
    <?php } else if (!$isSuccessFullyPosted) { ?>

    <?php } ?>

</div>
<!--Single post end-->
