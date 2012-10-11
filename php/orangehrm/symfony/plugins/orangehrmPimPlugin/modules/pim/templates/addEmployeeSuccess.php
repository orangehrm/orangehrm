<?php echo stylesheet_tag('../orangehrmPimPlugin/css/addEmployeeSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmPimPlugin/js/addEmployeeSuccess'); ?>
<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_userNameRequired = "<?php echo __("Should have at least %number% characters", array('%number%' => 5)); ?>";
    var lang_passwordRequired = "<?php echo __("Should have at least %number% characters", array('%number%' => 4)); ?>";
    var lang_unMatchingPassword = "<?php echo __("Passwords do not match"); ?>";
    var lang_statusRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_locationRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var cancelNavigateUrl = "<?php echo public_path("../../index.php?menu_no_top=hr"); ?>";
    var createUserAccount = "<?php echo $createUserAccount; ?>";
    var ldapInstalled = <?php echo ($sf_user->getAttribute('ldap.available'))?'true':'false'; ?>;
    var fileHelpText = <?php echo  '"'.__(CommonMessages::FILE_LABEL_IMAGE).'"'; ?>;
    //]]>
</script>
<?php if (isset($credentialMessage)) { ?>
<div align="center" >
    <br><br><br>
    <h1 style="color: red">Credentials Required </h1>
</div>
<?php } else { ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 700px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div class="outerbox" style="">
    <div class="mainHeading"><h2><?php echo __('Add Employee'); ?></h2></div>
    <div>
        <form id="frmAddEmp" method="post" action="<?php echo url_for('pim/addEmployee'); ?>" enctype="multipart/form-data" style="width: auto">
            <table id="addEmployeeTbl"><?php echo $form->render(); ?> </table>                       
            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>"  />
                <input type="button" class="savebutton" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
<?php } ?>
