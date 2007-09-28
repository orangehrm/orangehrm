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

	$lang_Error_PleaseCorrectTheFollowing 			= "Por favor corrija lo siguiente";
	$lang_Error_SelectAtLeastOneRecordToDelete 		= "Seleccione por lo menos un registro para borrar";
	$lang_Error_DoYouWantToDelete 					= "¿Desea borrar?";
	$lang_Error_EnterDate							= "Introduzca fecha";

	$lang_Error_PleaseSelectAYear 					= "Por favor seleccione año";
	$lang_Error_PleaseSelectAnEmployee 				= "Por favor seleccione un empleado";

	$lang_Error_DoYouWantToContinue 				= "¿Desea continuar?";

	$lang_Error_PleaseSelectAnEmployee				= "Por favor seleccione un empleado";

	$lang_Error_ShouldBeNumeric 					= "Debe ser numérico";
	$lang_Error_FollowingErrorsWereFound 			= "Los siguientes errores han sido identificados";
	$lang_Error_AreYouSureYouWantToDelete 			= "¿Esta seguro que desea borrar?";

	//leave
	$lang_Error_PleaseSelectAValidFromDate 			= "Selecciones una opción válida de fecha desde";
	$lang_Error_PleaseSelectALeaveType 				= "Por favor seleccione un tipo de permiso";

	$lang_Error_LeaveDateCannotBeABlankValue 		= "La fecha de permiso no puede estar vacía";
	$lang_Error_NameOfHolidayCannotBeBlank 			= "El tipo de permiso no puede estar vacío";

	$lang_Error_NoLeaveTypes 						= "Falta el tipo de permiso";
	$lang_Error_NoRecordsFound 						= "No se hallan datos";

	$lang_Error_InvalidDate 						= "Fecha inválida";

	//PIM
	$lang_Error_LastNameNumbers						= "El Appellido contiene cifras. ¿Desea continuar?";
	$lang_Error_FirstNameNumbers					=  "El nombre contiene cifras. ¿Desea Continuar?";
	$lang_Error_MiddleNameNumbers					= "El Segundo nombre contiene cifras. ¿Desea Continuar?";

	$lang_Error_MiddleNameEmpty						= "Falta El Segundo nombre ¿Desea continuar?";
	$lang_Error_LastNameEmpty						= "Falta Apellido ”";
	$lang_Error_FirstNameEmpty						= "Falta Nombre ”";
	$lang_Error_ChangePane							= "Por favor guarde los cambios antes de proceder a otra ventana”";

	$lang_Error_UploadFailed						= "La Carga no se completó satisfactoriamente”";

	// Company Structure
	$lang_Error_Company_General_Undefined 				= "Por favor defina primero los datos generales de la compañía";
	$lang_Error_CompStruct_UnitCount 					= "también los datos de hijos incluidos bajo padres seran borrados";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "Podría causar cambios en la estructura de la compañía";

	$lang_Error_SubDivisionNameCannotBeEmpty 			= "El nombre de la subdivision no puede estar vacío";
	$lang_Error_PleaseSelectATypeOrDefineACustomType 	= "Por favor seleccione un tipo o defina una opción personalizada";
	//Custom is specific, “tailored”, particular for the user
	$lang_Error_CompStruct_LocEmpty 					= "por favor seleccione una localidad o defina una nueva localidad y seleccionela";

	$lang_Error_LocationNameEmpty 						= "Falta Localidad";
	$lang_Error_CountryNotSelected 						= "País no seleccionado";
	$lang_Error_StateNotSelected 						= "Estado no seleccionado";
	$lang_Error_CityCannotBeEmpty 						= "Cuidad no puede estar vacía";
	$lang_Error_AddressEmpty 							= "Falta dirección”";
	$lang_Error_ZipEmpty 								= "Código postal no puede estar vacío";
	$lang_Error_CompStruct_ZipInvalid 					= "El código postal contiene caracteres no numéricos: Aquí están #characterList";

	$lang_Error_InvalidEmail 							= "Direccion de correo electrónico erronea";

	$lang_Error_FieldShouldBeNumeric					= "El campo debe ser numérico";

	$lang_Error_FieldShouldBeSelected					= "Selecione campo";
	$lang_Error_SelectAtLeastOneCheckBox 				= "Seleccione al menos un recuadro";

	/**
	 * Menu Items
	 *
	 */

	// Home
	$lang_Menu_Home 							= "página principal";

	$lang_Menu_Home_Support 					= "Soporte técnico";
//It is technical support. It is the title of the menu item. Once they click on it – they will get the detail, how to contact support personnel, who can help in case of any difficulties.
	$lang_Menu_Home_Forum 						= "Foro";
	$lang_Menu_Home_Blog 						= "blog";

	// Admin Module
	$lang_Menu_Admin 							= "Administrador";
//This is also the title of menu item. It is ment for administrator of the system. if threre is any short version for ”Administrator”, please, use it.
	$lang_Menu_Admin_CompanyInfo 				= "Información de la comapñía";
	$lang_Menu_Admin_CompanyInfo_Gen 			= "General";
	$lang_Menu_Admin_CompanyInfo_CompStruct 	= "Estructura de la compañia";
	$lang_Menu_Admin_CompanyInfo_Locations 		= "Localidades";
//Locations in this case is geographical locations – addresses where company departments are located.

	$lang_Menu_Admin_Job 						= "puesto";
	$lang_Menu_Admin_Job_JobTitles 				= "nombre del puesto";
	$lang_Menu_Admin_Job_PayGrades 				= "categorías salariales";
	$lang_Menu_Admin_Job_EmpStatus 				= "Estatus de empleo";
//The status can be full time, part time, on contract, permanent, internship, etc.
	$lang_Menu_Admin_Job_EEO 					= "categorías de empleo";
//Translate only “Job Categories” and leave EEO. The examples of the categories are technicians, professionals, officials and administrators
	$lang_Menu_Admin_Quali 						= "Qualificaciones";
//It is the title of the menu. Under this title the Education and the licenses that employee possess are defined.
	$lang_Menu_Admin_Quali_Education 			= "Formación";
	$lang_Menu_Admin_Quali_Licenses 			= "Licencias y permisos" ;
//Here it can be any kind of liceses, that employee has, licenses that are required for the job. i.e. driving license

	$lang_Menu_Admin_Skills 					= "aptitudes";
	$lang_Menu_Admin_Skills_Skills 				= "aptitudes";
	$lang_Menu_Admin_Skills_Languages 			= "Idiomas";

	$lang_Menu_Admin_Memberships 					= "Membresías";
	$lang_Menu_Admin_Memberships_Memberships 		= "Memberships";
	$lang_Menu_Admin_Memberships_MembershipTypes 	= "membresías";

	$lang_Menu_Admin_NationalityNRace 				= "Clases de miembresía";
	$lang_Menu_Admin_NationalityNRace_Nationality 	= "Nacionalidad";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces 	= "Raza, etnia";

	$lang_Menu_Admin_Users 							= "Usuario";
	$lang_Menu_Admin_Users_UserGroups 				= "Administrador de Grupos de usuarios";
	$lang_Menu_Admin_Users_HRAdmin 					= "RH Administración de usuarios";
	$lang_Menu_Admin_Users_ESS 						= "ESS Users";
