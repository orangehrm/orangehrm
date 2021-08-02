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

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Service\CustomFieldService;

class SaveCustomFieldController extends AbstractVueController
{
    /**
     * @var null|CustomFieldService
     */
    protected ?CustomFieldService $customFieldService = null;

    /**
     * @return CustomFieldService
     */
    public function getCustomFieldService(): CustomFieldService
    {
        if (is_null($this->customFieldService)) {
            $this->customFieldService = new CustomFieldService();
        }
        return $this->customFieldService;
    }

    public function preRender(Request $request): void
    {
        $id = $request->get('id');
        if ($id) {
            $component = new Component('custom-field-edit');
            $component->addProp(new Prop('custom-field-id', Prop::TYPE_NUMBER, $id));
            $component->addProp(new Prop('field-in-use', Prop::TYPE_ARRAY, $this->getCustomFieldService()->getCustomFieldDao()->isCustomFieldInUse($id)));
        } else {
            $component = new Component('custom-field-save');
        }
        $component->addProp(new Prop('screen-list', Prop::TYPE_ARRAY, CustomFieldController::SCREEN_LIST));
        $component->addProp(new Prop('field-type-list', Prop::TYPE_ARRAY, CustomFieldController::FIELD_TYPE_LIST));
        $this->setComponent($component);
    }
}
