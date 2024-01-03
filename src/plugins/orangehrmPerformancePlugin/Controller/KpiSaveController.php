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

namespace OrangeHRM\Performance\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Performance\Traits\Service\KpiServiceTrait;

class KpiSaveController extends AbstractVueController
{
    use KpiServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('kpi-edit');
            $component->addProp(new Prop('kpi-id', Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
        } else {
            $component = new Component('kpi-save');

            $defaultKpi = $this->getKpiService()->getKpiDao()->getDefaultKpi();
            if ($defaultKpi) {
                $component->addProp(new Prop('default-min-rating', Prop::TYPE_NUMBER, $defaultKpi->getMinRating()));
                $component->addProp(new Prop('default-max-rating', Prop::TYPE_NUMBER, $defaultKpi->getMaxRating()));
            }
        }
        $this->setComponent($component);
    }
}
