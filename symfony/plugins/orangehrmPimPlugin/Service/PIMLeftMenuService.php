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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class PIMLeftMenuService
{
    use UserRoleManagerTrait;
    use AuthUserTrait;
    use ConfigServiceTrait;
    use ModuleScreenHelperTrait;
    use ControllerTrait;
    use I18NHelperTrait;

    public const PIM_LEFTMENU_SESSION_KEY = 'pim.leftMenu.cache';
    public const PIM_LEFTMENU_TAXMENU_ENABLED = 'pim.leftMenu.isTaxMenuEnabled';

    private ?EmployeeService $employeeService = null;

    private array $availableActions = [
        'viewPersonalDetails' => [
            'module' => 'pim',
            'data_groups' => ['personal_information', 'personal_attachment', 'personal_custom_fields'],
            'label' => "Personal Details"
        ],
        'contactDetails' => [
            'module' => 'pim',
            'data_groups' => ['contact_details', 'contact_attachment', 'contact_custom_fields'],
            'label' => 'Contact Details'
        ],
        'viewEmergencyContacts' => [
            'module' => 'pim',
            'data_groups' => ['emergency_contacts', 'emergency_attachment', 'emergency_custom_fields'],
            'label' => 'Emergency Contacts'
        ],
        'viewDependents' => [
            'module' => 'pim',
            'data_groups' => ['dependents', 'dependents_attachment', 'dependents_custom_fields'],
            'label' => 'Dependents'
        ],
        'viewImmigration' => [
            'module' => 'pim',
            'data_groups' => ['immigration', 'immigration_attachment', 'immigration_custom_fields'],
            'label' => 'Immigration'
        ],
        'viewJobDetails' => [
            'module' => 'pim',
            'data_groups' => ['job_details', 'job_attachment', 'job_custom_fields'],
            'label' => 'Job'
        ],
        'viewSalaryList' => [
            'module' => 'pim',
            'data_groups' => ['salary_details', 'salary_attachment', 'salary_custom_fields'],
            'label' => 'Salary'
        ],
        'viewUsTaxExemptions' => [
            'module' => 'pim',
            'data_groups' => ['tax_exemptions', 'tax_attachment', 'tax_custom_fields'],
            'label' => 'Tax Exemptions'
        ],
        'viewReportToDetails' => [
            'module' => 'pim',
            'data_groups' => ['supervisor', 'subordinates', 'report-to_attachment', 'report-to_custom_fields'],
            'actions' => [],
            'label' => 'Report-to'
        ],
        'viewQualifications' => [
            'module' => 'pim',
            'data_groups' => [
                'qualification_work',
                'qualification_education',
                'qualification_skills',
                'qualification_languages',
                'qualification_license',
                'qualifications_attachment',
                'qualifications_custom_fields'
            ],
            'label' => 'Qualifications'
        ],
        'viewMemberships' => [
            'module' => 'pim',
            'data_groups' => ['membership', 'membership_attachment', 'membership_custom_fields'],
            'label' => 'Memberships'
        ]
    ];

    /**
     * @returns EmployeeService
     */
    public function getEmployeeService(): EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService): void
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Returns PIM left menu items in when looking at the given employee.
     *
     * @param int|null $empNumber Employee Number
     * @param bool $self If true, indicates menu when user is looking at his own info
     * @return array Array of menu items.
     * @throws CoreServiceException
     * @throws DaoException
     * @throws ServiceException
     */
    public function getMenuItems(?int $empNumber, bool $self): array
    {
        $menu = $this->getMenuFromCache($empNumber);

        if (empty($menu)) {
            $menu = $this->generateMenuItems($empNumber, $self);
            $this->saveMenuInCache($empNumber, $menu);
        }
        return $menu;
    }


    /**
     * Clears cached PIM menu for given employee
     *
     * If employee is null, all cached menu items are cleared.
     *
     * @param int|null $empNumber Employee Number (or null)
     */
    public function clearCachedMenu(?int $empNumber = null): void
    {
        $user = $this->getAuthUser();
        $cache = $user->getAttribute(self::PIM_LEFTMENU_SESSION_KEY, []);
        if (empty($empNumber)) {
            $cache = [];
        } else {
            unset($cache[$empNumber]);
        }

        $user->setAttribute(self::PIM_LEFTMENU_SESSION_KEY, $cache);
    }

    /**
     * @param int|null $empNumber
     * @param bool $self
     * @return bool
     * @throws CoreServiceException
     * @throws DaoException
     * @throws ServiceException
     */
    public function isPimAccessible(?int $empNumber, bool $self): bool
    {
        $menu = $this->getMenuItems($empNumber, $self);

        return count($menu) > 0;
    }

    /**
     * @param int|null $empNumber
     * @param bool $self
     * @return array
     * @throws CoreServiceException
     * @throws DaoException
     * @throws ServiceException
     */
    protected function generateMenuItems(?int $empNumber, bool $self): array
    {
        $menu = [];
        $entities = [];

        if (!empty($empNumber)) {
            $entities = ['Employee' => $empNumber];
        }

        $availableActions = $this->getAvailableActions();

        foreach ($availableActions as $action => $properties) {
            $dataGroupPermission = $this->getUserRoleManager()->getDataGroupPermissions(
                $properties['data_groups'],
                [],
                [],
                $self,
                $entities
            );
            if ($dataGroupPermission->canRead()) {
                $menu[$action] = $properties;
            } elseif ($action == 'viewJobDetails' && $this->isEmployeeWorkflowActionsAllowed($empNumber)) {
                $menu[$action] = $properties;
            }
        }

        return $menu;
    }

    /**
     * @param int|null $empNumber
     * @return bool
     * @throws DaoException
     * @throws ServiceException
     */
    protected function isEmployeeWorkflowActionsAllowed(?int $empNumber): bool
    {
        $userRoleManager = $this->getUserRoleManager();

        $employeeState = null;

        if (!empty($empNumber)) {
            $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
            if ($employee instanceof Employee) {
                $employeeState = $employee->getDecorator()->getState();
            }
        }

        $actionableStates = $userRoleManager->getActionableStates(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            [
                WorkflowStateMachine::EMPLOYEE_ACTION_TERMINATE,
                WorkflowStateMachine::EMPLOYEE_ACTION_REACTIVE
            ]
        );

        // If employee state not allowed, allow if can act on at least one state
        if (is_null($employeeState)) {
            $allowed = !empty($actionableStates);
        } else {
            $allowed = in_array($employeeState, $actionableStates);
        }

        return $allowed;
    }

    /**
     * Get PIM left menu for given employee from session cache (if available)
     *
     * @param int|null $empNumber Employee Number
     * @return array Menu array (or an empty array if not available in cache)
     */
    protected function getMenuFromCache(?int $empNumber): array
    {
        $cache = $this->getAuthUser()->getAttribute(self::PIM_LEFTMENU_SESSION_KEY, []);
        $key = empty($empNumber) ? 'default' : $empNumber;
        return $cache[$key] ?? [];
    }

    /**
     * Store menu for the given employee in the session cache.
     *
     * @param int|null $empNumber Employee Number
     * @param array $menu Menu array
     */
    protected function saveMenuInCache(?int $empNumber, array $menu): void
    {
        $user = $this->getAuthUser();
        $cache = $user->getAttribute(self::PIM_LEFTMENU_SESSION_KEY, []);
        $key = empty($empNumber) ? 'default' : $empNumber;
        $cache[$key] = $menu;
        $user->setAttribute(self::PIM_LEFTMENU_SESSION_KEY, $cache);
    }

    /**
     * @return array[]
     * @throws CoreServiceException
     */
    protected function getAvailableActions(): array
    {
        $availableActions = $this->availableActions;
        if (!$this->isTaxMenuEnabled()) {
            unset($availableActions['viewUsTaxExemptions']);
        }

        return $availableActions;
    }

    /**
     * Check if tax menu is enabled
     *
     * @return bool true if enabled, false if not
     * @throws CoreServiceException
     */
    protected function isTaxMenuEnabled(): bool
    {
        $user = $this->getAuthUser();

        if (!$user->hasAttribute(self::PIM_LEFTMENU_TAXMENU_ENABLED)) {
            $isTaxMenuEnabled = $this->getConfigService()->showPimTaxExemptions();
            $user->setAttribute(self::PIM_LEFTMENU_TAXMENU_ENABLED, $isTaxMenuEnabled);
        }

        return $user->getAttribute(self::PIM_LEFTMENU_TAXMENU_ENABLED, false);
    }

    /**
     * @param int $empNumber
     * @return array
     * @throws CoreServiceException
     * @throws DaoException
     * @throws ServiceException
     */
    public function getPreparedMenuItems(int $empNumber): array
    {
        $self = $this->getAuthUser()->getEmpNumber() === $empNumber;
        $menuItems = $this->getMenuItems($empNumber, $self);
        $menus = [];
        $currentModuleScreen = $this->getCurrentModuleAndScreen();
        $baseUrl = $this->getCurrentRequest()->getBaseUrl();


        foreach ($menuItems as $screen => $properties) {
            $url = $baseUrl . '/' . $properties['module'] . '/' . $screen . '/empNumber/' . $empNumber;
            $menus[] = [
                'name' => $this->getI18NHelper()->transBySource($properties['label']),
                'url' => $url,
                'active' => $currentModuleScreen->getScreen() === $screen
            ];
        }

        return $menus;
    }
}
