<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>

<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>
<script type="text/javascript">
    //<![CDATA[

    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_photoRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var deleteUrl = "<?php echo url_for('pim/viewPhotograph?option=delete&empNumber=' . $empNumber); ?>";
    var showDeteleButton = "<?php echo $showDeleteButton; ?>";
    var fileModified = "<?php echo $fileModify;?>";
    var newImgWidth = "<?php echo $newWidth; ?>";
    var newImgHeight = "<?php echo $newHeight; ?>";
    var fileFormatError = "<?php echo __('Only Types jpg, png, and gif Are Supported');?>";

    //]]>
</script>

<?php echo stylesheet_tag('../orangehrmPimPlugin/css/viewPhotographSuccess'); ?>
<!-- common table structure to be followed -->
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
        <?php include_partial('leftmenu', 
                array('empNumber' => $empNumber, 'form' => $form, 'editPhotoMode' => true));?></td>
        <td valign="top">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td valign="top">
                        <!-- this space is for contents -->
                        <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
                            <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                        </div>
                        <div class="formpage2col">
                            <div>
                                <div class="outerbox">
                                    <div class="mainHeading"><h2 id="immigrationHeading"><?php echo __('Photograph'); ?></h2></div>
                                    <?php if (($photographPermissions->canUpdate()) || ($photographPermissions->canDelete())) { ?>
                                    <form name="frmPhoto" id="frmPhoto" method="post" action="<?php echo url_for('pim/viewPhotograph'); ?>" enctype="multipart/form-data">
                                        <?php echo $form['_csrf_token']; ?>
                                        <?php echo $form['emp_number']->render();?>
                                        <?php if($form->hasGlobalErrors()) {
                                            echo $form->renderGlobalErrors();
                                        } ?>
                                        <br class="clear" />
                                        <div class="formFields">
                                            <?php echo $form['photofile']->renderLabel(__('Select a Photograph')); ?>
                                            <?php echo $form['photofile']->render(array("class" => "duplexBox")); ?>
                                            <br class="clear" />
                                            <span class="helpText"><?php echo __(CommonMessages::FILE_LABEL_IMAGE); ?></span><br class="clear" />
                                            
                                        </div>
                                        <div class="formbuttons">
                                            <?php if ($photographPermissions->canUpdate()) { ?>
                                                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Upload"); ?>" />
                                            <?php }
                                            if ($photographPermissions->canDelete() && ($showDeleteButton == 1)) { ?>
                                                <input type="button" class="savebutton" id="btnDelete" value="<?php echo __("Delete"); ?>" />
                                            <?php } ?>
                                        </div>
                                    </form>
                                    <?php }?>

                                </div>
                            </div>
                        </div>
                    </td>
                    <td valign="top" align="left">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- confirmation box -->
<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required');?>" style="display: none;">
    <?php echo __("Delete photograph?");?>
    <br /><br /><br />
    <div class="dialogButtons">
        <input type="button" id="btnYes" class="savebutton" value="<?php echo __('Ok');?>" />
        <input type="button" id="btnNo" class="savebutton" value="<?php echo __('Cancel');?>" />
    </div>
</div>

<?php echo javascript_include_tag('../orangehrmPimPlugin/js/viewPhotographSuccess'); ?>