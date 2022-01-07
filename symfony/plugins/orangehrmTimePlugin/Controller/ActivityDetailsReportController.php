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

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class ActivityDetailsReportController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('activity-details-report');

        if ($request->query->has('fromDate') && $request->query->has('toDate')) {
            $component->addProp(new Prop('from-date', Prop::TYPE_STRING, $request->query->get('fromDate')));
            $component->addProp(new Prop('to-date', Prop::TYPE_STRING, $request->query->get('toDate')));
        }

        if ($request->query->has('projectId')) {
            // TODO: Get project object
            $component->addProp(
                new Prop(
                    'project',
                    Prop::TYPE_OBJECT,
                    [
                        'id' => $request->query->getInt('projectId'),
                        'label' => 'Project Manhattan'
                    ]
                )
            );
        }

        if ($request->query->has('activityId')) {
            // TODO: Get activity object
            $component->addProp(
                new Prop(
                    'activity',
                    Prop::TYPE_OBJECT,
                    [
                        'id' => $request->query->getInt('activityId'),
                        'label' => 'Develop timesheet component'
                    ]
                )
            );
        }

        if ($request->query->has('includeTimesheet') && $request->query->get('includeTimesheet') == 'onlyApproved') {
            $component->addProp(
                new Prop(
                    'include-timesheet',
                    Prop::TYPE_BOOLEAN,
                    true
                )
            );
        } else {
            $component->addProp(
                new Prop(
                    'include-timesheet',
                    Prop::TYPE_BOOLEAN,
                    false
                )
            );
        }

        $this->setComponent($component);
    }
}
