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

        var email = $('txtMailAddress');
        if (!checkEmail(email.value)) {
            error = true;
            errors.push('<?php echo $lang_Error_InvalidEmail; ?>');
        }

        var port = $('txtSmtpPort');
        if ( !numbers(port) || ((port <= 0) || (port > 65535))) {
            error = true;
            errors.push('<?php echo $lang_Error_Invalid_Port; ?>');
        }

        if (error) {
            errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
            for (i in errors) {
                errStr += " - "+errors[i]+"\n";
            }
            alert(errStr);
            return false;

        } else  {
            return true;
        }
    } 

    function reset() {
        $('frmEmailConfig').reset();
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

        var emailConfigControls = new Array('txtMailAddress', 'txtMailType', 'txtSendmailPath', 'txtSmtpHost', 'txtSmtpPort', 'txtSmtpUser', 'txtSmtpPass');

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
        panels = ['sendmailDetails', 'smtpDetails1', 'smtpDetails2'];

        for (i=0; i<panels.length; i++) {
            $(panels[i]).className = 'hide';
        }

        switch (value) {
            case '<?php echo EmailConfiguration::EMAILCONFIGURATION_TYPE_SENDMAIL; ?>' :$(panels[0]).className = 'show';
                                                                                     break;
            case '<?php echo EmailConfiguration::EMAILCONFIGURATION_TYPE_SMTP; ?>' : $(panels[1]).className = 'show';
                                                                                     $(panels[2]).className = 'show';
                                                                                     break;
        }
    }
//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
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
                <input type="hidden" name="sqlState" id="sqlState" value="UpdateRecord"/>
                <label for="txtMailAddress"><?php echo $lang_MailFrom; ?></label>                     
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
                    <label for="txtSmtpHost"><?php echo $lang_SmtpHost; ?></label>
                    <input type="text" name="txtSmtpHost" id="txtSmtpHost"  class="formInputText"
                        value="<?php echo $editArr->getSmtpHost();?>" disabled="disabled" />
                    <label for="txtSmtpPort"><?php echo $lang_SmtpPort; ?></label>
                    <input type="text" name="txtSmtpPort" id="txtSmtpPort" class="formInputText"
                        value="<?php echo $editArr->getSmtpPort();?>" size="4" disabled="disabled" />
                    <br class="clear"/>                        
                </div>
                
                <div id="smtpDetails2">
                    <label for="txtSmtpUser"><?php echo $lang_SmtpUser; ?></label>
                    <input type="text" name="txtSmtpUser" id="txtSmtpUser" class="formInputText"
                        value="<?php echo $editArr->getSmtpUser();?>" disabled="disabled" />
                        
                    <label for="txtSmtpPass"><?php echo $lang_SmtpPassword; ?></label>
                    <input type="password" name="txtSmtpPass" id="txtSmtpPass" class="formInputText"
                        value="<?php echo $editArr->getSmtpPass();?>" disabled="disabled" />
                    <br class="clear"/>
                </div>                  

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>                
                    <input type="button" class="editbutton" id="editBtn" 
                        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                        value="<?php echo $lang_Common_Edit;?>" />
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
