<?php

require_once ROOT_PATH . '/lib/common/Language.php';

$lan = new Language();
 
require_once($lan->getLangPath("full.php")); 

$Previous = $lang_empview_previous;
$Next     = $lang_empview_next;
$dispMessage = "$lang_empview_norecorddisplay !";
$SearchBy = $lang_empview_searchby;
$description = $lang_empview_description;
$search = $lang_empview_serach;
$ADD_SUCCESS = $lang_empview_ADD_SUCCESS;
$UPDATE_SUCCESS = $lang_empview_UPDATE_SUCCESS;
$DELETE_SUCCESS = $lang_empview_DELETE_SUCCESS;

$ADD_FAILURE = $lang_empview_ADD_FAILURE;
$UPDATE_FAILURE = $lang_empview_UPDATE_FAILURE;
$DELETE_FAILURE = $lang_empview_DELETE_FAILURE;

switch ($_GET['uniqcode']) {

		case 'EST' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array($lang_view_EmploymentStatusID,$lang_view_EmploymentStatusName);
			$headingInfo = array($lang_view_EmploymentStatusID,$lang_view_EmploymentStatusName,1,$lang_empview_heading,$lang_view_message);	
			break;

		case 'JOB' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name);
			$headings= array($lang_jobtitle_jobtitid,$lang_jobtitle_jobtitname);
			$headingInfo = array($lang_jobtitle_jobtitid,$lang_jobtitle_jobtitname,1,$lang_jobtitle_heading,$lang_view_message1);
			break;
			
		case 'SKI' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array($lang_view_SkillID,$lang_view_SkillName);
			$headingInfo = array($lang_view_SkillID,$lang_view_SkillName,1,"$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills",$lang_view_message3);
			break;		
				
		case 'LOC' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name, $lang_view_CityName);
			$headings= array("$lang_compstruct_Location ID",$lang_view_LocationName, $lang_view_CityName);
			$headingInfo = array ("$lang_compstruct_Location ID",$lang_view_LocationName,1,"$lang_Menu_Admin_CompanyInfo : $lang_Menu_Admin_CompanyInfo_Locations",$lang_view_message4);
			break;		

		case 'MEM' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array("$lang_view_MembershipType ID",$lang_view_MembershipName);					
			$headingInfo = array ("$lang_view_MembershipType ID",$lang_view_MembershipName,1,"$lang_Menu_Admin_Memberships : $lang_Menu_Admin_Memberships_MembershipTypes",$lang_view_message13);
			break;

	
		case 'NAT' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array("$lang_hremp_nationality ID",$lang_view_NationalityName);
			$headingInfo = array ("$lang_hremp_nationality ID",$lang_view_NationalityName,1,$lang_nationalityinformation_heading,$lang_view_message3);
			break;
		
		case 'LAN' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array("$lang_empview_Language ID",$lang_view_LanguagName);
			$headingInfo = array ("$lang_empview_Language ID",$lang_view_LanguagName,1,"$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills_Languages",$lang_view_message21);
			break;

		case 'MME' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name, 'Type' );
			$headings= array("$lang_view_Membership ID",$lang_view_MembershipName, $lang_view_MembershipType);			
			$headingInfo = array ("$lang_view_Membership ID",$lang_view_MembershipName,1,"$lang_Menu_Admin_Memberships : $lang_Menu_Admin_Memberships",$lang_view_message22);
			break;	

		case 'SGR' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array("$lang_hrEmpMain_paygrade ID",$lang_view_PayGradeName);
			$headingInfo = array ("$lang_hrEmpMain_paygrade ID",$lang_view_PayGradeName,1,"$lang_Menu_Admin_Job : $lang_Menu_Admin_Job_PayGrades",$lang_view_message24);
			break;
		
		case 'EDU' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Admin_Common_Course, $lang_Admin_Common_Institute);
			$headings= array("$lang_hrEmpMain_education ID",$lang_Admin_Common_Course, $lang_Admin_Common_Institute);
			$headingInfo = array ("$lang_hrEmpMain_education ID",$lang_hrEmpMain_education,1,"$lang_Menu_Admin_Quali : $lang_Menu_Admin_Quali_Education",$lang_view_message25);
			break; 

		case 'EEC' :
			$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
			$headings= array($lang_view_EEOJobCategoryid,$lang_view_EEOJobCategoryName);
			$headingInfo = array ($lang_view_EEOJobCategoryid,$lang_view_EEOJobCategoryName,1,$lang_eeojobcat_heading,$lang_view_message3);
			break;
						
	case 'LAN' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
		$headings= array("$lang_empview_Language ID",$lang_view_LanguagName);
		$headingInfo = array ("$lang_empview_Language ID",$lang_view_LanguagName,1,'Languages',$lang_view_message21);
		break;
		
	case 'ETH' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
		$headings= array("$lang_view_EthnicRace ID",$lang_view_EthnicRaceName);
		$headingInfo = array ("$lang_view_EthnicRace ID",$lang_view_EthnicRaceName,1,$lang_ethnicrace_heading,$lang_view_message29);
		break;
			
	case 'QLF' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
		$headings= array('Qualification Type ID','Qualification Name');
		$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
		break;
		
	
	case 'LIC' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
		$headings= array('License ID','License Description');
		$headingInfo = array ('License ID','License Description',1,'Qualification : License',$lang_view_message3);
		break;
		
	case 'USR' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
		$headings= array('User ID','User Name');
		
		$esp = (isset($_GET['isAdmin']) && ($_GET['isAdmin'] == 'Yes'))? 'HR Admin' : 'ESS';
		
		$headingInfo = array ('User ID','User Name',1, 'Users: '.$esp.' Users','Deletion could make Orange HRM unusable');
		break;
		
	case 'USG' :
		$srchlist = array( "-$lang_Leave_Common_Select-" , 'ID' , $lang_Commn_name );
		$headings= array('User Group ID','User Group Name');
		$headingInfo = array ('User Group ID','User Group Name',1, 'Users: User Groups','Deletion could make Orange HRM unusable');
		break;
	

}
			 

?>	