//ESS – Employee Self Service. “Usuarios de autoservicio de empleados”
	$lang_Menu_Admin_EmailNotifications = "Aviso de correo electronico";
	$lang_Menu_Admin_EmailConfiguration = "Configuración";
	$lang_Menu_Admin_EmailSubscribe = "Suscribir";
	$lang_Menu_Admin_Customers = "Clientes";
	$lang_Menu_Admin_Projects = "Proyectos";

	// Leave Module
	$lang_Menu_Leave 									= "Permiso";
	$lang_Menu_Leave_PersonalLeaveSummary 				= "Sumario de permisos del personal";
	$lang_Menu_Leave_EmployeeLeaveSummary 				= "Sumario de permisos del empleado";
	$lang_Menu_Leave_LeaveSummary 						= "Sumario de permisos";
	$lang_Menu_Leave_LeavesList 						= "Lista de permisos";
	$lang_Menu_Leave_ApproveLeave 						= "Permisos aprobados";
	$lang_Menu_Leave_LeaveTypes 						= "Definir tipo de permisos";
	$lang_Menu_Leave_Apply 								= "Aplicar";
	$lang_Menu_Leave_Assign								= "Asignar permiso";
	$lang_Menu_Leave_LeaveList 							= "Lista de permisos";
	$lang_Menu_Leave_DefineDaysOff 						= "Definir días de permiso";
	$lang_Menu_Leave_DefineDaysOff_Weekends 			= "Fines de semana";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays 	= "Días festivos espcíficos";
//These are the holidays specific for the country where the country is located, like Christmas, Independence days, etc.
 	$lang_Leave_Title_Apply_Leave 						= "Aplicar permiso";
 	$lang_Leave_Title_Assign_Leave 						= "asignar permiso";

	// Report
	$lang_Menu_Reports = "Informe";
	$lang_Menu_Reports_ViewReports = "Ver informes";
	$lang_Menu_Reports_DefineReports = "definir informes";

	// Time module
	$lang_Menu_Time = "Tiempo";
	$lang_Menu_Time_Timesheets = "Hojas de servicio";
	$lang_Menu_Time_PersonalTimesheet = "hoja de servicio personal";
	$lang_Menu_Time_EmployeeTimesheets = "Hoja de servicio de empleado";



	/**
	 * Common
	 */
	$lang_Common_ConfirmDelete				= "¿Desea borrar?";
	$lang_Common_FieldEmpty				= "Campo vacío";
	$lang_Common_SelectDelete			= "Seleccione por lo menos un dato para borrar";
	$lang_Common_SelectField			= "Seleccione un campo de búsqueda";
	$lang_Commn_RequiredFieldMark 			= "Los campos marcados con asterisco son obligatorios";
	$lang_Commn_code 						= "Código";
	$lang_Commn_description 				= "Descripción";
	$lang_Commn_title 						=  "Nombre";
//In this context Title = Name
	$lang_Commn_name 						= "Nombre";
	$lang_Commn_PleaseWait					= "Espere por favor";
	$lang_Common_Select 					= "Selecionar";
	$lang_Commn_Email						= "Correo electrónico";

	$lang_Common_Edit = "Editar";
	$lang_Common_New = "Nuevo";

	//days
	$lang_Common_Monday 					= "Lunes";
	$lang_Common_Tuesday 					= "Martes";
	$lang_Common_Wednesday 					= "Miércoles";
	$lang_Common_Thursday 					= "Jueves";
	$lang_Common_Friday 					= "Viernes";
	$lang_Common_Saturday 					= "Sábado";
	$lang_Common_Sunday 					= "Domingo";

	$lang_Leave_Common_Weekend 				= "Fin de semana";

	// admin module
	$lang_Admin_Common_Institute 			= "Instituto";
	$lang_Admin_Common_Course 				= "Curso";

	// leave module
	$lang_Leave_Common_Date 				= "Fecha";

	$lang_Leave_Common_FromDate 			= "Desde (indique fecha)";
	$lang_Leave_Common_ToDate 				= "Hasta (indique fecha)";

	$lang_Leave_Common_LeaveQuotaNotAllocated = "Cuota de vacaciones no definida. Consulte Administrador de RH"  ;
//It is Human Resources Administrator

	$lang_Leave_Common_LeaveType 			= "tipo de permiso";  ;
	$lang_Leave_Common_Status 				= "Estatus";
	$lang_Leave_Common_Length 				= "Jornada completa7media jornada";
	$lang_Leave_Common_Range 				= "Rango";
//This is the range of days, the interval. I.e. from 5th of may to 9th of May is range of days
	$lang_Leave_Common_Comments 			= "Comentarios";
	$lang_Leave_Common_Comment 				= "comentario";
	$lang_Leave_Common_Approved 			= "Aprobado";
	$lang_Leave_Common_Cancelled 			= "Cancelado";
	$lang_Leave_Common_PendingApproval 		= "Pendiente de aprobación";
	$lang_Leave_Common_Rejected 			= "Denegada";
	$lang_Leave_Common_Taken 				= "Tomada";
	$lang_Leave_Common_InvalidStatus 		= "Estatus inválido";
	$lang_Leave_Common_FullDay 				= "jornada completa";
	$lang_Leave_Common_HalfDayMorning 		= "media jornada/mañana";
	$lang_Leave_Common_HalfDayAfternoon 	= "Media jornada/tarde";
	$lang_Leave_Common_HalfDay 				= "media jornada";
	$lang_Leave_Common_LeaveTaken 			= "Permiso tomado";
	$lang_Leave_Common_LeaveAvailable 		= "permiso disponible";
	$lang_Leave_Common_LeaveTypeName 		= "Nombre de tipo depermiso";
	$lang_Leave_Common_LeaveTypeId 			= "Identificador de Tipo de permisos";
//ID = Identificator… Maybe there is also the short version of it in Spanish?
	$lang_Leave_Common_Select 				= "seleccionar";
	$lang_Leave_Common_oldLeaveTypeName 	= "Nombre de tipos de permiso existentes";
	$lang_Leave_Common_newLeaveTypeName 	= "Nuevo nombre de tipo de permiso";
	$lang_Leave_Common_EmployeeName 		= "Nombre del empleado";
	$lang_Leave_Common_LeaveEntitled 		= "Total Permisos disponibles";
//This is the quota of the leave days, assigned for an employee
	$lang_Leave_Common_Year 				= "Año";
	$lang_Leave_Common_ListOfTakenLeave 	= "lista de permisos disfrutados";
	$lang_Leave_Common_Remove 				= "Eliminar";
	$lang_Leave_Common_AllEmployees			= "Todos los empleados";
	$lang_Leave_Common_All					= "Todos";

	$lang_Leave_Common_NameOfHoliday 		= "Nombre del dia Festivo";
//Yes, exactly… It is used to identify the holidays
	$lang_Leave_Common_Recurring 			= "Se repite anualmente";

 	$lang_Leave_Leave_list_Title1 			= "Aprobar permiso";
	$lang_Leave_Leave_Requestlist_Title1 	= "Aprobar la solicitud de permiso a #employeeName de empleado";
	$lang_Leave_Leave_list_Title2 			= "Permisos tomados por #employeeName de empleado en #dispYear";
	$lang_Leave_Leave_list_Title3 			= "lista de permisos";
	$lang_Leave_Select_Employee_Title 		= "seleccionar empleado";
	$lang_Leave_Leave_Summary_Title 		= "Sumario de permisos";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Sumario de permisos de #employeeName de empleado en #dispYear";
	$lang_Leave_Select_Employee_Title 		= "Seleccione empleado";
	$lang_Leave_Leave_Summary_EMP_Title 	= "Sumario de permisos en #dispYear";
	$lang_Leave_Leave_Summary_SUP_Title 	= "Sumario de permisos por #employeeName de empleado en #dispYear";
	$lang_Leave_Define_leave_Type_Title 	= "definir tipo de permiso";
	$lang_Leave_Leave_Type_Summary_Title 	= "tipos de permiso";
	$lang_Leave_Leave_Holiday_Specific_Title = "Definir dias de permiso: dias festivos";
	$lang_Leave_Leave_Holiday_Weeked_Title 	= "Definir días de permiso: fines de semana";

	$lang_bankInformation_code 				= "código";
	$lang_bankInformation_description 		= "Descripción";

	$lang_compstruct_add 			= "Agregar"; // Translated add to spanish using Google lang tools
	$lang_compstruct_delete 		= "Borrar";
	$lang_compstruct_clear 			= "despejar";
	$lang_compstruct_hide 			= "Ocultar";
	$lang_compstruct_save 			= "Guardar";

 	$lang_comphire_heading 							= "2Jerarquía de la compañía: Información de la comapañía";
	$lang_comphire_relationalhierarchy 				= "Relaciones jerárquicas";
