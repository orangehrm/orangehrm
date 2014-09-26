
<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/contactDetailsSuccess')); ?>

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

<div class="box pimPane" id="contact-details">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
    <div class="">
        <div class="head">
            <h1><?php echo __('Contact Details'); ?></h1>
        </div> <!-- head -->
        
        <div class="inner">
            <?php if ($contactDetailsPermission->canRead()) : ?>
            
            <?php include_partial('global/flash_messages', array('prefix' => 'contactdetails')); ?>
            
            <form id="frmEmpContactDetails" method="post" action="<?php echo url_for('pim/contactDetails'); ?>">
                <?php echo $form['_csrf_token']; ?>
                <?php echo $form['empNumber']->render(); ?>
                <fieldset>
                    <ol>
                        <li>
                            <?php echo $form['street1']->renderLabel(__("Address Street 1")); ?>
                            <?php echo $form['street1']->render(array("class" => "formInputText", "maxlength" => 70)); ?>
                        </li>
                        <li>
                            <?php echo $form['street2']->renderLabel(__("Address Street 2")); ?>
                            <?php echo $form['street2']->render(array("class" => "formInputText", "maxlength" => 70)); ?>
                        </li>
                        <li>
                            <?php echo $form['city']->renderLabel(__("City")); ?>
                            <?php echo $form['city']->render(array("class" => "formInputText", "maxlength" => 70)); ?>
                        </li>
                        <li>
                            <?php echo $form['province']->renderLabel(__("State/Province")); ?>
                            <?php echo $form['province']->render(array("class" => "formInputText", "maxlength" => 70)); ?>
                            <?php echo $form['state']->render(array("class" => "formInputText")); ?>
                        </li>
                        <li>
                            <?php echo $form['emp_zipcode']->renderLabel(__("Zip/Postal Code")); ?>
                            <?php echo $form['emp_zipcode']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
                        </li>
                        <li>
                            <?php echo $form['country']->renderLabel(__("Country")); ?>
                            <?php echo $form['country']->render(array("class" => "formInputText")); ?>
                        </li>                        
                    </ol>
                    <ol>
                        <li>
                            <?php echo $form['emp_hm_telephone']->renderLabel(__("Home Telephone")); ?>
                            <?php echo $form['emp_hm_telephone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                        </li>
                        <li>
                            <?php echo $form['emp_mobile']->renderLabel(__("Mobile")); ?>
                            <?php echo $form['emp_mobile']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                        </li>
                        <li>
                            <?php echo $form['emp_work_telephone']->renderLabel(__("Work Telephone")); ?>
                            <?php echo $form['emp_work_telephone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                        </li>
                    </ol>
                    <ol>
                        <li>
                            <?php echo $form['emp_work_email']->renderLabel(__("Work Email")); ?>
                            <?php echo $form['emp_work_email']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
                        </li>
                        <li>
                            <?php echo $form['emp_oth_email']->renderLabel(__("Other Email")); ?>
                            <?php echo $form['emp_oth_email']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
                        </li>
                    </ol>
                    <?php if ($contactDetailsPermission->canUpdate()) : ?>
                    <p>
                        <input type="button" class="" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
                    </p>
                    <?php endif; ?>
                </fieldset>
            </form>
            <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>
        </div> <!-- inner -->
        
    </div> <!-- personalDetails -->
    
    <?php 
    echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_CONTACT_DETAILS));
    echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_CONTACT_DETAILS)); 
    ?>
    
</div> <!-- End-of-contact-details -->
