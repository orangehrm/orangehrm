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

use Exception;
use OrangeHRM\Core\Authorization\Controller\CapableViewController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Helper\VueControllerHelper;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Service\PIMLeftMenuService;

abstract class BaseViewEmployeeController extends AbstractVueController implements CapableViewController
{
    use ConfigServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @var PIMLeftMenuService|null
     */
    protected ?PIMLeftMenuService $pimLeftMenuService = null;

    /**
     * @return PIMLeftMenuService|null
     */
    public function getPimLeftMenuService(): ?PIMLeftMenuService
    {
        if (!$this->pimLeftMenuService instanceof PIMLeftMenuService) {
            $this->pimLeftMenuService = new PIMLeftMenuService();
        }
        return $this->pimLeftMenuService;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function render(Request $request): string
    {
        $empNumber = $request->get('empNumber');
        if (empty($empNumber)) {
            throw new Exception('`empNumber` required attribute for ' . __METHOD__);
        }
        $menuTabs = $this->getPimLeftMenuService()->getPreparedMenuItems($empNumber);
        $this->getComponent()->addProp(
            new Prop('tabs', Prop::TYPE_ARRAY, $menuTabs)
        );
        $this->getComponent()->addProp(
            new Prop('allowed-file-types', Prop::TYPE_ARRAY, $this->getConfigService()->getAllowedFileTypes())
        );
        $this->getComponent()->addProp(
            new Prop('max-file-size', Prop::TYPE_NUMBER, $this->getConfigService()->getMaxAttachmentSize())
        );
        return parent::render($request);
    }

    /**
     * @return string[]
     */
    protected function getDataGroupsForCapabilityCheck(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function isCapable(Request $request): bool
    {
        $permission = $this->getUserRoleManagerHelper()->getDataGroupPermissionsForEmployee(
            $this->getDataGroupsForCapabilityCheck(),
            $request->get('empNumber')
        );
        return $permission->canRead();
    }

    /**
     * @param array $dataGroups
     * @param int $empNumber
     * @throws Exception
     */
    protected function setPermissionsForEmployee(array $dataGroups, int $empNumber)
    {
        $permissions = $this->getUserRoleManagerHelper()->getDataGroupPermissionCollectionForEmployee(
            $dataGroups,
            $empNumber
        );
        $this->getContext()->set(
            VueControllerHelper::PERMISSIONS,
            $permissions->toArray()
        );
    }
}
