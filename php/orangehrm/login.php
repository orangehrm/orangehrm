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
/* For logging PHP errors */
include_once('lib/confs/log_settings.php');

define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';

require_once ROOT_PATH . '/lib/common/Language.php';
$lan = new Language();
require_once ROOT_PATH . '/language/default/lang_default_full.php';
require_once($lan->getLangPath("full.php"));

session_start();

// To test a different style, can use http://host/orangehrm/login.php?styleSheet=abc
$styleSheet = CommonFunctions::getTheme();
$_SESSION['styleSheet'] = $styleSheet;

$wpath = explode('/login.php', $_SERVER['REQUEST_URI']);
$_SESSION['WPATH'] = $wpath[0];

require_once ROOT_PATH . '/lib/models/eimadmin/Login.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

/* LDAP Module */

$ldapFile = ROOT_PATH . "/plugins/ldap/LdapLogin.php";
$_SESSION['ldap'] = "disabled";
$_SESSION['ldapStatus'] = "disabled";

if (file_exists($ldapFile)) {
    require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
    require_once ROOT_PATH . '/plugins/PlugInFactory.php';
    $_SESSION['ldap'] = "enabled";
    require_once $ldapFile;
    $ldap = PlugInFactory::factory("LDAP");
    if ($ldap->checkAuthorizeLoginUser("Admin") && $ldap->checkAuthorizeModule("Admin")) {
        $ldapStatus = $ldap->retrieveLdapStatus();
        $_SESSION['ldapStatus'] = $ldapStatus;
    } else {
        throw new PlugInFactoryException(PlugInFactoryException::PLUGIN_INSTALL_ERROR);
    }
}

/* LDAP Module */

/* Print Benefits Module */

$benefitsFile = ROOT_PATH . "/plugins/printBenefits/pdfHspSummary.php";
$_SESSION['printBenefits'] = "disabled";

if (file_exists($benefitsFile)) {
    $_SESSION['printBenefits'] = "enabled";
}

/* Print Benefits Module */

/* Saving user time zone offset in session: Begins */

if (!empty($_POST['hdnUserTimeZoneOffset'])) {
    $_SESSION['userTimeZoneOffset'] = $_POST['hdnUserTimeZoneOffset'];
} else {
    $_SESSION['userTimeZoneOffset'] = 0;
}

/* Saving user time zone offset in session: Ends */

