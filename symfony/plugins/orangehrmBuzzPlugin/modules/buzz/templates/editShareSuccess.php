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
?>
<?php if ($error == 'no') { ?>
    <div id='<?php echo 'postContent_' . $post->getId() ?>'>
        <?php echo BuzzTextParserService::parseText($post->getText());
        ?>
        <?php
        if ($type == 'share') {
            $originalPost = $post->getPostShared();
            $originalPostId = $originalPost->getId();
            $originalPostEmpNumber = $originalPost->getEmployeeNumber();
            $originalPostSharerName = $originalPost->getEmployeeFirstLastName();
            $originalPostDate = $originalPost->getDate();
            $originalPostTime = $originalPost->getTime();
            $originalPostContent = $originalPost->getText();
            ?>
            <!--SUB POST START-->
            <div id="sharedPostBody">

                <div id="postBodyFirstRow">
                    <div id="postFirstRowColumnOne">
                        <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("pim/viewPhoto?empNumber=" . $originalPostEmpNumber); ?>" border="0" id="empPic" height="40" width="30"/>
                    </div>
                    <div id="postFirstRowColumnTwo">
                        <div id="postEmployeeName" >
                            <a class="name" href="javascript:void(0);" id='<?php echo 'postView_' . $post->getId() . '_' . $originalPostId ?>'>
                                <?php echo $originalPostSharerName; ?>
                            </a>
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

                <div id="postBodySecondRow">
                    <div id="postContent">
                        <?php echo BuzzTextParserService::parseText($originalPostContent); ?>
                    </div>
                </div>
            </div>
            <!--SUB POST END-->
            <div class="modal hide" id='<?php echo 'postViewOriginal_' . $post->getId() ?>'>

                <div class="modal-header">
                    <a class="close" data-dismiss="modal">×</a>
                </div>

                <div class="modal-body">

                    <div class="postView" id='<?php echo 'postViewContent_' . $post->getId() ?>'>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <div id ='errorFirstRow'>
        <?php
        include_partial('global/flash_messages');
        ?>
    </div>
<?php } ?>
