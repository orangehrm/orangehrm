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

	/**
	 * Error messages
	 *
	 */

	$lang_Error_PleaseCorrectTheFollowing 			= "Please correct the following";
	$lang_Error_SelectAtLeastOneRecordToDelete 		= "Select at least one record to delete";
	$lang_Error_DoYouWantToDelete 					= "Do you want to delete?";
	$lang_Error_EnterDate							= "Please enter date";

	$lang_Error_PleaseSelectAYear 					= "Please select a Year";
	$lang_Error_PleaseSelectAnEmployee 				= "Please select an Employee";

	$lang_Error_DoYouWantToContinue 				= "Do you want to continue?";

	$lang_Error_ShouldBeNumeric 					= "Should be Numeric";
	$lang_Error_FollowingErrorsWereFound 			= "Following errors were found";
	$lang_Error_AreYouSureYouWantToDelete 			= "Are you sure you want to delete";
	$lang_Error_AccessDenied                        = "Access Denied";

	//leave
	$lang_Error_PleaseSelectAValidFromDate 			= "Please select a valid From Date";
	$lang_Error_PleaseSelectAValidToDate 			= "Please select a valid To Date";
	$lang_Error_PleaseSelectALeaveType 				= "Please select a Leave Type";

	$lang_Error_LeaveDateCannotBeABlankValue 		= "Leave date cannot be a blank value!";
	$lang_Error_NameOfHolidayCannotBeBlank 			= "Name of holiday cannot be blank";

	$lang_Error_NoLeaveTypes 						= "No Leave Types";
           $lang_Error_NoYearSpecified 						= "No Year Specified";
	$lang_Error_NoRecordsFound 						= "No records found!";

	$lang_Error_InvalidDate 						= "Invalid date";

	$lang_Error_NonNumericHours						= "Number of hours should be numeric";
	$lang_Error_EmailConfigConfirm						= "Email configuration is not set or invalid. Do you want to continue ? ";
	$lang_Error_EmailConfigError_SendmailNotFound				= "No file is found under the given Sendmail path";
	$lang_Error_EmailConfigError_SendmailNotExecutable			= "File under the given Sendmail path is not executable";
	$lang_Error_EmailConfigError_SmtpHostNotDefined				= "SMTP host is not defined";
	//PIM
	$lang_Error_LastNameNumbers						= "Last Name contains numbers. Do you want to continue?";
	$lang_Error_FirstNameNumbers					= "First Name contains numbers. Do you want to continue?";
	$lang_Error_MiddleNameNumbers					= "Middle Name contains numbers. Do you want to continue?";
	$lang_Error_MiddleNameEmpty						= "Middle Name Empty. Do you want to continue?";
	$lang_Error_LastNameEmpty						= "Last Name Empty!";
	$lang_Error_FirstNameEmpty						= "First Name Empty!";
	$lang_Error_ChangePane							= "Please save the changes before you move onto another pane!";

	$lang_Error_UploadFailed						= "Upload failed!";
	$lang_Errro_WorkEmailIsNotValid                 = "The work email is not valid";
	$lang_Errro_OtherEmailIsNotValid                = "The other email is not valid";

	$lang_Error_DependantNameEmpty					= "Dependent name is empty";
        $lang_Error_ChildNameEmpty					= "Child name is empty";
        $lang_Error_ChildDobInvalid                                  = "Date of Birth is either empty or contains invalid entry";

	// Company Structure
	$lang_Error_Company_General_Undefined 				= "Please define Company General Information first!";
	$lang_Error_CompStruct_UnitCount 					= "Also #children unit(s) under #parent will be deteted";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "It could cause the company structure to change";

	$lang_Error_SubDivisionNameCannotBeEmpty 			= "Sub-division Name cannot be empty";
	$lang_Error_PleaseSelectATypeOrDefineACustomType 	= "Please select a Type or define a custom type";
	$lang_Error_CompStruct_LocEmpty 					= "Please select a Location or define a new Location and select";
	$lang_Error_CompStruct_Dept_Id_Invalid				= "Department ID is already exists. Please enter different id";

	$lang_Error_LocationNameEmpty 						= "Location Name empty";
	$lang_Error_CountryNotSelected 						= "Country not selected";
	$lang_Error_StateNotSelected 						= "State not selected";
	$lang_Error_CityCannotBeEmpty 						= "City Cannot be empty";
	$lang_Error_AddressEmpty 							= "Address empty";
	$lang_Error_ZipEmpty 								= "Zip - Code Cannot be empty";
	$lang_Error_CompStruct_ZipInvalid 					= "Zip - Code Contains non-numeric characters Here they are #characterList";

	$lang_Error_InvalidEmail 							= "Invalid E-mail address";

	$lang_Error_InvalidDescription						= "Invalid description";

	$lang_Error_FieldShouldBeNumeric					= "Field should be numeric";

	$lang_Error_FieldShouldBeSelected					= "Field should be selected";
	$lang_Error_SelectAtLeastOneCheckBox 				= "Select at least one check box";

	$lang_Notice_NoRights	= "No modules with administrative privileges were found. Adminstration rights of your user group may have " .
			"been revoked or temporarily suspended.";

	/**
	 * Menu Items
	 *
	 */

	// Home
	$lang_Menu_Home 							= "Home";
	$lang_Menu_Help								= 'Help';
	$lang_Menu_HelpContents						= 'Help Contents';
	$lang_Menu_Home_Support 					= "Support";
	$lang_Menu_Home_Forum 						= "Forum";
	$lang_Menu_Home_Blog 						= "Blog";
    $lang_Menu_Home_Training 					= "Training";
    $lang_Menu_Home_AddOns 						= "Add-Ons";
    $lang_Menu_Home_Customizations 				= "Customizations";

	$lang_Menu_Ess								= "ESS";
	$lang_Menu_Pim 								= "PIM";
	// Performance Module
	$lang_Menu_Perform 							= "Performance";
	// Admin Module
	$lang_Menu_Admin 							= "Admin";

	$lang_Menu_Admin_CompanyInfo 				= "Company Info";
	$lang_Menu_Admin_CompanyInfo_Gen 			= "General";
	$lang_Menu_Admin_CompanyInfo_CompStruct 	= "Company Structure";
    $lang_Menu_Admin_Company_Property           = "Company Property";
	$lang_Menu_Admin_CompanyInfo_Locations 		= "Locations";

	$lang_Menu_Admin_Job 						= "Job";
	$lang_Menu_Admin_Job_JobTitles 				= "Job Titles";
	$lang_Menu_Admin_Job_JobSpecs               = "Job Specifications";
	$lang_Menu_Admin_Job_PayGrades 				= "Pay Grades";
	$lang_Menu_Admin_Job_EmpStatus 				= "Employment Status";
	$lang_Menu_Admin_Job_EEO 					= "EEO Job Categories";

	$lang_Menu_Admin_Quali 						= "Qualification";
	$lang_Menu_Admin_Quali_Education 			= "Education";
	$lang_Menu_Admin_Quali_Licenses 			= "Licenses";

	$lang_Menu_Admin_Skills 					= "Skills";
	$lang_Menu_Admin_Skills_Skills 				= "Skills";
	$lang_Menu_Admin_Skills_Languages 			= "Languages";

	$lang_Menu_Admin_Memberships 					= "Memberships";
	$lang_Menu_Admin_Memberships_Memberships 		= "Memberships";
	$lang_Menu_Admin_Memberships_MembershipTypes 	= "Membership Types";

	$lang_Menu_Admin_NationalityNRace 				= "Nationality & Race";
	$lang_Menu_Admin_NationalityNRace_Nationality 	= "Nationality";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces 	= "Ethnic Races";

	$lang_Menu_Admin_Users 							= "Users";
	$lang_Menu_Admin_Users_UserGroups 				= "Admin User Groups";
	$lang_Menu_Admin_Users_HRAdmin 					= "HR Admin Users";
	$lang_Menu_Admin_Users_ESS 						= "ESS Users";

	$lang_Menu_Admin_EmailNotifications = "Email Notifications";
	$lang_Menu_Admin_EmailConfiguration = "Configuration";
	$lang_Menu_Admin_EmailSubscribe = "Subscribe";
	$lang_Menu_Admin_ProjectInfo = "Project Info";
	$lang_Menu_Admin_Customers = "Customers";
	$lang_Menu_Admin_Projects = "Projects";
	$lang_Menu_Admin_DataImportExport = "Data Import/Export";
	$lang_Menu_Admin_DataExport = "Export";
	$lang_Menu_Admin_DataExportDefine = "Define Custom Export";
	$lang_Menu_Admin_DataImport = "Import";
	$lang_Menu_Admin_DataImportDefine = "Define Custom Import";
	$lang_Menu_Admin_CustomFields = "Custom Fields";

	// LDAP Module
	$lang_LDAP_Configuration 	= "#ldapType Configuration";
	$lang_Menu_LDAP_Configuration = "LDAP Configuration";
	$lang_LDAP_Server			= "LDAP Server";
	$lang_LDAP_Port				= "LDAP Port";
	$lang_LDAP_Domain_Name		= "LDAP Domain Name";
	$lang_LDAP_Suffix			= "LDAP User Suffix";
	$lang_LDAP_Type				= "LDAP Type";
	$lang_LDAP_Enable			= "Enable LDAP Authentication";
	$lang_LDAP_Error_Server_Empty	= "Please enter LDAP server name";
	$lang_LDAP_Error_Domain_Empty	= "Please enter LDAP domain name";
	$lang_LDAP__Error_Extension_Disabled = "You haven't enabled the LDAP extension in PHP. Please go through the OrangeHRM LDAP Guide sent with the plugin for more information.";
	$lang_LDAP_Invalid_Port			= "Invalid LDAP Port";

	// Leave Module
	$lang_Menu_Leave 									= "Leave";
	$lang_Menu_Leave_PersonalLeaveSummary 				= "Personal Leave Summary";
	$lang_Menu_Leave_EmployeeLeaveSummary 				= "Employee Leave Summary";
        $lang_Menu_Leave_EmployeeLeaveSummaryPDF 				= "Summery of Employee Leave list";
        $lang_Menu_Leave_EmployeeLeaveDetailedPDF 				= "Detailed  Employee Leave list";
	$lang_Menu_Leave_LeaveSummary 						= "Leave Summary";
	$lang_Menu_Leave_LeavesList 						= "Leaves List";
	$lang_Menu_Leave_ApproveLeave 						= "Approve Leave";
	$lang_Menu_Leave_DefineLeavePeriod					= "Leave Period";
	$lang_Menu_Leave_LeaveTypes 						= "Leave Types";
	$lang_Menu_Leave_Apply 								= "Apply";
	$lang_Menu_Leave_Assign								= "Assign Leave";
	$lang_Menu_Leave_LeaveList 							= "Leave List";
	$lang_Menu_Leave_MyLeave 							= "My Leave";
	$lang_Menu_Leave_DefineDaysOff 						= "Define Days Off";
	$lang_Menu_Leave_DefineDaysOff_Weekends 			= "Days Off";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays 	= "Specific Holidays";
    $lang_Menu_Leave_WorkWeek               			= "Work Week";
    $lang_Menu_Leave_Holidays 	                        = "Holidays";

 	$lang_Leave_Title_Apply_Leave = "Apply Leave";
 	$lang_Leave_Title_Assign_Leave = "Assign Leave";
	$lang_Leave_APPLY_SUCCESS = "Apply succeeded";
	$lang_Leave_APPLY_FAILURE = "Apply failed";
	$lang_Leave_APPROVE_SUCCESS = "Leave Assigned";
	$lang_Leave_APPROVE_FAILURE = "Leave Assign failed";
	$lang_Leave_CANCEL_SUCCESS = "Successfully cancelled";
	$lang_Leave_CANCEL_FAILURE = "Cancellation failed";
	$lang_Leave_CHANGE_STATUS_SUCCESS = "Successfully changed the leave(s)";
	$lang_Leave_BALANCE_ZERO = "Your leave balance is zero";

	// Report
	$lang_Menu_Reports = "Reports";
	$lang_Menu_Reports_ViewReports = "View Reports";
	$lang_Menu_Reports_DefineReports = "Define Reports";

	// Time module
	$lang_Menu_Time = "Time";
	$lang_Menu_Time_Timesheets = "Timesheets";
	$lang_Menu_Time_PersonalTimesheet = "My timesheet";
	$lang_Menu_Time_EmployeeTimesheets = "Employee timesheets";

    // Recruitment module
	$lang_Menu_Recruit = "Recruitment";
	$lang_Menu_Recruit_JobVacancies = "Job Vacancies";
	$lang_Menu_Recruit_JobApplicants = "Applicants";
	
	// Performance Module
	$lang_Menu_Define_Kpi = "KPI List";

	/**
	 * Common
	 */
	$lang_Common_ConfirmDelete				= "Do you want to delete ?";
	$lang_Common_FieldEmpty				= "Field Empty";
	$lang_Common_SelectDelete			= "Select at least one record to delete";
	$lang_Common_SelectField			= "Select the field to search!";
	$lang_Commn_RequiredFieldMark 			= "Fields marked with an asterisk #star are required";
	$lang_Commn_code 						= "Code";
	$lang_Commn_description 				= "Description";
	$lang_Commn_title 						= "Title";
	$lang_Commn_name 						= "Name";
	$lang_Commn_PleaseWait					= "Please wait";
	$lang_Common_Select 				= "Select";
	$lang_Commn_Email						= "E-mail";
	$lang_Common_Loading				= "Loading";
	$lang_Common_LoadingPage                        = "Loading Page";
	$lang_Common_NotApplicable                      = "N/A";
	$lang_Common_Male                      		= "Male";
	$lang_Common_Female                    		= "Female";
	$lang_Common_TypeHereForHints 		= "Start Typing for Hints...";

	$lang_Common_Edit = "Edit";
	$lang_Common_New = "New";
	$lang_Common_Save = "Save";
	$lang_Common_Back = "Back";
    $lang_Common_Add = "Add";
    $lang_Common_Delete = "Delete";
    $lang_Common_Deleted = "Deleted";
    $lang_Common_Replace = "Replace";
    $lang_Common_Search = "Search";
    $lang_Common_Clear = "Clear";
    $lang_Common_Reset = "Reset";
    $lang_Common_Assign = "Assign";
    $lang_Common_Apply = "Apply";
    $lang_Common_Cancel = "Cancel";
    $lang_Common_View = "View";
    $lang_Common_Submit = "Submit";
    $lang_Common_Reject = "Reject";
    $lang_Common_Details = "Details";
    $lang_Common_Approve = "Approve";
    $lang_Common_Update = "Update";
    $lang_Common_InsertTime = "Insert Time";
	$lang_Common_SortAscending = "Sort in ascending order";
	$lang_Common_SortDescending = "Sort in descending order";


	$lang_Common_Time = "Time";
	$lang_Common_Date = "Date";
	$lang_Common_Note = "Note";
	
	$lang_Common_EmployeeName = "Employee Name";

	$lang_Common_AccessDenied = "Access Denied";

	$lang_Common_TEST_EMAIL_SUCCESS = "Test email sent successfully";
	$lang_Common_TEST_EMAIL_FAILIURE = "Test email failed. This can be due to incorrect configuration details";

    $lang_Common_Configure = "Configure";

	//days
	$lang_Common_Monday 					= "Monday";
	$lang_Common_Tuesday 					= "Tuesday";
	$lang_Common_Wednesday 					= "Wednesday";
	$lang_Common_Thursday 					= "Thursday";
	$lang_Common_Friday 					= "Friday";
	$lang_Common_Saturday 					= "Saturday";
	$lang_Common_Sunday 					= "Sunday";

	$lang_Common_Sort_ASC = "Ascending";
	$lang_Common_Sort_DESC = "Descending";
	$lang_Common_EncounteredTheFollowingProblems = "Encountered the following problems";

	$lang_Common_ADD_SUCCESS 				= "Successfully Added";
	$lang_Common_UPDATE_SUCCESS 			= "Successfully Updated";
	$lang_Common_DELETE_SUCCESS 			= "Successfully Deleted";
	$lang_Common_ADD_FAILURE 				= "Failed to Add";
    $lang_Common_DUP_USERS_FAILURE          = "Cannot Add Duplicate User";
	$lang_Common_UPDATE_FAILURE 			= "Failed to Update";
	$lang_Common_DELETE_FAILURE 			= "Failed to Delete";
	$lang_Common_UNKNOWN_FAILURE            = "Operation Failed";
	$lang_Common_DUPLICATE_NAME_FAILURE     = "Name is already in use";
	$lang_Common_COMPULSARY_FIELDS_NOT_ASSIGNED_FAILURE = "Compulsary fields not assigned";
	$lang_Common_IMPORT_FAILURE = "Import failed";
    $lang_Common_IMPORT_TEMPDIR_NOT_WRITABLE_FAILURE = "Import failed. No write permission to temporary directory";
    $lang_Common_IMPORT_FILE_PERMISSION_ERROR = "Import failed. Check file permissions";
    $lang_Common_IMPORT_FILE_EMPTY_ERROR = "Import failed. File was empty";

    // Common navigation
    $lang_Common_First						= "First";
    $lang_Common_Last 						= "Last";
    $lang_Common_Previous 					= "Previous";
	$lang_Common_Next 						= "Next";

	$lang_Leave_Common_Weekend 				= "Weekend";
	$lang_Leave_Common_Holiday				= "Holiday";

	// admin module
	$lang_Admin_Common_Institute 			= "Institute";
	$lang_Admin_Common_Course 				= "Course";
	$lang_Admin_education_InstituteCannotBeBlank = "Institute Cannot be a Blank Value!";
	$lang_Admin_CourseCannotBeBlank         = "Course Cannot be a Blank Value!";
	$lang_Admin_License_DescriptionCannotBeBlank = "License description cannot be blank!";

	// leave module
	$lang_Leave_Title = "OrangeHRM - Leave Module";
	$lang_Leave_Common_Date 				= "Date";

	$lang_Leave_Common_FromDate 			= "From Date";
	$lang_Leave_Common_ToDate 				= "To Date";

	$lang_Leave_Common_LeaveQuotaNotAllocated = "Leave Quota not allocated. Please consult HR Admin";

	$lang_Leave_Common_LeaveType 			= "Leave Type";
	$lang_Leave_Common_Status 				= "Status";
	$lang_Leave_Common_Length 				= "Full Day/Half Day";
	$lang_Leave_Common_Range 				= "Range";
	$lang_Leave_Common_Comments 			= "Comments";
	$lang_Leave_Common_Comment 				= "Comment";
	$lang_Leave_Common_Approved 			= "Approved";
	$lang_Leave_Common_Cancelled 			= "Cancelled";
	$lang_Leave_Common_Cancel 				= "Cancel";
	$lang_Leave_Common_PendingApproval 		= "Pending Approval";
	$lang_Leave_Common_Rejected 			= "Rejected";
	$lang_Leave_Common_Taken 				= "Taken";
	$lang_Leave_Common_InvalidStatus 		= "Invalid Status";
	$lang_Leave_Common_StatusDiffer 		= "Partly Approved / Status Differ";
	$lang_Leave_Common_FullDay 				= "Full Day";
	$lang_Leave_Common_HalfDayMorning 		= "Half Day / Morning";
	$lang_Leave_Common_HalfDayAfternoon 	= "Half Day / Afternoon";
	$lang_Leave_Common_HalfDay 				= "Half Day";
	$lang_Leave_Common_LeaveTaken 			= "Leave Taken";
	$lang_Leave_Common_LeaveRemaining 		= "Leave Remaining";
	$lang_Leave_Common_LeaveScheduled 		= "Leave Scheduled";
	$lang_Leave_Common_LeaveTypeName 		= "Leave Type Name";
	$lang_Leave_Common_LeaveTypeId 			= "Leave Type Id";
	$lang_Leave_Common_Select 				= "Select";
	$lang_Leave_Common_oldLeaveTypeName 	= "Existing Leave Type Name";
	$lang_Leave_Common_newLeaveTypeName 	= "New Leave Type Name";
	$lang_Leave_Common_EmployeeName 		= "Employee Name";
	$lang_Leave_Common_LeaveEntitled 		= "Leave Entitled";
	$lang_Leave_Common_Year 				= "Year";
	$lang_Leave_Common_ListOfTakenLeave 	= "List of Taken Leave";
	$lang_Leave_Common_Remove 				= "Remove";
	$lang_Leave_Common_AllEmployees			= "All Employees";
	$lang_Leave_Common_All					= "All";
	$lang_Leave_Common_InvalidDateRange 	= "Invalid Date Range";

	$lang_Leave_Common_NameOfHoliday 		= "Name of Holiday";
	$lang_Leave_Common_Recurring 			= "Repeats annually";

 	$lang_Leave_Leave_list_Title1 			= "Approve Leave";
 	$lang_Leave_all_emplyee_leaves			= "Leave List";
 	$lang_Leave_all_emplyee_taken_leaves	= "Taken Leaves";
	$lang_Leave_Leave_Requestlist_Title1 	= "Approve Leave Request for #employeeName";
	$lang_Leave_Leave_Requestlist_Title2 	= "Change Leave Request for #employeeName";
	$lang_Leave_Leave_list_Title2 			= "Leave Taken by #employeeName in #dispYear";
	$lang_Leave_Leave_list_Title3 			= "Leave List";
	$lang_Leave_Leave_list_Title4 			= "Scheduled Leaves";
	$lang_Leave_Leave_list_TitleAllSubordinates	= "Leave List (All Subordinates)";
	$lang_Leave_Leave_list_TitleAllEmployees= "Leave List (All Employees)";
	$lang_Leave_Leave_list_TitleMyLeaveList = "My Leave List";
	$lang_Leave_Leave_list_Title5 			= "Taken Leaves";
	$lang_Leave_Leave_list_ShowLeavesWithStatus = "Show leave with status";
	$lang_Leave_Leave_list_SelectAtLeastOneStatus = "Select at least one leave status";
	$lang_Leave_Leave_list_From             = "From";
	$lang_Leave_Leave_list_To               = "To";
    $lang_Leave_Leave_list_Period           = "Period";
	$lang_Leave_Select_Employee_Title 		= "Select Employee";
	$lang_Leave_Leave_Summary_Title 		= "Leave Summary";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Leave Summary for #employeeName for #dispYear";
	$lang_Leave_Select_Employee_Title 		= "Select employee or leave type";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Leave Summary for #dispYear";
	$lang_Leave_Leave_Summary_SUP_Title 	= "Leave Summary for #employeeName for #dispYear";
	$lang_Leave_Define_leave_Type_Title 	= "Define Leave Type ";
	$lang_Leave_Leave_Type_Summary_Title 	= "Leave Types";
	$lang_Leave_Leave_Holiday_Specific_Title = "Define Days Off : Specific Holidays";
	$lang_Leave_Leave_Holiday_Weeked_Title 	= "Define Days Off : Weekend";
	$lang_Leave_Summary_Deleted_Types_Shown = "Denotes deleted leave types.";
    $lang_Leave_Summary_Deleted_Types_MoreInfo = "Deleted leave types are shown here if employees have used these leave types before they were deleted. Deleted leave types are retained in the system but cannot be used for new leave requests.";
    $lang_Leave_LeaveCommentTooLong			= "Comment is too long. Maximun allowed size is %s characters.";
    $lang_Leave_Holiday_Edit_Warning = "All the applied future leave for date - #date , will be reset to Pending Approval. Are you sure?";
    $lang_Leave_Weekend_Disabled_Warning = " #star The total number of applied leave should be zero to change the weekends.";

	$lang_Leave_Holiday = "Holiday";
	$lang_Leave_NoOfDays = "No of Days";
	$lang_Leave_NoOfHours = "No of Hours";
	$lang_Leave_Period = "Leave Period";
	$lang_Leave_Closed = "Closed";

	$lang_Leave_Define_IsDeletedName  	= "Deleted Leave Type exists with the same name.";
	$lang_Leave_Define_UndeleteLeaveType    = "To re-use deleted leave type instead of creating a new leave type, click:";
	$lang_Leave_NAME_IN_USE_ERROR 		= "Leave Type name is in use. Choose another name.";
	$lang_Leave_ADD_FAILURE			= "Failed to Add";
	$lang_Leave_ADD_SUCCESS 		= "Successfully Added";
	$lang_Leave_LEAVE_TYPE_NOT_FOUND_ERROR 	= "Leave type not found";
	$lang_Leave_UNDELETE_SUCCESS		= "Leave type successfully undeleted.";
	$lang_Leave_DUPLICATE_LEAVE_TYPE_ERROR  = "Duplicate leave type names specified. Leave type names should be unique.";
	$lang_Leave_LEAVE_TYPE_EDIT_ERROR       = "Error saving changes";
	$lang_Leave_LEAVE_TYPE_EDIT_SUCCESS     = "Changes saved successfully";
	$lang_Leave_NO_CHANGES_TO_SAVE_WARNING  = "No changes to save";
	$lang_Leave_Undelete			= "Undelete";
	$lang_Leave_HOLIDAY_IN_USE_ERROR = "Date is in use. Choose another date";

	$lang_Leave_Summary_Error_CorrectLeaveSummary	= "Error(s) in the Leave Summary!\\nPlease correct the highlighted leave quota values.";
	$lang_Leave_Summary_Error_NonNumericValue		= "Non-numeric";
	$lang_Leave_Summary_Error_InvalidValue			= "Invalid value";

	$lang_bankInformation_code 				= "Code";
	$lang_bankInformation_description 		= "Description";

	$lang_compstruct_add 			= "Add";
	$lang_compstruct_delete 		= "Delete";
	$lang_compstruct_clear 			= "Clear";
	$lang_compstruct_hide 			= "Hide";
	$lang_compstruct_save 			= "Save";

 	$lang_comphire_heading 							= "Company Hierarchy: Company Information";
	$lang_comphire_relationalhierarchy 				= "Relational Hierarchy";
	$lang_comphire_employee 						= "Employee";
	$lang_comphire_definitionlevel 					= "Definition Level";
	$lang_comphire_telephone 						= "Telephone";
	$lang_comphire_fax 								= "Fax";
	$lang_comphire_email							= "Email";
	$lang_comphire_url 								= "URL";
	$lang_comphire_logo 							= "Logo";
	$lang_comphire_selecthie 						= "Select Hierarchy";
	$lang_comphire_selectdef 						= "Select Def. Level";
	$lang_compstruct_heading 						= "Company Info : Company Structure";
	$lang_compstruct_Dept_Id						= "Department ID:";
	$lang_compstruct_frmSub_divisionHeadingAdd 		= "Add a sub-division to";
	$lang_compstruct_frmSub_divisionHeadingEdit 	= "Edit";
	$lang_compstruct_Name 							= "Name";
	$lang_compstruct_Type 							= "Type";
	$lang_compstruct_Division 						= "Division";
	$lang_compstruct_Description 					= "Description";
	$lang_compstruct_Department 					= "Department";
	$lang_compstruct_Team 							= "Team";
	$lang_compstruct_Other 							= "Other";
	$lang_compstruct_Location 						= "Location";
	$lang_compstruct_frmNewLocation 				= "Define a new location";
	$lang_compstruct_Address 						= "Address";
	$lang_compstruct_country						= "Country";
	$lang_compstruct_state 							= "State / Province";
	$lang_compstruct_city 							= "City";
	$lang_compstruct_ZIP_Code 						= "ZIP Code";
	$lang_compstruct_Phone 							= "Phone";
	$lang_compstruct_no_root 						= "Root not found! Please define the root.";

 	$lang_corptit_heading 							= "Corporate Titles : Job Information";
	$lang_corptit_topinhierachy 					= "Top In Hierarchy";
	$lang_corptit_multipleheads 					= "Multiple Heads";
	$lang_corptit_headcount 						= "Head Count";
	$lang_corptit_nextlevelupgrade 					= "Next Level Upgrade";
	$lang_corptit_selectcor 						= "Select Corp. Title";
	$lang_corptit_salarygrade 						= "Salary Grade";
	$lang_corptit_selectsal 						= "Select Salary Grade";

 	$lang_costcenters_heading 						= "Cost Center : Company Information";

 	$lang_countryinformation_heading 				= "Country Information : Geo Information";

 	$lang_currencytypes_heading 					= "Currency Type : Job Information";


 	$lang_districtinformation_heading 				= "City Information : Geo Information";
	$lang_districtinformation_selectcounlist 		= "Select Country";
	$lang_districtinformation_selstatelist 			= "Select State";

 	$lang_eeojobcat_heading 						= "Job : EEO Job Category";
	$lang_eeojobcat_description 					= "Title";
	$lang_eeojobcat_TitleContainsNumbers            = "Title contains numbers. Do you want to continue?";
	$lang_eeojobcat_TitleMustBeSpecified            = "Title must be specified.";

 	$lang_electorateinformation_heading 			= "Electorate Information : Geo Information";
	$lang_emprepinfo_heading						= "Define Employee Reports";

 	$lang_emptypes_heading 					= "Employee Types : Nexus Information";
	$lang_emptypes_datelimited 				= "Date Limited";
	$lang_emptypes_prefix 					= "Prefix";

 	$lang_empview_heading 					= "Job : Employment Status";
	$lang_empview_EmpID 					= "Emp. ID";
	$lang_empview_EmpFirstName 				= "Emp. First Name";
	$lang_empview_EmpLastName 				= "Emp. Last Name";
	$lang_empview_EmpMiddleName 			= "Emp. Middle Name";
	$lang_empview_search					= "Search";
	$lang_empview_searchby 					= "Search By:";
	$lang_empview_description 				= "Search For:";
	$lang_empview_norecorddisplay 			= "No Records to Display";
	$lang_empstatus_PleaseEnterEmploymentStatus = "Please enter Employment Status Description";
	$lang_empview_SelectField               = "Select the field to search!";

	$lang_empview_last 						= "Last";
	$lang_empview_next 						= "Next";
	$lang_empview_previous 					= "Previous";
	$lang_empview_first						= "First";

	$lang_empview_employeeid 				= "Employee Id";
	$lang_empview_employeename 				= "Employee Name";
	$lang_empview_ADD_SUCCESS 				= "Successfully Added";
	$lang_empview_UPDATE_SUCCESS 			= "Successfully Updated";
	$lang_empview_DELETE_SUCCESS 			= "Successfully Deleted";
	$lang_empview_ADD_FAILURE 				= "Failed to Add";
	$lang_empview_DUPLICATE_EMPCODE_FAILURE	= "Failed to Add. Duplicate Employee Code Entered.";
	$lang_empview_SELF_SUPERVISOR_FAILURE 	= "You can not add yourself as a supervisor to you";
	$lang_empview_UPDATE_FAILURE 			= "Failed to Update";
	$lang_empview_DELETE_FAILURE 			= "Failed to Delete";
        $lang_empview_DELETE_PROHIBITED_FAILURE         = "You can't delete the group you belong to";
	$lang_empview_Language 					= "Language";
	$lang_empview_WorkExperience 			= "Work Experience";
	$lang_empview_Payment 					= "Payment";
	$lang_empview_Skills 					= "Skills";
	$lang_empview_Licenses 					= "Licenses";
	$lang_empview_EmployeeInformation 		= "Employee Information";
	$lang_empview_Memberships 				= "Memberships";
	$lang_empview_Report 					= "Report";
	$lang_empview_ReportTo					= "Report To";
	$lang_empview_SubDivision				= "Sub-Division";
	$lang_empview_JobTitle					= "Job Title";
	$lang_empview_Supervisor 				= "Supervisor";
	$lang_empview_EmploymentStatus			= "Employment status";
	$lang_emppop_title                      = "Search Employees";

 	$lang_ethnicrace_heading 				= "Nationality & Race :Ethnic Races";
 	$lang_ethnicrace_NameShouldBeSpecified  = "Ethnic Race Name should be specified";

 	$lang_extracurractcat_heading 			= "Extra Curricular Activities Category : Qualifications Information";

 	$lang_extracurractinfo_heading 			= "Extra Curricular Activity Information : Qualification Information";
	$lang_extracurractinfo_extracuaccat 	= "Extra Curricular Act Category";
	$lang_extracurractinfo_selectsecucat 	= "Select Ex. Curr. Cat";

 	$lang_geninfo_heading 					= "Company Info : General";
	$lang_geninfo_compname 					= "Company Name";
	$lang_geninfo_numEmployees				= "Number of Employees";
	$lang_geninfo_taxID 					= "Tax ID";
	$lang_geninfo_naics 					= "NAICS";
	$lang_geninfo_err_CompanyName 			= "Company name cannot be blank";
	$lang_geninfo_err_Phone 				= "Not a valid phone number";
    $lang_geninfo_err_Fax                   = "Not a valid fax number";

	$lang_geninfo_err_CommentLengthWarning	= "Length of comments exceeds the limit. Text at the end of the comment will be lost.";

 	$lang_hierarchydef_heading 				= "Hierarchy Information : Company Information";

	$lang_hremp_EmpFirstName 				= "First Name";
	$lang_hremp_EmpLastName 				= "Last Name";
	$lang_hremp_EmpMiddleName 				= "Middle Name";
	$lang_hremp_nickname 					= "Nick Name ";
	$lang_hremp_photo 						= "Photo ";
	$lang_hremp_ssnno 						= "SSN No :";
	$lang_hremp_nationality 				= "Nationality";
	$lang_hremp_sinno 						= "SIN No :";
	$lang_hremp_dateofbirth 				= "Date of Birth";
	$lang_hremp_otherid 					= "Other ID";
	$lang_hremp_maritalstatus 				= "Marital Status";
	$lang_hremp_selmarital					= "--Select--";
	$lang_hremp_selectnatio 				= "Select Nationality";
	$lang_hremp_selethnicrace 				= "Select Ethnic Race";
	$lang_hremp_smoker 						= "Smoker";
	$lang_hremp_gender 						= "Gender";
	$lang_hremp_dlicenno 					= "Driver's License Number";
	$lang_hremp_licexpdate 					= "License Expiry Date ";
	$lang_hremp_militaryservice 			= "Military Service ";
	$lang_hremp_ethnicrace 					= "Ethnic Race ";
	$lang_hremp_jobtitle 					= "Job Title";
	$lang_hremp_selempstat 					= "Select Empl. Status";
    $lang_hremp_jobspec                     = "Job Specification";
    $lang_hremp_jobspecduties               = "Job Duties";
	$lang_hremp_eeocategory 				= "EEO Category";
	$lang_hremp_seleeocat 					= "Select EEO Cat";
	$lang_hremp_joindate 					= "Joined Date";
	$lang_hremp_termination_date			= "Termination Date";
	$lang_hremp_termination_reason			= "Termination Reason";
	$lang_hremp_SelectJobTitle				= "Select Job Title";
	$lang_hremp_EmpStatus					= "Employment Status";
	$lang_hremp_Workstation					= "Workstation";
	$lang_hremp_Subdivision					= "Sub-division";
    $lang_hremp_Locations                   = "Locations";

    $lang_hremp_MaritalStatus_Unmarried		= "Unmarried";
    $lang_hremp_MaritalStatus_Married		= "Married";
    $lang_hremp_MaritalStatus_Divorced		= "Divorced";
    $lang_hremp_MaritalStatus_Others		= "Others";

 	$lang_hremp_dependents 					= "Dependents";
	$lang_hremp_children 					= "Children";
	$lang_hremp_relationship 				= "Relationship";
	$lang_hremp_AssignedDependents			= "Assigned Dependents";
	$lang_hremp_AssignedChildren			= "Assigned Children";

	$lang_hremp_AssignedEmergencyContacts   = "Assigned Emergency Contacts";

	$lang_hremp_street1						= "Street 1";
	$lang_hremp_street2 					= "Street 2";

	$lang_hremp_hmtele 						= "Home Telephone";
	$lang_hremp_mobile 						= "Mobile";
	$lang_hremp_worktele 					= "Work Telephone";
	$lang_hremp_city 						= "City/Town";
	$lang_hremp_workemail 					= "Work Email";
	$lang_hremp_otheremail 					= "Other Email";
	$lang_hremp_passport 					= "Passport";
	$lang_hremp_visa 						= "Visa";
	$lang_hremp_citizenship 				= "Citizenship";
	$lang_hremp_passvisano 					= "Passport/Visa No";
	$lang_hremp_issueddate 					= "Issued Date";
	$lang_hremp_i9status 					= "I9 Status";
	$lang_hremp_dateofexp 					= "Date of Expiry";
	$lang_hremp_i9reviewdate 				= "I9 Review Date";
	$lang_hremp_AssignedPassportVisas		= "Assigned Passport/Visa";
	$lang_hremp_path 						= "Path";
	$lang_hremp_filename 					= "File Name";
	$lang_hremp_size 						= "Size";
	$lang_hremp_type 						= "Type";
	$lang_hremp_name 						= "Name";
	$lang_hremp_InvalidPhone				= "Not a valid phone/fax number";
	$lang_hremp_largefileignore				= "1M Max, any larger attachments will be ignored";
	$lang_hremp_PleaseSelectFile				= "Please select a file.";
	$lang_hremp_ShowFile					= "Show File";
	$lang_hremp_Save					= "Save";
	$lang_hremp_Delete					= "Delete";
	$lang_lang_uploadfailed					= "Upload Failed";

	$lang_hremp_browse						= "Browse";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "Are you sure you want to delete the photograph";

	$lang_hremp_SelectAPhoto				= "Select a Photo";
	$lang_hremp_ClickToSeeFullSizeImage		= "Click on the photo to see the full size image";
	$lang_hremp_PhotoMaxSize				= "1M Max";
	$lang_hremp_PhotoDimensions				= "Dimensions 100x120";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "Issued date should be before expiration date";
	$lang_hremp_FromDateShouldBeBeforeToDate= "From date should be before To date";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "Starting Day should be before ending Date";
    $lang_hremp_CommentsShouldBeLimitedTo255Chars = "Comments should be limited to 255 characters";
    $lang_hremp_CommentsShouldBeLimitedTo200Chars = "Comments should be limited to 200 characters";
    $lang_hremp_CommentsShouldBeLimitedTo100Chars = "Comments should be limited to 100 characters";

    $lang_hremp_TerminationReasonShouldBeLimitedTo256Chars = "Termination reason should be limited to 256 characters";

	$lang_hremp_ContractExtensionStartDate	= "Contract Extension Start Date";
	$lang_hremp_ContractExtensionEndDate	= "Contract Extension End Date";
	$lang_hremp_EmployeeContracts 			= "Employee Contracts";
	$lang_hremp_AssignedContracts			= "Assigned Contracts";

	$lang_hremp_ShowEmployeeContracts 			= "Show employee contracts";
	$lang_hremp_HideEmployeeContracts 			= "Hide employee contracts";

    $lang_hremp_ShowEmployeeJobHistory      = "Show employee history";
    $lang_hremp_HideEmployeeJobHistory      = "Hide employee history";

    $lang_hremp_EmployeeJobHistory          = "Employee History";
    $lang_hremp_EmployeePreviousPositions   = "Previous Positions";
    $lang_hremp_EmployeePreviousSubUnits    = "Previous Sub-units";
    $lang_hremp_EmployeePreviousLocations   = "Previous Locations";
    $lang_hremp_EmployeeHistoryFrom         = "From";
    $lang_hremp_EmployeeHistoryTo           = "To";
    $lang_hremp_EmployeeHistoryNoItemsFound = "No items found";
    $lang_hremp_EmployeeAddHistoryItem      = "Add Employee History Item";
    $lang_hremp_EmployeeJobTitleOption      = "Job Title";
    $lang_hremp_EmployeeSubDivisionOption      = "Sub Division";
    $lang_hremp_EmployeeLocationOption      = "Location";
    $lang_hremp_EmployeeHistory_PleaseSelectJobTitle = "Please select a Job Title";
    $lang_hremp_EmployeeHistory_PleaseSelectSubDivision = "Please select a sub division";
    $lang_hremp_EmployeeHistory_PleaseSelectLocation = "Please select a location";
    $lang_hremp_EmployeeHistory_StartShouldBeforeEnd = "Start date should be before end date";
    $lang_hremp_EmployeeHistory_PleaseSpecifyStartDate = "Please specify valid start date";
    $lang_hremp_EmployeeHistory_PleaseSpecifyEndDate = "Please specify valid end date";

    $lang_hremp_EmployeeHistory_DatesWrong = "Please enter valid dates. From date should not be after To date";
    $lang_hremp_EmployeeHistory_ExpectedDateFormat = "Expected date format: ";
    $lang_hremp_EmployeeHistory_DatesWithErrorsHighlighted = "Dates with errors are highlighted in red ";

    $lang_hremp_AddLocation                 = "Add Location";
    $lang_hremp_SelectLocation              = "Select Location";
    $lang_hremp_PleaseSelectALocationFirst  = "Please select a location first";
    $lang_hremp_ErrorAssigningLocation      = "Error assigning location";
    $lang_hremp_ErrorRemovingLocation      = "Error removing location";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "Salary should be within min and max";

	$lang_hremp_SelectCurrency				= "Select Currency";
	$lang_hremp_SelectPayGrade				= "Select Pay Grade";
	$lang_hremp_Currency_is_already_exist	= 'Currency is already exist';

	$lang_hremp_ContractExtensionId			= "Contract Extension ID";
	$lang_hremp_ContractStartDate			= "Contract Start Date";
	$lang_hremp_ContractEndDate				= "Contract End Date";
    $lang_hremp_FieldShouldBeNumeric        = "Field should be numeric";

    $lang_hremp_Language					= "Language";

 	$lang_hremplan_employeelanguageflu 		= "Employee Language Fluency";
	$lang_hremplan_fluency 					= "Fluency";
	$lang_hremplan_ratinggarde 				= "Fluency";
	$lang_hremplan_assignlanguage 			= "Assigned Languages";

	$lang_hremplan_SelectLanguage			= "Select Language";
	$lang_hremplan_SelectFluency			= "Select Fluency";
	$lang_hremplan_SelectRating 			= "Select Rating";
	$lang_hremplan_NameContainsNonChars		= 'Language name can contain only characters';

	$lang_hremp_PleaseSelectJobTitle= "Please Select a Job Title for this Employee {here}";

	$lang_hremp_ie_CurrentSupervisors 		= "i.e. Current Employee's Supervisors";
	$lang_hremp_ie_CurrentSubordinates 		= "i.e. Current Employee's Subordinates";

	$lang_hremp_ie_PleaseSpecifyAtLeastOnePhoneNo = "Please specify at least one phone number";

 	$lang_hremplicenses_employeelicen 		= "Employee Licenses";
	$lang_hremplicenses_assignlicenses 		= "Assigned Licenses";
	$lang_hremplicenses_licentype 			= "License Type";
	$lang_hremplicenses_assignlicen 		= "Assigned Licenses";

	$lang_hremplicenses_NoLicenseSelected	= "No license selected";
	$lang_hremplicenses_SelectLicenseType	= "Select License Type";

	$lang_hrempmemberships_NoMembershipTypeSelected	        = "No membership type selected";
	$lang_hrempmemberships_NoMembershipSelected	        = "No membership selected";
	$lang_hrempmemberships_NoSubscriptionOwnerSelected	= "No subscription owner selected";

	$lang_hremp_SelectAtLEastOneAttachment = "Select at least one Attachment to Delete";

 	$lang_hrEmpMain_ratinggarde 			= "Competency";
	$lang_hrEmpMain_assignlanguage 			= "Assigned Languages";
	$lang_hrEmpMain_Writing 				= "Writing";
	$lang_hrEmpMain_Speaking 				= "Speaking";
	$lang_hrEmpMain_Reading 				= "Reading";
	$lang_hrEmpMain_Poor 					= "Poor";
	$lang_hrEmpMain_Basic 					= "Basic";
	$lang_hrEmpMain_Good 					= "Good";
	$lang_hrEmpMain_MotherTongue 			= "Mother Tongue";
	$lang_hrEmpMain_Individual 				= "Individual";
	$lang_hrEmpMain_employeemembership 		= "Employee Memberships";
	$lang_hrEmpMain_subownership 			= "Subscription Ownership";
	$lang_hrEmpMain_subamount 				= "Subscription Amount";
	$lang_hrEmpMain_subcomdate 				= "Subscription Commence Date";
	$lang_hrEmpMain_subredate 				= "Subscription Renewal Date";
	$lang_hrEmpMain_selmemtype 				= "Select Membership Type";
	$lang_hrEmpMain_selmemship 				= "Select Membership";
	$lang_hrEmpMain_selownership 			= "Select Ownership";
	$lang_hrEmpMain_assignmemship 			= "Assigned Memberships";
	$lang_hrEmpMain_paygrade 				= "Pay Grade";
	$lang_hrEmpMain_currency 				= "Currency";
	$lang_hrEmpMain_minpoint 				= "Minimum Salary";
	$lang_hrEmpMain_maxpoint 				= "Maximum Salary";
	$lang_hrEmpMain_bassalary 				= "Basic Salary";
	$lang_hrEmpMain_assignedsalary 			= "Assigned Salary";
	$lang_hrEmpMain_payfrequency 			= "Pay Frequency";

 	$lang_hrEmpMain_heading 				= "Employee Report";
	$lang_hrEmpMain_supervisorsubordinator 	= "Supervisor / Subordinate";
	$lang_hrEmpMain_reportingmethod 		= "Reporting Method";
	$lang_hrEmpMain_subordinateinfomation 	= "Subordinates";
	$lang_hrEmpMain_supervisorinfomation 	= "Supervisors";
	$lang_hrEmpMain_selecttype 				= "Select Method";
	$lang_hrEmpMain_Direct 					= "Direct";
	$lang_hrEmpMain_Indirect 				= "Indirect";
	$lang_hrEmpMain_ReportToId 				= "Id";
	$lang_hrEmpMain_ReportToName 			= "Name";
	$lang_hrEmpMain_employeeskill 			= "Employee Skills";
	$lang_hrEmpMain_yearofex 				= "Years of Experience";
	$lang_hrEmpMain_assignskills 			= "Assigned Skills";
	$lang_hrEmpMain_employerworkex 			= "Employee Work Experience";
	$lang_hrEmpMain_employer 				= "Employer";
	$lang_hrEmpMain_enddate 				= "End Date";
	$lang_hrEmpMain_startdate 				= "Start Date";
	$lang_hrEmpMain_assignworkex 			= "Assigned Work Experience";
	$lang_hrEmpMain_workexid 				= "Work Experience ID";
	$lang_hrEmpMain_internal 				= "Internal";
	$lang_hrEmpMain_major 					= "Major/Specialization";
	$lang_hrEmpMain_education 				= "Education";
	$lang_hrEmpMain_gpa 					= "GPA/Score";
	$lang_hrEmpMain_assigneducation 		= "Assigned Education";
	$lang_hrEmpMain_assignattach 			= "Assigned Attachments";

	$lang_hrEmpMain_SelectEducation			= "Select Education";
	$lang_hrEmpMain_YearsOfExperiencCannotBeBlank = "Years of Experience Cannot be Blank";
	$lang_hrEmpMain_YearsOfExperiencWrongFormat = "Years of Experience - Wrong input format";
	$lang_hrEmpMain_YearsOfExperiencBetween = "Years of Experience should be a value between #range";
	$lang_hrEmpMain_Skill 					= "Skill";
	$lang_hrEmpMain_EnterFromDateFirst      = "Enter From Date first";

	$lang_hrEmpMain_subown_Company = 'Company';
	$lang_hrEmpMain_subown_Individual = 'Individual';

	$lang_hrEmpMain_arrRepType_Supervisor = 'Supervisor';
	$lang_hrEmpMain_arrRepType_Subordinate = 'Subordinate';

	$lang_hrEmpMain_arrRepMethod_Direct = 'Direct';
	$lang_hrEmpMain_arrRepMethod_Indirect = 'Indirect';

	$lang_hrEmpMain_SelectMethod = 'Select Method';

	$lang_hrEmpMain_SubscriptionAmountShouldBeNumeric = "Subscription amount should be numeric";
	$lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue = "Membership subscription amount is empty. Do you want to continue";

	$lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate = "Commence Date should be before renewal date";

	$lang_hrEmpMain_membershiptype = "Membership Type";
	$lang_hrEmpMain_membership = "Membership";

	$lang_hrEmpMain_FederalIncomeTax = "Federal Income Tax";
	$lang_hrEmpMain_TaxStatus = "Status";
	$lang_hrEmpMain_TaxExemptions = "Exemptions";

	$lang_hrEmpMain_TaxStatusSelect = "--Select--";
	$lang_hrEmpMain_TaxStatusMarried = "Married";
	$lang_hrEmpMain_TaxStatusSingle = "Single";
	$lang_hrEmpMain_TaxStatusNonResidentAlien = "Non Resident Alien";
	$lang_hrEmpMain_TaxStatusNotApplicable = "Not Applicable";

	$lang_hrEmpMain_StateIncomeTax = "State Income Tax";
	$lang_hrEmpMain_TaxState = "State";
	$lang_hrEmpMain_TaxUnemploymentState = "Unemployment State";
	$lang_hrEmpMain_TaxWorkState = "Work State";

	$lang_hrEmpMain_DirectDebitAccount = "Account";
	$lang_hrEmpMain_DirectDebitAccountType = "Account Type";
	$lang_hrEmpMain_DirectDebitAccountTypeChecking = "Checking";
	$lang_hrEmpMain_DirectDebitAccountTypeSavings = "Savings";
	$lang_hrEmpMain_DirectDebitRoutingNumber = "Routing Number";
	$lang_hrEmpMain_DirectDebitAmount = "Amount";
	$lang_hrEmpMain_DirectDebitTransactionType = "Transaction Type";

	$lang_hrEmpMain_DirectDebitSelectTransactionType = "--Select--";

	$lang_hrEmpMain_DirectDebitTransactionTypeBlank = "Blank";
	$lang_hrEmpMain_DirectDebitTransactionTypePercentage = "Percentage";
	$lang_hrEmpMain_DirectDebitTransactionTypeFlat = "Flat";
	$lang_hrEmpMain_DirectDebitTransactionTypeFlatMinus = "Flat - ";

	$lang_hrEmpMain_DirectDebitAssigned = "Assigned Direct Debit Accounts";
	$lang_hrEmpMain_DirectDebitAccountShouldBeSpecified = "Account should be specified";
	$lang_hrEmpMain_DirectDebitRoutingNumberShouldBeSpecified = "Routing number should be specified";
	$lang_hrEmpMain_DirectDebitRoutingNumberShouldBeNumeric = "Routing number should be numeric";
	$lang_hrEmpMain_DirectDebitAmountShouldBeSpecified = "Amount should be specified";
	$lang_hrEmpMain_DirectDebitAmountShouldBeNumeric = "Amount should be numeric";
	$lang_hrEmpMain_DirectDebitAmountTooLarge = "Amount is too large";
	$lang_hrEmpMain_DirectDebitTransactionTypeShouldBeSelected = "Transaction Type should be selected";

 	$lang_jobtitle_heading 					= "Job : Job Title";
	$lang_jobtitle_jobtitid 				= "Job Title ID";
	$lang_jobtitle_jobtitname 				= "Job Title Name";
	$lang_jobtitle_jobtitdesc 				= "Job Description";
	$lang_jobtitle_jobtitcomments 			= "Job Title Comments";
    $lang_jobtitle_jobspec                  = "Job Specification";
	$lang_jobtitle_addpaygrade 				= "Add Pay Grade";
	$lang_jobtitle_emstatExpl 				= "Define the Employment Status allowed for the Job Title";
	$lang_jobtitle_editpaygrade 			= "Edit Pay Grade";
	$lang_jobtitle_addempstat 				= "Add Employment Status";
	$lang_jobtitle_editempstat 				= "Edit Employment Status";
	$lang_jobtitle_empstat 					= "Employment Status";
	$lang_jobtitle_NameShouldBeSpecified    = "Job Title Name should be specified";
	$lang_jobtitle_DescriptionShouldBeSpecified = "Job Description should be specified";
	$lang_jobtitle_PayGradeNotSelected      = "Pay Grade not selected";
    $lang_jobtitle_ShowingSavedValues       = "Currently showing the values saved in session when you last edited this record.";
    $lang_jobtitle_NoSelection              = "No Selection!";
    $lang_jobtitle_PleaseSelectEmploymentStatus = "Please select employment status";
    $lang_jobtitle_EnterEmploymentStatus    = "Please enter a value for Employment Status.";
 	$lang_jobspec_heading 					= "Job : Job Specifications";
 	$lang_jobspec_id 				        = "ID";
	$lang_jobspec_name 				        = "Name";
	$lang_jobspec_desc 				        = "Description";
	$lang_jobspec_duties 			        = "Duties";
	$lang_jobspec_PleaseSpecifyJobSpecName  = "Please specify Job Specification name";
	$lang_jobspec_NameInUse_Error           = "This name is in use";


 	$lang_languageinformation_heading 		= "Skills : Language";

 	$lang_licenses_heading 					= "Qualification : Licenses";

 	$lang_locations_heading 				= "Company Info : Locations";
 	$lang_locations_NameHasToBeSpecified    = "Location Name has to be specified";
 	$lang_locations_CountryShouldBeSelected = "Country should be selected!";
 	$lang_locations_AddressShouldBeSpecified = "Address should be specified";
 	$lang_locations_ZipCodeShouldBeSpecified = "Zip Code should be specified";
 	$lang_locations_ZipContainsNonNumericChars = "Zip - Code Contains non-numeric characters. Do you want to continue?";
 	$lang_locations_InvalidCharsInPhone = "Phone number contains invalid characters";
 	$lang_locations_InvalidCharsInFax = "Fax contains invalid characters";

 	$lang_membershipinfo_heading 			= "Memberships : Membership";
	$lang_membershipinfo_membershiptype 	= "Membership Type";
	$lang_membershipinfor_selectmember 		= "Select Membership";
	$lang_membershipinfo_MembershipShouldBeSpecified = "Membership Name should be specified";
	$lang_membershipinfo_MembershipTypeShouldBeSelected = "Membership Type should be selected";

 	$lang_membershiptypes_heading 			= "Memberships : Membership Type";
 	$lang_membershiptypes_NameShouldBeSpecified = "Membership Type name should be specified";

 	$lang_nationalityinformation_heading 	= "Nationality & Race : Nationality";
 	$lang_nationalityinformation_PleaseEnterNationality = "Please Enter a Nationality Name";

 	$lang_provinceinformation_heading 		= "State/Province Information : Geo Information";

 	$lang_qualificationtypes_heading 		= "Qualification Types : Qualifications Information";
	$lang_repview_ReportID 					= "Report ID";
	$lang_repview_ReportName 				= "Report Name";
	$lang_repview_ViewEmployeeReports 		= "View Employee Reports";
	$lang_repview_message 					= "Deletion might affect Company Hierarchy";
	$lang_repview_DefineEmployeeReports 	= "Define Employee Reports";
	$lang_repview_message2 					= "Deletion might affect viewing of reports";

 	$lang_routeinformation_heading 			= "Route Information : Route Information";
	$lang_salarygrades_heading 				= "Job : Pay Grade";
	$lang_salarygrades_currAss 				= "Currency Assigned";
    $lang_salarygrades_AssignedCurrencies   = "Assigned currencies";
    $lang_salarygrades_EditAssignedCurrency = "Edit Assigned currency";
    $lang_salarygrades_AssignNewCurrency    = "Assign new currency";
	$lang_salarygrades_stepSal 				= "Step Increase";
	$lang_salarygrades_selectcurrency		= "Currency field should be selected";
	$lang_salarygrades_minSalNumeric		= "Minimum salary should be numeric";
	$lang_salarygrades_maxSalNumeric		= "Maximum salary should be numeric";
	$lang_salarygrades_stepSalNumeric		= "Step salary should be numeric";
    $lang_salarygrades_minGreaterThanMax	= "Minimum Salary > Maximum Salary !";
	$lang_salarygrades_stepPlusMinGreaterThanMax = "Step Salary plus Minimum Salary should be less than Maximum salary";
	$lang_salarygrades_stepGreaterThanMax = "Step Salary should be less than Maximum salary";
	$lang_salarygrades_NameCannotBeBlank = "Pay Grade Name cannot be blank!";

	$lang_Error_salarygrades_DUPLICATE_NAME_FAILURE		= "Failed to add Pay Grades. Cannot have duplicate names";
	$lang_Error_salarygrades_ADD_FAILURE				= "Failed to add Pay Grades";

	$lang_salcurrdet_heading 				= "Currency Assignment to Salary Grade";
	$lang_salcurrdet_assigncurr 			= "Assigned Currency";

 	$lang_satutoryinfo_heading 				= "Statutory Type : Nexus Information";

 	$lang_view_EmploymentStatusID 			= "Employment Status ID";
	$lang_view_EmploymentStatusName 		= "Employment Status Name";
	$lang_view_deletePrompt                 = "Deletion cannot be undone";
	$lang_view_message 						= "Deletion may affect Job Titles";
	$lang_view_message1 					= "Deletion may affect Pay Grade of Employees in PIM";
	$lang_view_SkillName 					= "Skill Name";
	$lang_view_SkillID 						= "Skill ID";
	$lang_view_message3 					= "Deletion might affect employee information";
	$lang_view_LocationName 				= "Location Name";
	$lang_view_message4 					= "Deletion might affect Company Hierarchy. If the Location has associations deletion may fail";
	$lang_view_CurrencyName 				= "Currency Name";
	$lang_view_CurrencyTypes 				= "Currency Types";
	$lang_view_message5 					= "Deletion might affect Salary Currency Detail";
	$lang_view_CompanyHierarchy 			= "Company Hierarchy";
	$lang_view_CompanyHierarchyName 		= "Company Hierarchy Name";
	$lang_view_message6 					= "Deletion might affect employee information";
	$lang_view_QualificationType 			= "Qualification Type";
	$lang_view_QualificationName 			= "Qualification Name";
	$lang_view_message9 					= "Deletion might affect Qualifications, Designation Qualification, Employee Qualification";
	$lang_view_RatingMethod 				= "Rating Method";
	$lang_view_RatingMethodName 			= "Rating Method Name";
	$lang_view_message10 					= "Deletion might affect Qualification, Languages";
	$lang_view_CorporateTitle 				= "Corporate Title";
	$lang_view_CorporateTitleName 			= "Corporate Title name";
	$lang_view_message11 					= "Deletion might affect employee information, Designations";
	$lang_view_ExtraCategory 				= "Extra Curricular Activity Category";
	$lang_view_ExtraCategoryName 			= "Extra Curricular Activity Category Name";
	$lang_view_message12 					= "Deletion might affect Ex. Curr. Activities, Employee Ex. Curr. Activities";
	$lang_view_MembershipTypeName 			= "Membership Type Name";
	$lang_view_message13 					= "Deletion might affect employee memberships, memberships";
	$lang_view_EmployeeCategoryName 		= "Employee Category Name";
	$lang_view_message14 					= "Deletion might affect employee information";
	$lang_view_EmployeeGroupName 			= "Employee Group Name";
	$lang_view_message15 					= "Deletion might affect employee information";
	$lang_view_NationalityName 				= "Nationality Name";
	$lang_view_message16 					= "Deletion might affect employee information";
	$lang_view_CountryID 					= "Country ID";
	$lang_view_CountryName 					= "Country Name";
	$lang_view_message17 					= "Deletion might affect employee information";
	$lang_view_HierarchyDefinition 			= "Hierarchy Definition";
	$lang_view_HierarchyDefinitionName 		= "Hierarchy Definition Name";
	$lang_view_message18 					= "Deletion might affect Company Hierarchy";
	$lang_view_StateProvinceName 			= "State/Province Name";
	$lang_view_message19 					= "Deletion might affect employee information";
	$lang_view_CityName 					= "City Name";
	$lang_view_message20 					= "Deletion might affect employee information";
	$lang_view_LanguagName 					= "Language Name";
	$lang_view_message21 					= "Deletion might affect employee language";
	$lang_view_Membership 					= "Membership";
	$lang_view_MembershipType 				= "Membership Type";
	$lang_view_MembershipName 				= "Membership Name";
	$lang_view_Type 					= "Type";
	$lang_view_message22 					= "Deletion might affect employee membership";
	$lang_view_ExtraActivities 				= "Extra Curricular Activities";
	$lang_view_ExtraActivitiesName 			= "Extra Curricular Activities Name";
	$lang_view_message23 					= "Deletion might affect employee ex. curr. activities";
	$lang_view_PayGradeName 				= "Pay Grade Name";
	$lang_view_message24 					= "Deletion might affect employee information, Job Titles";
	$lang_view_message25 					= "Deletion might affect Education";
	$lang_view_EmployeeTypeID 				= "Employee TypeID";
	$lang_view_EmployeeTypeName 			= "Employee Type Name";
	$lang_view_message26 					= "Deletion might affect employee information";
	$lang_view_EEOJobCategory 				= "EEO Job Category";
	$lang_view_EEOJobCategoryid 			= "EEO Job Category Id";
	$lang_view_EEOJobCategoryName 			= "EEO Job Category Name";
	$lang_view_message27 					= "Deletion might affect employee information";
	$lang_view_message28 					= "Deletion might affect employee language";
	$lang_view_EthnicRace 					= "Ethnic Race";
	$lang_view_EthnicRaceName 				= "Ethnic Race Name";
	$lang_view_message29 					= "Deletion might affect employee records";
	$lang_view_message30 					= "Deletion might affect employee information";
	$lang_view_message31 					= "Deletion might affect employee information, Corporate Titles";
	$lang_view_message32 					= "Deletion might affect Qualifications, Designation Qualification, Employee Qualification";
	$lang_view_License_ID 					= "License ID";
	$lang_view_LicenseDescription 			= "License Description";
	$lang_view_message33 					= "Deletion might affect employee information";
	$lang_view_UserID 						= "User ID";
	$lang_view_UserName 					= "User Name";
	$lang_view_message34 					= "Deletion could make OrangeHRM unusable";
	$lang_view_UserGroupID 					= "User Group ID";
	$lang_view_UserGroupName 				= "User Group Name";
	$lang_view_message35 					= "Deletion could make OrangeHRM unusable";
        $lang_view_DeleteUserGroup                              = "Users belonging to the group will lose their privileges";

	$lang_view_Users 				= "Users";
	$lang_view_UserGroups 			= "Admin User Groups";
	$lang_view_HRAdmin 				= "HR Admin";
	$lang_view_ESS 					= "ESS";
	$lang_view_ID 					= "ID";

	//Customer
	$lang_view_CustomerId = "Customer Id";
	$lang_view_CustomerName = "Customer Name";
	$lang_customer_Heading          = "Customer";

	$lang_index_WelcomeMes 			= "Welcome #username";
	$lang_index_ChangePassword 		= "Change Password";
	$lang_index_Logout 				= "Logout";

	$lang_MailFrom = "Mail Sent As";
	$lang_MailSendingMethod = "Sending Method";
	$lang_MailTypes_Mail = "Internal PHP function";
	$lang_MailTypes_Sendmailer = "sendmail";
	$lang_MailTypes_Smtp = "SMTP";
	$lang_SendmailPath = "Path to Sendmail";
	$lang_EmailAuthentication = "Use SMTP Authentication";
	$lang_SmtpHost = "SMTP Host";
	$lang_SmtpPort = "SMTP Port";
	$lang_SmtpUser = "SMTP User";
	$lang_SmtpPassword = "SMTP Password";
	$lang_EmailSecurity = "Use Secure Connection";
	$lang_Email_SSL = "SSL";
	$lang_Email_TLS = "TLS";
	$lang_SmtpSendTestEmail = "Send Test Email";
	$lang_SmptTestEmailAddress = "Test Email Address";
	$lang_Email_SendMail_Instructions = "Please set correct path to sendmail by changing sendmail_path in php.ini";

    $lang_SmtpPathEditDisabled = "Editing of SMTP Path is disabled. Please edit lib/confs/mailConf.php directly to edit.";
    $lang_SmtpPathEditHowToEnable = "Enabling editing of SMTP path via a browser is not recommended due to security issues, but can be done by editing lib/confs/sysConf.php";

    $lang_SmtpPathEditEnabledLocalhost = "WARNING! Editing of SMTP Path is enabled for localhost.";
    $lang_SmtpPathEditEnabled = "WARNING! Editing of SMTP Path is enabled. This is not secure.";
    $lang_SmtpPathHowtoDisable = "Please disable once configuration is complete by editing lib/confs/sysConf.php";

	// Email Configuration Validation
	$lang_Error_FromEmailEmpty = "'Mail Sent As' cannot be empty. Enter a valid email address";
	$lang_Error_FromEmailInvalid = "'Mail Sent As' should be a valid email address";
	$lang_Error_SmtpHostEmpty = "'SMTP Host' cannot be empty";
	$lang_Error_SmtpPortEmpty = "'SMTP Port' cannot be empty";
	$lang_Error_Invalid_Port = "Invalid SMTP Port";
	$lang_Error_SmtpUsernameEmpty = "'SMTP User' cannot be empty";
	$lang_Error_SmtpPasswordEmpty = "'SMTP Password' cannot be empty";
	$lang_Error_TestEmailEmpty = "Test Email Address is empty";
	$lang_Error_TestEmailValid = "Test Email Address is invalid";

	//Projects
	$lang_view_Project_Heading = "Project";
	$lang_view_ProjectId = "Project Id";
	$lang_view_ProjectName = "Project Name";

	// Data Export
	$lang_DataExport_Title = "Export HR Data";
	$lang_DataExport_Type = "Export Type";
	$lang_DataExport_ExportTypeNotSelected = "Export Type not selected";
	$lang_DataExport_Export = "Export";
	$lang_DataExport_PluginsAreMarked = "Export Types marked with (+) are defined in Plugin files and are not editable via the UI.";
	$lang_DataExport_CustomExportTypesCanBeManaged = "Custom export types can be managed ";
	$lang_DataExport_ClickingHereLink = "here";

	// Define Custom Data Export
	$lang_DataExport_CustomExportId = "ID";
	$lang_DataExport_CustomExportName = "Export Name";
	$lang_DataExportCustom_Title = "Custom Export Definitions";
	$lang_DataExport_DefineCustomField_Heading = "Define Custom Export";
	$lang_DataExport_AssignedFields = "Assigned Fields";
	$lang_DataExport_AvailableFields = "Available Fields";
	$lang_DataExport_Add = "Add";
	$lang_DataExport_Remove = "Remove";
	$lang_DataExport_PleaseSpecifyExportName = "Please specify export name";
	$lang_DataExport_Error_NoFieldSelected = "No Field Selected";
	$lang_DataExport_Error_AssignAtLeastOneField = "Please assign at least one field";
	$lang_DataExport_Error_NameInUse = "This name is in use.";
	$lang_DataExport_Error_NoFieldSelectedForMove = "Please select at least one field from the Assigned fields to move";
	$lang_DataExport_MoveUp = "Move selected fields up";
	$lang_DataExport_MoveDown = "Move selected fields down";

	// Data Import
	$lang_DataImport_Title = "Import HR Data";
	$lang_DataImport_Type = "Import Type";
	$lang_DataImport_CSVFile = "CSV File";
	$lang_DataImport_ImportTypeNotSelected = "Import Type not selected";
	$lang_DataImport_Import = "Import";
	$lang_DataImport_PluginsAreMarked = "Import Types marked with (+) are defined in Plugin files and are not editable via the UI.";
	$lang_DataImport_CustomImportTypesCanBeManaged = "Custom import types can be managed ";
	$lang_DataImport_ClickingHereLink = "here";
	$lang_DataImport_Error_PleaseSelectFile = "Please select a CSV File";
	$lang_DataImport_Error_Invalid_File = "CSV File - Invalid file type";

	// Data Import Status
	$lang_DataImportStatus_Title = "Data Import Status";
	$lang_DataImportStatus_ContinuingDataImport = "Upload Successful. Continuing with Data Import";
	$lang_DataImportStatus_Summary = "Summary";
	$lang_DataImportStatus_Details = "Details of failed rows";
	$lang_DataImportStatus_NothingImported = "No rows were imported";
	$lang_DataImportStatus_ImportSuccess = "Import successful";
	$lang_DataImportStatus_ImportFailed = "Import failed, no rows imported";
	$lang_DataImportStatus_ImportSomeFailed = "Some rows failed to import";
	$lang_DataImportStatus_NumImported = "No. of rows imported";
	$lang_DataImportStatus_NumFailed = "No. of rows which failed to import";
	$lang_DataImportStatus_NumSkipped = "No. of header rows skipped";
	$lang_DataImportStatus_TimeRemainingSeconds = "seconds";
	$lang_DataImportStatus_FinalResult = "Final result";
	$lang_DataImportStatus_ImportInProgress = "Import in progress...";
	$lang_DataImportStatus_ImportCompleted = "Import completed";
	$lang_DataImportStatus_Progress = "Progress";
	$lang_DataImportStatus_ETA = "ETA";
	$lang_DataImportStatus_Heading_Row = "Row";
	$lang_DataImportStatus_Heading_Error = "Error";
	$lang_DataImportStatus_Heading_Comments = "Comments";

	$lang_DataImportStatus_Error_IMPORT_ERROR = "Import error ";
	$lang_DataImportStatus_Error_INCORRECT_COLUMN_NUMBER = "Incorrect number of columns";
	$lang_DataImportStatus_Error_MISSING_WORKSTATION = "Workstation not found";
	$lang_DataImportStatus_Error_COMPULSARY_FIELDS_MISSING_DATA = "Compulsary fields missing in data";
	$lang_DataImportStatus_Error_DD_DATA_INCOMPLETE = "Direct Deposit data is not complete";
	$lang_DataImportStatus_Error_INVALID_TYPE = "Invalid field data type";
	$lang_DataImportStatus_Error_DUPLICATE_EMPLOYEE_ID = "Employee ID is in use";
	$lang_DataImportStatus_Error_DUPLICATE_EMPLOYEE_NAME = "Employee with same name exists";
	$lang_DataImportStatus_Error_FIELD_TOO_LONG = "Field too long";

	// Define Custom Data Import
	$lang_DataImport_CustomImportId = "ID";
	$lang_DataImport_CustomImportName = "Import Name";
	$lang_DataImportCustom_Title = "Custom Import Definitions";
	$lang_DataImport_DefineCustomField_Heading = "Define Custom Import";
	$lang_DataImport_ContainsHeader = "Contains Header";
	$lang_DataImport_ContainsHeaderDescription = "If selected, OrangeHRM will skip first line of CSV file";
	$lang_DataImport_AssignedFields = "Assigned Fields";
	$lang_DataImport_AvailableFields = "Available Fields";
	$lang_DataImport_Add = "Add";
	$lang_DataImport_Remove = "Remove";
	$lang_DataImport_PleaseSpecifyImportName = "Please specify import name";
	$lang_DataImport_Error_NoFieldSelected = "No Field Selected";
	$lang_DataImport_Error_AssignAtLeastOneField = "Please assign at least one field";
	$lang_DataImport_Error_NameInUse = "This name is in use.";
	$lang_DataImport_Error_NoFieldSelectedForMove = "Please select at least one field from the Assigned fields to move";
	$lang_DataImport_MoveUp = "Move selected fields up";
	$lang_DataImport_MoveDown = "Move selected fields down";
	$lang_DataImport_Error_AssignCompulsaryFields = "The following compulsary fields have not been assigned";
	$lang_DataImport_CompulsaryFields = "The following fields are compulsary and must be assigned";
	$lang_DataImport_Error_CantRemoveCompulsaryFields = "The following fields are compulsary and cannot be removed";

	// Define custom data export CSV headings
	$lang_DataExport_DefineCustomFieldHeadings_Heading = "Define CSV Heading for Custom Export";
	$lang_DataExport_ColumnHeadings = "CSV column headings";
	$lang_DataExport_EditColumnHeadings = "Edit column headings if needed.";
	$lang_DataExport_Error_AllHeadingsMustBeSpecified = "All column headings must be specified";
	$lang_DataExport_Error_CommaNotAllowedInHeadings = "The comma (,) character is not allowed in headings";

	// Custom Fields
	$lang_CustomFields_Title = "Custom Fields";
	$lang_CustomFields_CustomFieldId = "Custom Field Id";
	$lang_CustomFields_CustomFieldName = "Custom Field Name";
	$lang_CustomFields_CustomFieldNumber = "Field Number";
	$lang_customeFields_Heading = "Custom Fields";
	$lang_customeFields_FieldName = "Field Name";
	$lang_customeFields_Type = "Type";
	$lang_customeFields_StringType = "String";
	$lang_customeFields_SelectType = "Drop Down";
	$lang_customeFields_SelectOptions = "Select Options";
	$lang_Admin_CustomeFields_PleaseSpecifyCustomFieldName = "Please specify custom field name";
	$lang_Admin_CustomeFields_PleaseSpecifySelectOptions = "Please specify select options";
	$lang_Admin_CustomeFields_SelectOptionsHint = "Enter allowed options separated by commas";
	$lang_Admin_CustomeFields_MaxCustomFieldsCreated = "The maximum number of custom fields have been created.";
	$lang_pim_CustomFields_NoCustomFieldsDefined = "No custom fields defined.";

	// PIM tab labels
	$lang_pim_PersonalDetails = 'Personal Details';
	$lang_pim_Employment = 'Employment';
	$lang_pim_Qualifications = 'Qualifications';
	$lang_pim_EmployeeList = 'Employee List';
	$lang_pim_AddEmployee = 'Add Employee';
    $lang_pim_Other = 'Other';

	$lang_pim_tabs_Personal = "Personal";
	$lang_pim_tabs_Contact = "Contact Details";
	$lang_pim_tabs_EmergencyContacts = "Emergency Contact(s)";
	$lang_pim_tabs_Dependents = "Dependents";
	$lang_pim_tabs_Immigration = "Immigration";
	$lang_pim_tabs_Job = "Job";
	$lang_pim_tabs_Payments = "Salary";
	$lang_pim_tabs_ReportTo = "Report-to";
	$lang_pim_tabs_WorkExperience = "Work experience";
	$lang_pim_tabs_Education = "Education";
	$lang_rep_Languages = "Languages";
	$lang_pim_tabs_Skills = "Skills";
	$lang_pim_tabs_Languages = "Languages";
	$lang_pim_tabs_License = "License";
	$lang_pim_tabs_Membership = "Membership";
	$lang_pim_tabs_Attachments = "Attachments";
	$lang_pim_tabs_Tax = "Tax Exemptions";
	$lang_pim_tabs_DirectDebit = "Direct Deposit";
	$lang_pim_tabs_Custom = "Custom";
	$lang_pim_tabs_Photo = "Photograph";

	$lang_pim_ClickToEditPhoto = "Click to edit photo";

	// Report module
	$lang_rep_SelectionCriteria = "Selection Criteria";
	$lang_rep_EmpNo = "Emp No";
	$lang_rep_Employee = "Employee";
	$lang_rep_AgeGroup = "Age Group";
	$lang_rep_DateBirth = "Date Of Birth";
	$lang_rep_PayGrade = "Pay Grade";
	$lang_rep_Education = "Education";
	$lang_rep_EmploymentStatus = "Employment Status";
	$lang_rep_ServicePeriod = "Service Period";
	$lang_rep_JoinedDate = "Joined Date";
	$lang_rep_JobTitle = "Job Title";
	$lang_rep_Language = "Language";
	$lang_rep_Skill = "Skill";

	$lang_rep_LastName = "Last Name";
	$lang_rep_FirstName = "First Name";
	$lang_rep_Address = "Address";
	$lang_rep_TelNo = "Tel No";
	$lang_rep_DateOfBirth = "Date Of Birth";
	$lang_rep_JoinDate = "Join Date";
	$lang_rep_Qualification = "Qualification";
	$lang_rep_EmployeeStates = "Employee States";
	$lang_rep_JoinedDate = "Joined Date";

	$lang_rep_SelectPayGrade = "Select Salary Grade";
	$lang_rep_SelectEducation = "Select Education Type";
	$lang_rep_SelectEmploymentType = "Select Employment Type";
	$lang_rep_SelectComparison = "Select Comparison";
	$lang_rep_SelectJobTitle = "Select Job Title";
	$lang_rep_SelectLanguage = "Select Language";
	$lang_rep_SelectSkill = "Select Skill";

	$lang_rep_Field = "Field";
	$lang_rep_AssignUserGroups = "Assign User Groups";
	$lang_rep_AssignedUserGroups = "Assigned User Groups";

	$lang_rep_SelectAtLeastOneCriteriaAndOneField = "Select at least one criteria and one field";
	$lang_rep_SelectTheComparison = "Select The Comparison";

	$lang_rep_AgeShouldBeNumeric = "Age Should Be Numeric";
	$lang_rep_InvalidAgeRange = "2nd Selected Age Lager Than The 1st Selected Age";

	$lang_rep_FieldNotSelected = "Field Not Selected";

	$lang_rep_DateShouldBeNumeric = "Date Should Be Numeric";
	$lang_rep_ValueShouldBeinYears = "Value Should Be in Years";
	$lang_rep_InvalidRange = "Invalid Range";

	$lang_rep_Report = "Report";
	$lang_rep_EmployeeNo = "Employee No";
	$lang_rep_EmployeeFirstName = "Employee First Name";
	$lang_rep_EmployeeLastName = "Employee Last Name";
	$lang_rep_ReportTo = "Report to";
	$lang_rep_ReportingMethod = "Reporting Method";
	$lang_rep_Address = "Address";
	$lang_rep_Telephone = "Telephone";
	$lang_rep_DateOfBirth = "Date of Birth";
	$lang_rep_Skills = "Skills";
	$lang_rep_SalaryGrade = "Salary Grade";
	$lang_rep_EmployeeStatus = "Employee Status";
	$lang_rep_JoinedDate = "Joined Date";
	$lang_rep_SubDivision = "Sub Division";
	$lang_rep_JobTitle = "Job Title";
	$lang_rep_YearOfPassing = "Year of Passing";
	$lang_rep_Contract = "Contract";
	$lang_rep_WorkExperience = "Work Experience";

	$lang_rep_SelectAtLeaseOneUserGroupToAssign = "Select at least one User Group check box to assign";
	$lang_rep_SelectAtLeaseOneUserGroupToDelete = "Select at least one User Group check box to delete";

	$lang_rep_ReportDefinition = "Report Definition";
	$lang_rep_AssignUserGroups = "Assign User Groups";
	$lang_rep_UserGroups = "User Groups";
	$lang_rep_UserGroup = "User Group";
	$lang_rep_NoUserGroupsAssigned = "No User Groups Assigned";
	$lang_rep_SelectUserGroup = "Select User Group";
	$lang_rep_NoGroupSelected = "Please select a user group to assign";
	$lang_rep_ReportNameEmpty = "Report Name Empty";

	$lang_rep_Error_DUPLICATE_NAME_ADDED = "Report name already exists.";
	$lang_rep_Error_ADD_FAILURE = "Report could not be added.";
	$lang_rep_Error_UPDATED_TO_DUPLICATE_NAME = "Changes to the report could not be updated. Report name already exists.";
	$lang_rep_Error_UPDATE_FAILURE = "Changes to the report could not be updated";

	// Skills
	$lang_Admin_Skill_Errors_NameCannotBeBlank = "Name cannot be a blank value!";


	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "Mail Configuration";
	$lang_Admin_SubscribeToMailNotifications = "Subscribe to E-mail Notifications";
	$lang_Admin_ENS_LeaveApplications = "Leave Applications";
	$lang_Admin_ENS_LeaveApprovals = "Leave Approvals";
	$lang_Admin_ENS_LeaveCancellations = "Leave Cancellations";
	$lang_Admin_ENS_LeaveRejections = "Leave Rejections";
	$lang_Admin_ENS_JobApplications = "Job Applications";
	$lang_Admin_ENS_HspNotifications = "HSP Notifications";
    $lang_Admin_ENS_SeekHireApproval = "New employee hire approval requests";
    $lang_Admin_ENS_HiringTasks = "Tasks sent on hiring of employee";
    $lang_Admin_ENS_HiringApproved = "Notifications of hiring new employees";

	//Users
	$lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong = "UserName should be at least five characters long";
	$lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong = "Password should be at least four characters long";
	$lang_Admin_Users_ErrorsPasswordMismatch = "Password Mismatch";
	$lang_Admin_Users_Errors_EmployeeIdShouldBeDefined = "Employee ID should be defined";
	$lang_Admin_Users_Errors_FieldShouldBeSelected = "Field should be selected";
	$lang_Admin_Users_Errors_ModuleShouldBeSelected = "Please select a module to assign the rights";
	$lang_Admin_Users_Errors_ViewShouldBeSelected = "View should be selected";
	$lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Passwords Are Not Matching.Retype Your New Password";
	$lang_Admin_Users_Errors_SpecialCharacters = "The username should not contain special characters";
	$lang_Admin_Users_WrongPassword = "Mismatch with your old password";
    $lang_Admin_Users_Errors_AdminUserGroupShouldBeSelected = "Admin User Group should be selected";

	$lang_Admin_Users_Errors_DoYouWantToClearRights = "Do you want to clear Rights";
	$lang_Admin_Users_Errors_SameGroup = "Your login account belongs to the same User Group, you are not allowed change rights to this group";
	$lang_Admin_Users_Errors_NameCannotBeBlank = "Name cannot be blank";

	$lang_Admin_Users_Modules = "Modules";
	$lang_Admin_Users_Module = "Module";
	$lang_Admin_Users_RightsAssignedToUserGroups = "Rights Assigned to User Groups";
	$lang_Admin_Users_UserGroup = "User Group";
	$lang_Admin_Users_UserGroupId = "User Group ID";
	$lang_Admin_Users_SelectModule = "Select Module";

	$lang_Admin_Users_UserName = "User Name";
	$lang_Admin_Users_Password = "Password";
	$lang_Admin_Users_Employee = "Employee";

	$lang_Admin_Users_ConfirmPassword = "Confirm Password";

	$lang_Admin_Users_Status = "Status";
	$lang_Admin_Users_Enabled = "Enabled";
	$lang_Admin_Users_Disabled = "Disabled";

	$lang_Admin_Users_UserGroup = "Admin User Group";
	$lang_Admin_Users_SelectUserGroup = "Select User Group";

	$lang_Admin_Users_NewPassword = "New Password";
	$lang_Admin_Users_ConfirmNewPassword = "Confirm New Password";

	$lang_Admin_Users_add = "Add";
	$lang_Admin_Users_delete = "Delete";
	$lang_Admin_Users_view = "View";
	$lang_Admin_Users_edit = "Edit";

	$lang_Admin_Users_AssignedRights = "Assigned Rights";
	$lang_Admin_Users_DefineReports = "Define Reports";
	$lang_Admin_Users_Assign_User_Rights = "Assign User Rights";
	$lang_Admin_Users_View_User_Rights = "View User Rights";

	$lang_Admin_Change_Password_OldPassword = "Old Password";

	$lang_Admin_Change_Password_Errors_EnterYourOldPassword = "Enter Your Old Password";
	$lang_Admin_Change_Password_Errors_EnterYourNewPassword = "Enter Your New Password";
	$lang_Admin_Change_Password_Errors_RetypeYourNewPassword = "Retype Your New Password";
	$lang_Admin_Change_Password_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Passwords Are Not Matching.Retype Your New Password";
	$lang_Admin_Change_Password_Errors_YourOldNewPasswordsAreEqual = "Your Old and New Passwords Are Equal";

	$lang_Admin_Project = "Project";
	$lang_Admin_Project_CutomerName = "Customer Name";
	$lang_Admin_Project_SelectCutomer = "Select customer";
	$lang_Admin_Project_Administrators = "Project Administrators";
	$lang_Admin_Project_EmployeeName = "Employee Name";
	$lang_Admin_Project_EmployeeAlreadyAnAdmin = "Selected employee is already a project admin for this project.";

	$lang_Admin_Project_Error_PleaseDSpecifyTheProjectId = "Please specify the Project Id";
	$lang_Admin_Project_Error_PleaseSelectACustomer = "Please select a customer";
	$lang_Admin_Project_Error_PleaseSpecifyTheName = "Please specify the name";

	$lang_Admin_Customer_PleaseSpecifyTheCustormerId = "Please specify the customer Id";
	$lang_Admin_Customer_Error_PleaseSpecifyTheName = "Please specify the name";

	$lang_Admin_ProjectActivities = "Project Activities";
    $lang_Admin_AssignedActivities = "Assigned Activities";
	$lang_Admin_Activity = "Activity";
	$lang_Admin_Project_Activity_ClickOnActivityToEdit = "Click on an existing activity to rename";
	$lang_Admin_Project_Activity_Error_PleaseSpecifyTheActivityName = "Please specify the activity name";
	$lang_Admin_Project_Error_NoProjects = "No projects found.";
	$lang_Admin_Project_NoActivitiesDefined = "No Activities defined.";
	$lang_Admin_Project_Activity_Error_NameAlreadyDefined = "An Activity with that name already exists in this project. Please select a different name.";

    //Company Property
    $lang_Admin_Company_Property = "Company Property";
    $lang_Admin_Company_Property_Title = "Company Info: Company Property";
    $lang_Admin_Property_Name = "Property Name";
    $lang_Admin_Prop_Emp_Name = "Employee";
    $lang_Admin_Company_Property_Err_Del_Not_Sel = "Please select a property to delete!";
    $lang_Admin_Company_Property_Err_Name_Empty = "Property name is empty!";
    $lang_Admin_Company_Property_Err_Name_Exists = "Property name is already exists!";
    $lang_Admin_Company_Property_Warn_Delete = "Do you want to delete selected items?";
    $lang_Admin_Property_Please_Select = "Not Assigned";

	//timesheet
	$lang_Time_Module_Title = "OrangeHRM - Time and Attendance Module";
	$lang_Time_Timesheet_TimesheetForViewTitle = "Timesheet for #periodName starting #startDate";
	$lang_Time_Timesheet_TimesheetForEditTitle = "Edit timesheet for #periodName starting #startDate";

	$lang_Time_Timesheet_TimesheetNameForViewTitle = "Timesheet for #name for #periodName starting #startDate";
	$lang_Time_Timesheet_TimesheetNameForEditTitle = "Edit timesheet for #name for #periodName starting #startDate";
	$lang_Time_Timesheet_EditTimesheetForWeekStarting = "Edit timesheet for week starting";
	$lang_Time_Timesheet_DeletedProjectsAndActivitiesNotice = "Deleted projects/activities are marked with an asterik %s. Click on a project/activity to edit.";

	$lang_Time_Timesheet_Status = "Status: #status";

	$lang_Time_Timesheet_Status_NotSubmitted = "Not submitted";
	$lang_Time_Timesheet_Status_Submitted = "Submitted";
	$lang_Time_Timesheet_Status_Approved = "Approved";
	$lang_Time_Timesheet_Status_Rejected = "Rejected";

	$lang_Time_Timesheet_Customer = "Customer";
	$lang_Time_Timesheet_ProjectActivity = "Project / Activity";
	$lang_Time_Timesheet_Project = "Project";
	$lang_Time_Timesheet_Activity = "Activity";
        $lang_Time_Timesheet_Project_Deleted = "Show Deleted";
	$lang_Time_Timesheet_Total = "Total";
	$lang_Time_TimeFormat = "Time Format";
	$lang_Time_Timesheet_ApprovedReport = "Employee Time Report for Approved Timesheets";

	$lang_Time_Errors_SUBMIT_SUCCESS = "Submit succeeded";
	$lang_Time_Errors_SUBMIT_FAILURE = "Submit failed";
	$lang_Time_Errors_UPDATE_SUCCESS = "Successfully updated";
	$lang_Time_Errors_UPDATE_FAILURE = "Failed to update";
	$lang_Time_Errors_CANCEL_SUCCESS = "Successfully cancelled";
	$lang_Time_Errors_CANCEL_FAILURE = "Failed to cancel";
	$lang_Time_Errors_APPROVE_SUCCESS = "Successfully approved";
	$lang_Time_Errors_APPROVE_FAILURE = "Failed to approve";
	$lang_Time_Errors_REJECT_SUCCESS = "Successfully rejected";
	$lang_Time_Errors_REJECT_FAILURE = "Failed to reject";
	$lang_Time_Errors_DELETE_SUCCESS = "Successfully deleted";
	$lang_Time_Errors_DELETE_FAILURE = "Failed to delete";
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "Unauthorized action";
	$lang_Time_Errors_APPROVED_TIMESHEET_FAILURE = "Time event can not be added to an approved timesheet";
	$lang_Time_Errors_REJECTED_TIMESHEET_FAILURE = "Time event can not be added to a rejected timesheet";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "No timesheet found";
	$lang_Time_Errors_INVALID_TIME_FAILURE = "Invalid time specified";
	$lang_Time_Errors_INVALID_DURATION_FAILURE = "Invalid duration specified";
	$lang_Time_Errors_EVENT_START_AFTER_END_ERROR = "Event start time after end time.";
	$lang_Time_Errors_INVALID_TIMESHEET_PERIOD_ERROR = "Invalid timesheet period.";
	$lang_Time_Errors_UNFINISHED_TIMESHEET_FAILURE = "Timesheet contains unfinished activities";
	$lang_Time_Errors_DUPLICATE_ROWS = "Duplicate rows found";
	$lang_Time_Errors_NO_PROJECT_SELECTED = "Please select a project";
	$lang_Time_Errors_NO_ACTIVITY_SELECTED = "Please select an activity";
	$lang_Time_Errors_MaxTotalDuration = 'Total duration of a day cannot exceed 24 hours';

	$lang_Time_Errors_EncounteredTheFollowingProblems = "Encountered the following problems. Rows with errors are highlighted.";
	$lang_Time_Errors_EncounteredFollowingProblems = "Encountered following problems";
	$lang_Time_Errors_ReportedDateNotSpecified_ERROR = "Reported date not specified";
	$lang_Time_Errors_ProjectNotSpecified_ERROR = "Project not specified";
	$lang_Time_Errors_CustomerNotSpecified_ERROR = "Customer not specified";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified_ERROR = "Invalid time or zero or negative interval specified";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval_ERROR = "Cannot specify both an interval and a duration";
	$lang_Time_Errors_InvalidReportedDate_ERROR = "Invalid value for Reported date.";
	$lang_Time_Errors_InvalidDuration_ERROR = "Duration should be a valid positive number";
	$lang_Time_Errors_InvalidStartTime_ERROR = "Invalid time value for start time";
	$lang_Time_Errors_InvalidEndTime_ERROR = "Invalid time value for end time";
	$lang_Time_Errors_EVENT_OUTSIDE_PERIOD_FAILURE = "Event time must be within the timesheet period.";
	$lang_Time_Errors_NoValidDurationOrInterval_ERROR = "No valid duration or interval specified";
	$lang_Time_Errors_ZeroOrNegativeIntervalSpecified_ERROR = "Zero or negative interval specified";
	$lang_Time_Errors_NO_EVENTS_WARNING = "No events to save";

	$lang_Time_Timesheet_StartTime = "Start time";
	$lang_Time_Timesheet_EndTime = "End time";
	$lang_Time_Timesheet_ReportedDate = "Reported date";
	$lang_Time_Timesheet_Duration = "Duration";
	$lang_Time_Timesheet_DurationUnits = "(hours)";
	$lang_Time_Timesheet_Decription = "Description";
	$lang_Time_Timesheet_NoCustomers = "No customers";
	$lang_Time_Timesheet_NoProjects = "No projects";
	$lang_Time_Timesheet_IncludeDeleteProjects = "Include deleted projects";

	$lang_Time_Select_Employee_Title = "Select Employee";

	$lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval = "Submitted timesheets pending supervisor approval";
	$lang_Time_Select_Employee_WeekStartingDate = "Week starting #date";

	$lang_Time_Select_Employee_TimesheetPeriod = "Timesheet period";

	$lang_Time_Errors_PleaseAddAComment = "Please add a comment";

	$lang_mtview_ADD_SUCCESS 				= "Successfully Added";
	$lang_mtview_UPDATE_SUCCESS 			= "Successfully Updated";
	$lang_mtview_DELETE_SUCCESS 			= "Successfully Deleted";
	$lang_mtview_ADD_FAILURE 				= "Failed to Add";
	$lang_mtview_UPDATE_FAILURE 			= "Failed to Update";
	$lang_mtview_DELETE_FAILURE 			= "Failed to Delete";

	// Attendance
	$lang_Time_Menu_Attendacne = "Attendance";
	$lang_Time_Menu_PunchInOut = "Punch In/Out";
	$lang_Time_Heading_PunchIn = "Punch In";
	$lang_Time_Heading_PunchOut = "Punch Out";
	$lang_Time_LastPunchIn = "Last punched in was at";
	$lang_Time_Menu_AttendanceConfiguration = "Configuration";
	$lang_Time_AttendanceConfiguration = "Attendance Configuration";
	$lang_Time_EmpChangeTime = "Employee can change displayed current time when he punches in/out";
	$lang_Time_EmpEditSubmitted = "Employee can edit submitted attendance records";
	$lang_Time_SupEditSubmitted = "Supervisor can edit submitted attendance records of subordinates";
	$lang_Time_AttendanceConfigSaving_SUCCESS = "Attendance configuration saved sucessfully";
	$lang_Time_AttendanceConfigSaving_FAILURE = "Failed saving attendance configuration";
	$lang_Time_PunchSaving_SUCCESS = "Record saved";
	$lang_Time_PunchSaving_FAILURE = "Saving record failed";
	$lang_Time_Errors_InvalidMaxTime = "Invalid time entered. Maximum acceptable time is 23:59";

	$lang_Time_Menu_MyReports = "My Reports";
	$lang_Time_Menu_EmployeeReports = "Employee Reports";
	$lang_Time_Heading_Attendance_Report = "Attendance Report";
	$lang_Time_ReportType = "Report Type";
	$lang_Time_Option_Summary = "Summary";
	$lang_time_Option_Detailed = "Detailed";
	$lang_Time_Button_Generate = "Generate";
	$lang_Time_In = "In";
	$lang_Time_Out = "Out";

	$lang_Time_Attendance_ReportSavingSuccess = "Attendance report saved successfully";
	$lang_Time_Attendance_ReportSavingFailure = "Saving attendace report failed";
	$lang_Time_Attendance_ReportNoChange = "No changes to save";
	$lang_Time_Attendance_Overlapping = "Overlapping records found";
	$lang_Time_Attendance_NoReports = "No reports to display";
	$lang_Time_Attendance_InvalidOutTime = "Punch Out time should be greater than Punch In time";
	$lang_Time_Attendance_EnterValidDates = "Please enter valid dates for To Date and From Date";
	$lang_Time_Attendance_EnterValidDateRange = "Please enter a valid date range (From Date must be before To Date)";
	$lang_Time_Attendance_NoteTooLong = "Note is too long";

	// 2.2 Time module
	$lang_Time_PunchInPunchOutTitle = "Punch In/Out";

	$lang_Time_Errors_InvalidDateOrTime = "Invalid date or time";
	$lang_Time_Errors_CurrentPunchTimeBeforeLastPunchTime = "Punch time cannot be before last time punched";
	$lang_Time_Errors_ZeroOrNegativeDurationTimeEventsAreNotAllowed = "Zero or negative duratons are not allowed";
	$lang_Time_Errors_ActivityNotSpecified_ERROR = "Activity must be specified";

	$lang_Menu_Time_PunchInOut = "Punch In/Out";

	$lang_Time_Timesheet_SelectProject = "Select a Project";
	$lang_Time_Timesheet_SelectProjectFirst = "Select a Project First";
	$lang_Time_Timesheet_SelectActivity = "Select an Activity";

	$lang_Time_Timesheet_UnfinishedActivitiesTitle = "Unfinished Activities";
	$lang_Time_SubmitTimeEventTitle = "Submit Time Event";

	$lang_Time_Timesheet_DateReportedFor = "Date reported for";

	$lang_Time_UnfinishedActivitiesTitle = "Unfinished Activities";
	$lang_Time_Add_NewEvent = "Add New Time Event";
	$lang_Time_NewEvent = "New Event";
	$lang_Time_Complete = "Complete";

	$lang_Time_DurationFormat = "hh:mm or 0.00h";
	$lang_Time_InsertTime="Insert time";

	$lang_Time_Errors_NO_RECORDS_CHANGED_WARNING = "No changes to save";
	$lang_Time_Errors_EXCEPTION_THROWN_WARNING = "Encountered a problem";

	$lang_Menu_Time_ProjectTime = "Project time";

	$lang_Time_DefineTimesheetPeriodTitle = "Define Timesheet Period";

	$lang_Time_FirstDayOfWeek = "First Day of Week";

	$lang_Menu_Time_DefineTimesheetPeriod = "Define Timesheet Period";

	$lang_Time_EmployeeTimeReportTitle = "Employee Time Report";

	$lang_Time_Common_FromDate = $lang_Leave_Common_FromDate;
	$lang_Time_Common_ToDate = $lang_Leave_Common_ToDate;
	$lang_Time_Common_All = $lang_Leave_Common_All;

	$lang_Time_Errors_EmployeeNotSpecified = "Employee not specified";

	$lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified = "Invalid date or zero or negative range specified";

	$lang_Time_Warning = 'Warning';
	$lang_Time_Warning_FieldsWereReset = 'Fields were reset due to following errors';
	$lang_Time_Warning_NoMatchingEmployeeFound = 'No matching employee found for the name #employeeName.';
	$lang_Time_Warning_NoMatchingSupervisorFound = 'No matching supervisor found for the name #supervisorName.';

	$lang_Menu_Time_EmployeeReports = "Employee Reports";

	$lang_Time_SelectTimesheetsTitle = "Select Timesheets";
	$lang_Time_Division = $lang_compstruct_Division;
	$lang_Time_Supervisor = $lang_empview_Supervisor;
	$lang_Time_EmploymentStatus = $lang_empview_EmploymentStatus;

	$lang_Time_NoEmploymentStatusDefined = "No employment status defined";

	$lang_Time_SelectWeekStartDay = "Please select week start day";
	$lang_Time_ContactAdminForTimesheetPeriodSet = "Timesheet period start day has not been defiend. Please contact HR Admin";
	$lang_Time_ContactAdminForTimesheetPeriodSetComplete = "Timesheet period start day has been set";
	$lang_Time_ProceedWithTimeModule = "Proceed with Time Module";

	$lang_Time_PrintTimesheetsTitle = "Print Timesheets";
	$lang_Time_Print = "Print";

	$lang_Common_Loading = "Loading";
	$lang_Common_Select = "Select";

	$lang_Menu_Time_PrintTimesheets = "Print Timesheets";

	$lang_Menu_Time_ProjectReports = "Project Reports";
	$lang_Time_ProjectReportTitle = "Project Reports";
	$lang_Time_Report_To = "To";
	$lang_Time_Report_From = "From";
	$lang_Time_TimeInHours = "Time (hours)";
	$lang_Time_ActivityReportTitle = "Activity Details";
	$lang_Time_Activity_Report_EmployeeName = "Employee Name";
	$lang_Time_Activity_Report_View = "View";
	$lang_Time_Activity_Report_TotalTime = "Total Time";
	$lang_Time_Activity_Report_NoEvents = "No events found.";

	$lang_Time_Errors_PROJECT_NOT_FOUND_FAILURE = "Project not found.";
	$lang_Time_Errors_ACTIVITY_NOT_FOUND_FAILURE = "Activity not found.";
	
	$lang_Time_TimeGrid_Hide_Comments = "Hide Comments";
	$lang_Time_TimeGrid_ViewOrHide_Comments = "View/Hide Comments";
	$lang_Time_TimeGrid_RemoveRow_Warning = "You have chosen to remove the row(s) permanently.\n\tPress OK to continue.";
	$lang_Time_TimeGrid_NoRowSelected_Warning = "No row(s) selected to be deleted"; 
	$lang_Time_TimeGrid_RemoveRow_Success = "Row(s) were deleted successfully";
	 $lang_Time_TimeGrid_RemoveRow_PartialSuccess = "Some of the rows could not be deleted";
	 $lang_Time_TimeGrid_RemoveRow_Failure = "Failed to delete row(s)";

	$lang_Common_Yes = "Yes";
	$lang_Common_No = "No";

	$lang_Leave_DoYouWantToCancelTheLeaveYouJustAssigned = "Do you want to cancel the leave you just assigned?";
	$lang_Leave_PleaseProvideAReason = "Please provide a reason";

	$lang_Time_Errors_OVERLAPPING_TIME_PERIOD_FAILURE = "You have a overlapping time period in your time events";

	$lang_view_DUPLICATE_NAME_FAILURE = "The same name is already used";

	$lang_Leave_CopyLeaveQuotaFromLastYear = "Copy leave quota from last year";
	$lang_Leave_CopyLeaveBroughtForwardFromLastYear = "Copy leave brought forward from last year";

	$lang_Leave_LEAVE_QUOTA_COPY_SUCCESS = "Successfully copied leave quota";
	$lang_Leave_LEAVE_QUOTA_COPY_FAILURE = "Failed to copy leave quota";

	$lang_Leave_LEAVE_BROUGHT_FORWARD_COPY_SUCCESS = "Successfully copied leave brought forward";
	$lang_Leave_LEAVE_BROUGHT_FORWARD_COPY_FAILURE = "Failed to copy leave brought forward";

	// Recruitment module
 	$lang_Recruit_VacancyID = 'Vacancy ID';
 	$lang_Recruit_JobTitleName = 'Job Title';
 	$lang_Recruit_HiringManager = 'Hiring Manager';
 	$lang_Recruit_VacancyStatus = 'Status';
 	$lang_Recruit_JobVacancyDeletionMessage = 'Deletion may affect Job applications';
 	$lang_Recruit_JobVacancyListHeading = 'Job Vacancies';
 	$lang_Recruit_JobVacancy_Active = 'Active';
 	$lang_Recruit_JobVacancy_InActive = 'Inactive';
 	$lang_Recruit_JobVacancy_Add_Heading = 'Add Job Vacancy';
 	$lang_Recruit_JobVacancy_Edit_Heading = 'Edit Job Vacancy';
 	$lang_Recruit_JobVacancy_JobTitleSelect = 'Select';
 	$lang_Recruit_JobVacancy_HiringManagerSelect = 'Select';
 	$lang_Recruit_JobVacancy_PleaseSpecifyJobTitle = 'Please specify Job Title';
 	$lang_Recruit_JobVacancy_PleaseSpecifyHiringManager = 'Please specify Hiring Manager';
 	$lang_Recruit_AllowedValuesAre = 'Allowed Values are: ';
	$lang_Recruit_NoManagersNotice = 'No managers found. You need to create a job title named \'Manager\' and assign to employees who are managers.';
	$lang_Recruit_NoHiringManagersNotice = 'No employees found to be assigned as Hiring Managers. You need to add employees\' data to the system.';

    $lang_Recruit_JobApplicationList_Heading = 'Applicants';
    $lang_Recruit_JobApplicationList_Name = 'Name';
    $lang_Recruit_JobApplicationList_PositionApplied = 'Position Applied';
    $lang_Recruit_JobApplicationList_Actions = 'Actions';
    $lang_Recruit_JobApplicationList_EventHistory = 'Event History';
    $lang_Recruit_JobApplicationList_Details = 'Details';
    $lang_Recruit_JobApplicationList_NoApplications = 'No Applicants found';

    $lang_Recruit_JobApplicationStatus_Submitted = 'Application Submitted';
    $lang_Recruit_JobApplicationStatus_FirstInterview = '1st Interview';
    $lang_Recruit_JobApplicationStatus_SecondInterview = '2nd Interview';
    $lang_Recruit_JobApplicationStatus_JobOffered = 'Job Offered';
    $lang_Recruit_JobApplicationStatus_OfferDeclined = 'Offer Declined';
    $lang_Recruit_JobApplicationStatus_PendingApproval = 'Pending Approval';
    $lang_Recruit_JobApplicationStatus_Hired = 'Hired';
    $lang_Recruit_JobApplicationStatus_Rejected = 'Rejected';

    $lang_Recruit_JobApplicationAction_Reject = 'Reject';
    $lang_Recruit_JobApplicationAction_FirstInterview = 'Schedule 1st Interview';
    $lang_Recruit_JobApplicationAction_SecondInterview = 'Schedule 2nd Interview';
    $lang_Recruit_JobApplicationAction_OfferJob = 'Offer Job';
    $lang_Recruit_JobApplicationAction_MarkDeclined = 'Mark Offer Declined';
    $lang_Recruit_JobApplicationAction_SeekApproval = 'Seek Approval';
    $lang_Recruit_JobApplicationAction_Approve = 'Approve';
    $lang_Recruit_JobApplicationAction_Interview_Time_Format = 'HH:MM';

    $lang_Recruit_JobApplicationDetails_Heading = 'Application Details';
    $lang_Recruit_JobApplicationDetails_Status = 'Application Status';
    $lang_Recruit_JobApplicationDetails_Actions = 'Actions';
    $lang_Recruit_JobApplicationHistory_EventHistory = 'Event History';
    $lang_Recruit_JobApplicationHistory_ApplicationForThePositionOf = 'Application for the position of';
    $lang_Recruit_JobApplicationHistory_DateApplied = 'Date Applied';
    $lang_Recruit_JobApplicationHistory_NoEvents = 'No Events available';
    $lang_Recruit_JobApplicationHistory_FirstInterview = '1st Interview';
    $lang_Recruit_JobApplicationHistory_SecondInterview = '2nd Interview';
    $lang_Recruit_JobApplicationHistory_Rejected = 'Rejected';
    $lang_Recruit_JobApplicationHistory_OfferedJob = 'Offered Job';
    $lang_Recruit_JobApplicationHistory_OfferMarkedAsDeclined = 'Offer marked as declined';
    $lang_Recruit_JobApplicationHistory_SeekApproval = 'Seek Approval';
    $lang_Recruit_JobApplicationHistory_Approved = 'Approved';
    $lang_Recruit_JobApplicationHistory_By = 'By';
    $lang_Recruit_JobApplicationHistory_ScheduledBy = 'Scheduled By';
    $lang_Recruit_JobApplicationHistory_At = 'At';
    $lang_Recruit_JobApplicationHistory_InterviewTime = 'Interview Time';
    $lang_Recruit_JobApplicationHistory_Interviewer = 'Interviewer';
    $lang_Recruit_JobApplicationHistory_Status = 'Status';
    $lang_Recruit_JobApplicationHistory_Notes = 'Notes';
    $lang_Recruit_JobApplicationHistory_StatusInterviewScheduled = 'Scheduled';
    $lang_Recruit_JobApplicationHistory_StatusFinished = 'Finished';
    $lang_Recruit_JobApplication_ScheduleFirstInterview = 'Schedule 1st Interview for';
    $lang_Recruit_JobApplication_ScheduleSecondInterview = 'Schedule 2nd Interview for';
    $lang_Recruit_JobApplication_Schedule_Date = 'Date';
    $lang_Recruit_JobApplication_Schedule_Time = 'Time';
    $lang_Recruit_JobApplication_Schedule_Interviewer = 'Interviewer';
    $lang_Recruit_JobApplication_Schedule_Notes = 'Notes';
    $lang_Recruit_JobApplication_Select = 'Select';
    $lang_Recruit_JobApplication_PleaseSpecifyDate = 'Please specify interview Date';
    $lang_Recruit_JobApplication_PleaseSpecifyTime = 'Please specify interview Time';
    $lang_Recruit_JobApplication_PleaseSpecifyInterviewer = 'Please specify Interviewer';
    $lang_Recruit_JobApplication_PleaseSpecifyValidDate = 'Date should be in the format: ';
    $lang_Recruit_JobApplication_PleaseSpecifyValidTime = 'Time should be in the format: ';
    $lang_Recruit_JobApplication_SecondInterviewShouldBeAfterFirst = 'Second interview date should be after the first interview date ';

    $lang_Recruit_JobApplicationConfirm_Heading = 'Confirm Action: ';
    $lang_Recruit_JobApplicationConfirm_ApplicantName = 'Applicant Name';
    $lang_Recruit_JobApplicationConfirm_Position = 'Position Applying for';

    $lang_Recruit_JobApplicationConfirm_ConfirmReject = 'Confirm Reject of above application';
    $lang_Recruit_JobApplicationConfirm_ConfirmRejectDesc = 'This will send an email to the applicant informing of the rejection';
    $lang_Recruit_JobApplicationConfirm_ConfirmOfferJob = 'Confirm Job Offer to above applicant.';
    $lang_Recruit_JobApplicationConfirm_ConfirmOfferJobDesc = 'No emails will be sent by the system to the applicant. The applicant will have to be contacted and offered the job.';
    $lang_Recruit_JobApplicationConfirm_ConfirmMarkDeclined = 'Mark Offer Declined';
    $lang_Recruit_JobApplicationConfirm_ConfirmMarkDeclinedDesc = 'Indicates that the applicant has declined the Job Offer.';
    $lang_Recruit_JobApplicationConfirm_ConfirmSeekApproval = 'Seek Approval';
    $lang_Recruit_JobApplicationConfirm_ConfirmApprove = 'Confirm Approval of above applicant';
    $lang_Recruit_JobApplicationConfirm_ConfirmApproveDesc = 'This will mark the applicant as hired and create an employee entry in the system for the applicant. The hiring manager will be notified, but no emails will be sent to the applicant.';

    $lang_Recruit_JobApplication_SeekApproval_Heading = 'Seek Approval to Hire ';
    $lang_Recruit_JobApplication_SeekApproval_GetApprovedBy = 'Get approved by ';
    $lang_Recruit_NoDirectorsNotice = 'No directors found. You need to create a job title named \'Director\' and assign to employees who are directors.';
    $lang_Recruit_JobApplication_SeekApproval_Notes = 'Notes';
    $lang_Recruit_JobApplication_SeekApproval_Desc = 'An email will be sent to the chosen person, asking for approval to hire.';
    $lang_Recruit_JobApplication_PleaseSpecifyDirector = 'Please specify a director to get approved by';
    $lang_Recruit_JobApplication_PleaseSpecifyNotes = 'Please provide some notes';

	$lang_Recruit_ApplicantVacancyList_Heading = 'Job Vacancies';
	$lang_Recruit_ApplicantVacancyList_Title = 'Job Vacancies';
	$lang_Recruit_ApplicationForm_Heading = 'Application Form for a position with ';
	$lang_Recruit_Application_CompanyNameNotSet = 'Company Name not set';
	$lang_Recruit_ApplicationForm_Position = 'Position Applying for';
	$lang_Recruit_Applicant_NoVacanciesFound = 'No Job Vacancies found. Please check back later.';
    $lang_Recruit_Applicant_ExpandAll = 'Expand All';
    $lang_Recruit_Applicant_CollapseAll = 'Collapse All';

	$lang_Recruit_ApplicationForm_FirstName = 'First Name';
	$lang_Recruit_ApplicationForm_MiddleName = 'Middle Name';
	$lang_Recruit_ApplicationForm_LastName = 'Last Name';
	$lang_Recruit_ApplicationForm_Street1 = 'Street 1';
	$lang_Recruit_ApplicationForm_Street2 = 'Street 2';
	$lang_Recruit_ApplicationForm_City = 'City';
	$lang_Recruit_ApplicationForm_StateProvince = 'State / Province';
	$lang_Recruit_ApplicationForm_Country = 'Country';
	$lang_Recruit_ApplicationForm_Zip = 'Zip Code';
	$lang_Recruit_ApplicationForm_Phone = 'Phone';
	$lang_Recruit_ApplicationForm_Mobile = 'Mobile';
	$lang_Recruit_ApplicationForm_Email = 'Email';
	$lang_Recruit_ApplicationForm_Qualifications = 'Qualifications and Experience';
	$lang_Recruit_ApplicationForm_Resume = 'Resume';
	$lang_Recruit_ApplicationForm_ResumeDownload = 'Download';
	$lang_Recruit_ApplicationForm_ResumeDescription = 'Size should be less than 1MB and only .doc, .docx, .odt, .pdf, .rtf, .txt are allowed';

	$lang_Recruit_ApplicationForm_PleaseSpecify = 'Please Specify ';
	$lang_Recruit_ApplicationForm_PleaseSpecifyValidEmail = 'Please Specify valid email address for ';
	$lang_Recruit_ApplicationForm_PleaseSpecifyValidPhone = 'Please Specify valid phone number for ';
	$lang_Recruit_ZipContainsNonNumericChars = 'Zip code contains non-numeric characters. Do you want to continue?';
	$lang_Recruit_ApplicationForm_PleaseSelect = 'Please Select ';

	$lang_Recruit_ApplicationStatus_SuccessHeading = 'Job Application Received';
	$lang_Recruit_ApplicationStatus_FailureHeading = 'Job Application Error';
	$lang_Recruit_ApplySuccess = 'Your application for the position of #jobtitle# was received';
	$lang_Recruit_ApplicantEmailedSuccess = 'An email confirmation was sent to the following email address: #email#';
	$lang_Recruit_ApplyFailure = 'There was an error while processing your application. Please try again later';
	$lang_Recruit_ApplyFailure_UploadError = 'There was an error when uploading your resume. Therefore your application was not submitted. Please try again';
	$lang_Recruit_ApplyFailure_UploadSizeError = 'Your resume size exceeds the allowed limit of 1MB';
	$lang_Recruit_ApplyFailure_UploadTypeError = 'Your resume file type is not allowed. Only .doc, .docx, .odt, .pdf, .rtf and .txt are allowed';
	$lang_Recruit_ApplicationStatus_FailureRetry = 'Retry Submission';

	$lang_Recruit_Resume_DeletionSucceeded = 'Resume Was Successfully Deleted';
	$lang_Recruit_Resume_DeletionFailed = 'Deleting Resume Failed';
	$lang_Recruit_Resume_ReplaceSucceeded = 'Resume Was Successfully Added';
	$lang_Recruit_Resume_ReplaceFailed = 'Adding Resume Failed';

	// login page
	$lang_login_title = "OrangeHRM - New Level of HR Management";
	$lang_login_UserNameNotGiven = "User Name not given!";
	$lang_login_PasswordNotGiven = "Password not given!";
	$lang_login_NeedJavascript = "You need a JavaScript enabled Browser. Ex. ";
	$lang_login_MozillaFirefox = "Mozilla Firefox";
	$lang_login_YourSessionExpired = "Your session expired because you were inactive. Please re-login.";
	$lang_login_LoginName = "Login Name";
	$lang_login_Password = "Password";
	$lang_login_Login = "Login";
	$lang_login_Clear = "Clear";
	$lang_login_InvalidLogin = "Invalid Login";
	$lang_login_UserDisabled = "User Disabled";
	$lang_login_EmployeeTerminated = "Login denied";
	$lang_login_NoEmployeeAssigned = "No Employee assigned to the ESS user account";
	$lang_login_temporarily_unavailable = "Service Temporarily Unavailable";
	$lang_login_OrangeHRMDescription = " comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vast range of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.";

	$lang_Leave_Common_FromTime = "From time";
	$lang_Leave_Common_ToTime = "To time";
	$lang_Leave_Common_TotalHours = "Total hours";
	$lang_Leave_Error_ToTimeBeforeFromTime = "To time before from time";
	$lang_Leave_Error_ZeroLengthHours = "Length of the leave is zero hours";
	$lang_Leave_Error_TotalTimeMoreThanADay = "Total time is greater than the shift length";
	$lang_Leave_Common_WorkshiftLengthIs = "Workshift length is ";
	$lang_Leave_Error_PleaseSpecifyEitherTotalTimeOrTheTimePeriod = "Please specify either total time or the time period";

	$lang_Leave_Error_DuplicateLeaveError = "Your leave request overlaps with the following existing leave requests.";
	$lang_Leave_Error_DuplicateLeaveErrorInstructions = "Please cancel existing leave requests that overlap or change the leave request below and retry.";

	$lang_Leave_Error_DuplicateLeaveWarning = "The following leave requests are on the same day as the current leave request";
	$lang_Leave_Error_DuplicateLeaveWarningInstructions = "Please review the existing leave requests and submit again to confirm or change the leave period if needed.";

	$lang_Leave_Duration = "Duration(hours)";
	$lang_Common_Hours = "hours";
	$lang_Common_Days = "days";

	$lang_Time_WorkShifts = "Work Shifts";
	$lang_Time_ShiftName = "Shift Name";
	$lang_Time_HoursPerDay = "Hours Per Day";
	$lang_Time_AvailableEmployees = "Available Employees";
	$lang_Time_AssignedEmployees = "Assigned Employees";
	$lang_Time_AssignEmployeesTitle = "Work Shift Edit";
    $lang_Time_WorkShift_Add = "Add new Work Shift";

	$lang_Time_Error_SpecifyWorkShiftName = "Specify work shift name";
	$lang_Time_Error_SpecifyHoursPerDay = "Specify hours per day";
	$lang_Time_Error_DecimalNotAllowed = "Decimal numbers are not allowed";
	$lang_Time_Error_HoursPerDayShouldBePositiveNumber = "Hours per day should be a positive number";
	$lang_Time_Error_HoursPerDayShouldBeLessThan24 = "Hours per day should be less than 24";
	$lang_Time_Error_HoursPerDayShouldBeANumericValue = "Hours per day should be a numeric value";
	$lang_Time_Error_NoEmployeeSelected = "No employee selected";

	$lang_Time_Errors_INVALID_WORK_SHIFT_FAILURE = "There were problems in the work shift details";
	$lang_Time_Errors_NO_RECORDS_SELECTED_FAILURE = "No records selected to delete";
	$lang_Time_Errors_UNKNOWN_ERROR_FAILURE = "Encountered an error";
	$lang_Time_Errors_INVALID_ID_FAILURE = "Invalid id";

	$lang_Menu_Time_WorkShifts = "Work shifts";

	include 'lang_default_benefits.php';

	$lang_Common_To = "to";

    // Leave Calendar

    $plugin_leave_Calendar = 'Leave Calendar';