<?php
/**
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
 */

	/**
	 * Error messages
	 *
	 */

	$lang_Error_PleaseCorrectTheFollowing 			= "Ret venligst følgende";
	$lang_Error_SelectAtLeastOneRecordToDelete 		= "Vælg mindst ét felt der skal slettes";
	$lang_Error_DoYouWantToDelete 					= "Ønsker du at slette?";
	$lang_Error_EnterDate							= "Indtast dato?";

	$lang_Error_PleaseSelectAYear 					= "Vælg år";
	$lang_Error_PleaseSelectAnEmployee 				= "Vælg medarbejder";

	$lang_Error_DoYouWantToContinue 				= "Ønsker du at fortsætte?";

	$lang_Error_PleaseSelectAnEmployee				= "Vælg medarbejder";

	$lang_Error_ShouldBeNumeric 					= "Skal være nummerisk";
	$lang_Error_FollowingErrorsWereFound 			= "Følgende fejl blev fundet";
	$lang_Error_AreYouSureYouWantToDelete 			= "Er du sikker på at du vil slette?";

	//leave
	$lang_Error_PleaseSelectAValidFromDate 			= "Vælg venligst en gyldig 'fra dato'";
	$lang_Error_PleaseSelectALeaveType 				= "Vælg venligst en fraværstype";

	$lang_Error_LeaveDateCannotBeABlankValue 		= "Fraværsdato kan ikke efterlades blankt";
	$lang_Error_NameOfHolidayCannotBeBlank 			= "Ferienavn kan ikke efterlades blankt";

	$lang_Error_NoLeaveTypes 						= "Ingen fraværstyper";
	$lang_Error_NoRecordsFound 						= "Ingen registreringer fundet";

	$lang_Error_InvalidDate 						= "Ugyldig dato";

	//PIM
	$lang_Error_LastNameNumbers						= "Efternavn indeholder tal. Ønsker du at fortsætte?";
	$lang_Error_FirstNameNumbers					= "Fornavn indeholder tal. Ønsker du at fortsætte?";
	$lang_Error_MiddleNameNumbers					= "Mellemnavn indeholder tal. Ønsker du at fortsætte?";
	$lang_Error_MiddleNameEmpty						= "Intet mellemnavn indtastet. Ønsker du at fortsætte?";
	$lang_Error_LastNameEmpty						= "Intet efternavn indtastet";
	$lang_Error_FirstNameEmpty						= "Intet fornavn indtastet";
	$lang_Error_ChangePane							= "Gem venligst ændringerne inden du går til et nyt felt";

	$lang_Error_UploadFailed						= "Upload fejlede!";

	// Company Structure
	$lang_Error_Company_General_Undefined 				= "Definer venligst generel virksomheds information først";
	$lang_Error_CompStruct_UnitCount 					= "Også #children under #parent vil blive slettet";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "Dette kan foresage ændringer i virksomhedens struktur";

	$lang_Error_SubDivisionNameCannotBeEmpty 			= "Navn for under-division kan ikke efterlades blankt";
	$lang_Error_PleaseSelectATypeOrDefineACustomType 	= "Vælg venligst en type eller definer en ny type";
	$lang_Error_CompStruct_LocEmpty 					= "Vælg venligst en lokation eller definer en ny lokation";

	$lang_Error_LocationNameEmpty 						= "Navn for lokation er ikke udfyldt";
	$lang_Error_CountryNotSelected 						= "Land er ikke valgt";
	$lang_Error_StateNotSelected 						= "Stat er ikke valgt";
	$lang_Error_CityCannotBeEmpty 						= "By kan ikke efterlades blank";
	$lang_Error_AddressEmpty 							= "Addresse er ikke udfyldt";
	$lang_Error_ZipEmpty 								= "Postnummer kan ikke efterlades blank";
	$lang_Error_CompStruct_ZipInvalid 					= "Postkode indeholder ikke-nummeriske tegn, her er de #characterList";

	$lang_Error_InvalidEmail 							= "Ugyldig E-mail addresse";

	$lang_Error_FieldShouldBeNumeric					= "Felt skal være nummerisk";

	$lang_Error_FieldShouldBeSelected					= "Felt skal vælges";
	$lang_Error_SelectAtLeastOneCheckBox 				= "Markér mindst én check box";

	/**
	 * Menu Items
	 *
	 */

	// Home
	$lang_Menu_Home 							= "Hjem";

	$lang_Menu_Home_Support 					= "Support";
	$lang_Menu_Home_Forum 						= "Forum";
	$lang_Menu_Home_Blog 						= "Blog";

	$lang_Menu_Ess								= "Selvbetjening";

	// Admin Module
	$lang_Menu_Admin 							= "Admin";

	$lang_Menu_Admin_CompanyInfo 				= "Virksomhedsinfo";
	$lang_Menu_Admin_CompanyInfo_Gen 			= "Generel";
	$lang_Menu_Admin_CompanyInfo_CompStruct 	= "Virksomhedsstruktur";
	$lang_Menu_Admin_CompanyInfo_Locations 		= "Lokationer";

	$lang_Menu_Admin_Job 						= "Job";
	$lang_Menu_Admin_Job_JobTitles 				= "Jobtitelr";
	$lang_Menu_Admin_Job_PayGrades 				= "Aflønningsniveauer";
	$lang_Menu_Admin_Job_EmpStatus 				= "Medarbejder Status";
	$lang_Menu_Admin_Job_EEO 					= "Faggrupper";

	$lang_Menu_Admin_Quali 						= "Kvalifikationer";
	$lang_Menu_Admin_Quali_Education 			= "Uddannelse";
	$lang_Menu_Admin_Quali_Licenses 			= "Licenser";

	$lang_Menu_Admin_Skills 					= "Færdigheder";
	$lang_Menu_Admin_Skills_Skills 				= "Færdigheder";
	$lang_Menu_Admin_Skills_Languages 			= "Sprog";

	$lang_Menu_Admin_Memberships 					= "Medlemskaber";
	$lang_Menu_Admin_Memberships_Memberships 		= "Medlemskaber";
	$lang_Menu_Admin_Memberships_MembershipTypes 	= "Type af medlemskaber";

	$lang_Menu_Admin_NationalityNRace 				= "Nationalitet og etnisk race";
	$lang_Menu_Admin_NationalityNRace_Nationality 	= "Nationalitet";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces 	= "Etnisk race";

	$lang_Menu_Admin_Users 							= "Brugere";
	$lang_Menu_Admin_Users_UserGroups 				= "Admin bruger grupper";
	$lang_Menu_Admin_Users_HRAdmin 					= "HR Admin brugere";
	$lang_Menu_Admin_Users_ESS 						= "Brugere af selvbetjening";

	$lang_Menu_Admin_EmailNotifications = "Email underrettelse";
	$lang_Menu_Admin_EmailConfiguration = "Konfiguration";
	$lang_Menu_Admin_EmailSubscribe = "Abonnér";
	$lang_Menu_Admin_Customers = "Kunder";
	$lang_Menu_Admin_Projects = "Projekter";

	// Leave Module
	$lang_Menu_Leave 									= "Fravær";
	$lang_Menu_Leave_PersonalLeaveSummary 				= "Personlig fraværsoversigt";
	$lang_Menu_Leave_EmployeeLeaveSummary 				= "Medarbejder fraværsoversigt";
	$lang_Menu_Leave_LeaveSummary 						= "Fraværsoversigt";
	$lang_Menu_Leave_LeavesList 						= "Fraværsliste";
	$lang_Menu_Leave_ApproveLeave 						= "Godkend fravær";
	$lang_Menu_Leave_LeaveTypes 						= "Fraværstype";
	$lang_Menu_Leave_Apply 								= "Ansøg";
	$lang_Menu_Leave_Assign								= "Tildel";
	$lang_Menu_Leave_LeaveList 							= "Fraværsliste";
	$lang_Menu_Leave_DefineDaysOff 						= "Definér fridage";
	$lang_Menu_Leave_DefineDaysOff_Weekends 			= "Weekender";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays 	= "Specifikke feriedage";

 	$lang_Leave_Title_Apply_Leave 						= "Ansøg om fravær ";
 	$lang_Leave_Title_Assign_Leave 						= "Tildel fravær";

	// Report
	$lang_Menu_Reports = "Raporter";
	$lang_Menu_Reports_ViewReports = "Se raporter";
	$lang_Menu_Reports_DefineReports = "Definér raporter";

	// Time module
	$lang_Menu_Time = "Tid";
	$lang_Menu_Time_Timesheets = "Tidstabel";
	$lang_Menu_Time_PersonalTimesheet = "Personlig tidsskema";
	$lang_Menu_Time_EmployeeTimesheets = "Medarbejderes tidsskema";



	/**
	 * Common
	 */
	$lang_Common_ConfirmDelete				= " Ønsker du at slette?";
	$lang_Common_FieldEmpty				= "Felt blankt";
	$lang_Common_SelectDelete			= "Vælg mindst ét felt der skal slettes";
	$lang_Common_SelectField			= "Vælg det felt der skal søges indenfor!";
	$lang_Commn_RequiredFieldMark 			= "Felter markeret med * er obligatoriske";
	$lang_Commn_code 						= "Kode";
	$lang_Commn_description 				= "Beskrivelse";
	$lang_Commn_title 						= "Titel";
	$lang_Commn_name 						= "Navn";
	$lang_Commn_PleaseWait					= "Vent venligst";
	$lang_Common_Select 				= "Vælg";
	$lang_Commn_Email						= "E-mail";

	$lang_Common_Edit = "Redigér";
	$lang_Common_New = "Ny";

	//days
	$lang_Common_Monday 					= "Mandag";
	$lang_Common_Tuesday 					= "Tirsdag";
	$lang_Common_Wednesday 					= "Onsdag";
	$lang_Common_Thursday 					= "Torsdag";
	$lang_Common_Friday 					= "Fredag";
	$lang_Common_Saturday 					= "Lørdag";
	$lang_Common_Sunday 					= "Søndag";

	$lang_Leave_Common_Weekend 				= "Weekend";

	// admin module
	$lang_Admin_Common_Institute 			= "Institut";
	$lang_Admin_Common_Course 				= "Studium";

	// leave module
	$lang_Leave_Common_Date 				= "Dato";

	$lang_Leave_Common_FromDate 			= "Fra dato";
	$lang_Leave_Common_ToDate 				= "Til dato";

	$lang_Leave_Common_LeaveType 			= "Fraværstype";
	$lang_Leave_Common_Status 				= "Status";
	$lang_Leave_Common_Length 				= "Hel dag/halv dag";
	$lang_Leave_Common_Range 				= "Periode";
	$lang_Leave_Common_Comments 			= "Kommentarer";
	$lang_Leave_Common_Comment 				= "Kommentar";
	$lang_Leave_Common_Approved 			= "Godkendt";
	$lang_Leave_Common_Cancelled 			= "Annulleret";
	$lang_Leave_Common_PendingApproval 		= "Under behandling";
	$lang_Leave_Common_Rejected 			= "Afvist";
	$lang_Leave_Common_Taken 				= "Taget";
	$lang_Leave_Common_InvalidStatus 		= "Ugyldig status";
	$lang_Leave_Common_FullDay 				= "Hel dag";
	$lang_Leave_Common_HalfDayMorning 		= "Halv dag / Morgen";
	$lang_Leave_Common_HalfDayAfternoon 	= "Halv dag / Eftermiddag";
	$lang_Leave_Common_HalfDay 				= "Halv dag";
	$lang_Leave_Common_LeaveTaken 			= "Fravær taget";
	$lang_Leave_Common_LeaveAvailable 		= "Fravær til rådighed";
	$lang_Leave_Common_LeaveTypeName 		= "Navn for fraværstype";
	$lang_Leave_Common_LeaveTypeId 			= "Id for fraværstype";
	$lang_Leave_Common_Select 				= "Vælg";
	$lang_Leave_Common_oldLeaveTypeName 	= "Eksisterende navn for fraværstype";
	$lang_Leave_Common_newLeaveTypeName 	= "New Leave Type Name";
	$lang_Leave_Common_EmployeeName 		= "Medarbejdernavn";
	$lang_Leave_Common_LeaveEntitled 		= "Fravær berettiget";
	$lang_Leave_Common_Year 				= "År";
	$lang_Leave_Common_ListOfTakenLeave 	= "Liste over fravær";
	$lang_Leave_Common_Remove 				= "Fjern";
	$lang_Leave_Common_AllEmployees			= "Alle medarbejdere";
	$lang_Leave_Common_All					= "Alle";

	$lang_Leave_Common_NameOfHoliday 		= "Name of Holiday";
	$lang_Leave_Common_Recurring 			= "Repeats annually";

 	$lang_Leave_Leave_list_Title1 			= "Godkend fravær";
	$lang_Leave_Leave_Requestlist_Title1 	= "Godkend fraværsanmodning fra #employeeName";
	$lang_Leave_Leave_list_Title2 			= "Fravær taget af #employeeName i #dispYear";
	$lang_Leave_Leave_list_Title3 			= "Fraværsliste";
	$lang_Leave_Select_Employee_Title 		= "Vælg medarbejder";
	$lang_Leave_Leave_Summary_Title 		= "Fraværsoversigt";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Fraværsoversigt for #employeeName i #dispYear";
	$lang_Leave_Select_Employee_Title 		= "Vælg medarbejder";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Medarbejderoversigt for #dispYear";
	$lang_Leave_Leave_Summary_SUP_Title 	= "Medarbejderoversigt for #employeeName for #dispYear";
	$lang_Leave_Define_leave_Type_Title 	= "Definer fraværstype";
	$lang_Leave_Leave_Type_Summary_Title 	= "Fraværstyper";
	$lang_Leave_Leave_Holiday_Specific_Title = "Definer fridage : Specifikke helligdage";
	$lang_Leave_Leave_Holiday_Weeked_Title 	= "Definer fridage : Weekend";

	$lang_bankInformation_code 				= "Kode";
	$lang_bankInformation_description 		= "Beskrivelse";

	$lang_compstruct_add 			= "Tilføj";
	$lang_compstruct_delete 		= "Slet";
	$lang_compstruct_clear 			= "Ryd";
	$lang_compstruct_hide 			= "Skjul";
	$lang_compstruct_save 			= "Gem";

 	$lang_comphire_heading 							= "Virksomhedens Hierarki: Company Information";
	$lang_comphire_relationalhierarchy 				= "Relational Hierarchy";
	$lang_comphire_employee 						= "Medarbejder";
	$lang_comphire_definitionlevel 					= "Definitionsniveau";
	$lang_comphire_telephone 						= "Telefon";
	$lang_comphire_fax 								= "Fax";
	$lang_comphire_email							= "Email";
	$lang_comphire_url 								= "URL";
	$lang_comphire_logo 							= "Logo";
	$lang_comphire_selecthie 						= "Vælg hierarki";
	$lang_comphire_selectdef 						= "Vælg Def. Level";
	$lang_compstruct_heading 						= "Virksomhedsinfo : Organisationsstruktur";
	$lang_compstruct_frmSub_divisionHeadingAdd 		= "Tilføj en under-division til";
	$lang_compstruct_frmSub_divisionHeadingEdit 	= "Redigér";
	$lang_compstruct_Name 							= "Navn";
	$lang_compstruct_Type 							= "Type";
	$lang_compstruct_Division 						= "Division";
	$lang_compstruct_Description 					= "Beskrivelse";
	$lang_compstruct_Department 					= "Afdeling";
	$lang_compstruct_Team 							= "Team";
	$lang_compstruct_Other 							= "Andet";
	$lang_compstruct_Location 						= "Lokation";
	$lang_compstruct_frmNewLocation 				= "Definer en ny lokation";
	$lang_compstruct_Address 						= "Addresse";
	$lang_compstruct_country						= "Land";
	$lang_compstruct_state 							= "Stat / kommune";
	$lang_compstruct_city 							= "By";
	$lang_compstruct_ZIP_Code 						= "Postnummer";
	$lang_compstruct_Phone 							= "Telefon";
	$lang_compstruct_no_root 						= "Rod ikke fundet! Definer venligst Rod";

 	$lang_corptit_heading 							= "Jobtitelr : Job Info";
	$lang_corptit_topinhierachy 					= "Top i hierarkiet";
	$lang_corptit_multipleheads 					= "Flertallig";
	$lang_corptit_headcount 						= "Optælling";
	$lang_corptit_nextlevelupgrade 					= "Næste niveau opgradering";
	$lang_corptit_selectcor 						= "Vælg Jobtitel";
	$lang_corptit_salarygrade 						= "Løntrin";
	$lang_corptit_selectsal 						= "Vælg løntrin";

 	$lang_costcenters_heading 						= "Omkostningscenter : Virksomhedsinfo";

 	$lang_countryinformation_heading 				= "Landeinfo : Geografisk info";

 	$lang_currencytypes_heading 					= "Valuta type : Job info";


 	$lang_districtinformation_heading 				= "By Info : Geografisk info";
	$lang_districtinformation_selectcounlist 		= "Vælg land";
	$lang_districtinformation_selstatelist 			= "Vælg stat";

 	$lang_eeojobcat_heading 						= "Job : Faggruppe";
	$lang_eeojobcat_description 					= "Titel";

 	$lang_electorateinformation_heading 			= "Vælger info : Geografisk info";
	$lang_emprepinfo_heading						= "Definer medarbejderreporter";

 	$lang_emptypes_heading 					= "Medarbejder typer : Nexus info";
	$lang_emptypes_datelimited 				= "Dato begrænset";
	$lang_emptypes_prefix 					= "Prefix";

 	$lang_empview_heading 					= "Job : Medarbejder status";
	$lang_empview_EmpID 					= "Medarbejder ID";
	$lang_empview_EmpFirstName 				= "Medarbejder fornavn";
	$lang_empview_EmpLastName 				= "Medarbejder efternavn";
	$lang_empview_EmpMiddleName 			= "Medarbejder mellemnavn";
	$lang_empview_search					= "Søg";
	$lang_empview_searchby 					= "Søg ved:";
	$lang_empview_description 				= "Søg efter:";
	$lang_empview_norecorddisplay 			= "Ingen registreringer at vise";
	$lang_empview_previous 					= "Foregående";
	$lang_empview_next 						= "Næste";
	$lang_empview_employeeid 				= "Medarbejder ID";
	$lang_empview_employeename 				= "Medarbejder navn";
	$lang_empview_ADD_SUCCESS 				= "Tilføjelse succesfuld";
	$lang_empview_UPDATE_SUCCESS 			= "Opdatering succesfuld";
	$lang_empview_DELETE_SUCCESS 			= "Sletningen succesfuld";
	$lang_empview_ADD_FAILURE 				= "Tilføjelsen fejlede";
	$lang_empview_UPDATE_FAILURE 			= "Opdateringen fejlede";
	$lang_empview_DELETE_FAILURE 			= "Sletningen fejlede";
	$lang_empview_Language 					= "Sprog";
	$lang_empview_WorkExperience 			= "Erhvervserfaring";
	$lang_empview_Payment 					= "Betaling";
	$lang_empview_Skills 					= "Færdigheder";
	$lang_empview_Licenses 					= "Licenser";
	$lang_empview_EmployeeInformation 		= "Medarbejder info";
	$lang_empview_Memberships 				= "Medlemskaber";
	$lang_empview_Report 					= "Reporter";
	$lang_empview_ReportTo					= "Reporter til";
	$lang_empview_SubDivision				= "Underdivision";
	$lang_empview_JobTitle					= "Jobtitel";
	$lang_empview_Supervisor 				= "Overordnede";



 	$lang_ethnicrace_heading 				= "Nationalitet og race : Etniske racer";

 	$lang_extracurractcat_heading 			= "Ekstra aktiviteter - kategori : Info om kvalifikationer";

 	$lang_extracurractinfo_heading 			= "Ekstra aktiviteter - info : Info om kvalifikationer";
	$lang_extracurractinfo_extracuaccat 	= "Ekstra aktiviteter - kategori";
	$lang_extracurractinfo_selectsecucat 	= "Vælg kategori for ekstra aktiviteter";

 	$lang_geninfo_heading 					= "Virksomhedsinfo : Generelt";
	$lang_geninfo_compname 					= "Virksomhedsnavn";
	$lang_geninfo_taxID 					= "Skatte ID";
	$lang_geninfo_naics 					= "NAICS";
	$lang_geninfo_err_CompanyName 			= "Virksomhedsnavn cannot be blank";
	$lang_geninfo_err_Phone 				= "Ikke et gyldigt telefon/fax nummer";

 	$lang_hierarchydef_heading 				= "Hierarki info : Virksomhedsinfo";

	$lang_hremp_EmpFirstName 				= "Fornavn";
	$lang_hremp_EmpLastName 				= "Efternavn";
	$lang_hremp_EmpMiddleName 				= "Mellemnavn";
	$lang_hremp_nickname 					= "Kaldenavn";
	$lang_hremp_photo 						= "Foto";
	$lang_hremp_ssnno 						= "Personnummer :";
	$lang_hremp_nationality 				= "Nationalitet";
	$lang_hremp_sinno 						= "Andet ID 1";
	$lang_hremp_dateofbirth 				= "Fødselsdag";
	$lang_hremp_otherid 					= "Andet ID 2";
	$lang_hremp_maritalstatus 				= "Partner status";
	$lang_hremp_selmarital					= "--Vælg--";
	$lang_hremp_selectnatio 				= "Vælg nationalitet";
	$lang_hremp_selethnicrace 				= "Vælg etnisk race";
	$lang_hremp_smoker 						= "Ryger";
	$lang_hremp_gender 						= "Køn";
	$lang_hremp_dlicenno 					= "Kørekortnummer";
	$lang_hremp_licexpdate 					= "Kørekort udløbsdato";
	$lang_hremp_militaryservice 			= "Værnepligt";
	$lang_hremp_ethnicrace 					= "Etnisk race ";
	$lang_hremp_jobtitle 					= "Jobtitel";
	$lang_hremp_selempstat 					= "Vælg medarbejderstatus";
	$lang_hremp_eeocategory 				= "Faggruppe";
	$lang_hremp_seleeocat 					= "Vælg faggruppe";
	$lang_hremp_joindate 					= "Tiltrædelsesdato";
	$lang_hremp_SelectJobTitle				= "Vælg Jobtitel";
	$lang_hremp_EmpStatus					= "Medarbejderstatus";
	$lang_hremp_Workstation					= "Arbejdssted";

 	$lang_hremp_dependents 					= "Afhængige";
	$lang_hremp_children 					= "Børn";
	$lang_hremp_relationship 				= "Relation";

	$lang_hremp_street1						= "Vejnavn 1";
	$lang_hremp_street2 					= "Vejnavn 2";

	$lang_hremp_hmtele 						= "Hjemme telefon";
	$lang_hremp_mobile 						= "Mobil";
	$lang_hremp_worktele 					= "Arbejdstelefon";
	$lang_hremp_city 						= "By";
	$lang_hremp_workemail 					= "Arbejdsemail";
	$lang_hremp_otheremail 					= "Anden email";
	$lang_hremp_passport 					= "Pas";
	$lang_hremp_visa 						= "Visa";
	$lang_hremp_citizenship 				= "Borgerskab";
	$lang_hremp_passvisano 					= "Pas/Visa nummer";
	$lang_hremp_issueddate 					= "Udstedelsesdato";
	$lang_hremp_i9status 					= "Status for arbejdstilladelse";
	$lang_hremp_dateofexp 					= "Udløbsdato";
	$lang_hremp_i9reviewdate 				= "Fornyelsesdato for arbejdstilladelse";
	$lang_hremp_path 						= "Sti";
	$lang_hremp_filename 					= "Filnavn";
	$lang_hremp_size 						= "Størrelse";
	$lang_hremp_type 						= "Type";
	$lang_hremp_name 						= "Navn";
	$lang_hremp_largefileignore				= "Max 1Mb, større vedhæftelser vil blive ignoreret";
	$lang_lang_uploadfailed					= "Upload Failed";

	$lang_hremp_browse						= "Søg i";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "Er du sikker på at du ønsker at slette dette billede";

	$lang_hremp_SelectAPhoto				= "Vælg et foto";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "Udstedelsdato bør være før udløbsdato";
	$lang_hremp_FromDateShouldBeBeforeToDate= "Fra-dato bør være før til-dato";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "Startdato bør være før slutdato";

	$lang_hremp_ContractExtensionStartDate	= "Kontraktforlængelsens startdato";
	$lang_hremp_ContractExtensionEndDate	= "Kontraktforlængelsens slutdato";
	$lang_hremp_EmployeeContracts 			= "Medarbejderkontrakter";
	$lang_hremp_AssignedContracts			= "Tildelte kontrakter";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "Løn bør være inde for minimum og maximum";

	$lang_hremp_SelectCurrency				= "Vælg valuta";

	$lang_hremp_ContractExtensionId			= "Kontraktforlængesles ID";
	$lang_hremp_ContractStartDate			= "Kontraktens startdato";
	$lang_hremp_ContractEndDate				= "Kontraktens slutdato";
    $lang_hremp_FieldShouldBeNumeric        = "Feltet bør være nummerisk";

    $lang_hremp_Language					= "Sprog";

 	$lang_hremplan_employeelanguageflu 		= "Sprog niveau";
	$lang_hremplan_fluency 					= "Niveau";
	$lang_hremplan_ratinggarde 				= "Niveau";
	$lang_hremplan_assignlanguage 			= "Tildelte sprog";

	$lang_hremplan_SelectLanguage			= "Vælg sprog";
	$lang_hremplan_SelectFluency			= "Vælg niveau";
	$lang_hremplan_SelectRating 			= "Vælg rating";

	$lang_hremp_PleaseSelectJobTitle= "Vælg venligst en jobtitel for denne medarbejder (her) ";

	$lang_hremp_ie_CurrentSupervisors 		= "Det vil sige medarbejderens nuværende supervisorer";
	$lang_hremp_ie_CurrentSubordinates 		= "Det vil sige medarbejderens nuværende underordnede";

 	$lang_hremplicenses_employeelicen 		= "Medarbejderens licenser";
	$lang_hremplicenses_assignlicenses 		= "Tildelte licenser";
	$lang_hremplicenses_licentype 			= "Licenstype";
	$lang_hremplicenses_assignlicen 		= "Tildelte licenser";

	$lang_hremplicenses_NoLicenseSelected	= "Ingen licenser valgt";
	$lang_hremplicenses_SelectLicenseType	= "Vælg licens type";

	$lang_hremp_SelectAtLEastOneAttachment = "Vælg mindst en vedhæftning der skal slettes";

 	$lang_hrEmpMain_ratinggarde 			= "Kompetencer";
	$lang_hrEmpMain_assignlanguage 			= "tildelte sprog";
	$lang_hrEmpMain_Writing 				= "Skriftlig";
	$lang_hrEmpMain_Speaking 				= "Tale";
	$lang_hrEmpMain_Reading 				= "Læse";
	$lang_hrEmpMain_Poor 					= "Dårlig";
	$lang_hrEmpMain_Basic 					= "Middel";
	$lang_hrEmpMain_Good 					= "God";
	$lang_hrEmpMain_MotherTongue 			= "Modersmål";
	$lang_hrEmpMain_Individual 				= "Individuelt";
	$lang_hrEmpMain_employeemembership 		= "Medarbejderens medlemskaber";
	$lang_hrEmpMain_subownership 			= "Ejer af abonnement";
	$lang_hrEmpMain_subamount 				= "Abonnementets størrelse";
	$lang_hrEmpMain_subcomdate 				= "Abonnementets startdato";
	$lang_hrEmpMain_subredate 				= "Abonnementets fornyelsesdato";
	$lang_hrEmpMain_selmemtype 				= "Vælg medlemsskabstype";
	$lang_hrEmpMain_selmemship 				= "Vælg medlemsskab";
	$lang_hrEmpMain_selownership 			= "Vælg ejerskab";
	$lang_hrEmpMain_assignmemship 			= "Tildelt medlemsskab";
	$lang_hrEmpMain_paygrade 				= "Lønniveau";
	$lang_hrEmpMain_currency 				= "Valuta";
	$lang_hrEmpMain_minpoint 				= "Minimum løn";
	$lang_hrEmpMain_maxpoint 				= "Maximum løn";
	$lang_hrEmpMain_bassalary 				= "Basisløn";
	$lang_hrEmpMain_assignedsalary 			= "Tildelt løn";

 	$lang_hrEmpMain_heading 				= "Medarbejder raport";
	$lang_hrEmpMain_supervisorsubordinator 	= "Overordnede / Underordnet";
	$lang_hrEmpMain_reportingmethod 		= "Reportingsmetode";
	$lang_hrEmpMain_subordinateinfomation 	= "Underornede";
	$lang_hrEmpMain_supervisorinfomation 	= "Overordnede";
	$lang_hrEmpMain_selecttype 				= "Vælg metode";
	$lang_hrEmpMain_Direct 					= "Direkte";
	$lang_hrEmpMain_Indirect 				= "Indirekte";
	$lang_hrEmpMain_employeeskill 			= "Medarbejderens færdigheder";
	$lang_hrEmpMain_yearofex 				= "Års erfaring";
	$lang_hrEmpMain_assignskills 			= "Tildelte færdigheder";
	$lang_hrEmpMain_employerworkex 			= "Medarbejders erhvervserfaring";
	$lang_hrEmpMain_employer 				= "Medarbejder";
	$lang_hrEmpMain_enddate 				= "Slut dato";
	$lang_hrEmpMain_startdate 				= "Start dato";
	$lang_hrEmpMain_assignworkex 			= "Tildelt erhvervserfaring";
	$lang_hrEmpMain_workexid 				= "Erhvervserfaring ID";
	$lang_hrEmpMain_internal 				= "Intern";
	$lang_hrEmpMain_major 					= "Linje/Specialisering";
	$lang_hrEmpMain_education 				= "Uddannelse";
	$lang_hrEmpMain_gpa 					= "Karaktergennemsnit";
	$lang_hrEmpMain_assigneducation 		= "Tildelt Uddannelse";
	$lang_hrEmpMain_assignattach 			= "Tildelte vedhæftelser";

	$lang_hrEmpMain_SelectEducation			= "Vælg uddannelse";
	$lang_hrEmpMain_YearsOfExperiencCannotBeBlank = "Års erfaring kan ikke efterlades blank";
	$lang_hrEmpMain_YearsOfExperiencWrongFormat = "Års erfaring  - Forkert input format";
	$lang_hrEmpMain_YearsOfExperiencBetween = "Års erfaring  skal have en værdi inden for #range";
	$lang_hrEmpMain_Skill 					= "Færdighed";

	$lang_hrEmpMain_subown_Company = 'Virksomhed';
	$lang_hrEmpMain_subown_Individual = 'Individuel';

	$lang_hrEmpMain_arrRepType_Supervisor = 'Supervisor';
	$lang_hrEmpMain_arrRepType_Subordinate = 'Underordned';

	$lang_hrEmpMain_arrRepMethod_Direct = 'Direkte';
	$lang_hrEmpMain_arrRepMethod_Indirect = 'Indirekte';

	$lang_hrEmpMain_SelectMethod = 'Vælg Metode';

	$lang_hrEmpMain_SubscriptionAmountShouldBeNumeric = "Abonnementets beløb bør være nummerisk";
	$lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue = "Abonnementets beløb for medlemskabet er tomt - ønsker du at fortsætte?";

	$lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate = "Startdato skal være før fornyelsesdato";

	$lang_hrEmpMain_membershiptype = "Medlemskabstype";
	$lang_hrEmpMain_membership = "Medlemskab";

 	$lang_jobtitle_heading 					= "Job : Jobtitel";
	$lang_jobtitle_jobtitid 				= "Jobtitel ID";
	$lang_jobtitle_jobtitname 				= "Jobtitel navn";
	$lang_jobtitle_jobtitdesc 				= "Job beskrivelse";
	$lang_jobtitle_jobtitcomments 			= "Jobtitel kommentarer";
	$lang_jobtitle_addpaygrade 				= "Tilføj lønniveau";
	$lang_jobtitle_emstatExpl 				= "Definer medarbejderstatus tilladt denne Jobtitel";
	$lang_jobtitle_editpaygrade 			= "Rediger løn niveau";
	$lang_jobtitle_addempstat 				= "Tilføj medarbejderstatus";
	$lang_jobtitle_editempstat 				= "Rediger medarbejderstatus";
	$lang_jobtitle_empstat 					= "Medarbejderstatus";

 	$lang_languageinformation_heading 		= "Færdigheder : sprog";

 	$lang_licenses_heading 					= "Kvalifikationer : Licenser";

 	$lang_locations_heading 				= "Virksomhedsinfo : Lokationer";

 	$lang_membershipinfo_heading 			= "Medlemskaber : Medlemskab";
	$lang_membershipinfo_membershiptype 	= "Medlemskabstype";
	$lang_membershipinfor_selectmember 		= "Vælg medlemskab";

 	$lang_membershiptypes_heading 			= "Medlemskaber : Medlemskabstype";

 	$lang_nationalityinformation_heading 	= "Nationalitet & race : Nationalitet";

 	$lang_provinceinformation_heading 		= "Stat / Kommune Information : Geografisk Information";

 	$lang_qualificationtypes_heading 		= "Kvalifikationstyper : kvalifikationsinfo";
	$lang_repview_ReportID 					= "Raport ID";
	$lang_repview_ReportName 				= "Raport navn";
	$lang_repview_ViewEmployeeReports 		= "Se medarbejderraporter";
	$lang_repview_message 					= "Sletning kan påvirke virksomhedens hierarki";
	$lang_repview_DefineEmployeeReports 	= "Definer medarbejderreporter";
	$lang_repview_message2 					= "Sletning kan påvirke visning af raporter";

 	$lang_routeinformation_heading 			= "Ruteinformation : Ruteinformation";
	$lang_salarygrades_heading 				= "Job : Lønniveau";
	$lang_salarygrades_currAss 				= "Tildelt valuta";
	$lang_salarygrades_stepSal 				= "Stigningsniveau";
	$lang_salcurrdet_heading 				= "Valuta tildelt til lønniveau";
	$lang_salcurrdet_assigncurr 			= "Tildelt valuta";

 	$lang_satutoryinfo_heading 				= "Lovmæssig type : Nexus Information";

 	$lang_view_EmploymentStatusID 			= "Medarbejderstatus ID";
	$lang_view_EmploymentStatusName 		= "Medarbejderstatus navn";
	$lang_view_message 						= "Sletning kan påvirke Jobtitelr";
	$lang_view_message1 					= "Sletning kan påvirke lønniveau for medarbejder i PIM modul";
	$lang_view_SkillName 					= "Færdighedsnavn";
	$lang_view_SkillID 						= "Færdighed ID";
	$lang_view_message3 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_LocationName 				= "Lokation";
	$lang_view_message4 					= "Sletning kan påvirke virksomhedens hierarki. Hvis lokationen har associationer kan sletningen mislykkes";
	$lang_view_CurrencyName 				= "Valutanavn";
	$lang_view_CurrencyTypes 				= "Valutatyper";
	$lang_view_message5 					= "Sletning kan påvirke detaljer for lønnens valuta";
	$lang_view_CompanyHierarchy 			= "Virksomhedens hierarki";
	$lang_view_CompanyHierarchyName 		= "Navn for virksomhedens hierarki";
	$lang_view_message6 					= "Sletning kan påvirke medarbejder information";
	$lang_view_QualificationType 			= "Kvalifikationstype";
	$lang_view_QualificationName 			= "Kvalifikationsnavn";
	$lang_view_message9 					= "Sletning kan påvirke kvalifikationer, kvalifikationsbetegnelse, medarbejder kvalifikationer";
	$lang_view_RatingMethod 				= "Bedømmelsesmetode";
	$lang_view_RatingMethodName 			= "Navn for bedømmelsesmetode";
	$lang_view_message10 					= "Sletning kan påvirke kvalifikationer og sprog";
	$lang_view_CorporateTitle 				= "Jobtitel";
	$lang_view_CorporateTitleName 			= "Navn for jobtitel";
	$lang_view_message11 					= "Sletning kan påvirke medabejderinformation, betegnelser";
	$lang_view_ExtraCategory 				= "Ekstra aktiviteter - kategori";
	$lang_view_ExtraCategoryName 			= "Ekstra aktiviteter - navn";
	$lang_view_message12 					= "Sletning kan påvirke ekstra aktiviteter, Medarbejders ekstra aktiviteter ";
	$lang_view_MembershipTypeName 			= "Medlemskabstype - navn";
	$lang_view_message13 					= "Sletning kan påvirke medarbejders medlemskab, medlemskab";
	$lang_view_EmployeeCategoryName 		= "Medarbejderkategori - navn";
	$lang_view_message14 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_EmployeeGroupName 			= "Medarbejdergruppenavn";
	$lang_view_message15 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_NationalityName 				= "Nationalitet";
	$lang_view_message16 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_CountryID 					= "Lande ID";
	$lang_view_CountryName 					= "Landenavn";
	$lang_view_message17 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_HierarchyDefinition 			= "Hierarkidefinition";
	$lang_view_HierarchyDefinitionName 		= "Hierarkidefinition - navn";
	$lang_view_message18 					= "Sletning kan påvirke virksomhedshierarki";
	$lang_view_StateProvinceName 			= "Stat/ kommune - navn";
	$lang_view_message19 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_CityName 					= "Bynavn";
	$lang_view_message20 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_LanguagName 					= "Sprognavn";
	$lang_view_message21 					= "Sletning kan påvirke employee language";
	$lang_view_Membership 					= "Medlemskab";
	$lang_view_MembershipType 				= "Medelemskabstype";
	$lang_view_MembershipName 				= "Medelemskabsnavn";
	$lang_view_message22 					= "Sletning kan påvirke medarbejders medlemskab";
	$lang_view_ExtraActivities 				= "Ekstra aktiviteter";
	$lang_view_ExtraActivitiesName 			= "Ekstra aktiviteter - Navn";
	$lang_view_message23 					= "Sletning kan påvirke medarbejders ekstra aktiviteter";
	$lang_view_PayGradeName 				= "Lønniveau - Navn";
	$lang_view_message24 					= "Sletning kan påvirke medarbejder information, Jobtitelr";
	$lang_view_message25 					= "Sletning kan påvirke uddannelse";
	$lang_view_EmployeeTypeID 				= "Medarbejdertype ID";
	$lang_view_EmployeeTypeName 			= "Medarbejdertype - Navn";
	$lang_view_message26 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_EEOJobCategory 				= "Faggruppe";
	$lang_view_EEOJobCategoryid 			= "Faggruppe Id";
	$lang_view_EEOJobCategoryName 			= "Faggruppe";
	$lang_view_message27 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_message28 					= "Sletning kan påvirke medarbejdersprog";
	$lang_view_EthnicRace 					= "Etnisk race";
	$lang_view_EthnicRaceName 				= "Etnisk race - navn";
	$lang_view_message29 					= "Sletning kan påvirke employee records";
	$lang_view_message30 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_message31 					= "Sletning kan påvirke medarbejderinformation, Jobtitler";
	$lang_view_message32 					= "Sletning kan påvirke kvalifikationer, Kvalifikationsbetegnelse, Medarbejderkvalifikationer";
	$lang_view_License_ID 					= "Licens ID";
	$lang_view_LicenseDescription 			= "Licens beskrivelse";
	$lang_view_message33 					= "Sletning kan påvirke medarbejderinformation";
	$lang_view_UserID 						= "Bruger ID";
	$lang_view_UserName 					= "Brugernavn";
	$lang_view_message34 					= "Sletning kan gøre OrangeHRM ubrugeligt";
	$lang_view_UserGroupID 					= "Brugergruppe ID";
	$lang_view_UserGroupName 				= "Brugergruppe navn";
	$lang_view_message35 					= "Sletning kan gøre OrangeHRM ubrugeligt";

	$lang_view_Users 				= "Brugere";
	$lang_view_UserGroups 			= "Admin Brugergrupper";
	$lang_view_HRAdmin 				= "HR Admin";
	$lang_view_ESS 					= "Selvbetjening";
	$lang_view_ID 					= "ID";

	//Customer
	$lang_view_CustomerId = "Kunde Id";
	$lang_view_CustomerName = "Kundenavn";

	$lang_index_WelcomeMes 			= "Velkommen #username";
	$lang_index_ChangePassword 		= "Skift kode";
	$lang_index_Logout 				= "Log af";

	$lang_MailFrom = "Mail sendt som";
	$lang_MailSendingMethod = "Forsendelsesmetode";
	$lang_MailTypes_Mail = "Intern PHP funktion";
	$lang_MailTypes_Sendmailer = "Mail sendt";
	$lang_MailTypes_Smtp = "SMTP";
	$lang_SendmailPath = "Path til Sendmail";
	$lang_SmtpHost = "SMTP Host";
	$lang_SmtpPort = "SMTP Port";
	$lang_SmtpUser = "SMTP Bruger";
	$lang_SmtpPassword = "SMTP Kode";
	//Projects
	$lang_view_ProjectId = "Project Id";
	$lang_view_ProjectName = "Projectnavn";

	// PIM tab labels
	$lang_pim_tabs_Personal = "Personlig";
	$lang_pim_tabs_Contact = "Kontakt";
	$lang_pim_tabs_EmergencyContacts = "Kontakter ved nødsituation";
	$lang_pim_tabs_Dependents = "Afhængige";
	$lang_pim_tabs_Immigration = "Immigration";
	$lang_pim_tabs_Job = "Job";
	$lang_pim_tabs_Payments = "Betalinger";
	$lang_pim_tabs_ReportTo = "Reporter-til";
	$lang_pim_tabs_WorkExperience = "Erhvervs- erfaring";
	$lang_pim_tabs_Education = "Uddannelse";
	$lang_pim_tabs_Skills = "Færdigheder";
	$lang_pim_tabs_Languages = "Sprog";
	$lang_pim_tabs_License = "Licenser";
	$lang_pim_tabs_Membership = "Medlem- skaber";
	$lang_pim_tabs_Attachments = "Vedhæftelser";

	// Report module
	$lang_rep_SelectionCriteria = "Valgkriterier";
	$lang_rep_EmpNo = "Medarbejder nummer";
	$lang_rep_AgeGroup = "Aldersgrupper";
	$lang_rep_PayGrade = "Lønniveau";
	$lang_rep_Education = "Uddanelse";
	$lang_rep_EmploymentStatus = "Medarbejderstatus";
	$lang_rep_ServicePeriod = "Service periode";
	$lang_rep_JobTitle = "Jobtitel";

	$lang_rep_LastName = "Efternavn";
	$lang_rep_FirstName = "Fornavn";
	$lang_rep_Address = "Addresse";
	$lang_rep_TelNo = "Telefon nummer";
	$lang_rep_DateOfBirth = "Fødselsdato";
	$lang_rep_JoinDate = "Tiltrædelsesdato";
	$lang_rep_Qualification = "Kvalifikationer";
	$lang_rep_EmployeeStates = "Medarbejderstatus";
	$lang_rep_JoinedDate = "Tiltrædelsesdato";

	$lang_rep_SelectPayGrade = "Vælg lønniveau";
	$lang_rep_SelectEducation = "Vælg uddannelsestype";
	$lang_rep_SelectEmploymentType = "Vælg medarbejdertype";
	$lang_rep_SelectComparison = "Vælg sammenligning";
	$lang_rep_SelectJobTitle = "Vælg jobtitler";

	$lang_rep_Field = "Felt";
	$lang_rep_AssignUserGroups = "Tildel brugergrupper";
	$lang_rep_AssignedUserGroups = "Tildelte brugergrupper";

	$lang_rep_SelectAtLeastOneCriteriaAndOneField = "Vælg mindst ét kriterie og ét felt";
	$lang_rep_SelectTheComparison = "Vælg sammenligningen";

	$lang_rep_AgeShouldBeNumeric = "Alder bør være nummerisk";
	$lang_rep_InvalidAgeRange = "Anden valgte alder er størrre end første valgte alder";

	$lang_rep_FieldNotSelected = "Felt er ikke valgt";

	$lang_rep_DateShouldBeNumeric = "Dato skal være nummerisk";

	$lang_rep_Report = "Reporter";
	$lang_rep_EmployeeNo = "Medarbejdernummer";
	$lang_rep_EmployeeFirstName = "Medarbejder - Fornavn";
	$lang_rep_EmployeeLastName = "Medarbejder - Efternavn";
	$lang_rep_ReportTo = "Reporter til";
	$lang_rep_ReportingMethod = "Reporteringsmetode";
	$lang_rep_Address = "Addresse";
	$lang_rep_Telephone = "Telefon";
	$lang_rep_DateOfBirth = "Fødselsdag";
	$lang_rep_Skills = "Færdigeheder";
	$lang_rep_SalaryGrade = "Lønnivieau";
	$lang_rep_EmployeeStatus = "Medabejderstatus";
	$lang_rep_JoinedDate = "Tiltrædelsesdato";
	$lang_rep_SubDivision = "Underdivision";
	$lang_rep_JobTitle = "Jobtitel";
	$lang_rep_YearOfPassing = "År";
	$lang_rep_Contract = "Kontrakt";
	$lang_rep_WorkExperience = "Erhvervserfaring";

	$lang_rep_SelectAtLeaseOneUserGroupToAssign = "Vælg og tilføj mindst én brugergruppe check box";

	$lang_rep_ReportDefinition = "Reporteringsdefinition";
	$lang_rep_AssignUserGroups = "Tildel brugergrupper";
	$lang_rep_UserGroups = "Brugergrupper";
	$lang_rep_NoUserGroupsAssigned = "Ingen brugergrupper tildelt";

	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "Mail konfigurering";
	$lang_Admin_SubscribeToMailNotifications = "Tilmeld til E-mail beskeder";
	$lang_Admin_ENS_LeaveApplications = "Fraværsansøgniger";
	$lang_Admin_ENS_LeaveApprovals = "Fraværsgodkendelser";
	$lang_Admin_ENS_LeaveCancellations = "Fraværsannulleringer";
	$lang_Admin_ENS_LeaveRejections = "Fraværsafslag";

	//Users
	$lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong = "Brugernavn skal være på mindst 5 tegn";
	$lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong = "Kode skal være på mindst 4 tegn";
	$lang_Admin_Users_ErrorsPasswordMismatch = "Kode passer ikke";
	$lang_Admin_Users_Errors_EmployeeIdShouldBeDefined = "Medarbejder ID skal defineres";
	$lang_Admin_Users_Errors_FieldShouldBeSelected = "Felt skal vælges";
	$lang_Admin_Users_Errors_ViewShouldBeSelected = "Vis skal vælges";
	$lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Koder matcher ikke. Indtast ny kode igen";

	$lang_Admin_Users_Errors_DoYouWantToClearRights = "Ønsker du at ryde rettigheder";
	$lang_Admin_Users_Errors_SameGroup = "Din login account tilhører den samme brugergruppe, du kan ikke skifte rettigheder for denne gruppe";
	$lang_Admin_Users_Errors_NameCannotBeBlank = "Navn kan ikke efterlades blank";

	$lang_Admin_Users_Modules = "Moduler";
	$lang_Admin_Users_Module = "Modul";
	$lang_Admin_Users_RightsAssignedToUserGroups = "Rettigheder tildelt brugergruppe";
	$lang_Admin_Users_UserGroup = "Brugergruppe";
	$lang_Admin_Users_UserGroupId = "Brugergruppe ID";

	$lang_Admin_Users_UserName = "Brugernavn";
	$lang_Admin_Users_Password = "Kode";

	$lang_Admin_Users_ConfirmPassword = "Bekræft kode";

	$lang_Admin_Users_Status = "Status";
	$lang_Admin_Users_Enabled = "Aktiv";
	$lang_Admin_Users_Disabled = "Inaktiv";

	$lang_Admin_Users_UserGroup = "Admin brugergruppe";
	$lang_Admin_Users_SelectUserGroup = "Vælg brugergruppe";

	$lang_Admin_Users_NewPassword = "Ny kode";
	$lang_Admin_Users_ConfirmNewPassword = "Bekræft ny kode";

	$lang_Admin_Users_add = "Tilføj";
	$lang_Admin_Users_delete = "Slet";
	$lang_Admin_Users_view = "Vis";
	$lang_Admin_Users_edit = "Regiger";

	$lang_Admin_Users_AssignedRights = "Tildelte rettigheder";
	$lang_Admin_Users_DefineReports = "Definer raporter";

	$lang_Admin_Change_Password_OldPassword = "Gammel kode";

	$lang_Admin_Change_Password_Errors_EnterYourOldPassword = "Indtast gammel kode";
	$lang_Admin_Change_Password_Errors_EnterYourNewPassword = "Indtast ny kode";
	$lang_Admin_Change_Password_Errors_RetypeYourNewPassword = "Indtast ny kode igen";
	$lang_Admin_Change_Password_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Koder matcher ikke. Indtast ny kode igen";
	$lang_Admin_Change_Password_Errors_YourOldNewPasswordsAreEqual = "Din gamle og nye kode er den samme";

	//timesheet

	$lang_Time_Timesheet_TimesheetForViewTitle = "Tidsskema for #periodName med start #startDate";
	$lang_Time_Timesheet_TimesheetForEditTitle = "Rediger tidsskema for #periodName med start #startDate";

	$lang_Time_Timesheet_TimesheetNameForViewTitle = "Tidsskema for #name for #periodName med start #startDate";
	$lang_Time_Timesheet_TimesheetNameForEditTitle = "Rediger tidsskema for #name for #periodName med start #startDate";

	$lang_Time_Timesheet_Status = "Status: #status";

	$lang_Time_Timesheet_Status_NotSubmitted = "Ikke indgivet";
	$lang_Time_Timesheet_Status_Submitted = "Indgivet";
	$lang_Time_Timesheet_Status_Approved = "Godkendt";
	$lang_Time_Timesheet_Status_Rejected = "Afvist";

	$lang_Time_Timesheet_Customer = "Kunde";
	$lang_Time_Timesheet_ProjectActivity = "Projekt / Aktivitet";

	$lang_Time_Timesheet_Total = "Total";

	$lang_Time_Errors_SUBMIT_SUCCESS = "Indgivelse vellykket";
	$lang_Time_Errors_SUBMIT_FAILURE = "Indgivelse fejlede";
	$lang_Time_Errors_UPDATE_SUCCESS = "Successfult opdateret";
	$lang_Time_Errors_UPDATE_FAILURE = "Opdatering fejlede";
	$lang_Time_Errors_CANCEL_SUCCESS = "Successfult annulleret";
	$lang_Time_Errors_CANCEL_FAILURE = "Annullering fejlede";
	$lang_Time_Errors_APPROVE_SUCCESS = "Successfult godkendt";
	$lang_Time_Errors_APPROVE_FAILURE = "Godkendelse fejlede";
	$lang_Time_Errors_REJECT_SUCCESS = "Successfult afvist";
	$lang_Time_Errors_REJECT_FAILURE = "Afvisning fejlede";
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "Uautoriseret commando";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "Ingen tidsskemaer fundet";

	$lang_Time_Errors_EncounteredTheFollowingProblems = "Følgende problemer opstod";
	$lang_Time_Errors_ReportedDateNotSpecified = "Raporteret dato ikke specificeret";
	$lang_Time_Errors_ProjectNotSpecified = "Project ikke specificeret";
	$lang_Time_Errors_CustomerNotSpecified = "Kunde ikke specificeret";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified = "Ugyldig tid eller nul eller negativt interval specificeret";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval = "Varighed og interval kan ikke specificeres medmindre de matcher";

	$lang_Time_Timesheet_StartTime = "Start tid";
	$lang_Time_Timesheet_EndTime = "Sluttid";
	$lang_Time_Timesheet_ReportedDate = "Raporteret dato";
	$lang_Time_Timesheet_Duration = "Varighed";
	$lang_Time_Timesheet_Decription = "Beskrivelse";
	$lang_Time_Timesheet_NoCustomers = "Ingen kunder";
	$lang_Time_Timesheet_NoProjects = "Ingen projekter";

	$lang_Time_Select_Employee_Title = "Vælg medarbejder";

	$lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval = "Indgivede tidsskemaer afventer overordnedes godkendelse";
	$lang_Time_Select_Employee_WeekStartingDate = "Ugens start #date";

	$lang_Time_Select_Employee_TimesheetPeriod = "Tidsskemas periode";

	$lang_Time_Errors_PleaseAddAComment = "Tilføj venligst kommentar";
?>
