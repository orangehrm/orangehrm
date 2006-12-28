<?php

/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software, http://www.hsenid.com
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
 * @author: S.H.Mohanjith <moha@mohanjith.net>
 */

	/**
	 * Error messages
	 * 
	 */
	
	$lang_Error_PleaseCorrectTheFollowing = "Please correct the following";
	$lang_Error_SelectAtLeastOneRecordToDelete = "Select tt least one record to delete";
	$lang_Error_DoYouWantToDelete = "Do you want to delete?";
	
	$lang_Error_PleaseSelectAYear = "Please select a Year";
	$lang_Error_PleaseSelectAnEmployee = "Please select an Employee";
	
	$lang_Error_DoYouWantToContinue = "Do you want to continue?";
	
	$lang_Error_ShouldBeNumeric = "Should be Numeric";	
	$lang_Error_FollowingErrorsWereFound = "Following errors were found";	
	$lang_Error_AreYouSureYouWantToDelete = "Are you sure you want to delete";
	
	//leave
	$lang_Error_PleaseSelectAValidFromDate = "Please select a valid From Date";
	$lang_Error_PleaseSelectALeaveType = "Please select a Leave Type";
			
	$lang_Error_LeaveDateCannotBeABlankValue = "Leave date cannot be a blank value!";
	
	$lang_Error_NoLeaveTypes = "No Leave Types";
	$lang_Error_NoRecordsFound = "No records found!";
	
	// Company Structure	
	$lang_Error_CompStruct_UnitCount = "Also #children unit(s) under #parent will be deteted";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "It could cause the company structure to change";
		
	$lang_Error_SubDivisionNameCannotBeEmpty = "Sub-division Name cannot be empty";
	$lang_Error_PleaseSelectATypeOrDefineACustomType = "Please select a Type or define a custom type";
	$lang_Error_CompStruct_LocEmpty = "Please select a Location or define a new Location and select";
 
	$lang_Error_LocationNameEmpty = "Location Name empty";
	$lang_Error_CountryNotSelected = "Country not selected";
	$lang_Error_StateNotSelected = "State not selected";
	$lang_Error_CityCannotBeEmpty = "City Cannot be empty";
	$lang_Error_AddressEmpty = "Address empty";
	$lang_Error_ZipEmpty = "Zip - Code Cannot be empty";
	$lang_Error_CompStruct_ZipInvalid = "Zip - Code Contains non-numeric characters Here they are #characterList";
	
	/**
	 * Menu Items
	 *
	 */
	 
	// Home
	$lang_Menu_Home = "Home";
	
	$lang_Menu_Home_Support = "Support";
	$lang_Menu_Home_Forum = "Forum";
	$lang_Menu_Home_Blog = "Blog";
	
	// Admin Module
	$lang_Menu_Admin = "Admin";
	
	$lang_Menu_Admin_CompanyInfo = "Company Info";
	$lang_Menu_Admin_CompanyInfo_Gen = "General";
	$lang_Menu_Admin_CompanyInfo_CompStruct = "Company Structure";
	$lang_Menu_Admin_CompanyInfo_Locations = "Locations";
	
	$lang_Menu_Admin_Job = "Job";
	$lang_Menu_Admin_Job_JobTitles = "Job Titles";
	$lang_Menu_Admin_Job_PayGrades = "Pay Grades";
	$lang_Menu_Admin_Job_EmpStatus = "Employment Status";
	$lang_Menu_Admin_Job_EEO = "EEO Job Categories";	
	
	$lang_Menu_Admin_Quali = "Qualification";
	$lang_Menu_Admin_Quali_Education = "Education";
	$lang_Menu_Admin_Quali_Licenses = "Licenses";
	
	$lang_Menu_Admin_Skills = "Skills";
	$lang_Menu_Admin_Skills_Skills = "Skills";
	$lang_Menu_Admin_Skills_Languages = "Languages";
	
	$lang_Menu_Admin_Memberships = "Memberships";
	$lang_Menu_Admin_Memberships_Memberships = "Memberships";
	$lang_Menu_Admin_Memberships_MembershipTypes = "Membership Types";
	
	$lang_Menu_Admin_NationalityNRace = "Nationality & Race";
	$lang_Menu_Admin_NationalityNRace_Nationality = "Nationality";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces = "Ethnic Races";
	
	$lang_Menu_Admin_Users = "Users";
	$lang_Menu_Admin_Users_UserGroups = "User Groups";
	$lang_Menu_Admin_Users_HRAdmin = "HR Admin Users";
	$lang_Menu_Admin_Users_ESS = "ESS Users";	
	
	// Leave Module	
	$lang_Menu_Leave = "Leave";
	$lang_Menu_Leave_PersonalLeaveSummary = "Personal Leave Summary";
	$lang_Menu_Leave_EmployeeLeaveSummary = "Employee Leave Summary";
	$lang_Menu_Leave_LeaveSummary = "Leave Summary";
	$lang_Menu_Leave_LeavesList = "Leaves List";
	$lang_Menu_Leave_ApproveLeave = "Approve Leave";
	$lang_Menu_Leave_LeaveTypes = "Leave Types";
	$lang_Menu_Leave_Apply = "Apply";
	$lang_Menu_Leave_LeaveList = "Leave List";

 	$lang_Leave_Title_Apply_Leave = "Apply Leave";
	
	/**
	 * Common
	 */
	$lang_Commn_RequiredFieldMark = "Fields marked with an asterisk #star are required";
	$lang_Commn_code = "Code";
	$lang_Commn_description = "Description";
	$lang_Commn_title = "Title";
	$lang_Commn_name = "Name";
	
	// admin module
	$lang_Admin_Common_Institute = "Institute";
	$lang_Admin_Common_Course = "Course";
	
	// leave module
	$lang_Leave_Common_Date = "Date";
	
	$lang_Leave_Common_FromDate = "From Date";
	$lang_Leave_Common_ToDate = "To Date";
	
	$lang_Leave_Common_LeaveType = "Leave Type";
	$lang_Leave_Common_Status = "Status";
	$lang_Leave_Common_Length = "Full Day/Half Day";
	$lang_Leave_Common_Comments = "Comments";
	$lang_Leave_Common_Comment = "Comment";
	$lang_Leave_Common_Approved = "Approved";
	$lang_Leave_Common_Cancelled = "Cancelled";
	$lang_Leave_Common_PendingApproval = "Pending Approval";
	$lang_Leave_Common_Rejected = "Rejected";
	$lang_Leave_Common_Taken = "Taken";
	$lang_Leave_Common_InvalidStatus = "Invalid Status";
	$lang_Leave_Common_FullDay = "Full Day";
	$lang_Leave_Common_HalfDayMorning = "Half Day / Morning";
	$lang_Leave_Common_HalfDayAfternoon = "Half Day / Afternoon";
	$lang_Leave_Common_LeaveTaken = "Leave Taken";
	$lang_Leave_Common_LeaveAvailable = "Leave Available";
	$lang_Leave_Common_LeaveTypeName = "Leave Type Name";
	$lang_Leave_Common_LeaveTypeId = "Leave Type Id";
	$lang_Leave_Common_Select = "Select";
	$lang_Leave_Common_oldLeaveTypeName = "Existing Leave Type Name";
	$lang_Leave_Common_newLeaveTypeName = "New Leave Type Name";
	$lang_Leave_Common_EmployeeName = "Empoloyee Name";
	$lang_Leave_Common_LeaveEntitled = "Leave Entitled";
	$lang_Leave_Common_Year = "Year";
	$lang_Leave_Common_ListOfTakenLeave = "List of Taken Leave";
	$lang_Leave_Common_Remove = "Remove";
		
 	$lang_Leave_Leave_list_Title1 = "Approve Leave";
	$lang_Leave_Leave_list_Title2 = "Leaves Taken by #employeeName in #dispYear";
	$lang_Leave_Leave_list_Title3 = "Leave List";
	$lang_Leave_Select_Employee_Title = "Select Employee";
	$lang_Leave_Leave_Summary_Title = "Leave Summary";
	$lang_Leave_Leave_Summary_EMP_Title = "Leave Summary for #employeeName for #dispYear";
	$lang_Leave_Select_Employee_Title = "Select Employee";
	$lang_Leave_Leave_Summary_EMP_Title = "Leave Summary for #dispYear";
	$lang_Leave_Leave_Summary_SUP_Title = "Leave Summary for #employeeName for #dispYear";
	$lang_Leave_Define_leave_Type_Title = "Define Leave Type ";
	$lang_Leave_Leave_Type_Summary_Title = "Leave Types";
		
	$lang_bankInformation_code = "Code";
	$lang_bankInformation_description = "Description";
	
	$lang_compstruct_add = "Add";
	$lang_compstruct_delete = "Delete";
	$lang_compstruct_clear = "Clear";
	$lang_compstruct_hide = "Hide";
	$lang_compstruct_save = "Save";
 
 	$lang_comphire_heading = "Company Hierarchy: Company Information";
	$lang_comphire_relationalhierarchy = "Relational Hierarchy";
	$lang_comphire_employee = "Employee";
	$lang_comphire_definitionlevel = "Definition Level";
	$lang_comphire_telephone = "Telephone";
	$lang_comphire_fax = "Fax";
	$lang_comphire_email = "Email";
	$lang_comphire_url = "Url";
	$lang_comphire_logo = "Logo";
	$lang_comphire_selecthie = "Select Hierarchy";
	$lang_comphire_selectdef = "Select Def. Level";
	$lang_compstruct_heading = "Company Info : Company Structure";
	$lang_compstruct_frmSub_divisionHeadingAdd = "Add a sub-division to";
	$lang_compstruct_frmSub_divisionHeadingEdit = "Edit";
	$lang_compstruct_Name = "Name";
	$lang_compstruct_Type = "Type";
	$lang_compstruct_Division = "Division";
	$lang_compstruct_Description = "Description";
	$lang_compstruct_Department = "Department";
	$lang_compstruct_Team = "Team";
	$lang_compstruct_Other = "Other";
	$lang_compstruct_Location = "Location";
	$lang_compstruct_frmNewLocation = "Define a new location";
	$lang_compstruct_Address = "Address";
	$lang_compstruct_country = "Country";
	$lang_compstruct_state = "State / Province";
	$lang_compstruct_city = "City";
	$lang_compstruct_ZIP_Code = "ZIP Code";
	$lang_compstruct_Phone = "Phone";
	$lang_compstruct_no_root = "Root not found! Please define the root.";
	
 	$lang_corptit_heading = "Corporate Titles : Job Information";
	$lang_corptit_topinhierachy = "Top In Hierachy";
	$lang_corptit_multipleheads = "Multiple Heads";
	$lang_corptit_headcount = "Head Count";
	$lang_corptit_nextlevelupgrade = "Next Level Upgrade";
	$lang_corptit_selectcor = "Select Corp. Title";
	$lang_corptit_salarygrade = "Salary Grade";
	$lang_corptit_selectsal = "Select Salary Grade";
	
 	$lang_costcenters_heading = "Cost Center : Company Information";
	
 	$lang_countryinformation_heading = "Country Information : Geo Information";
	  
 	$lang_currencytypes_heading = "Currency Type : Job Information";
	 
 
 	$lang_districtinformation_heading = "City Information : Geo Information";
	$lang_districtinformation_selectcounlist = "Select Country";
	$lang_districtinformation_selstatelist = "Select State";
	 
 	$lang_eeojobcat_heading = "Job : EEO Job Category";
	$lang_eeojobcat_description = "Title";
	 
 	$lang_electorateinformation_heading = "Electorate Information : Geo Information";
	$lang_emprepinfo_heading = "Define Employee Reports";
	
 	$lang_emptypes_heading = "Employee Types : Nexus Information";
	$lang_emptypes_datelimited = "Date Limited";
	$lang_emptypes_prefix = "Prefix";
	
 	$lang_empview_heading = "Job : Employment Status";
	$lang_empview_EmpID = "Emp. ID";
	$lang_empview_EmpFirstName = "Emp. First Name";
	$lang_empview_EmpLastName = "Emp. Last Name";
	$lang_empview_EmpMiddleName = "Emp. Middle Name";
	$lang_empview_serach = "Search";
	$lang_empview_searchby = "Search By:";
	$lang_empview_description = "Search For:";
	$lang_empview_norecorddisplay = "No Records to Display";
	$lang_empview_previous = "Previous";
	$lang_empview_next = "Next";
	$lang_empview_employeeid = "Employee Id";
	$lang_empview_employeename = "Employee Name";
	$lang_empview_ADD_SUCCESS = "Successfully Added";
	$lang_empview_UPDATE_SUCCESS = "Successfully Updated";
	$lang_empview_DELETE_SUCCESS = "Successfully Deleted";
	$lang_empview_ADD_FAILURE = "Failed to Add";
	$lang_empview_UPDATE_FAILURE = "Failed to Update";
	$lang_empview_DELETE_FAILURE = "Failed to Delete";
	$lang_empview_Language = "Language";
	$lang_empview_WorkExperience = "Work Experience";
	$lang_empview_Payment = "Payment";
	$lang_empview_Skills = "Skills";
	$lang_empview_Licenses = "Licenses";
	$lang_empview_EmployeeInformation = "Employee Information";
	$lang_empview_Memberships = "Memberships";
	$lang_empview_Report = "Report";
	 
 	$lang_ethnicrace_heading = "Nationality & Race :Ethnic Races";
	
 	$lang_extracurractcat_heading = "Extra Curricular Activities Category : Qualifications Information";
	
 	$lang_extracurractinfo_heading = "Extra Curricular Activity Information : Qualification Information";
	$lang_extracurractinfo_extracuaccat = "Extra Curricular Act Category";
	$lang_extracurractinfo_selectsecucat = "Select Ex. Curr. Cat";
	
 	$lang_geninfo_heading = "Company Info : General";
	$lang_geninfo_compname = "Company Name";
	$lang_geninfo_taxID = "Tax ID";
	$lang_geninfo_naics = "NAICS";
	$lang_geninfo_err_CompanyName = "Company name cannot be blank";
	$lang_geninfo_err_Phone = "Not a valid phone/fax number";
	
 	$lang_hierarchydef_heading = "Hierarchy Information : Company Information";
	
	$lang_hremp_nickname = "Nick Name ";
	$lang_hremp_photo = "Photo ";
	$lang_hremp_ssnno = "SSN No :";
	$lang_hremp_nationality = "Nationality";
	$lang_hremp_sinno = "SIN No :";
	$lang_hremp_dateofbirth = "Date of Birth";
	$lang_hremp_otherid = "Other ID";
	$lang_hremp_maritalstatus = "Marital Status";
	$lang_hremp_selectnatio = "Select Nationality";
	$lang_hremp_smoker = "Smoker";
	$lang_hremp_gender = "Gender";
	$lang_hremp_dlicenno = "Driver's License Number";
	$lang_hremp_licexpdate = "License Expiry Date ";
	$lang_hremp_militaryservice = "Military Service ";
	$lang_hremp_ethnicrace = "Ethnic Race ";
	$lang_hremp_jobtitle = "Job Title";
	$lang_hremp_selempstat = "Select Empl. Status";
	$lang_hremp_eeocategory = "EEO Category";
	$lang_hremp_seleeocat = "Select EEO Cat";
	$lang_hremp_joindate = "Joined Date";
	
 	$lang_hremp_dependents = "Dependents";
	$lang_hremp_children = "Children";
	$lang_hremp_relationship = "Relationship";
	$lang_hremp_hmtele = "Home Telephone";
	$lang_hremp_mobile = "Mobile";
	$lang_hremp_worktele = "Work Telephone";
	$lang_hremp_city = "City/Town";
	$lang_hremp_workemail = "Work Email";
	$lang_hremp_otheremail = "Other Email";
	$lang_hremp_passport = "Passport";
	$lang_hremp_visa = "Visa";
	$lang_hremp_citizenship = "Citizenship";
	$lang_hremp_passvisano = "Passport/Visa No";
	$lang_hremp_issueddate = "Issued Date";
	$lang_hremp_i9status = "I9 Status";
	$lang_hremp_dateofexp = "Date of Expiry";
	$lang_hremp_i9reviewdate = "I9 Review Date";
	$lang_hremp_path = "Path";
	$lang_hremp_filename = "File Name";
	$lang_hremp_size = "Size";
	$lang_hremp_type = "Type";
	        
 	$lang_hremplan_employeelanguageflu = "Employee Language Fluency";
	$lang_hremplan_fluency = "Area Of Competence";
	$lang_hremplan_ratinggarde = "Fluency";
	$lang_hremplan_assignlanguage = "Assigned Languages";
	
 	$lang_hremplicenses_employeelicen = "Employee Licenses";
	$lang_hremplicenses_assignlicenses = "Assigned Licenses";
	$lang_hremplicenses_licentype = "License Type";
	$lang_hremplicenses_assignlicen = "Assigned Licenses";
	
 	$lang_hrEmpMain_ratinggarde = "Competency";
	$lang_hrEmpMain_assignlanguage = "Assigned Languages";
	$lang_hrEmpMain_Writing = "Writing";
	$lang_hrEmpMain_Speaking = "Speaking";
	$lang_hrEmpMain_Reading = "Reading";
	$lang_hrEmpMain_Poor = "Poor";
	$lang_hrEmpMain_Basic = "Basic";
	$lang_hrEmpMain_Good = "Good";
	$lang_hrEmpMain_MotherTongue = "Mother Tongue";
	$lang_hrEmpMain_Individual = "Individual";
	$lang_hrEmpMain_employeemembership = "Employee Memberships";
	$lang_hrEmpMain_subownership = "Subscription Ownership";
	$lang_hrEmpMain_subamount = "Subscription Amount";
	$lang_hrEmpMain_subcomdate = "Subscription Commence Date";
	$lang_hrEmpMain_subredate = "Subscription Renwal Date";
	$lang_hrEmpMain_selmemtype = "Select Membership Type";
	$lang_hrEmpMain_selmemship = "Select Membership";
	$lang_hrEmpMain_selownership = "Select Ownership";
	$lang_hrEmpMain_assignmemship = "Assigned Memberships";
	$lang_hrEmpMain_paygrade = "Pay Grade";
	$lang_hrEmpMain_currency = "Currency";
	$lang_hrEmpMain_minpoint = "Minimum Salary";
	$lang_hrEmpMain_maxpoint = "Maximum Salary";
	$lang_hrEmpMain_bassalary = "Basic Salary";
	$lang_hrEmpMain_assignedsalary = "Assigned Salary";
	
 	$lang_hrEmpMain_heading = "Employee Report";
	$lang_hrEmpMain_supervisorsubordinator = "Supervisor / Subordinate";
	$lang_hrEmpMain_reportingmethod = "Reporting Method";
	$lang_hrEmpMain_subordinateinfomation = "Subordinate Infomation";
	$lang_hrEmpMain_supervisorinfomation = "Supervisor Infomation";
	$lang_hrEmpMain_selecttype = "Select Method";
	$lang_hrEmpMain_Direct = "Direct";
	$lang_hrEmpMain_Indirect = "Indirect";
	$lang_hrEmpMain_employeeskill = "Employee Skills";
	$lang_hrEmpMain_yearofex = "Years of Experience";
	$lang_hrEmpMain_assignskills = "Assigned Skills";
	$lang_hrEmpMain_employerworkex = "Employee Work Experience";
	$lang_hrEmpMain_employer = "Employer";
	$lang_hrEmpMain_enddate = "End Date";
	$lang_hrEmpMain_startdate = "Start Date";
	$lang_hrEmpMain_assignworkex = "Assigned Work Experience";
	$lang_hrEmpMain_workexid = "Work Experience ID";
	$lang_hrEmpMain_internal = "Internal";
	$lang_hrEmpMain_major = "Major/Specialization";
	$lang_hrEmpMain_education = "Education";
	$lang_hrEmpMain_gpa = "GPA/Score";
	$lang_hrEmpMain_assigneducation = "Assigned Education";
	$lang_hrEmpMain_assignattach = "Assigned Attachments";
	
 	$lang_jobtitle_heading = "Job : Job Title";
	$lang_jobtitle_jobtitid = "Job Title ID";
	$lang_jobtitle_jobtitname = "Job Title Name";
	$lang_jobtitle_jobtitdesc = "Job Description";
	$lang_jobtitle_jobtitcomments = "Job Title Comments";
	$lang_jobtitle_addpaygrade = "Add Pay Grade";
	$lang_jobtitle_emstatExpl = "Define the Employment Status allowed for the Job Title";
	$lang_jobtitle_editpaygrade = "Edit Pay Grade";
	$lang_jobtitle_addempstat = "Add Employment Status";
	$lang_jobtitle_editempstat = "Edit Employment Status";
	$lang_jobtitle_empstat = "Employment Status";
	
 	$lang_languageinformation_heading = "Skills : Language";
	
 	$lang_licenses_heading = "Qualification : Licenses";
	
 	$lang_locations_heading = "Company Info : Locations";
	
 	$lang_membershipinfo_heading = "Memberships : Membership";
	$lang_membershipinfo_membershiptype = "Membership Type";
	$lang_membershipinfor_selectmember = "Select Membership";
	
 	$lang_membershiptypes_heading = "Memeberships : Membership Type";
	
 	$lang_nationalityinformation_heading = "Nationality & Race : Nationality";
	
 	$lang_provinceinformation_heading = "State/Province Information : Geo Information";
	
 	$lang_qualificationtypes_heading = "Qualification Types : Qualifications Information";
	$lang_repview_ReportID = "Report ID";
	$lang_repview_ReportName = "Report Name";
	$lang_repview_ViewEmployeeReports = "View Employee Reports";
	$lang_repview_message = "Deletion might affect Company Hierarchy";
	$lang_repview_DefineEmployeeReports = "Define Employee Reports";
	$lang_repview_message2 = "Deletion might affect viewing of reports";
	 
 	$lang_routeinformation_heading = "Route Information : Route Information";
	$lang_salarygrades_heading = "Job : Pay Grade";
	$lang_salarygrades_currAss = "Currency Assigned";
	$lang_salarygrades_stepSal = "Step Increase";
	$lang_salcurrdet_heading = "Currency Assignment to Salary Grade";
	$lang_salcurrdet_assigncurr = "Assigned Currency";
	
 	$lang_satutoryinfo_heading = "Statutory Type : Nexus Information";
	
 	$lang_view_EmploymentStatusID = "Employment Status ID";
	$lang_view_EmploymentStatusName = "Employment Status Name";
	$lang_view_message = "Deletion may affect Job Titles";
	$lang_view_message1 = "Deletion may affect Pay Grade of Employees in PIM";
	$lang_view_SkillName = "Skill Name";
	$lang_view_SkillID = "Skill ID";
	$lang_view_message3 = "Deletion might affect Employee Information";
	$lang_view_LocationName = "Location Name";
	$lang_view_message4 = "Deletion might affect Company Hierarchy. If the Location has associations deletion may fail";
	$lang_view_CurrencyName = "Currency Name";
	$lang_view_CurrencyTypes = "Currency Types";
	$lang_view_message5 = "Deletion might affect Salary Currency Detail";
	$lang_view_CompanyHierarchy = "Company Hierarchy";
	$lang_view_CompanyHierarchyName = "Company Hierarchy Name";
	$lang_view_message6 = "Deletion might affect Employee Information";
	$lang_view_QualificationType = "Qualification Type";
	$lang_view_QualificationName = "Qualification Name";
	$lang_view_message9 = "Deletion might affect Qualifications, Designation Qualification, Employee Qualification";
	$lang_view_RatingMethod = "Rating Method";
	$lang_view_RatingMethodName = "Rating Method Name";
	$lang_view_message10 = "Deletion might affect Qualification, Languages";
	$lang_view_CorporateTitle = "Corporate Title";
	$lang_view_CorporateTitleName = "Corporate Title name";
	$lang_view_message11 = "Deletion might affect Employee Information, Designations";
	$lang_view_ExtraCategory = "Extra Curricular Activity Category";
	$lang_view_ExtraCategoryName = "Extra Curricular Activity Category Name";
	$lang_view_message12 = "Deletion might affect Ex. Curr. Activities, Employee Ex. Curr. Activities";
	$lang_view_MembershipTypeName = "Membership Type Name";
	$lang_view_message13 = "Deletion might affect Employee Memberships, Memberships";
	$lang_view_EmployeeCategoryName = "Employee Category Name";
	$lang_view_message14 = "Deletion might affect Employee Information";
	$lang_view_EmployeeGroupName = "Employee Group Name";
	$lang_view_message15 = "Deletion might affect Employee Information";
	$lang_view_NationalityName = "Nationality Name";
	$lang_view_message16 = "Deletion might affect Employee Information";
	$lang_view_CountryID = "Country ID";
	$lang_view_CountryName = "Country Name";
	$lang_view_message17 = "Deletion might affect Employee Information";
	$lang_view_HierarchyDefinition = "Hierarchy Definition";
	$lang_view_HierarchyDefinitionName = "Hierarchy Definition Name";
	$lang_view_message18 = "Deletion might affect Company Hierachy";
	$lang_view_StateProvinceName = "State/Province Name";
	$lang_view_message19 = "Deletion might affect Employee Information";
	$lang_view_CityName = "City Name";
	$lang_view_message20 = "Deletion might affect Employee Information";
	$lang_view_LanguagName = "Language Name";
	$lang_view_message21 = "Deletion might affect Employee Language";
	$lang_view_Membership = "Membership";
	$lang_view_MembershipType = "Membership Type";
	$lang_view_MembershipName = "Membership Name";
	$lang_view_message22 = "Deletion might affect Employee Membership";
	$lang_view_ExtraActivities = "Extra Curricular Activities";
	$lang_view_ExtraActivitiesName = "Extra Curricular Activities Name";
	$lang_view_message23 = "Deletion might affect Employee Ex. Curr. Activities";
	$lang_view_PayGradeName = "Pay Grade Name";
	$lang_view_message24 = "Deletion might affect Employee Information, Job Titles";
	$lang_view_message25 = "Deletion might affect Education";
	$lang_view_EmployeeTypeID = "Employee TypeID";
	$lang_view_EmployeeTypeName = "Employee Type Name";
	$lang_view_message26 = "Deletion might affect Employee Information";
	$lang_view_EEOJobCategory = "EEO Job Category";
	$lang_view_EEOJobCategoryid = "EEO Job Category Id";
	$lang_view_EEOJobCategoryName = "EEO Job Category Name";
	$lang_view_message27 = "Deletion might affect Employee Information";
	$lang_view_message28 = "Deletion might affect Employee Language";
	$lang_view_EthnicRace = "Ethnic Race";
	$lang_view_EthnicRaceName = "Ethnic Race Name";
	$lang_view_message29 = "Deletion might affect Employee";
	$lang_view_message30 = "Deletion might affect Employee Information";
	$lang_view_message31 = "Deletion might affect Employee Information, Corporate Titles";
	$lang_view_message32 = "Deletion might affect Qualifications, Designation Qualification, Employee Qualification";
	$lang_view_License_ID = "License ID";
	$lang_view_LicenseDescription = "License Description";
	$lang_view_message33 = "Deletion might affect Employee Information";
	$lang_view_UserID = "User ID";
	$lang_view_UserName = "User Name";
	$lang_view_message34 = "Deletion could make OrangeHRM unusable";
	$lang_view_UserGroupID = "User Group ID";
	$lang_view_UserGroupName = "User Group Name";
	$lang_view_message35 = "Deletion could make OrangeHRM unusable";
	
	$lang_view_Users = "Users";
	$lang_view_UserGroups = "User Groups";
	$lang_view_HRAdmin = "HR Admin";
	$lang_view_ESS = "ESS";
	$lang_view_ID = "ID";
	
	$lang_index_WelcomeMes = "Welcome #username";
	$lang_index_ChangePassword = "Change Password";
	$lang_index_Logout = "Logout";
?>