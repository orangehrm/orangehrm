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

require_once($lan->getLangPath("full.php"));

$locRights  = $_SESSION['localRights'];
$editArr = $this->popArr['editArr'];
$formAction = $_SERVER['PHP_SELF'] . "?uniqcode=EMX&amp;id=1";
$token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_Admin_EMX_MailConfiguration; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

    var editMode = false;

	function validate() {
		var errors = new Array();
		var error = false;

		var fromEmail = $("txtMailAddress").value;

		if (fromEmail == "") {
			error = true;
			errors.push("<?php echo $lang_Error_FromEmailEmpty; ?>");
		} else if(!checkEmail(fromEmail)) {
			error = true;
			errors.push("<?php echo $lang_Error_FromEmailInvalid; ?>");
		}

		if ($("txtMailType").value == "smtp") {

			if ($("txtSmtpHost").value == "") {
				error = true;
				errors.push("<?php echo $lang_Error_SmtpHostEmpty; ?>");
			}

			var smtpPort = $("txtSmtpPort");

			if (smtpPort.value == "") {
				error = true;
				errors.push("<?php echo $lang_Error_SmtpPortEmpty; ?>");
			} else if (!numbers(smtpPort) || ((smtpPort.value <= 0) || (smtpPort.value > 65535))) {
				error = true;
				errors.push("<?php echo $lang_Error_Invalid_Port; ?>");
			}

			var authTrue = $("optAuthLOGIN").checked;

			if (authTrue && $("txtSmtpUser").value == "") {
				error = true;
				errors.push("<?php echo $lang_Error_SmtpUsernameEmpty; ?>");
			}

			if (authTrue && $("txtSmtpPass").value == "") {
				error = true;
				errors.push("<?php echo $lang_Error_SmtpPasswordEmpty; ?>");
			}

			if ($("chkTestEmail").checked == true) {

				var testEmail = $("txtTestEmail").value;

				if (testEmail == "") {
					error = true;
					errors.push("<?php echo $lang_Error_TestEmailEmpty; ?>");
				} else if(!checkEmail(testEmail)) {
					error = true;
					errors.push("<?php echo $lang_Error_TestEmailValid; ?>");
				}

			}

		}

		if (error) {
			errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
			for (i in errors) {
				errStr += " - "+errors[i]+"\n";
			}
			alert(errStr);
			return false;

		} else  {
			$('sqlState').value = 'UpdateRecord';
			$('frmEmailConfig').submit();
			return true;
		}

	}

    function resetForm() {
        $('frmEmailConfig').reset();
        changeMailType();
    }

    function edit() {

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('frmEmailConfig').submit();
            }
            return;
        }
        editMode = true;

        var emailConfigControls = new Array('txtMailAddress', 'txtMailType', 'txtSendmailPath', 'txtSmtpHost',
        									'txtSmtpPort', 'txtSmtpUser', 'txtSmtpPass', 'optAuthNONE',
        									'optAuthLOGIN', 'optSecurityNONE', 'optSecuritySSL', 'optSecurityTLS',
        									'chkTestEmail', 'txtTestEmail');

        for (i in emailConfigControls) {
            $(emailConfigControls[i]).disabled = false;
        }

        $('editBtn').value="<?php echo $lang_Common_Save; ?>";
        $('editBtn').title="<?php echo $lang_Common_Save; ?>";
        $('editBtn').className = "savebutton";

<?php } else {?>
        alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
    }

	function changeMailType() {
 		value = $('txtMailType').value;
 		panels = ['sendmailDetails', 'smtpDetails1', 'smtpDetails2', 'smtpDetails3', 'smtpDetails4', 'smtpDetails5'];

 		for (i=0; i<panels.length; i++) {
 			$(panels[i]).className = 'hide';
 		}

 		switch (value) {
 			case '<?php echo EmailConfiguration::EMAILCONFIGURATION_TYPE_SENDMAIL; ?>' :$(panels[0]).className = 'show';
 																						$(panels[5]).className = 'show';
 																					 	break;
 			case '<?php echo EmailConfiguration::EMAILCONFIGURATION_TYPE_SMTP; ?>' : $(panels[1]).className = 'show';
 																					 $(panels[2]).className = 'show';
 																					 $(panels[3]).className = 'show';
 																					 $(panels[4]).className = 'show';
 																					 $(panels[5]).className = 'show';
  																					 break;
 		}
	}

	function changeAuth() {

		var authRadio = document.frmEmailConfig.optAuth;

		var val;
		for (var i=0; i < authRadio.length; i++) {
   			if (authRadio[i].checked) {
      			val = authRadio[i].value;
      		}
   		}

   		if (val == "NONE") {
			$('txtSmtpPass').disabled = true;
			$('txtSmtpUser').disabled = true;
   		} else {
			$('txtSmtpPass').disabled = false;
			$('txtSmtpUser').disabled = false;
   		}
	}

	function $(id) {
		return document.getElementById(id);
	}
