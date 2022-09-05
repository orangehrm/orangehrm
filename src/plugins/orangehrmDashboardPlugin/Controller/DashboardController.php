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

namespace OrangeHRM\Dashboard\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Service\ModuleService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Helper\VueControllerHelper;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;

class DashboardController extends AbstractVueController
{
    use AuthUserTrait;
    use ServiceContainerTrait;

    /**
     * @return ModuleService
     */
    public function getModuleService(): ModuleService
    {
        return $this->getContainer()->get(Services::MODULE_SERVICE);
    }

    public function preRender(Request $request): void
    {
        $component = new Component('view-dashboard');
        $this->setComponent($component);

        $isLeaveModuleEnabled = false;

        foreach ($this->getModuleService()->getModuleList() as $module) {
            if ($module->getName() === 'leave') {
                $isLeaveModuleEnabled = $module->getStatus();
                break;
            }
        }

        // TODO: Rebase data group permisssions
        $this->getContext()->set(
            VueControllerHelper::PERMISSIONS,
            [
                'admin_widgets' => [
                    'canRead' => $this->getAuthUser()->getUserRoleName() === 'Admin',
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false
                ],
                'leave_widget' => [
                    'canRead' => $isLeaveModuleEnabled,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false
                ]
            ]
        );
    }
}
