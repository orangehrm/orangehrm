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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class SaveEmailSubscriptionController extends AbstractVueController
{
    /**
     * @param Request $request
     * @throws \OrangeHRM\Core\Controller\Exception\VueControllerException
     */
    public function preRender(Request $request): void
    {
        // TODO: throw error if id not set
        if ($request->attributes->has('id')) {
            $component = new Component('email-subscription-edit');
            $component->addProp(new Prop('subscription-id', Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
            $this->setComponent($component);
        }
    }
}
