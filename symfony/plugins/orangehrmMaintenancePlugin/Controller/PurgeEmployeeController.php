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

namespace OrangeHRM\Maintenance\Controller;

use OrangeHRM\Authentication\Controller\AdminPrivilegeControllerInterface;
use OrangeHRM\Authentication\Controller\Traits\AdministratorAccessTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class PurgeEmployeeController extends AbstractVueController implements AdminPrivilegeControllerInterface
{
    use AuthUserTrait;
    use AdministratorAccessTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('purge-employee');
        $this->setComponent($component);
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request)
    {
        if (!$this->getAuthUser()->getHasAdminAccess()) {
            return $this->forwardToAdministratorAccess($request);
        }
        return parent::handle($request);
    }
}
