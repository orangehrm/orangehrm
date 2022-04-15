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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class SaveWorkShiftController extends AbstractVueController
{
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('work-shift-edit');
            $component->addProp(new Prop('work-shift-id', Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
        } else {
            $workShiftConfig = [
                "startTime" => $this->getConfigService()->getDefaultWorkShiftStartTime(),
                "endTime" => $this->getConfigService()->getDefaultWorkShiftEndTime(),
            ];
            $component = new Component('work-shift-save');
            $component->addProp(new Prop('work-shift-config', Prop::TYPE_OBJECT, $workShiftConfig));
        }
        $this->setComponent($component);
    }
}
