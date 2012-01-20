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
 */
/* For logging PHP errors */
include_once('lib/confs/log_settings.php');

session_start();

/**
 * This if case checks whether the user is logged in. If so it will decorate User object with the user's user role.
 * This decorated user object is only used to determine menu accessibility. This decorated user object should not be
 * used for any other purposess. This if case will be dicarded when the whole system is converted to symfony.
 */
if (file_exists('symfony/config/databases.yml')) {
    if (isset($_SESSION['user'])) {

        define('SF_APP_NAME', 'orangehrm');
        define('SF_ENV', 'prod');
        define('SF_CONN', 'doctrine');


        require_once(dirname(__FILE__) . '/symfony/config/ProjectConfiguration.class.php');
        $configuration = ProjectConfiguration::getApplicationConfiguration(SF_APP_NAME, 'prod', true);
        new sfDatabaseManager($configuration);
        $context = sfContext::createInstance($configuration);

        if ($_SESSION['isAdmin'] == "Yes") {
            $userRoleArray['isAdmin'] = true;
        } else {
            $userRoleArray['isAdmin'] = false;
        }

        $userRoleArray['isSupervisor'] = $_SESSION['isSupervisor'];
        $userRoleArray['isProjectAdmin'] = $_SESSION['isProjectAdmin'];
        $userRoleArray['isHiringManager'] = $_SESSION['isHiringManager'];
        $userRoleArray['isInterviewer'] = $_SESSION['isInterviewer'];

        if ($_SESSION['empNumber'] == null) {
            $userRoleArray['isEssUser'] = false;
        } else {
            $userRoleArray['isEssUser'] = true;
        }

        $userObj = new User();

        $simpleUserRoleFactory = new SimpleUserRoleFactory();
        $decoratedUser = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);
        $decoratedUser->setEmployeeNumber($_SESSION['empNumber']);
        $decoratedUser->setUserId($_SESSION['user']);

        $accessibleTimeMenuItems = $decoratedUser->getAccessibleTimeMenus();
        $accessibleTimeSubMenuItems = $decoratedUser->getAccessibleTimeSubMenus();
        $accessibleRecruitmentMenuItems = $decoratedUser->getAccessibleRecruitmentMenus();
        $attendanceMenus = $decoratedUser->getAccessibleAttendanceSubMenus();
        $reportsMenus = $decoratedUser->getAccessibleReportSubMenus();
        $recruitHomePage = './symfony/web/index.php/recruitment/viewCandidates';
    }
}

ob_start();

define('ROOT_PATH', dirname(__FILE__));

if (!is_file(ROOT_PATH . '/lib/confs/Conf.php')) {
    header('Location: ./install.php');
    exit();
}

if (!isset($_SESSION['user'])) {

    header("Location: ./symfony/web/index.php/auth/login");
    exit();
}

if (isset($_GET['ACT']) && $_GET['ACT'] == 'logout') {
    session_destroy();
    setcookie('Loggedin', '', time() - 3600, '/');
    header("Location: ./symfony/web/index.php/auth/login");
    exit();
}

/* Loading disabled modules: Begins */

require_once ROOT_PATH . '/lib/common/ModuleManager.php';

$disabledModules = array();

if (isset($_SESSION['admin.disabledModules'])) {
    
    $disabledModules = $_SESSION['admin.disabledModules'];
    
} else {
    
    $moduleManager = new ModuleManager();    
    $disabledModules = $moduleManager->getDisabledModuleList();
    $_SESSION['admin.disabledModules'] = $disabledModules;    
    
}

/* Loading disabled modules: Ends */

define('Admin', 'MOD001');
define('PIM', 'MOD002');
define('MT', 'MOD003');
define('Report', 'MOD004');
define('Leave', 'MOD005');
define('TimeM', 'MOD006');
define('Benefits', 'MOD007');
define('Recruit', 'MOD008');
define('Perform', 'MOD009');

$arrRights = array('add' => false, 'edit' => false, 'delete' => false, 'view' => false);
$arrAllRights = array(Admin => $arrRights,
    PIM => $arrRights,
    MT => $arrRights,
    Report => $arrRights,
    Leave => $arrRights,
    TimeM => $arrRights,
    Benefits => $arrRights,
    Recruit => $arrRights,
    Perform => $arrRights);

require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/Config.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

$_SESSION['path'] = ROOT_PATH;
?>
<?php
/* Default modules */
if (!isset($_GET['menu_no_top'])) {
    if ($_SESSION['isAdmin'] == 'Yes') {
        $_GET['menu_no_top'] = "hr";
    } else if ($_SESSION['isSupervisor']) {
        $_GET['menu_no_top'] = "ess";
    } else {
        $_GET['menu_no_top'] = "ess";
    }
}

/** Clean Get variables that are used in URLs in page */
$varsToClean = array('uniqcode', 'isAdmin', 'pageNo', 'id', 'repcode', 'reqcode', 'menu_no_top');

foreach ($varsToClean as $var) {
    if (isset($_GET[$var])) {
        $_GET[$var] = CommonFunctions::cleanAlphaNumericIdField($_GET[$var]);
    }
}


/* For checking TimesheetPeriodStartDaySet status : Begins */

//This should be change using $timesheetPeriodService->isTimesheetPeriodDefined() method to support symfony version of the timesheet period 
if (Config::getTimePeriodSet()) {
    $_SESSION['timePeriodSet'] = 'Yes';
} else {
    $_SESSION['timePeriodSet'] = 'No';
}
/* For checking TimesheetPeriodStartDaySet status : Ends */

