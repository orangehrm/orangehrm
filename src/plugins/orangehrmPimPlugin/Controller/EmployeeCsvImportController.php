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

namespace OrangeHRM\Pim\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Api\EmployeeCSVImportAPI;

class EmployeeCsvImportController extends AbstractVueController
{
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('employee-data-import');
        $component->addProp(new Prop('allowed-file-types', Prop::TYPE_ARRAY, EmployeeCSVImportAPI::PARAM_RULE_IMPORT_FILE_FORMAT));
        $component->addProp(new Prop('max-file-size', Prop::TYPE_NUMBER, $this->getConfigService()->getMaxAttachmentSize()));
        $this->setComponent($component);
    }
}
