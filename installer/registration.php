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
//$cupath = realpath(dirname(__FILE__) . '/../');
//define('ROOT_PATH', $cupath);
//require(ROOT_PATH . '/installer/registrationMessage.php');

?>
<script language="JavaScript">
    function login() {
        document.frmInstall.actionResponse.value = 'LOGIN';
        document.frmInstall.submit();
    }

    function noREG() {
        document.frmInstall.actionResponse.value = 'NOREG';
        document.frmInstall.submit();
    }


    function regInfo() {

        frm = document.frmInstall;
//	var messages = '';
//	if(frm.userName.value == '') {
//		messages += "\n" + ' - Enter last name ';
//	}
//	if(frm.company.value == '') {
//		messages += "\n" + ' - Enter company name';  
//    }
//
//	var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
//
//	if(frm.userEmail.value == '') {
//		messages += "\n" + ' - Enter e-mail address';
//	} else if (!reg.test(frm.userEmail.value)) {
//		messages += "\n" + ' - Invalid e-mail address';
//	}
//
//	if (messages != '') {
//        alert('Please correct the following error(s)' + messages);
//        return;
//    }
//
        document.frmInstall.actionResponse.value = 'REGINFO';
        registerAJAX();
        
        document.frmInstall.btnRegister.disabled = true;
    }


    function registerAJAX() {
        var xmlhttp;

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                
            } else if (xmlhttp.readyState == 1) {
                document.frmInstall.submit();
            }
        }

        xmlhttp.open("GET", "registrationMessage.php", true);
        xmlhttp.send();
        
    }

</script>
<link href="style.css" rel="stylesheet" type="text/css" />

<style>
    ul.registration li { 
        color:#dc8701; 
        height: 11px;
    }
    ul.registration li span { 
        color:black;           
    }

    .registration {           

    }
    .wrapper {
        display: block;
    }

    .wrapper_content_div {
        float: left;
        margin: 5px 30px 0px 0px; 
    }
    .clear {
        clear:both;
    }

</style>

<div style="display: block;" class="wrapper">
    <h2 style="margin: 5px;">Step 7: Registration</h2>
    <p style="margin:5px;">You have successfully installed OrangeHRM.</p>
    <div class="wrapper" style="width: 900px;">
        <div class="wrapper_content_div" style="font-family: arial,helvetica,sans-serif;font-size: 14px;">
            <h3 style="float: left;">Contribute to OrangeHRM by sending us your usage data</h3> <br>
             <div class="registrationWrapper" style="width: 750px;height: 200px; display: block;overflow: scroll;border: #000 solid thin;padding: 5px;">
                <p  ><span style="font-family: arial,helvetica,sans-seif;"><span>By enabling the <span>heartbeat</span> you allow</span>&nbsp; OrangeHRM to </span><span style="font-family: arial,helvetica,sans-serif;"><span style="font-family: arial,helvetica,sans-serif;">collect statistics about </span>usage in order to improve the user experience and performance. This function runs in the background and periodically sends data to the OrangeHRM Portal. THE DATA ARE JUST NUMBER TOTALS AND THEY DO NOT INCLUDE ANY PERSONAL INFORMATION.  Heartbeat tracks the number of users around the world and logs the time it takes to run database queries.</span><br /><br /></p>
                <div>Also by enabling Heartbeat you will receive the following:<br /></div>
                <ul>
                    <li>Bug fixes and other patches</li>
                    <li>Upgrades</li>
                    <li>Security updates</li>
                    <li>Other useful information about OrangeHRM</li>
                </ul>
                
                <p><br />Please contact us with any questions or comments at <a href="mailto:legal@orangehrm.com" target="_blank">legal@orangehrm.com</a>.</p>

            </div> 
            <!--    <div class="wrapper" style="width: 900px;">-->
            <?php if (!isset($reqAccept) || !$reqAccept) { ?>
                <ul style=" list-style-type: none; padding-left: 0px;">
                    <li>
                        <label for="companyNameInput" style="width:200px">Company Name (Optional): </label>
                        <input type="text" name="registerCompanyName" id="companyNameInput" value=""/>
                    </li>
                    <li>
                         <input type='hidden' value='off' name="hearbeatSelect" id = "hearbeatSelectHidden">
                        <input type = "checkbox" name="hearbeatSelect" id = "hearbeatSelect" checked="checked"/>
                        <label for = "" style="width: 300px;">I would like to send usage data to OrangeHRM</label>
                    </li>
                </ul>
            <?php } ?>
        </div>


        <?php
