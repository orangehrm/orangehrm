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

namespace OrangeHRM\Installer\Migration\V5_2_0;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\LangStringHelper;
use Symfony\Component\Yaml\Yaml;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->cleanLDAPAddonData();
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $this->getSchemaHelper()->changeColumn(
            'ohrm_i18n_translate',
            'value',
            ['Notnull' => false, 'Default' => null]
        );

        $q = $this->createQueryBuilder()
            ->update('ohrm_i18n_translate', 'translate')
            ->set('translate.value', ':translateValue')
            ->where('translate.value = :currentValue');
        $q->setParameter('currentValue', $q->expr()->literal(''))
            ->setParameter('translateValue', null)
            ->executeQuery();

        $this->getSchemaHelper()->dropColumn(
            'ohrm_i18n_translate',
            'translated'
        );

        $this->getLangHelper()->deleteLangStringByUnitId(
            'leave_requests_action',
            $this->getLangHelper()->getGroupIdByName('leave')
        );
        $this->getLangHelper()->deleteLangStringByUnitId(
            'should_be_least_n_characters',
            $this->getLangHelper()->getGroupIdByName('general')
        );
        $oldGroups = ['admin', 'general', 'leave', 'pim', 'attendance', 'dashboard', 'time'];
        foreach ($oldGroups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->updatePimLeftMenuConfigurators();
        $this->updateOrganizationStructure();

        $this->updateHomePage('Admin', 'dashboard/index');
        $this->updateHomePage('ESS', 'dashboard/index');

        $this->getSchemaHelper()->createTable('ohrm_user_auth_provider')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('user_id', Types::INTEGER, ['Notnull' => true])
            ->addColumn('provider_type', Types::INTEGER, ['Notnull' => true])
            ->addColumn('ldap_user_hash', Types::STRING, ['Length' => 255, 'Notnull' => false, 'Default' => null])
            ->addColumn('ldap_user_dn', Types::STRING, ['Length' => 255, 'Notnull' => false, 'Default' => null])
            ->addColumn('ldap_user_unique_id', Types::STRING, ['Length' => 255, 'Notnull' => false, 'Default' => null])
            ->setPrimaryKey(['id'])
            ->create();
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['user_id'],
            'ohrm_user',
            ['id'],
            'ohrm_user_id',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'RESTRICT']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_user_auth_provider', $foreignKeyConstraint);
        $q = $this->createQueryBuilder()
            ->update('ohrm_user', 'user')
            ->set('user.user_password', ':nullValue')
            ->setParameter('nullValue', null);
        $q->where('user.user_password = :emptyString')
            ->setParameter('emptyString', $q->expr()->literal(''))
            ->executeQuery();

        $this->getSchemaHelper()->createTable('ohrm_ldap_sync_status')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('sync_started_at', Types::DATETIME_MUTABLE, ['Notnull' => true])
            ->addColumn('sync_finished_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
            ->addColumn('synced_by', Types::INTEGER, ['Notnull' => false, 'Default' => null])
            ->addColumn('sync_status', Types::INTEGER, ['Notnull' => true])
            ->setPrimaryKey(['id'])
            ->create();
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['synced_by'],
            'ohrm_user',
            ['id'],
            'ohrm_ldap_sync_status_synced_by',
            ['onDelete' => 'SET NULL', 'onUpdate' => 'RESTRICT']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_ldap_sync_status', $foreignKeyConstraint);

        $this->getSchemaHelper()->createTable('ohrm_task_scheduler_log')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('started_at', Types::DATETIME_MUTABLE, ['Notnull' => true])
            ->addColumn('finished_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
            ->addColumn('command', Types::STRING, ['Length' => 255, 'Notnull' => true])
            ->addColumn('input', Types::TEXT, ['Notnull' => false, 'Default' => null, 'Comment' => '(DC2Type:json)'])
            ->addColumn('status', Types::INTEGER, ['Notnull' => true])
            ->setPrimaryKey(['id'])
            ->create();

        $this->cleanI18nGroups();
        $this->insertLDAPMenuItem();
        $this->insertLangStringNotes();

        $this->getConfigHelper()
            ->setConfigValue(
                ConfigService::KEY_DASHBOARD_EMPLOYEES_ON_LEAVE_TODAY_SHOW_ONLY_ACCESSIBLE,
                0
            );

        $this->getSchemaHelper()->dropIndex('ohrm_user', 'user_name');

        $this->getConfigHelper()->setConfigValue('help.url', 'https://starterhelp.orangehrm.com');
        $this->getConfigHelper()->setConfigValue('help.processorClass', 'ZendeskHelpProcessor');
        Logger::getLogger()->info('Deleting invalid config values');
        $this->getConfigHelper()->deleteConfigValue('https://opensourcehelp.orangehrm.com');
        $this->getConfigHelper()->deleteConfigValue('ZendeskHelpProcessor');

        Logger::getLogger()->info('Deleting legacy config values');
        $this->getConfigHelper()->deleteConfigValue('ldap_domain_name');
        $this->getConfigHelper()->deleteConfigValue('ldap_port');
        $this->getConfigHelper()->deleteConfigValue('ldap_server');
        $this->getConfigHelper()->deleteConfigValue('ldap_status');
        $this->getConfigHelper()->deleteConfigValue('beacon.activation_acceptance_status');
        $this->getConfigHelper()->deleteConfigValue('beacon.activiation_status');
        $this->getConfigHelper()->deleteConfigValue('beacon.company_name');
        $this->getConfigHelper()->deleteConfigValue('beacon.flash_period');
        $this->getConfigHelper()->deleteConfigValue('beacon.lock');
        $this->getConfigHelper()->deleteConfigValue('beacon.next_flash_time');
        $this->getConfigHelper()->deleteConfigValue('beacon.uuid');
        $this->getConfigHelper()->deleteConfigValue('showSIN');
        $this->getConfigHelper()->deleteConfigValue('showSSN');
        $this->getConfigHelper()->deleteConfigValue('showTaxExemptions');
        $this->getConfigHelper()->deleteConfigValue('report.mysql_group_concat_max_len');
        $this->getConfigHelper()->deleteConfigValue('attendanceEmpEditSubmitted');
        $this->getConfigHelper()->deleteConfigValue('attendanceSupEditSubmitted');
        $this->getConfigHelper()->deleteConfigValue('hsp_accrued_last_updated');
        $this->getConfigHelper()->deleteConfigValue('hsp_current_plan');
        $this->getConfigHelper()->deleteConfigValue('hsp_used_last_updated');
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.2.0';
    }

    /**
     * @return LangStringHelper
     */
    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }

    private function updatePimLeftMenuConfigurators(): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.menu_configurator', ':menuConfiguratorClassName')
            ->setParameter('menuConfiguratorClassName', 'OrangeHRM\\Pim\\Menu\\PIMLeftMenuItemConfigurator')
            ->andWhere('screen.module_id = :moduleId')
            ->setParameter('moduleId', $this->getDataGroupHelper()->getModuleIdByName('pim'));
        $qb->andWhere($qb->expr()->in('screen.action_url', ':screenUrls'))
            ->setParameter('screenUrls', [
                'viewPersonalDetails',
                'contactDetails',
                'viewEmergencyContacts',
                'viewDependents',
                'viewImmigration',
                'viewJobDetails',
                'viewSalaryList',
                'viewUsTaxExemptions',
                'viewReportToDetails',
                'viewQualifications',
                'viewMemberships',
                'viewPhotograph',
            ], Connection::PARAM_STR_ARRAY);
        $qb->executeQuery();
    }

    private function updateOrganizationStructure(): void
    {
        $q = $this->createQueryBuilder();
        $q->select('orgInfo.name')
            ->from('ohrm_organization_gen_info', 'orgInfo');
        $organizationName = $q->executeQuery()->fetchOne();

        if ($organizationName != null) {
            $this->createQueryBuilder()
                ->update('ohrm_subunit', 'subunit')
                ->set('subunit.name', ':organizationName')
                ->setParameter('organizationName', $organizationName)
                ->andWhere('subunit.level = :topLevel')
                ->setParameter('topLevel', 0)
                ->executeQuery();
        }
    }

    /**
     * @param string $userRole
     * @param string $url
     */
    private function updateHomePage(string $userRole, string $url): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_home_page', 'homePage')
            ->set('homePage.action', ':url')
            ->setParameter('url', $url)
            ->andWhere('homePage.user_role_id = :userRoleId')
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName($userRole))
            ->executeQuery();
    }

    private function insertLDAPMenuItem(): void
    {
        $adminId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Admin')
            ->andWhere('level = :level')
            ->setParameter('level', 1)
            ->executeQuery()
            ->fetchOne();
        $configurationId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Configuration')
            ->andWhere('level = :level')
            ->setParameter('level', 2)
            ->andWhere('parent_id = :parentId')
            ->setParameter('parentId', $adminId)
            ->executeQuery()
            ->fetchOne();

        $ldapConfigScreenId = $this->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('screen.name = :screenName')
            ->setParameter('screenName', 'Admin - LDAP Configuration')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('ohrm_menu_item')
            ->values(
                [
                    'menu_title' => ':menuTitle',
                    'screen_id' => ':screenId',
                    'parent_id' => ':parentId',
                    'level' => ':level',
                    'order_hint' => ':orderHint',
                    'status' => ':status'
                ]
            )
            ->setParameter('menuTitle', 'LDAP Configuration')
            ->setParameter('screenId', $ldapConfigScreenId)
            ->setParameter('parentId', $configurationId)
            ->setParameter('level', 3)
            ->setParameter('orderHint', 1000)
            ->setParameter('status', 1)
            ->executeQuery();
    }

    private function cleanLDAPAddonData(): void
    {
        $this->createQueryBuilder()
            ->delete('ohrm_data_group')
            ->andWhere('ohrm_data_group.name = :dataGroupName')
            ->setParameter('dataGroupName', 'ldap_configuration')
            ->executeQuery();
        $this->createQueryBuilder()
            ->delete('ohrm_screen')
            ->andWhere('ohrm_screen.name = :screenName')
            ->setParameter('screenName', 'LDAP Configuration')
            ->executeQuery();
        $this->createQueryBuilder()
            ->delete('ohrm_module')
            ->andWhere('ohrm_module.name = :moduleName')
            ->setParameter('moduleName', 'ldapAuthentication')
            ->executeQuery();
    }

    private function insertLangStringNotes(): void
    {
        $filepath = $filepath = __DIR__ . '/lang-string/notes.yaml';
        $yml = Yaml::parseFile($filepath);
        $langStrings = array_shift($yml);
        foreach ($langStrings as $langString) {
            $groupId = $this->langStringHelper->getGroupId($langString['group']);
            $this->createQueryBuilder()
                ->update('ohrm_i18n_lang_string', 'langString')
                ->set('langString.note', ':note')
                ->setParameter('note', $langString['note'])
                ->where('langString.unit_id = :unitId')
                ->andWhere('langString.group_id = :group')
                ->setParameter('unitId', $langString['unitId'])
                ->setParameter('group', $groupId)
                ->executeQuery();
        }
    }

    private function cleanI18nGroups(): void
    {
        $qb = $this->createQueryBuilder()->delete('ohrm_i18n_group');
        $qb->where($qb->expr()->in('ohrm_i18n_group.name', ':groups'))
            ->setParameter('groups', ['directory', 'branding'], Connection::PARAM_STR_ARRAY)
            ->executeQuery();

        $qb = $this->createQueryBuilder()
            ->select('ohrm_i18n_group.id')
            ->from('ohrm_i18n_group')
            ->andWhere('ohrm_i18n_group.name = :name')
            ->setParameter('name', 'help')
            ->orderBy('ohrm_i18n_group.id', 'DESC');
        $ids = $qb->executeQuery()->fetchFirstColumn();
        $helpGroupId = array_pop($ids);

        $qb = $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string', 'langString')
            ->set('langString.group_id', ':groupId')
            ->setParameter('groupId', $helpGroupId);
        $qb->andWhere($qb->expr()->in('langString.group_id', ':groupsToBeDeleted'))
            ->setParameter('groupsToBeDeleted', $ids, Connection::PARAM_INT_ARRAY)
            ->executeQuery();

        $qb = $this->createQueryBuilder()
            ->delete('ohrm_i18n_group');
        $qb->where($qb->expr()->in('ohrm_i18n_group.id', ':groupsToBeDeleted'))
            ->setParameter('groupsToBeDeleted', $ids, Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }
}
