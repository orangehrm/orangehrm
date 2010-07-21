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


require_once ROOT_PATH . '/lib/common/Language.php';

$lan = new Language();

require_once($lan->getLangPath("full.php"));

$Previous = $lang_empview_previous;
$Next     = $lang_empview_next;
$norecorddisplay = "$lang_empview_norecorddisplay!";
$searchby = $lang_empview_searchby;
$description = $lang_empview_description;
$search = $lang_empview_search;
$ADD_SUCCESS = $lang_empview_ADD_SUCCESS;
$UPDATE_SUCCESS = $lang_empview_UPDATE_SUCCESS;
$DELETE_SUCCESS = $lang_empview_DELETE_SUCCESS;

$ADD_FAILURE = $lang_empview_ADD_FAILURE;
$UPDATE_FAILURE = $lang_empview_UPDATE_FAILURE;
$DELETE_FAILURE = $lang_empview_DELETE_FAILURE;

switch ($_GET['uniqcode']) {

		case 'EST' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array($lang_view_EmploymentStatusID,$lang_view_EmploymentStatusName);
			$headingInfo = array($lang_view_EmploymentStatusID,$lang_view_EmploymentStatusName,1,$lang_empview_heading,$lang_view_message);
			break;

		case 'JOB' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name);
			$headings= array($lang_jobtitle_jobtitid,$lang_jobtitle_jobtitname);
			$headingInfo = array($lang_jobtitle_jobtitid,$lang_jobtitle_jobtitname,1,$lang_jobtitle_heading,$lang_view_message1);
			break;

		case 'SPC' : // Job Spec
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID, $lang_Commn_name);
			$headings= array($lang_jobspec_id, $lang_jobspec_name);
			$headingInfo = array($lang_jobspec_id, $lang_jobspec_name,1,$lang_jobspec_heading,$lang_view_message3);
			break;

		case 'SKI' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array($lang_view_SkillID,$lang_view_SkillName);
			$headingInfo = array($lang_view_SkillID,$lang_view_SkillName,1,"$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills",$lang_view_message3);
			break;

		case 'LOC' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name, $lang_view_CityName);
			$headings= array("$lang_compstruct_Location $lang_view_ID",$lang_view_LocationName, $lang_view_CityName);
			$headingInfo = array ("$lang_compstruct_Location $lang_view_ID",$lang_view_LocationName,1,"$lang_Menu_Admin_CompanyInfo : $lang_Menu_Admin_CompanyInfo_Locations",$lang_view_message4);
			break;

		case 'MEM' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array("$lang_view_MembershipType $lang_view_ID",$lang_view_MembershipName);
			$headingInfo = array ("$lang_view_MembershipType $lang_view_ID",$lang_view_MembershipName,1,"$lang_Menu_Admin_Memberships : $lang_Menu_Admin_Memberships_MembershipTypes",$lang_view_message13);
			break;


		case 'NAT' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array("$lang_hremp_nationality $lang_view_ID",$lang_view_NationalityName);
			$headingInfo = array ("$lang_hremp_nationality $lang_view_ID",$lang_view_NationalityName,1,$lang_nationalityinformation_heading,$lang_view_message3);
			break;

		case 'LAN' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array("$lang_empview_Language $lang_view_ID",$lang_view_LanguagName);
			$headingInfo = array ("$lang_empview_Language $lang_view_ID",$lang_view_LanguagName,1,"$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills_Languages",$lang_view_message21);
			break;

		case 'MME' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name, $lang_view_Type);
			$headings= array("$lang_view_Membership $lang_view_ID",$lang_view_MembershipName, $lang_view_MembershipType);
			$headingInfo = array ("$lang_view_Membership $lang_view_ID",$lang_view_MembershipName,1,"$lang_Menu_Admin_Memberships : $lang_Menu_Admin_Memberships",$lang_view_message22);
			break;

		case 'SGR' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array("$lang_hrEmpMain_paygrade $lang_view_ID",$lang_view_PayGradeName);
			$headingInfo = array ("$lang_hrEmpMain_paygrade $lang_view_ID",$lang_view_PayGradeName,1,"$lang_Menu_Admin_Job : $lang_Menu_Admin_Job_PayGrades",$lang_view_message24);
			break;

		case 'EDU' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Admin_Common_Course, $lang_Admin_Common_Institute);
			$headings= array("$lang_hrEmpMain_education $lang_view_ID",$lang_Admin_Common_Course, $lang_Admin_Common_Institute);
			$headingInfo = array ("$lang_hrEmpMain_education $lang_view_ID",$lang_hrEmpMain_education,1,"$lang_Menu_Admin_Quali : $lang_Menu_Admin_Quali_Education",$lang_view_message25);
			break;

		case 'EEC' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
			$headings= array($lang_view_EEOJobCategoryid,$lang_view_EEOJobCategoryName);
			$headingInfo = array ($lang_view_EEOJobCategoryid,$lang_view_EEOJobCategoryName,1,$lang_eeojobcat_heading,$lang_view_message3);
			break;

	case 'LAN' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array("$lang_empview_Language $lang_view_ID",$lang_view_LanguagName);
		$headingInfo = array ("$lang_empview_Language $lang_view_ID",$lang_view_LanguagName,1,'Languages',$lang_view_message21);
		break;

	case 'ETH' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array("$lang_view_EthnicRace $lang_view_ID",$lang_view_EthnicRaceName);
		$headingInfo = array ("$lang_view_EthnicRace $lang_view_ID",$lang_view_EthnicRaceName,1,$lang_ethnicrace_heading,$lang_view_message29);
		break;

	case 'LIC' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array($lang_view_License_ID,$lang_view_LicenseDescription);
		$headingInfo = array ($lang_view_License_ID,$lang_view_LicenseDescription,1,$lang_licenses_heading,$lang_view_message3);
		break;

	case 'USR' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array($lang_view_UserID,$lang_view_UserName);

		$esp = (isset($_GET['isAdmin']) && ($_GET['isAdmin'] == 'Yes'))? $lang_view_HRAdmin : $lang_view_ESS;

		$headingInfo = array ($lang_view_UserID,$lang_view_UserName,1, "$lang_view_Users : $esp $lang_view_Users",$lang_view_message34);
		break;

	case 'USG' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array($lang_view_UserGroupID,$lang_view_UserGroupName);
		$headingInfo = array ($lang_view_UserGroupID,$lang_view_UserGroupName,1, "$lang_view_Users : $lang_view_UserGroups",$lang_view_DeleteUserGroup);
                break;

	case 'CUS' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array($lang_view_CustomerId,$lang_view_CustomerName);
		$headingInfo = array ($lang_view_CustomerId,$lang_view_CustomerName,1, "$lang_Menu_Admin_Customers",$lang_view_message34);
		break;

	case 'PRJ' :

		$srchlist = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name );
		$headings= array($lang_view_ProjectId,$lang_view_ProjectName,$lang_view_CustomerName);
		$headingInfo = array ($lang_view_ProjectId,$lang_view_ProjectName,1, "$lang_Menu_Admin_Projects",$lang_view_message34);
		break;

	case 'CTM' :
		$srchlist = array( "-$lang_Leave_Common_Select-" );
		$headings= array($lang_CustomFields_CustomFieldId,$lang_CustomFields_CustomFieldName);
		$headingInfo = array ($lang_CustomFields_CustomFieldId,$lang_CustomFields_CustomFieldName,1, "$lang_CustomFields_Title",$lang_view_message26);
		break;

	case 'CEX' :
		$srchlist = array( "-$lang_Leave_Common_Select-", $lang_view_ID , $lang_DataExport_CustomExportName);
		$headings= array($lang_DataExport_CustomExportId,$lang_DataExport_CustomExportName);
		$headingInfo = array ($lang_DataExport_CustomExportId,$lang_DataExport_CustomExportName,1, "$lang_DataExportCustom_Title",$lang_view_deletePrompt);
		break;

	case 'CIM' :
		$srchlist = array( "-$lang_Leave_Common_Select-", $lang_view_ID , $lang_DataImport_CustomImportName );
		$headings= array($lang_DataImport_CustomImportId,$lang_DataImport_CustomImportName);
		$headingInfo = array ($lang_DataImport_CustomImportId,$lang_DataImport_CustomImportName,1, "$lang_DataImportCustom_Title",$lang_view_deletePrompt);
		break;

}

?>
