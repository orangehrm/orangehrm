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
<?php use_stylesheet('../orangehrmPimPlugin/css/attachments'); ?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--

<?php
$hasAttachments = count($attachmentList) > 0;
if(isset($_GET['ATT_UPLOAD']) && $_GET['ATT_UPLOAD'] == 'FAILED')
{
    echo "alert('" .__("Upload Failed")."');";
}

$locRights['add'] = true;
$locRights['delete'] = true;

?>
    //--><!]]></script>
<div id="attachmentsMessagebar" class="<?php echo isset($attachmentMessageType) ? "messageBalloon_{$attachmentMessageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
    <span style="font-weight: bold;"><?php echo isset($attachmentMessage) ? $attachmentMessage : ''; ?></span>
</div>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Attachments'); ?></h2></div>
<div id="parentPaneAttachments" >
    <form name="frmEmpAttachment" id="frmEmpAttachment" method="post" enctype="multipart/form-data"
          action="<?php echo url_for('pim/updateAttachment?empNumber='.$employee->empNumber); ?>">
    <?php echo $form['_csrf_token']; ?>
        <input type="hidden" name="EmpID" value="<?php echo $employee->empNumber;?>"/>
        <input type="hidden" name="seqNO" id="seqNO" value=""/>
        <input type="hidden" name="screen" value="<?php echo $screen;?>" />
        <input type="hidden" name="commentOnly" id="commentOnly" value="0" />

        <div id="addPaneAttachments" style="display:none" >
            <div id="attachmentSubHeadingDiv"><h3 id="attachmentSubHeading" style="float:left;"><?php echo __('Add Attachment'); ?></h3><span id="attachmentEditNote"></span></div>
            <br class="clear"/>
            <ul class="single_row_form">
                <li id="fileUploadRow">
                    <label class="sizeM"><?php echo __("Select File")?> <span class="required">*</span></label>
                    <div class="input_container input_file">
                        <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                        <input type="file" name="ufile" id="ufile" class="formInputText" style="width:100%;"/>
                        <p style="float: none; width: 100%; font-size: 11px;">[<?php echo __("1M Max, any larger attachments will be ignored")?>]</p>
                    </div>
                    
                    <div class="clear"></div>
                </li>
                <li>
                    <label class="sizeM"><?php echo __("Comment")?></label>
                    <div class="input_container">
                        <textarea name="txtAttDesc" id="txtAttDesc" rows="3" cols="35" style="margin-top: 10px;" ></textarea>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>

            
            <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSaveAttachment" id="btnSaveAttachment"
                       value="<?php echo __("Upload File");?>"
                       title="<?php echo __("Upload File");?>"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <input type="button" class="plainbtn" id="btnCommentOnly" value="<?php echo __("Save Comment Only"); ?>" />
                <input type="button" class="plainbtn" id="cancelButton" value="<?php echo __("Cancel"); ?>" />
            </div>
        </div>
    </form>

    <form name="frmEmpDelAttachments" id="frmEmpDelAttachments" method="post" action="<?php echo url_for('pim/deleteAttachments?empNumber='.$employee->empNumber); ?>">
        <?php echo $deleteForm['_csrf_token']; ?>
        <input type="hidden" name="EmpID" value="<?php echo $employee->empNumber;?>"/>

        <div class="subHeading"></div>
        <div class="actionbar" id="attachmentActions">
            <div class="actionbuttons">
                               <?php if ($locRights['add'])
                               { ?>
                <input type="button" class="addbutton" id="btnAddAttachment"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                       value="<?php echo __("Add");?>" title="<?php echo __("Add");?>"/>
            <?php } ?>
                        <?php	if ($locRights['delete'] && $hasAttachments)
        { ?>
                <input type="button" class="delbutton" id="btnDeleteAttachment"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                       value="<?php echo __("Delete");?>" title="<?php echo __("Delete");?>"/>

            <?php 	} ?>
            </div>
        </div>
        <?php if ($hasAttachments) { ?>
        <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblAttachments">
            <thead>
                <tr>
                    <td class="check"><input type="checkbox" id="attachmentsCheckAll" class="checkbox"/></td>
                    <td><?php echo __("File Name")?></td>
                    <td><?php echo __("Description")?></td>
                    <td><?php echo __("Size")?></td>
                    <td><?php echo __("Type")?></td>
                    <td><?php echo __("Date Added")?></td>
                    <td><?php echo __("Added By")?></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                        <?php

        $disabled = ($locRights['delete']) ? "" : 'disabled="disabled"';
        $row = 0;
        foreach ($attachmentList as $attachment)
        {
            $cssClass = ($row%2) ? 'even' : 'odd';
            ?>
                <tr class="<?php echo $cssClass;?>">
                    <td class="check"><input type='checkbox' <?php echo $disabled;?> class='checkbox' name='chkattdel[]'
                               value="<?php echo $attachment->attach_id; ?>"/></td>
                    <td><a title="<?php echo $attachment->description; ?>" target="_blank" class="fileLink"
                           href="<?php echo url_for('pim/viewAttachment?empNumber='.$employee->empNumber . '&attachId=' . $attachment->attach_id);?>">                            
                        <?php echo $attachment->filename; ?></a></td>
                    <td class="comments">
                        <?php echo $attachment->description; ?>
                    </td>
                    <td><?php echo add_si_unit($attachment->size); ?></td>
                    <td><?php echo $attachment->file_type; ?></td>
                    <td><?php echo ohrm_format_date($attachment->attached_time); ?></td>
                    <td><?php echo $attachment->attached_by_name; ?></td>
                    <td><a href="#" class="editLink"><?php echo __("Edit"); ?></a></td>
                </tr>
            <?php   $row++;
            }
            ?>
            </tbody>
        </table>
        <?php } else { ?>
        <br class="clear" />
        <?php } ?>
    </form>

</div>
</div>
</div>

<script type="text/javascript">
    //<![CDATA[
    
    var hideAttachmentListOnAdd = <?php echo $hasAttachments ? 'false' : 'true';?>;
    var lang_EditAttachmentHeading = "<?php echo __("Edit Attachment :") ?>";
    var lang_AddAttachmentHeading = "<?php echo __("Add Attachment"); ?>";
    var lang_EditAttachmentReplaceFile = "<?php echo __("Replace file");?>";
    var lang_EditAttachmentWithNewFile = "<?php echo __("with new file");?>";
    var lang_PleaseSelectAFile = "<?php echo __("Please select a file");?>";
    var lang_CommentsMaxLength = "<?php echo __("Comment cannot exceed 200 characters in length");?>";
    var lang_SelectAtLeastOneAttachment = "<?php echo __("Please Select At Least One Attachment To Delete"); ?>";  

    var clearAttachmentMessages = true;
    
    $(document).ready(function() {

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
            $("table#tblAttachments tbody input.checkbox").removeAttr("checked");
            if($("#attachmentsCheckAll").attr("checked")) {
                $("table#tblAttachments tbody input.checkbox").attr("checked", "checked");
            }
        });

        //remove tick from the all button if any checkbox unchecked
        $("table#tblAttachments tbody input.checkbox").click(function() {
            $("#attachmentsCheckAll").removeAttr('checked');
            if($("table#tblAttachments tbody input.checkbox").length == $("table#tblAttachments tbody input.checkbox:checked").length) {
                $("#attachmentsCheckAll").attr('checked', 'checked');
            }
        });
        // Edit a emergency contact in the list
        $('#frmEmpDelAttachments a.editLink').click(function() {
            if (clearAttachmentMessages) {
                $("#attachmentsMessagebar").text("").attr('class', "");
            }
            
            attachmentValidator.resetForm();
            
            var row = $(this).closest("tr");
            var seqNo = row.find('input.checkbox:first').val();
            var fileName = row.find('a.fileLink').text();
            var description = row.find("td:nth-child(3)").text();
            description = jQuery.trim(description);

            $('#seqNO').val(seqNo);
            $('#attachmentEditNote').html(lang_EditAttachmentReplaceFile + ' <b>' + fileName + '</b> ' + lang_EditAttachmentWithNewFile);
            $('#ufile').removeAttr("disabled");
            
            $('#txtAttDesc').val(description);

            $("#frmEmpAttachment").data('add_mode', false);

            $('#btnCommentOnly').show();

            // hide validation error messages
            $("label.error1col[generated='true']").css('display', 'none');
            $('#attachmentActions').hide();
            
            $("table#tblAttachments input.checkbox").hide();
            
            $('h3#attachmentSubHeading').text(lang_EditAttachmentHeading);
            $('#addPaneAttachments').show();
        });

        // Add a emergency contact
        $('#btnAddAttachment').click(function() {
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
            $('h3#attachmentSubHeading').text(lang_AddAttachmentHeading);
            $('#addPaneAttachments').show();
            
            $("table#tblAttachments input.checkbox").hide();
            $("table#tblAttachments a.editLink").hide();
            
            if (hideAttachmentListOnAdd) {
                $('#frmEmpDelAttachments').hide();
            }
            
        });
        
        $('#cancelButton').click(function() {
            $("#attachmentsMessagebar").text("").attr('class', "");
            
            attachmentValidator.resetForm();
            $('#addPaneAttachments').hide();
            $('#attachmentActions').show();
            $('#ufile').val('');
            $('#txtAttDesc').val('');
            $('#frmEmpDelAttachments').show();
            $("table#tblAttachments input.checkbox").show();
            $("table#tblAttachments a.editLink").show();            
        });
        
        $('#btnDeleteAttachment').click(function() {            
            
            var checked = $('#frmEmpDelAttachments input:checked').length;

            if ( checked == 0 )
            {
                $("#attachmentsMessagebar").attr('class', 'messageBalloon_notice').text(lang_SelectAtLeastOneAttachment);
            }
            else
            {
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
    
        $('table#tblAttachments input.checkbox[value="<?php echo $attSeqNO;?>"]').
            closest('tr').find('a.editLink').trigger('click');
        
<?php } ?>
    
       $('#txtAttDesc').val('<?php echo $attComments;?>');
        clearAttachmentMessages = true;       
<?php } ?>
      
        
    });
    //]]>
</script>