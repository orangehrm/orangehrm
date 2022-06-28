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

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;

class CandidateActionController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        // TODO: Validate candidateId & actionId & permission
        $candidateId = $request->query->getInt('candidateId');
        $actionId = $request->query->getInt('selectedAction');
        $interviewId = $request->query->getInt('interviewId');
        switch ($actionId) {
            case 2:
                $component = new Component('shortlist-action');
                break;
            case 3:
                $component = new Component('reject-action');
                break;
            case 4:
                $component = new Component('interview-schedule-action');
                break;
            case 5:
                $component = new Component('interview-passed-action');
                break;
            case 6:
                $component = new Component('interview-failed-action');
                break;
            case 7:
                $component = new Component('offer-job-action');
                break;
            case 8:
                $component = new Component('offer-decline-action');
                break;
            case 9:
                $component = new Component('hire-action');
                break;
            default:
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        if ($interviewId) {
            $component->addProp(new Prop('interview-id', Prop::TYPE_NUMBER, $interviewId));
        }
        $component->addProp(new Prop('candidate-id', Prop::TYPE_NUMBER, $candidateId));
        $this->setComponent($component);
    }
}