//This is the hierarchy, the order, the sequence of relations (i.e. within the company how are the departments related
	$lang_comphire_employee 						= "empleado";
	$lang_comphire_definitionlevel 					= "Do not translate";
	$lang_comphire_telephone 						= "teléfono";
	$lang_comphire_fax 								= "Fax";
	$lang_comphire_email							= "Correo electrónico";
	$lang_comphire_url 								= "URL";
	$lang_comphire_logo 							= "logo";
	$lang_comphire_selecthie 						= "Seleccionar jerarquía";
//This is the hierarchy, the order, the sequence of relations (i.e. within the company how are the departments related)

	$lang_comphire_selectdef 						= "Do not translate";
	$lang_compstruct_heading 						= "información de la compañía: estructura de la compañía";
	$lang_compstruct_frmSub_divisionHeadingAdd 		= "Añadir subdivision a";
	$lang_compstruct_frmSub_divisionHeadingEdit 	= "editar";
	$lang_compstruct_Name 							= "nombre";
	$lang_compstruct_Type 							= "tipo";
	$lang_compstruct_Division 						= "División” Division is similar to branch";
	$lang_compstruct_Description 					= "descripción";
	$lang_compstruct_Department 					= "Departamento";
	$lang_compstruct_Team 							= "equipo";
	$lang_compstruct_Other 							= "Otro";
	$lang_compstruct_Location 						= "Localidad";
//Locations in this case is geographical locations – addresses where company departments are located.

	$lang_compstruct_frmNewLocation 				= "definir nueva localidad";
//Locations in this case is geographical locations – addresses where company departments are located.

	$lang_compstruct_Address 						= "Dirección";
	$lang_compstruct_country						= "país";
	$lang_compstruct_state 							= "Provincia/Estado";
	$lang_compstruct_city 							= "Ciudad";
	$lang_compstruct_ZIP_Code 						= "Código postal";
	$lang_compstruct_Phone 							= "Teléfono";
	$lang_compstruct_no_root 						= "origen no encontrado" ;
//This message is being shown if the user tries to define the company structure, if he has not defined the company itself yet. Meaning in the hierarchy, the top most item (root) was not defined, but we are trying to define the “child” items…

 	$lang_corptit_heading 							= "Designación corporativa: descripción de puesto";
	$lang_corptit_topinhierachy 					= "Tope en la jerarquía";
	$lang_corptit_multipleheads 					= "Multiple Heads";
//Do not translate
	$lang_corptit_headcount 						= "Head Count";
//Do not translate
	$lang_corptit_nextlevelupgrade 					= "Ascender al siguiente nivel";
	$lang_corptit_selectcor 						= "Seleccionar designación corporativa";
	$lang_corptit_salarygrade 						= "categoría salarial";
	$lang_corptit_selectsal 						= "Seleccionar categoría salarial";

 	$lang_costcenters_heading 						= "Centro de coste : Información de la compañía";

 	$lang_countryinformation_heading 				= "Información de país: información geográfica";

 	$lang_currencytypes_heading 					= "Tipo de divisa: Información sobre puesto";


 	$lang_districtinformation_heading 				= "Información de cuidad: Informacioón geográfica";
	$lang_districtinformation_selectcounlist 		= "Seleccionar país";
	$lang_districtinformation_selstatelist 			= "Seleccionar estado";

 	$lang_eeojobcat_heading 						= "Puesto: EEO categoría de puesto";
//Do not translate, EEO, please. It is the specific job categorization in US.
	$lang_eeojobcat_description 					= "Designación de puesto";

 	$lang_electorateinformation_heading 			= "Electorate Information : Geo Information";
//Do not translate
	$lang_emprepinfo_heading						= "Definir informes de empleado";

 	$lang_emptypes_heading 					= "Employee Types : Nexus Information";//Do not translate
	$lang_emptypes_datelimited 				= "Fecha limitada";
	$lang_emptypes_prefix 					= "prefijo";

 	$lang_empview_heading 					= "estatus de empleo";
	$lang_empview_EmpID 					= "Identificación de empleado";
	$lang_empview_EmpFirstName 				= "Nombre de empleado";
	$lang_empview_EmpLastName 				= "Apellido de empleado";
	$lang_empview_EmpMiddleName 			= "Segundo nombre del empleado";

	$lang_empview_search					= "buscar";
	$lang_empview_searchby 					= "buscar por";
	$lang_empview_description 				= "Buscar para";
	$lang_empview_norecorddisplay 			= "no se hallan datos";

	$lang_empview_last 						= "último";
	$lang_empview_next 						= "siguiente";
	$lang_empview_previous 					= "anterior";
	$lang_empview_first						= "primero";

	$lang_empview_employeeid 				= "identificador de empleado";
	$lang_empview_employeename 				= "nombre de empleado";
	$lang_empview_ADD_SUCCESS 				= "añadido correctamente";
	$lang_empview_UPDATE_SUCCESS 			= "Actualidado correctamente";
	$lang_empview_DELETE_SUCCESS 			= "borrado correctamente";
	$lang_empview_ADD_FAILURE 				= "Falló añadir";
	$lang_empview_UPDATE_FAILURE 			= "Falló actualizar";
	$lang_empview_DELETE_FAILURE 			= "Falló borrar";
	$lang_empview_Language 					= "Idioma";
	$lang_empview_WorkExperience 			= "Experiencia de trabajo";
	$lang_empview_Payment 					= "pago";
	$lang_empview_Skills 					= "aptitudes";
	$lang_empview_Licenses 					= "Licencias y permisos" ;
//It is license that allows employees to do something (i.e. driving license – employee can drive a vehicle)


	$lang_empview_EmployeeInformation 		= "Información del empleado";
	$lang_empview_Memberships 				= "Membresías";
//It says about memberships in different associations, organizations, that the company employees might possess

	$lang_empview_Report 					= "Informe";
	$lang_empview_ReportTo					= "Reporta a";
	$lang_empview_SubDivision				= "Subdivisión";
	$lang_empview_JobTitle					= "Nombre del puesto";
	$lang_empview_Supervisor 				= "supervisor";



 	$lang_ethnicrace_heading 				= "nacionalidad y raza: Raza y etnia";

 	$lang_extracurractcat_heading 			= "Categoria de actividades extra curriculares. Información de cualificaciones";
//Employees

 	$lang_extracurractinfo_heading 			= "información de actividades extra curriculares: Información de cualificaciones";
	$lang_extracurractinfo_extracuaccat 	= "Categorías de actividades extra curriculares";
