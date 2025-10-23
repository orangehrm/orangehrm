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
        /** @var DisplayField[] $displayFields */
        $displayFields = $this->getEntityManager()->getRepository(DisplayField::class)->findAll();
        $results = [];
        foreach ($displayFields as $displayField) {
            $result = [];
            $result['id'] = $displayField->getId();
            $result['name'] = $displayField->getName();
            $result['label'] = $displayField->getLabel();
            $result['fieldAlias'] = $displayField->getFieldAlias();
            $result['sortable'] = $displayField->isSortable() ? 'true' : 'false';
            $result['elementType'] = $displayField->getElementType();
            $result['elementProperty'] = $displayField->getElementProperty();
            $result['width'] = $displayField->getWidth();
            $result['isValueList'] = (int)$displayField->isValueList();
            $result['encrypted'] = (int)$displayField->isEncrypted();
            $result['exportable'] = (int)$displayField->isExportable();
            $result['meta'] = (int)$displayField->isMeta();
            $result['className'] = $displayField->getClassName();
            $result['report_group_id'] = $displayField->getReportGroup()->getId();
            if ($displayField->getDisplayFieldGroup()) {
                $result['display_field_group_id'] = $displayField->getDisplayFieldGroup()->getId();
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
