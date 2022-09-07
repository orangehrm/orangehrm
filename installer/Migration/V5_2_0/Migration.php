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

namespace OrangeHRM\Installer\Migration\V5_2_0;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->getSchemaHelper()->changeColumn(
            'ohrm_i18n_translate',
            'value',
            ['Notnull' => false, 'Default' => null]
        );

        $this->createQueryBuilder()
            ->update('ohrm_i18n_translate', 'translate')
            ->set('translate.value', ':translateValue')
            ->where('translate.value', ':currentValue')
            ->setParameter('currentValue', '')
            ->setParameter('translateValue', null)
            ->executeQuery();

        $this->getSchemaHelper()->dropColumn(
            'ohrm_i18n_translate',
            'translated'
        );

        $oldGroups = ['admin', 'general', 'leave'];
        foreach ($oldGroups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
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
}