//The things employee does besides work… I.e. going to the gym, volunteering for some charity organization, etc.
	$lang_extracurractinfo_selectsecucat 	= "seleccionar categoria extra curricular";

 	$lang_geninfo_heading 					= "Información de la compañía: general";
	$lang_geninfo_compname 					= "nombre de la compañía";
	$lang_geninfo_taxID 					= "Numero de identificación fiscal";
	$lang_geninfo_naics 					= "NAICS";
//Do not translate
	$lang_geninfo_err_CompanyName 			= "Falta nombre de la compañía";
	$lang_geninfo_err_Phone 				= "número de teléfono/fax inválido";

 	$lang_hierarchydef_heading 				= "Información de jerarquía: Información de la compañía";
//Hierarchy – here it is the information of the structure of the company. How the company departments are related to each other

	$lang_hremp_EmpFirstName 				= "nombre";
	$lang_hremp_EmpLastName 				= "Apellido";
	$lang_hremp_EmpMiddleName 				= "Segundo nombre";
	$lang_hremp_nickname 					= "Apodo";
	$lang_hremp_photo 						= "foto ";
	$lang_hremp_ssnno 						= "número de seguridad social";
//Yes, it is Social sequrity number
	$lang_hremp_nationality 				= "nacionalidad";
	$lang_hremp_sinno 						= "número de SIN: " ;
//Just leave it as it is, it’s also US specific
	$lang_hremp_dateofbirth 				= "fecha de nacimiento";
	$lang_hremp_otherid 					= "otros datos de indentificación";
	$lang_hremp_maritalstatus 				= "Estado civil";
	$lang_hremp_selmarital					= "—seleccionar—";
	$lang_hremp_selectnatio 				= "seleccionar nacionalidad";
	$lang_hremp_selethnicrace 				= "seleccionar raza/etnia";
	$lang_hremp_smoker 						= "fumador";
	$lang_hremp_gender 						= "Sexo";
	$lang_hremp_dlicenno 					= "número de permiso de conducir";
	$lang_hremp_licexpdate 					= "Fecha de caducidad de permiso de conducir";
//Yes, driving license
	$lang_hremp_militaryservice 			= "servicio military";
	$lang_hremp_ethnicrace 					= "Raza/etnia";
	$lang_hremp_jobtitle 					= "Nombre del puesto";
	$lang_hremp_selempstat 					= "Seleccionar estatus de empleado";
//Employee
	$lang_hremp_eeocategory 				= "Categoría"   ;
//Translate only Category
	$lang_hremp_seleeocat 					= "Select EEO Cat";
//EEO – don’t translate “Seleccionar categoría de EEO”
	$lang_hremp_joindate 					= "fecha incorporación";
	$lang_hremp_SelectJobTitle				= "Seleccionar nombre de puesto";
	$lang_hremp_EmpStatus					= "Estatus de empleo";
	$lang_hremp_Workstation					= "lugar de trabajo";

 	$lang_hremp_dependents 					= "Personas dependientes";
	$lang_hremp_children 					= "hijos";
	$lang_hremp_relationship 				= "Parentesco" ;
//The employee gives the people details of those whom to contact in case of emergency. Relationship – how employee is related to these people (i.e. wife, friend, mother, etc.)
	$lang_hremp_street1						= "calle 1";
	$lang_hremp_street2 					= "calle 2";

	$lang_hremp_hmtele 						= "teléfono particular";
	$lang_hremp_mobile 						= "móvil";
	$lang_hremp_worktele 					= "teléfono de trabajo";
	$lang_hremp_city 						= "cuidad";
	$lang_hremp_workemail 					= "correo electrónico oficial";
	$lang_hremp_otheremail 					= "otro correo electrónico";
	$lang_hremp_passport 					= "pasaporte";
	$lang_hremp_visa 						= "Visado";
	$lang_hremp_citizenship 				= "cuidadanía";
	$lang_hremp_passvisano 					= "numero de pasaporte/visado";
	$lang_hremp_issueddate 					= "fecha de expedición";
	$lang_hremp_i9status 					= "I9 Estatus";
//Do not translate I9
	$lang_hremp_dateofexp 					= "fecha de caducidad";
	$lang_hremp_i9reviewdate 				= "I9 Review Date";
//Do not translate I9 ”Fecha de revisión de I9”

	$lang_hremp_path 						= "ruta";
	$lang_hremp_filename 					= "nombre de archivo";
	$lang_hremp_size 						= "tamaño";
	$lang_hremp_type 						= "tipo";
	$lang_hremp_name 						= "nombre";
	$lang_hremp_largefileignore				= "max 1M, todo anexo mayor será ignorado";
	$lang_lang_uploadfailed					= "Falló la carga de datos";

	$lang_hremp_browse						= "Navegar";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "Está seguro que desea borrar la fotografía";

	$lang_hremp_SelectAPhoto				= "seleccionar foto";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "la fecha de expedición debe ser anterior a la fecha de caducidad";
	$lang_hremp_FromDateShouldBeBeforeToDate= "Desde debe ser anterior a hasta";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "Dia de inicio debe ser anterior a dia de finalización";

	$lang_hremp_ContractExtensionStartDate	= "Dia de inicio de extension de contrato";
	$lang_hremp_ContractExtensionEndDate	= "dia de finalización de extension de contrato";
	$lang_hremp_EmployeeContracts 			= "contratos del empleado";
	$lang_hremp_AssignedContracts			= "Contratos asignados";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "El salario ha de estar entre el maximo y el mínimo";

	$lang_hremp_SelectCurrency				= "Seleccionar divisa";

	$lang_hremp_ContractExtensionId			= "identificador de la extension de contrato";
	$lang_hremp_ContractStartDate			= "fecha de inicio de contrato";
	$lang_hremp_ContractEndDate				= "fecha de vencimiento de contrato";
    $lang_hremp_FieldShouldBeNumeric        = "el campo debe ser numérico";

    $lang_hremp_Language					= "idioma";

 	$lang_hremplan_employeelanguageflu 		= "fluidez idiomática del empleado";
//Any word that could be used to explain how good is the knowledge of the language
	$lang_hremplan_fluency 					= "Fluidez" ;
//Yes, how fluent
	$lang_hremplan_ratinggarde 				= "fluidez";
	$lang_hremplan_assignlanguage 			= "idiomas asignados";

	$lang_hremplan_SelectLanguage			= "seleccionar idioma";
	$lang_hremplan_SelectFluency			= "seleccionar nivel de fluidez";
//Yes, use the more suitable phrase in Spanish (here, setting the fluency there are options like satisfactory, good, excellent, etc._
	$lang_hremplan_SelectRating 			= "seleccione valoración";

	$lang_hremp_PleaseSelectJobTitle= "Por favor seleccione un nombre de puesto para el empleado (aqui)";

	$lang_hremp_ie_CurrentSupervisors 		= "…Por ejemple:  Nombre del supervisor actual";
//Yes
	$lang_hremp_ie_CurrentSubordinates 		= "Por ejemplo . Actuales Subordinados del empleado";

 	$lang_hremplicenses_employeelicen 		= "licencias y permisos del empleado";
//It is licenses or maybe some certificates that employee possesses
	$lang_hremplicenses_assignlicenses 		= "Permisos asignados";
//The all possible licenses are defined for the whole company, while setting up the system. Then particular employee does not have all the licenses defined in the system, but only few of them. So we need to assign – select the appropriate licenses from the list for each employee. These will be assigned licenses
	$lang_hremplicenses_licentype 			= "tipo de licencia";
	$lang_hremplicenses_assignlicen 		= "permisos asignados";