//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
    <div class="formpage2col">
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Admin_EMX_MailConfiguration;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

            <form name="frmEmailConfig" id="frmEmailConfig" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">
               <input type="hidden" name="token" value="<?php echo $token;?>" />
                <input type="hidden" name="sqlState" id="sqlState" value="UpdateRecord"/>
                <label for="txtMailAddress"><?php echo $lang_MailFrom; ?><span class="required">*</span></label>
                <input type="text" name="txtMailAddress" id="txtMailAddress" class="formInputText"
                    value="<?php echo $editArr->getMailAddress();?>" disabled="disabled" />

                <label for="txtMailType"><?php echo $lang_MailSendingMethod; ?></label>
                <select name="txtMailType" id="txtMailType" onchange="changeMailType();" onclick="changeMailType();"
                         class="formSelect" disabled="disabled">
                    <option value="0">-- <?php echo $lang_Common_Select;?> --</option>
                    <option value="<?php echo EmailConfiguration::EMAILCONFIGURATION_TYPE_SENDMAIL; ?>"
                        <?php echo ($editArr->getMailType() == EmailConfiguration::EMAILCONFIGURATION_TYPE_SENDMAIL )? 'selected="selected"': ''?>
                        ><?php echo $lang_MailTypes_Sendmailer; ?></option>
                    <option value="<?php echo EmailConfiguration::EMAILCONFIGURATION_TYPE_SMTP; ?>"
                        <?php echo ($editArr->getMailType() == EmailConfiguration::EMAILCONFIGURATION_TYPE_SMTP)? 'selected="selected"': ''?>
                        ><?php echo $lang_MailTypes_Smtp; ?></option>
                </select>
                <br class="clear"/>

                <!-- Sendmail -->
                <div id="sendmailDetails">
                    <label for="txtSendmailPath"><?php echo $lang_SendmailPath; ?></label>
                    <input type="text" name="txtSendmailPath" id="txtSendmailPath" class="formInputText"
                        value="<?php echo $editArr->getSendmailPath();?>" disabled="disabled" />
                    <br class="clear"/>
                </div>

                <!-- SMTP -->
                <div id="smtpDetails1">
                    <label for="txtSmtpHost"><?php echo $lang_SmtpHost; ?><span class="required">*</span></label>
                    <input type="text" name="txtSmtpHost" id="txtSmtpHost"  class="formInputText"
                        value="<?php echo $editArr->getSmtpHost();?>" disabled="disabled" />
                    <label for="txtSmtpPort"><?php echo $lang_SmtpPort; ?><span class="required">*</span></label>
                    <input type="text" name="txtSmtpPort" id="txtSmtpPort" class="formInputText"
                        value="<?php echo $editArr->getSmtpPort();?>" size="4" disabled="disabled" />
                    <br class="clear"/>
                </div>


                <div id="smtpDetails2">
                    <label for="optAuth"><?php echo $lang_EmailAuthentication; ?></label>
                    <input type="radio" checked="checked" onchange="changeAuth();" name="optAuth" id="optAuthNONE" value="NONE"
                    <?php echo ($editArr->getSmtpAuth() == "NONE") ? "checked" : ""?> disabled="disabled" />
                    <?php echo $lang_Common_No; ?>
                    <input type="radio" onchange="changeAuth();" name="optAuth" id="optAuthLOGIN" value="LOGIN"
                    <?php echo ($editArr->getSmtpAuth() == "LOGIN") ? "checked" : ""?> disabled="disabled" />
                    <?php echo $lang_Common_Yes; ?>
                    <br class="clear"/>
                </div>

                <div id="smtpDetails3">
                    <label for="txtSmtpUser"><?php echo $lang_SmtpUser; ?></label>
                    <input type="text" name="txtSmtpUser" id="txtSmtpUser" class="formInputText"
                        value="<?php echo $editArr->getSmtpUser();?>" disabled="disabled" />

                    <label for="txtSmtpPass"><?php echo $lang_SmtpPassword; ?></label>
                    <input type="password" name="txtSmtpPass" id="txtSmtpPass" class="formInputText"
                        value="<?php echo $editArr->getSmtpPass();?>" disabled="disabled" />
                    <br class="clear"/>
                </div>

                <div id="smtpDetails4">
                    <label for="optSecurity"><?php echo $lang_EmailSecurity; ?></label>
                    <input type="radio" checked="checked" name="optSecurity" id="optSecurityNONE" value="NONE"
                    <?php echo ($editArr->getSmtpSecurity() == "NONE") ? "checked" : ""; ?> disabled="disabled" />
                    <?php echo $lang_Common_No; ?>
                    <input type="radio" name="optSecurity" id="optSecuritySSL" value="SSL"
                    <?php echo ($editArr->getSmtpSecurity() == "SSL") ? "checked" : ""; ?> disabled="disabled" />
                    <?php echo $lang_Email_SSL; ?>
                    <input type="radio" name="optSecurity" id="optSecurityTLS" value="TLS"
                    <?php echo ($editArr->getSmtpSecurity() == "TLS") ? "checked" : ""; ?> disabled="disabled" />
                    <?php echo $lang_Email_TLS; ?>
                    <br class="clear"/>
                </div>

                <div id="smtpDetails5">
                    <label for="chkTestEmail"><?php echo $lang_SmtpSendTestEmail; ?></label>
                    <input type="checkbox" name="chkTestEmail" id="chkTestEmail" class="formInputText" disabled="disabled" />
                    <label for="txtTestEmail"><?php echo $lang_SmptTestEmailAddress; ?></label>
                    <input type="text" name="txtTestEmail" id="txtTestEmail" class="formInputText" disabled="disabled" />
                    <br class="clear"/>
                </div>

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="editbutton" id="editBtn"
                        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Edit;?>" />
                    <input type="button" class="clearbutton" onclick="resetForm();" tabindex="3"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Reset;?>" />
<?php } ?>
                </div>
            </form>
        </div>

        <script type="text/javascript">
        //<![CDATA[
            changeMailType();
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }
        //]]>
        </script>
        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
