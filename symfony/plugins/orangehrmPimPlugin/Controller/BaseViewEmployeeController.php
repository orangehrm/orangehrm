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
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Service\PIMLeftMenuService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

abstract class BaseViewEmployeeController extends AbstractVueController
{
    use ConfigServiceTrait;

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
        $this->getComponent()->addProp(new Prop('allowed-file-types', Prop::TYPE_ARRAY, $this->getConfigService()->getAllowedFileTypes()));
        return parent::render($request);
    }
}
