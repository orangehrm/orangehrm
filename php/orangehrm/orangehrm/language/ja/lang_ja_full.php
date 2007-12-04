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

	$lang_Error_PleaseCorrectTheFollowing 			= "以下を直してください";
	$lang_Error_SelectAtLeastOneRecordToDelete 		= "削除したいレコードをひとつ選んでください";
	$lang_Error_DoYouWantToDelete 					= "削除したいですか";
	$lang_Error_EnterDate							= "日付入力?";

	$lang_Error_PleaseSelectAYear 					= "年を選択してください";
	$lang_Error_PleaseSelectAnEmployee 				= "従業員を選んでください";

	$lang_Error_DoYouWantToContinue 				= "続けたいですか?";

	$lang_Error_PleaseSelectAnEmployee				= "従業員を選んでください";

	$lang_Error_ShouldBeNumeric 					= "数値であるべきです";
	$lang_Error_FollowingErrorsWereFound 			= "次のエラーは見つけられました";
	$lang_Error_AreYouSureYouWantToDelete 			= "確実に削除したいですか";
	$lang_Error_AccessDenied                        = "アクセス拒絶";

	//leave
	$lang_Error_PleaseSelectAValidFromDate 			= "有効な日付を選択してください＞から";
	$lang_Error_PleaseSelectAValidToDate 			= "有効な日付を選択してくださいまで";
	$lang_Error_PleaseSelectALeaveType 				= "休暇タイプを選択してください";

	$lang_Error_LeaveDateCannotBeABlankValue 		= "休暇日付は、空白としないでください!";
	$lang_Error_NameOfHolidayCannotBeBlank 			= "休日の名前は、空白としないでください";

	$lang_Error_NoLeaveTypes 						= "休暇タイプはありません";
	$lang_Error_NoRecordsFound 						= "レコードはありません!";

	$lang_Error_InvalidDate 						= "無効な日付";

	//PIM
	$lang_Error_LastNameNumbers						= "名字には数字があります。続けますか";
	$lang_Error_FirstNameNumbers					= "名前は数字があります。続けたいですか";
	$lang_Error_MiddleNameNumbers					= "ミドルネームにはは数字があります。続けますか";
	$lang_Error_MiddleNameEmpty						= "ミドルネームは空白です。続けますか";
	$lang_Error_LastNameEmpty						= "名字が空白です!";
	$lang_Error_FirstNameEmpty						= "名前が空白です!";
	$lang_Error_ChangePane							= "次のステップに移す前に変化をセーブしてください!";

	$lang_Error_UploadFailed						= "アップロードは失敗しました!";
	$lang_Errro_WorkEmailIsNotValid                 = "ワークeメールは有効ではありません";
	$lang_Errro_OtherEmailIsNotValid                = "他のeメールは有効ではありません";

	// Company Structure
	$lang_Error_Company_General_Undefined 				= "最初に会社一般の情報を定めてください!";
	$lang_Error_CompStruct_UnitCount 					= "また #parentの下の#children単位は、削除されます";
	$lang_Error_ItCouldCauseTheCompanyStructureToChange = "それによって、会社構造が変わるかもしれないです";

	$lang_Error_SubDivisionNameCannotBeEmpty 			= "サブ・デビジョンの名前は空白としないでください";
	$lang_Error_PleaseSelectATypeOrDefineACustomType 	= "タイプを選択するか、またはカスタムタイプを定義してください";
	$lang_Error_CompStruct_LocEmpty 					= "ロケーションを選ぶか、新しいロケーションを定義して選んでください";

	$lang_Error_LocationNameEmpty 						= "ロケーション名前は空白です";
	$lang_Error_CountryNotSelected 						= "；国を選んでください";
	$lang_Error_StateNotSelected 						= "州を選ばれていない";
	$lang_Error_CityCannotBeEmpty 						= "市は空白としないでください";
	$lang_Error_AddressEmpty 							= "住所は空白です";
	$lang_Error_ZipEmpty 								= "ジップ ・ コードを空白としないでください";
	$lang_Error_CompStruct_ZipInvalid 					= "ジップ ・コードには非数字があります。　ここでそれらは#characterList";

	$lang_Error_InvalidEmail 							= "無効なeメールアドレス";

	$lang_Error_InvalidDescription						= "無効の記述";

	$lang_Error_FieldShouldBeNumeric					= "フィールドは数でなければなりません";

	$lang_Error_FieldShouldBeSelected					= "フィールドは選ばれなければなりません";
	$lang_Error_SelectAtLeastOneCheckBox 				= "少なくとも1つのチェックボックスを選んでください";

	/**
	 * Menu Items
	 *
	 */

	// Home
	$lang_Menu_Home 							= "ホーム";

	$lang_Menu_Home_Support 					= "サポート";
	$lang_Menu_Home_Forum 						= "フォーラム";
	$lang_Menu_Home_Blog 						= "ブログ";

	$lang_Menu_Ess								= "ESS";
	$lang_Menu_Pim 								= "PIM";

	// Admin Module
	$lang_Menu_Admin 							= "アドミン";

	$lang_Menu_Admin_CompanyInfo 				= "会社情報";
	$lang_Menu_Admin_CompanyInfo_Gen 			= "一般的な情報";
	$lang_Menu_Admin_CompanyInfo_CompStruct 	= "会社構造";
	$lang_Menu_Admin_CompanyInfo_Locations 		= "場所";

	$lang_Menu_Admin_Job 						= "職業";
	$lang_Menu_Admin_Job_JobTitles 				= "職タイトル";
	$lang_Menu_Admin_Job_PayGrades 				= "給与等級";
	$lang_Menu_Admin_Job_EmpStatus 				= "仕事ステータス";
	$lang_Menu_Admin_Job_EEO 					= "EEO職業のカテゴリ";

	$lang_Menu_Admin_Quali 						= "資格";
	$lang_Menu_Admin_Quali_Education 			= "教育";
	$lang_Menu_Admin_Quali_Licenses 			= "ライセンス";

	$lang_Menu_Admin_Skills 					= "技能";
	$lang_Menu_Admin_Skills_Skills 				= "技能";
	$lang_Menu_Admin_Skills_Languages 			= "言語";

	$lang_Menu_Admin_Memberships 					= "メンバーシップ";
	$lang_Menu_Admin_Memberships_Memberships 		= "メンバーシップ";
	$lang_Menu_Admin_Memberships_MembershipTypes 	= "メンバーシップタイプ";

	$lang_Menu_Admin_NationalityNRace 				= "国籍と人種";
	$lang_Menu_Admin_NationalityNRace_Nationality 	= "国籍";
	$lang_Menu_Admin_NationalityNRace_EthnicRaces 	= "人種";

	$lang_Menu_Admin_Users 							= "ユーザー";
	$lang_Menu_Admin_Users_UserGroups 				= "アドミンユーザーグループ";
	$lang_Menu_Admin_Users_HRAdmin 					= "HR アドミンユーザー";
	$lang_Menu_Admin_Users_ESS 						= "ESS ユーザー";

	$lang_Menu_Admin_EmailNotifications = "eメール通知";
	$lang_Menu_Admin_EmailConfiguration = "構成";
	$lang_Menu_Admin_EmailSubscribe = "加入";
	$lang_Menu_Admin_ProjectInfo = "プロジェクト情報";
	$lang_Menu_Admin_Customers = "クライアント";
	$lang_Menu_Admin_Projects = "プロジェクト";

	// Leave Module
	$lang_Menu_Leave 									= "休暇";
	$lang_Menu_Leave_PersonalLeaveSummary 				= "個人の休暇要約";
	$lang_Menu_Leave_EmployeeLeaveSummary 				= "従業員休暇要約";
	$lang_Menu_Leave_LeaveSummary 						= "休暇要約";
	$lang_Menu_Leave_LeavesList 						= "休暇リスト";
	$lang_Menu_Leave_ApproveLeave 						= "休暇を承認してください";
	$lang_Menu_Leave_LeaveTypes 						= "休暇タイプを定義してください";
	$lang_Menu_Leave_Apply 								= "申し込む";
	$lang_Menu_Leave_Assign								= "休暇割り当てる";
	$lang_Menu_Leave_LeaveList 							= "休暇リスト";
	$lang_Menu_Leave_DefineDaysOff 						= "オフである何日も定義してください";
	$lang_Menu_Leave_DefineDaysOff_Weekends 			= "週末";
	$lang_Menu_Leave_DefineDaysOff_SpecificHolidays 	= "特別な休日";

 	$lang_Leave_Title_Apply_Leave = "休暇申し込む";
 	$lang_Leave_Title_Assign_Leave = "休暇割り当てる";
	$lang_Leave_APPLY_SUCCESS = "申し込み成功";
	$lang_Leave_APPLY_FAILURE = "申し込み失敗";
	$lang_Leave_APPROVE_SUCCESS = "休暇を割り当てました";
	$lang_Leave_APPROVE_FAILURE = "休暇を割り当ての失敗";
	$lang_Leave_CANCEL_SUCCESS = "キャンセル成功";
	$lang_Leave_CHANGE_STATUS_SUCCESS = "休暇ステータスチェーンジは成功";

	// Report
	$lang_Menu_Reports = "レポート";
	$lang_Menu_Reports_ViewReports = "レポートを見る";
	$lang_Menu_Reports_DefineReports = "レポートの定義";

	// Time module
	$lang_Menu_Time = "時間";
	$lang_Menu_Time_Timesheets = "タイムシート";
	$lang_Menu_Time_PersonalTimesheet = "マイタイムシート";
	$lang_Menu_Time_EmployeeTimesheets = "従業員タイムシート";

	/**
	 * Common
	 */
	$lang_Common_ConfirmDelete				= "削除したいですか?";
	$lang_Common_FieldEmpty				= "フィールド空白";
	$lang_Common_SelectDelete			= "削除したいレコードをひとつ選んでください";
	$lang_Common_SelectField			= "探したいフィールドを選んでください!";
	$lang_Commn_RequiredFieldMark 			= "アスタリスク#starがつけているフィールドは必要です";
	$lang_Commn_code 						= "コード";
	$lang_Commn_description 				= "記述";
	$lang_Commn_title 						= "タイトル";
	$lang_Commn_name 						= "名前";
	$lang_Commn_PleaseWait					= "お待ちください";
	$lang_Common_Select 				= "選んでください";
	$lang_Commn_Email						= "e-メール";
	$lang_Common_Loading				= "ローディング";
	$lang_Common_LoadingPage                        = "ローディングページ";
	$lang_Common_NotApplicable                      = "N/A";
	$lang_Common_Male                      		= "男";
	$lang_Common_Female                    		= "女";

	$lang_Common_Edit = "編集";
	$lang_Common_New = "新しい";
	$lang_Common_Save = "セーブ";

	$lang_Common_Time = "タイム";
	$lang_Common_Note = "メモ";

	$lang_Common_AccessDenied = "アクセス拒絶";

	//days
	$lang_Common_Monday 					= "月";
	$lang_Common_Tuesday 					= "火";
	$lang_Common_Wednesday 					= "水";
	$lang_Common_Thursday 					= "木";
	$lang_Common_Friday 					= "金";
	$lang_Common_Saturday 					= "土";
	$lang_Common_Sunday 					= "日";

	$lang_Common_Sort_ASC = "上がる";
	$lang_Common_Sort_DESC = "下がる";
	$lang_Common_EncounteredTheFollowingProblems = "以下の問題に遭遇しました";

	$lang_Common_ADD_SUCCESS 				= "加入成功";
	$lang_Common_UPDATE_SUCCESS 			= "アップデート成功";
	$lang_Common_DELETE_SUCCESS 			= "削除成功";
	$lang_Common_ADD_FAILURE 				= "加入失敗";
	$lang_Common_UPDATE_FAILURE 			= "";
	$lang_Common_DELETE_FAILURE 			= "削除失敗";

	$lang_Leave_Common_Weekend 				= "週末";

	// admin module
	$lang_Admin_Common_Institute 			= "センター";
	$lang_Admin_Common_Course 				= "コース";
	$lang_Admin_education_InstituteCannotBeBlank = "センターは空白としないでください!";
	$lang_Admin_CourseCannotBeBlank         = "コースは空白としないでください!";
	$lang_Admin_License_DescriptionCannotBeBlank = "ライセンス記述は空白としないでください!";

	// leave module
	$lang_Leave_Title = "OrangeHRM - 休暇モジュール";
	$lang_Leave_Common_Date 				= "日付";

	$lang_Leave_Common_FromDate 			= "日から";
	$lang_Leave_Common_ToDate 				= "日まで";

	$lang_Leave_Common_LeaveQuotaNotAllocated = "休日コーターを割り当てていません. HR Adminに相談してください";

	$lang_Leave_Common_LeaveType 			= "休暇タイプ";
	$lang_Leave_Common_Status 				= "ステータス";
	$lang_Leave_Common_Length 				= "丸一日・半日";
	$lang_Leave_Common_Range 				= "範囲";
	$lang_Leave_Common_Comments 			= "コメント";
	$lang_Leave_Common_Comment 				= "コメント";
	$lang_Leave_Common_Approved 			= "承認";
	$lang_Leave_Common_Cancelled 			= "キャンセル";
	$lang_Leave_Common_PendingApproval 		= "承認は未定";
	$lang_Leave_Common_Rejected 			= "拒絶";
	$lang_Leave_Common_Taken 				= "受け取り";
	$lang_Leave_Common_InvalidStatus 		= "無効なステータス";
	$lang_Leave_Common_StatusDiffer 		= "違うステータス";
	$lang_Leave_Common_FullDay 				= "丸一日";
	$lang_Leave_Common_HalfDayMorning 		= "半日 / Morning午前";
	$lang_Leave_Common_HalfDayAfternoon 	= "半日/ 午後";
	$lang_Leave_Common_HalfDay 				= "半日";
	$lang_Leave_Common_LeaveTaken 			= "取った休暇";
	$lang_Leave_Common_LeaveRemaining 		= "残りの休暇";
	$lang_Leave_Common_LeaveScheduled 		= "予定された休暇";
	$lang_Leave_Common_LeaveTypeName 		= "休暇タイプ名";
	$lang_Leave_Common_LeaveTypeId 			= "休暇タイプID";
	$lang_Leave_Common_Select 				= "選択";
	$lang_Leave_Common_oldLeaveTypeName 	= "既存の休暇タイプ名";
	$lang_Leave_Common_newLeaveTypeName 	= "新たな休暇タイプ名";
	$lang_Leave_Common_EmployeeName 		= "従業員名";
	$lang_Leave_Common_LeaveEntitled 		= "取られる休暇";
	$lang_Leave_Common_Year 				= "年";
	$lang_Leave_Common_ListOfTakenLeave 	= "取った休暇のリスト";
	$lang_Leave_Common_Remove 				= "除く";
	$lang_Leave_Common_AllEmployees			= "各従業員";
	$lang_Leave_Common_All					= "すべて";

	$lang_Leave_Common_NameOfHoliday 		= "休日名";
	$lang_Leave_Common_Recurring 			= "毎年繰り返します";

 	$lang_Leave_Leave_list_Title1 			= "休暇承認";
	$lang_Leave_Leave_Requestlist_Title1 	= "#employeeNameに休暇要求を承認";
	$lang_Leave_Leave_list_Title2 			= "#employeeNameは#dispYearに取った休暇";
	$lang_Leave_Leave_list_Title3 			= "休暇リスト";
	$lang_Leave_Select_Employee_Title 		= "従業員選ぶ";
	$lang_Leave_Leave_Summary_Title 		= "休暇要約";
	$lang_Leave_Leave_Summary_EMP_Title 	= "#dispYearの#employeeNameの休暇要約";
	$lang_Leave_Select_Employee_Title 		= "従業員か休暇タイプの選択";
	$lang_Leave_Leave_Summary_EMP_Title 	= "#dispYearの休暇要約";
	$lang_Leave_Leave_Summary_SUP_Title 	= "#dispYearの#employeeNameの休暇要約";
	$lang_Leave_Define_leave_Type_Title 	= "休暇タイプを定義してください";
	$lang_Leave_Leave_Type_Summary_Title 	= "休暇タイプ";
	$lang_Leave_Leave_Holiday_Specific_Title = "オフである何日も定義してください : 特別な休日";
	$lang_Leave_Leave_Holiday_Weeked_Title 	= "オフである何日も定義してください : 週末";
	$lang_Leave_Summary_Deleted_Types_Shown = "指示削除された休暇タイプ指示";
        $lang_Leave_Summary_Deleted_Types_MoreInfo = "Deleted leave types are shown here if employees have used these leave types before they were deleted. Deleted leave types are retained in the system but cannot be used for new leave requests.";

	$lang_Leave_Holiday = "休日";
	$lang_Leave_NoOfDays = "日数";
	$lang_Leave_Period = "休暇期間";

	$lang_Leave_Define_IsDeletedName  	= "削除された休暇は同名である.";
	$lang_Leave_Define_UndeleteLeaveType    = "新休暇タイプの代わりに削除された休暇, クリック:";
	$lang_Leave_NAME_IN_USE_ERROR 		= "休暇タイプ名を使用されれている。. 他の名を選んでください.";
	$lang_Leave_ADD_FAILURE			= "加えるは失敗";
	$lang_Leave_ADD_SUCCESS 		= "加えるは成功";
	$lang_Leave_LEAVE_TYPE_NOT_FOUND_ERROR 	= "休暇タイプは見つかれない";
	$lang_Leave_UNDELETE_SUCCESS		= "休暇タイプ非削除成功.";
	$lang_Leave_DUPLICATE_LEAVE_TYPE_ERROR  = "複製した休暇タイプが指定されました. 休暇タイプ名はユニークでなければならない.";
	$lang_Leave_LEAVE_TYPE_EDIT_ERROR       = "Error saving changes";
	$lang_Leave_LEAVE_TYPE_EDIT_SUCCESS     = "非削除";
	$lang_Leave_NO_CHANGES_TO_SAVE_WARNING  = "No changes to save";
	$lang_Leave_Undelete			= "非削除";

	$lang_bankInformation_code 				= "コード";
	$lang_bankInformation_description 		= "記述";

	$lang_compstruct_add 			= "加";
	$lang_compstruct_delete 		= "削除";
	$lang_compstruct_clear 			= "クリア";
	$lang_compstruct_hide 			= "隠す";
	$lang_compstruct_save 			= "セーブ";

 	$lang_comphire_heading 							= "会社の階層構造:会社情報";
	$lang_comphire_relationalhierarchy 				= "関係階層構造";
	$lang_comphire_employee 						= "従業員";
	$lang_comphire_definitionlevel 					= "定義レベル";
	$lang_comphire_telephone 						= "電話";
	$lang_comphire_fax 								= "ファックス";
	$lang_comphire_email							= "eメール";
	$lang_comphire_url 								= "URL";
	$lang_comphire_logo 							= "ロゴ";
	$lang_comphire_selecthie 						= "階層構造選択";
	$lang_comphire_selectdef 						= "定義レベル選択";
	$lang_compstruct_heading 						= "会社情報:会社構造";
	$lang_compstruct_frmSub_divisionHeadingAdd 		= "サブ・デビジョンを加えてください";
	$lang_compstruct_frmSub_divisionHeadingEdit 	= "編集";
	$lang_compstruct_Name 							= "名前";
	$lang_compstruct_Type 							= "タイプ";
	$lang_compstruct_Division 						= "デビジョン";
	$lang_compstruct_Description 					= "記述";
	$lang_compstruct_Department 					= "部";
	$lang_compstruct_Team 							= "チーム";
	$lang_compstruct_Other 							= "他";
	$lang_compstruct_Location 						= "場所";
	$lang_compstruct_frmNewLocation 				= "新場所の定義";
	$lang_compstruct_Address 						= "住所";
	$lang_compstruct_country						= "国";
	$lang_compstruct_state 							= "州・区";
	$lang_compstruct_city 							= "市";
	$lang_compstruct_ZIP_Code 						= "ZIP コード";
	$lang_compstruct_Phone 							= "電話";
	$lang_compstruct_no_root 						= "根を見つかれない!根を定義してください.";

 	$lang_corptit_heading 							= "会社タイトル : 仕事情報";
	$lang_corptit_topinhierachy 					= "階層のトップ";
	$lang_corptit_multipleheads 					= "Multiple Heads";
	$lang_corptit_headcount 						= "Head Count";
	$lang_corptit_nextlevelupgrade 					= "次のレベルアップグレード";
	$lang_corptit_selectcor 						= "会社タイトル選択";
	$lang_corptit_salarygrade 						= "給料グレード";
	$lang_corptit_selectsal 						= "給料グレード選択";

 	$lang_costcenters_heading 						= "コストセンター :会社情報";

 	$lang_countryinformation_heading 				= "国情報 :地理的な情報";

 	$lang_currencytypes_heading 					= "通貨タイプ :仕事情報";


 	$lang_districtinformation_heading 				= "市情報:地理的な情報";
	$lang_districtinformation_selectcounlist 		= "国を選んでください";
	$lang_districtinformation_selstatelist 			= "州を選んでください";

 	$lang_eeojobcat_heading 						= "仕事: EEO　仕事カテゴリ";
	$lang_eeojobcat_description 					= "タイトル";
	$lang_eeojobcat_TitleContainsNumbers            = "タイトルには数字があります.続けますか";
	$lang_eeojobcat_TitleMustBeSpecified            = "タイトルを指定しなければなりません.";

 	$lang_electorateinformation_heading 			= "選挙区情報 :地理的な情報";
	$lang_emprepinfo_heading						= "従業員レポートの定義";

 	$lang_emptypes_heading 					= "従業員タイプ: Nexus Information";
	$lang_emptypes_datelimited 				= "制限された日付";
	$lang_emptypes_prefix 					= "Prefix";

 	$lang_empview_heading 					= "仕事:仕事ステータス";
	$lang_empview_EmpID 					= "従業員ID";
	$lang_empview_EmpFirstName 				= "従業員名前";
	$lang_empview_EmpLastName 				= "従業員名字";
	$lang_empview_EmpMiddleName 			= "従業員ミドルネーム";
	$lang_empview_search					= "検索";
	$lang_empview_searchby 					= "何を使って検索:";
	$lang_empview_description 				= "何を検索:";
	$lang_empview_norecorddisplay 			= "表示するためにレコードはありません";
	$lang_empstatus_PleaseEnterEmploymentStatus = "仕事ステータス記述加入";
	$lang_empview_SelectField               = "検索するフィールド選択!";

	$lang_empview_last 						= "最後";
	$lang_empview_next 						= "次";
	$lang_empview_previous 					= "前";
	$lang_empview_first						= "最初";

	$lang_empview_employeeid 				= "従業員ID";
	$lang_empview_employeename 				= "従業員名前";
	$lang_empview_ADD_SUCCESS 				= "加えるは成功";
	$lang_empview_UPDATE_SUCCESS 			= "アプデートは成功";
	$lang_empview_DELETE_SUCCESS 			= "削除成功";
	$lang_empview_ADD_FAILURE 				= "加えるは失敗";
	$lang_empview_UPDATE_FAILURE 			= "アップデート失敗";
	$lang_empview_DELETE_FAILURE 			= "削除失敗";
	$lang_empview_Language 					= "言語";
	$lang_empview_WorkExperience 			= "職歴";
	$lang_empview_Payment 					= "支払い";
	$lang_empview_Skills 					= "技能";
	$lang_empview_Licenses 					= "ライセンス";
	$lang_empview_EmployeeInformation 		= "従業員情報";
	$lang_empview_Memberships 				= "メンバーシップ";
	$lang_empview_Report 					= "レポート";
	$lang_empview_ReportTo					= "だれにレポート";
	$lang_empview_SubDivision				= "サブ・デビジョン";
	$lang_empview_JobTitle					= "職タイトル";
	$lang_empview_Supervisor 				= "監督者";
	$lang_empview_EmploymentStatus			= "仕事ステータス";
	$lang_emppop_title                      = "検索従業員";

 	$lang_ethnicrace_heading 				= "国籍と人種:人種";
 	$lang_ethnicrace_NameShouldBeSpecified  = "人種名を指定しなければなりません";

 	$lang_extracurractcat_heading 			= "課外活動カテゴリー: 資格情報";

 	$lang_extracurractinfo_heading 			= "課外活動情報 : 資格情報";
	$lang_extracurractinfo_extracuaccat 	= "課外活動カテゴリー";
	$lang_extracurractinfo_selectsecucat 	= "課外活動カテゴリーの選択";

 	$lang_geninfo_heading 					= "会社情報: 一般的な情報";
	$lang_geninfo_compname 					= "会社名";
	$lang_geninfo_taxID 					= "税金ID";
	$lang_geninfo_naics 					= "NAICS";
	$lang_geninfo_err_CompanyName 			= "会社名を空白としないでください";
	$lang_geninfo_err_Phone 				= "無効な電話・ファックス番号";

 	$lang_hierarchydef_heading 				= "階層構造情報: 会社情報";

	$lang_hremp_EmpFirstName 				= "名前";
	$lang_hremp_EmpLastName 				= "名字";
	$lang_hremp_EmpMiddleName 				= "ミドルネーム";
	$lang_hremp_nickname 					= "ニック名 ";
	$lang_hremp_photo 						= "写真";
	$lang_hremp_ssnno 						= "SSN 番号 :";
	$lang_hremp_nationality 				= "国籍";
	$lang_hremp_sinno 						= "SIN 番号 :";
	$lang_hremp_dateofbirth 				= "生年月日";
	$lang_hremp_otherid 					= "他ID";
	$lang_hremp_maritalstatus 				= "結婚歴";
	$lang_hremp_selmarital					= "--選択--";
	$lang_hremp_selectnatio 				= "国籍選択";
	$lang_hremp_selethnicrace 				= "人種選択";
	$lang_hremp_smoker 						= "喫煙家";
	$lang_hremp_gender 						= "性";
	$lang_hremp_dlicenno 					= "運転ライセンス番号";
	$lang_hremp_licexpdate 					= "ライセンス有効期限日 ";
	$lang_hremp_militaryservice 			= "兵役";
	$lang_hremp_ethnicrace 					= "人種 ";
	$lang_hremp_jobtitle 					= "職タイトル";
	$lang_hremp_selempstat 					= "従業員ステータスの選択";
	$lang_hremp_eeocategory 				= "EEO カテゴリー";
	$lang_hremp_seleeocat 					= "EEOカテゴリー選択";
	$lang_hremp_joindate 					= "入社日";
	$lang_hremp_SelectJobTitle				= "職タイトル選択";
	$lang_hremp_EmpStatus					= "仕事ステータス";
	$lang_hremp_Workstation					= "ワークステーション";
	$lang_hremp_Subdivision					= "サブ・デビジョン";

 	$lang_hremp_dependents 					= "扶養家族";
	$lang_hremp_children 					= "子供";
	$lang_hremp_relationship 				= "関係";

	$lang_hremp_street1						= "道 1";
	$lang_hremp_street2 					= "道 2";

	$lang_hremp_hmtele 						= "家庭用電話番号";
	$lang_hremp_mobile 						= "携帯電話番号";
	$lang_hremp_worktele 					= "オフィス電話番号";
	$lang_hremp_city 						= "市・町";
	$lang_hremp_workemail 					= "オフィスe-メール";
	$lang_hremp_otheremail 					= "他e-メール";
	$lang_hremp_passport 					= "パスポート";
	$lang_hremp_visa 						= "ビザ";
	$lang_hremp_citizenship 				= "市民権";
	$lang_hremp_passvisano 					= "パスポート・ビザ番号";
	$lang_hremp_issueddate 					= "発行日";
	$lang_hremp_i9status 					= "I9 Status";
	$lang_hremp_dateofexp 					= "満期日";
	$lang_hremp_i9reviewdate 				= "I9 Review Date";
	$lang_hremp_path 						= "通路";
	$lang_hremp_filename 					= "ファイルネーム";
	$lang_hremp_size 						= "サイズ";
	$lang_hremp_type 						= "タイプ";
	$lang_hremp_name 						= "名前";
	$lang_hremp_InvalidPhone				= "無効の電話・ファックス番号";
	$lang_hremp_largefileignore				= "1M Max, any larger attachments will be ignored";
	$lang_hremp_PleaseSelectFile				= "ファイルを選んでください";
	$lang_hremp_ShowFile					= "ファイルを示す";
	$lang_hremp_Save					= "セーブ";
	$lang_hremp_Delete					= "削除";
	$lang_lang_uploadfailed					= "アップロード失敗";

	$lang_hremp_browse						= "ブラウズ";

	$lang_hremp_AreYouSureYouWantToDeleteThePhotograph = "確実に写真を削除したいですか";

	$lang_hremp_SelectAPhoto				= "写真の選択";
	$lang_hremp_PhotoMaxSize				= "1M Max";
	$lang_hremp_PhotoDimensions				= "寸法 100x120";

	$lang_hremp_IssedDateShouldBeBeforeExp	= "満期期以前、発行された期日があるべきです。";
	$lang_hremp_FromDateShouldBeBeforeToDate= "～まで日以前，～から日があるべきです";
	$lang_hremp_StaringDateShouldBeBeforeEnd= "始日以前，終日があるべきです";

	$lang_hremp_ContractExtensionStartDate	= "契約延長を始まる日";
	$lang_hremp_ContractExtensionEndDate	= "契約延長を終わる日";
	$lang_hremp_EmployeeContracts 			= "従業員契約";
	$lang_hremp_AssignedContracts			= "割り当てられた契約";

	$lang_hremp_ShowEmployeeContracts 			= "従業員契約を示す";
	$lang_hremp_HideEmployeeContracts 			= "従業員契約を隠す";

	$lang_hremp_SalaryShouldBeWithinMinAndMa= "給料は最高と最低の間";

	$lang_hremp_SelectCurrency				= "通貨選択";

	$lang_hremp_ContractExtensionId			= "契約延長ID";
	$lang_hremp_ContractStartDate			= "契約始まる日";
	$lang_hremp_ContractEndDate				= "契約終わる日";
    $lang_hremp_FieldShouldBeNumeric        = "フィールドは数でなければなりません";

    $lang_hremp_Language					= "言語";

 	$lang_hremplan_employeelanguageflu 		= "従業員の言語流暢";
	$lang_hremplan_fluency 					= "流暢";
	$lang_hremplan_ratinggarde 				= "流暢";
	$lang_hremplan_assignlanguage 			= "割り当てられた言語";

	$lang_hremplan_SelectLanguage			= "言語選択";
	$lang_hremplan_SelectFluency			= "流暢さ選択";
	$lang_hremplan_SelectRating 			= "格付け選択";

	$lang_hremp_PleaseSelectJobTitle= "職タイトルを選んでください{ここ }";

	$lang_hremp_ie_CurrentSupervisors 		= "例：現在の従業員の監督者";
	$lang_hremp_ie_CurrentSubordinates 		= "例：現在の従業員の下位";

	$lang_hremp_ie_PleaseSpecifyAtLeastOnePhoneNo = "少なくとも1つの電話番号を指定してください。";

 	$lang_hremplicenses_employeelicen 		= "従業員ライセンス";
	$lang_hremplicenses_assignlicenses 		= "割り当てたライセンス";
	$lang_hremplicenses_licentype 			= "ライセンスタイプ";
	$lang_hremplicenses_assignlicen 		= "割り当てたライセンス";

	$lang_hremplicenses_NoLicenseSelected	= "ライセンスを選ばれていません";
	$lang_hremplicenses_SelectLicenseType	= "ライセンスタイプ選択";

	$lang_hrempmemberships_NoMembershipTypeSelected	        = "メンバーシップタイプを選ばれていません";
	$lang_hrempmemberships_NoMembershipSelected	        = "メンバーを選ばれていません";
	$lang_hrempmemberships_NoSubscriptionOwnerSelected	= "購読オーナーは選ばれていません";

	$lang_hremp_SelectAtLEastOneAttachment = "Select at least one Attachment添付 to Delete";

 	$lang_hrEmpMain_ratinggarde 			= "Competency";
	$lang_hrEmpMain_assignlanguage 			= "Assigned Languages";
	$lang_hrEmpMain_Writing 				= "書く力";
	$lang_hrEmpMain_Speaking 				= "話す力";
	$lang_hrEmpMain_Reading 				= "読む力";
	$lang_hrEmpMain_Poor 					= "Poor";
	$lang_hrEmpMain_Basic 					= "基本的";
	$lang_hrEmpMain_Good 					= "Good";
	$lang_hrEmpMain_MotherTongue 			= "母語";
	$lang_hrEmpMain_Individual 				= "個人";
	$lang_hrEmpMain_employeemembership 		= "従業員メンバーシップ";
	$lang_hrEmpMain_subownership 			= "購読 オーナーシップ";
	$lang_hrEmpMain_subamount 				= "購読価値";
	$lang_hrEmpMain_subcomdate 				= "Subscription購読 Commence Date";
	$lang_hrEmpMain_subredate 				= "Subscription購読 Renwal Date";
	$lang_hrEmpMain_selmemtype 				= "Select Membership Type";
	$lang_hrEmpMain_selmemship 				= "Select Membership";
	$lang_hrEmpMain_selownership 			= "Select Ownership";
	$lang_hrEmpMain_assignmemship 			= "Assigned Memberships";
	$lang_hrEmpMain_paygrade 				= "Pay Grade";
	$lang_hrEmpMain_currency 				= "通貨";
	$lang_hrEmpMain_minpoint 				= "Minimum Salary";
	$lang_hrEmpMain_maxpoint 				= "Maximum Salary";
	$lang_hrEmpMain_bassalary 				= "Basic Salary";
	$lang_hrEmpMain_assignedsalary 			= "Assigned Salary";

 	$lang_hrEmpMain_heading 				= "Employee Report";
	$lang_hrEmpMain_supervisorsubordinator 	= "Supervisor監督者 / Subordinate";
	$lang_hrEmpMain_reportingmethod 		= "Reporting Method";
	$lang_hrEmpMain_subordinateinfomation 	= "Subordinates";
	$lang_hrEmpMain_supervisorinfomation 	= "Supervisors監督者";
	$lang_hrEmpMain_selecttype 				= "Select Method";
	$lang_hrEmpMain_Direct 					= "Direct";
	$lang_hrEmpMain_Indirect 				= "Indirect";
	$lang_hrEmpMain_employeeskill 			= "Employee Skills技能";
	$lang_hrEmpMain_yearofex 				= "Years of Experience";
	$lang_hrEmpMain_assignskills 			= "Assigned Skills技能";
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

	$lang_hrEmpMain_arrRepType_Supervisor = 'Supervisor監督者';
	$lang_hrEmpMain_arrRepType_Subordinate = 'Subordinate';

	$lang_hrEmpMain_arrRepMethod_Direct = 'Direct';
	$lang_hrEmpMain_arrRepMethod_Indirect = 'Indirect';

	$lang_hrEmpMain_SelectMethod = 'Select Method';

	$lang_hrEmpMain_SubscriptionAmountShouldBeNumeric = "Subscription 購読amount should be numeric";
	$lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue = "Membership subscription amount is empty. Do you want to continue";

	$lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate = "Commence Date should be before renewal date";

	$lang_hrEmpMain_membershiptype = "Membership Type";
	$lang_hrEmpMain_membership = "Membership";

 	$lang_jobtitle_heading 					= "Job : Job Title職タイトル";
	$lang_jobtitle_jobtitid 				= "Job Title職タイトル ID";
	$lang_jobtitle_jobtitname 				= "Job Title 職タイトルName";
	$lang_jobtitle_jobtitdesc 				= "Job Description";
	$lang_jobtitle_jobtitcomments 			= "Job Title 職タイトルComments";
	$lang_jobtitle_addpaygrade 				= "Add Pay Grade";
	$lang_jobtitle_emstatExpl 				= "Define the 仕事ステータスEmployment Status allowed for the 職タイトルJob Title";
	$lang_jobtitle_editpaygrade 			= "Edit Pay Grade";
	$lang_jobtitle_addempstat 				= "Add仕事ステータス Employment Status";
	$lang_jobtitle_editempstat 				= "Edit Employment Status";
	$lang_jobtitle_empstat 					= "Employment Status仕事ステータス";
	$lang_jobtitle_NameShouldBeSpecified    = "Job Title職タイトル Name should be specified";
	$lang_jobtitle_DescriptionShouldBeSpecified = "Job Description should be specified";
	$lang_jobtitle_PayGradeNotSelected      = "Pay Grade not selected";
    $lang_jobtitle_ShowingSavedValues       = "Currently showing the values saved in session when you last edited this record.";
    $lang_jobtitle_NoSelection              = "No Selection!";
    $lang_jobtitle_PleaseSelectEmploymentStatus = "Please select employement status";
    $lang_jobtitle_EnterEmploymentStatus    = "Please enter a value for 仕事ステータスEmployment Status.";

 	$lang_languageinformation_heading 		= "Skills技能 : Language";

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

 	$lang_membershiptypes_heading 			= "Memeberships : Membership Type";
 	$lang_membershiptypes_NameShouldBeSpecified = "Membership Type name should be specified";

 	$lang_nationalityinformation_heading 	= "Nationality & Race : Nationality国籍と人種";
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
	$lang_salarygrades_stepSal 				= "Step Increase";
	$lang_salarygrades_selectcurrency		= "Currency通貨 field should be selected";
	$lang_salarygrades_minSalNumeric		= "Minimum salary should be numeric";
	$lang_salarygrades_maxSalNumeric		= "Maximum salary should be numeric";
	$lang_salarygrades_stepSalNumeric		= "Step salary should be numeric";
    $lang_salarygrades_minGreaterThanMax	= "Minimum Salary > Maximum Salary !";
	$lang_salarygrades_stepPlusMinGreaterThanMax = "Step Salary plus Minimum Salary should be less than Maximum salary";
	$lang_salarygrades_stepGreaterThanMax = "Step Salary should be less than Maximum salary";
	$lang_salarygrades_NameCannotBeBlank = "Pay Grade Name cannot be blank!";

	$lang_salcurrdet_heading 				= "Currency通貨 Assignment to Salary Grade";
	$lang_salcurrdet_assigncurr 			= "Assigned Currency";

 	$lang_satutoryinfo_heading 				= "Statutory Type : Nexus Information";

 	$lang_view_EmploymentStatusID 			= "Employment Status 仕事ステータスID";
	$lang_view_EmploymentStatusName 		= "Employment Status仕事ステータス Name";
	$lang_view_message 						= "Deletion may affect 職タイトルJob Titles";
	$lang_view_message1 					= "Deletion may affect Pay Grade of Employees in PIM";
	$lang_view_SkillName 					= "Skill Name";
	$lang_view_SkillID 						= "Skill ID";
	$lang_view_message3 					= "Deletion might affect employee information";
	$lang_view_LocationName 				= "Location Name";
	$lang_view_message4 					= "Deletion might affect Company Hierarchy. If the Location has associations deletion may fail";
	$lang_view_CurrencyName 				= "Currency通貨 Name";
	$lang_view_CurrencyTypes 				= "Currency通貨 Types";
	$lang_view_message5 					= "Deletion might affect Salary Currency Detail";
	$lang_view_CompanyHierarchy 			= "Company Hierarchy";
	$lang_view_CompanyHierarchyName 		= "Company Hierarchy Name";
	$lang_view_message6 					= "Deletion might affect employee information";
	$lang_view_QualificationType 			= "Qualification Type";
	$lang_view_QualificationName 			= "Qualification Name";
	$lang_view_message9 					= "Deletion might affect Qualifications, Designation Qualification, Employee Qualification";
	$lang_view_RatingMethod 				= "格付け Method";
	$lang_view_RatingMethodName 			= "Rating 格付けMethod Name";
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
	$lang_view_NationalityName 				= "Nationality Name国籍と人種";
	$lang_view_message16 					= "Deletion might affect employee information";
	$lang_view_CountryID 					= "Country ID";
	$lang_view_CountryName 					= "Country Name";
	$lang_view_message17 					= "Deletion might affect employee information";
	$lang_view_HierarchyDefinition 			= "Hierarchy Definition";
	$lang_view_HierarchyDefinitionName 		= "Hierarchy Definition Name";
	$lang_view_message18 					= "Deletion might affect Company Hierachy";
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
	$lang_view_message24 					= "Deletion might affect employee information, 職タイトルJob Titles";
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
	$lang_MailTypes_Sendmailer = "Send Mail";
	$lang_MailTypes_Smtp = "SMTP";
	$lang_SendmailPath = "Path to Sendmail";
	$lang_SmtpHost = "SMTP Host";
	$lang_SmtpPort = "SMTP Port";
	$lang_SmtpUser = "SMTP User";
	$lang_SmtpPassword = "SMTP Password";
	$lang_Error_Invalid_Port = "Invalid SMTP Port";

	//Projects
	$lang_view_Project_Heading = "Project";
	$lang_view_ProjectId = "Project Id";
	$lang_view_ProjectName = "Project Name";

	// PIM tab labels
	$lang_pim_tabs_Personal = "Personal";
	$lang_pim_tabs_Contact = "Contact";
	$lang_pim_tabs_EmergencyContacts = "Emergency Contact(s)";
	$lang_pim_tabs_Dependents = "Dependents扶養家族";
	$lang_pim_tabs_Immigration = "Immigration";
	$lang_pim_tabs_Job = "Job";
	$lang_pim_tabs_Payments = "Payments";
	$lang_pim_tabs_ReportTo = "Report-to";
	$lang_pim_tabs_WorkExperience = "Work experience";
	$lang_pim_tabs_Education = "Education";
	$lang_rep_Languages = "Languages";
	$lang_pim_tabs_Skills = "Skills技能";
	$lang_pim_tabs_Languages = "Languages";
	$lang_pim_tabs_License = "License";
	$lang_pim_tabs_Membership = "Membership";
	$lang_pim_tabs_Attachments = "Attachments";

	// Report module
	$lang_rep_SelectionCriteria = "Selection Criteria";
	$lang_rep_EmpNo = "Emp No";
	$lang_rep_Employee = "Employee";
	$lang_rep_AgeGroup = "Age Group";
	$lang_rep_PayGrade = "Pay Grade";
	$lang_rep_Education = "Education";
	$lang_rep_EmploymentStatus = "Employment Status仕事ステータス";
	$lang_rep_ServicePeriod = "Service Period";
	$lang_rep_JoinedDate = "Joined Date";
	$lang_rep_JobTitle = "職タイトルJob Title";
	$lang_rep_Language = "Language";
	$lang_rep_Skill = "Skill";

	$lang_rep_LastName = "Last Name";
	$lang_rep_FirstName = "First Name";
	$lang_rep_Address = "Address";
	$lang_rep_TelNo = "Tel No";
	$lang_rep_DateOfBirth = "Date Of Birth";
	$lang_rep_JoinDate = "入社日";
	$lang_rep_Qualification = "Qualification";
	$lang_rep_EmployeeStates = "Employee States";
	$lang_rep_JoinedDate = "入社日";

	$lang_rep_SelectPayGrade = "Select Salary Grade";
	$lang_rep_SelectEducation = "Select Education Type";
	$lang_rep_SelectEmploymentType = "Select Employment Type";
	$lang_rep_SelectComparison = "Select Comparison";
	$lang_rep_SelectJobTitle = "Select職タイトル Job Title";
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
	$lang_rep_DateOfBirth = "生年月日";
	$lang_rep_Skills = "Skills技能";
	$lang_rep_SalaryGrade = "Salary Grade";
	$lang_rep_EmployeeStatus = "Employee Status";
	$lang_rep_JoinedDate = "入社日";
	$lang_rep_SubDivision = "サブ・デビジョン";
	$lang_rep_JobTitle = "Job Title職タイトル";
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

	// Skills
	$lang_Admin_Skill_Errors_NameCannotBeBlank = "Name cannot be a blank value!";


	// Email Notification Subscription
	$lang_Admin_EMX_MailConfiguration = "Mail Configuration";
	$lang_Admin_SubscribeToMailNotifications = "Subscribe to E-mail Notifications";
	$lang_Admin_ENS_LeaveApplications = "Leave Applications";
	$lang_Admin_ENS_LeaveApprovals = "Leave Approvals";
	$lang_Admin_ENS_LeaveCancellations = "Leave Cancellations";
	$lang_Admin_ENS_LeaveRejections = "Leave Rejections";

	//Users
	$lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong = "UserName should be atleast five characters long";
	$lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong = "Password should be atleast four characters long";
	$lang_Admin_Users_ErrorsPasswordMismatch = "Password Mismatch";
	$lang_Admin_Users_Errors_EmployeeIdShouldBeDefined = "Employee ID should be defined";
	$lang_Admin_Users_Errors_FieldShouldBeSelected = "Field should be selected";
	$lang_Admin_Users_Errors_ViewShouldBeSelected = "View should be selected";
	$lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "Passwords Are Not Matching.Retype Your New Password";
	$lang_Admin_Users_WrongPassword = "Mismatch with your old password";

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

	$lang_Admin_Change_Password_Errors_EnterYourOldPassword = "古いパスワードの入力";
	$lang_Admin_Change_Password_Errors_EnterYourNewPassword = "新しいパスワードの入力";
	$lang_Admin_Change_Password_Errors_RetypeYourNewPassword = "新しいパスワードの確認";
	$lang_Admin_Change_Password_Errors_PasswordsAreNotMatchingRetypeYourNewPassword = "パスワードは、合っていません. 新しいパスワードを再タイプしてください";
	$lang_Admin_Change_Password_Errors_YourOldNewPasswordsAreEqual = "古い新しいパスワードが合う";

	$lang_Admin_Project = "プロジェクト";
	$lang_Admin_Project_CutomerName = "顧客名前";
	$lang_Admin_Project_SelectCutomer = "顧客選択";
	$lang_Admin_Project_Administrators = "プロジェクト管理者";
	$lang_Admin_Project_EmployeeName = "従業員名";
	$lang_Admin_Project_EmployeeAlreadyAnAdmin = "従業員はすでにプロジェクト管理者です";

	$lang_Admin_Project_Error_PleaseDSpecifyTheProjectId = "プロジェクトIDの定義";
	$lang_Admin_Project_Error_PleaseSelectACustomer = "顧客を選んでください";
	$lang_Admin_Project_Error_PleaseSpecifyTheName = "名前を選んでください";

	$lang_Admin_Customer_PleaseSpecifyTheCustormerId = "顧客Idを指定してください";
	$lang_Admin_Customer_Error_PleaseSpecifyTheName = "名前を指定してください";

	$lang_Admin_ProjectActivities = "プロジェクトタスク";
	$lang_Admin_Activity = "タスク";
	$lang_Admin_Project_Activity_ClickOnActivityToEdit = "改名するのに既存のタスクをクリックしてください";
	$lang_Admin_Project_Activity_Error_PleaseSpecifyTheActivityName = "タスク名を指定してください";
	$lang_Admin_Project_Error_NoProjects = "プロジェクトはありません";
	$lang_Admin_Project_NoActivitiesDefined = "タスクは定義していません";
	$lang_Admin_Project_Activity_Error_NameAlreadyDefined = "そのタスク名が、　このプロジェクトにすでに　あります.違った名選んでください";

	//timesheet
	$lang_Time_Module_Title = "オレンジHRM -時間と出席モジュール";
	$lang_Time_Timesheet_TimesheetForViewTitle = "#startDateから始める#periodName のタイムシート";
	$lang_Time_Timesheet_TimesheetForEditTitle = "#startDateから始める#periodName のタイムシートの編集";

	$lang_Time_Timesheet_TimesheetNameForViewTitle = "#name の#startDateから始める#periodName のタイムシート";
	$lang_Time_Timesheet_TimesheetNameForEditTitle = "#name の#startDateから始める#periodName のタイムシートの編集";

	$lang_Time_Timesheet_Status = "ステータス: #status";

	$lang_Time_Timesheet_Status_NotSubmitted = "提出していません";
	$lang_Time_Timesheet_Status_Submitted = "提出しました";
	$lang_Time_Timesheet_Status_Approved = "承認";
	$lang_Time_Timesheet_Status_Rejected = "拒絶";

	$lang_Time_Timesheet_Customer = "顧客";
	$lang_Time_Timesheet_ProjectActivity = "プロジェクト・タスク";
	$lang_Time_Timesheet_Project = "プロジェクト";
	$lang_Time_Timesheet_Activity = "タスク";

	$lang_Time_Timesheet_Total = "合計";

	$lang_Time_Errors_SUBMIT_SUCCESS = "提出成功";
	$lang_Time_Errors_SUBMIT_FAILURE = "提出失敗";
	$lang_Time_Errors_UPDATE_SUCCESS = "アップデート成功";
	$lang_Time_Errors_UPDATE_FAILURE = "アップデート失敗";
	$lang_Time_Errors_CANCEL_SUCCESS = "キャンセル成功";
	$lang_Time_Errors_CANCEL_FAILURE = "キャンセル失敗";
	$lang_Time_Errors_APPROVE_SUCCESS = "承認成功";
	$lang_Time_Errors_APPROVE_FAILURE = "承認失敗";
	$lang_Time_Errors_REJECT_SUCCESS = "拒絶成功";
	$lang_Time_Errors_REJECT_FAILURE = "拒絶失敗";
	$lang_Time_Errors_DELETE_SUCCESS = "削除成功";
	$lang_Time_Errors_DELETE_FAILURE = "削除失敗";
	$lang_Time_Errors_UNAUTHORIZED_FAILURE = "権限のない動作";

	$lang_Time_Errors_NO_TIMESHEET_FAILURE = "タイムシートはありません";
	$lang_Time_Errors_INVALID_TIME_FAILURE = "無効なタイムシート";
	$lang_Time_Errors_EVENT_START_AFTER_END_ERROR = "エベントの始時間と終時間.";
	$lang_Time_Errors_INVALID_TIMESHEET_PERIOD_ERROR = "無効なタイムシート期間";

	$lang_Time_Errors_EncounteredTheFollowingProblems = "下の問題になりました。. エラーがある列は、ハイライトされています";
	$lang_Time_Errors_ReportedDateNotSpecified_ERROR = "出頭した日を定義していません";
	$lang_Time_Errors_ProjectNotSpecified_ERROR = "プロジェクトを指定していまん";
	$lang_Time_Errors_CustomerNotSpecified_ERROR = "顧客を指定しています";
	$lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified_ERROR = "無効なタイム、ゼロか否定的間隔の指定";
	$lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval_ERROR = "期間と間隔が指定できない";
	$lang_Time_Errors_InvalidReportedDate_ERROR = "出頭した日が無効なかちです ";
	$lang_Time_Errors_InvalidDuration_ERROR = "期間は有効で明確な数字でなければなりません";
	$lang_Time_Errors_InvalidStartTime_ERROR = "始まる時間は無効です";
	$lang_Time_Errors_InvalidEndTime_ERROR = "終わる時間は無効です";
	$lang_Time_Errors_EVENT_OUTSIDE_PERIOD_FAILURE = "エベントタイムはタイムシート期間と合歯なければなりません";
	$lang_Time_Errors_NoValidDurationOrInterval_ERROR = "間隔のために有効な期間が指定していません";
	$lang_Time_Errors_ZeroOrNegativeIntervalSpecified_ERROR = "ゼロか否定的間隔の指定";
	$lang_Time_Errors_NO_EVENTS_WARNING = "セーブするエベントはありません";

	$lang_Time_Timesheet_StartTime = "始時間";
	$lang_Time_Timesheet_EndTime = "終時間";
	$lang_Time_Timesheet_ReportedDate = "出頭日";
	$lang_Time_Timesheet_Duration = "期間";
	$lang_Time_Timesheet_DurationUnits = "(時)";
	$lang_Time_Timesheet_Decription = "記述";
	$lang_Time_Timesheet_NoCustomers = "顧客はいません";
	$lang_Time_Timesheet_NoProjects = "プロジェクトはありません";

	$lang_Time_Select_Employee_Title = "従業員選択";

	$lang_Time_Select_Employee_SubmittedTimesheetsPendingSupervisorApproval = "提出したタイムシートは監督者の承認があるまで";
	$lang_Time_Select_Employee_WeekStartingDate = "週始 #date";

	$lang_Time_Select_Employee_TimesheetPeriod = "タイムシート期間";

	$lang_Time_Errors_PleaseAddAComment = "コメントを書いてください";

	$lang_mtview_ADD_SUCCESS 				= "加成功";
	$lang_mtview_UPDATE_SUCCESS 			= "アップデート成功";
	$lang_mtview_DELETE_SUCCESS 			= "削除成功";
	$lang_mtview_ADD_FAILURE 				= "加失敗";
	$lang_mtview_UPDATE_FAILURE 			= "アップデート失敗";
	$lang_mtview_DELETE_FAILURE 			= "削除失敗";

	// 2.2 Time module
	$lang_Time_PunchInPunchOutTitle = "タイムカードを押して入る・出る";
	$lang_Time_PunchIn = "入る";
	$lang_Time_PunchOut = "出る";

	$lang_Time_LastPunchIn = "最初にタイムカードを押して入った時間:";
	$lang_Time_LastPunchOut = "最後にタイムカードを押して出た時間:";

	$lang_Time_Errors_InvalidDateOrTime = "無効な日か時間";
	$lang_Time_Errors_CurrentPunchTimeBeforeLastPunchTime = "Punch time cannot be before last time punched";
	$lang_Time_Errors_ZeroOrNegativeDurationTimeEventsAreNotAllowed = "ゼロか否定的な期間は許していません";
	$lang_Time_Errors_ActivityNotSpecified_ERROR = "タスクは指定されていません";

	$lang_Menu_Time_PunchInOut = "タイムカードを押して入る・出る";

	$lang_Time_Timesheet_SelectProject = "プロジェクトを選んでください";

	$lang_Time_Timesheet_UnfinishedActivitiesTitle = "未完にタスク";
	$lang_Time_SubmitTimeEventTitle = "タイムイベントの提出";

	$lang_Time_Timesheet_DateReportedFor = "出頭日";

	$lang_Time_UnfinishedActivitiesTitle = "未完にタスク";
	$lang_Time_NewEvent = "新イベント";
	$lang_Time_Complete = "完成";

	$lang_Time_DurationFormat = "時:分か0.00時";
	$lang_Time_InsertTime="タイム加入";

	$lang_Time_Errors_NO_RECORDS_CHANGED_WARNING = "セーブしたいチェーンジはあリません";
	$lang_Time_Errors_EXCEPTION_THROWN_WARNING = "問題が出てきました";

	$lang_Menu_Time_ProjectTime = "プロジェクトタイム";

	$lang_Time_DefineTimesheetPeriodTitle = "タイムシート期間の定義";

	$lang_Time_FirstDayOfWeek = "週の初日";

	$lang_Menu_Time_DefineTimesheetPeriod = "タイムシート期間の定義";

	$lang_Time_EmployeeTimeReportTitle = "従業員タイムレポート";

	$lang_Time_Common_FromDate = $lang_Leave_Common_FromDate;
	$lang_Time_Common_ToDate = $lang_Leave_Common_ToDate;
	$lang_Time_Common_All = $lang_Leave_Common_All;

	$lang_Time_Errors_EmployeeNotSpecified = "従業員は定義していません";

	$lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified = "無効な日、ゼロか否定的範囲が指定しました";

	$lang_Menu_Time_EmployeeReports = "従業員のレポート";

	$lang_Time_SelectTimesheetsTitle = "タイムシート選択";
	$lang_Time_Division = $lang_compstruct_Division;
	$lang_Time_Supervisor = $lang_empview_Supervisor;
	$lang_Time_EmploymentStatus = $lang_empview_EmploymentStatus;

	$lang_Time_NoEmploymentStatusDefined = "職業ステータスは定義していません";

	$lang_Time_PrintTimesheetsTitle = "タイムシートプリント";
	$lang_Time_Print = "プリント";

	$lang_Common_Loading = "Loading";

	$lang_Menu_Time_PrintTimesheets = "タイムシートプリント";

	$lang_Menu_Time_ProjectReports = "プロジェクトレポート";
	$lang_Time_ProjectReportTitle = "プロジェクトレポート";
	$lang_Time_Report_To = "まで";
	$lang_Time_Report_From = "から";
	$lang_Time_TimeInHours = "時間 (時)";
	$lang_Time_ActivityReportTitle = "タスク情報";
	$lang_Time_Activity_Report_EmployeeName = "従業員名";
	$lang_Time_Activity_Report_View = "View";
	$lang_Time_Activity_Report_TotalTime = "合計";
	$lang_Time_Activity_Report_NoEvents = "エベントはありません";

	$lang_Time_Errors_PROJECT_NOT_FOUND_FAILURE = "プロジェクトはありません";
	$lang_Time_Errors_ACTIVITY_NOT_FOUND_FAILURE = "タスクはありません";

	$lang_Common_Yes = "はい";
	$lang_Common_No = "いいえ";

	$lang_Leave_DoYouWantToCancelTheLeaveYouJustAssigned = "割り当てた休暇をキャンセルしたいですか";
	$lang_Leave_PleaseProvideAReason = "理由をあげてください";

	$lang_Time_Errors_OVERLAPPING_TIME_PERIOD_FAILURE = "エベントタイムは重複";

	$lang_view_DUPLICATE_NAME_FAILURE = "同名は既に使われています";

	$lang_Leave_CopyLeaveQuotaFromLastYear = "去年の休暇持ち分のコピー";

	$lang_Leave_LEAVE_QUOTA_COPY_SUCCESS = "休暇持ち分コピーは成功";
	$lang_Leave_LEAVE_QUOTA_COPY_FAILURE = "休暇持ち分コピーは失敗";

	// login page
	$lang_login_title = "オレンジHRM - HR 管理の最新のレベル";
	$lang_login_UserNameNotGiven = "ユーサーネームはありません!";
	$lang_login_PasswordNotGiven = "パスワードはあリません!";
	$lang_login_NeedJavascript = "You need a JavaScript enabled ブラウズBrowser. Ex. ";
	$lang_login_MozillaFirefox = "Mozilla Firefox";
	$lang_login_YourSessionExpired = "Your session expired because you were inactive. Please re-login.";
	$lang_login_LoginName = "ログインネーム";
	$lang_login_Password = "パスワード";
	$lang_login_Login = "ログイン";
	$lang_login_Clear = "クリア";
	$lang_login_InvalidLogin = "無効なログイン";
	$lang_login_UserDisabled = "User ";
	$lang_login_NoEmployeeAssigned = "ESSユーサーアカウントには従業員が割り当てていません";
	$lang_login_OrangeHRMDescription = " comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vastrange of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.";

	$lang_Leave_Common_FromTime = "時　から";
	$lang_Leave_Common_ToTime = "時　まで";
	$lang_Leave_Common_TotalHours = "合計時";
	$lang_Leave_Error_ToTimeBeforeFromTime = "To time before from time";
	$lang_Leave_Error_TotalTimeMoreThanADay = "Total time is greater than the shift length";
	$lang_Leave_Error_PleaseSpecifyEitherTotalTimeOrTheTimePeriod = "Please specify either total time or the time period";

	$lang_Leave_Duration = "期間(時)";
	$lang_Common_Hours = "時";
	$lang_Common_Days = "日";

	$lang_Time_WorkShifts = "仕事交替制";
	$lang_Time_ShiftName = "交替名";
	$lang_Time_HoursPerDay = "日につき時間";
	$lang_Time_AvailableEmployees = "在従業員";
	$lang_Time_AssignedEmployees = "割り当てられた従業員";
	$lang_Time_AssignEmployeesTitle = "仕事交替編集";

	$lang_Time_Error_SpecifyWorkShiftName = "交替の指定";
	$lang_Time_Error_SpecifyHoursPerDay = "日につき時間の指定";
	$lang_Time_Error_HoursPerDayShouldBePositiveNumber = "日につき時間は、正数でなければなりません";
	$lang_Time_Error_NoEmployeeSelected = "従業員を選択していません";

	$lang_Time_Errors_INVALID_WORK_SHIFT_FAILURE = "仕事交替記述の問題";
	$lang_Time_Errors_NO_RECORDS_SELECTED_FAILURE = "削除されるのにレコードは選ばれません";
	$lang_Time_Errors_UNKNOWN_ERROR_FAILURE = "エラー";
	$lang_Time_Errors_INVALID_ID_FAILURE = "無効なID";

	$lang_Menu_Time_WorkShifts = "仕事交替";
?>
