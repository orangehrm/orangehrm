
<div class="box twoColumn">

    <div class="head">
        <h1><?php echo __("Mail Configuration")?></h1>
    </div>
    
	<div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
        <?php include_partial('global/form_errors', array('form' => $form)); ?>

	    <form action="<?php echo url_for('admin/listMailConfiguration');?>" method="post" id="frmSave" name="frmSave" class="longLabels">

            <?php echo $form['_csrf_token']; ?>
	        
            <fieldset>
                
                <ol>
                    <li>
                        <?php echo $form['txtMailAddress']->renderLabel(__("Mail Sent As") . ' <em>*</em>'); ?>
                        <?php echo $form['txtMailAddress']->render(array("maxlength" => 100)); ?>                        
                    </li>
                    <li>
                        <?php echo $form['cmbMailSendingMethod']->renderLabel(__("Sending Method")); ?>
                        <?php echo $form['cmbMailSendingMethod']->render(); ?>
                    </li>
                    <li id="divsendmailControls" class="toggleDiv">
                        <?php echo $form['txtSendmailPath']->renderLabel(__("Path to Sendmail")); ?>
                        <?php echo $form['txtSendmailPath']->render(array("maxlength" => 100)); ?>
                    </li>      
                </ol>
                
                <ol id="divsmtpControls" class="toggleDiv">
                    <li>
                        <?php echo $form['txtSmtpHost']->renderLabel(__("SMTP Host")); ?>
                        <?php echo $form['txtSmtpHost']->render(array("maxlength" => 100)); ?>
                    </li>
                    <li>
                        <?php echo $form['txtSmtpPort']->renderLabel(__("SMTP Port")); ?>
                        <?php echo $form['txtSmtpPort']->render(array("maxlength" => 100)); ?>
                    </li>               
                    <li class="line radio">
                        <?php echo $form['optAuth']->renderLabel(__("Use SMTP Authentication")); ?>
                        <?php echo $form['optAuth']->render(); ?>
                    </li>               
                    <li>
                        <?php echo $form['txtSmtpUser']->renderLabel(__("SMTP User")); ?>
                        <?php echo $form['txtSmtpUser']->render(array("maxlength" => 100)); ?>
                    </li>               
                    <li>
                        <?php echo $form['txtSmtpPass']->renderLabel(__("SMTP Password")); ?>
                        <?php echo $form['txtSmtpPass']->render(array("maxlength" => 100)); ?>
                    </li>        
                    <li class="line radio">
                        <?php echo $form['optSecurity']->renderLabel(__("User Secure Connection")); ?>
                        <?php echo $form['optSecurity']->render(array("maxlength" => 100)); ?>
                    </li>  
                </ol>
                
                <ol>
                    <li>
                        <?php echo $form['chkSendTestEmail']->renderLabel(__("Send Test Email")); ?>
                        <?php echo $form['chkSendTestEmail']->render(array("maxlength" => 100)); ?>
                    </li>
                    <li>
                        <?php echo $form['txtTestEmail']->renderLabel(__("Test Email Address")); ?>
                        <?php echo $form['txtTestEmail']->render(array("maxlength" => 100)); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" value="<?php echo __("Edit")?>"   id="editBtn" class=""/>
                    <input type="button" value="<?php echo __("Reset")?>" id="resetBtn"  tabindex="3"  class="reset"/>
                </p>
                
            </fieldset>
            
        </form>

    </div>

</div>

