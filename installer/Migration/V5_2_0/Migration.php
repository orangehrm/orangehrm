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
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->getSchemaHelper()->changeColumn(
            'ohrm_i18n_translate',
            'value',
            ['Notnull' => false, 'Default' => null]
        );

        $oldGroups = ['admin', 'general'];
        foreach ($oldGroups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $this->updatePimLeftMenuConfigurators();
        $this->updateOrganizationStructure();
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
}
