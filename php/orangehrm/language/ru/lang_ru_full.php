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
 * @author: Darija Jevtuchova <darija@orangehrm.com>
 */

	/**
	 * Error messages
	 *
	 */

	$lang_Error_PleaseCorrectTheFollowing = "Пожайлуста, исправьте";
	$lang_Error_SelectAtLeastOneRecordToDelete = "Выберите хотя бы одну запись для удаления";
	$lang_Error_DoYouWantToDelete = "Удалить?";
	$lang_Error_EnterDate = "Введите дату";

	$lang_Error_PleaseSelectAYear = "Выберите год";
	$lang_Error_PleaseSelectAnEmployee = "Выберите служащего";

	$lang_Error_DoYouWantToContinue = " Продолжить?";

	$lang_Error_ShouldBeNumeric = "Разрешены только цифры";
	$lang_Error_FollowingErrorsWereFound = "Были обнаружены следущие ошибки";
	$lang_Error_AreYouSureYouWantToDelete = "Вы уверены, что xотите стереть?";

	//leave
	$lang_Error_PleaseSelectAValidFromDate = "Выберите действующую дату начала";
	$lang_Error_PleaseSelectALeaveType = "Выберите тип отпуска";

	$lang_Error_LeaveDateCannotBeABlankValue = "Введите дату отпуска!";
	$lang_Error_NameOfHolidayCannotBeBlank = "Введите название праздника";

	$lang_Error_NoLeaveTypes = "Нет типов отпуска";
	$lang_Error_NoRecordsFound = "Записи не найдены!";

	$lang_Error_InvalidDate = "Недействительная дата";

	$lang_hremp_FieldShouldBeNumeric = "Разрешены только цифры";

	// Company Structure
	$lang_Error_Company_General_Undefined = "Пожалуйста, введите основную информацию о компании";
	$lang_Error_CompStruct_UnitCount = "#children подразделения #parent будут стёрты.";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "Это может вызвать изменения структуры компании";

	$lang_Error_SubDivisionNameCannotBeEmpty = "Введите название подразделения";
	$lang_Error_PleaseSelectATypeOrDefineACustomType = "Пожалуйста, выберите тип из существующих или введите новый тип";
	$lang_Error_CompStruct_LocEmpty = "Пожалуйста, выберите местонахождение из существующих или введите новое местонахождение";

	$lang_Error_LocationNameEmpty = "Введите название местонахождения";
	$lang_Error_CountryNotSelected = "Выберите страну";
	$lang_Error_StateNotSelected = "Введите округ";
	$lang_Error_CityCannotBeEmpty = "Введите город";
	$lang_Error_AddressEmpty = "Введите адрес";
	$lang_Error_ZipEmpty = "Введите почтовый индекс";
	$lang_Error_CompStruct_ZipInvalid = "Символы #characterList в почтовом индексе";

	$lang_Error_InvalidEmail  = "Недействительный адрес эл. почты";

	//PIM
	$lang_Error_LastNameNumbers				= "Фамилия содержит цифровую информацию. Продолжить?";
	$lang_Error_FirstNameNumbers			= "Имя содержит цифровую информацию. Продолжить?";
	$lang_Error_MiddleNameNumbers			= "Отчество содержит цифровую информацию. Продолжить?";
	$lang_Error_MiddleNameEmpty				= "Отчествоне не введено. Продолжить?";
	$lang_Error_LastNameEmpty				= "Фамилия не введена!";
	$lang_Error_FirstNameEmpty				= "Имя не введено!";
	$lang_Error_ChangePane					= "Пожайлуста, соxраните информацию перед тем как выбрать следующую панель!";

	$lang_Error_UploadFailed = "Загрузка не удалась!";

	$lang_Error_FieldShouldBeSelected		= "Выберите значение";

	$lang_Error_FieldShouldBeNumeric		= "Разрешены только цифры";

	$lang_Error_SelectAtLeastOneCheckBox 	= "Обозначьте xотя бы одну галочку";

	/**
	 * Menu Items
	 *
	 */

	// Home
	$lang_Menu_Home = "Главная";

	$lang_Menu_Home_Support = "Поддержка";
	$lang_Menu_Home_Forum = "Форум";
	$lang_Menu_Home_Blog = "Блог";

	// Admin Module
	$lang_Menu_Admin = "Администратор";

	$lang_Menu_Admin_CompanyInfo = "О компании";
	$lang_Menu_Admin_CompanyInfo_Gen = "Общая информация";
	$lang_Menu_Admin_CompanyInfo_CompStruct = "Структура компании";
	$lang_Menu_Admin_CompanyInfo_Locations = "Местонахождение";

	$lang_Menu_Admin_Job = "Служба";
	$lang_Menu_Admin_Job_JobTitles = "Название должности";
	$lang_Menu_Admin_Job_PayGrades = "Уровень оплаты";
	$lang_Menu_Admin_Job_EmpStatus = "Рабочий статус";
	$lang_Menu_Admin_Job_EEO = "ЕЕО Категория";

	$lang_Menu_Admin_Quali = "Квалификации";
	$lang_Menu_Admin_Quali_Education = "Образование";
	$lang_Menu_Admin_Quali_Licenses = "Лицензии";

	$lang_Menu_Admin_Skills = "Навыки";
	$lang_Menu_Admin_Skills_Skills = "Служебные навыки";
	$lang_Menu_Admin_Skills_Languages = "Языки";

	$lang_Menu_Admin_Memberships = "Членство";
	$lang_Menu_Admin_Memberships_Memberships = "Членство";
	$lang_Menu_Admin_Memberships_MembershipTypes = "Тип членства";

	$lang_Menu_Admin_NationalityNRace = "Национал. и раса";
	$lang_Menu_Admin_NationalityNRace_Nationality = "Национальность";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces = "этнические расы";

	$lang_Menu_Admin_Users = "Пользователи";
	$lang_Menu_Admin_Users_UserGroups = "Группы пользователей";
	$lang_Menu_Admin_Users_HRAdmin = "Менеджер отдела кадров";
	$lang_Menu_Admin_Users_ESS = "Пользователи самообслуживания";

	$lang_Menu_Admin_EmailNotifications  = "Извещения";
	$lang_Menu_Admin_EmailConfiguration  = "Конфигурация";
	$lang_Menu_Admin_EmailSubscribe  = "Подписаться";

	// admin module
	$lang_Admin_Common_Institute = "Институт";
	$lang_Admin_Common_Course = "Специальность";

	// Leave Module
	$lang_Menu_Leave = "Отпуск";
	$lang_Menu_Leave_PersonalLeaveSummary = "Сводка личных отпусков";
	$lang_Menu_Leave_EmployeeLeaveSummary = "Сводка отпусков служащего";
	$lang_Menu_Leave_LeaveSummary = "Сводка отпусков";
	$lang_Menu_Leave_LeavesList = "Перечень отпусков";
	$lang_Menu_Leave_ApproveLeave = "Подтвердить отпуск";
	$lang_Menu_Leave_LeaveTypes = "Типы отпуска";
	$lang_Menu_Leave_Apply = "Обращение";
	$lang_Menu_Leave_Assign	= "Назначить";
	$lang_Menu_Leave_LeaveList = "Перечень отпусков";
	$lang_Menu_Leave_DefineDaysOff = "Нерабочие дни";
	$lang_Menu_Leave_DefineDaysOff_Weekends = "Выxодные";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays = "Праздники";

	$lang_Leave_Title_Apply_Leave = "Обратиться за отпуском";
	$lang_Leave_Title_Assign_Leave = "Назначить отпуск";

	// Report
	$lang_Menu_Reports = "Отчёты";
	$lang_Menu_Reports_ViewReports = "Создать отчёт";
	$lang_Menu_Reports_DefineReports = "Просмотр отчётов";

	/**
	 * Common
	 */

	$lang_Common_ConfirmDelete = "Вы уверены, что xотите стереть?";
	$lang_Common_FieldEmpty = "Значение поля не введено";
	$lang_Common_SelectDelete = "Выберите xотя бы одну запись, которую вы xотите стереть";
	$lang_Common_SelectField = "Выберите критерий поиска!";
	$lang_Commn_RequiredFieldMark = "Обязательно заполнить поля, обозначенные звёздочкой #star";
	$lang_Commn_code = "Код";
	$lang_Commn_description = "Описание";
	$lang_Commn_title = "Название";
	$lang_Commn_name = "Название";
	$lang_Commn_PleaseWait = "Подождите, пожайлуста";

	$lang_Commn_Email = "Эл. почта";
	$lang_Common_Edit = "Редактировать";
	$lang_Common_New = "Новый";

	//days
	$lang_Common_Monday = "Понедельник";
	$lang_Common_Tuesday = "Вторник";
	$lang_Common_Wednesday = "Среда";
	$lang_Common_Thursday = "Четверг";
	$lang_Common_Friday = "Пятница";
	$lang_Common_Saturday = "Суббота";
	$lang_Common_Sunday = "Воскресенье";

	$lang_Leave_Common_Weekend = "Выxодной";

	//leave module
	$lang_Leave_Common_Date = "Дата";
	$lang_Leave_Common_FromDate = "От";
	$lang_Leave_Common_ToDate = "До";
	$lang_Leave_Common_LeaveType = "Тип отпуска";
	$lang_Leave_Common_Status = "Статус";
	$lang_Leave_Common_Length = "Целый день / пол дня";
	$lang_Leave_Common_Range = "Интервал";
	$lang_Leave_Common_Comments = "Коментарии";
	$lang_Leave_Common_Comment = "Комментарий";
	$lang_Leave_Common_Approved = "Подтвержденный";
	$lang_Leave_Common_Cancelled = "Анулированный";
	$lang_Leave_Common_PendingApproval = "Ожидающий подтверждения";
	$lang_Leave_Common_Rejected = "Отказанный";
	$lang_Leave_Common_Taken = "Использованный";
	$lang_Leave_Common_InvalidStatus = "Недействительный статус";
	$lang_Leave_Common_FullDay = "Целый день";
	$lang_Leave_Common_HalfDayMorning = "Пол дня / Утро";
	$lang_Leave_Common_HalfDayAfternoon = "Пол дня / После полудня";
	$lang_Leave_Common_HalfDay = "Пол дня";
	$lang_Leave_Common_LeaveTaken = "Отпуск использован";
	$lang_Leave_Common_LeaveAvailable = "Предостовляемый отпуск";
	$lang_Leave_Common_LeaveTypeName = "Название типа отпуска";
	$lang_Leave_Common_LeaveTypeId = "Код типа отпуска";
	$lang_Leave_Common_Select = "Выберите";
	$lang_Leave_Common_oldLeaveTypeName = "Название существущего типа отпуска";
	$lang_Leave_Common_newLeaveTypeName = "Название нового типа отпуска";
	$lang_Leave_Common_EmployeeName = "Имя служащего";
	$lang_Leave_Common_LeaveEntitled = "Дано право на отпуск";
	$lang_Leave_Common_Year = "Год";
	$lang_Leave_Common_ListOfTakenLeave = "Перечень использованных отпусков";
	$lang_Leave_Common_Remove = "Удалить";
	$lang_Leave_Common_AllEmployees	= "Все служащие";
	$lang_Leave_Common_All = "Все";

	$lang_Leave_Common_NameOfHoliday = "Название праздника";
	$lang_Leave_Common_Recurring = "Повторяющийся";

	$lang_bankInformation_code = "Код";
	$lang_bankInformation_description = "Описание";

	$lang_compstruct_add = "Добавить" ;
	$lang_compstruct_delete = "Удалить";
	$lang_compstruct_clear = "Стереть";
	$lang_compstruct_hide = "Скрыть";
	$lang_compstruct_save = "Сохранить";

	$lang_Leave_Leave_list_Title1 = "Подтвердить отпуск";
	$lang_Leave_Leave_Requestlist_Title1 = "Поддвердить просьбу отпуска от #employeeName";
	$lang_Leave_Leave_list_Title2 = "Отпуск использованный #employeeName в #dispYear";
	$lang_Leave_Leave_list_Title3 = "Перечень отпусков";
	$lang_Leave_Select_Employee_Title = "Выберите служащего";
	$lang_Leave_Leave_Summary_Title = "Сводка отпусков";
	$lang_Leave_Leave_Summary_EMP_Title = "Сводка отпусков за";
	$lang_Leave_Select_Employee_Title = "Выберите служащего";
	$lang_Leave_Leave_Summary_EMP_Title = "Сводка отпусков за #dispYear";
	$lang_Leave_Leave_Summary_SUP_Title = "Сводка отпусков #employeeName за #dispYear";
	$lang_Leave_Define_leave_Type_Title = "Определить тип отпуска";
	$lang_Leave_Leave_Type_Summary_Title = "Типы отпуска";
	$lang_Leave_Leave_Holiday_Specific_Title = "Нерабочие дни: Праздники";
	$lang_Leave_Leave_Holiday_Weeked_Title = "Нерабочие дни: Выxодные";

	$lang_comphire_heading = "Иерархия компаний: Информация о компании";
	$lang_comphire_relationalhierarchy = "Относительная иерархия" ;
	$lang_comphire_employee = "Служащий";
	$lang_comphire_definitionlevel = "Уровень определения" ;
	$lang_comphire_telephone = "Телефон";
	$lang_comphire_fax = "Факс";
	$lang_comphire_email = "Эл. почта";
	$lang_comphire_url = "URL";
	$lang_comphire_logo = "Лого";
	$lang_comphire_selecthie = "Выберите иерархию";
	$lang_comphire_selectdef = "Выберите уровень опред.";
	$lang_compstruct_heading = "Информация о компании : Структура компании";
	$lang_compstruct_frmSub_divisionHeadingAdd = "Добавить подразделение к";
	$lang_compstruct_frmSub_divisionHeadingEdit = "Редактировать";
	$lang_compstruct_Name = "Название";
	$lang_compstruct_Type = "Вид";
	$lang_compstruct_Division = "Отдел";
	$lang_compstruct_Description = "Описание";
	$lang_compstruct_Address = "Адрес";
	$lang_compstruct_Department = "Департамент" ;
	$lang_compstruct_Team = "Команда / бригада";
	$lang_compstruct_Other = "Другой";
	$lang_compstruct_Location = "Местонахождение ";
	$lang_compstruct_frmNewLocation = "Укажите новое местонахождение";
	$lang_compstruct_country = "Страна";
	$lang_compstruct_state = "Округ / область";
	$lang_compstruct_city = "Город";
	$lang_compstruct_ZIP_Code = "Почтовый индекс";
	$lang_compstruct_Phone = "Телефон";
	$lang_compstruct_no_root = "Основа не найдена! Пожайлуста, укажите основу";

	$lang_corptit_heading = "Корпоративные звания : Информация о работе";
	$lang_corptit_topinhierachy = "Верx иерархии";
	$lang_corptit_multipleheads = "Множественное руководство";
	$lang_corptit_headcount = "Число руководителей";
	$lang_corptit_nextlevelupgrade = "Следующее повышение";
	$lang_corptit_selectcor = "Выберите корпоратив. звание";
	$lang_corptit_salarygrade = "Уровень зарплаты";
	$lang_corptit_selectsal = "Выберите уровень зарплаты";

	$lang_costcenters_heading = "Центр стоимости : Информация о компании";

	$lang_countryinformation_heading = "Информация о стране: Гео информация";

	$lang_currencytypes_heading = "Вид валюты : Информация о работе";

	$lang_districtinformation_heading = "Информация о городе : Гео информация";
	$lang_districtinformation_selectcounlist = "Выберите страну";
	$lang_districtinformation_selstatelist = "Выберите округ";

	$lang_eeojobcat_heading = "Служба : ЕЕО Категория";
	$lang_eeojobcat_description = "Название";

	$lang_electorateinformation_heading = "Информация избирательного округа : Гео Информация";
	$lang_emprepinfo_heading = "Определите отчёты по служащим";

	$lang_emptypes_heading = "Виды служащих : Информация о звене" ;
	$lang_emptypes_datelimited = "Дата ограниченна";
	$lang_emptypes_prefix = "Префикс" ;

	$lang_empview_heading = "Служба : Служебный статус";
	$lang_empview_EmpID = "Код служащего";
	$lang_empview_EmpFirstName = "Имя Служ.";
	$lang_empview_EmpLastName = "Фамилия Служ.";
	$lang_empview_EmpMiddleName = "Отчество служ.";
	$lang_empview_search = "Поиск";
	$lang_empview_searchby = "Поиск по:";
	$lang_empview_description = "Искать:";
	$lang_empview_norecorddisplay = "Записей нет";
	$lang_empview_previous = "Предыдущий";
	$lang_empview_next = "Следующий"	;
	$lang_empview_employeeid = "Код служащего";
	$lang_empview_employeename = "Имя Служащего"	;
	$lang_empview_ADD_SUCCESS = "Добавлено";
	$lang_empview_UPDATE_SUCCESS = "Изменено";
	$lang_empview_DELETE_SUCCESS = "Удалено";
	$lang_empview_ADD_FAILURE = "Добавление не выполнено";
	$lang_empview_UPDATE_FAILURE = "Модификация не выполнена";
	$lang_empview_DELETE_FAILURE = "Удаление не выполнено";
	$lang_empview_Language = "Язык";
	$lang_empview_WorkExperience = "Опыт работы";
	$lang_empview_Payment = "Оплата";
	$lang_empview_Skills = "Навыки";
	$lang_empview_Licenses = "Лицензии";
	$lang_empview_EmployeeInformation = "Информация о служащем";
	$lang_empview_Memberships = "Членство";
	$lang_empview_Report = "Отчёт";
	$lang_empview_SubDivision = "Подразделение";
	$lang_empview_JobTitle = "Должность";
	$lang_empview_Supervisor = "Руководитель";

	$lang_ethnicrace_heading = "Национальность и раса : Этнические расы";

	$lang_extracurractcat_heading = "Вид дополнителной деятельности : Информация о квалификациях";

	$lang_extracurractinfo_heading = "Информация о дополнителной деятельности : Информация о квалификациях";
	$lang_extracurractinfo_extracuaccat = "Вид дополнителной деятельности";
	$lang_extracurractinfo_selectsecucat = "Выберите вид дополн. деят.";

	$lang_geninfo_heading = "Инфомация о компании : Общая информация";
	$lang_geninfo_compname = "Название компании";
	$lang_geninfo_taxID = "Налоговый идентификатор" ;
	$lang_geninfo_naics = "NAICS";
	$lang_geninfo_err_CompanyName = "Ввод названия компании обязателен"	;
	$lang_geninfo_err_Phone = "Не действительный номер телефона / факса";

	$lang_hierarchydef_heading = "Информация иерархии : Информация о компании";

	$lang_hremp_EmpFirstName = "Имя";
	$lang_hremp_EmpLastName = "Фамилия";
	$lang_hremp_EmpMiddleName = "Отчество";
	$lang_hremp_nickname = "Псевдоним";
	$lang_hremp_photo = "Фото";
	$lang_hremp_ssnno = "SSN ";
	$lang_hremp_nationality = "Национальность";
	$lang_hremp_sinno = "Номер соц. страхования";
	$lang_hremp_dateofbirth = "Дата рождения";
	$lang_hremp_otherid = "Другой идентификатор";
	$lang_hremp_selmarital	= "--Выберите--";
	$lang_hremp_maritalstatus = "Семейное положение";
	$lang_hremp_selectnatio = "Выберите национальность";
	$lang_hremp_selethnicrace = "Выберите этническую рассу";
	$lang_hremp_smoker = "Курящий(-ая)";
	$lang_hremp_gender = "Пол";
	$lang_hremp_dlicenno = "Номер водительских прав";
	$lang_hremp_licexpdate = "Дата окончания срока действия прав";
	$lang_hremp_militaryservice = "Служба в армии";
	$lang_hremp_ethnicrace = "Этническая раса" ;
	$lang_hremp_jobtitle = "Должность";
	$lang_hremp_selempstat = "Выберите статус служ.";
	$lang_hremp_eeocategory = "ЕЕО категори ";
	$lang_hremp_seleeocat = "Выберите EEO катег.";
	$lang_hremp_joindate = "Дата начала работы";
	$lang_hremp_SelectJobTitle = "Выберите должность";
	$lang_hremp_EmpStatus = "Служебный статус";
	$lang_hremp_Workstation = "Рабочее место";

	$lang_hremp_dependents = "Иждивенцы";
	$lang_hremp_children = "Дети";
	$lang_hremp_relationship = "Степень родства";

	$lang_hremp_street1	= "Адрес 1";
	$lang_hremp_street2 = "Адрес 2";

	$lang_hremp_hmtele = "Домашний телефон";
	$lang_hremp_mobile = "Мобильный";
	$lang_hremp_worktele = "Рабочий телефон";
	$lang_hremp_city = "Город";
	$lang_hremp_workemail = "Рабочая эл. почта";
	$lang_hremp_otheremail = "Другая эл. почта";
	$lang_hremp_passport = "Паспорт" ;
	$lang_hremp_visa = "Виза";
	$lang_hremp_citizenship = "Гражданство";
	$lang_hremp_passvisano = "Номер паспорта / визы";
	$lang_hremp_issueddate = "Дата выдачи";
	$lang_hremp_i9status = "I9 Статус" ;
	$lang_hremp_dateofexp = "Дата окончания";
	$lang_hremp_i9reviewdate = "Дата просмотра I9" ;
	$lang_hremp_path = "Путь";
	$lang_hremp_filename = "Название файла";
	$lang_hremp_size = "Размер";
	$lang_hremp_type = "Тип";
	$lang_hremp_name = "Название";
	$lang_hremp_largefileignore	= "Максимум 1MB. Более крупные приложения не будут загруженны";
	$lang_lang_uploadfailed	= "Загрузка файла не удалась";


	$lang_hremp_browse = "Выбрать";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "Вы уверены, что xотите стереть фотографию?";

	$lang_hremp_SelectAPhoto = "Выберите фотографию";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "Дата выдачи должна быть раньше чем окончание срока действия";
	$lang_hremp_FromDateShouldBeBeforeToDate= "Дата От должна быть раньче чем дата До";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "Дата начала должна быть перед датой конца";

	$lang_hremp_ContractExtensionStartDate = "Дата начала продления контракта";
	$lang_hremp_ContractExtensionEndDate = "Дата конца продления контракта";
	$lang_hremp_EmployeeContracts = "Контракты служащего";
	$lang_hremp_AssignedContracts = "Присвоенные контракты";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "Зарплата должна быть между минимальным и максимальным значением";

	$lang_hremp_ContractExtensionId	= "Иденитфикатор продления контракта";
	$lang_hremp_ContractStartDate = "Начало контракта";
	$lang_hremp_ContractEndDate	= "Конец контракта";

	$lang_hremp_SelectCurrency = "Выберите валюту";

	$lang_hremp_Language = "Язык";

	$lang_hremplan_employeelanguageflu = "Языковые навыки служащего";
	$lang_hremplan_fluency = "Критерий";
	$lang_hremplan_ratinggarde = "Уровень знаний";
	$lang_hremplan_assignlanguage = "Владение языками";

	$lang_hremplan_SelectLanguage			= "Выберите язык";
	$lang_hremplan_SelectFluency			= "Выберите уровень знаний";
	$lang_hremplan_SelectRating 			= "Выберите";

	$lang_hremp_PleaseSelectJobTitle= "Пожайлуста, выберите должность этого служащего {здесь}";

	$lang_hremp_ie_CurrentSupervisors = "Например, руководители данного служащего";
	$lang_hremp_ie_CurrentSubordinates = "Например, подчинённые данного служащего";

	$lang_hremplicenses_employeelicen = "Лицензии служащего";
	$lang_hremplicenses_assignlicenses = "Полученные лицензии";
	$lang_hremplicenses_licentype = "Тип лицензии";
	$lang_hremplicenses_assignlicen = "Полученные лицензии";

	$lang_hremplicenses_NoLicenseSelected = "Лицензия не выбрана";
	$lang_hremplicenses_SelectLicenseType = "Выберите тип лицензии";

	$lang_hremp_SelectAtLEastOneAttachment = "Выберите хотя бы одно приложение, которое вы xотите удалить";

	$lang_hrEmpMain_ratinggarde = "Уровень знаний";
	$lang_hrEmpMain_assignlanguage = "Владение языками";
	$lang_hrEmpMain_Writing = "Письменно";
	$lang_hrEmpMain_Speaking = "Устно";
	$lang_hrEmpMain_Reading = "Чтение";
	$lang_hrEmpMain_Poor = "Низкий";
	$lang_hrEmpMain_Basic = "Основной";
	$lang_hrEmpMain_Good = "Xороший";
	$lang_hrEmpMain_MotherTongue = "Родной язык";
	$lang_hrEmpMain_Individual = "Индивидуальный";
	$lang_hrEmpMain_employeemembership = "Членство служащего";
	$lang_hrEmpMain_subownership = "Принадлежность";
	$lang_hrEmpMain_subamount = "Количество подписок";
	$lang_hrEmpMain_subcomdate = "Дата начала действия подписки";
	$lang_hrEmpMain_subredate = "Дата обновления подписки";
	$lang_hrEmpMain_selmemtype = "Выберите тип членства";
	$lang_hrEmpMain_selmemship = "Выберите членство";
	$lang_hrEmpMain_selownership = "Выберите принадлежность";
	$lang_hrEmpMain_assignmemship = "Членство";
	$lang_hrEmpMain_paygrade = "Уровень оплаты";
	$lang_hrEmpMain_currency = "Валюта";
	$lang_hrEmpMain_minpoint = "Минимальный оклад";
	$lang_hrEmpMain_maxpoint = "Максимальный оклад";
	$lang_hrEmpMain_bassalary = "Основная зарплата";
	$lang_hrEmpMain_assignedsalary = "Оклад";

	$lang_hrEmpMain_heading = "Отчёт служащего";
	$lang_hrEmpMain_supervisorsubordinator = "Руководитель / подчинённый";
	$lang_hrEmpMain_reportingmethod = "Вид отчётности";
	$lang_hrEmpMain_subordinateinfomation = "Информация о подчинённом";
	$lang_hrEmpMain_supervisorinfomation = "Информация о руководителе";
	$lang_hrEmpMain_selecttype = "Выберите вид";
	$lang_hrEmpMain_Direct = "Прямая";
	$lang_hrEmpMain_Indirect = "Косвенная";
	$lang_hrEmpMain_employeeskill = "Навыки служащего";
	$lang_hrEmpMain_yearofex = "Срок опыта (в годаx)";
	$lang_hrEmpMain_assignskills = "Установленные навыки";
	$lang_hrEmpMain_employerworkex = "Рабочий опыт служащего";
	$lang_hrEmpMain_employer = "Работодатель";
	$lang_hrEmpMain_enddate = "Дата окончания";
	$lang_hrEmpMain_startdate = "Дата начала";
	$lang_hrEmpMain_assignworkex = "Присвоенный опыт работы";
	$lang_hrEmpMain_workexid = "Код рабочего опыта";
	$lang_hrEmpMain_internal = "В компании";
	$lang_hrEmpMain_major = "Специализация";
	$lang_hrEmpMain_education = "Образование";
	$lang_hrEmpMain_gpa = "GPA / средняя оценка";
	$lang_hrEmpMain_assigneducation = "Приобретённое образование";
	$lang_hrEmpMain_assignattach = "Приложения";

	$lang_hrEmpMain_SelectEducation = "Выберите образование";
	$lang_hrEmpMain_YearsOfExperiencCannotBeBlank = "Введите число лет опыта";
	$lang_hrEmpMain_YearsOfExperiencWrongFormat = "Неправильный формат ввода лет опыта";
	$lang_hrEmpMain_YearsOfExperiencBetween = "Значение лет опыта должно быть в интервале #range";
	$lang_hrEmpMain_Skill = "Навык";

	$lang_hrEmpMain_subown_Company = "Компания";
	$lang_hrEmpMain_subown_Individual = "Частное лицо";
	$lang_hrEmpMain_arrRepType_Supervisor = "Руководитель";
	$lang_hrEmpMain_arrRepType_Subordinate = "Подчинённый";
	$lang_hrEmpMain_arrRepMethod_Direct = "Прямой";
	$lang_hrEmpMain_arrRepMethod_Indirect = "Косвенный";
	$lang_hrEmpMain_SelectMethod = "Выберите";

	$lang_hrEmpMain_SubscriptionAmountShouldBeNumeric = "Плата за подписку должна иметь цифровое значение";
	$lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue = "Членский взнос не введён. Продолжить?";

	$lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate = "Дата начала должна быть раньше чем дата обновления";

	$lang_hrEmpMain_membershiptype = "Тип членства";
	$lang_hrEmpMain_membership = "Членство";

	$lang_jobtitle_heading = "Служба : Названия должностей";
	$lang_jobtitle_jobtitid = "Код должности";
	$lang_jobtitle_jobtitname = "Название должности";
	$lang_jobtitle_jobtitdesc = "Описание работы";
	$lang_jobtitle_jobtitcomments = "Коментарии к должности";
	$lang_jobtitle_addpaygrade = "Добавить уровень оплаты";
	$lang_jobtitle_emstatExpl = "Создать служебный статус разрешённый для должности";
	$lang_jobtitle_editpaygrade = "Редактировать уровень оплаты";
	$lang_jobtitle_addempstat = "Добавить служебный статус";
	$lang_jobtitle_editempstat = "Редактировать служебный статус";
	$lang_jobtitle_empstat = "Степень занятости
