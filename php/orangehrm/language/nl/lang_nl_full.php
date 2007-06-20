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
 * @author: Age de Jong
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

	//leave
	$lang_Error_PleaseSelectAValidFromDate 			= "aub Selecteer een geldige begindatum";
	$lang_Error_PleaseSelectALeaveType 				= "aub Selecteer een verlof type";

	$lang_Error_LeaveDateCannotBeABlankValue 		= "Verlofdatum mag geen blanco veld zijn!";
	$lang_Error_NameOfHolidayCannotBeBlank 			= "Naam van de vakantiedag kan niet blanco zijn";

	$lang_Error_NoLeaveTypes 						= "Geen verlof types";
	$lang_Error_NoRecordsFound 						= "Geen velden gevonden!";

	$lang_Error_InvalidDate 						= "Geen geldige data";

	//PIM
	$lang_Error_LastNameNumbers						= "De achternaam bevat nummers. Wilt u doorgaan?";
	$lang_Error_FirstNameNumbers					= "Voornaam bevat nummers. Wilt u doorgaan?";
	$lang_Error_MiddleNameNumbers					= "Tussenvoegsel bevat nummers. Wilt u doorgaan?";
	$lang_Error_MiddleNameEmpty						= "Tussenvoegsel niet ingevuld. Wilt u doorgaan?";
	$lang_Error_LastNameEmpty						= "Achternaam niet ingevuld!";
	$lang_Error_FirstNameEmpty						= "Voornaam niet ingevuld!";
	$lang_Error_ChangePane							= "Aub gegevens opslaan voordat u naar een ander pagina gaat.";

	$lang_Error_UploadFailed						= "Upload fout!";

	// Company Structure
	$lang_Error_Company_General_Undefined 				= "Aub eerst de algemene bedrijfsinformatie invullen.";
	$lang_Error_CompStruct_UnitCount 					= "Ook #children onderdelen onder #parent zullen worden verwijderd";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "Bedrijfsstructuur kan gewijzigd worden";

	$lang_Error_SubDivisionNameCannotBeEmpty 			= "Sub-divisie naam niet ingevuld";
	$lang_Error_PleaseSelectATypeOrDefineACustomType 	= "aub Selecteer een type of definieer een klant";
	$lang_Error_CompStruct_LocEmpty 					= "aub Selecteer een locatie of definieer een nieuwe locatie and selecteer";

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
	$lang_Error_SelectAtLeastOneCheckBox 				= "Selecteer tenminste een vak";

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

	// Admin Module
	$lang_Menu_Admin 							= "Admin";

	$lang_Menu_Admin_CompanyInfo 				= "Bedrijfsinformatie";
	$lang_Menu_Admin_CompanyInfo_Gen 			= "Algemeen";
	$lang_Menu_Admin_CompanyInfo_CompStruct 	= "Bedrijfsstructuur";
	$lang_Menu_Admin_CompanyInfo_Locations 		= "Locaties";

	$lang_Menu_Admin_Job 						= "Functie";
	$lang_Menu_Admin_Job_JobTitles 				= "Functie titel";
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
	$lang_Menu_Admin_EmailSubscribe = "Omschrijf";
	$lang_Menu_Admin_Customers = "Klanten";
	$lang_Menu_Admin_Projects = "Projecten";

	// Leave Module
	$lang_Menu_Leave 									= "Verlof";
	$lang_Menu_Leave_PersonalLeaveSummary 				= "Persoonlijk verlof";
	$lang_Menu_Leave_EmployeeLeaveSummary 				= "verlof werknemer";
	$lang_Menu_Leave_LeaveSummary 						= "Verlof redenen";
	$lang_Menu_Leave_LeavesList 						= "Overzicht verlof";
	$lang_Menu_Leave_ApproveLeave 						= "Goedkeuring verlof";
	$lang_Menu_Leave_LeaveTypes 						= "Type verlof";
	$lang_Menu_Leave_Apply 								= "Toepassen";
	$lang_Menu_Leave_Assign								= "Verlof toewijzen";
	$lang_Menu_Leave_LeaveList 							= "Overzicht verlof";
	$lang_Menu_Leave_DefineDaysOff 						= "Vrije dagen";
	$lang_Menu_Leave_DefineDaysOff_Weekends 			= "Weekeindes";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays 	= "Specifieke vakantiedagen";

 	$lang_Leave_Title_Apply_Leave 						= "Verlof toepassen";
 	$lang_Leave_Title_Assign_Leave 						= "Verlof toewijzen";
	$lang_Leave_APPLY_SUCCESS 						= "Succesvol toegepast";
	$lang_Leave_APPLY_FAILURE 						= "Toepassen mislukt";
	$lang_Leave_APPROVE_SUCCESS 						= "Verlof toegekend";
	$lang_Leave_APPROVE_FAILURE 						= "Verlof toekennen mislukt";

	// Report
	$lang_Menu_Reports = "Rapporten";
	$lang_Menu_Reports_ViewReports = "Bekijk rapporten";
	$lang_Menu_Reports_DefineReports = "Nieuw rapport";

	// Time module
	$lang_Menu_Time = "Tijd";
	$lang_Menu_Time_Timesheets = "Tijdverantwoording";
	$lang_Menu_Time_PersonalTimesheet = "Persoonlijke tijdverantwoording";
	$lang_Menu_Time_EmployeeTimesheets = "Tijdverantwoording medewerkers";



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

	$lang_Common_Edit 						= "Verander";
	$lang_Common_New 						= "Nieuw";

	//days
	$lang_Common_Monday 					= "maandag";
	$lang_Common_Tuesday 					= "dinsdag";
	$lang_Common_Wednesday 					= "woensdag";
	$lang_Common_Thursday 					= "donderdag";
	$lang_Common_Friday 					= "vrijdag";
	$lang_Common_Saturday 					= "zaterdag";
	$lang_Common_Sunday 					= "zondag";

	$lang_Leave_Common_Weekend 				= "Weekend";

	// admin module
	$lang_Admin_Common_Institute 			= "Instituut";
	$lang_Admin_Common_Course 				= "Cursus";

	// leave module
	$lang_Leave_Common_Date 				= "Datum";

	$lang_Leave_Common_FromDate 			= "Van";
	$lang_Leave_Common_ToDate 				= "Tot";

	$lang_Leave_Common_LeaveQuotaNotAllocated = "Verlof quota niet toegewezen. Raadpleeg de administrator";
	$lang_Leave_Common_LeaveType 			= "Verlof type";
	$lang_Leave_Common_Status 				= "Status";
	$lang_Leave_Common_Length 				= "Volledige dag/halve dag";
	$lang_Leave_Common_Range 				= "Volgorde";
	$lang_Leave_Common_Comments 			= "Notities";
	$lang_Leave_Common_Comment 				= "Notitie";
	$lang_Leave_Common_Approved 			= "Akkoord";
	$lang_Leave_Common_Cancelled 			= "Geannuleerd";
	$lang_Leave_Common_PendingApproval 		= "In afwachting van akkoord";
	$lang_Leave_Common_Rejected 			= "Afgewezen";
	$lang_Leave_Common_Taken 				= "Reeds genomen";
	$lang_Leave_Common_InvalidStatus 		= "Ongeldige status";
	$lang_Leave_Common_FullDay 				= "Volledige dag";
	$lang_Leave_Common_HalfDayMorning 		= "Halve dag / ochtend";
	$lang_Leave_Common_HalfDayAfternoon 	= "Halve dag / middag";
	$lang_Leave_Common_HalfDay 				= "Halve dag";
	$lang_Leave_Common_LeaveTaken 			= "Opgenomen vrije dag";
	$lang_Leave_Common_LeaveAvailable 		= "Mogelijke vrije dagen";
	$lang_Leave_Common_LeaveTypeName 		= "Titel van vrije dag";
	$lang_Leave_Common_LeaveTypeId 			= "Vrije dag type Id";
	$lang_Leave_Common_Select 				= "Selecteer";
	$lang_Leave_Common_oldLeaveTypeName 	= "Bestaande titel vrije dagen";
	$lang_Leave_Common_newLeaveTypeName 	= "Nieuwe titel vrije dagen";
	$lang_Leave_Common_EmployeeName 		= "Naam werknemer";
	$lang_Leave_Common_LeaveEntitled 		= "Verlof goedgekeurd";
	$lang_Leave_Common_Year 				= "Jaar";
	$lang_Leave_Common_ListOfTakenLeave 	= "Opgenomen verlof dagen";
	$lang_Leave_Common_Remove 				= "Annuleren";
	$lang_Leave_Common_AllEmployees			= "Alle werknemers";
	$lang_Leave_Common_All					= "Alles";

	$lang_Leave_Common_NameOfHoliday 		= "Naam van Vakantie";
	$lang_Leave_Common_Recurring 			= "Herhalend";

 	$lang_Leave_Leave_list_Title1 			= "Vrije dag /dagen goedgekeurd";
	$lang_Leave_Leave_Requestlist_Title1 	= "Akkoord vrije dagen voor #employeeName";
	$lang_Leave_Leave_list_Title2 			= "Opgenomen vrije dagen door #employeeName in #dispYear jaar";
	$lang_Leave_Leave_list_Title3 			= "Lijst van vrije dagen";
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

	$lang_Leave_Holiday = "Vakantie";

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

 	$lang_electorateinformation_heading 			= "Electoraat informatie : Geografische information";
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
	$lang_empview_searchby 					= "Zoeken met:";
	$lang_empview_description 				= "Zoeken naar:";
	$lang_empview_norecorddisplay 			= "Geen gegevens";

	$lang_empview_last 						= "Laatste";
	$lang_empview_previous 					= "Vorige";
	$lang_empview_next 						= "Volgende";
	$lang_empview_first						= "Eerste";

	$lang_empview_employeeid 				= "Werknemer ID";
	$lang_empview_employeename 				= "Naam werknemer";
	$lang_empview_ADD_SUCCESS 				= "Toevoegen succesvol";
	$lang_empview_UPDATE_SUCCESS 			= "Wijziging succesvol";
	$lang_empview_DELETE_SUCCESS 			= "Verwijderen succesvol";
	$lang_empview_ADD_FAILURE 				= "Toegevoegen gefaald";
	$lang_empview_UPDATE_FAILURE 			= "Wijzigen gefaald";
	$lang_empview_DELETE_FAILURE 			= "Verwijderen gefaald";
	$lang_empview_Language 					= "Talen";
	$lang_empview_WorkExperience 			= "Werkervaring";
	$lang_empview_Payment 					= "Betaling";
	$lang_empview_Skills 					= "Vaardigheden";
	$lang_empview_Licenses 					= "Certificaten";
	$lang_empview_EmployeeInformation 		= "werknemer informatie";
	$lang_empview_Memberships 				= "Lid van";
	$lang_empview_Report 					= "Rapport";
	$lang_empview_ReportTo					= "Rapporteert aan";
	$lang_empview_SubDivision				= "Sub-divisie";
	$lang_empview_JobTitle					= "Functie titel";
	$lang_empview_Supervisor 				= "Leidinggevende";
	$lang_empview_EmploymentStatus			= "Werknemer status";

 	$lang_ethnicrace_heading 				= "Nationaliteit & origine : Origine";

 	$lang_extracurractcat_heading 			= "Nevenactiviteiten";

 	$lang_extracurractinfo_heading 			= "Nevenactiviteiten";
	$lang_extracurractinfo_extracuaccat 	= "Nevenactiviteiten categorie";
	$lang_extracurractinfo_selectsecucat 	= "Selecteer een nevenactiviteit categorie";

 	$lang_geninfo_heading 					= "Bedrijfs info : algemeen";
	$lang_geninfo_compname 					= "Bedrijfsnaam";
	$lang_geninfo_taxID 					= "BTW nummer";
	$lang_geninfo_naics 					= "KvK nummer";
	$lang_geninfo_err_CompanyName 			= "Bedrijfsnaam moet ingevuld zijn";
	$lang_geninfo_err_Phone 				= "Geen geldig telefoonnummer/faxnummer";

 	$lang_hierarchydef_heading 				= "Hierarchie informatie : Bedrijfsinformatie";

	$lang_hremp_EmpFirstName 				= "Voornaam";
	$lang_hremp_EmpLastName 				= "Achternaam";
	$lang_hremp_EmpMiddleName 				= "Tussenvoegsel";
	$lang_hremp_nickname 					= "Roepnaam ";
	$lang_hremp_photo 						= "Foto ";
	$lang_hremp_ssnno 						= "Sofinummer";
	$lang_hremp_nationality 				= "Nationaliteit";
	$lang_hremp_sinno 						= "Verzekeringsnummer";
	$lang_hremp_dateofbirth 				= "Geboorte datum";
	$lang_hremp_otherid 					= "Andere ID";
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
	$lang_hremp_eeocategory 				= "EEO categorie";
	$lang_hremp_seleeocat 					= "Selecteer EEO categorie";
	$lang_hremp_joindate 					= "In dienst";
	$lang_hremp_SelectJobTitle				= "Selecteer functie naam";
	$lang_hremp_EmpStatus					= "Werknemer status";
	$lang_hremp_Workstation					= "Positie";
	$lang_hremp_Subdivision					= "Sub-divisie";

 	$lang_hremp_dependents 					= "Naaste";
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
	$lang_hremp_largefileignore				= "1M max, groter wordt geweigerd";
	$lang_lang_uploadfailed					= "Upload fout";

	$lang_hremp_browse						= "Zoeken";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "Weet u zeker dat u de foto wilt verwijderen";

	$lang_hremp_SelectAPhoto				= "Selecteer een foto";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "Verwerkings dag moet voor einddatum zijn";
	$lang_hremp_FromDateShouldBeBeforeToDate= "Van datum moet voor tot datum";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "Begin dag moet voor eind dag";

	$lang_hremp_ContractExtensionStartDate	= "Contract verlenging begindatum";
	$lang_hremp_ContractExtensionEndDate	= "Contract verleging einddatum";
	$lang_hremp_EmployeeContracts 			= "Contracten werknemer";
	$lang_hremp_AssignedContracts			= "Toegekende contracten";

	$lang_hremp_ShowEmployeeContracts 			= "Laat werknemer contracten zien";
	$lang_hremp_HideEmployeeContracts 			= "Verberg werknemer contracten";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "Salaris moet tussen min en max zijn";

	$lang_hremp_SelectCurrency				= "Selecteer valuta";

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

	$lang_hremp_ie_CurrentSupervisors 		= "Werknemers huidige manager";
	$lang_hremp_ie_CurrentSubordinates 		= "Werknemers huidige ondergeschikten";

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

 	$lang_hrEmpMain_heading 				= "werknemers rapport";
	$lang_hrEmpMain_supervisorsubordinator 	= "Manager / ondergeschikten";
	$lang_hrEmpMain_reportingmethod 		= "Rapporteer methode";
	$lang_hrEmpMain_subordinateinfomation 	= "Informatie ondergeschikten";
	$lang_hrEmpMain_supervisorinfomation 	= "Informatie manager";
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

 	$lang_jobtitle_heading 					= "Functie : Titel van functie";
	$lang_jobtitle_jobtitid 				= "Functie naam ID";
	$lang_jobtitle_jobtitname 				= "Titel functie";
	$lang_jobtitle_jobtitdesc 				= "Werkomschrijving";
	$lang_jobtitle_jobtitcomments 			= "Functie naam opmerkingen";
	$lang_jobtitle_addpaygrade 				= "Toevoegen salaris schaal";
	$lang_jobtitle_emstatExpl 				= "Definieer werk status voor titel van baan";
	$lang_jobtitle_editpaygrade 			= "Verander salaris schaal";
	$lang_jobtitle_addempstat 				= "Toevoegen werk status";
	$lang_jobtitle_editempstat 				= "Verander werk status ";
	$lang_jobtitle_empstat 					= "Status werknemer";

 	$lang_languageinformation_heading 		= "Vaardigheden : Talen";

 	$lang_licenses_heading 					= "Kwalificatie : Certificaten";

 	$lang_locations_heading 				= "Bedrijfs info : Locaties";

 	$lang_membershipinfo_heading 			= "Lidmaatschap : Lidmaatschap";
	$lang_membershipinfo_membershiptype 	= "Lidmaatschap type";
	$lang_membershipinfor_selectmember 		= "Selecteer lidmaatschap";

 	$lang_membershiptypes_heading 			= "Lidmaatschap : Lidmaatschap type";

 	$lang_nationalityinformation_heading 	= "Nationaliteit & Origine : Nationaliteit";

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

	$lang_salcurrdet_heading 				= "Valuta toegekend aan salaris schaal";
	$lang_salcurrdet_assigncurr 			= "Toegekende valuta";

 	$lang_satutoryinfo_heading 				= "Statutair type : Nexus informatie";

 	$lang_view_EmploymentStatusID 			= "Werk status ID";
	$lang_view_EmploymentStatusName 		= "Werk status Naam";
	$lang_view_message 						= "Verwijderen kan baan titles beinvloeden";
	$lang_view_message1 					= "Verwijderen kan salaris schaal van werknemers beinvloeden in PIM";
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

	$lang_index_WelcomeMes 			= "Welkom #username";
	$lang_index_ChangePassword 		= "Verander wachtwoord";
	$lang_index_Logout 				= "Uitloggen";

	$lang_MailFrom = "E-mail gestuurd vanaf";
	$lang_MailSendingMethod = "Verzendmethode";
	$lang_MailTypes_Mail = "Intern PHP functie";
	$lang_MailTypes_Sendmailer = "Stuur E-mail";
	$lang_MailTypes_Smtp = "SMTP";
	$lang_SendmailPath = "Pad naar Sendmail";
	$lang_SmtpHost = "SMTP Host";
	$lang_SmtpPort = "SMTP poort";
	$lang_SmtpUser = "SMTP gebruiker";
	$lang_SmtpPassword = "SMTP wachtwoord";
	//Projects
	$lang_view_ProjectId = "Projectnummer";
	$lang_view_ProjectName = "Projectnaam";

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

	// Report module
	$lang_rep_SelectionCriteria = "Selectie criteria";
	$lang_rep_EmpNo = "Werknemers nummer";
	$lang_rep_Employee = "Werknemer";
	$lang_rep_AgeGroup = "Leeftijdsgroep";
	$lang_rep_PayGrade = "Salaris schaal";
	$lang_rep_Education = "Opleiding";
	$lang_rep_EmploymentStatus = "Werknemers status";
	$lang_rep_ServicePeriod = "Dienstverband";
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

	$lang_rep_ReportDefinition = "Rapport Omschrijving";
	$lang_rep_AssignUserGroups = "Toekennen gebruikersgroep";
	$lang_rep_UserGroups = "Gebruikers groep";
	$lang_rep_NoUserGroupsAssigned = "Geen gebruikersgroep toegekend";

	// Skills
	$lang_Admin_Skill_Errors_NameCannotBeBlank = "Naam kan niet een lege waarde bevatten!";


	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "Mail Configuratie";
	$lang_Admin_SubscribeToMailNotifications = "Een notificatie per E-mail";
	$lang_Admin_ENS_LeaveApplications = "Verlof aanvragen";
	$lang_Admin_ENS_LeaveApprovals = "Goedgekeurd verlof";
	$lang_Admin_ENS_LeaveCancellations = "Geannuleerd verlof";
	$lang_Admin_ENS_LeaveRejections = "Geweigerd verlof";

	//Users
	$lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong = "Gebruikersnaam moet tenminste 5 karakters zijn";
	$lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong = "Wachtwoord moet tenminste 4 karakters lang zijn";
	$lang_Admin_Users_ErrorsPasswordMismatch = "Wachtwoord incorrect";
	$lang_Admin_Users_Errors_EmployeeIdShouldBeDefined = "Werknemers ID moet gedefineerd zijn";
	$lang_Admin_Users_Errors_FieldShouldBeSelected = "Veld moet geselecteerd zijn";
	$lang_Admin_Users_Errors_ViewShouldBeSelected = "Weergave moet geselecteerd zijn";
	$lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Wachtwoorden komen niet overeen, geef wachtwoorden opnieuw op";

	$lang_Admin_Users_Errors_DoYouWantToClearRights = "Wilt u de rechten verwijderen";
	$lang_Admin_Users_Errors_SameGroup = "Uw account behoord tot deze gebruikers groep. U bent niet gemachtigt om deze gebruikers groep aan te passen";
	$lang_Admin_Users_Errors_NameCannotBeBlank = "Naam mag niet leeg zijn";

	$lang_Admin_Users_Modules = "Modules";
	$lang_Admin_Users_Module = "Module";
	$lang_Admin_Users_RightsAssignedToUserGroups = "Rechten zijn toegepast op gebruikers groep";
	$lang_Admin_Users_UserGroup = "Gebruikers groep";
	$lang_Admin_Users_UserGroupId = "Gebruikers groep ID";

	$lang_Admin_Users_UserName = "Gebruikersnaam";
	$lang_Admin_Users_Password = "Wachtwoord";

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

	$lang_Admin_Project_CutomerName = "Klant naam";
	$lang_Admin_Project_SelectCutomer = "Selecteer klant";

	$lang_Admin_Project_Error_PleaseDSpecifyTheProjectId = "Geef aub het project ID op";
	$lang_Admin_Project_Error_PleaseSelectACustomer = "Selecteer een klant";
	$lang_Admin_Project_Error_PleaseSpecifyTheName = "Geef aub de naam op";

	$lang_Admin_Customer_PleaseSpecifyTheCustormerId = "Geef aub het klant ID op";
	$lang_Admin_Customer_Error_PleaseSpecifyTheName = "Geef aub de naam op";
	//timesheet
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

	$lang_Time_Timesheet_Total = "Total";

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
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "U bent niet gemachtigt om deze handeling te doen";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "Geen urenstaat gevonden";

	$lang_Time_Errors_EncounteredTheFollowingProblems = "De volgende problemen zijn gevonden";
	$lang_Time_Errors_ReportedDateNotSpecified = "Datum niet opgegeven";
	$lang_Time_Errors_ProjectNotSpecified = "Project niet opgegeven";
	$lang_Time_Errors_CustomerNotSpecified = "Klant niet opgegeven";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified = "Ongeldige tijd of nul of negative interval opgegeven";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval = "Niet toegestaan om tijdsduur en interval op te geven tenzij ze overeen komen";

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
?>
