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
 */

namespace OrangeHRM\Installer\Migration\V5_0_0;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    protected ?TranslationHelper $translationHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getSchemaHelper()->addColumn(
            'ohrm_screen',
            'menu_configurator',
            Types::STRING,
            ['Length' => 255, 'Default' => null, 'Notnull' => false]
        );
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $this->updateMenuConfigurator('attendance', null, 'OrangeHRM\\Attendance\\Menu\\AttendanceMenuConfigurator');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->getSchemaHelper()->dropColumn('ohrm_leave', 'comments');
        $this->getSchemaHelper()->dropColumn('ohrm_leave_request', 'comments');
        $this->getSchemaHelper()->dropColumn('ohrm_menu_item', 'url_extras');
        $this->getSchemaManager()->dropTable('ohrm_data_group_screen');

        $this->addTimezoneColumnsToAttendanceRecord();

        $qb = $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.module_id', ':moduleId')
            ->setParameter('moduleId', $this->getDataGroupHelper()->getModuleIdByName('time'));
        $qb->where($qb->expr()->in('screen.action_url', ':actionUrls'))
            ->setParameter(
                'actionUrls',
                ['viewCustomers', 'viewProjects', 'addCustomer', 'saveProject'],
                Connection::PARAM_STR_ARRAY
            )->executeQuery();

        $this->createQueryBuilder()
            ->update('ohrm_user_role_data_group', 'dataGroupPermission')
            ->set('dataGroupPermission.can_update', ':canUpdate')
            ->setParameter('canUpdate', false, ParameterType::BOOLEAN)
            ->andWhere('dataGroupPermission.data_group_id = :dataGroupId')
            ->setParameter('dataGroupId', $this->getDataGroupHelper()->getDataGroupIdByName('time_projects'))
            ->andWhere('dataGroupPermission.user_role_id = :userRoleId')
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName('ProjectAdmin'))
            ->executeQuery();
        $this->getDataGroupHelper()->addDataGroupPermissions(
            'attendance_summary',
            'Admin',
            true,
            false,
            false,
            false,
            true
        );

        $this->updateMenuConfigurator('admin', 'saveJobTitle', 'OrangeHRM\\Admin\\Menu\\JobTitleMenuConfigurator');
        $this->updateMenuConfigurator('admin', 'saveLocation', 'OrangeHRM\\Admin\\Menu\\LocationMenuConfigurator');
        $this->updateMenuConfigurator('admin', 'payGrade', 'OrangeHRM\\Admin\\Menu\\PayGradeConfigurator');
        $this->updateMenuConfigurator('admin', 'saveSystemUser', 'OrangeHRM\\Admin\\Menu\\UserMenuConfigurator');

        $this->updateMenuConfigurator('pim', 'viewMyDetails', 'OrangeHRM\\Pim\\Menu\\MyInfoMenuConfigurator');
        $this->updateMenuConfigurator(
            'pim',
            'definePredefinedReport',
            'OrangeHRM\\Pim\\Menu\\PimReportMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'pim',
            'displayPredefinedReport',
            'OrangeHRM\\Pim\\Menu\\PimReportMenuConfigurator'
        );

        $this->updateMenuConfigurator('leave', 'defineLeaveType', 'OrangeHRM\\Leave\\Menu\\LeaveTypeMenuConfigurator');
        $this->updateMenuConfigurator(
            'leave',
            'viewLeaveRequest',
            'OrangeHRM\\Leave\\Menu\\DetailedLeaveRequestMenuConfigurator'
        );

        $this->updateMenuConfigurator('time', 'addCustomer', 'OrangeHRM\\Time\\Menu\\CustomerMenuConfigurator');
        $this->updateMenuConfigurator('time', 'saveProject', 'OrangeHRM\\Time\\Menu\\ProjectMenuConfigurator');
        $this->updateMenuConfigurator(
            'time',
            'displayProjectActivityDetailsReport',
            'OrangeHRM\\Time\\Menu\\DetailedProjectActivityReportMenuConfigurator'
        );

        $this->configureLeaveMenuItemsIfLeavePeriodDefined();
        $this->configureTimeMenuItemsIfTimesheetStartDateDefined();

        $qb = $this->createQueryBuilder()
            ->update('ohrm_module_default_page', 'defaultPage')
            ->set('defaultPage.action', ':screenUrl')
            ->setParameter('screenUrl', 'leave/viewLeaveList')
            ->andWhere('defaultPage.module_id = :moduleId')
            ->setParameter('moduleId', $this->getDataGroupHelper()->getModuleIdByName('leave'))
            ->andWhere('defaultPage.action = :action')
            ->setParameter('action', 'leave/viewLeaveList/reset/1');
        $qb->andWhere($qb->expr()->in('defaultPage.user_role_id', ':userRoleIds'))
            ->setParameter(
                'userRoleIds',
                [
                    $this->getDataGroupHelper()->getUserRoleIdByName('Admin'),
                    $this->getDataGroupHelper()->getUserRoleIdByName('Supervisor')
                ],
                Connection::PARAM_INT_ARRAY
            )->executeQuery();

        $this->getSchemaHelper()->changeColumn(
            'ohrm_timesheet_action_log',
            'performed_by',
            ['Default' => null, 'Notnull' => false]
        );
        $this->getSchemaHelper()->dropForeignKeys('ohrm_timesheet_action_log', ['ohrm_timesheet_action_log_ibfk_1']);
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['performed_by'],
            'ohrm_user',
            ['id'],
            'ohrm_timesheet_action_log_performed_by_id',
            ['onDelete' => 'SET NULL', 'onUpdate' => 'RESTRICT']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_timesheet_action_log', $foreignKeyConstraint);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_i18n_lang_string', ['sourceId']);
        $this->getSchemaHelper()->dropColumn('ohrm_i18n_lang_string', 'source_id');
        $this->getSchemaManager()->dropTable('ohrm_i18n_source');
        $this->getSchemaHelper()->changeColumn(
            'ohrm_i18n_lang_string',
            'unit_id',
            ['Type' => Type::getType(Types::STRING), 'Length' => 255]
        );

        $this->getDataGroupHelper()->addDataGroupPermissions(
            'time_employee_reports',
            'Admin',
            true,
            false,
            false,
            false,
            true
        );
        $this->getDataGroupHelper()->addDataGroupPermissions(
            'time_employee_reports',
            'Supervisor',
            false,
            false,
            false,
            false,
            true
        );

        $this->getSchemaHelper()->addColumn(
            'ohrm_module',
            'display_name',
            Types::STRING,
            ['Length' => 120]
        );
        $this->updateModuleDisplayNames();
        $this->insertI18nGroups();

        $groups = ['admin', 'general', 'pim', 'leave', 'time', 'attendance', 'maintenance', 'help', 'auth'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->deleteNonCustomizedLangStrings($group);
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $langCodes = [
            'bg_BG',
            'da_DK',
            'de',
            'en_US',
            'es',
            'es_AR',
            'es_BZ',
            'es_CR',
            'es_ES',
            'fr',
            'fr_FR',
            'id_ID',
            'ja_JP',
            'nl',
            'om_ET',
            'th_TH',
            'vi_VN',
            'zh_Hans_CN',
            'zh_Hant_TW'
        ];
        foreach ($langCodes as $langCode) {
            $this->getTranslationHelper()->addTranslations($langCode);
        }

        $this->createQueryBuilder()
            ->update('ohrm_project ', 'project')
            ->set('project.description', ':description')
            ->where('project.description = :emptyString')
            ->setParameter('description', null)
            ->setParameter('emptyString', "")
            ->executeQuery();

        $oAuthClientScreenId = $this->getDataGroupHelper()->getScreenIdByModuleAndUrl(
            $this->getDataGroupHelper()->getModuleIdByName('admin'),
            'registerOAuthClient'
        );
        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_user_role_screen')
            ->values(
                [
                    'screen_id' => ':screenId',
                    'user_role_id' => ':userRoleId',
                    'can_read' => ':read',
                    'can_create' => ':create',
                    'can_update' => ':update',
                    'can_delete' => ':delete',
                ]
            )
            ->setParameter('screenId', $oAuthClientScreenId)
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName('Admin'))
            ->setParameter('read', true, ParameterType::BOOLEAN)
            ->setParameter('create', true, ParameterType::BOOLEAN)
            ->setParameter('update', true, ParameterType::BOOLEAN)
            ->setParameter('delete', true, ParameterType::BOOLEAN)
            ->executeQuery();

        $q = $this->createQueryBuilder();
        $q->select('customFields.extra_data', 'customFields.field_num')
            ->from('hs_hr_custom_fields ', 'customFields')
            ->where('customFields.type = :type')
            ->setParameter('type', 1);
        $results = $q->executeQuery()
            ->fetchAllAssociative();
        foreach ($results as $result) {
            $this->createQueryBuilder()
                ->update('hs_hr_custom_fields ', 'customFields')
                ->set('customFields.extra_data', ':newExtraData')
                ->where('customFields.field_num = :fieldNum')
                ->setParameter('newExtraData', str_replace(', ', ',', $result['extra_data']))
                ->setParameter('fieldNum', $result['field_num'])
                ->executeQuery();
        }

        $q = $this->createQueryBuilder();
        $q->update('hs_hr_employee ', 'employee')
            ->set('employee.emp_firstname ', ':firstName')
            ->set('employee.emp_lastname ', ':lastName')
            ->andWhere($q->expr()->isNotNull('employee.purged_at'))
            ->setParameter('firstName', 'Purged')
            ->setParameter('lastName', 'Employee')
            ->executeQuery();

        $this->cleanUniqueIdTable();

        $q = $this->createQueryBuilder()
            ->select('filter_field.filter_field_id')
            ->from('ohrm_filter_field', 'filter_field')
            ->where('filter_field.name = :filterField')
            ->setParameter('filterField', 'include');

        $filterFieldId = $q->executeQuery()->fetchOne();
        $this->createQueryBuilder()
            ->update('ohrm_selected_filter_field', 'selected_filter_field')
            ->set('selected_filter_field.filter_field_order', ':newFilterFieldOrder')
            ->where('selected_filter_field.filter_field_order = :filterFieldOrder ')
            ->andWhere('selected_filter_field.filter_field_id = :filterFieldId')
            ->setParameter('newFilterFieldOrder', 1)
            ->setParameter('filterFieldOrder', 0)
            ->setParameter('filterFieldId', $filterFieldId)
            ->executeQuery();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.0';
    }

    /**
     * Enable all menu items under `Time` if timesheet start date defined, disable menu items if not
     */
    private function configureTimeMenuItemsIfTimesheetStartDateDefined(): void
    {
        $this->configureMenuItems('timesheet_period_set', 'Time');
    }

    /**
     * Enable all menu items under `Leave` if leave period defined, disable menu items if not
     */
    private function configureLeaveMenuItemsIfLeavePeriodDefined(): void
    {
        $this->configureMenuItems('leave_period_defined', 'Leave');
    }

    /**
     * @param string $configName
     * @param string $parentMenuTitle
     */
    private function configureMenuItems(string $configName, string $parentMenuTitle): void
    {
        $defined = $this->getConnection()->createQueryBuilder()
                ->select('config.value')
                ->from('hs_hr_config', 'config')
                ->where('config.name = :configName')
                ->setParameter('configName', $configName)
                ->setMaxResults(1)
                ->fetchOne() == "Yes";
        $parentMenuItemId = $this->getConnection()->createQueryBuilder()
            ->select('menuItem.id')
            ->from('ohrm_menu_item', 'menuItem')
            ->where('menuItem.menu_title = :menuTitle')
            ->setParameter('menuTitle', $parentMenuTitle)
            ->setMaxResults(1)
            ->fetchOne();
        $this->createQueryBuilder()
            ->update('ohrm_menu_item', 'menuItem')
            ->set('menuItem.status', ':status')
            ->setParameter('status', $defined, ParameterType::BOOLEAN)
            ->andWhere('menuItem.parent_id = :parentId')
            ->setParameter('parentId', $parentMenuItemId)
            ->executeQuery();
    }

    /**
     * Add `punch_in_timezone_name`, `punch_out_timezone_name` columns and set default values
     */
    private function addTimezoneColumnsToAttendanceRecord(): void
    {
        $this->getSchemaHelper()->addColumn(
            'ohrm_attendance_record',
            'punch_in_timezone_name',
            Types::STRING,
            ['Length' => 100, 'Default' => null, 'Notnull' => false]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_attendance_record',
            'punch_out_timezone_name',
            Types::STRING,
            ['Length' => 100, 'Default' => null, 'Notnull' => false]
        );
        $attendanceHelper = new AttendanceHelper($this->getConnection());
        foreach (AttendanceHelper::TIMEZONE_MAP as $timezone => $offset) {
            $attendanceHelper->updatePunchInTimezoneOffset($offset, $timezone);
            $attendanceHelper->updatePunchOutTimezoneOffset($offset, $timezone);
        }

        $this->hideAddonMenuItems();
    }

    /**
     * @param string $module
     * @param string|null $screenUrl
     * @param string $menuConfiguratorClassName
     */
    private function updateMenuConfigurator(string $module, ?string $screenUrl, string $menuConfiguratorClassName): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.menu_configurator', ':menuConfiguratorClassName')
            ->setParameter('menuConfiguratorClassName', $menuConfiguratorClassName)
            ->andWhere('screen.module_id = :moduleId')
            ->setParameter('moduleId', $this->getDataGroupHelper()->getModuleIdByName($module));
        if (!is_null($screenUrl)) {
            $qb->andWhere('screen.action_url = :screenUrl')
                ->setParameter('screenUrl', $screenUrl);
        }
        $qb->executeQuery();
    }

    private function updateModuleDisplayNames(): void
    {
        $displayNames = [
            'core' => 'Core',
            'admin' => 'Admin',
            'pim' => 'PIM',
            'leave' => 'Leave',
            'time' => 'Time',
            'attendance' => 'Attendance',
            'recruitment' => 'Recruitment',
            'recruitmentApply' => 'Recruitment Apply',
            'communication' => 'Communication',
            'dashboard' => 'Dashboard',
            'performance' => 'Performance',
            'directory' => 'Directory',
            'maintenance' => 'Maintenance',
            'marketPlace' => 'Market Place',
            'buzz' => 'Buzz',
        ];
        foreach ($displayNames as $module => $displayName) {
            $this->createQueryBuilder()
                ->update('ohrm_module', 'module')
                ->set('module.display_name', ':displayName')
                ->setParameter('displayName', $displayName)
                ->andWhere('module.name = :name')
                ->setParameter('name', $module)
                ->executeQuery();
        }
    }

    private function insertI18nGroups()
    {
        $groups = [
            'attendance' => 'Attendance',
            'help' => 'Help',
            'auth' => 'Auth',
        ];
        foreach ($groups as $name => $title) {
            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_i18n_group')
                ->values(
                    [
                        'name' => ':name',
                        'title' => ':title',
                    ]
                )
                ->setParameter('name', $name)
                ->setParameter('title', $title)
                ->executeQuery();
        }
    }

    /**
     * @return LangStringHelper
     */
    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper($this->getConnection());
        }
        return $this->langStringHelper;
    }

    /**
     * @return TranslationHelper
     */
    public function getTranslationHelper(): TranslationHelper
    {
        if (is_null($this->translationHelper)) {
            $this->translationHelper = new TranslationHelper($this->getConnection());
        }
        return $this->translationHelper;
    }

    private function cleanUniqueIdTable(): void
    {
        $this->createQueryBuilder()
            ->delete('hs_hr_unique_id')
            ->andWhere('table_name != :table')
            ->setParameter('table', 'hs_hr_employee')
            ->executeQuery();
    }

    private function hideAddonMenuItems(): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_menu_item', 'menuItem')
            ->set('menuItem.status', ':status')
            ->setParameter('status', false, ParameterType::BOOLEAN);
        $qb->where('menuItem.menu_title = :menuTitle')
            ->where($qb->expr()->in('menuItem.menu_title', ':menuTitles'))
            ->setParameter('menuTitles', ['Claim', 'LDAP Configuration'], Connection::PARAM_STR_ARRAY)
            ->executeQuery();
    }
}
