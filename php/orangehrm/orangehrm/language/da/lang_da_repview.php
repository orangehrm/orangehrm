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


$srchlist[0] = array( -1 , 0 , 1 );
$srchlist[1] = array( "-$lang_Leave_Common_Select-" , $lang_view_ID , $lang_Commn_name);

$searchby = $lang_empview_searchby;
$searchfor = $lang_empview_description;

$Previous = $lang_empview_previous;
$Next     = $lang_empview_next;
$dispMessage = "$lang_empview_norecorddisplay !";
$SearchBy = $lang_empview_searchby;
$description = $lang_Commn_description ;
$search = $lang_empview_search;

$ADD_SUCCESS = $lang_empview_ADD_SUCCESS;
$UPDATE_SUCCESS = $lang_empview_UPDATE_SUCCESS ;
$DELETE_SUCCESS = $lang_empview_DELETE_SUCCESS ;

$ADD_FAILURE = $lang_empview_ADD_FAILURE;
$UPDATE_FAILURE = $lang_empview_UPDATE_FAILURE;
$DELETE_FAILURE = $lang_empview_DELETE_FAILURE;

switch ($_GET['repcode']) {

		case 'EMPVIEW' :
			$heading = array ($lang_repview_ReportID, $lang_repview_ReportName, 1, $lang_repview_ViewEmployeeReports, $lang_repview_message);
			break;

		case 'EMPDEF' :
			$heading = array ($lang_repview_ReportID, $lang_repview_ReportName, 0, $lang_repview_DefineEmployeeReports , $lang_repview_message2);
			break;
}
?>
