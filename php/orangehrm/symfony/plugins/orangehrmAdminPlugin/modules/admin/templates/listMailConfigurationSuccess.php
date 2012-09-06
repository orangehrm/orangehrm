<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<?php use_stylesheet('../orangehrmAdminPlugin/css/listMailConfiguration'); ?>
<div class="formpage3col">

    <?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>

	<div class="outerbox">
		<div class="mainHeading"><h2><?php echo __("Mail Configuration")?></h2></div>

	    <form action="<?php echo url_for('admin/saveMailConfiguration');?>" onsubmit="" method="post" id="frmSave" name="frmSave">
                <?php echo $form['_csrf_token']; ?>
	        <input type="hidden" value="UpdateRecord" name="sqlState"/>
           <table cellspacing="0" cellpadding="0" border="0" width="650px">
                <tr>
                    <td class="mailTableColumn">
                        <label for="txtMailAddress"><?php echo __("Mail Sent As")?><span class="required">*</span></label>
                        <input type="text"  value="<?php  echo $mailAddress;?>" class="formInputText" id="txtMailAddress" name="txtMailAddress"/>
                    </td>
                    <td class="mailTableColumn">
                        <label for="cmbMailSendingMethod"><?php echo __("Sending Method")?></label>
                        <select id="cmbMailSendingMethod" name="cmbMailSendingMethod" class="formSelect">
                            <option value="sendmail" <?php if($emailType == "sendmail"){ echo "selected";} ?> ><?php echo __("Sendmail") ?></option>
                            <option value="smtp" <?php if($emailType == "smtp"){ echo "selected";} ?>><?php echo __("SMTP") ?></option>
                        </select>
                        <br class="clear"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="divsendmailControls" class="toggleDiv">
                        <label for="txtSendmailPath"><?php echo __("Path to Sendmail")?></label>
                        <input type="text" id="txtSendmailPath" name="txtSendmailPath" value="<?php echo $sendMailPath;?>" class="formInputText" />
                        <br class="clear"/>
                        </div>
                    </td>
                </tr>
           </table>
            <div id="divsmtpControls" class="toggleDiv">
                <table cellspacing="0" cellpadding="0" border="0" width="650px">
                    <tr>
                        <td class="mailTableColumn">
                           <label for="txtSmtpHost"><?php echo __("SMTP Host") ?> <span class="required">*</span></label>
                            <input type="text" name="txtSmtpHost" id="txtSmtpHost"  class="formInputText" value="<?php echo $smtpHost;?>" />
                        </td>
                        <td class="mailTableColumn">
                            <label for="txtSmtpPort"><?php echo __("SMTP Port"); ?> <span class="required">*</span></label>
                            <input type="text" name="txtSmtpPort" id="txtSmtpPort" class="formInputText" value="<?php echo $smtpPort;?>"/>
                            <br class="clear"/>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2">
                            <label><?php echo __("Use SMTP Authentication"); ?></label>
                            <input type="radio" name="optAuth" id="optAuthNONE" class="formRadio" value="none" <?php if($smtpAuth == '' || $smtpAuth == 'none') { echo "checked";}?> />
                            <label for="optAuthNONE" class="optionlabel"><?php echo __("No") ?></label>
                            <input type="radio" name="optAuth" id="optAuthLOGIN" class="formRadio" value="login" <?php if($smtpAuth == 'login') { echo "checked";}?>/>
                            <label for="optAuthLOGIN" class="optionlabel"><?php echo __("Yes") ?></label>
                            <br class="clear"/>
                       </td>
                   </tr>
                   <tr>
                        <td class="mailTableColumn">
                            <label for="txtSmtpUser"><?php echo __("SMTP User") ?></label>
                            <input type="text" name="txtSmtpUser" id="txtSmtpUser" class="formInputText" value="<?php echo $smtpUser;?>" />
                        </td>
                        <td class="mailTableColumn">
                            <label for="txtSmtpPass"><?php echo __("SMTP Password") ?></label>
                            <input type="password" name="txtSmtpPass" id="txtSmtpPass" class="formInputText" value="<?php echo $smtpPass;?>" />
                            <br class="clear"/>
                        </td>
                  </tr>
                  <tr>
                        <td colspan="2">
                            <label><?php echo __("User Secure Connection") ?></label>
                            <input type="radio" name="optSecurity" id="optSecurityNONE" class="formRadio" value="none" <?php if($smtpSecurity == 'none') { echo "checked";}?> />
                            <label for="optSecurityNONE"  class="optionlabel"><?php echo __("No"); ?></label>
        
                            <input type="radio" name="optSecurity" id="optSecuritySSL" class="formRadio" value="ssl" <?php if($smtpSecurity == 'ssl') { echo "checked";}?> />
                            <label for="optSecuritySSL" class="optionlabel"><?php echo __("SSL") ?></label>
        
                            <input type="radio" name="optSecurity" id="optSecurityTLS" class="formRadio" value="tls" <?php if($smtpSecurity == 'tls') { echo "checked";}?> />
                            <label for="optSecurityTLS" class="optionlabel"><?php echo __("TLS") ?></label>
                            <br class="clear" />
                        </td>
                  </tr>
              </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="0" width="650px">
            <tr>
                <td class="mailTableColumn">
                    <label for="chkSendTestEmail"><?php echo __("Send Test Email")?></label>
                    <input type="checkbox" id="chkSendTestEmail" name="chkSendTestEmail"
                        value="Yes" class="formInputText" />
                </td>
                <td class="mailTableColumn">
                    <label for="txtTestEmail"><?php echo __("Test Email Address") ?></label>
                    <input type="text" name="txtTestEmail" id="txtTestEmail" class="formInputText" />
                    <br class="clear"/>
                </td>
                
            </tr>
         </table>
	        <div class="formbuttons">
	            <input type="button" value="<?php echo __("Edit")?>"   id="editBtn" class="editbutton"/>
	            <input type="button" value="<?php echo __("Reset")?>" id="resetBtn"  tabindex="3"  class="clearbutton"/>
	        </div>

	    </form>

	</div>
	<div class="requirednotice"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
