<?php 
use_stylesheet(plugin_web_path('orangehrmPimPlugin', 'css/viewPersonalDetailsSuccess.css'));
?>

<div class="box pimPane" id="employee-details">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
    <div class="personalDetails" id="pdMainContainer">
        
        <div class="head">
            <h1><?php echo __('Personal Details'); ?></h1>
        </div> <!-- head -->
    
        <div class="inner">

            <?php if ($personalInformationPermission->canRead()) : ?>

            <?php include_partial('global/flash_messages', array('prefix' => 'personaldetails')); ?>

            <form id="frmEmpPersonalDetails" method="post" action="<?php echo url_for('pim/viewPersonalDetails'); ?>">

                <?php echo $form['_csrf_token']; ?>
                <?php echo $form['txtEmpID']->render(); ?>

                <fieldset>
                    <!--
                    <div class="helpLabelContainer">
                        <div><label>First Name</label></div>
                        <div><label>Middle Name</label></div>
                        <div><label>Last Name</label></div>
                    </div>
                    -->
                    <ol>
                        <li class="line nameContainer">
                            <label for="Full_Name" class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
                            <ol class="fieldsInLine">
                                <li>
                                    <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                                    <?php echo $form['txtEmpFirstName']->render(array("class" => "block default editable", "maxlength" => 30, "title" => __('First Name'))); ?>
                                </li>
                                <li>
                                    <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                                    <?php echo $form['txtEmpMiddleName']->render(array("class" => "block default editable", "maxlength" => 30, "title" => __('Middle Name'))); ?>
                                </li>
                                <li>
                                    <div class="fieldDescription"><em>*</em> <?php echo __('Last Name'); ?></div>
                                    <?php echo $form['txtEmpLastName']->render(array("class" => "block default editable", "maxlength" => 30, "title" => __('Last Name'))); ?>
                                </li>
                            </ol>    
                        </li>
                    </ol>
                    <ol>
                        <li>
                            <label for="personal_txtEmployeeId"><?php echo __('Employee Id'); ?></label>
                            <?php echo $form['txtEmployeeId']->render(array("maxlength" => 10, "class" => "editable")); ?>
                        </li>
                        <li>
                            <label for="personal_txtOtherID"><?php echo __('Other Id'); ?></label>
                            <?php echo $form['txtOtherID']->render(array("maxlength" => 30, "class" => "editable")); ?>
                        </li>
                        <li class="long">
                            <label for="personal_txtLicenNo"><?php echo __("Driver's License Number"); ?></label>
                            <?php echo $form['txtLicenNo']->render(array("maxlength" => 30, "class" => "editable")); ?>
                        </li>
                        <li>
                            <label for="personal_txtLicExpDate"><?php echo __('License Expiry Date'); ?></label>
                            <?php echo $form['txtLicExpDate']->render(array("class"=>"calendar editable")); ?>
                        </li>
                        <?php if ($showSSN) : ?>
                        <li class="new">
                            <label for="personal_txtNICNo"><?php echo __('SSN Number'); ?></label>
                            <?php echo $form['txtNICNo']->render(array("class" => "editable", "maxlength" => 30)); ?>
                        </li>                    
                        <?php endif; ?>
                        <?php if ($showSIN) : ?>
                        <li class="<?php echo !($showSSN)?'new':''; ?>">
                            <label for="personal_txtSINNo"><?php echo __('SIN Number'); ?></label>
                            <?php echo $form['txtSINNo']->render(array("class" => "editable", "maxlength" => 30)); ?>
                        </li>                    
                        <?php endif; ?>                    
                    </ol>
                    <ol>
                        <li class="radio">
                            <label for="personal_optGender"><?php echo __("Gender"); ?></label>
                            <?php echo $form['optGender']->render(array("class"=>"editable")); ?>
                        </li>
                        <li>
                            <label for="personal_cmbMarital"><?php echo __('Marital Status'); ?></label>
                            <?php echo $form['cmbMarital']->render(array("class"=>"editable")); ?>
                        </li>
                        <li class="new">
                            <label for="personal_cmbNation"><?php echo __("Nationality"); ?></label>
                            <?php echo $form['cmbNation']->render(array("class"=>"editable")); ?>
                        </li>
                        <li>
                            <label for="personal_DOB"><?php echo __("Date of Birth"); ?></label>
                            <?php echo $form['DOB']->render(array("class"=>"editable")); ?>
                        </li>
                        <?php if(!$showDeprecatedFields) : ?>
                        <li class="required new">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                        <?php endif; ?>
                    </ol>    
                    <?php if($showDeprecatedFields) : ?>    
                    <ol>
                        <li>
                            <label for="personal_txtEmpNickName"><?php echo __("Nick Name"); ?></label>
                            <?php echo $form['txtEmpNickName']->render(array("maxlength" => 30, "class" => "editable")); ?>
                        </li>
                        <li>
                            <label for="personal_chkSmokeFlag"><?php echo __('Smoker'); ?></label>
                            <?php echo $form['chkSmokeFlag']->render(array("class" => "editable")); ?>
                        </li>
                        <li class="new">
                            <label for="personal_txtMilitarySer"><?php echo __("Military Service"); ?></label>
                            <?php echo $form['txtMilitarySer']->render(array("maxlength" => 30, "class" => "editable")); ?>
                        </li>
                        <li class="required new">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>                    
                    </ol>
                    <?php endif; ?>                        

                    <?php  if ($personalInformationPermission->canUpdate()) : ?>
                    <p><input type="button" id="btnSave" value="<?php echo __("Edit"); ?>" /></p>
                    <?php endif; ?>

                </fieldset>
            </form>

            <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>

        </div> <!-- inner -->
        
    </div> <!-- pdMainContainer -->

    
    <?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_PERSONAL_DETAILS));?>
    <?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_PERSONAL_DETAILS));?>
    
</div> <!-- employee-details -->
 
<?php //echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php //echo javascript_include_tag('orangehrm.datepicker.js')?>

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_firstNameRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_lastNameRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_selectGender = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';

    var fileModified = 0;
    
    var readOnlyFields = <?php echo json_encode($form->getReadOnlyWidgetNames());?>
    
 
    //]]>
</script>

<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/viewPersonalDetailsSuccess')); ?>
