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

error_reporting(E_ERROR | E_PARSE);

/* For logging PHP errors */
include_once('lib/confs/log_settings.php');
include_once('installer/OrangeHrmRegistration.php');
$ohrmRegistration = new OrangeHrmRegistration();

if (!defined('ROOT_PATH')) {
    $rootPath = realpath(dirname(__FILE__));
    define('ROOT_PATH', $rootPath);
}

require_once(ROOT_PATH . '/installer/utils/installUtil.php');

function back($currScreen) {

    for ($i = 0; $i < 2; $i++) {
        switch ($currScreen) {

            default :
            case 0 : unset($_SESSION['WELCOME']);
                break;
            case 1 : unset($_SESSION['LICENSE']);
                break;
            case 2 : unset($_SESSION['DBCONFIG']);
                break;
            case 3 : unset($_SESSION['SYSCHECK']);
                break;
            case 4 : unset($_SESSION['DEFUSER']);
                break;
            case 5 : unset($_SESSION['CONFDONE']);
                break;
            case 6 : $_SESSION['UNISTALL'] = true;
                unset($_SESSION['CONFDONE']);
                unset($_SESSION['INSTALLING']);
                break;
            case 7 : return false;
                break;
        }

        $currScreen--;
    }

    return true;
}

if (!isset($_SESSION['SID'])) {
    session_start(getSessionCookieParams());
}

clearstatcache();

if (is_file(ROOT_PATH . '/lib/confs/Conf.php') && !isset($_SESSION['INSTALLING'])) {
    header('Location: ./index.php');
    exit();
}

if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}

/* This $_SESSION['cMethod'] is used to determine wheter to use an existing database or a new one */

$_SESSION['cMethod'] = 'new';

if (isset($_POST['cMethod'])) {
    $_SESSION['cMethod'] = $_POST['cMethod'];
}

