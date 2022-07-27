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

namespace OrangeHRM\Pim\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Service\ReportGeneratorServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class SaveEmployeeReportController extends AbstractVueController
{
    use I18NHelperTrait;
    use ReportGeneratorServiceTrait;
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component("employee-report-edit");
            $component->addProp(new Prop("report-id", Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
        } else {
            $component = new Component("employee-report-save");
        }

        $selectionCriteria = [
            ["id" => 8, "key" => "employee_name", "label" => "Employee Name"],
            ["id" => 9, "key" => "pay_grade", "label" => "Pay Grade"],
            ["id" => 10, "key" => "education", "label" => "Education"],
            ["id" => 11, "key" => "employment_status", "label" => "Employment Status"],
            ["id" => 12, "key" => "service_period", "label" => "Service Period"],
            ["id" => 13, "key" => "joined_date", "label" => "Joined Date"],
            ["id" => 14, "key" => "job_title", "label" => "Job Title"],
            ["id" => 15, "key" => "language", "label" => "Language"],
            ["id" => 16, "key" => "skill", "label" => "Skill"],
            ["id" => 17, "key" => "age_group", "label" => "Age Group"],
            ["id" => 18, "key" => "sub_unit", "label" => "Sub Unit"],
            ["id" => 19, "key" => "gender", "label" => "Gender"],
            ["id" => 20, "key" => "location", "label" => "Location"],
        ];
        $component->addProp(
            new Prop(
                'selection-criteria',
                Prop::TYPE_ARRAY,
                array_map(
                    fn (array $criteria) => [
                        'id' => $criteria['id'],
                        'key' => $criteria['key'],
                        'label' => $this->getI18NHelper()->transBySource($criteria['label'])
                    ],
                    $selectionCriteria
                )
            )
        );

        $displayFieldGroups = $this->getReportGeneratorService()
            ->getReportGeneratorDao()->getDisplayFieldGroups();

        $component->addProp(
            new Prop(
                'display-field-groups',
                Prop::TYPE_ARRAY,
                array_map(
                    fn (array $displayFieldGroup) => [
                        'id' => $displayFieldGroup['id'],
                        'label' => $this->getI18NHelper()->transBySource($displayFieldGroup['name'])
                    ],
                    $displayFieldGroups
                )
            )
        );

        $displayFields = $this->getReportGeneratorService()->getReportGeneratorDao()
            ->getDisplayFields();

        $component->addProp(
            new Prop(
                'display-fields',
                Prop::TYPE_ARRAY,
                array_map(
                    fn (array $displayField) => [
                        'field_group_id' => $displayField['field_group_id'],
                        'fields' => array_map(
                            fn (array $field) => [
                                'id' => $field['id'],
                                'label' => $this->getI18NHelper()->transBySource($field['label']),
                            ],
                            $displayField['fields']
                        )
                    ],
                    $displayFields
                )
            )
        );

        $this->setComponent($component);
    }
}