</div>

<script type="text/javascript"><!--
	$(document).ready(function() {

		var mode	=	'edit';

		//Disable all fields
		$('#frmSave :input').attr('disabled', true);
		$('#editBtn').removeAttr('disabled');

		// Displaying the appropriate send mail method controls when page is ready
		toggleSendMailMethodControls();

		// Changing the read-nly status of SMTP authentication fields when page is ready
		toggleSMTPAuthenticationFields();

		$("#editBtn").click(function() {

			if( mode == 'edit')
			{
				$('#editBtn').attr('value', "<?php echo __('Save');?>");
				$('#frmSave :input').removeAttr('disabled');
				mode = 'save';
			}else
			{

				$('#frmSave').submit();
			}
		});

		//Validate the form
		$("#frmSave").validate({
			 rules: {
			 	'txtMailAddress': { required: true }
		 	 },
		 	 messages: {
		 		'txtMailAddress': '<?php echo __(ValidationMessages::REQUIRED); ?>'
		 	 }
		 });
		$("#chkSendTestEmail").click(function() {
			if($("#chkSendTestEmail").attr("checked")){
				$("#txtTestEmail").rules("add", {
		              required: true,
		              email: true,
		             messages: {
					   required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
					   email: '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>'
		             }
		         });
	        } else {
	        	$("#txtTestEmail").rules("remove", "required");
	        	$("#txtTestEmail").rules("remove", "email");
		    }
		})
		
        checkAuthenticationActivate();
		$("#optAuthLOGIN, #optAuthNONE").change(function() {
			checkAuthenticationActivate();
        })
        
		
        checkSmtpValidation();
        checkSendMailValidation();
		$("#cmbMailSendingMethod").change(function() {
			checkSmtpValidation();
			checkSendMailValidation();
        })
		//When click reset buton
		$("#resetBtn").click(function() {
			document.forms[0].reset('');
		 });

		// When changing the mail sending method
		$("#cmbMailSendingMethod").change(toggleSendMailMethodControls);

		// When changing the Use SMTP Authentication
		$("#optAuthLOGIN").change(toggleSMTPAuthenticationFields);
		$("#optAuthNONE").change(toggleSMTPAuthenticationFields);
	 });

	function toggleSendMailMethodControls(){
		$(".toggleDiv").hide();
		divId = "#div" + $("#cmbMailSendingMethod").val() + "Controls";
		$(divId).show();
	}

	function checkSmtpValidation(){
		if($("#cmbMailSendingMethod").val() == 'smtp'){
            $("#txtSmtpHost").rules("add", {
                  required: true,
                 messages: {
                   required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                 }
             });
            $("#txtSmtpPort").rules("add", {
                required: true,
                number: true,
                maxlength: 10,
               messages: {
                 required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                 number: '<?php echo __('Should be a number'); ?>',
                 maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 10)); ?>'
               }
           });
        } else {
            $("#txtSmtpHost").rules("remove", "required");
            $("#txtSmtpPort").rules("remove", "required");
        }
	}
        
	function checkSendMailValidation(){
		if($("#cmbMailSendingMethod").val() == 'sendmail'){
            $("#txtSendmailPath").rules("add", {
                  required: true,
                  remote: "<?php echo url_for('admin/isFileExists') ?>",
                 messages: {
                   required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                   remote: '<?php echo __('File not exists'); ?>'
                 }
             });
        } else {
            $("#txtSendmailPath").rules("remove", "required");
        }
	}

    function checkAuthenticationActivate() {
        if($("#optAuthLOGIN").attr("checked")){
        	$("label[for=txtSmtpUser]").append('<span class="required">*</span>');
        	$("label[for=txtSmtpPass]").append('<span class="required">*</span>');
            $("#txtSmtpUser").rules("add", {
                  required: true,
                 messages: {
                   required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                 }
             });
            $("#txtSmtpPass").rules("add", {
                  required: true,
                 messages: {
                   required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                 }
             });
        } else {
            $("#txtSmtpUser").rules("remove", "required");
            $("#txtSmtpPass").rules("remove", "required");
            $("label[for=txtSmtpUser] span").remove();
            $("label[for=txtSmtpPass] span").remove();
        }
    }
	function toggleSMTPAuthenticationFields() {
		if ($('#optAuthLOGIN').attr('checked')) {
			$('#txtSmtpUser').removeAttr('readonly');
			$('#txtSmtpPass').removeAttr('readonly');
		} else {
			$('#txtSmtpUser').attr('readonly', true);
			$('#txtSmtpPass').attr('readonly', true);
		}
	}
--></script>