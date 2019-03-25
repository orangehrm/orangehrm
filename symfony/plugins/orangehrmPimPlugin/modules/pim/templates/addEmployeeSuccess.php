
<?php
use_javascript(plugin_web_path('orangehrmPimPlugin', 'js/addEmployeeSuccess'));
use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'css/passwordStrength.css'));
?>

<div class="box">

<?php if (isset($credentialMessage)) { ?>

<div class="message warning">
    <?php echo __(CommonMessages::CREDENTIALS_REQUIRED) ?> 
</div>

<?php } else { ?>

    <div class="head">
        <h1><?php echo __('Add Employee'); ?></h1>
    </div>

    <div class="inner" id="addEmployeeTbl">
        <?php include_partial('global/flash_messages'); ?>        
        <form id="frmAddEmp" method="post" action="<?php echo url_for('pim/addEmployee'); ?>" 
              enctype="multipart/form-data">
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="btnSave" value="<?php echo __("Save"); ?>"  />
                </p>
            </fieldset>
        </form>
    </div>

<?php } ?>
    
</div> <!-- Box -->    

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __js("Edit"); ?>";
    var save = "<?php echo __js("Save"); ?>";
    var lang_firstNameRequired = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_userNameRequired = "<?php echo __js("Should have at least %number% characters", array('%number%' => 5)); ?>";
    var lang_passwordRequired = "<?php echo __js("Should have at least %number% characters", array('%number%' => 8)); ?>";
    var lang_unMatchingPassword = "<?php echo __js("Passwords do not match"); ?>";
    var lang_statusRequired = "<?php echo __js(ValidationMessages::REQUIRED); ?>";
    var lang_locationRequired = "<?php echo __js(ValidationMessages::REQUIRED); ?>";
    var cancelNavigateUrl = "<?php echo public_path("../../index.php?menu_no_top=hr"); ?>";
    var createUserAccount = "<?php echo $createUserAccount; ?>";
    var ldapInstalled = '<?php echo ($sf_user->getAttribute('ldap.available')) ? 'true' : 'false'; ?>';
    var fieldHelpBottom = <?php echo '"' . __js(CommonMessages::FILE_LABEL_IMAGE) . '. ' . __js('Recommended dimensions: 200px X 200px') . '"'; ?>;
    var openIdEnabled = "<?php echo $openIdEnabled; ?>";
    var user_Max64Chars             = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 64)); ?>';
    var lang_maxLengthExceeds = '<?php echo __js("Password length should be less than %max% characters. Try a different password.", array('%max%' => 64)); ?>';
    var lang_passwordStrengthInvalid = '<?php echo __js("Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password.");?>';
    var requiredStrengthCheckUrl = '<?php echo url_for('securityAuthentication/checkMinimumRequiredPasswordStrengthAjax') ?>';
    var user_UserPasswordLength     = '<?php echo __js("Should have at least %number% characters", array('%number%' => 8)); ?>';
    //]]>
</script>
