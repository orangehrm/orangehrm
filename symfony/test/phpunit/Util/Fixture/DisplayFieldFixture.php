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

namespace OrangeHRM\Tests\Util\Fixture;

use OrangeHRM\Entity\DisplayField;
use OrangeHRM\Entity\DisplayFieldGroup;

class DisplayFieldFixture extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    protected function getContent(): array
    {
        /** @var DisplayField[] $filterFields */
        $filterFields = $this->getEntityManager()->getRepository(DisplayField::class)->findAll();
        $results = [];
        foreach ($filterFields as $filterField) {
            $result = [];
            $result['id'] = $filterField->getId();
            $result['name'] = $filterField->getName();
            $result['label'] = $filterField->getLabel();
            $result['fieldAlias'] = $filterField->getFieldAlias();
            $result['sortable'] = $filterField->isSortable() ? 'true' : 'false';
            $result['elementType'] = $filterField->getElementType();
            $result['elementProperty'] = $filterField->getElementProperty();
            $result['width'] = $filterField->getWidth();
            $result['isValueList'] = (int)$filterField->isValueList();
            $result['encrypted'] = (int)$filterField->isEncrypted();
            $result['exportable'] = (int)$filterField->isExportable();
            $result['meta'] = (int)$filterField->isMeta();
            $result['className'] = $filterField->getClassName();
            $result['report_group_id'] = $filterField->getReportGroup()->getId();
            if ($filterField->getDisplayFieldGroup()) {
                $result['display_field_group_id'] = $filterField->getDisplayFieldGroup()->getId();
            }
            $results[] = $result;
        }

        /** @var DisplayFieldGroup[] $displayFieldGroups */
        $displayFieldGroups = $this->getEntityManager()->getRepository(DisplayFieldGroup::class)->findAll();
        $displayGroups = [];
        foreach ($displayFieldGroups as $displayFieldGroup) {
            $displayGroups[] = [
                'id' => $displayFieldGroup->getId(),
                'name' => $displayFieldGroup->getName(),
                'isList' => (int)$displayFieldGroup->isList(),
                'report_group_id' => $displayFieldGroup->getReportGroup()->getId(),
            ];
        }

        return ['DisplayFieldGroup' => $displayGroups, 'DisplayField' => $results];
    }

    /**
     * @inheritDoc
     */
    public static function getFileName(): string
    {
        return 'DisplayField.yaml';
    }
}