if ((isset($_POST['actionID'])) && $_POST['actionID'] == 'chkAuthentication') {

    $login = new Login();

    $rset = $login->filterUser(trim($_POST['txtUserName']));

    if (md5("") == $rset[0][1] && $_SESSION['ldapStatus'] == "enabled") {
        $ldapAuth = $ldap->ldapAuth($rset[0][0], $_POST['txtPassword']);
        if ($ldapAuth) { // stuff in normal login process
            $_SESSION['ladpUser'] = true;

            if ($rset[0][5] == 'Enabled') {
                if (($rset[0][7] == "Yes") || (($rset[0][7] == "No") && !empty($rset[0][6]))) {
                    $_SESSION['user'] = $rset[0][3];
                    $_SESSION['userGroup'] = $rset[0][4];
                    $_SESSION['isAdmin'] = $rset[0][7];
                    $_SESSION['empID'] = $rset[0][6]; // This is employee ID with leading zeros.
                    $_SESSION['empNumber'] = $rset[0][9]; // This is the real employee ID (emp_number) with no padding.

                    $_SESSION['fname'] = $rset[0][2];

                    /* If not an admin user, check if a supervisor and/or project admin */
                    $isSupervisor = false;
                    $isProjectAdmin = false;
                    $isHiringManager = false;
                    $isInterviewer = false;

                    if ($_SESSION['isAdmin'] == 'No') {

                        $authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
                        $isSupervisor = $authorizeObj->isSupervisor();
                        $isProjectAdmin = $authorizeObj->isProjectAdmin();
                        $isHiringManager = $authorizeObj->isHiringManager();
                        $isInterviewer = $authorizeObj->isInterviewer();
                    }

                    $_SESSION['isSupervisor'] = $isSupervisor;
                    $_SESSION['isProjectAdmin'] = $isProjectAdmin;
                    $_SESSION['isHiringManager'] = $isHiringManager;
                    $_SESSION['isInterviewer'] = $isInterviewer;

                    $wpath = explode('/login.php', $_SERVER['REQUEST_URI']);
                    $_SESSION['WPATH'] = $wpath[0];

                    // TODO: Can set user specific stylesheet here.
                    $_SESSION['styleSheet'] = $styleSheet;

                    setcookie('Loggedin', 'True', 0, '/');

                    header("Location: ./index.php");
                } else {
                    $InvalidLogin = 3;
                }
            } else {
                $InvalidLogin = 2;
            }
        } else {
            $InvalidLogin = 1;
        }
    } else if (md5($_POST['txtPassword']) == $rset[0][1]) {
        if ($rset[0][8] == EmploymentStatus::EMPLOYMENT_STATUS_ID_TERMINATED) {
            $InvalidLogin = 5;
        } else if ($rset[0][5] == 'Enabled') {
            if (($rset[0][7] == "Yes") || (($rset[0][7] == "No") && !empty($rset[0][6]))) {
                $_SESSION['user'] = $rset[0][3];
                $_SESSION['userGroup'] = $rset[0][4];
                $_SESSION['isAdmin'] = $rset[0][7];
                $_SESSION['empID'] = $rset[0][6]; // This is employee ID with leading zeros.
                $_SESSION['empNumber'] = $rset[0][9]; // This is the real employee ID (emp_number) with no padding.

                $_SESSION['fname'] = $rset[0][2];

                /* If not an admin user, check if a supervisor and/or project admin */
                $isSupervisor = false;
                $isProjectAdmin = false;
                $isHiringManager = false;
                $isInterviewer = false;


                if ($_SESSION['isAdmin'] == 'No') {

                    $authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
                    $isSupervisor = $authorizeObj->isSupervisor();
                    $isProjectAdmin = $authorizeObj->isProjectAdmin();
                    $isHiringManager = $authorizeObj->isHiringManager();
                    $isInterviewer = $authorizeObj->isInterviewer();
                }
                $_SESSION['isSupervisor'] = $isSupervisor;
                $_SESSION['isProjectAdmin'] = $isProjectAdmin;
                $_SESSION['isHiringManager'] = $isHiringManager;
                $_SESSION['isInterviewer'] = $isInterviewer;

                $wpath = explode('/login.php', $_SERVER['REQUEST_URI']);
                $_SESSION['WPATH'] = $wpath[0];

                // TODO: Can set user specific stylesheet here.
                $_SESSION['styleSheet'] = $styleSheet;

                setcookie('Loggedin', 'True', 0, '/');

                header("Location: ./index.php");
            } else {
                $InvalidLogin = 3;
            }
        } else
            $InvalidLogin=2;
    } else {
        $InvalidLogin = 1;
    }
}
?>
<html>
    <head>
        <title><?php echo $lang_login_title; ?></title>
        <link href="favicon.ico" rel="icon" type="image/gif"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="scripts/jquery/jquery.js"></script>
        <script type="text/javascript">

            function addHint(inputObject, hintImageURL) {
                if (inputObject.val() == '') {
                    inputObject.css('background', "url('" + hintImageURL + "') no-repeat 4px 2px");
                }
            }
            
            function removeHint(inputObject) {
                inputObject.css('background', '');
            }
            $(document).ready(function() {
                addHint($('#txtUserName'), 'themes/orange/images/login/username-hint.png');

                addHint($('#txtPassword'), 'themes/orange/images/login/password-hint.png');
                $('#txtUserName').click(function() {
                    removeHint($(this));
                    removeHint($("#txtPassword"));
                });
                $('#txtUserName').keyup(function() {
                    removeHint($(this));
                    removeHint($("#txtPassword"));
                });
            });
        
            function submitForm() {

                if(document.loginForm.txtUserName.value == "") {
                    alert('<?php echo $lang_login_UserNameNotGiven; ?>');
                    return false;
                }

                if(document.loginForm.txtPassword.value == "") {
                    alert("<?php echo $lang_login_PasswordNotGiven; ?>");
                    return false;
                }

                document.loginForm.actionID.value = "chkAuthentication";
                document.loginForm.hdnUserTimeZoneOffset.value = calculateUserTimeZoneOffset();
                document.loginForm.submit();
            }

            if (window.parent != window) {
                window.parent.location.reload();
            }

            function calculateUserTimeZoneOffset() {

                var myDate = new Date();
                var offset = (-1)*myDate.getTimezoneOffset()/60;

                return offset;

            }

        </script>
        <link href="themes/<?php echo $styleSheet; ?>/css/style.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            <!--
            body {
                background-color: #FFFFFF;
            }
            .bodyTXT {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #666666;
            }
            .style2 {color: #339900}
            .loginTXT {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #666666;
                height: 19px;
                vertical-align: middle;
                padding-top:0;
            }

            div#login {
                background-image: url(themes/<?php echo $styleSheet; ?>/images/login/login.png);
                height: 700px;
                width: 1000px;
                border-style: hidden;
                margin: auto;
                padding-left: 10px;
            }

            div#username {
                padding-top: 154px;
                padding-left: 510px;
            }

            div#password {
                padding-top: 38px;
                padding-left: 510px;
            }

            input#txtUserName {
                width: 240px;
                border: 0px;
                background-color:transparent;
            }

            input#txtPassword {
                width: 240px;
                border: 0px;
                background-color:transparent;
            }

            div#loginButton {
                padding-top: 36px;
                padding-left: 506px;
                float: left;
                width: 130px;
            }

            .button {
                background: url(themes/<?php echo $styleSheet; ?>/images/login/Login_button.png) no-repeat;
                cursor:pointer;
                width: 94px;
                height: 23px;
                border: none;
            }
            font#validationMsg {
                color: #DD7700;

            }

            div#validtaeMsg {
                padding-left: 660px; 
                padding-top: 37px;
            }

            input:not([type="image"]) {
                background-color: transparent;
                border: none;
            }

            input:focus:not([readonly]):not([type="image"]), select:focus, textarea:focus {
                background-color: transparent;
            }

            div#link {
                padding-left: 230px;
                padding-top: 105px;
                float: left;

            }
            div#logo {
                padding-left: 230px;
                padding-top: 70px;
            }
            img {
                border: none;
            }

        </style></head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
        <noscript>
        <strong><font color='Red' style="padding-left:15px; text-decoration:blink;">
            <?php echo $lang_login_NeedJavascript; ?>
            <a href="http://www.mozilla.com/firefox/" target="_blank" style="text-decoration:none;"><?php echo $lang_login_MozillaFirefox; ?></a>
            </font>
        </strong>
        </noscript>
        <?php if (isset($_COOKIE['Loggedin']) && isset($_SERVER['HTTP_REFERER'])) { ?>
            <strong><font color='Red' style="padding-left:15px;"><?php echo $lang_login_YourSessionExpired; ?></font>
            </strong>
        <?php } ?>

        <div id="login">

            <div id="logo">
                <img src="themes/<?php echo $styleSheet; ?>/images/login/logo.png">
            </div>
            <form name="loginForm" id="loginForm" class="loginForm" method="post" action="./login.php" onSubmit="submitForm(); return false;">
                <input type="hidden" name="actionID"/>
                <input type="hidden" name="hdnUserTimeZoneOffset" id="hdnUserTimeZoneOffset" value="" />
                <div id="username" class="bodyTXT">
                    <?php if (isset($_POST['txtUserName'])) { ?>
                        <input id="txtUserName" name="txtUserName" type="text" class="loginText" value="<?php echo CommonFunctions::escapeHtml($_POST['txtUserName']); ?>" tabindex="1" />
                    <?php } else { ?>
                        <input id="txtUserName" name="txtUserName" type="text" class="loginText" tabindex="1" />
                    <?php } ?>
                </div>

                <div id="password" class="bodyTXT"><input type="password" id="txtPassword" name="txtPassword" class="loginText"></div>
                <div id="loginButton">
                    <input type="Submit" name="Submit" class="button" id="loginBtn" value=""/></div>                    
                <?php
                if (isset($InvalidLogin)) {
                    switch ($InvalidLogin) {

                        case 1 : $InvalidLoginMes = $lang_login_InvalidLogin;
                            break;
                        case 2 : $InvalidLoginMes = $lang_login_UserDisabled;
                            break;
                        case 3 : $InvalidLoginMes = $lang_login_NoEmployeeAssigned;
                            break;
                        case 4 : $InvalidLoginMes = $lang_login_temporarily_unavailable;
                            break;
                        case 5 : $InvalidLoginMes = $lang_login_EmployeeTerminated;
                            break;
                    }
                } else {
                    $InvalidLoginMes = "&nbsp;";
                }

                $longMessage = "";

                if (strlen($InvalidLoginMes) > 14) {
                    $longMessage = $InvalidLoginMes;
                    $InvalidLoginMes = "<a title='{$longMessage}' >" . substr($InvalidLoginMes, 0, 11) . "...</a>";
                }
                ?>
                <div id="validtaeMsg">
                    <?php if ($InvalidLoginMes != "&nbsp;") : ?>
                        <img id="validationMark" src="themes/<?php echo $styleSheet; ?>/images/login/mark.png">
                    <?php endif; ?>
                    <strong ><font id="validationMsg"><?php echo $InvalidLoginMes; ?></font></strong>
                </div>
            </form>
            <?php $browser = $_SERVER['HTTP_USER_AGENT']; ?>
            <?php if (strstr($browser, "MSIE 8.0")): ?>
                <?php $footer = 'width: 700px' ?>
                <?php $socialNetwork = 'padding-top: 100px' ?>
            <?php else: ?>
                <?php $footer = 'width: 475px' ?>
                <?php $socialNetwork = 'padding-top: 110px' ?>

            <?php endif; ?>
            <div id="footer" >
                <div id="link" style="<?php echo $footer ?>"><lable><a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a> ver 2.6.9 &copy; OrangeHRM Inc. 2005 - 2011 All rights reserved.</lable></div>
                <div id="socialNetwork" style="<?php echo $socialNetwork ?>">
                    <a href="http://www.linkedin.com/groups?home=&gid=891077" target="_blank"><img src="themes/<?php echo $styleSheet; ?>/images/login/linkedin.png"></a>&nbsp;
                    <a href="http://www.facebook.com/OrangeHRM" target="_blank"><img src="themes/<?php echo $styleSheet; ?>/images/login/facebook.png"></a>&nbsp;
                    <a href="http://twitter.com/orangehrm" target="_blank"><img src="themes/<?php echo $styleSheet; ?>/images/login/twiter.png"></a>&nbsp;
                    <a href="http://www.youtube.com/results?search_query=orangehrm&search_type=" target="_blank"><img src="themes/<?php echo $styleSheet; ?>/images/login/youtube.png"></a>&nbsp;
                </div>
            </div>
        </div>

    </body>
</html>