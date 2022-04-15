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
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class SaveEmployeeReportController extends AbstractVueController
{
    use I18NHelperTrait;
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

        $displayFieldGroups = [
            ["id" => 1, "label" => "Personal"],
            ["id" => 2, "label" => "Contact Details"],
            ["id" => 3, "label" => "Emergency Contacts"],
            ["id" => 4, "label" => "Dependents"],
            ["id" => 15, "label" => "Memberships"],
            ["id" => 10, "label" => "Work Experience"],
            ["id" => 11, "label" => "Education"],
            ["id" => 12, "label" => "Skills"],
            ["id" => 13, "label" => "Languages"],
            ["id" => 14, "label" => "License"],
            ["id" => 9, "label" => "Supervisors"],
            ["id" => 8, "label" => "Subordinates"],
            ["id" => 7, "label" => "Salary"],
            ["id" => 6, "label" => "Job"],
            ["id" => 5, "label" => "Immigration"],
        ];
        $component->addProp(
            new Prop(
                'display-field-groups',
                Prop::TYPE_ARRAY,
                array_map(
                    fn (array $displayFieldGroup) => [
                        'id' => $displayFieldGroup['id'],
                        'label' => $this->getI18NHelper()->transBySource($displayFieldGroup['label'])
                    ],
                    $displayFieldGroups
                )
            )
        );

        $displayFields = [
            [
                "field_group_id" => 1,
                "fields" => [
                    [
                        "id" => 9,
                        "label" => "Employee Id",
                    ],
                    [
                        "id" => 10,
                        "label" => "Employee Last Name",
                    ],
                    [
                        "id" => 11,
                        "label" => "Employee First Name",
                    ],
                    [
                        "id" => 12,
                        "label" => "Employee Middle Name",
                    ],
                    [
                        "id" => 13,
                        "label" => "Date of Birth",
                    ],
                    [
                        "id" => 14,
                        "label" => "Nationality",
                    ],
                    [
                        "id" => 15,
                        "label" => "Gender",
                    ],
                    [
                        "id" => 17,
                        "label" => "Marital Status",
                    ],
                    [
                        "id" => 18,
                        "label" => "Driver License Number",
                    ],
                    [
                        "id" => 19,
                        "label" => "License Expiry Date",
                    ],
                    [
                        "id" => 97,
                        "label" => "Other Id",
                    ],
                ],
            ],
            [
                "field_group_id" => 2,
                "fields" => [
                    [
                        "id" => 20,
                        "label" => "Address",
                    ],
                    [
                        "id" => 21,
                        "label" => "Home Telephone",
                    ],
                    [
                        "id" => 22,
                        "label" => "Mobile",
                    ],
                    [
                        "id" => 23,
                        "label" => "Work Telephone",
                    ],
                    [
                        "id" => 24,
                        "label" => "Work Email",
                    ],
                    [
                        "id" => 25,
                        "label" => "Other Email",
                    ],
                ],
            ],
            [
                "field_group_id" => 3,
                "fields" => [
                    [
                        "id" => 26,
                        "label" => "Name",
                    ],
                    [
                        "id" => 27,
                        "label" => "Home Telephone",
                    ],
                    [
                        "id" => 28,
                        "label" => "Work Telephone",
                    ],
                    [
                        "id" => 29,
                        "label" => "Relationship",
                    ],
                    [
                        "id" => 30,
                        "label" => "Mobile",
                    ],
                ],
            ],
            [
                "field_group_id" => 4,
                "fields" => [
                    [
                        "id" => 31,
                        "label" => "Name",
                    ],
                    [
                        "id" => 32,
                        "label" => "Relationship",
                    ],
                    [
                        "id" => 33,
                        "label" => "Date of Birth",
                    ],
                ],
            ],
            [
                "field_group_id" => 5,
                "fields" => [
                    [
                        "id" => 84,
                        "label" => "Number",
                    ],
                    [
                        "id" => 85,
                        "label" => "Issued Date",
                    ],
                    [
                        "id" => 86,
                        "label" => "Expiry Date",
                    ],
                    [
                        "id" => 87,
                        "label" => "Eligibility Status",
                    ],
                    [
                        "id" => 88,
                        "label" => "Issued By",
                    ],
                    [
                        "id" => 89,
                        "label" => "Eligibility Review Date",
                    ],
                    [
                        "id" => 90,
                        "label" => "Comments",
                    ],
                    [
                        "id" => 95,
                        "label" => "Document Type",
                    ],
                ],
            ],
            [
                "field_group_id" => 6,
                "fields" => [
                    [
                        "id" => 75,
                        "label" => "Contract Start Date",
                    ],
                    [
                        "id" => 76,
                        "label" => "Contract End Date",
                    ],
                    [
                        "id" => 77,
                        "label" => "Job Title",
                    ],
                    [
                        "id" => 78,
                        "label" => "Employment Status",
                    ],
                    [
                        "id" => 80,
                        "label" => "Job Category",
                    ],
                    [
                        "id" => 81,
                        "label" => "Joined Date",
                    ],
                    [
                        "id" => 82,
                        "label" => "Sub Unit",
                    ],
                    [
                        "id" => 83,
                        "label" => "Location",
                    ],
                    [
                        "id" => 113,
                        "label" => "Termination Date",
                    ],
                    [
                        "id" => 114,
                        "label" => "Termination Reason",
                    ],
                    [
                        "id" => 120,
                        "label" => "Termination Note",
                    ],
                ],
            ],
            [
                "field_group_id" => 7,
                "fields" => [
                    [
                        "id" => 65,
                        "label" => "Pay Grade",
                    ],
                    [
                        "id" => 66,
                        "label" => "Salary Component",
                    ],
                    [
                        "id" => 67,
                        "label" => "Amount",
                    ],
                    [
                        "id" => 68,
                        "label" => "Comments",
                    ],
                    [
                        "id" => 69,
                        "label" => "Pay Frequency",
                    ],
                    [
                        "id" => 70,
                        "label" => "Currency",
                    ],
                    [
                        "id" => 71,
                        "label" => "Direct Deposit Account Number",
                    ],
                    [
                        "id" => 72,
                        "label" => "Direct Deposit Account Type",
                    ],
                    [
                        "id" => 73,
                        "label" => "Direct Deposit Routing Number",
                    ],
                    [
                        "id" => 74,
                        "label" => "Direct Deposit Amount",
                    ],
                ],
            ],
            [
                "field_group_id" => 8,
                "fields" => [
                    [
                        "id" => 63,
                        "label" => "First Name",
                    ],
                    [
                        "id" => 91,
                        "label" => "Last Name",
                    ],
                    [
                        "id" => 94,
                        "label" => "Reporting Method",
                    ],
                ],
            ],
            [
                "field_group_id" => 9,
                "fields" => [
                    [
                        "id" => 62,
                        "label" => "First Name",
                    ],
                    [
                        "id" => 64,
                        "label" => "Last Name",
                    ],
                    [
                        "id" => 93,
                        "label" => "Reporting Method",
                    ],
                ],
            ],
            [
                "field_group_id" => 10,
                "fields" => [
                    [
                        "id" => 41,
                        "label" => "Company",
                    ],
                    [
                        "id" => 42,
                        "label" => "Job Title",
                    ],
                    [
                        "id" => 43,
                        "label" => "From",
                    ],
                    [
                        "id" => 44,
                        "label" => "To",
                    ],
                    [
                        "id" => 45,
                        "label" => "Comment",
                    ],
                    [
                        "id" => 112,
                        "label" => "Duration",
                    ],
                ],
            ],
            [
                "field_group_id" => 11,
                "fields" => [
                    [
                        "id" => 47,
                        "label" => "Level",
                    ],
                    [
                        "id" => 48,
                        "label" => "Year",
                    ],
                    [
                        "id" => 49,
                        "label" => "Score",
                    ],
                    [
                        "id" => 115,
                        "label" => "Institute",
                    ],
                    [
                        "id" => 116,
                        "label" => "Major/Specialization",
                    ],
                    [
                        "id" => 117,
                        "label" => "Start Date",
                    ],
                    [
                        "id" => 118,
                        "label" => "End Date",
                    ],
                ],
            ],
            [
                "field_group_id" => 12,
                "fields" => [
                    [
                        "id" => 52,
                        "label" => "Skill",
                    ],
                    [
                        "id" => 53,
                        "label" => "Years of Experience",
                    ],
                    [
                        "id" => 54,
                        "label" => "Comments",
                    ],
                ],
            ],
            [
                "field_group_id" => 13,
                "fields" => [
                    [
                        "id" => 55,
                        "label" => "Language",
                    ],
                    [
                        "id" => 57,
                        "label" => "Competency",
                    ],
                    [
                        "id" => 58,
                        "label" => "Comments",
                    ],
                    [
                        "id" => 92,
                        "label" => "Fluency",
                    ],
                ],
            ],
            [
                "field_group_id" => 14,
                "fields" => [
                    [
                        "id" => 59,
                        "label" => "License Type",
                    ],
                    [
                        "id" => 60,
                        "label" => "Issued Date",
                    ],
                    [
                        "id" => 61,
                        "label" => "Expiry Date",
                    ],
                    [
                        "id" => 119,
                        "label" => "License Number",
                    ],
                ],
            ],
            [
                "field_group_id" => 15,
                "fields" => [
                    [
                        "id" => 35,
                        "label" => "Membership",
                    ],
                    [
                        "id" => 36,
                        "label" => "Subscription Paid By",
                    ],
                    [
                        "id" => 37,
                        "label" => "Subscription Amount",
                    ],
                    [
                        "id" => 38,
                        "label" => "Currency",
                    ],
                    [
                        "id" => 39,
                        "label" => "Subscription Commence Date",
                    ],
                    [
                        "id" => 40,
                        "label" => "Subscription Renewal Date",
                    ],
                ],
            ],
        ];
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

    /**
     * @param array $fields
     * @return array
     */
    private function translateFields(array $fields): array
    {
        $translatedField =[];
        foreach ($fields as $field) {
            $translatedField[] =[
                'id' => $field['id'],
                'label' =>$this->getI18NHelper()->transBySource($field['label']),
            ];
        }
        return $translatedField;
    }
}
