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
        $groupIds = [null];
        $groups = [];
        $displayGroupIds = [null];
        $displayGroups = [];
        foreach ($filterFields as $filterField) {
            $result = [];
            $result['id'] = $filterField->getId();
            $result['name'] = $filterField->getName();
            $result['label'] = $filterField->getLabel();
            $result['fieldAlias'] = $filterField->getFieldAlias();
            $result['sortable'] = $filterField->isSortable();
            $result['elementType'] = $filterField->getElementType();
            $result['elementProperty'] = $filterField->getElementProperty();
            $result['width'] = $filterField->getWidth();
            $result['isValueList'] = $filterField->isValueList();
            $result['encrypted'] = $filterField->isEncrypted();
            $result['meta'] = $filterField->isMeta();
            $result['className'] = $filterField->getClassName();
            $result['report_group_id'] = $filterField->getReportGroup()->getId();
            $result['display_field_group_id'] = $filterField->getDisplayFieldGroup() ?
                $filterField->getDisplayFieldGroup()->getId() : null;
            if (!in_array($result['report_group_id'], $groupIds)) {
                $groupIds[] = $result['report_group_id'];
                $groups[] = [
                    'id' => $filterField->getReportGroup()->getId(),
                    'name' => $filterField->getReportGroup()->getName(),
                    'coreSql' => $filterField->getReportGroup()->getCoreSql(),
                ];
            }
            if (!in_array($result['display_field_group_id'], $displayGroupIds)) {
                $displayGroupIds[] = $result['display_field_group_id'];
                $displayGroups[] = [
                    'id' => $filterField->getDisplayFieldGroup()->getId(),
                    'name' => $filterField->getDisplayFieldGroup()->getName(),
                    'isList' => $filterField->getDisplayFieldGroup()->isList(),
                    'report_group_id' => $filterField->getDisplayFieldGroup()->getReportGroup()->getId(),
                ];
            }
            $results[] = $result;
        }

        return ['ReportGroup' => $groups, 'DisplayFieldGroup' => $displayGroups, 'DisplayField' => $results];
    }

    /**
     * @inheritDoc
     */
    public static function getFileName(): string
    {
        return 'DisplayField.yaml';
    }
}