//The same as above

	$lang_hremplicenses_NoLicenseSelected	= "no hay licencia seleccionada";
	$lang_hremplicenses_SelectLicenseType	= "seleccione tipo de licencia";

	$lang_hremp_SelectAtLEastOneAttachment = "seleccione por lo menos un anexo para borrar";

 	$lang_hrEmpMain_ratinggarde 			= "competencia";
	$lang_hrEmpMain_assignlanguage 			= "lenguajes asignados";
	$lang_hrEmpMain_Writing 				= "escrito";
	$lang_hrEmpMain_Speaking 				= "hablado";
	$lang_hrEmpMain_Reading 				= "leído";
	$lang_hrEmpMain_Poor 					= "bajo";
	$lang_hrEmpMain_Basic 					= "básico";
	$lang_hrEmpMain_Good 					= "alto";
	$lang_hrEmpMain_MotherTongue 			= "lengua materna";
	$lang_hrEmpMain_Individual 				= "individual";
	$lang_hrEmpMain_employeemembership 		= "Membresías del empleado";  // Yes. For example professional membership – employee is the member of Chartered Institute of Marketing
	$lang_hrEmpMain_subownership 			= "Titular de membresía";
//Is it company or individual, who owns the membership?
	$lang_hrEmpMain_subamount 				= "importe de la suscripción";
	$lang_hrEmpMain_subcomdate 				= "fecha de inicio de la suscripción";
	$lang_hrEmpMain_subredate 				= "fecha de renovación de la suscripción";
	$lang_hrEmpMain_selmemtype 				= "seleccione categoría de miembro";
	$lang_hrEmpMain_selmemship 				= "seleccione membresía";
//Here similar to the licenses – all the memberships are being defined for the organization in general. Then for each employee we select the membership type (i.e. – professional membership) and the particular membership (i.e. CIM member)
	$lang_hrEmpMain_selownership 			= "Seleccione titular de membresia"; // Yes, it can be company or individual. I.e. organization pays the membership fee and can assign 5 of it’s employees as the members of some society. Then the ownership = Company

	$lang_hrEmpMain_assignmemship 			= "membresías asignadas";
//Here similar to the licenses – all the memberships are being defined for the organization in general. Then for each employee we select the membership type (i.e. – professional membership) and the particular membership (i.e. CIM member)

	$lang_hrEmpMain_paygrade 				= "categoría salarial";
//Yes, for example we define, that all the managers will receive the salary between 1000 and 1500 Euro, this will be the pay grade

$lang_hrEmpMain_currency 				= "divisa";
	$lang_hrEmpMain_minpoint 				= "slario mínimo";
	$lang_hrEmpMain_maxpoint 				= "salario máximo";
	$lang_hrEmpMain_bassalary 				= "salario básico";
	$lang_hrEmpMain_assignedsalary 			= "salario asignado";

 	$lang_hrEmpMain_heading 				= "informe de empleado";
	$lang_hrEmpMain_supervisorsubordinator 	= "supervisior/subordinado";
	$lang_hrEmpMain_reportingmethod 		= "método de informe";
	$lang_hrEmpMain_subordinateinfomation 	= "subordinado";
	$lang_hrEmpMain_supervisorinfomation 	= "supervisor";
	$lang_hrEmpMain_selecttype 				= "seleccione método";
	$lang_hrEmpMain_Direct 					= "directo";
	$lang_hrEmpMain_Indirect 				= "indirecto";
	$lang_hrEmpMain_employeeskill 			= "capacitación del empleado";
	$lang_hrEmpMain_yearofex 				= "años de experiencia";
	$lang_hrEmpMain_assignskills 			= "capacitación asignada";
	$lang_hrEmpMain_employerworkex 			= "experiencia laboral del empleado";
	$lang_hrEmpMain_employer 				= "empleador";
	$lang_hrEmpMain_enddate 				= "fecha de vencimiento";
	$lang_hrEmpMain_startdate 				= "fecha de inicio";
	$lang_hrEmpMain_assignworkex 			= "Experiencia laboral asignada";
	$lang_hrEmpMain_workexid 				= "identificador de experiencia laboral";
//Yes, it is a number, used to identify the previous work experience the employee had. But it should not mean that if the number is 1, this was the first job that employee was working at.
	$lang_hrEmpMain_internal 				= "interno";
	$lang_hrEmpMain_major 					= "Especialización";
//It is the major subject in the university. I.e. Mary’s major is English Literature
	$lang_hrEmpMain_education 				= "Formación";
	$lang_hrEmpMain_gpa 					= "Puntuación de GPA";
//It is the average score, the one has collected during exams. Just leave it as GPA
	$lang_hrEmpMain_assigneducation 		= "formación asignada";
	$lang_hrEmpMain_assignattach 			= "Anexos asignados";
//Yes, documents, pictures, spreadsheets – some files that are relevant to the employee
	$lang_hrEmpMain_SelectEducation			= "seleccione formación";
	$lang_hrEmpMain_YearsOfExperiencCannotBeBlank = "años de experiencia no puede estar en blanco";
	$lang_hrEmpMain_YearsOfExperiencWrongFormat = "años de experiencia - formato erróneo para introducir";
	$lang_hrEmpMain_YearsOfExperiencBetween = "años de experiencia debe ser un valor entre #range";
	$lang_hrEmpMain_Skill 					= "capacitación";

	$lang_hrEmpMain_subown_Company = 'compañía';
	$lang_hrEmpMain_subown_Individual = 'individual';

	$lang_hrEmpMain_arrRepType_Supervisor = 'supervisor';
	$lang_hrEmpMain_arrRepType_Subordinate = 'Subordinado';

	$lang_hrEmpMain_arrRepMethod_Direct = 'directo';
	$lang_hrEmpMain_arrRepMethod_Indirect = 'indirecto';

	$lang_hrEmpMain_SelectMethod = 'seleccione método';

	$lang_hrEmpMain_SubscriptionAmountShouldBeNumeric = "el importe de subscripción debe ser numérico";
	$lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue = "falta el importe de la suscripción de membresía ¿desea continuar?";

	$lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate = "Fecha de inicio debe ser anterior a fecha de renovación";

	$lang_hrEmpMain_membershiptype = "Tipo de miembresía";
	$lang_hrEmpMain_membership = "miembresía";

 	$lang_jobtitle_heading 					= "Puesto: nombre de puesto";
	$lang_jobtitle_jobtitid 				= " Identiicador de nombre de puesto";
	$lang_jobtitle_jobtitname 				= "nombre del puesto";
