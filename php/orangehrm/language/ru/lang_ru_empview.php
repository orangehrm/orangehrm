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


$srchlist[0] = array( -1 , 0 , 1, 2, 3);
$srchlist[1] = array( "-$lang_Leave_Common_Select-",
					  $lang_empview_EmpID,
					  $lang_empview_EmpFirstName,
					  $lang_empview_EmpLastName,
					  $lang_empview_EmpMiddleName,
					  $lang_empview_JobTitle,
					  $lang_empview_SubDivision,
					  $lang_empview_Supervisor);

$search    			= $lang_empview_search;
$searchby 			= $lang_empview_searchby;
$description		= $lang_empview_description;
$norecorddisplay 	= $lang_empview_norecorddisplay;
$previous 			= $lang_empview_previous;
$next				= $lang_empview_next;
$employeeid 		= $lang_empview_employeeid;
$employeename 		= $lang_empview_employeename;

$ADD_SUCCESS = $lang_empview_ADD_SUCCESS;
$UPDATE_SUCCESS = $lang_empview_UPDATE_SUCCESS;
$DELETE_SUCCESS = $lang_empview_DELETE_SUCCESS;

$ADD_FAILURE = $lang_empview_ADD_FAILURE;
$UPDATE_FAILURE = $lang_empview_UPDATE_FAILURE;
$DELETE_FAILURE = $lang_empview_DELETE_FAILURE;

switch ($_GET['reqcode']) {

	case  'LAN' :
		       $headingInfo = array ($lang_empview_Language, 1);
		       break;

	case  'EXP' :
			   $headingInfo = array ($lang_empview_WorkExperience, 1);
			   break;

	case  'SAL' :
			   $headingInfo = array ($lang_empview_Payment, 1);
			   break;
	case  'SKI' :
			   $headingInfo = array ($lang_empview_Skills, 1);
			   break;

	case  'LIC' :
			  $headingInfo = array ($lang_empview_Licenses, 1);
			  break;

	case 'EMP' :
		   	 $headingInfo = array ($lang_empview_EmployeeInformation,1);
			  break;

	case  'MEM' :
			$headingInfo = array ($lang_empview_Memberships, 1);
			break;

	case 'REP' :
			$headingInfo = array ($lang_empview_ReportTo,1);
			break;
}
?>
