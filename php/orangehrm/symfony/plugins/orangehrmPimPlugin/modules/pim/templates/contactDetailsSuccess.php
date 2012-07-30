<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<?php echo javascript_include_tag('../orangehrmPimPlugin/js/contactDetailsSuccess'); ?>
<?php echo stylesheet_tag('../orangehrmPimPlugin/css/contactDetailsSuccess'); ?>

<script type="text/javascript">

    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var invalidHomePhoneNumber = '<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>';
    var invalidMobilePhoneNumber = '<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>';
    var invalidWorkPhoneNumber = '<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>';
    var incorrectWorkEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var incorrectOtherEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var fileModified = 0;
    var emails = <?php echo json_encode($form->getEmailList()); ?>;
    var emailList =eval(emails);
    var lang_emailExistmsg = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    
    <?php if (isset($empNumber)) { ?>
                    var empNumber = '<?php echo $empNumber; ?>';
    <?php } else { ?>
                    var empNumber = "";
    <?php } ?>
    
    //]]>
</script>

<!-- common table structure to be followed -->
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
            <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form));?>
        </td>
        <td valign="top">
            <table cellspacing="0" cellpadding="0" border="0" width="90%">
                <tr>
                    <td valign="top" width="750">
                        <!-- this space is for contents -->
                        <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 530px;">
                            <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                        </div>
                        <?php if ($contactDetailsPermission->canRead()) { ?>
                        <div class="outerbox">
                            <div class="mainHeading"><h2><?php echo __('Contact Details'); ?></h2></div>
                            <div>
                                <form id="frmEmpContactDetails" method="post" action="<?php echo url_for('pim/contactDetails'); ?>">
                                    <?php echo $form['_csrf_token']; ?>
                                    <?php echo $form['empNumber']->render(); ?>
                                    <br />
                                    <div>
                                        <?php echo $form['street1']->renderLabel(__("Address Street 1")); ?>
                                        <?php echo $form['street1']->render(array("class" => "txtBox", "maxlength" => 70, 'size' => 35)); ?>
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['street2']->renderLabel(__("Address Street 2")); ?>
                                        <?php echo $form['street2']->render(array("class" => "txtBox", "maxlength" => 70, 'size' => 35)); ?>
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['city']->renderLabel(__("City")); ?>
                                        <?php echo $form['city']->render(array("class" => "txtBox", "maxlength" => 70)); ?>
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['province']->renderLabel(__("State/Province")); ?>
                                        <?php echo $form['province']->render(array("class" => "txtBox", "maxlength" => 70)); ?>
                                        <?php echo $form['state']->render(array("class" => "drpDown")); ?>
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['emp_zipcode']->renderLabel(__("Zip/Postal Code")); ?>
                                        <?php echo $form['emp_zipcode']->render(array("class" => "txtBoxSmall", "maxlength" => 10)); ?>
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['country']->renderLabel(__("Country")); ?>
                                        <?php echo $form['country']->render(array("class" => "drpDown")); ?>
                                        <br class="clear" />
                                    </div>
                                    <br />
                                    <div class="hrLine" >&nbsp;</div>
                                    <div>
                                        <?php echo $form['emp_hm_telephone']->renderLabel(__("Home Telephone")); ?>
                                        <?php echo $form['emp_hm_telephone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                                        <br class="clear" />
                                    </div>
                                    
                                    <div>
                                        <?php echo $form['emp_mobile']->renderLabel(__("Mobile")); ?>
                                        <?php echo $form['emp_mobile']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['emp_work_telephone']->renderLabel(__("Work Telephone")); ?>
                                        <?php echo $form['emp_work_telephone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                                        <br class="clear" />
                                    </div>
                                    <br />
                                    <div class="hrLine" >&nbsp;</div>
                                    <div>
                                        <?php echo $form['emp_work_email']->renderLabel(__("Work Email")); ?>
                                        <?php echo $form['emp_work_email']->render(array("class" => "txtBox", "maxlength" => 50)); ?>
                                        
                                        <br class="clear" />
                                    </div>
                                    <div>
                                        <?php echo $form['emp_oth_email']->renderLabel(__("Other Email")); ?>
                                        <?php echo $form['emp_oth_email']->render(array("class" => "txtBox", "maxlength" => 50)); ?>
                                        <br class="clear" />
                                    </div>
                                    
                                    <div class="formbuttons">
                                        <?php if ($contactDetailsPermission->canUpdate()){?>
                                        <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
                                        <?php }?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php 
                            echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_CONTACT_DETAILS));
                            echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_CONTACT_DETAILS));
                        ?>
                    </td>
                    <td valign="top" align="center">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>