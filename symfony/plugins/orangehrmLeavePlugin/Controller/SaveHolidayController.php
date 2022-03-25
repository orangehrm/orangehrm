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
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class SaveHolidayController extends AbstractVueController
{
    use ConfigServiceTrait;

    public const HOLIDAY_LENGTH_LIST = [
        ['id' => Holiday::HOLIDAY_HALF_DAY_LENGTH, 'label' => Holiday::HOLIDAY_HALF_DAY_LENGTH_NAME],
        ['id' => Holiday::HOLIDAY_FULL_DAY_LENGTH, 'label' => Holiday::HOLIDAY_FULL_DAY_LENGTH_NAME],
    ];

    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('holiday-edit');
            $component->addProp(new Prop('holiday-id', Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
        } else {
            $component = new Component('holiday-save');
        }
        $component->addProp(new Prop('holiday-length-list', Prop::TYPE_ARRAY, self::HOLIDAY_LENGTH_LIST));
        $this->setComponent($component);
    }
}