if ($_SESSION['isAdmin'] == 'Yes') {
    $rights = new Rights();

    foreach ($arrAllRights as $moduleCode => $currRights) {
        $arrAllRights[$moduleCode] = $rights->getRights($_SESSION['userGroup'], $moduleCode);
    }

    $ugroup = new UserGroups();
    $ugDet = $ugroup->filterUserGroups($_SESSION['userGroup']);

    $arrRights['repDef'] = $ugDet[0][2] == '1' ? true : false;
} else {

    /* Assign supervisors edit and view rights to the PIM
     * They have PIM rights over their subordinates, but they cannot add/delete
     * employees. But they have add/delete rights in the employee details page.
     */
    if ($_SESSION['isSupervisor']) {
        $arrAllRights[PIM] = array('add' => false, 'edit' => true, 'delete' => false, 'view' => true);
    }

    /*
     * Assign Manager's access to recruitment module
     */
    if ($_SESSION['isHiringManager'] || $_SESSION['isInterviewer']) {
        $arrAllRights[Recruit] = array('view' => true);
    }
}

switch ($_GET['menu_no_top']) {
    case "eim":
        $arrRights = $arrAllRights[Admin];
        break;
    case "hr" :
        $arrRights = $arrAllRights[PIM];
        break;
    case "mt" :
        $arrRights = $arrAllRights[MT];
        break;
    case "rep" :
        $arrRights = $arrAllRights[Report];
        break;
    case "leave" :
        $arrRights = $arrAllRights[Leave];
        break;
    case "time" :
        $arrRights = $arrAllRights[TimeM];
        break;
    case "recruit" :
        $arrRights = $arrAllRights[Recruit];
        break;
    case "perform" :
        $arrRights = $arrAllRights[Perform];
        break;
}
$_SESSION['localRights'] = $arrRights;

$styleSheet = CommonFunctions::getTheme();

$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

// Default leave home page
$leavePeriodDefined = Config::isLeavePeriodDefined();
if (!$leavePeriodDefined) {
    if ($authorizeObj->isAdmin()) {
        $leaveHomePage = './symfony/web/index.php/leave/defineLeavePeriod';
    } else {
        $leaveHomePage = './symfony/web/index.php/leave/showLeavePeriodNotDefinedWarning';
    }
} else {
    if ($authorizeObj->isAdmin()) {
        $leaveHomePage = './symfony/web/index.php/leave/viewLeaveList/reset/1';
    } else if ($authorizeObj->isSupervisor()) {
        if ($authorizeObj->isAdmin()) {
            $leaveHomePage = './symfony/web/index.php/leave/viewLeaveList/reset/1';
        } else {
            $leaveHomePage = './symfony/web/index.php/leave/viewLeaveList/reset/1';
        }
    } else if ($authorizeObj->isESS()) {
        $leaveHomePage = './symfony/web/index.php/leave/viewMyLeaveList/reset/1';
    }
}

// Time module default pages
if (!$authorizeObj->isAdmin() && $authorizeObj->isESS()) {
    if ($_SESSION['timePeriodSet'] == 'Yes') {
        $timeHomePage = './symfony/web/index.php/time/viewMyTimeTimesheet';
    } else {
        $timeHomePage = './symfony/web/index.php/time/defineTimesheetPeriod';
    }

} else {
    if ($_SESSION['timePeriodSet'] == 'Yes') {
        $timeHomePage = './symfony/web/index.php/time/viewEmployeeTimesheet';
    } else {
        $timeHomePage = './symfony/web/index.php/time/defineTimesheetPeriod';
    }

}

if (!$authorizeObj->isAdmin() && $authorizeObj->isESS()) {
    $beneftisHomePage = 'lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year';
    $empId = $_SESSION['empID'];
    $year = date('Y');
    $personalHspSummary = "lib/controllers/CentralController.php?benefitcode=Benefits&action=Search_Hsp_Summary&empId=$empId&year=$year";
} else {
    $beneftisHomePage = 'lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year';
    $personalHspSummary = 'lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Summary_Select_Year_Employee_Admin';
}

if ($authorizeObj->isESS()) {
    if ($_SESSION['timePeriodSet'] == 'Yes') {
        $timeHomePage = './symfony/web/index.php/attendance/punchIn';
    } else {
        $timeHomePage = './symfony/web/index.php/time/defineTimesheetPeriod';
    }
}

// Default page in admin module is the Company general info page.
$defaultAdminView = "GEN";
$allowAdminView = false;

if ($_SESSION['isAdmin'] == 'No') {
    if ($_SESSION['isProjectAdmin']) {

        // Default page for project admins is the Project Activity page
        $defaultAdminView = "PAC";

        // Allow project admins to view PAC (Project Activity) page only (in the admin module)
        // If uniqcode is not set, the default view is Project activity
        if ((!isset($_GET['uniqcode'])) || ($_GET['uniqcode'] == 'PAC')) {
            $allowAdminView = true;
        }
    }
}

require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/menu/MenuItem.php';

$lan = new Language();

require_once ROOT_PATH . '/language/default/lang_default_full.php';
require_once($lan->getLangPath("full.php"));

require_once ROOT_PATH . '/themes/' . $styleSheet . '/menu/Menu.php';
$menuObj = new Menu();