//While defining possible job titles, the identificator (ID), the name of job title (actual job title) and the description are being specified. So you can just translate it as Job Title
	$lang_jobtitle_jobtitdesc 				= "descripción de puesto";
	$lang_jobtitle_jobtitcomments 			= "comentarios sobre el nombre de puesto";
	$lang_jobtitle_addpaygrade 				= "añadir categoría salarial";
	$lang_jobtitle_emstatExpl 				= "definir el estatus de empleo permitido para el puesto";
	$lang_jobtitle_editpaygrade 			= "editar categoría salarial";
	$lang_jobtitle_addempstat 				= "añadir estatus de empleo";
	$lang_jobtitle_editempstat 				= "editar estatus de empleo";
	$lang_jobtitle_empstat 					= "Employment Status";

 	$lang_languageinformation_heading 		= "aptitudes: idiomas";

 	$lang_licenses_heading 					= "cualificaciones: licencias";

 	$lang_locations_heading 				= "información de la compañía: localidades";

 	$lang_membershipinfo_heading 			= "miembro: miembro";
	$lang_membershipinfo_membershiptype 	= "tipo de miembro";
	$lang_membershipinfor_selectmember 		= "seleccione miembro";

 	$lang_membershiptypes_heading 			= "Miembro: tipo de miembro";

 	$lang_nationalityinformation_heading 	= "nacionalidad y raza: nacionalidad";

 	$lang_provinceinformation_heading 		= "información de Estado/provincia: información geográfica";

 	$lang_qualificationtypes_heading 		= "Tipos de cualificaciones: información sobre cualificaciones";
	$lang_repview_ReportID 					= "identificador de informe";
	$lang_repview_ReportName 				= "nombre de informe";
	$lang_repview_ViewEmployeeReports 		= "ver registros de empleado";
	$lang_repview_message 					= "borrar puede afectar a la jerarquia de la compañía";
	$lang_repview_DefineEmployeeReports 	= "definir informes de empleados";
	$lang_repview_message2 					= "Borrar puede afectar la visualización de informes";

 	$lang_routeinformation_heading 			= "Route Information : Route Information"; //No need to translate
	$lang_salarygrades_heading 				= "puesto: categoría salarial";
	$lang_salarygrades_currAss 				= "divisa asignada";
	$lang_salarygrades_stepSal 				= "escala de incremento";
	$lang_salcurrdet_heading 				= "asignación de divisa para categoría salarial";
	$lang_salcurrdet_assigncurr 			= "asignación de divisa";

 	$lang_satutoryinfo_heading 				= "Statutory Type : Nexus Information";
//No need to translate

 	$lang_view_EmploymentStatusID 			= "identificación de estatus de empleo";
	$lang_view_EmploymentStatusName 		= "nombre de estatus de empleo";
	$lang_view_message 						= "borrar puede afectar a las designaciones de puestos";
	$lang_view_message1 					= "Borrar puede afectar al grado salarial de empleados en el Módulo de Información de Personal MIP";
//PIM = Personal Information Module – the part of the program where all the employee related details are being entered
	$lang_view_SkillName 					= "nombre de aptitud";
	$lang_view_SkillID 						= "identificación de la aptitud";
	$lang_view_message3 					= "borrar puede afectar a la información del empleado";
	$lang_view_LocationName 				= "nombre de localización";
	$lang_view_message4 					= "Borrado puede afectar a la jerarquía de la compañía. Si la localización tiene vínculos el borrado puede fallar";
	$lang_view_CurrencyName 				= "nombre de la divisa";
	$lang_view_CurrencyTypes 				= "tipos de divisa";
	$lang_view_message5 					= "borrar puede affectar a lo detalles de la divisa del salario";
	$lang_view_CompanyHierarchy 			= "jerarquía de la compañía";
	$lang_view_CompanyHierarchyName 		= "nombre de la jerarquía de la compañía";
	$lang_view_message6 					= "borrar puede afectar a la información sobre el empleado";
	$lang_view_QualificationType 			= "tipo de cualificacion";
	$lang_view_QualificationName 			= "nombre de cualificación";
	$lang_view_message9 					= "borrar puede afectar a las cualificaciones, designación de cualificaciones, cualificaciones de empleado";
	$lang_view_RatingMethod 				= "método de valoración";
	$lang_view_RatingMethodName 			= "nombre de método de valoración";
	$lang_view_message10 					= "borrado puede afectar a cualificaciones, idiomas";
	$lang_view_CorporateTitle 				= "título corporativo";
	$lang_view_CorporateTitleName 			= "nombre de título corporativo";
	$lang_view_message11 					= "borrar puede afectar a la información de empleado, designaciones";
	$lang_view_ExtraCategory 				= "categoría de actividad extra curricular";
	$lang_view_ExtraCategoryName 			= "nombre de categoría de actividad extra curricular";
	$lang_view_message12 					= "Borrar puede afectar las actividades extra curriculares, actividades extra curriculares de empleado";
	$lang_view_MembershipTypeName 			= "Tipo de miembro";
	$lang_view_message13 					= "Borrar puede afectar a miembresias de empleado, membresías";
//The memberships are being defined for the whole organization (all the possible memberships that employees might have). Then particular memberships are being assigned to the particular employee. If we want to delete one membership from the list of memberships, and it was already assigned to some employee, it will be deleted from the Employee memberships too. So Employee membership will also get affected.
	$lang_view_EmployeeCategoryName 		= "nombre de categoría de empleado";
	$lang_view_message14 					= "borrar puede afectar a la información de empleado";
	$lang_view_EmployeeGroupName 			= "nombre de grupo del empleado";
	$lang_view_message15 					= "borrar puede afectar a la información de empleado";
	$lang_view_NationalityName 				= "nombre de nacionalidad";
	$lang_view_message16 					= "borrar puede afectar a la información de empleado";
	$lang_view_CountryID 					= "identificación de país";
	$lang_view_CountryName 					= "nombre de país";
	$lang_view_message17 					= "borrar puede afectar a la información de empleado";
	$lang_view_HierarchyDefinition 			= "Hierarchy Definition";
	$lang_view_HierarchyDefinitionName 		= "nombre de definición de jerarquía";
	$lang_view_message18 					= "borrar puede afectar a la jerarquía de la compañía";
	$lang_view_StateProvinceName 			= "Nombre de Estado/provincia";
	$lang_view_message19 					= "borrar puede afectar a la información de empleado";
	$lang_view_CityName 					= "nombre de cuidad";
	$lang_view_message20 					= "borrar puede afectar a la información de empleado";
	$lang_view_LanguagName 					= "nombre de idioma";
	$lang_view_message21 					= "borrar puede afectar a idiomas de empleado";
	$lang_view_Membership 					= "miembro";
	$lang_view_MembershipType 				= "tipos de miembro";
	$lang_view_MembershipName 				= "Membership Name";
	$lang_view_message22 					= "borrar puede afectar a membresía de empleado";
	$lang_view_ExtraActivities 				= "actividades extra curriculares";
	$lang_view_ExtraActivitiesName 			= "nombre de actividades extra curriculares";
	$lang_view_message23 					= "borrar puede afectar a actividades extra curriculares de empleado";
	$lang_view_PayGradeName 				= "nombre de nivel salarial";
	$lang_view_message24 					= "borrar puede afectar a la información de empleado, nombre de puestos";
	$lang_view_message25 					= "borrado puede afectar a formación";
	$lang_view_EmployeeTypeID 				= "identificador de tipo de empleado";
	$lang_view_EmployeeTypeName 			= "nombre de tipo de empleado";
	$lang_view_message26 					= "borrar puede afectar a la información de empleado";
	$lang_view_EEOJobCategory 				= "EEO Job Category";
//Do not translate EEO “Categoría de puesto”
	$lang_view_EEOJobCategoryid 			= "EEO Job Category Id";
//Do not translate EEO “identificador de categoría de puesto”
	$lang_view_EEOJobCategoryName 			= "EEO Job Category Name";
//Do not translate EEO “nombre de categoría de puesto”
	$lang_view_message27 					= "borrar puede afectar a la información de empleado";
	$lang_view_message28 					= "borrar puede afectar a idiomas del empleado";
	$lang_view_EthnicRace 					= "raza/etnia";
	$lang_view_EthnicRaceName 				= "nombre de raza/etnia";
	$lang_view_message29 					= "borrar puede afectar a los registros de empleado";
	$lang_view_message30 					= "borrar puede afectar a la información de empleado";
	$lang_view_message31 					= "borrar puede afectar a la información de empleado";