<script type="text/javascript">
//<![CDATA[    

    var requiredMsg = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var validEmailMsg = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    
    $(document).ready(function() {
        
        $('#emailConfigurationForm_chkSendTestEmail').attr('checked', false);
        $('#emailConfigurationForm_txtTestEmail').val('');
        
        var mode	=	'edit';
        
        //Disable all fields
        $('#frmSave :input').attr('disabled', true);
        $('#editBtn').removeAttr('disabled');
        
        // Displaying the appropriate send mail method controls when page is ready
        toggleSendMailMethodControls();
        
        $("#editBtn").click(function() {
            
            if( mode == 'edit')
            {
                $('#editBtn').attr('value', "<?php echo __('Save'); ?>");
                $('#frmSave :input').removeAttr('disabled');
                toggleSMTPAuthenticationFields();        
                checkSendTestMail();                
                mode = 'save';
            }else
            {
                $('#frmSave').submit();
            }
        });
        
        //Validate the form
        var validator = $("#frmSave").validate({
            rules: {
                'emailConfigurationForm[txtMailAddress]': { 
                    required: true,
                    email: true,
                    onkeyup: 'if_invalid'
                }
            },
            messages: {
                'emailConfigurationForm[txtMailAddress]': {
                    required: requiredMsg,
                    email: validEmailMsg
                }
            }
        });
        
        $("label[for=emailConfigurationForm_txtTestEmail] em").remove();
        $("#emailConfigurationForm_chkSendTestEmail").change(checkSendTestMail);
        
        checkAuthenticationActivate();
        $("#emailConfigurationForm_optAuth_login, #emailConfigurationForm_optAuth_none").change(function() {
            checkAuthenticationActivate();
        })
        
        
        checkSmtpValidation();
        $("#emailConfigurationForm_cmbMailSendingMethod").change(function() {
            checkSmtpValidation();
        })
        //When click reset buton
        $("#resetBtn").click(function() {
            document.forms[0].reset('');
            validator.resetForm();
        });
        
        // When changing the mail sending method
        $("#emailConfigurationForm_cmbMailSendingMethod").change(toggleSendMailMethodControls);
        
        // When changing the Use SMTP Authentication
        $("#emailConfigurationForm_optAuth_login").change(toggleSMTPAuthenticationFields);
        $("#emailConfigurationForm_optAuth_none").change(toggleSMTPAuthenticationFields);
    });
    
    function toggleSendMailMethodControls(){
        $(".toggleDiv").hide();
        divId = "#div" + $("#emailConfigurationForm_cmbMailSendingMethod").val() + "Controls";
        $(divId).show();
    }
    
    function checkSendTestMail() {
        
        if($("#emailConfigurationForm_chkSendTestEmail").attr("checked")){
            $("label[for=emailConfigurationForm_txtTestEmail]").append(' <em>*</em>');                
            $("#emailConfigurationForm_txtTestEmail")
                .rules("add", {
                    required: true,
                    email: true,
                    onkeyup: 'if_invalid',
                    messages: {
                        required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                        email: '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>'
                    }
                });
                
                $("#emailConfigurationForm_txtTestEmail").removeAttr('disabled');
                
        } else {
            $("label[for=emailConfigurationForm_txtTestEmail] em").remove();
            $("#emailConfigurationForm_txtTestEmail").rules("remove", "required email onkeyup")
                
            $("#emailConfigurationForm_txtTestEmail").attr('disabled', true);
        }
    }
            
    function checkSmtpValidation(){
        if($("#emailConfigurationForm_cmbMailSendingMethod").val() == 'smtp'){
            $("#emailConfigurationForm_txtSmtpHost").rules("add", {
                required: true,
                messages: {
                    required: requiredMsg
                }
            });
            $("#emailConfigurationForm_txtSmtpPort").rules("add", {
                required: true,
                number: true,
                maxlength: 10,
                messages: {
                    required: requiredMsg,
                    number: '<?php echo __('Should be a number'); ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 10)); ?>'
                }
            });
        } else {
            $("#emailConfigurationForm_txtSmtpHost").rules("remove", "required");
            $("#emailConfigurationForm_txtSmtpPort").rules("remove", "required");
        }
    }
    
    function checkAuthenticationActivate() {
        if($("#emailConfigurationForm_optAuth_login").attr("checked")){
            $("label[for=emailConfigurationForm_txtSmtpUser]").append(' <em>*</em>');
            $("label[for=emailConfigurationForm_txtSmtpPass]").append(' <em>*</em>');
            $("#emailConfigurationForm_txtSmtpUser").rules("add", {
                required: true,
                messages: {
                    required: requiredMsg
                }
            });
            $("#emailConfigurationForm_txtSmtpPass").rules("add", {
                required: true,
                messages: {
                    required: requiredMsg
                }
            });
        } else {
            $("#emailConfigurationForm_txtSmtpUser").rules("remove", "required");
            $("#emailConfigurationForm_txtSmtpPass").rules("remove", "required");
            $("label[for=emailConfigurationForm_txtSmtpUser] em").remove();
            $("label[for=emailConfigurationForm_txtSmtpPass] em").remove();
        }
    }
    function toggleSMTPAuthenticationFields() {
        if ($('#emailConfigurationForm_optAuth_login').attr('checked')) {
            
            $('#emailConfigurationForm_txtSmtpUser').removeAttr('disabled');
            $('#emailConfigurationForm_txtSmtpPass').removeAttr('disabled');
        } else {
            $('#emailConfigurationForm_txtSmtpUser').attr('disabled', true);
            $('#emailConfigurationForm_txtSmtpPass').attr('disabled', true);
        }
    }
//]]>    
</script>