/* Create menu items */
/* TODO: Extract to separate class */
$menu = array();

/* View for Admin users */
if ($_SESSION['isAdmin'] == 'Yes' || $arrAllRights[Admin]['view']) {
    $menuItem = new MenuItem("admin", $lang_Menu_Admin, "./index.php?menu_no_top=eim");
    $menuItem->setCurrent($_GET['menu_no_top'] == "eim");

    $subs = array();

    $sub = new MenuItem("companyinfo", "Organization", "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("companyinfo", "General Information", "./symfony/web/index.php/admin/viewOrganizationGeneralInformation");
    $subsubs[] = new MenuItem("companyinfo", "Locations", "./symfony/web/index.php/admin/viewLocations");
    $subsubs[] = new MenuItem("companyinfo", "Structure", "./symfony/web/index.php/admin/viewCompanyStructure");

    $sub->setSubMenuItems($subsubs);


    $subs[] = $sub;

    $sub = new MenuItem("job", $lang_Menu_Admin_Job, "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("job", "Job Titles", "./symfony/web/index.php/admin/viewJobTitleList");
    $subsubs[] = new MenuItem("job", $lang_Menu_Admin_Job_PayGrades, "./symfony/web/index.php/admin/viewPayGrades");
    $subsubs[] = new MenuItem("job", $lang_Menu_Admin_Job_EmpStatus, "./symfony/web/index.php/admin/employmentStatus");
    $subsubs[] = new MenuItem("job", "Job Categories", "./symfony/web/index.php/admin/jobCategory");
    $subsubs[] = new MenuItem("job", $lang_Menu_Admin_Job_WorkShifts, "./symfony/web/index.php/admin/workShift");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("qualifications", $lang_Menu_Admin_Quali, "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("qualifications", $lang_Menu_Admin_Skills_Skills, "./symfony/web/index.php/admin/viewSkills");
    $subsubs[] = new MenuItem("qualifications", $lang_Menu_Admin_Quali_Education, "./symfony/web/index.php/admin/viewEducation");
    $subsubs[] = new MenuItem("qualifications", $lang_Menu_Admin_Quali_Licenses, "./symfony/web/index.php/admin/viewLicenses");
    $subsubs[] = new MenuItem("qualifications", $lang_Menu_Admin_Skills_Languages, "./symfony/web/index.php/admin/viewLanguages");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("memberships", $lang_Menu_Admin_Memberships, "./symfony/web/index.php/admin/membership", "rightMenu");
    $subs[] = $sub;

    $sub = new MenuItem("nationalities", "Nationalities", "./symfony/web/index.php/admin/nationality", "rightMenu");
    $subs[] = $sub;

    $sub = new MenuItem("users", $lang_Menu_Admin_Users, "./symfony/web/index.php/admin/viewSystemUsers", "rightMenu");
    $subsubs = array();

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmSecurityAuthenticationPlugin') && $arrAllRights[Admin]['edit']) {
        $subsubs[] = new MenuItem('users', 'Configure Security Authentication', './symfony/web/index.php/securityAuthentication/securityAuthenticationConfigure', 'rightMenu');
    }

    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("email", $lang_Menu_Admin_EmailNotifications, "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("email", $lang_Menu_Admin_EmailConfiguration, "./symfony/web/index.php/admin/listMailConfiguration");
    $subsubs[] = new MenuItem("email", $lang_Menu_Admin_EmailSubscribe, "./symfony/web/index.php/admin/viewEmailNotification");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("project", $lang_Menu_Admin_ProjectInfo, "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("project", __("Customers"), "./symfony/web/index.php/admin/viewCustomers");
    $subsubs[] = new MenuItem("project", __("Projects"), "./symfony/web/index.php/admin/viewProjects");

    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("configuration", "Configuration", "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("configuration", "Localization", "./symfony/web/index.php/admin/localization");
    $subsubs[] = new MenuItem("configuration", "Modules", "./symfony/web/index.php/admin/viewModules");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmAuditTrailPlugin') && $arrAllRights[Admin]['view']) {
        $subs[] = new MenuItem('audittrail', 'Audit Trail', './symfony/web/index.php/audittrail/viewAuditTrail', 'rightMenu');
    }

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmLDAPAuthenticationPlugin') && $arrAllRights[Admin]['edit']) {
        $subs[] = new MenuItem('ldap', 'LDAP Configuration', './symfony/web/index.php/ldapAuthentication/configureLDAPAuthentication', 'rightMenu');
    }

    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
} else if ($_SESSION['isProjectAdmin'] && !$_SESSION['isSupervisor']) {
    $menuItem = new MenuItem("admin", $lang_Menu_Admin, './symfony/web/index.php/admin/viewProjects', 'rightMenu');
    $menuItem->setCurrent($_GET['menu_no_top'] == "eim");
    $subs[] = new MenuItem("project", "Projects", "./symfony/web/index.php/admin/viewProjects", 'rightMenu');
    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
} else if ($_SESSION['isSupervisor'] && $_SESSION['isProjectAdmin']) {
    $menuItem = new MenuItem("admin", $lang_Menu_Admin, './symfony/web/index.php/admin/viewProjects', 'rightMenu');
    $menuItem->setCurrent($_GET['menu_no_top'] == "eim");
    $subs[] = new MenuItem("project", __("Projects"), "./symfony/web/index.php/admin/viewProjects", 'rightMenu');
    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}

define('PIM_MENU_TYPE', 'left');
$_SESSION['PIM_MENU_TYPE'] = PIM_MENU_TYPE;

/* PIM menu start */
if (($_SESSION['isAdmin'] == 'Yes' || $_SESSION['isSupervisor']) && $arrAllRights[PIM]['view']) {

    $menuItem = new MenuItem("pim", $lang_Menu_Pim, "./index.php?menu_no_top=hr&reset=1");
    $menuItem->setCurrent($_GET['menu_no_top'] == "hr");
    $enablePimMenu = false;
    if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top'] == "hr") && isset($_GET['reqcode']) && $arrRights['view']) {
        $enablePimMenu = true;
    }
    $subs = array();
    if ($_SESSION['isAdmin'] == 'Yes') {

        $sub = new MenuItem("configure", "Configuration", "#");
        $subsubs = array();
        $subsubs[] = new MenuItem("pimconfig", "Optional Fields", "./symfony/web/index.php/pim/configurePim", "rightMenu");
        $subsubs[] = new MenuItem("customfields", $lang_Menu_Admin_CustomFields, "./symfony/web/index.php/pim/listCustomFields");
        $subsubs[] = new MenuItem("customfields", "Data Import", "./symfony/web/index.php/admin/pimCsvImport");
        $subsubs[] = new MenuItem("customfields", "Reporting Methods", "./symfony/web/index.php/pim/viewReportingMethods");
        $subsubs[] = new MenuItem("customfields", "Termination Reasons", "./symfony/web/index.php/pim/viewTerminationReasons");
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    }

    $subs[] = new MenuItem("emplist", $lang_pim_EmployeeList, "./symfony/web/index.php/pim/viewEmployeeList/reset/1", "rightMenu");
    if ($arrAllRights[PIM]['add']) {
        $subs[] = new MenuItem("empadd", $lang_pim_AddEmployee, "./symfony/web/index.php/pim/addEmployee", "rightMenu");
    }

    if ($_SESSION['isAdmin'] == 'Yes') {
        $subs[] = new MenuItem("reports", $lang_Menu_Reports, "./symfony/web/index.php/core/viewDefinedPredefinedReports/reportGroup/3/reportType/PIM_DEFINED", "rightMenu");
    }

    if (PIM_MENU_TYPE == 'dropdown') {
        $sub = new MenuItem("personal", $lang_pim_tabs_Personal, "#", null, $enablePimMenu);
        $subsubs = array();
        $subsubs[] = new MenuItem("personal", $lang_pim_PersonalDetails, "javascript:parent.rightMenu.displayLayer(1)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_Contact, "javascript:parent.rightMenu.displayLayer(4)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_EmergencyContacts, "javascript:parent.rightMenu.displayLayer(5)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_Dependents, "javascript:parent.rightMenu.displayLayer(3)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_Immigration, "javascript:parent.rightMenu.displayLayer(10)", null, $enablePimMenu);
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;

        $sub = new MenuItem("employment", $lang_pim_Employment, "#", null, $enablePimMenu);
        $subsubs = array();

        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_Job, "javascript:parent.rightMenu.displayLayer(2)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_Payments, "javascript:parent.rightMenu.displayLayer(14)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_Tax, "javascript:parent.rightMenu.displayLayer(18)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_DirectDebit, "javascript:parent.rightMenu.displayLayer(19)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_ReportTo, "javascript:parent.rightMenu.displayLayer(15)", null, $enablePimMenu);
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;

        $sub = new MenuItem("pimqualifications", $lang_pim_Qualifications, "#", null, $enablePimMenu);
        $subsubs = array();
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_WorkExperience, "javascript:parent.rightMenu.displayLayer(17)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_Education, "javascript:parent.rightMenu.displayLayer(9)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_Skills, "javascript:parent.rightMenu.displayLayer(16)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_Languages, "javascript:parent.rightMenu.displayLayer(11)", null, $enablePimMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_License, "javascript:parent.rightMenu.displayLayer(12)", null, $enablePimMenu);
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;

        $subs[] = new MenuItem("pimmemberships", $lang_pim_tabs_Membership, "javascript:parent.rightMenu.displayLayer(13)", null, $enablePimMenu);
        $subs[] = new MenuItem("attachments", $lang_pim_tabs_Attachments, "javascript:parent.rightMenu.displayLayer(6)", null, $enablePimMenu);
        $subs[] = new MenuItem("custom", $lang_pim_tabs_Custom, "javascript:parent.rightMenu.displayLayer(20)", null, $enablePimMenu);
    } else if (PIM_MENU_TYPE == 'mixed') {
        $subs[] = new MenuItem("personal", $lang_pim_tabs_Personal, "javascript:parent.rightMenu.displayLayer(1)", null, $enablePimMenu);
        $subs[] = new MenuItem("employment", $lang_pim_Employment, "javascript:parent.rightMenu.displayLayer(2)", null, $enablePimMenu);
        $subs[] = new MenuItem("pimqualifications", $lang_pim_Qualifications, "javascript:parent.rightMenu.displayLayer(17)", null, $enablePimMenu);
        $subs[] = new MenuItem("pimmemberships", $lang_pim_tabs_Membership, "javascript:parent.rightMenu.displayLayer(13)", null, $enablePimMenu);
        $subs[] = new MenuItem("attachments", $lang_pim_tabs_Attachments, "javascript:parent.rightMenu.displayLayer(6)", null, $enablePimMenu);
        $subs[] = new MenuItem("custom", $lang_pim_tabs_Custom, "javascript:parent.rightMenu.displayLayer(20)", null, $enablePimMenu);
    }
    $menuItem->setSubMenuItems($subs);

    $menu[] = $menuItem;
}

/* Start leave menu */
if (($_SESSION['empID'] != null) || $arrAllRights[Leave]['view']) {
    $menuItem = new MenuItem("leave", $lang_Menu_Leave, "./index.php?menu_no_top=leave&reset=1");
    $menuItem->setCurrent($_GET['menu_no_top'] == "leave");

    $subs = array();
    $subsubs = array();

    if ($authorizeObj->isAdmin() && $arrAllRights[Leave]['view']) {

        $sub = new MenuItem("leavesummary", $lang_Common_Configure, "#");

        $subsubs[] = new MenuItem("leaveperiod", $lang_Menu_Leave_DefineLeavePeriod, './symfony/web/index.php/leave/defineLeavePeriod', 'rightMenu');
        $subsubs[] = new MenuItem("leavetypes", $lang_Menu_Leave_LeaveTypes, './symfony/web/index.php/leave/leaveTypeList');
        $subsubs[] = new MenuItem("daysoff", $lang_Menu_Leave_WorkWeek, "./symfony/web/index.php/leave/defineWorkWeek");
        $subsubs[] = new MenuItem("daysoff", $lang_Menu_Leave_Holidays, "./symfony/web/index.php/leave/viewHolidayList");

        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    }

    $subs[] = new MenuItem("leavesummary", $lang_Menu_Leave_LeaveSummary, "./symfony/web/index.php/leave/viewLeaveSummary", 'rightMenu');

    if ($authorizeObj->isSupervisor() && !$authorizeObj->isAdmin()) {
        $subs[] = new MenuItem("leavelist", $lang_Leave_all_emplyee_leaves, './symfony/web/index.php/leave/viewLeaveList/reset/1', 'rightMenu');
    }
    if ($authorizeObj->isAdmin() && $arrAllRights[Leave]['view']) {
        $subs[] = new MenuItem("leavelist", $lang_Leave_all_emplyee_leaves, './symfony/web/index.php/leave/viewLeaveList/reset/1', 'rightMenu');
    }

    if (($authorizeObj->isAdmin() && $arrAllRights[Leave]['add']) || $authorizeObj->isSupervisor()) {
        $subs[] = new MenuItem("assignleave", $lang_Menu_Leave_Assign, "./symfony/web/index.php/leave/assignLeave", 'rightMenu');
    }

    if ($authorizeObj->isESS()) {
        $subs[] = new MenuItem("leavelist", $lang_Menu_Leave_MyLeave, './symfony/web/index.php/leave/viewMyLeaveList/reset/1', 'rightMenu');
        $subs[] = new MenuItem("applyLeave", $lang_Menu_Leave_Apply, "./symfony/web/index.php/leave/applyLeave", 'rightMenu');
    }

    if (file_exists('symfony/plugins/orangehrmLeaveCalendarPlugin/config/orangehrmLeaveCalendarPluginConfiguration.class.php')) {//if plugin is installed
        $subs[] = new MenuItem("leavelist", $plugin_leave_Calendar, './symfony/web/index.php/leavecalendar/showLeaveCalendar', 'rightMenu');
    }
    /* Emptying the leave menu items if leave period is not defined */
    if (!$leavePeriodDefined) {
        $subs = array();
    }

    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}

/* Start time menu */
if (($_SESSION['empID'] != null) || $arrAllRights[TimeM]['view']) {
    $menuItem = new MenuItem("time", $lang_Menu_Time, "./index.php?menu_no_top=time");
    $menuItem->setCurrent($_GET['menu_no_top'] == "time");

    /* Only show rest of menu if time period set */
    if ($_SESSION['timePeriodSet'] == "Yes" && file_exists('symfony/config/databases.yml')) {
        $subs = array();

        // modified under restructure time menu story

        $subsubs = array();
        $subsubs0 = array();
        $subsubs1 = array();
        if ($accessibleTimeMenuItems != null) {
            foreach ($accessibleTimeMenuItems as $ttt) {

                $sub = new MenuItem("timesheets", $ttt->getDisplayName(), $ttt->getLink(), 'rightMenu');

                if ($ttt->getDisplayName() == "Timesheets") {

                    foreach ($accessibleTimeSubMenuItems as $ctm) {

                        $subsubs[] = new MenuItem("timesheets", $ctm->getDisplayName(), $ctm->getLink());
                    }

                    $sub->setSubMenuItems($subsubs);
                }
                if ($ttt->getDisplayName() == "Attendance") {

                    foreach ($attendanceMenus as $ptm) {
                        $subsubs0[] = new MenuItem("timesheets", $ptm->getDisplayName(), $ptm->getLink());
                    }

                    $sub->setSubMenuItems($subsubs0);
                }

                if ($ttt->getDisplayName() == "Reports") {

                    foreach ($reportsMenus as $ptm) {
                        $subsubs1[] = new MenuItem("timesheets", $ptm->getDisplayName(), $ptm->getLink());
                    }

                    $sub->setSubMenuItems($subsubs1);
                }

                $subs[] = $sub;
            }
        }

        $menuItem->setSubMenuItems($subs);
    }
    $menu[] = $menuItem;
}

/* Start recruitment menu */

if ($arrAllRights[Recruit]['view']) {


    $menuItem = new MenuItem("recruit", $lang_Menu_Recruit, "./index.php?menu_no_top=recruit");
    $menuItem->setCurrent($_GET['menu_no_top'] == "recruit");

    if (file_exists('symfony/config/databases.yml')) {
        $subs = array();
        foreach ($accessibleRecruitmentMenuItems as $tttt) {
            $subs[] = new MenuItem("recruit", $tttt->getDisplayName(), $tttt->getLink(), "rightMenu");
        }
    }
    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}

/* Performance menu start */

$menuItem = new MenuItem("perform", $lang_Menu_Perform, "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/viewReview/mode/new");
$menuItem->setCurrent($_GET['menu_no_top'] == "perform");
$enablePerformMenu = false;
if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top'] == "perform") && isset($_GET['reqcode']) && $arrRights['view']) {
    $enablePerformMenu = true;
}
$subs = array();

