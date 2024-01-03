<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Installer\Migration\V5_1_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\LangStringHelper;
use OrangeHRM\Installer\Util\V1\TranslationHelper;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    protected ?TranslationHelper $translationHelper = null;

    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.action_url ', ':actionUrl')
            ->setParameter('actionUrl', 'viewPerformanceTracker')
            ->andWhere('screen.module_id = :moduleId')
            ->setParameter('moduleId', $this->getDataGroupHelper()->getModuleIdByName('performance'))
            ->andWhere('screen.name = :name')
            ->setParameter('name', 'Manage_Trackers')
            ->executeQuery();

        $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.action_url ', ':actionUrl')
            ->setParameter('actionUrl', 'searchEvaluatePerformanceReview')
            ->andWhere('screen.module_id = :moduleId')
            ->setParameter('moduleId', $this->getDataGroupHelper()->getModuleIdByName('performance'))
            ->andWhere('screen.name = :name')
            ->setParameter('name', 'Search Evaluate Performance')
            ->executeQuery();

        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $this->addValidColumnToRequestResetPassword();

        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_kpi CHANGE job_title_code job_title_code INT(13) NOT NULL'
        );
        $kpiForeignKeyConstraint = new ForeignKeyConstraint(
            ['job_title_code'],
            'ohrm_job_title',
            ['id'],
            'ohrm_kpi_for_job_title_id',
            ['onCascade' => 'DELETE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_kpi', $kpiForeignKeyConstraint);

        $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.action_url ', ':actionUrl')
            ->setParameter('actionUrl', 'searchPerformanceReview')
            ->andWhere('screen.name  = :name')
            ->setParameter('name', 'Search Performance Review')
            ->executeQuery();

        $this->getLangHelper()->deleteLangStringByUnitId(
            'this_page_is_being_developed',
            $this->getLangHelper()->getGroupIdByName('general')
        );

        $this->updateLangStringVersion('5.0.0');
        $groups = ['recruitment', 'performance'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->deleteNonCustomizedLangStrings($group);
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $oldGroups = ['admin', 'general', 'maintenance'];
        foreach ($oldGroups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->createQueryBuilder()
            ->delete('ohrm_i18n_lang_string')
            ->andWhere('ohrm_i18n_lang_string.group_id = :groupId')
            ->setParameter('groupId', $this->getLangHelper()->getGroupIdByName('directory'))
            ->executeQuery();

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
            $this->getTranslationHelper()->addTranslations(__DIR__, $langCode);
        }
        $this->updateLangStringVersion($this->getVersion());

        $performanceModuleId = $this->getDataGroupHelper()->getModuleIdByName('performance');

        $this->insertModuleDefaultPage(
            $performanceModuleId,
            $this->getDataGroupHelper()->getUserRoleIdByName('Admin'),
            'performance/searchEvaluatePerformanceReview',
            20
        );
        $this->insertModuleDefaultPage(
            $performanceModuleId,
            $this->getDataGroupHelper()->getUserRoleIdByName('Supervisor'),
            'performance/searchEvaluatePerformanceReview',
            10
        );
        $this->insertModuleDefaultPage(
            $performanceModuleId,
            $this->getDataGroupHelper()->getUserRoleIdByName('ESS'),
            'performance/myPerformanceReview',
            0
        );

        $reviewListScreenId = $this->getDataGroupHelper()
            ->getScreenIdByModuleAndUrl(
                $performanceModuleId,
                'searchEvaluatePerformanceReview',
            );

        $this->createQueryBuilder()
            ->update('ohrm_user_role_screen', 'userRoleScreen')
            ->set('userRoleScreen.user_role_id', ':userRoleId')
            ->setParameter(
                'userRoleId',
                $this->getDataGroupHelper()->getUserRoleIdByName('Supervisor')
            )
            ->andWhere('userRoleScreen.screen_id = :screenId')
            ->setParameter('screenId', $reviewListScreenId)
            ->executeQuery();

        $this->createQueryBuilder()
            ->update('ohrm_menu_item', 'menuItem')
            ->set('menuItem.menu_title', ':menuTitle')
            ->setParameter('menuTitle', 'Employee Reviews')
            ->andWhere('menuItem.screen_id = :screenId')
            ->setParameter('screenId', $reviewListScreenId)
            ->executeQuery();

        $this->insertReviewWorkflowStates();
        $this->insertSelfReviewWorkflowStates();
        $this->insertReviewListScreenForAdminRole($reviewListScreenId);
        $this->modifyThemeTable();

        $groupId = $this->getLangHelper()->getGroupIdByName('general');
        $toDeleteLangStringId = $this->getLangStringHelper()->getLangStringIdByValueAndGroup('Allows Phone Numbers Only', $groupId);
        $toPreserveLangStringId = $this->getLangStringHelper()->getLangStringIdByValueAndGroup('Allows numbers and only + - / ( )', $groupId);

        $this->createQueryBuilder()
            ->update('ohrm_i18n_translate', 'translate')
            ->set('translate.lang_string_id', ':langStringId')
            ->setParameter('langStringId', $toPreserveLangStringId)
            ->andWhere('translate.lang_string_id = :deletedLangStringId')
            ->setParameter('deletedLangStringId', $toDeleteLangStringId)
            ->executeQuery();

        $this->createQueryBuilder()
            ->delete('ohrm_i18n_lang_string')
            ->andWhere('ohrm_i18n_lang_string.id = :id')
            ->setParameter('id', $toDeleteLangStringId)
            ->executeQuery();

        $this->modifyTrackerLogsUserForeignKey();

        $maintenanceModuleId = $this->getDataGroupHelper()->getModuleIdByName('maintenance');
        $maintenanceModuleScreenId = $this->getDataGroupHelper()
            ->getScreenIdByModuleAndUrl($maintenanceModuleId, 'viewMaintenanceModule');
        $this->createQueryBuilder()
            ->update('ohrm_menu_item', 'menu_item')
            ->set('menu_item.screen_id', ':screenId')
            ->setParameter('screenId', $maintenanceModuleScreenId)
            ->andWhere('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Maintenance')
            ->andWhere('level = :level')
            ->setParameter('level', 1)
            ->executeQuery();

        $this->updateRecruitmentMenuItems();
        $this->updatePerformanceMenuItems();

        $this->modifyEmployeeTrackerScreenRolePermission($performanceModuleId);

        $this->createQueryBuilder()
            ->update('ohrm_job_vacancy', 'vacancy')
            ->set('vacancy.status', ':newClosedStatus')
            ->setParameter('newClosedStatus', 0)
            ->andWhere('vacancy.status = :closedStatus')
            ->setParameter('closedStatus', 2)
            ->executeQuery();
        $this->getSchemaHelper()->changeColumn(
            'ohrm_job_vacancy',
            'status',
            ['Type' => Type::getType(Types::BOOLEAN), 'Default' => true, 'Notnull' => true]
        );

        $this->getDataGroupHelper()->addDataGroupPermissions('apiv2_admin_job_title', 'Interviewer', true);
        $this->getDataGroupHelper()->addDataGroupPermissions('apiv2_admin_job_title', 'HiringManager', true);
    }


    /**
     * @param string $version
     */
    private function updateLangStringVersion(string $version): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string', 'lang_string')
            ->set('lang_string.version', ':version')
            ->setParameter('version', $version);
        $qb->andWhere($qb->expr()->isNull('lang_string.version'))
            ->executeQuery();
    }

    /**
     * @return void
     */
    private function addValidColumnToRequestResetPassword(): void
    {
        $this->getSchemaHelper()->addColumn(
            'ohrm_reset_password',
            'expired',
            Types::BOOLEAN,
            ['Default' => true, 'Notnull' => true]
        );
    }

    /**
     * @param int $moduleId
     * @param int $userRoleId
     * @param string $action
     * @param int $priority
     */
    private function insertModuleDefaultPage(
        int $moduleId,
        int $userRoleId,
        string $action,
        int $priority
    ): void {
        $this->createQueryBuilder()
            ->insert('ohrm_module_default_page')
            ->values(
                [
                    'module_id' => ':moduleId',
                    'user_role_id' => ':userRoleId',
                    'action' => ':action',
                    'priority' => ':priority'
                ]
            )
            ->setParameter('moduleId', $moduleId)
            ->setParameter('userRoleId', $userRoleId)
            ->setParameter('action', $action)
            ->setParameter('priority', $priority)
            ->executeQuery();
    }

    private function insertReviewWorkflowStates(): void
    {
        // Admin workflows
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'INITIAL', 'ADMIN', WorkflowStateMachine::REVIEW_INACTIVE_SAVE, 'SAVED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'INITIAL', 'ADMIN', WorkflowStateMachine::REVIEW_ACTIVATE, 'ACTIVATED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'SAVED', 'ADMIN', WorkflowStateMachine::REVIEW_INACTIVE_SAVE, 'SAVED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'SAVED', 'ADMIN', WorkflowStateMachine::REVIEW_ACTIVATE, 'ACTIVATED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'ACTIVATED', 'ADMIN', WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE, 'IN PROGRESS');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'ACTIVATED', 'ADMIN', WorkflowStateMachine::REVIEW_COMPLETE, 'COMPLETED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'IN PROGRESS', 'ADMIN', WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE, 'IN PROGRESS');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'IN PROGRESS', 'ADMIN', WorkflowStateMachine::REVIEW_COMPLETE, 'COMPLETED');

        // Supervisor workflows
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'ACTIVATED', 'SUPERVISOR', WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE, 'IN PROGRESS');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'ACTIVATED', 'SUPERVISOR', WorkflowStateMachine::REVIEW_COMPLETE, 'COMPLETED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'IN PROGRESS', 'SUPERVISOR', WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE, 'IN PROGRESS');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_REVIEW, 'IN PROGRESS', 'SUPERVISOR', WorkflowStateMachine::REVIEW_COMPLETE, 'COMPLETED');
    }

    private function insertSelfReviewWorkflowStates(): void
    {
        // Admin workflows
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'SELF COMPLETED', 'ADMIN', WorkflowStateMachine::SELF_REVIEW_SUPERVISOR_ACTION, 'SUPERVISOR UPDATED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'SUPERVISOR UPDATED', 'ADMIN', WorkflowStateMachine::SELF_REVIEW_SUPERVISOR_ACTION, 'SUPERVISOR UPDATED');

        // Supervisor workflows
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'SELF COMPLETED', 'SUPERVISOR', WorkflowStateMachine::SELF_REVIEW_SUPERVISOR_ACTION, 'SUPERVISOR UPDATED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'SUPERVISOR UPDATED', 'SUPERVISOR', WorkflowStateMachine::SELF_REVIEW_SUPERVISOR_ACTION, 'SUPERVISOR UPDATED');

        // ESS workflows
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'INITIAL', 'ESS USER', WorkflowStateMachine::SELF_REVIEW_SELF_SAVE, 'SELF IN PROGRESS');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'INITIAL', 'ESS USER', WorkflowStateMachine::SELF_REVIEW_SELF_COMPLETE, 'SELF COMPLETED');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'SELF IN PROGRESS', 'ESS USER', WorkflowStateMachine::SELF_REVIEW_SELF_SAVE, 'SELF IN PROGRESS');
        $this->insertWorkflowState(WorkflowStateMachine::FLOW_SELF_REVIEW, 'SELF IN PROGRESS', 'ESS USER', WorkflowStateMachine::SELF_REVIEW_SELF_COMPLETE, 'SELF COMPLETED');
    }

    /**
     * @param int $workflow
     * @param string $state
     * @param string $role
     * @param int $action
     * @param string $resultingState
     * @return void
     */
    private function insertWorkflowState(
        int $workflow,
        string $state,
        string $role,
        int $action,
        string $resultingState
    ): void {
        $this->createQueryBuilder()
            ->insert('ohrm_workflow_state_machine')
            ->values(
                [
                    'workflow' => ':workflow',
                    'state' => ':state',
                    'role' => ':role',
                    'action' => ':action',
                    'resulting_state' => ':resultingState',
                ]
            )
            ->setParameter('workflow', $workflow)
            ->setParameter('state', $state)
            ->setParameter('role', $role)
            ->setParameter('action', $action)
            ->setParameter('resultingState', $resultingState)
            ->executeQuery();
    }

    /**
     * @param int $reviewListScreenId
     */
    private function insertReviewListScreenForAdminRole(int $reviewListScreenId): void
    {
        $this->createQueryBuilder()
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
            ->setParameter('screenId', $reviewListScreenId)
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName('Admin'))
            ->setParameter('read', 1)
            ->setParameter('create', 0)
            ->setParameter('update', 1)
            ->setParameter('delete', 0)
            ->executeQuery();
    }

    private function modifyThemeTable(): void
    {
        $this->getSchemaHelper()->dropColumn('ohrm_theme', 'social_media_icons');
        $this->getSchemaHelper()
            ->addColumn(
                'ohrm_theme',
                'show_social_media_icons',
                Types::BOOLEAN,
                ['Notnull' => true, 'Default' => true]
            );
        $this->getSchemaHelper()->renameColumn('ohrm_theme', 'main_logo', 'client_logo');
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_banner',
            Types::BLOB,
            ['Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_logo_filename',
            Types::STRING,
            ['Length' => 100, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_logo_file_type',
            Types::STRING,
            ['Length' => 100, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_logo_file_size',
            Types::INTEGER,
            ['Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_banner_filename',
            Types::STRING,
            ['Length' => 100, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_banner_file_type',
            Types::STRING,
            ['Length' => 100, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'client_banner_file_size',
            Types::INTEGER,
            ['Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'login_banner_filename',
            Types::STRING,
            ['Length' => 100, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'login_banner_file_type',
            Types::STRING,
            ['Length' => 100, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_theme',
            'login_banner_file_size',
            Types::INTEGER,
            ['Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->changeColumn(
            'ohrm_theme',
            'login_banner',
            ['Notnull' => false, 'Default' => null]
        );

        $this->createQueryBuilder()
            ->update('ohrm_theme')
            ->set('ohrm_theme.variables', ':variables')
            ->where('ohrm_theme.theme_name = :themeName')
            ->setParameter(
                'variables',
                '{"primaryColor":"#FF7B1D","primaryFontColor":"#FFFFFF","secondaryColor":"#76BC21","secondaryFontColor":"#FFFFFF","primaryGradientStartColor":"#FF920B","primaryGradientEndColor":"#F35C17"}'
            )
            ->setParameter('themeName', 'default')
            ->executeQuery();

        $this->createQueryBuilder()
            ->update('ohrm_theme')
            ->set('ohrm_theme.theme_name', ':newName')
            ->where('ohrm_theme.theme_name = :currentName')
            ->setParameter('currentName', 'custom')
            ->setParameter('newName', 'custom_4x')
            ->executeQuery();
    }

    private function modifyTrackerLogsUserForeignKey(): void
    {
        $this->getSchemaHelper()->dropForeignKeys('ohrm_performance_tracker_log', ['fk_ohrm_performance_tracker_log_1']);
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['user_id'],
            'ohrm_user',
            ['id'],
            'ohrm_performance_tracker_log_modified_by_id',
            ['onDelete' => 'SET NULL', 'onUpdate' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_performance_tracker_log', $foreignKeyConstraint);
    }

    /**
     * @param int $performanceModuleId
     */
    private function modifyEmployeeTrackerScreenRolePermission(int $performanceModuleId): void
    {
        $employeeTrackerScreenId = $this->getDataGroupHelper()
            ->getScreenIdByModuleAndUrl(
                $performanceModuleId,
                'viewEmployeePerformanceTrackerList',
            );

        $this->createQueryBuilder()
            ->update('ohrm_user_role_screen', 'userRoleScreen')
            ->set('userRoleScreen.user_role_id', ':userRoleId')
            ->setParameter(
                'userRoleId',
                $this->getDataGroupHelper()->getUserRoleIdByName('Reviewer')
            )
            ->andWhere('userRoleScreen.screen_id = :screenId')
            ->andWhere('userRoleScreen.user_role_id = :oldUserRoleId')
            ->setParameter('screenId', $employeeTrackerScreenId)
            ->setParameter(
                'oldUserRoleId',
                $this->getDataGroupHelper()->getUserRoleIdByName('ESS')
            )
            ->executeQuery();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.1.0';
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

    private function updateRecruitmentMenuItems(): void
    {
        $this->updateMenuConfigurator(
            'recruitment',
            'addJobVacancy',
            'OrangeHRM\\Recruitment\\Menu\\VacancyMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'recruitment',
            'addCandidate',
            'OrangeHRM\\Recruitment\\Menu\\CandidateMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'recruitment',
            'changeCandidateVacancyStatus',
            'OrangeHRM\\Recruitment\\Menu\\CandidateMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'recruitment',
            'candidateHistory',
            'OrangeHRM\\Recruitment\\Menu\\CandidateMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'recruitment',
            'interviewAttachments',
            'OrangeHRM\\Recruitment\\Menu\\CandidateMenuConfigurator'
        );
    }

    private function updatePerformanceMenuItems(): void
    {
        $this->updateMenuConfigurator(
            'performance',
            'saveKpi',
            'OrangeHRM\\Performance\\Menu\\KpiMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'performance',
            'addPerformanceTracker',
            'OrangeHRM\\Performance\\Menu\\PerformanceTrackerMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'performance',
            'addPerformanceTrackerLog',
            'OrangeHRM\\Performance\\Menu\\PerformanceTrackerLogMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'performance',
            'saveReview',
            'OrangeHRM\\Performance\\Menu\\ManageReviewMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'performance',
            'reviewEvaluate',
            'OrangeHRM\\Performance\\Menu\\MyReviewMenuConfigurator'
        );
        $this->updateMenuConfigurator(
            'performance',
            'reviewEvaluateByAdmin',
            'OrangeHRM\\Performance\\Menu\\AdminReviewMenuConfigurator'
        );
    }
}
