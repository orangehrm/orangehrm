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

namespace OrangeHRM\Core\Report\DisplayField;

final class EntityAliasMapping
{
    public const ALIAS_MAPPING = [
        'nationality' => 'employee.nationality',
        'employmentStatus' => 'employee.empStatus',
        'jobTitle' => 'employee.jobTitle',
        'jobCategory' => 'employee.jobCategory',
        'subunit' => 'employee.subDivision',
        'location' => 'employee.locations',
        'dependent' => 'employee.dependents',
        'emergencyContact' => 'employee.emergencyContacts',
        'immigrationRecord' => 'employee.immigrationRecords',
        'workExperience' => 'employee.workExperience',
        'education' => 'employee.educations',
        'skill' => 'employee.skills',
        'language' => 'employee.languages',
        'license' => 'employee.licenses',
        'membership' => 'employee.memberships',
        'salary' => 'employee.salaries',
        'employmentContract' => 'employee.employmentContracts',
        'subordinate' => 'employee.subordinates',
        'supervisor' => 'employee.supervisors',
        'payGrade' => 'salaries.payGrade',
        'currencyType' => 'salaries.currencyType',
        'payPeriod' => 'salaries.payPeriod',
        'directDebit' => 'salaries.directDebit',
        'employeeTerminationRecord' => 'employee.employeeTerminationRecord',
        'terminationReason' => 'employeeTerminationRecord.terminationReason',
    ];

    public const ALIAS_DEPENDENCIES = [
        'nationality' => 'employee',
        'employmentStatus' => 'employee',
        'jobTitle' => 'employee',
        'jobCategory' => 'employee',
        'subunit' => 'employee',
        'location' => 'employee',
        'dependent' => 'employee',
        'emergencyContact' => 'employee',
        'immigrationRecord' => 'employee',
        'workExperience' => 'employee',
        'education' => 'employee',
        'skill' => 'employee',
        'language' => 'employee',
        'license' => 'employee',
        'membership' => 'employee',
        'salary' => 'employee',
        'employmentContract' => 'employee',
        'subordinate' => 'employee',
        'supervisor' => 'employee',
        'payGrade' => 'salary',
        'currencyType' => 'salary',
        'payPeriod' => 'salary',
        'directDebit' => 'salary',
        'employeeTerminationRecord' => 'employee',
        'terminationReason' => 'employeeTerminationRecord',
    ];
}