//        if (isset($reqAccept)) {
//
//            if ($reqAccept) {
//                ?>
                <!--<p>Registration information was collected, and Succesfully sent to OrangeHRM.com</p>-->
            <?php//} else { ?>
                <!--<p class="error">Registration information was collected, but NOT sent to OrangeHRM.com, please click Retry to try again, or click Skip to proceed and login into OrangeHRM</p>-->
                <?php
//            }
//        }

//        if (!isset($reqAccept) || (!$reqAccept)) {
//            
        ?>
        <!--            <div class="wrapper_content_div">
                        <h3>Detail</h3>
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="tdComponent_n">First name</td>
                                <td class="tdValues_n"><input type="text" name="firstName" tabindex="1" value="//<?php echo isset($_POST['firstName']) ? $_POST['firstName'] : '' ?>"/></td>
                            </tr>
                            <tr>
                                <td class="tdComponent_n">Last name <span class="required">*</span></td>
                                <td class="tdValues_n"><input type="text" name="userName" tabindex="2" value="//<?php echo isset($_POST['userName']) ? $_POST['userName'] : '' ?>"/></td>
                            </tr>
                            <tr>
                                <td class="tdComponent_n">Company<span class="required">*</span></td>
                                <td class="tdValues_n"><input type="text" name="company" tabindex="3" value="//<?php echo isset($_POST['company']) ? $_POST['company'] : '' ?>"/></td>
                            </tr>
                            <tr>
                            <tr>
                            <tr>
                                <td class="tdComponent_n">Email<span class="required">*</span></td>
                                <td class="tdValues_n"><input type="text" name="userEmail" tabindex="4" value="//<?php echo isset($_POST['userEmail']) ? $_POST['userEmail'] : '' ?>"/></td>
                            </tr>
                            <tr>
                                <td class="tdComponent_n">Telephone</td>
                                <td class="tdValues_n"><input type="text" name="userTp" tabindex="5" value="//<?php echo isset($_POST['userTp']) ? $_POST['userTp'] : '' ?>"/></td>
                            </tr>
                            <tr>
                                <td class="tdComponent_n">Comments</td>
                                <td class="tdValues_n"><textarea name="userComments" tabindex="6">//<?php echo isset($_POST['userComments']) ? $_POST['userComments'] : '' ?></textarea></td>
                            </tr>
                            <tr>
                                <td class="tdComponent_n">Updates/Newsletter</td>
                                <td class="tdValues_n"><input type="checkbox" name="chkUpdates" value="1" tabindex="7" //<?php echo (isset($_POST['chkUpdates']) && ($_POST['chkUpdates'] == 1)) ? 'checked' : '' ?> /></td>
                            </tr>
                            <tr>
                                <td>
        
        
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 10px;">
                                    <span class="required"> * </span>Required Fields      
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br style="clear: both"/>-->
        <?php // } ?>
        <div style=" margin-top: 10px;float: left; clear: left;margin-bottom: 10px;">
            

                <div>
    <!--                    <input name="button" type="button" onclick="noREG();" value="Skip" tabindex="11"/>-->
                    <input name="btnRegister" type="button" onclick="regInfo();" value="Finish" tabindex="1"/>

                </div>
           
        </div>    
    </div>
    <br class="clear"/>
