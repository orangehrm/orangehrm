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

class SaveEmployeeReportController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('employee-report-save');

        $selectionCriteria = [
            ["id" => 'employee_name', "label" => "Employee Name"],
            ["id" => 'pay_grade', "label" => "Pay Grade"],
            ["id" => 'education', "label" => "Education"],
            ["id" => 'employment_status', "label" => "Employment Status"],
            ["id" => 'service_period', "label" => "Service Period"],
            ["id" => 'joined_date', "label" => "Joined Date"],
            ["id" => 'job_title', "label" => "Job Title"],
            ["id" => 'language', "label" => "Language"],
            ["id" => 'skill', "label" => "Skill"],
            ["id" => 'age_group', "label" => "Age Group"],
            ["id" => 'sub_unit', "label" => "Sub Unit"],
            ["id" => 'gender', "label" => "Gender"],
            ["id" => 'location', "label" => "Location"]
        ];
        $component->addProp(new Prop('selection-criteria', Prop::TYPE_ARRAY, $selectionCriteria));

        $displayFieldGroups = [
            ["id" => 'display_group_1', "label" => "Personal"],
            ["id" => 'display_group_2', "label" => "Contact Details"],
            ["id" => 'display_group_3', "label" => "Emergency Contacts"],
            ["id" => 'display_group_4', "label" => "Dependents"],
            ["id" => 'display_group_15', "label" => "Memberships"],
            ["id" => 'display_group_10', "label" => "Work Experience"],
            ["id" => 'display_group_11', "label" => "Education"],
            ["id" => 'display_group_12', "label" => "Skills"],
            ["id" => 'display_group_13', "label" => "Languages"],
            ["id" => 'display_group_14', "label" => "License"],
            ["id" => 'display_group_9', "label" => "Supervisors"],
            ["id" => 'display_group_8', "label" => "Subordinates"],
            ["id" => 'display_group_7', "label" => "Salary"],
            ["id" => 'display_group_6', "label" => "Job"],
            ["id" => 'display_group_5', "label" => "Immigration"]
        ];
        $component->addProp(new Prop('display-field-groups', Prop::TYPE_ARRAY, $displayFieldGroups));

        $this->setComponent($component);
    }
}