if ($arrAllRights[Perform]['add'] && ($_SESSION['isAdmin'] == 'Yes')) {
    $subs[] = new MenuItem('definekpi', $lang_Menu_Define_Kpi, "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/listDefineKpi");
    $subs[] = new MenuItem('definekpi', 'Add KPI', "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/saveKpi");
    $subs[] = new MenuItem('definekpi', 'Copy KPI', "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/copyKpi");
    $subs[] = new MenuItem('definekpi', 'Add Review', "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/saveReview");
}

$subs[] = new MenuItem('definekpi', 'Reviews', "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/viewReview/mode/new");

$menuItem->setSubMenuItems($subs);

$menu[] = $menuItem;

/* Start ESS menu */
if ($_SESSION['isAdmin'] != 'Yes') {
    $menuItem = new MenuItem("ess", $lang_Menu_Ess, './symfony/web/index.php/pim/viewPersonalDetails?empNumber=' . $_SESSION['empID'], "rightMenu");

    $menuItem->setCurrent($_GET['menu_no_top'] == "ess");
    $enableEssMenu = false;
    if ($_GET['menu_no_top'] == "ess") {
        $enableEssMenu = true;
    }
    $subs = array();
    if (PIM_MENU_TYPE == 'dropdown') {
        $sub = new MenuItem("personal", $lang_pim_tabs_Personal, "#", null, $enableEssMenu);
        $subsubs = array();
        $subsubs[] = new MenuItem("personal", $lang_pim_PersonalDetails, "javascript:parent.rightMenu.displayLayer(1)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_Contact, "javascript:parent.rightMenu.displayLayer(4)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_EmergencyContacts, "javascript:parent.rightMenu.displayLayer(5)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_Dependents, "javascript:parent.rightMenu.displayLayer(3)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("personal", $lang_pim_tabs_Immigration, "javascript:parent.rightMenu.displayLayer(10)", null, $enableEssMenu);
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;

        $sub = new MenuItem("employment", $lang_pim_Employment, "#", null, $enableEssMenu);
        $subsubs = array();

        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_Job, "javascript:parent.rightMenu.displayLayer(2)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_Payments, "javascript:parent.rightMenu.displayLayer(14)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_Tax, "javascript:parent.rightMenu.displayLayer(18)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_DirectDebit, "javascript:parent.rightMenu.displayLayer(19)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("employment", $lang_pim_tabs_ReportTo, "javascript:parent.rightMenu.displayLayer(15)", null, $enableEssMenu);
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;

        $sub = new MenuItem("pimqualifications", $lang_pim_Qualifications, "#", null, $enableEssMenu);
        $subsubs = array();
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_WorkExperience, "javascript:parent.rightMenu.displayLayer(17)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_Education, "javascript:parent.rightMenu.displayLayer(9)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_Skills, "javascript:parent.rightMenu.displayLayer(16)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_Languages, "javascript:parent.rightMenu.displayLayer(11)", null, $enableEssMenu);
        $subsubs[] = new MenuItem("pimqualifications", $lang_pim_tabs_License, "javascript:parent.rightMenu.displayLayer(12)", null, $enableEssMenu);
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;

        $subs[] = new MenuItem("pimmemberships", $lang_pim_tabs_Membership, "javascript:parent.rightMenu.displayLayer(13)", null, $enableEssMenu);
        $subs[] = new MenuItem("attachments", $lang_pim_tabs_Attachments, "javascript:parent.rightMenu.displayLayer(6)", null, $enableEssMenu);
        $subs[] = new MenuItem("custom", $lang_pim_tabs_Custom, "javascript:parent.rightMenu.displayLayer(20)", null, $enableEssMenu);
    } else if (PIM_MENU_TYPE == 'mixed') {
        $subs[] = new MenuItem("personal", $lang_pim_tabs_Personal, "javascript:parent.rightMenu.displayLayer(1)", null, $enablePimMenu);
        $subs[] = new MenuItem("employment", $lang_pim_Employment, "javascript:parent.rightMenu.displayLayer(2)", null, $enablePimMenu);
        $subs[] = new MenuItem("pimqualifications", $lang_pim_Qualifications, "javascript:parent.rightMenu.displayLayer(17)", null, $enablePimMenu);
        $subs[] = new MenuItem("pimmemberships", $lang_pim_tabs_Membership, "javascript:parent.rightMenu.displayLayer(13)", null, $enablePimMenu);
        $subs[] = new MenuItem("attachments", $lang_pim_tabs_Attachments, "javascript:parent.rightMenu.displayLayer(6)", null, $enablePimMenu);
        $subs[] = new MenuItem("custom", $lang_pim_tabs_Custom, "javascript:parent.rightMenu.displayLayer(20)", null, $enablePimMenu);
    }
    $menuItem->setSubMenuItems($subs);

    $menu[] = $menuItem;
}

/* Start benefits menu */
if (($_SESSION['empID'] != null) || $arrAllRights[Benefits]['view']) {
    $menuItem = new MenuItem("benefits", $lang_Menu_Benefits, "./index.php?menu_no_top=benefits");
    $menuItem->setCurrent($_GET['menu_no_top'] == "benefits");

    $subs = array();

    /* TODO: clean up this part based on requirements */
    if ($_SESSION['isAdmin'] == "Yes" && $arrAllRights[Benefits]['view']) {
        $yearVal = date('Y');
        $sub = new MenuItem("hsp", $lang_Menu_Benefits_HealthSavingsPlan, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Summary&year={$yearVal}");
        $subsubs = array();
        $subsubs[] = new MenuItem("hsp", $lang_Menu_Benefits_Define_Health_savings_plans, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Define_Health_Savings_Plans");
        $subsubs[] = new MenuItem("hsp", $lang_Menu_Benefits_EmployeeHspSummary, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Summary&year={$yearVal}");
        $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspPaymentsDue, "lib/controllers/CentralController.php?benefitcode=Benefits&action=List_Hsp_Due");
        $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspExpenditures, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Expenditures_Select_Year_And_Employee");
        $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspUsed, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Used_Select_Year&year={$yearVal}");
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    } else {

        if (Config::getHspCurrentPlan() > 0) {
            $sub = new MenuItem("hsp", $lang_Menu_Benefits_HealthSavingsPlan, $personalHspSummary);
        } else {
            $sub = new MenuItem("hsp", $lang_Menu_Benefits_HealthSavingsPlan, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Not_Defined");
        }
        $subsubs = array();

        if ($authorizeObj->isESS()) {
            $yearVal = date('Y');
            $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspExpenditures, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Expenditures&year={$yearVal}&employeeId={$_SESSION['empID']}");

            if (Config::getHspCurrentPlan() > 0) { // Show only when Admin has defined a HSP plan
                $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspRequest, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Request_Add_View");
                $subsubs[] = new MenuItem("hsp", $lang_Menu_Benefits_PersonalHspSummary, $personalHspSummary);
            }
        }
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    }

    if ($_SESSION['isAdmin'] == "Yes" && $arrAllRights[Benefits]['view']) {
        $sub = new MenuItem("payrollschedule", $lang_Menu_Benefits_PayrollSchedule, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year");

        $subsubs = array();
        $subsubs[] = new MenuItem("payrollschedule", $lang_Benefits_ViewPayrollSchedule, "lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year");
        if ($arrAllRights[Benefits]['add']) {
            $subsubs[] = new MenuItem("payrollschedule", $lang_Benefits_AddPayPeriod, "lib/controllers/CentralController.php?benefitcode=Benefits&action=View_Add_Pay_Period");
        }
        $sub->setSubMenuItems($subsubs);

        $subs[] = $sub;
    }

    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}

/* Start help menu */
$menuItem = new MenuItem("help", $lang_Menu_Help, '#');
$subs = array();
$subs[] = new MenuItem("support", $lang_Menu_Home_Support, "http://www.orangehrm.com/support-plans.php?utm_source=application_support&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
$subs[] = new MenuItem("forum", $lang_Menu_Home_Forum, "http://www.orangehrm.com/forum/", '_blank');
$subs[] = new MenuItem("blog", $lang_Menu_Home_Blog, "http://www.orangehrm.com/blog/", '_blank');
$subs[] = new MenuItem("support", $lang_Menu_Home_Training, "http://www.orangehrm.com/training.php?utm_source=application_traning&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
$subs[] = new MenuItem("support", $lang_Menu_Home_AddOns, "http://www.orangehrm.com/addon-plans.shtml?utm_source=application_addons&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
$subs[] = new MenuItem("support", $lang_Menu_Home_Customizations, "http://www.orangehrm.com/customizations.php?utm_source=application_cus&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
$subs[] = new MenuItem("bug", "Bug Tracker", "http://sourceforge.net/tracker/?group_id=156477&atid=799942", '_blank');

$menuItem->setSubMenuItems($subs);
$menu[] = $menuItem;
/* End of main menu definition */

/* Checking for disabled modules: Begins */

$count = count($menu);

for ($i=0; $i<$count; $i++) {

    if (in_array(strtolower($menu[$i]->getMenuText()), $disabledModules)) {
        unset($menu[$i]);
    }
    
}

/* Checking for disabled modules: Ends */

$welcomeMessage = preg_replace('/#username/', ((isset($_SESSION['fname'])) ? $_SESSION['fname'] : ''), $lang_index_WelcomeMes);

if (isset($_SESSION['ladpUser']) && $_SESSION['ladpUser'] && $_SESSION['isAdmin'] != "Yes") {
    $optionMenu = array();
} else {
    $optionMenu[] = new MenuItem("changepassword", $lang_index_ChangePassword,
                    "./symfony/web/index.php/admin/changeUserPassword");
}

$optionMenu[] = new MenuItem("logout", $lang_index_Logout, './symfony/web/index.php/auth/logout', '_parent');

// Decide on home page
if (($_GET['menu_no_top'] == "eim") && ($arrRights['view'] || $allowAdminView)) {
    $uniqcode = isset($_GET['uniqcode']) ? $_GET['uniqcode'] : $defaultAdminView;
    $isAdmin = isset($_GET['isAdmin']) ? ('&amp;isAdmin=' . $_GET['isAdmin']) : '';

    /* TODO: Remove this pageNo variable */
    $pageNo = isset($_GET['pageNo']) ? '&amp;pageNo=1' : '';
    if (isset($_GET['uri'])) {
        $uri = (substr($_GET['uri'], 0, 11) == 'performance') ? $_GET['uri'] : 'performance/viewReview/mode/new';
        $home = './symfony/web/index.php/' . $uri;
    } else {
        $home = "./symfony/web/index.php/admin/viewOrganizationGeneralInformation"; //TODO: Use this after fully converted to Symfony
    }
} elseif (($_GET['menu_no_top'] == "hr") && $arrRights['view']) {

    $home = "./symfony/web/index.php/pim/viewEmployeeList/reset/1";
    if (isset($_GET['uri'])) {
        $home = $_GET['uri'];
    } elseif (isset($_GET['id'])) {
        $home = "./symfony/web/index.php/pim/viewPersonalDetails?empNumber=" . $_GET['id'];
    }
} elseif ($_GET['menu_no_top'] == "ess") {
    $home = './symfony/web/index.php/pim/viewPersonalDetails?empNumber=' . $_SESSION['empID'];
} elseif ($_GET['menu_no_top'] == "leave") {
    $home = $leaveHomePage;
} elseif ($_GET['menu_no_top'] == "time") {
    $home = $timeHomePage;
} elseif ($_GET['menu_no_top'] == "benefits") {
    $home = $beneftisHomePage;
} elseif ($_GET['menu_no_top'] == "recruit") {
    $home = $recruitHomePage;
} elseif ($_GET['menu_no_top'] == "performance") {
    $uri = (substr($_GET['uri'], 0, 11) == 'performance') ? $_GET['uri'] : 'performance/viewReview/mode/new';
    $home = './symfony/web/index.php/' . $uri;
} else {
    $rightsCount = 0;
    foreach ($arrAllRights as $moduleRights) {
        foreach ($moduleRights as $right) {
            if ($right) {
                $rightsCount++;
            }
        }
    }

    if ($rightsCount === 0) {
        $home = 'message.php?case=no-rights&type=notice';
    } else {
        $home = "";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>OrangeHRM</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link href="themes/<?php echo $styleSheet; ?>/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="favicon.ico" rel="icon" type="image/gif"/>
        <script type="text/javaScript" src="scripts/archive.js"></script>
<?php
$menuObj->getCSS();
$menuObj->getJavascript($menu);
?>
    </head>

    <body>
        <div id="companyLogoHeader"></div>

	<div align='right' style="height:0px; width:100%;">
	<?php

	try
{
if($xml = @simplexml_load_file("http://www.orangehrm.com/global_update/orangehrm_updates.xml"))
	{
	foreach($xml->children() as $child)
	  {
		$data[] = $child;
	  }
	for($i=0; $i<count($data); $i=$i+3)
		{
			echo "<div style='width: auto; float:right; margin: 12px 2px 0px 0px;'>
				<table border='0'>
					<tr><td><a href='".$data[$i]."' target='_blank' style='text-decoration:none;'><font style='color:green; font-weight:bold; font-size:12px;'>".$data[$i+1]."</font>&nbsp;&nbsp;</td></tr>
					<tr><td align='center'><a href='".$data[$i]."' target='_blank' style='text-decoration:none;'><font style='font-size:10px; font-weight:bold;'>".$data[$i+2]."</font></a></td></tr>
				</table>
			      </div>";
		}
	}
else
	{
		echo "";
	}
}
catch(exception $e)
{
	echo "";
}

	?>	
	</div>	


	<div id="rightHeaderImage"></div>
<?php $menuObj->getMenu($menu, $optionMenu, $welcomeMessage); ?>

        <div id="main-content" style="float:left;height:640px;text-align:center;padding-left:0px;">
            <iframe style="display:block;margin-left:auto;margin-right:auto;width:100%;" src="<?php echo $home; ?>" id="rightMenu" name="rightMenu" height="100%;" frameborder="0"></iframe>

        </div>

        <div id="main-footer" style="clear:both;text-align:center;height:20px;">
            <a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a> ver 2.6.12.1 &copy; OrangeHRM Inc. 2005 - 2011 All rights reserved.
        </div>
        <script type="text/javascript">
            //<![CDATA[
            function exploitSpace() {
                dimensions = windowDimensions();

                if (document.getElementById("main-content")) {
                    document.getElementById("main-content").style.height = (dimensions[1]  - 100 - <?php echo $menuObj->getMenuHeight(); ?>) + 'px';
                }

                if (document.getElementById("main-content")) {
                    if (dimensions[0] < 940) {
                        dimensions[0] = 940;
                    }

                    document.getElementById("main-content").style.width = (dimensions[0] - <?php echo $menuObj->getMenuWidth(); ?>) + 'px';
                }
            }

            exploitSpace();
            window.onresize = exploitSpace;
            //]]>
        </script>

    </body>
</html>
<?php ob_end_flush(); ?>
