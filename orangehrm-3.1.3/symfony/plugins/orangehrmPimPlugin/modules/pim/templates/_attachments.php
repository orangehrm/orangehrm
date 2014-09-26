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
$hasAttachments = count($attachmentList) > 0;
?>

<?php if ($permission->canRead()) : ?>

<a name="attachments"></a>

<div id="addPaneAttachments">
    <div class="head" id="saveHeading">
        <h1><?php echo __('Add Attachment'); ?></h1>
    </div> <!-- head -->
    <div class="inner">
        
        <?php include_partial('global/flash_messages', array('prefix' => 'saveAttachmentPane')); ?>

        <form name="frmEmpAttachment" id="frmEmpAttachment" method="post" enctype="multipart/form-data" action="<?php echo url_for('pim/updateAttachment?empNumber='.$employee->empNumber); ?>">

            <?php echo $form['_csrf_token']; ?>
            <input type="hidden" name="EmpID" value="<?php echo $employee->empNumber;?>"/>
            <input type="hidden" name="seqNO" id="seqNO" value=""/>
            <input type="hidden" name="screen" value="<?php echo $screen;?>" />
            <input type="hidden" name="commentOnly" id="commentOnly" value="0" />

            <fieldset>
                <ol>
                    <li id="currentFileLi">
                        <label><?php echo __("Current File")?></label>
                        <span id="currentFileSpan"></span>
                    </li>                    
                    <li class="fieldHelpContainer">
                        <label id="selectFileSpan" style="height:100%"><?php echo __("Select File")?> <em>*</em></label>
                        <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />                        
                        <input type="file" name="ufile" id="ufile" />
                        <label class="fieldHelpBottom"><?php echo __(CommonMessages::FILE_LABEL_SIZE); ?></label>
                    </li>
                    <li class="largeTextBox">
                        <label><?php echo __("Comment")?></label>
                        <textarea name="txtAttDesc" id="txtAttDesc" rows="3" cols="35" ></textarea>
                    </li>
                    <li class="required"><em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></li>
                </ol>
                <p>
                    <input type="button" name="btnSaveAttachment" id="btnSaveAttachment" value="<?php echo __("Upload");?>" />
                    <input type="button" id="btnCommentOnly" value="<?php echo __("Save Comment Only"); ?>" />
                    <input type="button" class="cancel" id="cancelButton" value="<?php echo __("Cancel"); ?>" />
                </p>
            </fieldset>        

        </form> <!-- frmEmpAttachment -->   
        
    </div> <!-- inner -->
</div> <!-- addPaneAttachments -->


<div id="attachmentList" class="miniList">
    <div class="head">
        <h1><?php echo __('Attachments'); ?></h1>
    </div>
    <div class="inner">
        
        <?php include_partial('global/flash_messages', array('prefix' => 'listAttachmentPane')); ?>

        <form name="frmEmpDelAttachments" id="frmEmpDelAttachments" method="post" action="<?php echo url_for('pim/deleteAttachments?empNumber='.$employee->empNumber); ?>">

            <?php echo $deleteForm['_csrf_token']; ?>
            <input type="hidden" name="EmpID" value="<?php echo $employee->empNumber;?>"/>

            <p id="attachmentActions">
                <?php if ($permission->canCreate()) : ?>
                <input type="button" class="addbutton" id="btnAddAttachment" value="<?php echo __("Add");?>" />
                <?php elseif (!$hasAttachments) :
                        echo __(TopLevelMessages::NO_RECORDS_FOUND);
                      endif; // $permission->canCreate() ?>
                <?php if ($permission->canDelete() && $hasAttachments) : ?>
                 <input type="button" class="delete" id="btnDeleteAttachment" value="<?php echo __("Delete");?>"/>
                <?php endif; // $permission->canDelete() && $hasAttachments ?>
            </p>
            
            <?php if ($hasAttachments) : ?>
            
                <table id="tblAttachments" cellpadding="0" cellspacing="0" width="100%" class="table tablesorter">
                    <thead>
                        <tr>
                            <?php if ($permission->canDelete()){?>
                            <th width="2%"><input type="checkbox" id="attachmentsCheckAll" class="checkboxAtch"/></th>
                            <?php }?>
                            <th width="15%"><?php echo __("File Name")?></th>
                            <th width="38%"><?php echo __("Description")?></th>
                            <th width="10%"><?php echo __("Size")?></th>
                            <th width="10%"><?php echo __("Type")?></th>
                            <th width="10%"><?php echo __("Date Added")?></th>
                            <th width="10%"><?php echo __("Added By")?></th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                            $disabled = ($permission->canDelete()) ? "" : 'disabled="disabled"';
                            $row = 0;
                        ?>
                        
                        <?php foreach ($attachmentList as $attachment) : ?>
                        <?php $cssClass = ($row%2) ? 'even' : 'odd'; ?>
                            
                            <tr class="<?php echo $cssClass;?>">
                                <?php if ($permission->canDelete()){?>
                                <td class="center">
                                    <input type="checkbox" <?php echo $disabled;?> class="checkboxAtch" 
                                    name="chkattdel[]" value="<?php echo $attachment->attach_id; ?>"/>
                                </td>
                                <?php }?>
                                <td>
                                    <?php if (!$permission->canDelete()){?>
                                        <input type="hidden" <?php echo $disabled;?> 
                                               name="chkattid[]" value="<?php echo $attachment->attach_id; ?>"/>                                    
                                    <?php }?>
                                    <a title="<?php echo __('Click to download'); ?>" target="_blank" class="fileLink tiptip"
                                    href="<?php echo url_for('pim/viewAttachment?empNumber='.$employee->empNumber . '&attachId=' . $attachment->attach_id);?>">
                                    <?php echo $attachment->filename; ?></a>
                                </td>
                                <td>
                                    <?php echo $attachment->description; ?>
                                </td>
                                <td>
                                    <?php echo add_si_unit($attachment->size); ?>
                                </td>
                                <td>
                                    <?php echo $attachment->file_type; ?>
                                </td>
                                <td>
                                    <?php echo set_datepicker_date_format($attachment->attached_time); ?>
                                </td>
                                <?php
                                $performedBy = $attachment->attached_by_name;
                                $performedBy = ($performedBy == 'Admin')?__($performedBy):$performedBy;
                                ?>
                                <td>
                                    <?php echo $performedBy; ?>
                                </td>
                                <?php if ($permission->canUpdate()) : ?>                                
                                <td>
                                    <a href="#" class="editLink"><?php echo __("Edit"); ?></a>
                                </td>
                                <?php else: ?>
                                <td>
                                </td>
                                <?php endif; ?>
                            </tr>
                        
                        <?php $row++; ?>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
            
            <?php endif; // $hasAttachments ?>
            
        </form> 

    </div>
