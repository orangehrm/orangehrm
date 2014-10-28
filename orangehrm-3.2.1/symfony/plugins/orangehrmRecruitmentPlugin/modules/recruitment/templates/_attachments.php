<?php
/*
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
 *
 */
?>

<?php
use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/attachmentsPartial'));
?>

<?php
$hasAttachments = count($attachmentList) > 0;
?>
<?php if($permissions->canUpdate()){?>
<a name="attachments"></a>

<div id="addPaneAttachments">
    <div class="head" id="saveHeading">
        <h1><?php echo __('Add Attachment'); ?></h1>
    </div> <!-- head -->
    <div class="inner">
        <form name="frmRecAttachment" id="frmRecAttachment" method="post" enctype="multipart/form-data" action="<?php echo url_for('recruitment/updateAttachment?screen=' . $screen); ?>">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["vacancyId"]->render(); ?>
            <?php echo $form["commentOnly"]->render(); ?>
            <?php echo $form["recruitmentId"]->render(); ?>

            <fieldset>
                <ol>
                    <li id="currentFileLi">
                        <label><?php echo __("Current File")?></label>
                        <span id="currentFileSpan"></span>
                    </li>                     
                    <li class="fieldHelpContainer">
                        <label id="selectFileSpan"><?php echo __("Select File") ?> <em>*</em></label>
                        <?php echo $form['ufile']->render(array("class" => "atachment")); ?>
                        <label class="fieldHelpBottom"><?php echo __(CommonMessages::FILE_LABEL_SIZE); ?></label>
                    </li>
                    <li class="largeTextBox">
                        <?php echo $form['comment']->renderLabel(__('Comment')); ?>
                        <?php echo $form['comment']->render(array("class" => "comment", "cols" => 36, "rows" => 3)); ?>
                    </li>
                    <li class="required"><em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></li>
                </ol>
                <p>
                    <input type="button" name="btnSaveAttachment" id="btnSaveAttachment" value="<?php echo __("Upload"); ?>" />
                    <input type="button" id="btnCommentOnly" value="<?php echo __("Save Comment Only"); ?>" />
                    <input type="button" class="cancel" id="cancelButton" value="<?php echo __("Cancel"); ?>" />
                </p>
            </fieldset>
        </form>
    </div> <!-- inner -->
</div> <!-- addPaneAttachments -->
<?php }?>

<?php if($permissions->canRead()){?>
<div id="attachmentList" class="miniList">
    <div class="head">
        <h1><?php echo __('Attachments'); ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages', array('prefix' => 'jobAttachmentPane')); ?>
        <form name="frmRecDelAttachments" id="frmRecDelAttachments" method="post" action="<?php echo url_for('recruitment/deleteAttachments?screen=' . $screen); ?>">
            <?php echo $deleteForm['_csrf_token']; ?>
            <?php if ($permissions->canUpdate()){?>
            <p id="attachmentActions">
                <input type="button" class="addbutton" id="btnAddAttachment" value="<?php echo __("Add"); ?>" />
                <?php if ($hasAttachments) : ?>
                    <input type="button" class="delete" id="btnDeleteAttachment" value="<?php echo __("Delete"); ?>"/>
                <?php endif; // $hasAttachments ?>
            </p>
            <?php }?>
            <?php if ($hasAttachments) : ?>
                <table id="tblAttachments" cellpadding="0" cellspacing="0" width="100%" class="table tablesorter">
                    <thead>
                        <tr>
                            <?php if ($permissions->canUpdate()){?>
                                <th width="2%"><input type="checkbox" id="attachmentsCheckAll" class="checkboxAtch"/></th>
                            <?php }?>
                            <th><?php echo __("File Name") ?></th>                   
                            <th><?php echo __("Size") ?></th>
                            <th><?php echo __("Type") ?></th>
                            <th><?php echo __("Comment") ?></th>
                            <?php if ($permissions->canUpdate()){?>
                                <th></td>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //$disabled = ($locRights['delete']) ? "" : 'disabled="disabled"';
                        $row = 0;
                        foreach ($attachmentList as $attachment) {
                            $cssClass = ($row % 2) ? 'even' : 'odd';
                            ?>
                            <tr class="<?php echo $cssClass; ?>">
                                <?php if ($permissions->canUpdate()){?>
                                <td class="check">
                                    <input type='checkbox' class='checkboxAtch' name='delAttachments[]'
                                                         value="<?php echo $attachment->id; ?>"/></td>
                                <?php }?>
                                <td>
                                    <?php if ($permissions->canUpdate()){?>
                                        <a title="<?php echo $attachment->fileName; ?>" target="_blank" class="fileLink"
                                            href="<?php echo url_for('recruitment/viewAttachment?attachId=' . $attachment->id . '&screen=' . $screen); ?>"><?php echo $attachment->fileName; ?></a>
                                    <?php }else{
                                       echo $attachment->fileName; 
                                    }?>
                                </td>
                                <td><?php echo add_si_unit($attachment->fileSize); ?></td>
                                <td><?php echo $attachment->fileType; ?></td>
                                <td class="comments">
                                    <?php echo $attachment->comment; ?>
                                </td>
                                <?php if ($permissions->canUpdate()){?>
                                <td><a href="#" class="editLink"><?php echo __("Edit"); ?></a></td>
                                <?php }?>
                            </tr>
                            <?php
                            $row++;
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; // $hasAttachments ?>
        </form> 
    </div>
</div> <!-- attachmentList -->   
<?php }?>

<script type="text/javascript">
    //<![CDATA[
    
    var hideAttachmentListOnAdd = <?php echo $hasAttachments ? 'false' : 'true'; ?>;
    var lang_SelectAtLeastOneAttachment = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var id = '<?php echo $id; ?>';
    var clearAttachmentMessages = true;
    var lang_SelectFile = "<?php echo __("Select File"); ?>";
    var lang_ReplaceWith = "<?php echo __("Replace With"); ?>";
    var lang_EditAttachmentHeading = "<?php echo __("Edit Attachment"); ?>";
    var lang_AddAttachmentHeading = "<?php echo __("Add Attachment"); ?>";
    var lang_PleaseSelectAFile = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_CommentsMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_SelectAtLeastOneAttachment = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";

    // Scroll to bottom if neccessary. Works around issue in IE8 where
    // using the <a name="attachments" is not sufficient

<?php if ($scrollToAttachments) { ?>
        window.scrollTo(0, $(document).height());
<?php } ?>
    //]]>
</script>