if (isset($_POST['actionResponse']))
    switch ($_POST['actionResponse']) {

        case 'WELCOMEOK' : $_SESSION['WELCOME'] = 'OK';
            break;
        case 'LICENSEOK' : $_SESSION['LICENSE'] = 'OK';
            break;
        case 'SYSCHECKOK' : $_SESSION['SYSCHECK'] = 'OK';
            break;

        case 'DBINFO' : $uname = "";
            $passw = "";
            if (isset($_POST['dbUserName'])) {
                $uname = trim($_POST['dbUserName']);
            }
            if (isset($_POST['dbPassword'])) {
                $passw = trim($_POST['dbPassword']);
            }
            $dbInfo = array('dbHostName' => trim($_POST['dbHostName']),
                'dbHostPort' => trim($_POST['dbHostPort']),
                'dbName' => trim($_POST['dbName']),
                'dbUserName' => $uname,
                'dbPassword' => $passw);

            if (!isset($_POST['chkSameUser'])) {
                $dbInfo['dbOHRMUserName'] = trim($_POST['dbOHRMUserName']);
                $dbInfo['dbOHRMPassword'] = trim($_POST['dbOHRMPassword']);
            }

            if ($_POST['dbCreateMethod'] == 'existing') {
                $dbInfo['dbUserName'] = trim($_POST['dbOHRMUserName']);
                $dbInfo['dbPassword'] = trim($_POST['dbOHRMPassword']);
            }

            $_SESSION['dbCreateMethod'] = $_POST['dbCreateMethod'];

            $_SESSION['dbInfo'] = $dbInfo;

            $requiredExtensionsEnabled = false;
            $conn = null;
            if (isMySqliEnabled() && isPdoEnabled()) {
                $requiredExtensionsEnabled = true;
                $conn = mysqli_connect($dbInfo['dbHostName'], $dbInfo['dbUserName'], $dbInfo['dbPassword'], "", $dbInfo['dbHostPort']);
            }

            if ($conn) {
                $conn->set_charset("utf8mb4");
                $mysqlHost = mysqli_get_server_info($conn);

                if ($_POST['dbCreateMethod'] == 'new' && mysqli_select_db($conn, $dbInfo['dbName'])) {
                    $error = 'DBEXISTS';
                } elseif ($_POST['dbCreateMethod'] == 'new' && !isset($_POST['chkSameUser'])) {

                    mysqli_select_db($conn, 'mysql');
                    $dbOHRMUserName = mysqli_real_escape_string($conn, $dbInfo['dbOHRMUserName']);
                    $query = "SELECT USER FROM user WHERE USER = '$dbOHRMUserName'";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        $error = 'DBUSEREXISTS';
                    } else {
                        $_SESSION['DBCONFIG'] = 'OK';
                    }
                } elseif ($_POST['dbCreateMethod'] == 'existing') {
                    if (mysqli_select_db($conn, $dbInfo['dbName'])) {
                        $result = mysqli_query($conn, "SHOW TABLES");

                        if (mysqli_num_rows($result) > 0) {
                            $error = 'DBNOTEMPTY';
                        } else {
                            $_SESSION['DBCONFIG'] = 'OK';
                        }
                    } else {
                        $error = 'WRONGDBINFO';
                    }
                } else {
                    $_SESSION['DBCONFIG'] = 'OK';
                }

                $errorMsg = mysqli_error($conn);
                $mysqlErrNo = mysqli_errno($conn);

            } elseif (!$requiredExtensionsEnabled) {
                if (!isMySqliEnabled() && !isPdoEnabled()) {
                    $error = 'EXTENSION_MYSQL_PDO';
                } elseif (!isMySqliEnabled()) {
                    $error = 'EXTENSION_MYSQL';
                } elseif (!isPdoEnabled()) {
                    $error = 'EXTENSION_PDO';
                }
            } else {
                $error = 'WRONGDBINFO';
                $errorMsg = mysqli_connect_error();
                $mysqlErrNo = mysqli_connect_errno();
            }

            /* For Data Encryption: Begins */

            $_SESSION['ENCRYPTION'] = "Inactive";
            if (isset($_POST['chkEncryption'])) {

                $keyResult = createKeyFile('key.ohrm');
                if ($keyResult) {
                    $_SESSION['ENCRYPTION'] = "Active";
                } else {
                    $_SESSION['ENCRYPTION'] = "Failed";
                }
            }

            /* For Data Encryption: Ends */

            $_SESSION['dbHostName'] = $dbInfo['dbHostName'];
            $_SESSION['dbHostPort'] = $dbInfo['dbHostPort'];
            $_SESSION['dbName'] = $dbInfo['dbName'];
            $_SESSION['dbUserName'] = $dbInfo['dbUserName'];
            $_SESSION['dbPassword'] = $dbInfo['dbPassword'];

            break;

        case 'DEFUSERINFO' :
            $_SESSION['defUser']['AdminUserName'] = trim($_POST['OHRMAdminUserName']);
            $_SESSION['defUser']['AdminPassword'] = trim($_POST['OHRMAdminPassword']);

            $_SESSION['defUser']['organizationName'] = trim($_POST['organizationName']);
            $_SESSION['defUser']['country'] = trim($_POST['country']);
            $_SESSION['defUser']['language'] = trim($_POST['language']);
            $_SESSION['defUser']['timezone'] = trim($_POST['timezone']);
            $_SESSION['defUser']['adminEmployeeFirstName'] = trim($_POST['adminEmployeeFirstName']);
            $_SESSION['defUser']['adminEmployeeLastName'] = trim($_POST['adminEmployeeLastName']);
            $_SESSION['defUser']['organizationEmailAddress'] = trim($_POST['organizationEmailAddress']);
            $_SESSION['defUser']['contactNumber'] = trim($_POST['contactNumber']);
            $_SESSION['defUser']['randomNumber'] = rand(1,100);
            $_SESSION['DEFUSER'] = 'OK';
            break;

        case 'CANCEL' : session_destroy();
            header("Location: ./install.php");
            exit(0);
            break;

        case 'BACK' : back($_POST['txtScreen']);
            break;

        case 'CONFIRMED' : {
            $_SESSION['INSTALLING'] = 0;
            $_SESSION['defUser']['type'] = 0;
            $ohrmRegistration->sendRegistrationData();
        }
            break;

        case 'REGISTER' : $_SESSION['CONFDONE'] = 'OK';
            break;


        case 'REGINFO' 	:	$reqAccept = sendRegistrationData($_POST);
							break;

	case 'NOREG' 	:	$reqAccept = sendRegistrationData($_POST);

        case 'LOGIN' :
            $userName = $_SESSION['defUser']['AdminUserName'];
            $password = $_SESSION['defUser']['AdminPassword'];
            session_destroy();

            header("Location: ./");
            session_start(getSessionCookieParams());
            $_SESSION['AdminUserName'] = $userName;
            $_SESSION['AdminPassword'] = $password;
            $_SESSION['Installation'] = "You have successfully installed OrangeHRM";
            exit(0);
            break;
    }

if (isset($error)) {
    $_SESSION['error'] = $error;
}

if (isset($mysqlErrNo)) {
    $_SESSION['mysqlErrNo'] = $mysqlErrNo;
}

if (isset($errorMsg)) {
    $_SESSION['errorMsg'] = $errorMsg;
}

if (isset($reqAccept)) {
    $_SESSION['reqAccept'] = $reqAccept;
}

if (isset($_SESSION['INSTALLING']) && !isset($_SESSION['UNISTALL'])) {
    include(ROOT_PATH . '/installer/applicationSetup.php');
}

if (isset($_SESSION['UNISTALL'])) {
    include(ROOT_PATH . '/installer/cleanUp.php');
}

header('Location: ./installer/installerUI.php');