//Translate only deletion might affect employee information part
	$lang_view_message32 					= "borrar puede afectar a cualificaciones, designación de cualificaciones, cualificación de empleado";
	$lang_view_License_ID 					= "identificador de licencia"; // The different types of licenses that employees might possess are being saved into the system and given an identification number (ID) and description.
	$lang_view_LicenseDescription 			= "descripción de licencia";
	$lang_view_message33 					= "borrar puede afectar a la información de empleado";
	$lang_view_UserID 						= "identificador de usuario";
	$lang_view_UserName 					= "nombre de usuario";
	$lang_view_message34 					= "Atención borrar podría inutilizar OrangeHRM";
	$lang_view_UserGroupID 					= "Identificador de grupo de usuario";
	$lang_view_UserGroupName 				= "nombre de grupo de usuario";
	$lang_view_message35 					= "Atención borrar podria inutilizar OrangeHRM";

	$lang_view_Users 				= "usuarios";
	$lang_view_UserGroups 			= "adnimistrador de grupos de usuarios";
	$lang_view_HRAdmin 				= "Administrador de RH";
//HR – Human Resources
	$lang_view_ESS 					= "Autoservicio de Empleado";
//ESS – Employee Self Service
	$lang_view_ID 					= "identificador";
//ID - Identificator

	//Customer
	$lang_view_CustomerId = "identificador de cliente";
	$lang_view_CustomerName = "nombre de cliente";

	$lang_index_WelcomeMes 			= "Bienvenido #username";
	$lang_index_ChangePassword 		= "cambiar contraseña";
	$lang_index_Logout 				= "salir";

	$lang_MailFrom = "correo mandado como";
	$lang_MailSendingMethod = "Sending Method";
	$lang_MailTypes_Mail = "Función …interna";
//Do not translate PHP
	$lang_MailTypes_Sendmailer = "Enviar Correo";
	$lang_MailTypes_Smtp = "SMTP";
//Do not translate SMTP
	$lang_SendmailPath = "Path to Sendmail"; //“ruta a Sendmail”
//The path to the folder, where the sent mail goes i.e. C:\Windows\temp\files
	$lang_SmtpHost = "SMTP Host";
//Do not translate
	$lang_SmtpPort = "SMTP Port";
//Do not translate
	$lang_SmtpUser = "usuario";
//Do not translate SMPT
	$lang_SmtpPassword = "contraseña";
//Do not translate SMPT
	//Projects
	$lang_view_ProjectId = "identificador de proyecto";
	$lang_view_ProjectName = "nombre de proyecto";

	// PIM tab labels
	$lang_pim_tabs_Personal = "personal";
	$lang_pim_tabs_Contact = "contrato";
	$lang_pim_tabs_EmergencyContacts = "contratos de emergencia";
	$lang_pim_tabs_Dependents = "dependientes";
	$lang_pim_tabs_Immigration = "immigración";
	$lang_pim_tabs_Job = "puesto";
	$lang_pim_tabs_Payments = "pagos";
	$lang_pim_tabs_ReportTo = "informa a";
	$lang_pim_tabs_WorkExperience = "experiencia laboral";
	$lang_pim_tabs_Education = "formación";
	$lang_pim_tabs_Skills = "aptitudes";
	$lang_pim_tabs_Languages = "idiomas";
	$lang_pim_tabs_License = "Licencias y permisos" ; // “” It is license that allows employees to do something (i.e. driving license – employee can drive a vehicle)
	$lang_pim_tabs_Membership = "membresias";  //Like in the example above Institute of Marketing membership. It says about memberships in different associations, organizations, that the company employees might possess
	$lang_pim_tabs_Attachments = "anexos";

	// Report module
	$lang_rep_SelectionCriteria = "criterios de selección";
	$lang_rep_EmpNo = "número de empleado";
	$lang_rep_AgeGroup = "grupo de edad";
	$lang_rep_PayGrade = "categoría salrial";
	$lang_rep_Education = "formación";
	$lang_rep_EmploymentStatus = "estatus de empleo";
	$lang_rep_ServicePeriod = "periodo de servicio";
	$lang_rep_JobTitle = "nombre del puesto";

	$lang_rep_LastName = "Apellido";
	$lang_rep_FirstName = "Nombre";
	$lang_rep_Address = "dirección";
	$lang_rep_TelNo = "numero de teléfono";
	$lang_rep_DateOfBirth = "fecha de nacimiento";
	$lang_rep_JoinDate = "fecha de incorporación";
	$lang_rep_Qualification = "cualificacion";
	$lang_rep_EmployeeStates = "Estatutos de empleados";
//Sorry, it was our mistake. Shoul be Employee Statuses
	$lang_rep_JoinedDate = "se incorporó en fecha";

	$lang_rep_SelectPayGrade = "seleccione categoría salarial";
	$lang_rep_SelectEducation = "seleccione typo de formación";
	$lang_rep_SelectEmploymentType = "seleccione tipo de empleo";
	$lang_rep_SelectComparison = "seleccione comparación";
	$lang_rep_SelectJobTitle = "seleccione nombre de puesto";

	$lang_rep_Field = "campo";
	$lang_rep_AssignUserGroups = "Asignar grupos de usuarios";
	$lang_rep_AssignedUserGroups = "grupos de usuario asignados";

	$lang_rep_SelectAtLeastOneCriteriaAndOneField = "Seleccionar por lo menos un criterio y un campo";
	$lang_rep_SelectTheComparison = "seleccione la comparación";

	$lang_rep_AgeShouldBeNumeric = "la edad ha de ser numérica";
	$lang_rep_InvalidAgeRange = "la Segunda edad seleccionada es mayor que la primera edad seleccionada";

	$lang_rep_FieldNotSelected = "campo no seleccionado";

	$lang_rep_DateShouldBeNumeric = "los datos deben ser numéricos";

	$lang_rep_Report = "informe";
	$lang_rep_EmployeeNo = "número de empleado";
	$lang_rep_EmployeeFirstName = "nombre del empleado";
	$lang_rep_EmployeeLastName = "apellido del empleado";
	$lang_rep_ReportTo = "reporta a";
	$lang_rep_ReportingMethod = "método de informe";
	$lang_rep_Address = "dirección";
	$lang_rep_Telephone = "teléfono";
	$lang_rep_DateOfBirth = "fecha de nacimiento";
	$lang_rep_Skills = "aptitudes";
	$lang_rep_SalaryGrade = "categoría salarial";
	$lang_rep_EmployeeStatus = "estatus de empleado";
	$lang_rep_JoinedDate = "fecha de incorporación";
	$lang_rep_SubDivision = "subdivisión";
	$lang_rep_JobTitle = "nombre del puesto";
	$lang_rep_YearOfPassing = "Año de graduación";
