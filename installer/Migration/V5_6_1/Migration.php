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

namespace OrangeHRM\Installer\Migration\V5_6_1;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->updateDisplayFieldClassNames();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.6.1';
    }

    private function updateDisplayFieldClassNames(): void
    {
        $empLocationFieldId = $this->getDisplayFieldIdFromFieldAlias('empLocation');
        $empContStartDateFieldId = $this->getDisplayFieldIdFromFieldAlias('empContStartDate');
        $empContEndDateFieldId = $this->getDisplayFieldIdFromFieldAlias('empContEndDate');

        $this->updateDisplayFieldClassById(
            $empLocationFieldId,
            'OrangeHRM\Core\Report\DisplayField\GenericBasicDisplayFieldWithAggregate'
        );
        $this->updateDisplayFieldClassById(
            $empContStartDateFieldId,
            'OrangeHRM\Core\Report\DisplayField\GenericDateDisplayFieldWithAggregate'
        );
        $this->updateDisplayFieldClassById(
            $empContEndDateFieldId,
            'OrangeHRM\Core\Report\DisplayField\GenericDateDisplayFieldWithAggregate'
        );

    }

    /**
     * @param string $name
     * @return int
     */
    private function getDisplayFieldIdFromFieldAlias(string $name): int
    {
        $qb = $this->createQueryBuilder()
            ->select('field.display_field_id')
            ->from('ohrm_display_field', 'field')
            ->where('field.field_alias = :displayFieldName')
            ->setParameter('displayFieldName', $name)
            ->setMaxResults(1);

        return $qb->fetchOne();
    }

    /**
     * @param int $id
     * @param string $className
     */
    private function updateDisplayFieldClassById(int $id, string $className): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_display_field')
            ->set('ohrm_display_field.class_name', ':className')
            ->setParameter('className', $className)
            ->where('ohrm_display_field.display_field_id = :displayFieldId')
            ->setParameter('displayFieldId', $id)
            ->executeQuery();
    }
}
