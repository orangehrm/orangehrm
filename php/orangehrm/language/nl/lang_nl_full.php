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
 * @author: Tobias Peereboom <tobias@peereboom.eu>
 * @author: Sam Verkoelen <sam@zomerparkfeest.nl>
 * @author: Age de Jong <age.dejong@qualitycontacts.nl>
 *
 */

	/**
	 * Error messages
	 *
	 */

	$lang_Error_PleaseCorrectTheFollowing 			= "Aub het volgende corrigeren";
	$lang_Error_SelectAtLeastOneRecordToDelete 		= "Selecteer een veld om te verwijderen";
	$lang_Error_DoYouWantToDelete 					= "Wilt u de gegevens verwijderen?";
	$lang_Error_EnterDate							= "Datum invullen?";

	$lang_Error_PleaseSelectAYear 					= "Een jaar selecteren";
	$lang_Error_PleaseSelectAnEmployee 				= "Een werknemer selecteren";

	$lang_Error_DoYouWantToContinue 				= "Wilt u doorgaan?";

	$lang_Error_PleaseSelectAnEmployee				= "Selecteer een medewerker a.u.b.";

	$lang_Error_ShouldBeNumeric 					= "Moet numeriek zijn";
	$lang_Error_FollowingErrorsWereFound 			= "De volgende fouten zijn gevonden";
	$lang_Error_AreYouSureYouWantToDelete 			= "Weet u zeker dat u de gegevens wilt verwijderen";
	$lang_Error_AccessDenied                        = "Toegang geweigerd";

	//leave
	$lang_Error_PleaseSelectAValidFromDate 			= "aub Selecteer een geldige begindatum";
	$lang_Error_PleaseSelectAValidToDate 			= "aub Selecteer een geldige tot datum op";
	$lang_Error_PleaseSelectALeaveType 				= "aub Selecteer een verlof type";

	$lang_Error_LeaveDateCannotBeABlankValue 		= "Verlofdatum mag geen blanco veld zijn!";
	$lang_Error_NameOfHolidayCannotBeBlank 			= "Naam van de vakantiedag kan niet blanco zijn";

	$lang_Error_NoLeaveTypes 						= "Geen verlof types";
	$lang_Error_NoRecordsFound 						= "Geen velden gevonden!";

	$lang_Error_InvalidDate 						= "Geen geldige data";

	$lang_Error_NonNumericHours						= "Aantal uren moet numeriek zijn";
	$lang_Error_EmailConfigConfirm						= "Email configuratie is niet opgegeven of ongeldig. Wilt u doorgaan? ";
	$lang_Error_EmailConfigError_SendmailNotFound				= "Er is geen bestand gevonden op het opgegeven Sendmail pad";
	$lang_Error_EmailConfigError_SendmailNotExecutable			= "Het bestand onder het opgegeven Sendmail pad is niet uitvoerbaar";
	$lang_Error_EmailConfigError_SmtpHostNotDefined				= "SMTP host is niet gedefinierd";
	//PIM
	$lang_Error_LastNameNumbers						= "De achternaam bevat nummers. Wilt u doorgaan?";
	$lang_Error_FirstNameNumbers					= "Voornaam bevat nummers. Wilt u doorgaan?";
	$lang_Error_MiddleNameNumbers					= "Tussenvoegsel bevat nummers. Wilt u doorgaan?";
	$lang_Error_MiddleNameEmpty						= "Tussenvoegsel niet ingevuld. Wilt u doorgaan?";
	$lang_Error_LastNameEmpty						= "Achternaam niet ingevuld!";
	$lang_Error_FirstNameEmpty						= "Voornaam niet ingevuld!";
	$lang_Error_ChangePane							= "Aub gegevens opslaan voordat u naar een ander pagina gaat.";

	$lang_Error_UploadFailed						= "Upload fout!";
	$lang_Errro_WorkEmailIsNotValid                 = "Het werk e-mail adres is niet geldig";
	$lang_Errro_OtherEmailIsNotValid                = "Het andere e-mail adres is niet geldig";

	$lang_Error_DependantNameEmpty					= "Naam naaste is leeg";

	// Company Structure
	$lang_Error_Company_General_Undefined 				= "Aub eerst de algemene bedrijfsinformatie invullen.";
	$lang_Error_CompStruct_UnitCount 					= "Ook #children onderdelen onder #parent zullen worden verwijderd";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "Bedrijfsstructuur kan gewijzigd worden";

	$lang_Error_SubDivisionNameCannotBeEmpty 			= "Sub-divisie naam niet ingevuld";
	$lang_Error_PleaseSelectATypeOrDefineACustomType 	= "aub Selecteer een type of definieer een klant";
	$lang_Error_CompStruct_LocEmpty 					= "aub Selecteer een locatie of definieer een nieuwe locatie and selecteer";
	$lang_Error_CompStruct_Dept_Id_Invalid				= "Afdelings ID bestaat al. Geef een andere id op";

	$lang_Error_LocationNameEmpty 						= "Locatie naam niet ingevuld";
	$lang_Error_CountryNotSelected 						= "Land niet geselecteerd";
	$lang_Error_StateNotSelected 						= "Provincie niet geselecteerd";
	$lang_Error_CityCannotBeEmpty 						= "Stad niet ingevuld";
	$lang_Error_AddressEmpty 							= "Adres niet ingevuld";
	$lang_Error_ZipEmpty 								= "Postcode niet ingevuld";
	$lang_Error_CompStruct_ZipInvalid 					= "Postcode bevat geen numerieke karakters: #characterList";

	$lang_Error_InvalidEmail 							= "Ongeldig E-mail adres";

	$lang_Error_InvalidDescription						= "Ongeldige omschrijving";

	$lang_Error_FieldShouldBeNumeric					= "Veld moet numeriek zijn";

	$lang_Error_FieldShouldBeSelected					= "Veld moet geselecteerd zijn";
	$lang_Error_SelectAtLeastOneCheckBox 				= "Selecteer minimaal een vak";

	/**
	 * Menu Items
	 *
	 */

	// Home
	$lang_Menu_Home 							= "Home";

	$lang_Menu_Home_Support 					= "Helpdesk";
	$lang_Menu_Home_Forum 						= "Forum";
	$lang_Menu_Home_Blog 						= "Blog";

	$lang_Menu_Ess								= "Persoonlijke informatie";
	$lang_Menu_Pim 								= "PIM";

	// Admin Module
	$lang_Menu_Admin 							= "Admin";

	$lang_Menu_Admin_CompanyInfo 				= "Bedrijfsinformatie";
	$lang_Menu_Admin_CompanyInfo_Gen 			= "Algemeen";
	$lang_Menu_Admin_CompanyInfo_CompStruct 	= "Bedrijfsstructuur";
    $lang_Menu_Admin_Company_Property           = "Eigenschappen bedrijf";
	$lang_Menu_Admin_CompanyInfo_Locations 		= "Locaties";

	$lang_Menu_Admin_Job 						= "Functie";
	$lang_Menu_Admin_Job_JobTitles 				= "Functie titel";
	$lang_Menu_Admin_Job_JobSpecs               = "Functie specificaties";
	$lang_Menu_Admin_Job_PayGrades 				= "Salaris schaal";
	$lang_Menu_Admin_Job_EmpStatus 				= "Dienst status";
	$lang_Menu_Admin_Job_EEO 					= "EEO Baan categorie";

	$lang_Menu_Admin_Quali 						= "Kwalificaties";
	$lang_Menu_Admin_Quali_Education 			= "Opleidingen";
	$lang_Menu_Admin_Quali_Licenses 			= "Certificaten";

	$lang_Menu_Admin_Skills 					= "Vaardigheden";
	$lang_Menu_Admin_Skills_Skills 				= "Vaardigheden";
	$lang_Menu_Admin_Skills_Languages 			= "Talen";

	$lang_Menu_Admin_Memberships 					= "Lidmaatschap";
	$lang_Menu_Admin_Memberships_Memberships 		= "Lidmaatschap";
	$lang_Menu_Admin_Memberships_MembershipTypes 	= "Type lidmaatschap";

	$lang_Menu_Admin_NationalityNRace 				= "Nationaliteit";
	$lang_Menu_Admin_NationalityNRace_Nationality 	= "Nationaliteit";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces 	= "Origine";

	$lang_Menu_Admin_Users 							= "Gebruikers";
	$lang_Menu_Admin_Users_UserGroups 				= "Beheerders groepen";
	$lang_Menu_Admin_Users_HRAdmin 					= "Beheerders";
	$lang_Menu_Admin_Users_ESS 						= "Gebruikers";

	$lang_Menu_Admin_EmailNotifications = "E-mail notificatie";
	$lang_Menu_Admin_EmailConfiguration = "Configuratie";
	$lang_Menu_Admin_EmailSubscribe = "Schrijf in";
	$lang_Menu_Admin_ProjectInfo = "Project informatie";
	$lang_Menu_Admin_Customers = "Klanten";
	$lang_Menu_Admin_Projects = "Projecten";
	$lang_Menu_Admin_DataImportExport = "Data importeren/exporteren";
	$lang_Menu_Admin_DataExport = "exporteren";
	$lang_Menu_Admin_DataExportDefine = "Definieer vrij invulbare export";
	$lang_Menu_Admin_DataImport = "importeren";
	$lang_Menu_Admin_DataImportDefine = "Definieer vrij invulbare import";
	$lang_Menu_Admin_CustomFields = "Vrij invulbare velden";

	// LDAP Module
	$lang_LDAP_Configuration 	= "#ldapType configuratie";
	$lang_LDAP_Configuration 	= "LDAP configuratie";
	$lang_LDAP_Server			= "LDAP server";
	$lang_LDAP_Port				= "LDAP poort";
	$lang_LDAP_Domain_Name		= "LDAP domein naam";
	$lang_LDAP_Suffix			= "LDAP gebruiker achtervoegsel";
	$lang_LDAP_Type				= "LDAP type";
	$lang_LDAP_Enable			= "Gebruik LDAP authenticatie";
	$lang_LDAP_Error_Server_Empty	= "Geef de LDAP server naam op";
	$lang_LDAP_Error_Domain_Empty	= "Geef de LDAP domein naam op";
	$lang_LDAP__Error_Extension_Disabled = "U heeft de LDAP extensie niet geactiveerd in PHP. Neem aub de OrangeHRM LDAP handleiding door die u heeft ontvangen bij de plugin voor meer informatie.";
	$lang_LDAP_Invalid_Port			= "De LDAP poort is ongeldig";

	// Leave Module
	$lang_Menu_Leave 									= "Verlof";
	$lang_Menu_Leave_PersonalLeaveSummary 				= "Persoonlijk verlof";
	$lang_Menu_Leave_EmployeeLeaveSummary 				= "Verlof werknemer";
	$lang_Menu_Leave_LeaveSummary 						= "Verlof totalen";
	$lang_Menu_Leave_LeavesList 						= "Overzicht verlof";
	$lang_Menu_Leave_ApproveLeave 						= "Goedkeuring verlof";
	$lang_Menu_Leave_LeaveTypes 						= "Type verlof";
	$lang_Menu_Leave_Apply 								= "Verlof aanvragen";
	$lang_Menu_Leave_Assign								= "Verlof toewijzen";
	$lang_Menu_Leave_LeaveList 							= "Overzicht verlof";
	$lang_Menu_Leave_MyLeave 							= "Mijn verlof";
	$lang_Menu_Leave_DefineDaysOff 						= "Vrije dagen";
	$lang_Menu_Leave_DefineDaysOff_Weekends 			= "Weekeindes";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays 	= "Specifieke vakantiedagen";

 	$lang_Leave_Title_Apply_Leave 						= "Verlof aanvragen";
 	$lang_Leave_Title_Assign_Leave 						= "Verlof toewijzen";
	$lang_Leave_APPLY_SUCCESS 						= "Succesvol aangevraagd";
	$lang_Leave_APPLY_FAILURE 						= "Aanvraag mislukt";
	$lang_Leave_APPROVE_SUCCESS 						= "Verlof toegekend";
	$lang_Leave_APPROVE_FAILURE 						= "Verlof toekennen mislukt";
	$lang_Leave_CANCEL_SUCCESS = "Succesvol geannuleerd";
	$lang_Leave_CANCEL_FAILURE = "Annuleren mislukt";
	$lang_Leave_CHANGE_STATUS_SUCCESS = "Succesvol de verlof status aangepast";

	// Report
	$lang_Menu_Reports = "Rapporten";
	$lang_Menu_Reports_ViewReports = "Bekijk rapporten";
	$lang_Menu_Reports_DefineReports = "Nieuw rapport";

	// Time module
	$lang_Menu_Time = "Tijd";
	$lang_Menu_Time_Timesheets = "Tijdverantwoording";
	$lang_Menu_Time_PersonalTimesheet = "Persoonlijke tijdverantwoording";
	$lang_Menu_Time_EmployeeTimesheets = "Tijdverantwoording medewerkers";

    // Recruitment module
	$lang_Menu_Recruit = "Werving en selectie";
	$lang_Menu_Recruit_JobVacancies = "Vacatures";
	$lang_Menu_Recruit_JobApplicants = "Sollicitanten";

	/**
	 * Common
	 */
	$lang_Common_ConfirmDelete				= "Wilt u de gegevens verwijderen?";
	$lang_Common_FieldEmpty					= "Leeg veld";
	$lang_Common_SelectDelete				= "Selecteer een veld om te verwijderen";
	$lang_Common_SelectField				= "Selecteer een zoekveld!";
	$lang_Commn_RequiredFieldMark 			= "Velden gemarkeerd met een asterisk #star zijn noodzakelijk";
	$lang_Commn_code 						= "Code";
	$lang_Commn_description 				= "Omschrijving";
	$lang_Commn_title 						= "Titel";
	$lang_Commn_name 						= "Naam";
	$lang_Commn_PleaseWait					= "Wachten aub";
	$lang_Common_Select 					= "Selecteer";
	$lang_Commn_Email						= "E-mail";
	$lang_Common_Loading				= "Laden";
	$lang_Common_LoadingPage                        = "Laden pagina";
	$lang_Common_NotApplicable                      = "N.v.t.";
	$lang_Common_Male                      		= "Man";
	$lang_Common_Female                    		= "Vrouw";
	$lang_Common_TypeHereForHints 		= "Begin met typen voor hints...";

	$lang_Common_Edit 						= "Verander";
	$lang_Common_New 						= "Nieuw";
	$lang_Common_Save = "Opslaan";
	$lang_Common_Back = "Terug";
    $lang_Common_Add = "Toevoegen";
    $lang_Common_Delete = "Verwijder";

	$lang_Common_Time = "Tijd";
	$lang_Common_Note = "Notitie";

	$lang_Common_AccessDenied = "Toegang geweigerd";

	//days
	$lang_Common_Monday 					= "maandag";
	$lang_Common_Tuesday 					= "dinsdag";
	$lang_Common_Wednesday 					= "woensdag";
	$lang_Common_Thursday 					= "donderdag";
	$lang_Common_Friday 					= "vrijdag";
	$lang_Common_Saturday 					= "zaterdag";
	$lang_Common_Sunday 					= "zondag";

	$lang_Common_Sort_ASC = "Aflopend";
	$lang_Common_Sort_DESC = "Oplopend";
	$lang_Common_EncounteredTheFollowingProblems = "De volgende problemen hebben zich voorgedaan";

	$lang_Common_ADD_SUCCESS 				= "Succesvol toegevoegd";
	$lang_Common_UPDATE_SUCCESS 			= "Succesvol bijgewerkt";
	$lang_Common_DELETE_SUCCESS 			= "Succesvol verwijderd";
	$lang_Common_ADD_FAILURE 				= "Toevoegen mislukt";
	$lang_Common_UPDATE_FAILURE 			= "Bijwerken mislukt";
	$lang_Common_DELETE_FAILURE 			= "Verwijderen mislukt";
	$lang_Common_UNKNOWN_FAILURE            = "Operatie mislukt";
	$lang_Common_DUPLICATE_NAME_FAILURE     = "Naam is al in gebruik";
	$lang_Common_COMPULSARY_FIELDS_NOT_ASSIGNED_FAILURE = "Compulsary velden niet toegewezen";
	$lang_Common_IMPORT_FAILURE = "Importeren mislukt";

	$lang_Leave_Common_Weekend 				= "Weekend";

	// admin module
	$lang_Admin_Common_Institute 			= "Instituut";
	$lang_Admin_Common_Course 				= "Cursus";
	$lang_Admin_education_InstituteCannotBeBlank = "Instituut moet een waarde bevatten!";
	$lang_Admin_CourseCannotBeBlank         = "Cursus moet een waarde bevatten!";
	$lang_Admin_License_DescriptionCannotBeBlank = "Licentie omschrijving moet een waarde bevatten!";

	// leave module
	$lang_Leave_Title = "OrangeHRM - Verlof module";
	$lang_Leave_Common_Date 				= "Datum";

	$lang_Leave_Common_FromDate 			= "Van";
	$lang_Leave_Common_ToDate 				= "Tot";

	$lang_Leave_Common_LeaveQuotaNotAllocated = "Verlof quota niet toegewezen. Raadpleeg de administrator";

	$lang_Leave_Common_LeaveType 			= "Verlof type";
	$lang_Leave_Common_Status 				= "Status";
	$lang_Leave_Common_Length 				= "Volledige dag/halve dag";
	$lang_Leave_Common_Range 				= "Meerdere dagen";
	$lang_Leave_Common_Comments 			= "Notities";
	$lang_Leave_Common_Comment 				= "Notitie";
	$lang_Leave_Common_Approved 			= "Akkoord";
	$lang_Leave_Common_Cancelled 			= "Geannuleerd";
	$lang_Leave_Common_Cancel 				= "Annuleer";
	$lang_Leave_Common_PendingApproval 		= "In afwachting van akkoord";
	$lang_Leave_Common_Rejected 			= "Afgewezen";
	$lang_Leave_Common_Taken 				= "Reeds genomen";
	$lang_Leave_Common_InvalidStatus 		= "Ongeldige status";
	$lang_Leave_Common_StatusDiffer 		= "De status verschilt";
	$lang_Leave_Common_FullDay 				= "Volledige dag";
	$lang_Leave_Common_HalfDayMorning 		= "Halve dag / ochtend";
	$lang_Leave_Common_HalfDayAfternoon 	= "Halve dag / middag";
	$lang_Leave_Common_HalfDay 				= "Halve dag";
	$lang_Leave_Common_LeaveTaken 			= "Opgenomen vrije dag";
	$lang_Leave_Common_LeaveAvailable 		= "Mogelijke vrije dagen";
	$lang_Leave_Common_LeaveRemaining 		= "Restant verlof";
	$lang_Leave_Common_LeaveScheduled 		= "Gepland verlof";
	$lang_Leave_Common_LeaveTypeName 		= "Titel van vrije dag";
	$lang_Leave_Common_LeaveTypeId 			= "Vrije dag type Id";
	$lang_Leave_Common_Select 				= "Selecteer";
	$lang_Leave_Common_oldLeaveTypeName 	= "Bestaande titel vrije dagen";
	$lang_Leave_Common_newLeaveTypeName 	= "Nieuwe titel vrije dagen";
	$lang_Leave_Common_EmployeeName 		= "Naam werknemer";
	$lang_Leave_Common_LeaveEntitled 		= "Toegewezen dagen";
	$lang_Leave_Common_Year 				= "Jaar";
	$lang_Leave_Common_ListOfTakenLeave 	= "Opgenomen verlof dagen";
	$lang_Leave_Common_Remove 				= "Annuleren";
	$lang_Leave_Common_AllEmployees			= "Alle werknemers";
	$lang_Leave_Common_All					= "Alles";
	$lang_Leave_Common_InvalidDateRange 	= "Ongeldig datumbereik";

	$lang_Leave_Common_NameOfHoliday 		= "Naam van Vakantie";
	$lang_Leave_Common_Recurring 			= "Herhalend";

 	$lang_Leave_Leave_list_Title1 			= "Vrije dag /dagen goedgekeurd";
 	$lang_Leave_all_emplyee_leaves			= "Verlof lijst";
 	$lang_Leave_all_emplyee_taken_leaves	= "Opgenomen verlof";
	$lang_Leave_Leave_Requestlist_Title1 	= "Akkoord vrije dagen voor #employeeName";
	$lang_Leave_Leave_Requestlist_Title2 	= "Verander verlof verzoek voor #employeeName";
	$lang_Leave_Leave_list_Title2 			= "Opgenomen vrije dagen door #employeeName in #dispYear jaar";
	$lang_Leave_Leave_list_Title3 			= "Lijst van vrije dagen";
	$lang_Leave_Leave_list_Title4 			= "Toegestane verlofen";
	$lang_Leave_Leave_list_TitleAllEmployees= "Verlof lijst (alle medewerkers)";
	$lang_Leave_Leave_list_TitleMyLeaveList = "Mijn verlof lijst";
	$lang_Leave_Leave_list_Title5 			= "Opgenomen verlof";
	$lang_Leave_Leave_list_ShowLeavesWithStatus = "Laat verlof zijn met status";
	$lang_Leave_Leave_list_SelectAtLeastOneStatus = "Selecteer minstens 1 verlof status";
	$lang_Leave_Leave_list_From             = "Van";
	$lang_Leave_Leave_list_To               = "Tot";
	$lang_Leave_Select_Employee_Title 		= "Selecteer werknemer";
	$lang_Leave_Leave_Summary_Title 		= "Overzicht van vrije dagen";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Overzicht vrije dagen voor #employeeName in #dispYear jaar";
	$lang_Leave_Select_Employee_Title 		= "Selecteer werknemer";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Overzicht vrije dagen voor #dispYear jaar";
	$lang_Leave_Leave_Summary_SUP_Title 	= "Overzicht vrije dagen voor #employeeName in #dispYear jaar";
	$lang_Leave_Define_leave_Type_Title 	= "Definieer verlof type";
	$lang_Leave_Leave_Type_Summary_Title 	= "Verlof type";
	$lang_Leave_Leave_Holiday_Specific_Title = "Definieer verlofdagen: gespecificeerde vakantiedagen";
	$lang_Leave_Leave_Holiday_Weeked_Title 	= "Definieer verlofdagen: Weekend";
	$lang_Leave_Summary_Deleted_Types_Shown = "Duidt geschrapte verloftypes aan.";
        $lang_Leave_Summary_Deleted_Types_MoreInfo = "Verwijderde verlof types worden hier weergegeven als de medewerker deze in het verleden heeft gebruikt voor verwijdering. Verwijderde verlof types worden bewaard in het systeem maar kunnen niet opnieuw gebruikt worden voor nieuwe verloven.";

	$lang_Leave_Holiday = "Vakantie";
	$lang_Leave_NoOfDays = "Aantal dagen";
	$lang_Leave_NoOfHours = "Aantal uren";
	$lang_Leave_Period = "Verlof periode";
	$lang_Leave_Closed = "Gesloten";

	$lang_Leave_Define_IsDeletedName  	= "Een verwijderd verlof type bestaat met dezelfde naam.";
	$lang_Leave_Define_UndeleteLeaveType    = "Om een verwijderd verlof type opnieuw te gebruiken in plaats van een nieuwe te maken, klik:";
	$lang_Leave_NAME_IN_USE_ERROR 		= "Naam verlof type is in gebruik. Kies een andere naam.";
	$lang_Leave_ADD_FAILURE			= "Toevoegen mislukt";
	$lang_Leave_ADD_SUCCESS 		= "Succesvol toegevoegd";
	$lang_Leave_LEAVE_TYPE_NOT_FOUND_ERROR 	= "Verlof type niet gevonden";
	$lang_Leave_UNDELETE_SUCCESS		= "Verlof type succesvol geheractiveerd.";
	$lang_Leave_DUPLICATE_LEAVE_TYPE_ERROR  = "Dubbele verlof type namen aangegeven. Verlof type namen moeten uniek zijn.";
	$lang_Leave_LEAVE_TYPE_EDIT_ERROR       = "Fout met opslaan van veranderigen";
	$lang_Leave_LEAVE_TYPE_EDIT_SUCCESS     = "Veranderingen succesvol opgeslagen";
	$lang_Leave_NO_CHANGES_TO_SAVE_WARNING  = "Geen veranderingen om op te slaan";
	$lang_Leave_Undelete			= "Heractiveer";

	$lang_Leave_Summary_Error_CorrectLeaveSummary	= "Er zijn fouten in het verlof overzicht!\\nCorrigeer aub de gemarkeerde verlof quota waarde.";
	$lang_Leave_Summary_Error_NonNumericValue		= "Niet-nummeriek";
	$lang_Leave_Summary_Error_InvalidValue			= "Ongeldige waarde";

	$lang_bankInformation_code 				= "Code";
	$lang_bankInformation_description 		= "Omschrijving";

	$lang_compstruct_add 					= "Toevoegen";
	$lang_compstruct_delete 				= "Verwijderen";
	$lang_compstruct_clear 					= "Opnieuw";
	$lang_compstruct_hide 					= "Verbergen";
	$lang_compstruct_save 					= "Bewaren";

 	$lang_comphire_heading 							= "Bedrijfs Hierarchie: Bedrijfsinformatie";
	$lang_comphire_relationalhierarchy 				= "Relatie Hierarchie";
	$lang_comphire_employee 						= "Werknemer";
	$lang_comphire_definitionlevel 					= "Omschrijvings niveau";
	$lang_comphire_telephone 						= "Telefoon";
	$lang_comphire_fax 								= "Fax";
	$lang_comphire_email							= "Email";
	$lang_comphire_url 								= "URL";
	$lang_comphire_logo 							= "Logo";
	$lang_comphire_selecthie 						= "Selecteer Hierarchie";
	$lang_comphire_selectdef 						= "Selecteer omschrijvings niveau";
	$lang_compstruct_heading 						= "Bedrijfsinfo : Bedrijfsstructuur";
	$lang_compstruct_Dept_Id						= "Afdelings ID:";
	$lang_compstruct_frmSub_divisionHeadingAdd 		= "Toevoegen van sub-divisie";
	$lang_compstruct_frmSub_divisionHeadingEdit 	= "Wijzigen";
	$lang_compstruct_Name 							= "Naam";
	$lang_compstruct_Type 							= "Type";
	$lang_compstruct_Division 						= "Divisie";
	$lang_compstruct_Description 					= "Omschrijving";
	$lang_compstruct_Department 					= "Afdeling";
	$lang_compstruct_Team 							= "Team";
	$lang_compstruct_Other 							= "Anders";
	$lang_compstruct_Location 						= "Locatie";
	$lang_compstruct_frmNewLocation 				= "Definieer nieuwe locatie";
	$lang_compstruct_Address 						= "Adres";
	$lang_compstruct_country						= "Land";
	$lang_compstruct_state 							= "Provincie";
	$lang_compstruct_city 							= "Stad";
	$lang_compstruct_ZIP_Code 						= "Postcode";
	$lang_compstruct_Phone 							= "Telefoon";
	$lang_compstruct_no_root 						= "Bron niet gevonden! AUB definieer bron.";

 	$lang_corptit_heading 							= "Bedrijfs titels : Baan informatie";
	$lang_corptit_topinhierachy 					= "Top in Hierachie";
	$lang_corptit_multipleheads 					= "Meerdere personen";
	$lang_corptit_headcount 						= "Tellende personen";
	$lang_corptit_nextlevelupgrade 					= "verhogen naar volgende niveau";
	$lang_corptit_selectcor 						= "Selecteer Bedrijfstitel";
	$lang_corptit_salarygrade 						= "Salaris schaal";
	$lang_corptit_selectsal 						= "Selecteer salaris schaal";

 	$lang_costcenters_heading 						= "Kostencenter : Bedrijfsinformatie";

 	$lang_countryinformation_heading 				= "Land informatie : Geografische informatie";

 	$lang_currencytypes_heading 					= "Valuta : Baan informatie";


 	$lang_districtinformation_heading 				= "Stad informatie : Geografische informatie";
	$lang_districtinformation_selectcounlist 		= "Selecteer land";
	$lang_districtinformation_selstatelist 			= "Selecteer provincie";

 	$lang_eeojobcat_heading 						= "Baan : EEO Baan categorie";
	$lang_eeojobcat_description 					= "Titel";
	$lang_eeojobcat_TitleContainsNumbers            = "Titel bevat nummers. Wilt u doorgaan?";
	$lang_eeojobcat_TitleMustBeSpecified            = "Titel moet opgegeven worden.";

 	$lang_electorateinformation_heading 			= "Electoraat informatie : Geografische informatie";
	$lang_emprepinfo_heading						= "Definieer werknemers rapporten";

 	$lang_emptypes_heading 					= "Werknemer types : Nexus Informatie";
	$lang_emptypes_datelimited 				= "Datum gelimiteerd";
	$lang_emptypes_prefix 					= "Voorvoegsel";

 	$lang_empview_heading 					= "Functie: dienst status";
	$lang_empview_EmpID 					= "Werknemer ID";
	$lang_empview_EmpFirstName 				= "Werknemer naam";
	$lang_empview_EmpLastName 				= "Werknemer achternaam";
	$lang_empview_EmpMiddleName 			= "Werknemer tussenvoegsel";
	$lang_empview_search					= "Zoeken";
	$lang_empview_searchby 					= "Zoek op:";
	$lang_empview_description 				= "Zoek naar:";
	$lang_empview_norecorddisplay 			= "Geen gegevens";
	$lang_empstatus_PleaseEnterEmploymentStatus = "Geef aub de medewerker status omschrijving op";
	$lang_empview_SelectField               = "Selecteer het veld om te doorzoeken!";

	$lang_empview_last 						= "Laatste";
	$lang_empview_next 						= "Volgende";
	$lang_empview_previous 					= "Vorige";
	$lang_empview_first						= "Eerste";

	$lang_empview_employeeid 				= "Werknemer ID";
	$lang_empview_employeename 				= "Naam werknemer";
	$lang_empview_ADD_SUCCESS 				= "Toevoegen succesvol";
	$lang_empview_UPDATE_SUCCESS 			= "Wijziging succesvol";
	$lang_empview_DELETE_SUCCESS 			= "Verwijderen succesvol";
	$lang_empview_ADD_FAILURE 				= "Toegevoegen mislukt";
	$lang_empview_DUPLICATE_EMPCODE_FAILURE	= "Toevoegen mislukt. Er is een dubbele medewerker code opgegeven.";
	$lang_empview_SELF_SUPERVISOR_FAILURE 	= "Je kan jezelf niet toewijzen als je eigen supervisor";
	$lang_empview_UPDATE_FAILURE 			= "Wijzigen mislukt";
	$lang_empview_DELETE_FAILURE 			= "Verwijderen mislukt";
	$lang_empview_Language 					= "Talen";
	$lang_empview_WorkExperience 			= "Werkervaring";
	$lang_empview_Payment 					= "Betaling";
	$lang_empview_Skills 					= "Vaardigheden";
	$lang_empview_Licenses 					= "Certificaten";
	$lang_empview_EmployeeInformation 		= "Werknemer informatie";
	$lang_empview_Memberships 				= "Lid van";
	$lang_empview_Report 					= "Rapport";
	$lang_empview_ReportTo					= "Rapporteert aan";
	$lang_empview_SubDivision				= "Sub-divisie";
	$lang_empview_JobTitle					= "Functie titel";
	$lang_empview_Supervisor 				= "Leidinggevende";
	$lang_empview_EmploymentStatus			= "Werknemer status";
	$lang_emppop_title                                      = "Zoek medewerkers";

 	$lang_ethnicrace_heading 				= "Nationaliteit & origine : Origine";
 	$lang_ethnicrace_NameShouldBeSpecified  = "Nationaliteitnaam moet worden opgegeven";

 	$lang_extracurractcat_heading 			= "Nevenactiviteiten";

 	$lang_extracurractinfo_heading 			= "Nevenactiviteiten";
	$lang_extracurractinfo_extracuaccat 	= "Nevenactiviteiten categorie";
	$lang_extracurractinfo_selectsecucat 	= "Selecteer een nevenactiviteit categorie";

 	$lang_geninfo_heading 					= "Bedrijfs info : algemeen";
	$lang_geninfo_compname 					= "Bedrijfsnaam";
	$lang_geninfo_numEmployees				= "Aantal medewerkers";
	$lang_geninfo_taxID 					= "BTW nummer";
	$lang_geninfo_naics 					= "KvK nummer";
	$lang_geninfo_err_CompanyName 			= "Bedrijfsnaam moet ingevuld zijn";
	$lang_geninfo_err_Phone 				= "Geen geldig telefoonnummer/faxnummer";
	$lang_geninfo_err_CommentLengthWarning	= "De lengte voor commentaar is groter dan het toegestane maximum. Tekst aan het einde van het commentaar zal verloren raken.";

 	$lang_hierarchydef_heading 				= "Hierarchie informatie : Bedrijfsinformatie";

	$lang_hremp_EmpFirstName 				= "Voornaam";
	$lang_hremp_EmpLastName 				= "Achternaam";
	$lang_hremp_EmpMiddleName 				= "Tussenvoegsel";
	$lang_hremp_nickname 					= "Roepnaam ";
	$lang_hremp_photo 						= "Foto ";
	$lang_hremp_ssnno 						= "Sofinummer";
	$lang_hremp_nationality 				= "Nationaliteit";
	$lang_hremp_sinno 						= "Verzekeringsnummer";
	$lang_hremp_dateofbirth 				= "Geboortedatum";
	$lang_hremp_otherid 					= "Overig ID nummer";
	$lang_hremp_maritalstatus 				= "Burgerlijke status";
	$lang_hremp_selmarital					= "--Selecteer--";
	$lang_hremp_selectnatio 				= "Selecteer nationaliteit";
	$lang_hremp_selethnicrace 				= "Selecteer origine";
	$lang_hremp_smoker 						= "Roker";
	$lang_hremp_gender 						= "Geslacht";
	$lang_hremp_dlicenno 					= "Rijbewijs nummer";
	$lang_hremp_licexpdate 					= "Rijbewijs vervaldatum ";
	$lang_hremp_militaryservice 			= "Militairendienst ";
	$lang_hremp_ethnicrace 					= "Origine ";
	$lang_hremp_jobtitle 					= "Functie naam";
	$lang_hremp_selempstat 					= "Selecteer werknemer status";
    $lang_hremp_jobspec                     = "Functie specificaties";
    $lang_hremp_jobspecduties               = "Functie taken";
	$lang_hremp_eeocategory 				= "EEO categorie";
	$lang_hremp_seleeocat 					= "Selecteer EEO categorie";
	$lang_hremp_joindate 					= "In dienst";
	$lang_hremp_termination_date			= "Datum uit dienst";
	$lang_hremp_termination_reason			= "Beeindigings reden";
	$lang_hremp_SelectJobTitle				= "Selecteer functie naam";
	$lang_hremp_EmpStatus					= "Werknemer status";
	$lang_hremp_Workstation					= "Positie";
	$lang_hremp_Subdivision					= "Sub-divisie";
    $lang_hremp_Locations                   = "Locaties";

 	$lang_hremp_dependents 					= "Naasten";
	$lang_hremp_children 					= "Kinderen";
	$lang_hremp_relationship 				= "Relatie";

	$lang_hremp_street1						= "Adres 1";
	$lang_hremp_street2 					= "Adres 2";

	$lang_hremp_hmtele 						= "Telefoon thuis";
	$lang_hremp_mobile 						= "Mobiel";
	$lang_hremp_worktele 					= "Telefoon werk";
	$lang_hremp_city 						= "Stad";
	$lang_hremp_workemail 					= "Email werk";
	$lang_hremp_otheremail 					= "Andere Email";
	$lang_hremp_passport 					= "Paspoort";
	$lang_hremp_visa 						= "Visum";
	$lang_hremp_citizenship 				= "Nationaliteit";
	$lang_hremp_passvisano 					= "Paspoort/Visumnummer";
	$lang_hremp_issueddate 					= "Datum van afgifte";
	$lang_hremp_i9status 					= "I9 status";
	$lang_hremp_dateofexp 					= "Geldig tot";
	$lang_hremp_i9reviewdate 				= "I9 beoordelings datum";
	$lang_hremp_path 						= "Pad";
	$lang_hremp_filename 					= "Bestandsnaam";
	$lang_hremp_size 						= "Omvang";
	$lang_hremp_type 						= "Type";
	$lang_hremp_name 						= "Naam";
	$lang_hremp_InvalidPhone				= "Geen geldig telefoon/fax nummer";
	$lang_hremp_largefileignore				= "1MB max, groter wordt geweigerd";
	$lang_hremp_PleaseSelectFile				= "Selecteer aub een bestand.";
	$lang_hremp_ShowFile					= "Bestand weergeven";
	$lang_hremp_Save					= "Opslaan";
	$lang_hremp_Delete					= "Verwijder";
	$lang_lang_uploadfailed					= "Upload fout";

	$lang_hremp_browse						= "Zoeken";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "Weet u zeker dat u de foto wilt verwijderen";

	$lang_hremp_SelectAPhoto				= "Selecteer een foto";
	$lang_hremp_PhotoMaxSize				= "1M maximaal";
	$lang_hremp_PhotoDimensions				= "Afmetingen 100x120";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "Verwerkings dag moet voor einddatum zijn";
	$lang_hremp_FromDateShouldBeBeforeToDate= "Vanaf-datum moet voor tot-datum";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "Begin dag moet voor eind dag";

	$lang_hremp_ContractExtensionStartDate	= "Contract verlenging begindatum";
	$lang_hremp_ContractExtensionEndDate	= "Contract verleging einddatum";
	$lang_hremp_EmployeeContracts 			= "Contracten werknemer";
	$lang_hremp_AssignedContracts			= "Toegekende contracten";

	$lang_hremp_ShowEmployeeContracts 			= "Laat werknemer contracten zien";
	$lang_hremp_HideEmployeeContracts 			= "Verberg werknemer contracten";

    $lang_hremp_ShowEmployeeJobHistory      = "Laat medewerker history zien";
    $lang_hremp_HideEmployeeJobHistory      = "Verberg medewerker history";

    $lang_hremp_EmployeeJobHistory          = "Medewerker history";
    $lang_hremp_EmployeePreviousPositions   = "Vorige posities";
    $lang_hremp_EmployeePreviousSubUnits    = "Vorige sub-units";
    $lang_hremp_EmployeePreviousLocations   = "Vorige locaties";
    $lang_hremp_EmployeeHistoryFrom         = "Van";
    $lang_hremp_EmployeeHistoryTo           = "Tot";
    $lang_hremp_EmployeeHistoryNoItemsFound = "Geen items gevonden";
    $lang_hremp_EmployeeAddHistoryItem      = "Voeg medewerker history item toe";
    $lang_hremp_EmployeeJobTitleOption      = "Functie titel";
    $lang_hremp_EmployeeSubDivisionOption      = "Sub divisie";
    $lang_hremp_EmployeeLocationOption      = "Locatie";
    $lang_hremp_EmployeeHistory_PleaseSelectJobTitle = "Selecteer aub een functie titel";
    $lang_hremp_EmployeeHistory_PleaseSelectSubDivision = "Selecteer aub een sub divisie";
    $lang_hremp_EmployeeHistory_PleaseSelectLocation = "Selecteer aub een locatie";
    $lang_hremp_EmployeeHistory_StartShouldBeforeEnd = "Start datum moet voor de eind datum zijn";
    $lang_hremp_EmployeeHistory_PleaseSpecifyStartDate = "Specificeer een geldige start datum";
    $lang_hremp_EmployeeHistory_PleaseSpecifyEndDate = "Specificeer een geldige eind datum";

    $lang_hremp_EmployeeHistory_DatesWrong = "Geef aub geldige datums op. De Van datum moet niet na de Tot datum zijn";
    $lang_hremp_EmployeeHistory_ExpectedDateFormat = "Verwacht datum formaat: ";
    $lang_hremp_EmployeeHistory_DatesWithErrorsHighlighted = "Datums die een fout bevatten zijn in rood weergegeven ";

    $lang_hremp_AddLocation                 = "Locatie toevoegen";
    $lang_hremp_SelectLocation              = "Select Location";
    $lang_hremp_PleaseSelectALocationFirst  = "Selecteer aub eerst een locatie";
    $lang_hremp_ErrorAssigningLocation      = "Fout in toewijzen van locatie";
    $lang_hremp_ErrorRemovingLocation      = "Fout in verwijderen locatie";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "Salaris moet tussen min en max zijn";

	$lang_hremp_SelectCurrency				= "Selecteer valuta";
	$lang_hremp_SelectPayGrade				= "Select Pay Grade";

	$lang_hremp_ContractExtensionId			= "Contract verlenging ID";
	$lang_hremp_ContractStartDate			= "Contract startdatum";
	$lang_hremp_ContractEndDate				= "Contract einddatum";
    $lang_hremp_FieldShouldBeNumeric        = "Veld moet numeriek zijn";

    $lang_hremp_Language					= "Taal";

 	$lang_hremplan_employeelanguageflu 		= "Beheersing talen werknemer";
	$lang_hremplan_fluency 					= "Vakbekwaamheids gebied";
	$lang_hremplan_ratinggarde 				= "Beheerst talen";
	$lang_hremplan_assignlanguage 			= "Toegekende talen";

	$lang_hremplan_SelectLanguage			= "Selecteer taal";
	$lang_hremplan_SelectFluency			= "Selecteer beheersing";
	$lang_hremplan_SelectRating 			= "Selecteer niveau";

	$lang_hremp_PleaseSelectJobTitle= "aub Selecteer een functie voor deze werknemer {here}";

	$lang_hremp_ie_CurrentSupervisors 		= "b.v. Medewerkers huidige supervisor";
	$lang_hremp_ie_CurrentSubordinates 		= "Werknemers huidige ondergeschikten";

	$lang_hremp_ie_PleaseSpecifyAtLeastOnePhoneNo = "Geef aub minimaal één telefoonnummer op";

 	$lang_hremplicenses_employeelicen 		= "Werknemers cerficaten";
	$lang_hremplicenses_assignlicenses 		= "Toegekende certificaten";
	$lang_hremplicenses_licentype 			= "Certificaat type";
	$lang_hremplicenses_assignlicen 		= "Toegekende certificaten";

	$lang_hremplicenses_NoLicenseSelected	= "Geen certificaten geselecteerd";
	$lang_hremplicenses_SelectLicenseType	= "Selecteer certificaat type";

	$lang_hrempmemberships_NoMembershipTypeSelected	        = "Geen lidmaatschap type geselecteerd";
	$lang_hrempmemberships_NoMembershipSelected	        = "Geen lidmaatschap geselecteerd";
	$lang_hrempmemberships_NoSubscriptionOwnerSelected	= "Geen abonnement eigenaar geselecteerd";

	$lang_hremp_SelectAtLEastOneAttachment = "Selecteer een bijlage om te verwijderen";

 	$lang_hrEmpMain_ratinggarde 			= "Vakbekwaamheid";
	$lang_hrEmpMain_assignlanguage 			= "Toegekende talen";
	$lang_hrEmpMain_Writing 				= "Schrijven";
	$lang_hrEmpMain_Speaking 				= "Spreken";
	$lang_hrEmpMain_Reading 				= "Lezen";
	$lang_hrEmpMain_Poor 					= "Slecht";
	$lang_hrEmpMain_Basic 					= "Basis";
	$lang_hrEmpMain_Good 					= "Goed";
	$lang_hrEmpMain_MotherTongue 			= "Moeder taal";
	$lang_hrEmpMain_Individual 				= "Individueel";
	$lang_hrEmpMain_employeemembership 		= "Werknemers lidmaatschap";
	$lang_hrEmpMain_subownership 			= "Eigendomsrecht abonnement";
	$lang_hrEmpMain_subamount 				= "Eigendomsrecht hoeveelheid";
	$lang_hrEmpMain_subcomdate 				= "Eigendomsrecht aanvangdatum";
	$lang_hrEmpMain_subredate 				= "Eigendomsrecht vernieuwingsdatum";
	$lang_hrEmpMain_selmemtype 				= "Selecteer lidmaatschap type";
	$lang_hrEmpMain_selmemship 				= "Selecteer lidmaatschap";
	$lang_hrEmpMain_selownership 			= "Selecteer eigendomsrecht";
	$lang_hrEmpMain_assignmemship 			= "Toegekende lidmaatschappen";
	$lang_hrEmpMain_paygrade 				= "Salaris schaal";
	$lang_hrEmpMain_currency 				= "Valuta";
	$lang_hrEmpMain_minpoint 				= "Minimum salaris";
	$lang_hrEmpMain_maxpoint 				= "Maximum salaris";
	$lang_hrEmpMain_bassalary 				= "Basis salaris";
	$lang_hrEmpMain_assignedsalary 			= "Toegekend salaris";
	$lang_hrEmpMain_payfrequency 			= "Salaris betalingsfrequentie";

 	$lang_hrEmpMain_heading 				= "Werknemers rapport";
	$lang_hrEmpMain_supervisorsubordinator 	= "Leidinggevende / ondergeschikte";
	$lang_hrEmpMain_reportingmethod 		= "Rapporteer methode";
	$lang_hrEmpMain_subordinateinfomation 	= "Informatie ondergeschikten";
	$lang_hrEmpMain_supervisorinfomation 	= "Informatie leidingevende";
	$lang_hrEmpMain_selecttype 				= "Selecteer methode";
	$lang_hrEmpMain_Direct 					= "Direct";
	$lang_hrEmpMain_Indirect 				= "Indirect";
	$lang_hrEmpMain_employeeskill 			= "Ervaring werknemer";
	$lang_hrEmpMain_yearofex 				= "Aantal jaren ervaring";
	$lang_hrEmpMain_assignskills 			= "Toegekende mogelijkheden";
	$lang_hrEmpMain_employerworkex 			= "Werknemers werkervaring";
	$lang_hrEmpMain_employer 				= "Werkgever";
	$lang_hrEmpMain_enddate 				= "Einddatum";
	$lang_hrEmpMain_startdate 				= "Startdatum";
	$lang_hrEmpMain_assignworkex 			= "Toegekende werkervaring";
	$lang_hrEmpMain_workexid 				= "Werkervaring ID";
	$lang_hrEmpMain_internal 				= "Intern";
	$lang_hrEmpMain_major 					= "Afgestuurd in/Gespecialiseerd";
	$lang_hrEmpMain_education 				= "Opleiding";
	$lang_hrEmpMain_gpa 					= "Test/score";
	$lang_hrEmpMain_assigneducation 		= "Toegekende opleiding";
	$lang_hrEmpMain_assignattach 			= "Toegevoegde bijlagen";

	$lang_hrEmpMain_SelectEducation			= "Selecteer opleiding";
	$lang_hrEmpMain_YearsOfExperiencCannotBeBlank = "Aantal jaren van ervaring moet worden ingevuld";
	$lang_hrEmpMain_YearsOfExperiencWrongFormat = "Aantal jaren van ervaring - Verkeerde format ingevuld";
	$lang_hrEmpMain_YearsOfExperiencBetween = "Aantal jaren van ervaring moet zijn tussen de #range";
	$lang_hrEmpMain_Skill 					= "Vaardigheden";
	$lang_hrEmpMain_EnterFromDateFirst      = "Geef eerst de vanaf datum op";

	$lang_hrEmpMain_subown_Company = 'Bedrijf';
	$lang_hrEmpMain_subown_Individual = 'Individueel';

	$lang_hrEmpMain_arrRepType_Supervisor = 'Leidinggevende';
	$lang_hrEmpMain_arrRepType_Subordinate = 'Ondergeschikte';

	$lang_hrEmpMain_arrRepMethod_Direct = 'Direct';
	$lang_hrEmpMain_arrRepMethod_Indirect = 'Indirect';

	$lang_hrEmpMain_SelectMethod = 'Selecteer methode';

	$lang_hrEmpMain_SubscriptionAmountShouldBeNumeric = "Abonnement hoeveelheid moet numeriek zijn";
	$lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue = "Lidmaatschap abonnement hoeveelheid is niet ingevuld. Wilt u verder gaan";

	$lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate = "Aanvangdatum moet voor vernieuwingsdatum zijn";

	$lang_hrEmpMain_membershiptype = "Lidmaatschap type";
	$lang_hrEmpMain_membership = "Lidmaatschap";

	$lang_hrEmpMain_FederalIncomeTax = "Federale inkomsten belasting";
	$lang_hrEmpMain_TaxStatus = "Status";
	$lang_hrEmpMain_TaxExemptions = "Vrijstellingen";

	$lang_hrEmpMain_TaxStatusSelect = "-Selecteer-";
	$lang_hrEmpMain_TaxStatusMarried = "Gehuwd";
	$lang_hrEmpMain_TaxStatusSingle = "Alleenstaand";
	$lang_hrEmpMain_TaxStatusNonResidentAlien = "Niet ingezette vreemdeling";
	$lang_hrEmpMain_TaxStatusNotApplicable = "Niet van toepassing";

	$lang_hrEmpMain_StateIncomeTax = "Staat inkomsten belasting";
	$lang_hrEmpMain_TaxState = "Staat";
	$lang_hrEmpMain_TaxUnemploymentState = "Werkloos staat";
	$lang_hrEmpMain_TaxWorkState = "Werk staat";

	$lang_hrEmpMain_DirectDebitAccount = "Account";
	$lang_hrEmpMain_DirectDebitAccountType = "Account type";
	$lang_hrEmpMain_DirectDebitAccountTypeChecking = "Checking";
	$lang_hrEmpMain_DirectDebitAccountTypeSavings = "Savings";
	$lang_hrEmpMain_DirectDebitRoutingNumber = "Routing Number";
	$lang_hrEmpMain_DirectDebitAmount = "Bedrag";
	$lang_hrEmpMain_DirectDebitTransactionType = "Transactie type";

	$lang_hrEmpMain_DirectDebitSelectTransactionType = "-Selecteer-";

	$lang_hrEmpMain_DirectDebitTransactionTypeBlank = "Blank";
	$lang_hrEmpMain_DirectDebitTransactionTypePercentage = "Percentage";
	$lang_hrEmpMain_DirectDebitTransactionTypeFlat = "Flat";
	$lang_hrEmpMain_DirectDebitTransactionTypeFlatMinus = "Flat - ";

	$lang_hrEmpMain_DirectDebitAssigned = "Toegewezen bankrekening accounts";
	$lang_hrEmpMain_DirectDebitAccountShouldBeSpecified = "Account moet worden gespecificeerd";
	$lang_hrEmpMain_DirectDebitRoutingNumberShouldBeSpecified = "Routeringnummer moet worden gespecificeerd";
	$lang_hrEmpMain_DirectDebitRoutingNumberShouldBeNumeric = "Routeringnummer moet numeriek zijn";
	$lang_hrEmpMain_DirectDebitAmountShouldBeSpecified = "Bedrag moet worden opgegeven";
	$lang_hrEmpMain_DirectDebitAmountShouldBeNumeric = "Bedrag moet numeriek zijn";
	$lang_hrEmpMain_DirectDebitTransactionTypeShouldBeSelected = "Transactie type moet worden geselecteerd";

 	$lang_jobtitle_heading 					= "Functie : Titel van functie";
	$lang_jobtitle_jobtitid 				= "Functie naam ID";
	$lang_jobtitle_jobtitname 				= "Titel functie";
	$lang_jobtitle_jobtitdesc 				= "Werkomschrijving";
	$lang_jobtitle_jobtitcomments 			= "Functie naam opmerkingen";
    $lang_jobtitle_jobspec                  = "Functie specificatie";
	$lang_jobtitle_addpaygrade 				= "Toevoegen salaris schaal";
	$lang_jobtitle_emstatExpl 				= "Definieer werk status voor titel van baan";
	$lang_jobtitle_editpaygrade 			= "Verander salaris schaal";
	$lang_jobtitle_addempstat 				= "Toevoegen werk status";
	$lang_jobtitle_editempstat 				= "Verander werk status ";
	$lang_jobtitle_empstat 					= "Status werknemer";
	$lang_jobtitle_NameShouldBeSpecified    = "Functie naam moet worden opgegeven";
	$lang_jobtitle_DescriptionShouldBeSpecified = "Functie omschrijving moet worden opgegeven";
	$lang_jobtitle_PayGradeNotSelected      = "Salaris schaal niet geselecteerd";
    $lang_jobtitle_ShowingSavedValues       = "Momenteel worden de opgeslagen waardes weergegeven toen u voor het laatst dit record bewerkte.";
    $lang_jobtitle_NoSelection              = "Geen selectie gemaakt!";
    $lang_jobtitle_PleaseSelectEmploymentStatus = "Selecteer aub een medewerker status";
    $lang_jobtitle_EnterEmploymentStatus    = "Geef aub een waarde op voor de medewerker status.";
 	$lang_jobspec_heading 					= "Functie : Fuctie specificaties";
 	$lang_jobspec_id 				        = "ID";
	$lang_jobspec_name 				        = "Naam";
	$lang_jobspec_desc 				        = "Omschrijving";
	$lang_jobspec_duties 			        = "Taken";
	$lang_jobspec_PleaseSpecifyJobSpecName  = "Specificeer aub een functie specificatie naam";
	$lang_jobspec_NameInUse_Error           = "Deze naam is in gebruik";


 	$lang_languageinformation_heading 		= "Vaardigheden : Talen";

 	$lang_licenses_heading 					= "Kwalificatie : Certificaten";

 	$lang_locations_heading 				= "Bedrijfs info : Locaties";
 	$lang_locations_NameHasToBeSpecified    = "Locatie naam moet worden opgegeven";
 	$lang_locations_CountryShouldBeSelected = "Land moet worden geselecteerd!";
 	$lang_locations_AddressShouldBeSpecified = "Adres moet worden opgegeven";
 	$lang_locations_ZipCodeShouldBeSpecified = "Postcode moet worden opgegeven";
 	$lang_locations_ZipContainsNonNumericChars = "Postcode bevat niet-numerieke tekens. Wilt u doorgaan?";
 	$lang_locations_InvalidCharsInPhone = "Telefoon nummer bevat ongeldige tekens";
 	$lang_locations_InvalidCharsInFax = "Fax nummer bevat ongeldige tekens";

 	$lang_membershipinfo_heading 			= "Lidmaatschap : Lidmaatschap";
	$lang_membershipinfo_membershiptype 	= "Lidmaatschap type";
	$lang_membershipinfor_selectmember 		= "Selecteer lidmaatschap";
	$lang_membershipinfo_MembershipShouldBeSpecified = "Lidmaatschap naam moet worden opgegeven";
	$lang_membershipinfo_MembershipTypeShouldBeSelected = "Lidmaatschap type moet worden geselecteerd";

 	$lang_membershiptypes_heading 			= "Lidmaatschap : Lidmaatschap type";
 	$lang_membershiptypes_NameShouldBeSpecified = "Naam lidmaatschap type moet worden opgegeven";

 	$lang_nationalityinformation_heading 	= "Nationaliteit & Origine : Nationaliteit";
 	$lang_nationalityinformation_PleaseEnterNationality = "Geef een nationaliteitsnaam op";

 	$lang_provinceinformation_heading 		= "Provincie informatie : Geo informatie";

 	$lang_qualificationtypes_heading 		= "Kwalificatie types : Kwalificatie Informatie";
	$lang_repview_ReportID 					= "Rapport ID";
	$lang_repview_ReportName 				= "Rapport Naam";
	$lang_repview_ViewEmployeeReports 		= "Werknemers rapport bekijken";
	$lang_repview_message 					= "Verwijderen kan bedrijfs hierarchie beinvloeden";
	$lang_repview_DefineEmployeeReports 	= "Definieer rapport van werknemer";
	$lang_repview_message2 					= "Verwijderen kan het bekijken van het rapport beinvloeden";

 	$lang_routeinformation_heading 			= "Route Informatie : Route Informatie";
	$lang_salarygrades_heading 				= "Baan : Salaris schaal";
	$lang_salarygrades_currAss 				= "Toegekende valuta";
	$lang_salarygrades_stepSal 				= "Schaal verhoging";
	$lang_salarygrades_selectcurrency 			= "Valute veld moet geselecteerd worden";
	$lang_salarygrades_minSalNumeric 			= "Minimum salaris moet nummeriek zijn";
	$lang_salarygrades_maxSalNumeric 			= "Maximum salaris moet nummeriek zijn";
	$lang_salarygrades_stepSalNumeric 			= "Schaal verhoging moet nummeriek zijn";
        $lang_salarygrades_minGreaterThanMax                    = "Minimum salaris > maximum salaris!";
	$lang_salarygrades_stepPlusMinGreaterThanMax = "Verschil tussen salaris plus minimum salaris moet minder zijn dan het maximum salaris";
	$lang_salarygrades_stepGreaterThanMax = "Stap salaris moet minder zijn dan het maximum salaris";
	$lang_salarygrades_NameCannotBeBlank = "Salarisschaal naam mag niet leeg zijn!";
	
	$lang_Error_salarygrades_DUPLICATE_NAME_FAILURE		= "Toevoegen van salarisschaal mislukt. Namen mogen niet hetzelfde zijn";
	$lang_Error_salarygrades_ADD_FAILURE				= "Toevoegen salarisschaal mislukt";

	$lang_salcurrdet_heading 				= "Valuta toegekend aan salaris schaal";
	$lang_salcurrdet_assigncurr 			= "Toegekende valuta";

 	$lang_satutoryinfo_heading 				= "Statutair type : Nexus informatie";

 	$lang_view_EmploymentStatusID 			= "Werk status ID";
	$lang_view_EmploymentStatusName 		= "Werk status Naam";
	$lang_view_message 						= "Verwijderen kan baan titles beinvloeden";
	$lang_view_message1 					= "Verwijderen kan salaris schaal van werknemers beinvloeden in PIM";
	$lang_view_deletePrompt                 = "Verwijdering kan niet ongedaan worden";
	$lang_view_SkillName 					= "Naam vaardigheden";
	$lang_view_SkillID 						= "Vaardigheden ID";
	$lang_view_message3 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_LocationName 				= "Naam locatie";
	$lang_view_message4 					= "Verwijderen kan bedrijfs hierarchie beinvloeden. Als de locatie geassocieerden heeft kan verwijderen niet uitgevoerd worden";
	$lang_view_CurrencyName 				= "Valuta naam";
	$lang_view_CurrencyTypes 				= "Valuta types";
	$lang_view_message5 					= "Verwijderen kan detail van salaris valuta beinvloeden";
	$lang_view_CompanyHierarchy 			= "Bedrijfshierarchie";
	$lang_view_CompanyHierarchyName 		= "Naam bedrijfshierarchie";
	$lang_view_message6 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_QualificationType 			= "Kwalificatie type";
	$lang_view_QualificationName 			= "Kwalificatie naam";
	$lang_view_message9 					= "Verwijderen kan kwalificaties,kwalificatie toekenning,werknemers kwalificatie beinvloeden";
	$lang_view_RatingMethod 				= "Berekenings methode";
	$lang_view_RatingMethodName 			= "Berekenings methode naam";
	$lang_view_message10 					= "Verwijderen kan kwalificatie, talen beinvloeden";
	$lang_view_CorporateTitle 				= "Bedrijfstitel";
	$lang_view_CorporateTitleName 			= "Bedrijfstitel naam";
	$lang_view_message11 					= "Verwijderen kan werknemers informatie, toekenning beinvloeden";
	$lang_view_ExtraCategory 				= "Nevenactiviteiten categorie";
	$lang_view_ExtraCategoryName 			= "Nevenactiviteiten categorie naam";
	$lang_view_message12 					= "Verwijderen kan de volgende onderdelen mogelijk beinvloeden: Nevenactiviteiten";
	$lang_view_MembershipTypeName 			= "Lidmaatschap naam type";
	$lang_view_message13 					= "Verwijderen kan werknemers lidmaatschap beinvloeden";
	$lang_view_EmployeeCategoryName 		= "Werknemer naam categorie";
	$lang_view_message14 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_EmployeeGroupName 			= "Werknemers groepsnaam";
	$lang_view_message15 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_NationalityName 				= "Naam nationaliteit";
	$lang_view_message16 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_CountryID 					= "Land";
	$lang_view_CountryName 					= "Naam land";
	$lang_view_message17 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_HierarchyDefinition 			= "Omschrijving hierarchie";
	$lang_view_HierarchyDefinitionName 		= "Omschrijvings naam hierarchie";
	$lang_view_message18 					= "Verwijderen kan bedrijfshierarchie beinvloeden";
	$lang_view_StateProvinceName 			= "Naam provincie";
	$lang_view_message19 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_CityName 					= "Naam stad";
	$lang_view_message20 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_LanguagName 					= "Naam talen";
	$lang_view_message21 					= "Verwijderen kan werknemers talen beinvloeden";
	$lang_view_Membership 					= "Lidmaatschap";
	$lang_view_MembershipType 				= "Type lidmaatschap";
	$lang_view_MembershipName 				= "Naam lidmaatschap";
	$lang_view_Type 					= "Type";
	$lang_view_message22 					= "Verwijderen kan werknemers lidmaatschap beinvloeden";
	$lang_view_ExtraActivities 				= "Extra nevenactiviteiten";
	$lang_view_ExtraActivitiesName 			= "Extra nevenactiviteiten naam";
	$lang_view_message23 					= "Verwijderen kan de volgende onderdelen mogelijk beinvloeden: extra nevenactiviteiten";
	$lang_view_PayGradeName 				= "Naam salaris schaal";
	$lang_view_message24 					= "Verwijderen kan werknemers informatie, titel van baan beinvloeden";
	$lang_view_message25 					= "Verwijderen kan opleiding beinvloeden";
	$lang_view_EmployeeTypeID 				= "Werknemrs type ID";
	$lang_view_EmployeeTypeName 			= "Werknemer naam type";
	$lang_view_message26 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_EEOJobCategory 				= "EEO Functie categorie";
	$lang_view_EEOJobCategoryid 			= "EEO Functie categorie Id";
	$lang_view_EEOJobCategoryName 			= "EEO Naam functie categorie";
	$lang_view_message27 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_message28 					= "Verwijderen kan werknemers talen beinvloeden";
	$lang_view_EthnicRace 					= "Etnische minderheid";
	$lang_view_EthnicRaceName 				= "Naam ethnische minderheid";
	$lang_view_message29 					= "Verwijderen kan werknemers gegevens beinvloeden";
	$lang_view_message30 					= "verwijderen kan werkenemers informatie beinvloeden";
	$lang_view_message31 					= "Verwijderen kan werknemers informatie, baan titles beinvloeden";
	$lang_view_message32 					= "Verwijderen kan kwalificaties,toegekende kwalificatie,werknemers kwalificatie beinvloeden";
	$lang_view_License_ID 					= "Certificaat ID";
	$lang_view_LicenseDescription 			= "Certificaat omschrijving";
	$lang_view_message33 					= "Verwijderen kan werknemers informatie beinvloeden";
	$lang_view_UserID 						= "Gebruiker ID";
	$lang_view_UserName 					= "Gebruikersnaam";
	$lang_view_message34 					= "Verwijderen kan OrangeHRM onbruikbaar maken";
	$lang_view_UserGroupID 					= "Gebruikersgroep ID";
	$lang_view_UserGroupName 				= "Naam gebruikersgroep";
	$lang_view_message35 					= "Verwijderen kan OrangeHRM onbruikbaar maken";

	$lang_view_Users 				= "Gebruikers";
	$lang_view_UserGroups 			= "Gebruikersgroep";
	$lang_view_HRAdmin 				= "Beheerders";
	$lang_view_ESS 					= "Medewerkers";
	$lang_view_ID 					= "ID";

	//Customer
	$lang_view_CustomerId = "Klantnummer";
	$lang_view_CustomerName = "Klantnaam";
	$lang_customer_Heading          = "Klant";

	$lang_index_WelcomeMes 			= "Welkom #username";
	$lang_index_ChangePassword 		= "Verander wachtwoord";
	$lang_index_Logout 				= "Uitloggen";

	$lang_MailFrom = "E-mail gestuurd vanaf";
	$lang_MailSendingMethod = "Verzendmethode";
	$lang_MailTypes_Mail = "Intern PHP functie";
	$lang_MailTypes_Sendmailer = "SendMail";
	$lang_MailTypes_Smtp = "SMTP";
	$lang_SendmailPath = "Pad naar SendMail";
	$lang_SmtpHost = "SMTP Host";
	$lang_SmtpPort = "SMTP poort";
	$lang_SmtpUser = "SMTP gebruiker";
	$lang_SmtpPassword = "SMTP wachtwoord";
	$lang_Error_Invalid_Port = "Ongeldige SMTP poort";

	//Projects
	$lang_view_Project_Heading = "Project";
	$lang_view_ProjectId = "Projectnummer";
	$lang_view_ProjectName = "Projectnaam";

	// Data Export
	$lang_DataExport_Title = "Exporteer HR data";
	$lang_DataExport_Type = "Export type";
	$lang_DataExport_ExportTypeNotSelected = "Export type niet geselecteerd";
	$lang_DataExport_Export = "Export";
	$lang_DataExport_PluginsAreMarked = "Export types gemarkeerd met (+) zijn gedefinieerd in Plugin bestanden en kunnen niet bewerkt worden via de gebruikers interface.";
	$lang_DataExport_CustomExportTypesCanBeManaged = "Vrij opgeefbare export types kunnen beheerd worden ";
	$lang_DataExport_ClickingHereLink = "hier";

	// Define Custom Data Export
	$lang_DataExport_CustomExportId = "ID";
	$lang_DataExport_CustomExportName = "Export naam";
	$lang_DataExportCustom_Title = "Vrij opgeefbare export definities";
	$lang_DataExport_DefineCustomField_Heading = "Definieer vrij opgeefbare export";
	$lang_DataExport_AssignedFields = "Toegewezen velden";
	$lang_DataExport_AvailableFields = "Beschikbare velden";
	$lang_DataExport_Add = "Toevoegen";
	$lang_DataExport_Remove = "Verwijderen";
	$lang_DataExport_PleaseSpecifyExportName = "Geef een naam voor de export op";
	$lang_DataExport_Error_NoFieldSelected = "Geen veld geselecteerd";
	$lang_DataExport_Error_AssignAtLeastOneField = "Wijs minstens 1 veld toe";
	$lang_DataExport_Error_NameInUse = "Deze naam is in gebruik.";
	$lang_DataExport_Error_NoFieldSelectedForMove = "Selecteer minstens 1 veld van de toegewezen velden om te verplaatsen";
	$lang_DataExport_MoveUp = "Verplaats geselecteerde velden naar boven";
	$lang_DataExport_MoveDown = "Verplaats geselecteerde velden naar beneden";

	// Data Import
	$lang_DataImport_Title = "Importeer HR data";
	$lang_DataImport_Type = "Import type";
	$lang_DataImport_CSVFile = "CSV bestand";
	$lang_DataImport_ImportTypeNotSelected = "Import type niet geselecteerd";
	$lang_DataImport_Import = "Importeer";
	$lang_DataImport_PluginsAreMarked = "Import types gemarkeerd met (+) zijn gedefinieerd in Plugin bestanden en zijn niet bewerkbaar via de gebruikers interface.";
	$lang_DataImport_CustomImportTypesCanBeManaged = "Vrij opgeefbare import types kunnen beheerd worden ";
	$lang_DataImport_ClickingHereLink = "hier";
	$lang_DataImport_Error_PleaseSelectFile = "Selecteer een CSV bestand";

	// Data Import Status
	$lang_DataImportStatus_Title = "Data import status";
	$lang_DataImportStatus_ContinuingDataImport = "Upload voltooid. Ga verder met de data import";
	$lang_DataImportStatus_Summary = "Samenvatting";
	$lang_DataImportStatus_Details = "Details van mislukte rijen";
	$lang_DataImportStatus_NothingImported = "Er zijn geen rijen geimporteerd";
	$lang_DataImportStatus_ImportSuccess = "Importeren voltooid";
	$lang_DataImportStatus_ImportFailed = "Importeren mislukt, er zijn geen rijen geimporteerd";
	$lang_DataImportStatus_ImportSomeFailed = "Een aantal rijen zijn niet geimporteerd";
	$lang_DataImportStatus_NumImported = "Aantal rijen die zijn geimporteerd";
	$lang_DataImportStatus_NumFailed = "Aantal rijen die mislukt zijn geimporteerd te worden";
	$lang_DataImportStatus_NumSkipped = "Aantal van hoofd rijen die zijn overgeslagen";
	$lang_DataImportStatus_TimeRemainingSeconds = "seconden";
	$lang_DataImportStatus_FinalResult = "Uiteindelijk resultaat";
	$lang_DataImportStatus_ImportInProgress = "Importeren is bezig...";
	$lang_DataImportStatus_ImportCompleted = "Importeren voltooid";
	$lang_DataImportStatus_Progress = "Voortgang";
	$lang_DataImportStatus_ETA = "Schatting resterende tijd";
	$lang_DataImportStatus_Heading_Row = "Rij";
	$lang_DataImportStatus_Heading_Error = "Fout";
	$lang_DataImportStatus_Heading_Comments = "Commentaar";

	$lang_DataImportStatus_Error_IMPORT_ERROR = "Importeer fout ";
	$lang_DataImportStatus_Error_INCORRECT_COLUMN_NUMBER = "Onjuist aantal kolommen";
	$lang_DataImportStatus_Error_MISSING_WORKSTATION = "Werkstation niet gevonden";
	$lang_DataImportStatus_Error_COMPULSARY_FIELDS_MISSING_DATA = "Compulsary velden missen in de data";
	$lang_DataImportStatus_Error_DD_DATA_INCOMPLETE = "Direct Deposit data is niet compleet";
	$lang_DataImportStatus_Error_INVALID_TYPE = "Ongeldig veld data type";
	$lang_DataImportStatus_Error_DUPLICATE_EMPLOYEE_ID = "Medewerker ID is in gebruik";
	$lang_DataImportStatus_Error_DUPLICATE_EMPLOYEE_NAME = "Er bestaat al een medewerker met dezelfde naam";
	$lang_DataImportStatus_Error_FIELD_TOO_LONG = "Veld is te lang";

	// Define Custom Data Import
	$lang_DataImport_CustomImportId = "ID";
	$lang_DataImport_CustomImportName = "Import naam";
	$lang_DataImportCustom_Title = "Vrij invulbare import definities";
	$lang_DataImport_DefineCustomField_Heading = "Defineer vrij invulbare import";
	$lang_DataImport_ContainsHeader = "Bevat header";
	$lang_DataImport_ContainsHeaderDescription = "Indien geselecteerd, zal OrangeHRM de 1e lijn van het CSV bestand overslaan";
	$lang_DataImport_AssignedFields = "Toegewezen velden";
	$lang_DataImport_AvailableFields = "Beschikbare velden";
	$lang_DataImport_Add = "Toevoegen";
	$lang_DataImport_Remove = "Verwijderen";
	$lang_DataImport_PleaseSpecifyImportName = "Geef de import naam op";
	$lang_DataImport_Error_NoFieldSelected = "Geen velden geselecteerd";
	$lang_DataImport_Error_AssignAtLeastOneField = "Wijs minimaal 1 veld toe";
	$lang_DataImport_Error_NameInUse = "Deze naam is in gebruik.";
	$lang_DataImport_Error_NoFieldSelectedForMove = "Selecteer minstens 1 veld van de toegewezen velden om te verplaatsen";
	$lang_DataImport_MoveUp = "Verplaats geselecteerde velden omhoog";
	$lang_DataImport_MoveDown = "Verplaats geselecteerde velden naar beneneden";
	$lang_DataImport_Error_AssignCompulsaryFields = "De volgende compulsary velden zijn niet toegewezen";
	$lang_DataImport_CompulsaryFields = "De volgende velden zijn compulsary en moeten toegewezen worden";
	$lang_DataImport_Error_CantRemoveCompulsaryFields = "De volgende velden zijn compulsary en kunnen niet verwijderd worden";

	// Define custom data export CSV headings
	$lang_DataExport_DefineCustomFieldHeadings_Heading = "Definieer CSV regelnaam voor vrij invulbare export";
	$lang_DataExport_ColumnHeadings = "CSV kolom namen";
	$lang_DataExport_EditColumnHeadings = "Bewerk kolom namen indien nodig.";
	$lang_DataExport_Error_AllHeadingsMustBeSpecified = "Alle kolom namen moeten worden opgegeven";
	$lang_DataExport_Error_CommaNotAllowedInHeadings = "Het komma teken (,) is niet toegestaan in de kolomnamen";

	// Custom Fields
	$lang_CustomFields_Title = "Vrij invulbare velden";
	$lang_CustomFields_CustomFieldId = "Vrij invulbaar veld id";
	$lang_CustomFields_CustomFieldName = "Vrij invulbaar veld naam";
	$lang_CustomFields_CustomFieldNumber = "Veld nummer";
	$lang_customeFields_Heading = "Vrij invulbare velden";
	$lang_customeFields_FieldName = "Veld naam";
	$lang_customeFields_Type = "Type";
	$lang_customeFields_StringType = "String";
	$lang_customeFields_SelectType = "Drop Down";
	$lang_customeFields_SelectOptions = "Selectie opties";
	$lang_Admin_CustomeFields_PleaseSpecifyCustomFieldName = "Geef een naam op voor het vrij invulbare veld";
	$lang_Admin_CustomeFields_PleaseSpecifySelectOptions = "Geef de selectie opties op";
	$lang_Admin_CustomeFields_SelectOptionsHint = "Geef de toegestane opties op gescheiden door een komma";
	$lang_Admin_CustomeFields_MaxCustomFieldsCreated = "Het maximum aantal vrij opgeefbare velden zijn aangemaakt.";
	$lang_pim_CustomFields_NoCustomFieldsDefined = "Geen vrij invulbare velden gedefinieerd.";

	// PIM tab labels
	$lang_pim_tabs_Personal = "Persoonlijk";
	$lang_pim_tabs_Contact = "contact";
	$lang_pim_tabs_EmergencyContacts = "Contact bij calamiteiten";
	$lang_pim_tabs_Dependents = "Naasten";
	$lang_pim_tabs_Immigration = "Immigratie";
	$lang_pim_tabs_Job = "Functie";
	$lang_pim_tabs_Payments = "Betalingen";
	$lang_pim_tabs_ReportTo = "Rapporteert aan";
	$lang_pim_tabs_WorkExperience = "Werkervaring";
	$lang_pim_tabs_Education = "Opleiding";
	$lang_rep_Languages = "Talen";
	$lang_pim_tabs_Skills = "Vaardigheden";
	$lang_pim_tabs_Languages = "Talen";
	$lang_pim_tabs_License = "Certificaten";
	$lang_pim_tabs_Membership = "Lidmaatschap";
	$lang_pim_tabs_Attachments = "Bijlagen";
	$lang_pim_tabs_Tax = "Vrijstelling van belasting";
	$lang_pim_tabs_DirectDebit = "Directe storting";
	$lang_pim_tabs_Custom = "Vrij invulbaar";

	// Report module
	$lang_rep_SelectionCriteria = "Selectie criteria";
	$lang_rep_EmpNo = "Werknemers nummer";
	$lang_rep_Employee = "Werknemer";
	$lang_rep_AgeGroup = "Leeftijdsgroep";
	$lang_rep_PayGrade = "Salaris schaal";
	$lang_rep_Education = "Opleiding";
	$lang_rep_EmploymentStatus = "Werknemers status";
	$lang_rep_ServicePeriod = "Dienstverband";
	$lang_rep_JoinedDate = "Datum in dienst";
	$lang_rep_JobTitle = "Functie titel";
	$lang_rep_Language = "Taal";
	$lang_rep_Skill = "Vaardigheid";

	$lang_rep_LastName = "Achternaam";
	$lang_rep_FirstName = "Voornaam";
	$lang_rep_Address = "Adres";
	$lang_rep_TelNo = "Telefoonnummer";
	$lang_rep_DateOfBirth = "Geboortedatum";
	$lang_rep_JoinDate = "Datum in dienst treden";
	$lang_rep_Qualification = "Kwalificaties";
	$lang_rep_EmployeeStates = "Werknemers verklaring";
	$lang_rep_JoinedDate = "In dienst";

	$lang_rep_SelectPayGrade = "Selecteer Salaris schaal";
	$lang_rep_SelectEducation = "Selecteer type opleiding";
	$lang_rep_SelectEmploymentType = "Selecteer type werk";
	$lang_rep_SelectComparison = "Selecteer vergelijking";
	$lang_rep_SelectJobTitle = "Selecteer naam functie";
	$lang_rep_SelectLanguage = "Selecteer taal";
	$lang_rep_SelectSkill = "Selecteer vaardigheid";

	$lang_rep_Field = "Veld";
	$lang_rep_AssignUserGroups = "Toekennen gebruikersgroep";
	$lang_rep_AssignedUserGroups = "Toekennen gebruikersgroep";

	$lang_rep_SelectAtLeastOneCriteriaAndOneField = "Selecteer een criteria en een veld";
	$lang_rep_SelectTheComparison = "Selecteer de vergelijkingen";

	$lang_rep_AgeShouldBeNumeric = "Leeftijd moet numeriek zijn";
	$lang_rep_InvalidAgeRange = "2de geselecteerde leeftijd moet lager zijn dan 1st geselecteerde leeftijd";

	$lang_rep_FieldNotSelected = "Geen veld geselecteerd";

	$lang_rep_DateShouldBeNumeric = "Datum moet numeriek zijn";
	$lang_rep_ValueShouldBeinYears = "Waarde moet in jaren zijn";
	$lang_rep_InvalidRange = "Ongeldige range";

	$lang_rep_Report = "Rapport";
	$lang_rep_EmployeeNo = "Werknemers nummer";
	$lang_rep_EmployeeFirstName = "Voornaam werknemer";
	$lang_rep_EmployeeLastName = "Achternaam werknemer";
	$lang_rep_ReportTo = "Reporteerd aan";
	$lang_rep_ReportingMethod = "Reportage methode";
	$lang_rep_Address = "Adres";
	$lang_rep_Telephone = "Telefoon";
	$lang_rep_DateOfBirth = "Geboortedatum ";
	$lang_rep_Skills = "Vaardigheden";
	$lang_rep_SalaryGrade = "Salaris schaal";
	$lang_rep_EmployeeStatus = "Status werknemer";
	$lang_rep_JoinedDate = "In dienst";
	$lang_rep_SubDivision = "Sub divisie";
	$lang_rep_JobTitle = "Functie titel";
	$lang_rep_YearOfPassing = "Geslaagd in jaar";
	$lang_rep_Contract = "Contract";
	$lang_rep_WorkExperience = "Werk ervaring";

	$lang_rep_SelectAtLeaseOneUserGroupToAssign = "Selecteer 1 gebruikersgroep om toe te kennen";
	$lang_rep_SelectAtLeaseOneUserGroupToDelete = "Selecteer minstens 1 gebruikersgroep om te verwijderen";

	$lang_rep_ReportDefinition = "Rapport Omschrijving";
	$lang_rep_AssignUserGroups = "Toekennen gebruikersgroepen";
	$lang_rep_UserGroups = "Gebruikers groepen";
	$lang_rep_UserGroup = "Gebruikers groep";
	$lang_rep_NoUserGroupsAssigned = "Geen gebruikersgroepen toegekend";
	$lang_rep_SelectUserGroup = "Selecteer gebruikers groep";
	$lang_rep_NoGroupSelected = "Selecteer aub een gebruikers groep om toe te wijzen";
	$lang_rep_ReportNameEmpty = "Raport naam is leeg";

	$lang_rep_Error_DUPLICATE_NAME_ADDED = "Reportage naam bestaat reeds.";
	$lang_rep_Error_ADD_FAILURE = "Rapport kon niet toegevoegd worden.";
	$lang_rep_Error_UPDATED_TO_DUPLICATE_NAME = "Wijzigingen aan de rapportage konden niet worden verwerkt. De rapportage naam bestaat reeds.";
	$lang_rep_Error_UPDATE_FAILURE = "Wijzigingen aan de rapportage konden niet worden verwerkt";

	// Skills
	$lang_Admin_Skill_Errors_NameCannotBeBlank = "Naam kan niet een lege waarde bevatten!";


	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "Mail Configuratie";
	$lang_Admin_SubscribeToMailNotifications = "Een notificatie per E-mail";
	$lang_Admin_ENS_LeaveApplications = "Verlofaanvragen";
	$lang_Admin_ENS_LeaveApprovals = "Goedgekeurd verlof";
	$lang_Admin_ENS_LeaveCancellations = "Geannuleerd verlof";
	$lang_Admin_ENS_LeaveRejections = "Geweigerd verlof";
	$lang_Admin_ENS_JobApplications = "Baan sollicitaties";
	$lang_Admin_ENS_HspNotifications = "HSP notificaties";
    $lang_Admin_ENS_SeekHireApproval = "Nieuwe medewerker werven goedkeurings verzoek";
    $lang_Admin_ENS_HiringTasks = "Taken verzonden voor indienst nemen van medewerker";
    $lang_Admin_ENS_HiringApproved = "Notificaties voor indienst nemen nieuwe medewerker";

	//Users
	$lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong = "Gebruikersnaam moet tenminste 4 karakters zijn";
	$lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong = "Wachtwoord moet tenminste 4 karakters lang zijn";
	$lang_Admin_Users_ErrorsPasswordMismatch = "Wachtwoord incorrect";
	$lang_Admin_Users_Errors_EmployeeIdShouldBeDefined = "Werknemers ID moet gedefineerd zijn";
	$lang_Admin_Users_Errors_FieldShouldBeSelected = "Veld moet geselecteerd zijn";
	$lang_Admin_Users_Errors_ViewShouldBeSelected = "Weergave moet geselecteerd zijn";
	$lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Wachtwoorden komen niet overeen, geef wachtwoorden opnieuw op";
	$lang_Admin_Users_WrongPassword = "Komt niet overeen met uw oude wachtwoord";

	$lang_Admin_Users_Errors_DoYouWantToClearRights = "Wilt u de rechten verwijderen";
	$lang_Admin_Users_Errors_SameGroup = "Uw account behoord tot deze gebruikers groep. U bent niet gemachtigt om deze gebruikers groep aan te passen";
	$lang_Admin_Users_Errors_NameCannotBeBlank = "Naam mag niet leeg zijn";

	$lang_Admin_Users_Modules = "Modules";
	$lang_Admin_Users_Module = "Module";
	$lang_Admin_Users_RightsAssignedToUserGroups = "Rechten zijn toegepast op gebruikers groep";
	$lang_Admin_Users_UserGroup = "Gebruikers groep";
	$lang_Admin_Users_UserGroupId = "Gebruikers groep ID";
	$lang_Admin_Users_SelectModule = "Selecteer module";

	$lang_Admin_Users_UserName = "Gebruikersnaam";
	$lang_Admin_Users_Password = "Wachtwoord";
	$lang_Admin_Users_Employee = "Medewerker";

	$lang_Admin_Users_ConfirmPassword = "Wachtwoord bevestingen";

	$lang_Admin_Users_Status = "Status";
	$lang_Admin_Users_Enabled = "Actief";
	$lang_Admin_Users_Disabled = "Niet actief";

	$lang_Admin_Users_UserGroup = "Beheerders gebruikers groep";
	$lang_Admin_Users_SelectUserGroup = "Selecteerd gebruikers groep";

	$lang_Admin_Users_NewPassword = "Nieuw wachtwoord";
	$lang_Admin_Users_ConfirmNewPassword = "Bevestig nieuw wachtwoord";

	$lang_Admin_Users_add = "Toevoegen";
	$lang_Admin_Users_delete = "Verwijderen";
	$lang_Admin_Users_view = "Weergeven";
	$lang_Admin_Users_edit = "Wijzigen";

	$lang_Admin_Users_AssignedRights = "Toegekende rechten";
	$lang_Admin_Users_DefineReports = "Defineer rapporten";
	$lang_Admin_Users_Assign_User_Rights = "Wijs gebruiikers rechten toe";
	$lang_Admin_Users_View_User_Rights = "Bekijk gebruikers rechten";

	$lang_Admin_Change_Password_OldPassword = "Oud wachtwoord";

	$lang_Admin_Change_Password_Errors_EnterYourOldPassword = "Geef uw oude wachtwoord";
	$lang_Admin_Change_Password_Errors_EnterYourNewPassword = "Geef uw nieuwe wachtwoord";
	$lang_Admin_Change_Password_Errors_RetypeYourNewPassword = "Bevestig uw nieuwe wachtwoord";
	$lang_Admin_Change_Password_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Wachtwoorden komen niet overeen. Geef opnieuw uw nieuwe wachtwoord";
	$lang_Admin_Change_Password_Errors_YourOldNewPasswordsAreEqual = "Uw oude en nieuwe wachtwoord zijn gelijk";

	$lang_Admin_Project = "Project";
	$lang_Admin_Project_CutomerName = "Klant naam";
	$lang_Admin_Project_SelectCutomer = "Selecteer klant";
	$lang_Admin_Project_Administrators = "Project administrators";
	$lang_Admin_Project_EmployeeName = "Medewerker naam";
	$lang_Admin_Project_EmployeeAlreadyAnAdmin = "Geselecteerde medewerker is reeds een project admin voor dit project.";

	$lang_Admin_Project_Error_PleaseDSpecifyTheProjectId = "Geef aub het project ID op";
	$lang_Admin_Project_Error_PleaseSelectACustomer = "Selecteer een klant";
	$lang_Admin_Project_Error_PleaseSpecifyTheName = "Geef aub de naam op";

	$lang_Admin_Customer_PleaseSpecifyTheCustormerId = "Geef aub het klant ID op";
	$lang_Admin_Customer_Error_PleaseSpecifyTheName = "Geef aub de naam op";

	$lang_Admin_ProjectActivities = "Project activiteiten";
	$lang_Admin_Activity = "Activiteit";
	$lang_Admin_Project_Activity_ClickOnActivityToEdit = "Klik op een bestaande activiteit om te hernoemen";
	$lang_Admin_Project_Activity_Error_PleaseSpecifyTheActivityName = "Geef de activiteiten naam op";
	$lang_Admin_Project_Error_NoProjects = "Geen projecten gevonden.";
	$lang_Admin_Project_NoActivitiesDefined = "Geen activiteiten gedefinieerd.";
	$lang_Admin_Project_Activity_Error_NameAlreadyDefined = "Een activiteit met dezelfde naam bestaat al in dit project. Selecteer aub een andere naam.";

    //Company Property
    $lang_Admin_Company_Property = "Bedrijfs eigendommen";
    $lang_Admin_Company_Property_Title = "Bedrijfs info: Bedrijfs eigendommen";
    $lang_Admin_Property_Name = "Eigendoms naam";
    $lang_Admin_Prop_Emp_Name = "Medewerker";
    $lang_Admin_Company_Property_Err_Del_Not_Sel = "Selecteer aub een eigendom om te verwijderen!";
    $lang_Admin_Company_Property_Err_Name_Empty = "Eigendoms naam is leeg!";
    $lang_Admin_Company_Property_Err_Name_Exists = "Eigendoms naam bestaat al!";
    $lang_Admin_Company_Property_Warn_Delete = "Wilt u de gelecteerde items verwijderen?";
    $lang_Admin_Property_Please_Select = "Niet toegewezen";

	//timesheet
	$lang_Time_Module_Title = "OrangeHRM - Tijd en opkomst module";
	$lang_Time_Timesheet_TimesheetForViewTitle = "Urenstaat voor #periodName startend #startDate";
	$lang_Time_Timesheet_TimesheetForEditTitle = "Wijzig urenstaat voor #periodName startend #startDate";

	$lang_Time_Timesheet_TimesheetNameForViewTitle = "Urenstaat van #name voor #periodName startend #startDate";
	$lang_Time_Timesheet_TimesheetNameForEditTitle = "Wijzig urenstaat van #name voor #periodName startend #startDate";

	$lang_Time_Timesheet_Status = "Status: #status";

	$lang_Time_Timesheet_Status_NotSubmitted = "Niet toegevoegd";
	$lang_Time_Timesheet_Status_Submitted = "Toegevoegd";
	$lang_Time_Timesheet_Status_Approved = "Geaccepteerd";
	$lang_Time_Timesheet_Status_Rejected = "Afgewezen";

	$lang_Time_Timesheet_Customer = "Klant";
	$lang_Time_Timesheet_ProjectActivity = "Project / Activiteiten";
	$lang_Time_Timesheet_Project = "Project";
	$lang_Time_Timesheet_Activity = "Activiteit";

	$lang_Time_Timesheet_Total = "Total";
	$lang_Time_TimeFormat = "Tijd formaat";

	$lang_Time_Errors_SUBMIT_SUCCESS = "Toevoegen succesvol";
	$lang_Time_Errors_SUBMIT_FAILURE = "Toevoegen mislukt";
	$lang_Time_Errors_UPDATE_SUCCESS = "Aanpassen succesvol";
	$lang_Time_Errors_UPDATE_FAILURE = "Aanpassen mislukt";
	$lang_Time_Errors_CANCEL_SUCCESS = "Annuleren succesvol";
	$lang_Time_Errors_CANCEL_FAILURE = "Annuleren mislukt";
	$lang_Time_Errors_APPROVE_SUCCESS = "Accepteren succesvol";
	$lang_Time_Errors_APPROVE_FAILURE = "Accepteren mislukt";
	$lang_Time_Errors_REJECT_SUCCESS = "Afwijzen succesvol";
	$lang_Time_Errors_REJECT_FAILURE = "Afwijzen mislukt";
	$lang_Time_Errors_DELETE_SUCCESS = "Succesvol verwijderd";
	$lang_Time_Errors_DELETE_FAILURE = "Verwijderen mislukt";
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "U bent niet gemachtigd om deze handeling te doen";
	$lang_Time_Errors_APPROVED_TIMESHEET_FAILURE = "Tijd evenement kan niet worden toegevoegd aan een goedgekeurde urenstaat";
	$lang_Time_Errors_REJECTED_TIMESHEET_FAILURE = "Tijd evenement kan niet worden toegevoegd aan een afgekeurde urenstaat";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "Geen urenstaat gevonden";
	$lang_Time_Errors_INVALID_TIME_FAILURE = "Ongeldige tijd gespecificeerd";
	$lang_Time_Errors_EVENT_START_AFTER_END_ERROR = "Evenement start tijd na eind tijd.";
	$lang_Time_Errors_INVALID_TIMESHEET_PERIOD_ERROR = "Ongeldige urenstaat periode.";

	$lang_Time_Errors_EncounteredTheFollowingProblems = "De volgende problemen zijn gevonden";
	$lang_Time_Errors_EncounteredFollowingProblems = "De volgende problemen deden zich voor";
	$lang_Time_Errors_ReportedDateNotSpecified = "Datum niet opgegeven";
	$lang_Time_Errors_ProjectNotSpecified = "Project niet opgegeven";
	$lang_Time_Errors_CustomerNotSpecified = "Klant niet opgegeven";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified = "Ongeldige tijd of nul of negative interval opgegeven";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval = "Niet toegestaan om tijdsduur en interval op te geven tenzij ze overeen komen";
	$lang_Time_Errors_InvalidReportedDate_ERROR = "Ongeldige waarde voor rapportage datum.";
	$lang_Time_Errors_InvalidDuration_ERROR = "Duur zou een geldig positief nummer moeten zijn";
	$lang_Time_Errors_InvalidStartTime_ERROR = "Ongledige tijd waarde voor de start tijd";
	$lang_Time_Errors_InvalidEndTime_ERROR = "Ongeldige tijd waarde voor de eind tijd";
	$lang_Time_Errors_EVENT_OUTSIDE_PERIOD_FAILURE = "Evenement tijd moet in de urenstaat periode zijn.";
	$lang_Time_Errors_NoValidDurationOrInterval_ERROR = "Geen geldige duur of interval opgegeven";
	$lang_Time_Errors_ZeroOrNegativeIntervalSpecified_ERROR = "Nul of negatieve interval opgegeven";
	$lang_Time_Errors_NO_EVENTS_WARNING = "Geen evenementen om op te slaan";

	$lang_Time_Timesheet_StartTime = "Start tijd";
	$lang_Time_Timesheet_EndTime = "Eind tijd";
	$lang_Time_Timesheet_ReportedDate = "Datum";
	$lang_Time_Timesheet_Duration = "Tijdsduur";
	$lang_Time_Timesheet_DurationUnits = "(uren)";
	$lang_Time_Timesheet_Decription = "Omschrijving";
	$lang_Time_Timesheet_NoCustomers = "Geen klanten";
	$lang_Time_Timesheet_NoProjects = "Geen projecten";

	$lang_Time_Select_Employee_Title = "Selecteer medewerker";

	$lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval = "Ingevoerde urenstaat in de wachtrij voor goedkeuring door management";
	$lang_Time_Select_Employee_WeekStartingDate = "Week start #date";

	$lang_Time_Select_Employee_TimesheetPeriod = "Urenstaat periode";

	$lang_Time_Errors_PleaseAddAComment = "Voeg commentaar toe";

	$lang_mtview_ADD_SUCCESS 				= "Succesvol toegevoegd";
	$lang_mtview_UPDATE_SUCCESS 			= "Succesvol bijgewerkt";
	$lang_mtview_DELETE_SUCCESS 			= "Succesvol verwijderd";
	$lang_mtview_ADD_FAILURE 				= "Toevoegen mislukt";
	$lang_mtview_UPDATE_FAILURE 			= "Bijwerken mislukt";
	$lang_mtview_DELETE_FAILURE 			= "Verwijderen mislukt";

	// 2.2 Time module
	$lang_Time_PunchInPunchOutTitle = "Klok in/uit";
	$lang_Time_PunchIn = "In";
	$lang_Time_PunchOut = "Uit";

	$lang_Time_LastPunchIn = "Laatste inklok tijd:";
	$lang_Time_LastPunchOut = "Laatste uitklok tijd:";

	$lang_Time_Errors_InvalidDateOrTime = "Ongeldige datum of tijd";
	$lang_Time_Errors_CurrentPunchTimeBeforeLastPunchTime = "Klok tijd kan niet zijn voor de laatste tijd geklokt";
	$lang_Time_Errors_ZeroOrNegativeDurationTimeEventsAreNotAllowed = "Nul of negatieve duren zijn niet toegestaan";
	$lang_Time_Errors_ActivityNotSpecified_ERROR = "Activiteit niet opgegeven";

	$lang_Menu_Time_PunchInOut = "Klik in/uit";

	$lang_Time_Timesheet_SelectProject = "Selecteer een project";

	$lang_Time_Timesheet_UnfinishedActivitiesTitle = "Onafgemaakte activiteiten";
	$lang_Time_SubmitTimeEventTitle = "Geef tijd evenement op";

	$lang_Time_Timesheet_DateReportedFor = "Datum voor rapportage";

	$lang_Time_UnfinishedActivitiesTitle = "Onafgemaakte activiteiten";
	$lang_Time_NewEvent = "Nieuw evenement";
	$lang_Time_Complete = "Kompleet";

	$lang_Time_DurationFormat = "hh:mm of 0.00u";
	$lang_Time_InsertTime="Voeg tijd in";

	$lang_Time_Errors_NO_RECORDS_CHANGED_WARNING = "Geen wijzigingen om op te slaan";
	$lang_Time_Errors_EXCEPTION_THROWN_WARNING = "Er heeft zich een probleem opgedaan";

	$lang_Menu_Time_ProjectTime = "Project tijd";

	$lang_Time_DefineTimesheetPeriodTitle = "Geef urenstaat periode op";

	$lang_Time_FirstDayOfWeek = "Eerste dag van de week";

	$lang_Menu_Time_DefineTimesheetPeriod = "Geef urenstaat periode op";

	$lang_Time_EmployeeTimeReportTitle = "Medewerker tijd rapportage";

	$lang_Time_Common_FromDate = $lang_Leave_Common_FromDate;
	$lang_Time_Common_ToDate = $lang_Leave_Common_ToDate;
	$lang_Time_Common_All = $lang_Leave_Common_All;

	$lang_Time_Errors_EmployeeNotSpecified = "Medewerker niet opgegeven";

	$lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified = "Ongeldige datum of nul of negatieve reeks opgegeven";

	$lang_Menu_Time_EmployeeReports = "Medewerker rapportages";

	$lang_Time_SelectTimesheetsTitle = "Selecteer urenstaat";
	$lang_Time_Division = $lang_compstruct_Division;
	$lang_Time_Supervisor = $lang_empview_Supervisor;
	$lang_Time_EmploymentStatus = $lang_empview_EmploymentStatus;

	$lang_Time_NoEmploymentStatusDefined = "Geen medewerker status opgegeven";

	$lang_Time_SelectWeekStartDay = "Selecteer de startdag van de week";
	$lang_Time_ContactAdminForTimesheetPeriodSet = "Startdag van de week voor de urenstaat is niet opgegeven. Neem contact op met de HR manager";
	$lang_Time_ContactAdminForTimesheetPeriodSetComplete = "Urenstaat startdag is bepaald";
	$lang_Time_ProceedWithTimeModule = "Ga verder met de Tijd Module";

	$lang_Time_PrintTimesheetsTitle = "Print urenstaat";
	$lang_Time_Print = "Print";

	$lang_Common_Loading = "Laden";
	$lang_Common_Select = "Selecteer";

	$lang_Menu_Time_PrintTimesheets = "Print urenstaat";

	$lang_Menu_Time_ProjectReports = "Project rapportages";
	$lang_Time_ProjectReportTitle = "Project rapportages";
	$lang_Time_Report_To = "Aan";
	$lang_Time_Report_From = "Van";
	$lang_Time_TimeInHours = "Tijd (uren)";
	$lang_Time_ActivityReportTitle = "Activiteiten details";
	$lang_Time_Activity_Report_EmployeeName = "Medewerker naam";
	$lang_Time_Activity_Report_View = "Bekijk";
	$lang_Time_Activity_Report_TotalTime = "Totaal tijd";
	$lang_Time_Activity_Report_NoEvents = "Geen evenementen gevonden.";

	$lang_Time_Errors_PROJECT_NOT_FOUND_FAILURE = "Project niet gevonden.";
	$lang_Time_Errors_ACTIVITY_NOT_FOUND_FAILURE = "Activiteit niet gevonden.";

	$lang_Common_Yes = "Ja";
	$lang_Common_No = "Nee";

	$lang_Leave_DoYouWantToCancelTheLeaveYouJustAssigned = "Wilt u het verlof annuleren die u zojuist heeft toegewezen?";
	$lang_Leave_PleaseProvideAReason = "Geef aub een reden op";

	$lang_Time_Errors_OVERLAPPING_TIME_PERIOD_FAILURE = "U heeft een overlappende tijdperiode in uw tijd evenementen";

	$lang_view_DUPLICATE_NAME_FAILURE = "Dezelfde naam is reeds in gebruik";

	$lang_Leave_CopyLeaveQuotaFromLastYear = "Kopieer verlof quota van verleden jaar";
	$lang_Leave_CopyLeaveBroughtForwardFromLastYear = "Kopieer verlof van vorig jaar";

	$lang_Leave_LEAVE_QUOTA_COPY_SUCCESS = "Succesvol verlof quota gekopieerd";
	$lang_Leave_LEAVE_QUOTA_COPY_FAILURE = "Kopieeren van verlof quota mislukt";

	$lang_Leave_LEAVE_BROUGHT_FORWARD_COPY_SUCCESS = "Succesvol verlof gekopieerd";
	$lang_Leave_LEAVE_BROUGHT_FORWARD_COPY_FAILURE = "Verlof kopieeren mislukt";

	// Recruitment module
 	$lang_Recruit_VacancyID = 'Vacature ID';
 	$lang_Recruit_JobTitleName = 'Functie naam';
 	$lang_Recruit_HiringManager = 'Aanvrager';
 	$lang_Recruit_VacancyStatus = 'Status';
 	$lang_Recruit_JobVacancyDeletionMessage = 'Verwijderen kan effect hebben op de sollicitaties op die functie';
 	$lang_Recruit_JobVacancyListHeading = 'Functie vacatures';
 	$lang_Recruit_JobVacancy_Active = 'Aktief';
 	$lang_Recruit_JobVacancy_InActive = 'Niet aktief';
 	$lang_Recruit_JobVacancy_Add_Heading = 'Voeg functie vacature toe';
 	$lang_Recruit_JobVacancy_Edit_Heading = 'Bewerk functie vacature';
 	$lang_Recruit_JobVacancy_JobTitleSelect = 'Selecteer';
 	$lang_Recruit_JobVacancy_HiringManagerSelect = 'Selecteer';
 	$lang_Recruit_JobVacancy_PleaseSpecifyJobTitle = 'Specificeer aub een functie titel';
 	$lang_Recruit_JobVacancy_PleaseSpecifyHiringManager = 'Specificeer aub de aanvrager';
 	$lang_Recruit_AllowedValuesAre = 'Toegestane waardes zijn: ';
	$lang_Recruit_NoManagersNotice = 'Geen managers gevonden. U moet een nieuwe functie titel aanmaken genaamd \'Manager\' en deze toewijzen aan de medewerkers die manager zijn.';
	$lang_Recruit_NoHiringManagersNotice = 'Geen medewerkers gevonden die zijn toegewezen als aanvragers. U moet de medewerker\' data toevoegen aan het systeem.';

    $lang_Recruit_JobApplicationList_Heading = 'Sollicitanten';
    $lang_Recruit_JobApplicationList_Name = 'Naam';
    $lang_Recruit_JobApplicationList_PositionApplied = 'Gesolliciteerd op positie';
    $lang_Recruit_JobApplicationList_Actions = 'Acties';
    $lang_Recruit_JobApplicationList_EventHistory = 'Proces geschiedenis';
    $lang_Recruit_JobApplicationList_Details = 'Details';
    $lang_Recruit_JobApplicationList_NoApplications = 'Geen sollicitanten gevonden';

    $lang_Recruit_JobApplicationStatus_Submitted = 'Sollicitatie verzonden';
    $lang_Recruit_JobApplicationStatus_FirstInterview = '1e sollicitatie gesprek';
    $lang_Recruit_JobApplicationStatus_SecondInterview = '2e sollicitatie gesprek';
    $lang_Recruit_JobApplicationStatus_JobOffered = 'Baan aangeboden';
    $lang_Recruit_JobApplicationStatus_OfferDeclined = 'Aanbod geweigerd';
    $lang_Recruit_JobApplicationStatus_PendingApproval = 'In afwachting van goedkeuring';
    $lang_Recruit_JobApplicationStatus_Hired = 'Aangenomen';
    $lang_Recruit_JobApplicationStatus_Rejected = 'Afgewezen';

    $lang_Recruit_JobApplicationAction_Reject = 'Afwijzen';
    $lang_Recruit_JobApplicationAction_FirstInterview = '1e sollicitatie gesprek inplannen';
    $lang_Recruit_JobApplicationAction_SecondInterview = '2e sollicitatie gesprek inplannen';
    $lang_Recruit_JobApplicationAction_OfferJob = 'Baan aanbieden';
    $lang_Recruit_JobApplicationAction_MarkDeclined = 'Kenmerk aanbod als afgewezen';
    $lang_Recruit_JobApplicationAction_SeekApproval = 'Vraag goedkeuring';
    $lang_Recruit_JobApplicationAction_Approve = 'Goedkeuren';

    $lang_Recruit_JobApplicationDetails_Heading = 'Sollicitatie details';
    $lang_Recruit_JobApplicationDetails_Status = 'Sollicitatie status';
    $lang_Recruit_JobApplicationDetails_Actions = 'Acties';
    $lang_Recruit_JobApplicationHistory_EventHistory = 'Proces geschiedenis';
    $lang_Recruit_JobApplicationHistory_ApplicationForThePositionOf = 'Sollicitatie voor de positie als';
    $lang_Recruit_JobApplicationHistory_DateApplied = 'Datum van toepassing';
    $lang_Recruit_JobApplicationHistory_NoEvents = 'Geen gebeurtenissen aanwezig';
    $lang_Recruit_JobApplicationHistory_FirstInterview = '1e sollicitatie gesprek';
    $lang_Recruit_JobApplicationHistory_SecondInterview = '2e sollicitatie gesprek';
    $lang_Recruit_JobApplicationHistory_Rejected = 'Afgewezen';
    $lang_Recruit_JobApplicationHistory_OfferedJob = 'Baan aangeboden';
    $lang_Recruit_JobApplicationHistory_OfferMarkedAsDeclined = 'Aanbod gekenmerkt als afgewezen';
    $lang_Recruit_JobApplicationHistory_SeekApproval = 'Vraag goedkeuring';
    $lang_Recruit_JobApplicationHistory_Approved = 'Goedgekeurd';
    $lang_Recruit_JobApplicationHistory_By = 'Door';
    $lang_Recruit_JobApplicationHistory_ScheduledBy = 'Ingeplaned door';
    $lang_Recruit_JobApplicationHistory_At = 'Op';
    $lang_Recruit_JobApplicationHistory_InterviewTime = 'Tijd sollicitatie gesprek';
    $lang_Recruit_JobApplicationHistory_Interviewer = 'Sollicitatie gesprek door';
    $lang_Recruit_JobApplicationHistory_Status = 'Status';
    $lang_Recruit_JobApplicationHistory_Notes = 'Notities';
    $lang_Recruit_JobApplicationHistory_StatusInterviewScheduled = 'Geplaned';
    $lang_Recruit_JobApplicationHistory_StatusFinished = 'Beeindigd';
    $lang_Recruit_JobApplication_ScheduleFirstInterview = 'Plan 1e sollicitatie gesprek voor';
    $lang_Recruit_JobApplication_ScheduleSecondInterview = 'Plan 2e sollicitatie gesprek voor';
    $lang_Recruit_JobApplication_Schedule_Date = 'Datum';
    $lang_Recruit_JobApplication_Schedule_Time = 'Tijd';
    $lang_Recruit_JobApplication_Schedule_Interviewer = 'Sollicitatie gesprek door';
    $lang_Recruit_JobApplication_Schedule_Notes = 'Notities';
    $lang_Recruit_JobApplication_Select = 'Selecteer';
    $lang_Recruit_JobApplication_PleaseSpecifyDate = 'Geef aub een datum voor het sollicitatie gesprek op';
    $lang_Recruit_JobApplication_PleaseSpecifyTime = 'Geef aub een tijd op voor het sollicitatie gesprek';
    $lang_Recruit_JobApplication_PleaseSpecifyInterviewer = 'Geef aub de persoon op die het sollicitiatie gesprek gaat houden';
    $lang_Recruit_JobApplication_PleaseSpecifyValidDate = 'Datum moet zijn in het formaat: ';
    $lang_Recruit_JobApplication_PleaseSpecifyValidTime = 'Tijd moet zijn in het formaat: ';
    $lang_Recruit_JobApplication_SecondInterviewShouldBeAfterFirst = 'Tweede sollicitatie gesprek datum moet zijn na het eerste sollicitatie gesprek datum ';

    $lang_Recruit_JobApplicationConfirm_Heading = 'Bevestig actie: ';
    $lang_Recruit_JobApplicationConfirm_ApplicantName = 'Naam sollicitant';
    $lang_Recruit_JobApplicationConfirm_Position = 'Solliciteert op de functie als';

    $lang_Recruit_JobApplicationConfirm_ConfirmReject = 'Bevestig afwijzing voor bovenstaande sollicitant';
    $lang_Recruit_JobApplicationConfirm_ConfirmRejectDesc = 'Dit zal een email versturen naar de sollicitant waarin deze geinformeerd wordt over zijn of haar afwijzing';
    $lang_Recruit_JobApplicationConfirm_ConfirmOfferJob = 'Bevestig baan aanbod voor bovenstaande sollicitant.';
    $lang_Recruit_JobApplicationConfirm_ConfirmOfferJobDesc = 'Er wordt geen email verzonden door het systeem naar de sollicitant. Deze moet benaderd worden en hem de baan aangeboden worden.';
    $lang_Recruit_JobApplicationConfirm_ConfirmMarkDeclined = 'Markeer aanbod als afgewezen';
    $lang_Recruit_JobApplicationConfirm_ConfirmMarkDeclinedDesc = 'Indicatie dat de sollicitant het baan aanbod heeft afgewezen.';
    $lang_Recruit_JobApplicationConfirm_ConfirmSeekApproval = 'Vraag goedkeuring';
    $lang_Recruit_JobApplicationConfirm_ConfirmApprove = 'Bevestig goedkeuring van bovenstaande sollicitant';
    $lang_Recruit_JobApplicationConfirm_ConfirmApproveDesc = 'Dit zal de sollicitant kenmerken als aangenomen en een medewerker record in het systeem aanmaken voor de sollicitant. De manager die de functie had uitgezet zal geinformeerd worden, maar er worden geen emails verzonden naar de sollicitant.';

    $lang_Recruit_JobApplication_SeekApproval_Heading = 'Vraag goedkeuring voor vacature ';
    $lang_Recruit_JobApplication_SeekApproval_GetApprovedBy = 'Krijg goedkeuring van ';
    $lang_Recruit_NoDirectorsNotice = 'Geen directeuren gevonden. Je moet een functie naam aanmaken met de naam \'Director\' en deze toewijzen aan de medewerkers die directeur zijn.';
    $lang_Recruit_JobApplication_SeekApproval_Notes = 'Notities';
    $lang_Recruit_JobApplication_SeekApproval_Desc = 'Een email zal worden verzonden naar de gekozen persoon, met daarin de vraag om de vacature goed te keuren.';
    $lang_Recruit_JobApplication_PleaseSpecifyDirector = 'Selecteer aub een directeur om goedkeuring van te krijgen';
    $lang_Recruit_JobApplication_PleaseSpecifyNotes = 'Vul aub wat notities in';

	$lang_Recruit_ApplicantVacancyList_Heading = 'Vacatures';
	$lang_Recruit_ApplicantVacancyList_Title = 'Vacatures';
	$lang_Recruit_ApplicationForm_Heading = 'Sollicitaite formulier voor een positie als ';
	$lang_Recruit_Application_CompanyNameNotSet = 'Bedrijfsnaam niet opgegeven';
	$lang_Recruit_ApplicationForm_Position = 'Sollicitatie voor de functie';
	$lang_Recruit_Applicant_NoVacanciesFound = 'Er zijn geen openstaande vacatures gevonden. Kom later nog eens terug.';

	$lang_Recruit_ApplicationForm_FirstName = 'Voornaam';
	$lang_Recruit_ApplicationForm_MiddleName = 'Tussenvoegsel';
	$lang_Recruit_ApplicationForm_LastName = 'Achternaam';
	$lang_Recruit_ApplicationForm_Street1 = 'Straat 1';
	$lang_Recruit_ApplicationForm_Street2 = 'Straat 2';
	$lang_Recruit_ApplicationForm_City = 'Plaats';
	$lang_Recruit_ApplicationForm_StateProvince = 'Provincie';
	$lang_Recruit_ApplicationForm_Country = 'Land';
	$lang_Recruit_ApplicationForm_Zip = 'Postcode';
	$lang_Recruit_ApplicationForm_Phone = 'Telefoon';
	$lang_Recruit_ApplicationForm_Mobile = 'Mobiele telefoon';
	$lang_Recruit_ApplicationForm_Email = 'E-mail';
	$lang_Recruit_ApplicationForm_Qualifications = 'Kwalificaties en kennis';

	$lang_Recruit_ApplicationForm_PleaseSpecify = 'Geef aub op ';
	$lang_Recruit_ApplicationForm_PleaseSpecifyValidEmail = 'Geef aub een geldig email adres op voor ';
	$lang_Recruit_ApplicationForm_PleaseSpecifyValidPhone = 'Geef aub een geldig telefoonnummer op voor ';
	$lang_Recruit_ApplicationForm_PleaseSelect = 'Selecteer aub ';

	$lang_Recruit_ApplicationStatus_SuccessHeading = 'Sollicitatie ontvangen';
	$lang_Recruit_ApplicationStatus_FailureHeading = 'Sollicitatie error';
	$lang_Recruit_ApplySuccess = 'Uw sollicitatie voor de functie als #jobtitle# is ontvangen. Een e-mail bevestiging is verzonden naar het volgende e-mail adres: #email#';
	$lang_Recruit_ApplyFailure = 'Er was een probleem met het verwerken van uw sollicitatie. Probeer het aub later nogmaals.';

	// login page
	$lang_login_title = "OrangeHRM - Nieuwe toepassing van HR management";
	$lang_login_UserNameNotGiven = "Gebruikersnaam niet opgegeven!";
	$lang_login_PasswordNotGiven = "Wachtwoord niet opgegeven!";
	$lang_login_NeedJavascript = "Uw heeft een browser nodig die JavaScript aan kan. Bv. ";
	$lang_login_MozillaFirefox = "Mozilla Firefox";
	$lang_login_YourSessionExpired = "Uw sessie is verlopen omdat u te lang niet actief was. Aub opnieuw aanmelden.";
	$lang_login_LoginName = "Gebruikersnaam";
	$lang_login_Password = "Wachtwoord";
	$lang_login_Login = "Aanmelden";
	$lang_login_Clear = "Schonen";
	$lang_login_InvalidLogin = "Ongeldige aanmelding";
	$lang_login_UserDisabled = "Gebruiker disabled";
	$lang_login_EmployeeTerminated = "Aanmelden gewijgerd";
	$lang_login_NoEmployeeAssigned = "Er is geen medewerker toegewezen aan het ESS gebruikers account";
	$lang_login_temporarily_unavailable = "Service tijdelijk niet beschikbaar";
	$lang_login_OrangeHRMDescription = " komt als een volledige oplossing voor het effectief managen en ontwikkelen van uw Human Resource. Het zal u assisteren in de complexe en strategische processen voor het managen van deze belangrijke afdeling voor uw onderneming. Gebaseerd op een modulaire architectuur, voorziet het u in een groot aantal HR activiteiten, met mogelijkheden die de hoofd HR mananagement activiteiten reflecteren. Het komt als een web geschikte aplicatie en de beschikbare flexibiliteit in ogenschouw nemende, OrangeHRM is een perfect platform voor het reengineering van uw HR processen en een nieuw level te bereiken in HR Management.";

	$lang_Leave_Common_FromTime = "Van tijd";
	$lang_Leave_Common_ToTime = "Tot tijd";
	$lang_Leave_Common_TotalHours = "Totaal uren";
	$lang_Leave_Error_ToTimeBeforeFromTime = "Tot tijd voor van tijd";
	$lang_Leave_Error_ZeroLengthHours = "De lengte van het verlof is nul uren";
	$lang_Leave_Error_TotalTimeMoreThanADay = "Totaal tijd is groter dan de shift lengte";
	$lang_Leave_Common_WorkshiftLengthIs = "Werkshift lengte is ";
	$lang_Leave_Error_PleaseSpecifyEitherTotalTimeOrTheTimePeriod = "Geef of de totaaltijd op of de tijdperiode";

	$lang_Leave_Error_DuplicateLeaveError = "Je verlof verzoek overlapt met de volgende reeds bestande verzoeken.";
	$lang_Leave_Error_DuplicateLeaveErrorInstructions = "Annuleer een bestaand verlof verzoek die overlapt of verander het verlof verzoek hieronder en probeer het opnieuw.";

	$lang_Leave_Error_DuplicateLeaveWarning = "De volgende verlof verzoeken zijn op dezelfde dag als het huidige verlof verzoek";
	$lang_Leave_Error_DuplicateLeaveWarningInstructions = "Bekijk de bestaande verlof verzoeken en geef deze opnieuw op of verander de verlof periode indien nodig.";

	$lang_Leave_Duration = "Duur (uren)";
	$lang_Common_Hours = "uren";
	$lang_Common_Days = "dagen";

	$lang_Time_WorkShifts = "Werk shifts";
	$lang_Time_ShiftName = "Shift naam";
	$lang_Time_HoursPerDay = "Uren per dag";
	$lang_Time_AvailableEmployees = "Beschikbare medewerkers";
	$lang_Time_AssignedEmployees = "Toegewezen medewerkers";
	$lang_Time_AssignEmployeesTitle = "Werk shift bewerken";

	$lang_Time_Error_SpecifyWorkShiftName = "Geef werk shift naam op";
	$lang_Time_Error_SpecifyHoursPerDay = "Geef uren per dag op";
	$lang_Time_Error_DecimalNotAllowed = "Decimalen zijn niet toegestaan";
	$lang_Time_Error_HoursPerDayShouldBePositiveNumber = "Uren per dag moet een positief nummer zijn";
	$lang_Time_Error_HoursPerDayShouldBeLessThan24 = "Uren per dag zou minder moeten zijn dan 24";
	$lang_Time_Error_NoEmployeeSelected = "Geen medewerker geselecteerd";

	$lang_Time_Errors_INVALID_WORK_SHIFT_FAILURE = "Er waren problemem in de details van de werk shift";
	$lang_Time_Errors_NO_RECORDS_SELECTED_FAILURE = "Geen records geselecteerd om te verwijderen";
	$lang_Time_Errors_UNKNOWN_ERROR_FAILURE = "Er heeft zich een probleem voor gedaan";
	$lang_Time_Errors_INVALID_ID_FAILURE = "Ongeldige id";

	$lang_Menu_Time_WorkShifts = "Werk shifts";

	include 'lang_default_benefits.php';

	$lang_Common_To = "to";
?>
