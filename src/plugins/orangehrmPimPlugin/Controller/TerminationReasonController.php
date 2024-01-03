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
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Service\TerminationReasonConfigurationService;

class TerminationReasonController extends AbstractVueController
{
    /**
     * @var TerminationReasonConfigurationService|null
     */
    protected ?TerminationReasonConfigurationService $terminationReasonService = null;

    /**
     * @return TerminationReasonConfigurationService
     */
    protected function getTerminationReasonService(): TerminationReasonConfigurationService
    {
        if (!$this->terminationReasonService instanceof TerminationReasonConfigurationService) {
            $this->terminationReasonService = new TerminationReasonConfigurationService();
        }
        return $this->terminationReasonService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('termination-reason-list');
        $reasonsInUse = $this->getTerminationReasonService()->getReasonIdsInUse();
        $component->addProp(new Prop('unselectable-ids', Prop::TYPE_ARRAY, $reasonsInUse));
        $this->setComponent($component);
    }
}
