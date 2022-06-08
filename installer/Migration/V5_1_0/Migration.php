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

namespace OrangeHRM\Installer\Migration\V5_1_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    protected ?TranslationHelper $translationHelper = null;

    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');
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

        $groups = ['recruitment', 'performance'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->deleteNonCustomizedLangStrings($group);
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $oldGroups = ['admin', 'general', 'pim', 'leave', 'time', 'attendance', 'maintenance', 'help', 'auth'];
        foreach ($oldGroups as $group) {
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

        $performanceModuleId = $this->getDataGroupHelper()->getModuleIdByName('performance');

        $this->insertModuleDefaultPage(
            $performanceModuleId,
            $this->getDataGroupHelper()->getUserRoleIdByName('Admin'),
            'performance/searchEvaluatePerformancReview',
            20
        );
        $this->insertModuleDefaultPage(
            $performanceModuleId,
            $this->getDataGroupHelper()->getUserRoleIdByName('Supervisor'),
            'performance/searchEvaluatePerformancReview',
            20
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
                'searchEvaluatePerformancReview',
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
}