</div> <!-- attachmentList -->    
<?php endif; // $permission->canRead() ?>

<script type="text/javascript">
    //<![CDATA[
    
    var hideAttachmentListOnAdd = <?php echo $hasAttachments ? 'false' : 'true';?>;
    var lang_EditAttachmentHeading = "<?php echo __("Edit Attachment"); ?>";
    var lang_AddAttachmentHeading = "<?php echo __("Add Attachment"); ?>";
    var lang_SelectFile = "<?php echo __("Select File");?>";
    var lang_ReplaceWith = "<?php echo __("Replace With");?>";
    var lang_PleaseSelectAFile = "<?php echo __(ValidationMessages::REQUIRED);?>";
    var lang_CommentsMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 200));?>";
    var lang_SelectAtLeastOneAttachment = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var hasError = <?php echo ($sf_user->hasFlash('saveAttachmentPane.warning'))?'true':'false'; ?>;

    var clearAttachmentMessages = true;
    
    $(document).ready(function() {
        
        $('#btnDeleteAttachment').attr('disabled', 'disabled');

        if (!hasError) {
            $('#addPaneAttachments').hide();
        }
        
        $('#currentFileLi').hide();
        $('#btnCommentOnly').hide();
        
        $("#frmEmpAttachment").data('add_mode', true);

        jQuery.validator.addMethod("attachment",
        function() {

            var addMode = $("#frmEmpAttachment").data('add_mode');
            if (!addMode) {
                return true;
            } else {
                var file = $('#ufile').val();
                return file != "";
            }
        }, ""
    );
    var attachmentValidator =
        $("#frmEmpAttachment").validate({

            rules: {
                ufile : {attachment:true},
                txtAttDesc: {maxlength: 200}
            },
            messages: {
                ufile: lang_PleaseSelectAFile,
                txtAttDesc: {maxlength: lang_CommentsMaxLength}
            }
            
        });

        //if check all button clicked
        $("#attachmentsCheckAll").click(function() {
            $("table#tblAttachments tbody input.checkboxAtch").removeAttr("checked");
            if($("#attachmentsCheckAll").attr("checked")) {
                $("table#tblAttachments tbody input.checkboxAtch").attr("checked", "checked");
            }
            
            if($('table#tblAttachments tbody .checkboxAtch:checkbox:checked').length > 0) {
                $('#btnDeleteAttachment').removeAttr('disabled');
            } else {
                $('#btnDeleteAttachment').attr('disabled', 'disabled');
            }
        });

        //remove tick from the all button if any checkbox unchecked
        $("table#tblAttachments tbody input.checkboxAtch").click(function() {
            $("#attachmentsCheckAll").removeAttr('checked');
            if($("table#tblAttachments tbody input.checkboxAtch").length == $("table#tblAttachments tbody input.checkboxAtch:checked").length) {
                $("#attachmentsCheckAll").attr('checked', 'checked');
            }
            
            if($('table#tblAttachments tbody .checkboxAtch:checkbox:checked').length > 0) {
                $('#btnDeleteAttachment').removeAttr('disabled');
            } else {
                $('#btnDeleteAttachment').attr('disabled', 'disabled');
            }
        });
        // Edit an attachment in the list
        $('#attachmentList a.editLink').click(function(event) {
            event.preventDefault();
            
            if (clearAttachmentMessages) {
                $("#attachmentsMessagebar").text("").attr('class', "");
            }
            
            attachmentValidator.resetForm();
            
            var row = $(this).closest("tr");            
            var fileName = row.find('a.fileLink').text();
            var seqNo;
            var description;
            
            var checkBox = row.find('input.checkboxAtch:first');
            if (checkBox.length > 0) {
                seqNo = checkBox.val();
                description = row.find("td:nth-child(3)").text();
            } else {
                seqNo = row.find('input[type=hidden]:first').val();
                description = row.find("td:nth-child(2)").text();                
            }
            description = jQuery.trim(description); 

            $('#seqNO').val(seqNo);
            $('#ufile').removeAttr("disabled");
            
            $('#txtAttDesc').val(description);

            $("#frmEmpAttachment").data('add_mode', false);

            $('#btnCommentOnly').show();

            // hide validation error messages
            $("label.error1col[generated='true']").css('display', 'none');
            $('#attachmentActions').hide();
            
            $("table#tblAttachments input.checkboxAtch").hide();
            
            $('#addPaneAttachments').show();
            $('#saveHeading h1').text(lang_EditAttachmentHeading);
            
            $('#currentFileLi').show();
            $('#currentFileSpan').text(fileName);
            $('#selectFileSpan').text(lang_ReplaceWith);
            
        });

        // Add a emergency contact
        $('#btnAddAttachment').click(function() {
            
            $('#currentFileLi').hide();
            $('#selectFileSpan').text(lang_SelectFile);
            
            if (clearAttachmentMessages) {
                $("#attachmentsMessagebar").text("").attr('class', "");
            }
            $('#seqNO').val('');
            $('#attachmentEditNote').text('');
            $('#txtAttDesc').val('');

            $("#frmEmpAttachment").data('add_mode', true);
            $('#btnCommentOnly').hide();

            // hide validation error messages
            $("label.error1col[generated='true']").css('display', 'none');
            
            $('#ufile').removeAttr("disabled");
            $('#attachmentActions').hide();
            $('#saveHeading h1').text(lang_AddAttachmentHeading);
            $('#addPaneAttachments').show();
            
            $("table#tblAttachments input.checkboxAtch").hide();
            $("table#tblAttachments a.editLink").hide();
            
            if (hideAttachmentListOnAdd) {
                $('#attachmentList').hide();
            }
            
        });
        
        $('#cancelButton').click(function() {
            $("#attachmentsMessagebar").text("").attr('class', "");
            
            attachmentValidator.resetForm();
            $('#addPaneAttachments').hide();
            $('#attachmentActions').show();
            $('#ufile').val('');
            $('#txtAttDesc').val('');
            $('#attachmentList').show();
            $("table#tblAttachments input.checkboxAtch").show();
            $("table#tblAttachments a.editLink").show();            
        });
        
        $('#btnDeleteAttachment').click(function() {

            var checked = $('#attachmentList input:checked').length;

            if (checked > 0) {
                $('#frmEmpDelAttachments').submit();
            }
            
        });

        $('#btnSaveAttachment').click(function() {
            $("#frmEmpAttachment").data('add_mode', true);
            $('#frmEmpAttachment').submit();
        });
        
        $('#btnCommentOnly').click(function() {
            $("#frmEmpAttachment").data('add_mode', false);
            $('#commentOnly').val('1');
            $('#frmEmpAttachment').submit();
        });
        
<?php if ($attEditPane) { ?>
        clearAttachmentMessages = false;
<?php    if ($attSeqNO === false) { ?>
    
        $('#btnAddAttachment').trigger('click');
        
<?php } else { ?>
    
        $('table#tblAttachments input.checkboxAtch[value="<?php echo $attSeqNO;?>"]').
            closest('tr').find('a.editLink').trigger('click');
        
<?php } ?>
    
       $('#txtAttDesc').val('<?php echo $attComments;?>');
        clearAttachmentMessages = true;       
<?php } ?>
      
 
    //
    // Scroll to bottom if neccessary. Works around issue in IE8 where
    // using the <a name="attachments" is not sufficient
    //
<?php  if ($scrollToAttachments) { ?>
        window.scrollTo(0, $(document).height());
<?php } ?>
    });
    //]]>
</script>