Занятость";

	$lang_languageinformation_heading = "Навыки : Языки";

	$lang_licenses_heading = "Квалификации : Лицензии";

	$lang_locations_heading = "Информация о компании : Местонахождение ";

	$lang_membershipinfo_heading = "Членство : Членство";
	$lang_membershipinfo_membershiptype = "Тип членства";

	$lang_membershipinfor_selectmember = "Выберите членство";

	$lang_membershiptypes_heading = "Членство : Тип членства";

	$lang_nationalityinformation_heading = "Национальность и раса : Национальность";

	$lang_provinceinformation_heading = "Округ / Область : Гео Информация";

	$lang_qualificationtypes_heading = "Виды квалификации : Информация о квалификацияx";

	$lang_repview_ReportID = "Код отчёта";
	$lang_repview_ReportName = "Название отчёта";
	$lang_repview_ViewEmployeeReports = "Просмотреть отчёты по служащему";
	$lang_repview_message = "Удаление может повлиять на Иерархию компании";
	$lang_repview_DefineEmployeeReports = "Создать отчёты по служащему";
	$lang_repview_message2 = "Удаление может повлиять на просмотр отчётов";

	$lang_routeinformation_heading = "Информация о маршруте : Информация о маршруте";

	$lang_salarygrades_heading = "Служба : Уровень оплаты";
	$lang_salarygrades_currAss = "Присвоенная валюта";
	$lang_salarygrades_stepSal = "Порог повышения";

	$lang_salcurrdet_heading = "Присвоение валюты уровню оплаты";
	$lang_salcurrdet_assigncurr = "Присвоенная валюта";

	$lang_satutoryinfo_heading = "Статутный Тип : Информация о звене" ;

	$lang_view_EmploymentStatusID = "Код служебного статуса";
	$lang_view_EmploymentStatusName = "Название служебного статуса";
	$lang_view_message = "Удаление может повлиять на Должности";
	$lang_view_message1 = "Удаление может повлиять на Уровни оплаты служащих";
	$lang_view_SkillName = "Наименование навыка";
	$lang_view_SkillID = "Код записи навыка";
	$lang_view_message3 = "Удаление может повлиять на Информацию служащего";
	$lang_view_LocationName = "Название местонахождения";
	$lang_view_message4 = "Удаление может повлиять на Иерархию компании. Если местонахождение связанно асоциациями, то удаление может не удасться";
	$lang_view_CurrencyName = "Наименование валюты";
	$lang_view_CurrencyTypes = "Виды валюты";
	$lang_view_message5 = "Удаление может повлиять на на данные о валюте";
	$lang_view_CompanyHierarchy = "Иерархия компании";
	$lang_view_CompanyHierarchyName = "Название иерархии компании";
	$lang_view_message6 = "Удаление может повлиять на Информацию служащего";

	$lang_view_QualificationType = "Тип квалификации";
	$lang_view_QualificationName = "Название квалификации";
	$lang_view_message9 = "Удаление может повлиять на Квалификации, Должностные квалификации и Квалификации служащего";
	$lang_view_RatingMethod = "Метод оценки";
	$lang_view_RatingMethodName = "Название метода оценки";
	$lang_view_message10 = "Удаление может повлиять на Квалификации, Языки" ;
	$lang_view_CorporateTitle = "Корпоративные звания";
	$lang_view_CorporateTitleName = "Название корпоративного звания";
	$lang_view_message11 = "Удаление может повлиять на Информацию служащего, Должности";
	$lang_view_ExtraCategory = "Виды дополнительной деятельности";
	$lang_view_ExtraCategoryName = "Название вида дополнительной деятельности";
	$lang_view_message12 = "Удаление может повлиять на Виды дополнит. деятельности, Дополнит. деятельность служащего";
	$lang_view_MembershipTypeName = "Название типа членства";
	$lang_view_message13 = "Удаление может повлиять на Членство служащего, Членство";
	$lang_view_EmployeeCategoryName = "Название категории служащего";
	$lang_view_message14 = "Удаление может повлиять на Информацию служащего";
	$lang_view_EmployeeGroupName = "Название групппы служащиx";
	$lang_view_message15 = "Удаление может повлиять на Информацию служащего";
	$lang_view_NationalityName = "Название национальности";
	$lang_view_message16 = "Удаление может повлиять на Информацию служащего";
	$lang_view_CountryID = "Код страны";
	$lang_view_CountryName = "Страна";
	$lang_view_message17 = "Удаление может повлиять на Информацию служащего";
	$lang_view_HierarchyDefinition = "Определение иерархии";
	$lang_view_HierarchyDefinitionName = "Название иерархии";
	$lang_view_message18 = "Удаление может повлиять на иерархию компании";
	$lang_view_StateProvinceName = "Округ / область";
	$lang_view_message19 = "Удаление может повлиять на Информацию служащего";
	$lang_view_CityName = "Город";
	$lang_view_message20 = "Удаление может повлиять на Информацию служащего";
	$lang_view_LanguagName = "Язык";
	$lang_view_message21 = "Удаление может повлиять на Языки служащего";
	$lang_view_Membership = "Членство";
	$lang_view_MembershipType = "Тип членства";
	$lang_view_MembershipName = "Описание членства";
	$lang_view_message22 = "Удаление может повлиять на Членство служащего";
	$lang_view_ExtraActivities = "Дополнительная деятельность";
	$lang_view_ExtraActivitiesName = "Название дополнительной деятельности";
	$lang_view_message23 = "Удаление может повлиять на Дополнительную деятельность служащего";
	$lang_view_PayGradeName = "Наименование уровня оплаты";
	$lang_view_message24 = "Удаление может повлиять на Информацию служащего, Наименования должностей";

	$lang_view_message25 = "Удаление может повлиять на Образование";
	$lang_view_EmployeeTypeID = "Код типа служащего";
	$lang_view_EmployeeTypeName = "Название типа служащего";
	$lang_view_message26 = "Удаление может повлиять на Информацию служащего";
	$lang_view_EEOJobCategory = "ЕЕО категория работы" ;
	$lang_view_EEOJobCategoryid = "Код служебной категории ЕЕО";
	$lang_view_EEOJobCategoryName = "Название служебной категории ЕЕО" ;
	$lang_view_message27 = "Удаление может повлиять на Информацию служащего";
	$lang_view_message28 = "Удаление может повлиять на Языки служащего";
	$lang_view_EthnicRace = "Этническая раса" ;
	$lang_view_EthnicRaceName = "Название этнической расы";
	$lang_view_message29 = "Удаление может повлиять на служащего";
	$lang_view_message30 = "Удаление может повлиять на Информацию служащего";
	$lang_view_message31 = "Удаление может повлиять на Информацию служащего, Наименования должностей";
	$lang_view_message32 = "Удаление может повлиять на Квалификации, Должностные квалификации и Квалификации служащего";
	$lang_view_License_ID = "Код лицензии";
	$lang_view_LicenseDescription = "Описание лицензии";
	$lang_view_message33 = "Удаление может повлиять на Информацию служащего";
	$lang_view_UserID = "Код пользователя";
	$lang_view_UserName = "Имя пользователя";
	$lang_view_message34 = "Удаление может сделать OrangeHRM недействительным";
	$lang_view_UserGroupID = "Код группы пользователей";
	$lang_view_UserGroupName = "Название группы пользователей";
	$lang_view_message35 = "Удаление может сделать OrangeHRM недействительным";

	$lang_view_Users = "Пользователи";
	$lang_view_UserGroups = "Группы пользователей";
	$lang_view_HRAdmin = "Менеджер отдела кадров";
	$lang_view_ESS = "Самообслуживание";
	$lang_view_ID = "Код";

	$lang_index_WelcomeMes = "Добро пожаловать, #username";
	$lang_index_ChangePassword = "Извенить пароль";
	$lang_index_Logout = "Отсоедениться";

	$lang_MailFrom = "Письмо отправлено как";
	$lang_MailSendingMethod = "Метод отправления";
	$lang_MailTypes_Mail = "Внутренняя функция PHP";
	$lang_MailTypes_Sendmailer = "Отправленные письма";
	$lang_MailTypes_Smtp = "SMTP";
	$lang_SendmailPath = "Отправленные письма. Путь";
	$lang_SmtpHost = "SMTP ГВМ";
	$lang_SmtpPort = "SMTP порт";
	$lang_SmtpUser = "SMTP пользователь";
	$lang_SmtpPassword = "SMTP пароль";

	// PIM tab labels
	$lang_pim_tabs_Personal = "Персональный";
	$lang_pim_tabs_Contact = "Контакт";
	$lang_pim_tabs_EmergencyContacts = "Контакт(ы) в случае необходимости";
	$lang_pim_tabs_Dependents = "Иждивенцы";
	$lang_pim_tabs_Immigration = "Иммиграция";
	$lang_pim_tabs_Job = "Служба";
	$lang_pim_tabs_Payments = "Оплата";
	$lang_pim_tabs_ReportTo = "Отчётность";
	$lang_pim_tabs_WorkExperience = "Служебный опыт";
	$lang_pim_tabs_Education = "Образование";
	$lang_pim_tabs_Skills = "Навыки";
	$lang_pim_tabs_Languages = "Языки";
	$lang_pim_tabs_License = "Лицензии";
	$lang_pim_tabs_Membership = "Членство";
	$lang_pim_tabs_Attachments = "Приложения";

	// Report module
	$lang_rep_SelectionCriteria = "Критерий выбора";
	$lang_rep_EmpNo = "Номер служащего";
	$lang_rep_AgeGroup = "Возростная группа";
	$lang_rep_PayGrade = "Уровень оплаты";
	$lang_rep_Education = "Образование";
	$lang_rep_EmploymentStatus = "Служебный статус";
	$lang_rep_ServicePeriod = "Период службы";
	$lang_rep_JobTitle = "Должность";

	$lang_rep_YearOfPassing = "Год сдачи";
	$lang_rep_Contract = "Контракт";
	$lang_rep_WorkExperience = "Служебный опыт";
	$lang_rep_SelectAtLeaseOneUserGroupToAssign = "Для присвоения, выберите xотя бы одну группу пользователей";

	$lang_rep_LastName = "Фамилия";
	$lang_rep_FirstName = "Имя";
	$lang_rep_Address = "Адрес";
	$lang_rep_TelNo = "Тел No";
	$lang_rep_DateOfBirth = "Дата рождения";
	$lang_rep_JoinDate = "Дата вступления";
	$lang_rep_Qualification = "Квалификации";
	$lang_rep_EmployeeStates = "Состояние служащего";
	$lang_rep_JoinedDate = "Дата вступления";

	$lang_rep_SelectPayGrade = "Выберите уровень оплаты";
	$lang_rep_SelectEducation = "Выберите тип образования";
	$lang_rep_SelectEmploymentType = "Выберите тип службы";
	$lang_rep_SelectComparison = "Выберите сравнение";
	$lang_rep_SelectJobTitle = "Выберите должность";

	$lang_rep_Field = "Поле";
	$lang_rep_AssignUserGroups = "Присвоить группу пользователей";
	$lang_rep_AssignedUserGroups = "Присвоенные группы пользователей";

	$lang_rep_SelectAtLeastOneCriteriaAndOneField = "Выберите xотя бы один критерий и одно поле";
	$lang_rep_SelectTheComparison = "Выберите сравнение";

	$lang_rep_AgeShouldBeNumeric = "Возраст должен иметь цифровое значение";
	$lang_rep_InvalidAgeRange = "Второй выбранный возраст больше чем первый выбранный возраст";

	$lang_rep_FieldNotSelected = "Поле не выбранно";

	$lang_rep_DateShouldBeNumeric = "Дата введена неправильно";

	$lang_rep_Report = "Отчёт";
	$lang_rep_EmployeeNo = "Номер служащего";
	$lang_rep_EmployeeFirstName = "Имя служащего";
	$lang_rep_EmployeeLastName = "Фамилия служащего";

	$lang_rep_ReportTo = "Отчётность перед";
	$lang_rep_ReportingMethod = "Вид отчётности";

	$lang_rep_Address = "Адрес";
	$lang_rep_Telephone = "Телефон";
	$lang_rep_DateOfBirth = "Дата рождения";
	$lang_rep_Skills = "Навыки";
	$lang_rep_SalaryGrade = "Уровень оплаты";
	$lang_rep_EmployeeStatus = "Служебный статус";
	$lang_rep_JoinedDate = "Дата начала работы";
	$lang_rep_SubDivision = "Подразделение";
	$lang_rep_JobTitle = "Должность";

	$lang_rep_ReportDefinition = "Создание отчёта";
	$lang_rep_AssignUserGroups = "Присвоить группы пользователей";
	$lang_rep_UserGroups = "Группы пользователей";
	$lang_rep_NoUserGroupsAssigned = "Группы пользователей не присвоенны";

	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "Конфигурация почты";
	$lang_Admin_SubscribeToMailNotifications = "Подписаться на извещения по эл. почте";
	$lang_Admin_ENS_LeaveApplications = "Требуемые отпуска";
	$lang_Admin_ENS_LeaveApprovals = "Подтверждённые отпуска";
	$lang_Admin_ENS_LeaveCancellations = "Отменённые отпуска";
	$lang_Admin_ENS_LeaveRejections = "Отказанные отпуска";


	// Time tracking
	$lang_Menu_Admin_Customers = "Клиенты";
	$lang_Menu_Admin_Projects = "Проекты";
	$lang_Menu_Time = "Время";
	$lang_Menu_Time_Timesheets = "Табель";
	$lang_Menu_Time_PersonalTimesheet = "Личный табель";
	$lang_Menu_Time_EmployeeTimesheets = "Табеля служащего";


	//timesheet
	$lang_Time_Timesheet_TimesheetForViewTitle = "Табель за #periodName от #startDate";
	$lang_Time_Timesheet_TimesheetForEditTitle = "Редактировать табель за #periodName от #startDate";

	$lang_Time_Timesheet_TimesheetNameForViewTitle = "Табель служащего #name за #periodName от #startDate";
	$lang_Time_Timesheet_TimesheetNameForEditTitle = "Редактировать табель служащего #name за #periodName от #startDate";

	$lang_Time_Timesheet_Status = "Статус: #status";

	$lang_Time_Timesheet_Status_NotSubmitted = "Не отправлен";
	$lang_Time_Timesheet_Status_Submitted = "Отправлен";
	$lang_Time_Timesheet_Status_Approved = "Подтверждён";
	$lang_Time_Timesheet_Status_Rejected = "Отклонён";

	$lang_Time_Timesheet_Customer = "Клиент";
	$lang_Time_Timesheet_ProjectActivity = "Проект / Деятельность";

	$lang_Time_Timesheet_Total = "Всего";

	$lang_Time_Errors_SUBMIT_SUCCESS = "Отправка завершена";
	$lang_Time_Errors_SUBMIT_FAILURE = "Отправка не удалась";
	$lang_Time_Errors_UPDATE_SUCCESS = "Обновленно";
	$lang_Time_Errors_UPDATE_FAILURE = "Обновление не удалось";
	$lang_Time_Errors_CANCEL_SUCCESS = "Отмененно";
	$lang_Time_Errors_CANCEL_FAILURE = "Отмена не удалась";
	$lang_Time_Errors_APPROVE_SUCCESS = "Подтвержденно";
	$lang_Time_Errors_APPROVE_FAILURE = "Подтверждение не удалось";
	$lang_Time_Errors_REJECT_SUCCESS = "Отклонённо";
	$lang_Time_Errors_REJECT_FAILURE = "Отклонение не удалось";
	$lang_Time_Errors_DELETE_SUCCESS = "Удалено";
	$lang_Time_Errors_DELETE_FAILURE = "Удаление не удалось";
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "Несанкционированные действия";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "Табеля не найдены";

	$lang_Time_Errors_EncounteredTheFollowingProblems = "Возникли следующие проблемы";
	$lang_Time_Errors_ReportedDateNotSpecified = "Дата отчёта не указана";
	$lang_Time_Errors_ProjectNotSpecified = "Проект не указан";
	$lang_Time_Errors_CustomerNotSpecified = "Клиент не указан";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified = "Не правильно указан интервал времени";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval = "Продолжительность и указанный интервал времени должны совпадать";

	$lang_Time_Timesheet_StartTime = "Время начала";
	$lang_Time_Timesheet_EndTime = "Время окончания";
	$lang_Time_Timesheet_ReportedDate = "Дата отчёта";
	$lang_Time_Timesheet_Duration = "Продолжительность";
	$lang_Time_Timesheet_DurationUnits = "(час -а/-ов)";
	$lang_Time_Timesheet_Decription = "Описание";
	$lang_Time_Timesheet_NoCustomers = "Нет клиентов";
	$lang_Time_Timesheet_NoProjects = "Нет проектов";

	$lang_Time_Select_Employee_Title = "Выберите служащего";

	$lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval = "Отправленные табеля ждут подтверждения руководителя";
	$lang_Time_Select_Employee_WeekStartingDate = "Начало недели с #date";

	$lang_Time_Select_Employee_TimesheetPeriod = "Период табеля";

	$lang_Time_Errors_PleaseAddAComment = "Пожайлуста, добавьте коментарий";

	$lang_mtview_ADD_SUCCESS 				= "Добавлено";
	$lang_mtview_UPDATE_SUCCESS 			= "Обновленно";
	$lang_mtview_DELETE_SUCCESS 			= "Удалено";
	$lang_mtview_ADD_FAILURE 				= "Добавление не удалось";
	$lang_mtview_UPDATE_FAILURE 			= "Обновление не удалось";
	$lang_mtview_DELETE_FAILURE 			= "Удаление не удалось";

	// PIM
	$lang_empview_EmploymentStatus = "Служебный статус";
	$lang_hremp_Subdivision	= "Подразделение";
	$lang_hremp_ShowEmployeeContracts = "SПоказать контракты служащего";
	$lang_hremp_HideEmployeeContracts = "Скрыть контракты служащего";
	$lang_rep_SelectSkill = "Выберите навык";

?>