//It is year of completion of final exams
	$lang_rep_Contract = "contrato";
	$lang_rep_WorkExperience = "experiencia profesional";

	$lang_rep_SelectAtLeaseOneUserGroupToAssign = "seleccione al menos una casilla de grupo de usuario para asignar";

	$lang_rep_ReportDefinition = "definición de informe";
	$lang_rep_AssignUserGroups = "asignar grupos de usuario";
	$lang_rep_UserGroups = "grupos de usuario";
	$lang_rep_NoUserGroupsAssigned = "no hay grupos de usuario asignados";

	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "configuración de correo";
	$lang_Admin_SubscribeToMailNotifications = "suscribirse a notificaciones de correo electrónico";
	$lang_Admin_ENS_LeaveApplications = "solicitud de permiso";
	$lang_Admin_ENS_LeaveApprovals = "aprobación de permiso";
	$lang_Admin_ENS_LeaveCancellations = "cancelación de permiso";
	$lang_Admin_ENS_LeaveRejections = "denegación de permiso";

	//Users
	$lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong = "el nombre de usuario ha de contener al menos cinco caracteres";
	$lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong = "la contraseña ha de contener al menos cinco caracteres";

	$lang_Admin_Users_ErrorsPasswordMismatch = "la contraseña no coincide";
	$lang_Admin_Users_Errors_EmployeeIdShouldBeDefined = "la identificación de usuario debe ser definida";
	$lang_Admin_Users_Errors_FieldShouldBeSelected = "un campo debe ser selecionado";
	$lang_Admin_Users_Errors_ViewShouldBeSelected = "opción ver debe ser seleccionada";
	$lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "La contraseña no coincide. Reescriba su nueva contraseña";

	$lang_Admin_Users_Errors_DoYouWantToClearRights = "¿Quiere despejar Derechos?";
	$lang_Admin_Users_Errors_SameGroup = "su cuenta de registro de usuario pertenece al mismo grupo de usuario, no le esta permitido cambiar derechos en este grupo";
	$lang_Admin_Users_Errors_NameCannotBeBlank = "Falta nombre";

	$lang_Admin_Users_Modules = "módulos";
	$lang_Admin_Users_Module = "módulo";
	$lang_Admin_Users_RightsAssignedToUserGroups = "DErechos asignados a grupos de usuarios";
	$lang_Admin_Users_UserGroup = "grupo de usuraios";
	$lang_Admin_Users_UserGroupId = "identificador de grupo de usuarios";

	$lang_Admin_Users_UserName = "nombre de usuario";
	$lang_Admin_Users_Password = "contraseña";

	$lang_Admin_Users_ConfirmPassword = "confirmar contraseña";

	$lang_Admin_Users_Status = "estatus";
	$lang_Admin_Users_Enabled = "activado";
	$lang_Admin_Users_Disabled = "desactivado";

	$lang_Admin_Users_UserGroup = "administrador de grupo de usuarios";
	$lang_Admin_Users_SelectUserGroup = "seleccione grupo de usuarios";

	$lang_Admin_Users_NewPassword = "nueva contraseña";
	$lang_Admin_Users_ConfirmNewPassword = "confirmar nueva contraseña";

	$lang_Admin_Users_add = "añadir";
	$lang_Admin_Users_delete = "borrar";
	$lang_Admin_Users_view = "ver";
	$lang_Admin_Users_edit = "editar";

	$lang_Admin_Users_AssignedRights = "asignar derechos";
	$lang_Admin_Users_DefineReports = "definir informes";

	$lang_Admin_Change_Password_OldPassword = "Antigua contraseña";

	$lang_Admin_Change_Password_Errors_EnterYourOldPassword = "introduzca su Antigua contraseña";
	$lang_Admin_Change_Password_Errors_EnterYourNewPassword = "introduzca su nueva contraseña";
	$lang_Admin_Change_Password_Errors_RetypeYourNewPassword = "teclee de nuevo su nueva contraseña";
	$lang_Admin_Change_Password_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "contraseñas no coinciden. Teclee de Nuevo su Nueva contraseña";
	$lang_Admin_Change_Password_Errors_YourOldNewPasswordsAreEqual = "sus contraseñas nueva y Antigua son identicas";

	$lang_Admin_Project_CutomerName = "nombre del cliente";
	$lang_Admin_Project_SelectCutomer = "seleccione cliente";

	$lang_Admin_Project_Error_PleaseDSpecifyTheProjectId = "por favor especifique el identificador del proyecto";
	$lang_Admin_Project_Error_PleaseSelectACustomer = "por favor seleccione un cliente";
	$lang_Admin_Project_Error_PleaseSpecifyTheName = "por favor especifique el nombre";

	$lang_Admin_Customer_PleaseSpecifyTheCustormerId = "por favor especifique la identificación del cliente";
	$lang_Admin_Customer_Error_PleaseSpecifyTheName = "por favor especifique el nombre";

	//timesheet
	$lang_Time_Timesheet_TimesheetForViewTitle = "hoja de servicio de #periodName desde #startDate"; //#periodname  #startdate
//Do not translate the words with # prefics
	$lang_Time_Timesheet_TimesheetForEditTitle = "editar hoja de servicio  #periodName desde #startDate";  //“ #periodoname  #startdate
//Do not translate the words with # prefics

	$lang_Time_Timesheet_TimesheetNameForViewTitle = "hoja de servicio de #name para #periodName desde #startDate"; // #name  #periodname  #startdate
//Do not translate the words with # prefics

	$lang_Time_Timesheet_TimesheetNameForEditTitle = "editar hoja de servicio de #name para #periodName desde #startDate";  //“ #name  #periodname  #startdate

//Do not translate the words with # prefics

	$lang_Time_Timesheet_Status = "estatus: #status";// :#estatus

	$lang_Time_Timesheet_Status_NotSubmitted = "no presentado";
	$lang_Time_Timesheet_Status_Submitted = "presentado";
	$lang_Time_Timesheet_Status_Approved = "aprobado";
	$lang_Time_Timesheet_Status_Rejected = "denegado";

	$lang_Time_Timesheet_Customer = "cliente";
	$lang_Time_Timesheet_ProjectActivity = "proyecto / actividad";

	$lang_Time_Timesheet_Total = "total";

	$lang_Time_Errors_SUBMIT_SUCCESS = "presentación satisfactoria";
	$lang_Time_Errors_SUBMIT_FAILURE = "presentación fallida";
	$lang_Time_Errors_UPDATE_SUCCESS = "actualización satisfactoria";
	$lang_Time_Errors_UPDATE_FAILURE = "actualización fallida";
	$lang_Time_Errors_CANCEL_SUCCESS = "anulación satisfactoria";
	$lang_Time_Errors_CANCEL_FAILURE = "anulación fallida";
	$lang_Time_Errors_APPROVE_SUCCESS = "aprobación satisfactoria";
	$lang_Time_Errors_APPROVE_FAILURE = "aprobación fallida";
	$lang_Time_Errors_REJECT_SUCCESS = "denegación satisafactoria";
	$lang_Time_Errors_REJECT_FAILURE = "denegación fallida";
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "actuación no autorizada";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "no se encontró hoja de servicio";

	$lang_Time_Errors_EncounteredTheFollowingProblems = "Encountered the following problems";
	$lang_Time_Errors_ReportedDateNotSpecified_ERROR = "fecha de informe no especificada";
	$lang_Time_Errors_ProjectNotSpecified_ERROR = "proyecto no especificado";
	$lang_Time_Errors_CustomerNotSpecified_ERROR = "cliente no especificado";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified_ERROR = "tiempo inválido o zero o intervalo negativo seleccionado";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval_ERROR = "no autorizado a especificar duración e intervalo a menos que coincidan";

	$lang_Time_Timesheet_StartTime = "tiempo de inicio";
	$lang_Time_Timesheet_EndTime = "tiempo de finalización";
	$lang_Time_Timesheet_ReportedDate = "fecha de informe";
	$lang_Time_Timesheet_Duration = "duración";
	$lang_Time_Timesheet_Decription = "descripción";
	$lang_Time_Timesheet_NoCustomers = "no hay clientes";
	$lang_Time_Timesheet_NoProjects = "no hay proyectos";

	$lang_Time_Select_Employee_Title = "seleccione empleado";

	$lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval = "hojas de servicio presentadas pendientes de probación del superior";
	$lang_Time_Select_Employee_WeekStartingDate = "comienzo de la semana #date";

	$lang_Time_Select_Employee_TimesheetPeriod = "periodo de hoja de servicio";

	$lang_Time_Errors_PleaseAddAComment = "por favor añada comentario";

?>
