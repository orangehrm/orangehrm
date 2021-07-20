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

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Framework\Http\Request;

class EmployeeProfilePictureController extends BaseViewEmployeeController
{
    public function preRender(Request $request): void
    {
        $empNumber = $request->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-profile-picture');
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $empNumber));
            $component->addProp(new Prop('allowed-image-types', Prop::TYPE_ARRAY, EmpPicture::ALLOWED_IMAGE_TYPES));
            $this->setComponent($component);

            $this->setPermissionsForEmployee(['photograph'], $empNumber);
        } else {
            $this->handleBadRequest();
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDataGroupsForCapabilityCheck(): array
    {
        return ['photograph'];
    }
}
