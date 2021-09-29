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

namespace OrangeHRM\Leave\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class LeaveRequestController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $id = $request->get('id');
        $mode = $request->get('mode');
        // TODO: 404 if no id
        if (!$id) die;

        $component = new Component('leave-view-request');
        $component->addProp(new Prop('leave-request-id', Prop::TYPE_NUMBER, $id));
        if($mode && $mode == "my-leave") {
            $component->addProp(new Prop('my-leave-request', Prop::TYPE_BOOLEAN, true));
        }
        $this->setComponent($component);
    }
}
