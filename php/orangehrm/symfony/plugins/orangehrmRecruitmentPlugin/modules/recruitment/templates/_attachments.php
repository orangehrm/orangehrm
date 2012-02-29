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
 *
 */
?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<?php use_stylesheet('../orangehrmRecruitmentPlugin/css/attachmentsPartial'); ?>
<?php use_javascript('../orangehrmRecruitmentPlugin/js/attachmentsPartial'); ?>
<?php $hasAttachments = count($attachmentList) > 0; ?>

<div id="attachmentsMessagebar" class="<?php echo isset($attachmentMessageType) ? "messageBalloon_{$attachmentMessageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
    <span style="font-weight: bold;"><?php echo isset($attachmentMessage) ? $attachmentMessage : ''; ?></span>
</div>

<div id="addAttachment">
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Attachments'); ?></h2></div>
    <div id="parentPaneAttachments" >
        <form name="frmRecAttachment" id="frmRecAttachment" method="post" enctype="multipart/form-data"
	      action="<?php echo url_for('recruitment/updateAttachment?screen='.$screen); ?>">
	<?php echo $form['_csrf_token']; ?>
	<?php echo $form["vacancyId"]->render(); ?>
	<?php echo $form["commentOnly"]->render(); ?>
	<?php echo $form["recruitmentId"]->render(); ?>

	 <div id="addPaneAttachments" style="display: none" >
	 <div id="attachmentSubHeadingDiv"><h3 id="attachmentSubHeading" style="float:left;"><?php echo __('Add Attachment'); ?></h3>
		 <div id="attachmentEditNote"></div>
	 </div>
	 <br class="clear"/>
	    <div>
		<?php echo $form['ufile']->renderLabel(__('Select File') . ' <span class="required">*</span>'); ?>
                <?php echo $form['ufile']->render(array("class" => "atachment", "size" =>28)); ?><br class="clear"/>
		<span class="helpText"><?php echo __(CommonMessages::FILE_LABEL_SIZE); ?></span>

                <br class="clear"/>
		<br class="clear"/>
	    </div>

	    <div>
		<?php echo $form['comment']->renderLabel(__('Comment')); ?>
                <?php echo $form['comment']->render(array("class" => "comment", "cols"=>36, "rows"=>3)); ?>
                <br class="clear"/>
	    </div>
	    </div>
	    <div id="actionButtons" class="formbuttons" style="display: none">
                <input type="button" class="savebutton" name="btnSaveAttachment" id="btnSaveAttachment"
                       value="<?php echo __("Upload");?>"
                       title="<?php echo __("Upload");?>"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <input type="button" class="plainbtn" id="btnCommentOnly" style="width: 140px" value="<?php echo __("Save Comment Only"); ?>" />
                <input type="button" class="plainbtn" id="athCancelButton" value="<?php echo __("Cancel"); ?>" />
            </div>
	</form>

	<form name="frmRecDelAttachments" id="frmRecDelAttachments" method="post" action="<?php echo url_for('recruitment/deleteAttachments?screen='.$screen);?>">
        <?php echo $deleteForm['_csrf_token']; ?>
	    <div>
                <input type="button" class="addbutton" id="btnAddAttachment"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                       value="<?php echo __("Add");?>" title="<?php echo __("Add");?>"/>
		<?php if($hasAttachments){ ?>
		<input type="button" class="delbutton" id="btnDeleteAttachment"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                       value="<?php echo __("Delete");?>" title="<?php echo __("Delete");?>"/>
		<?php } ?>
            </div>
	    <?php if ($hasAttachments) { ?>
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblAttachments">
            <thead>
                <tr>
                    <td class="check"><input type="checkbox" id="attachmentsCheckAll" class="checkboxAtch"/></td>
                    <td><?php echo __("File Name")?></td>                   
                    <td><?php echo __("Size")?></td>
                    <td><?php echo __("Type")?></td>
		    <td><?php echo __("Comment")?></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
       <?php
        //$disabled = ($locRights['delete']) ? "" : 'disabled="disabled"';
        $row = 0;
        foreach ($attachmentList as $attachment)
        {
            $cssClass = ($row%2) ? 'even' : 'odd';
            ?>
                <tr class="<?php echo $cssClass;?>">
                    <td class="check"><input type='checkbox' class='checkboxAtch' name='delAttachments[]'
                               value="<?php echo $attachment->id; ?>"/></td>
                    <td><a title="<?php echo $attachment->fileName; ?>" target="_blank" class="fileLink"
                           href="<?php echo url_for('recruitment/viewAttachment?attachId=' . $attachment->id . '&screen=' . $screen);?>"><?php echo $attachment->fileName; ?></a></td>
                    <td><?php echo add_si_unit($attachment->fileSize); ?></td>
                    <td><?php echo $attachment->fileType; ?></td>
                     <td class="comments">
                        <?php echo $attachment->comment; ?>
                    </td>
                    <td><a href="#" class="editLink"><?php echo __("Edit"); ?></a></td>
                </tr>
            <?php   $row++;
            }
            ?>
            </tbody>
        </table>
        <?php } else { ?>
        <?php } ?>
	</form>
    </div>
</div>
</div>

<script type="text/javascript">
    //<![CDATA[
	var lang_SelectAtLeastOneAttachment = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
	var id = '<?php echo $id; ?>';
	var clearAttachmentMessages = true;
	var lang_EditAttachmentReplaceFile = "<?php echo __("Replace file");?>";
	var lang_EditAttachmentWithNewFile = "<?php echo __("with new file");?>";
	var lang_EditAttachmentHeading = "<?php echo __("Edit Attachment") . " :" ?>";
	var lang_PleaseSelectAFile = "<?php echo __(ValidationMessages::REQUIRED);?>";
	var lang_CommentsMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250));?>";
	var lang_AddAttachmentHeading = "<?php echo __("Add Attachment"); ?>";

	// Scroll to bottom if neccessary. Works around issue in IE8 where
	// using the <a name="attachments" is not sufficient

	<?php  if ($scrollToAttachments) { ?>
		window.scrollTo(0, $(document).height());
	<?php } ?>
	//]]>
</script>