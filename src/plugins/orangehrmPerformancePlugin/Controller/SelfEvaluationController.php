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

use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class SelfEvaluationController extends AdminEvaluationController
{
    use PerformanceReviewServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $id = $request->attributes->getInt('id');
            $component = new Component('self-evaluation');

            $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()->getPerformanceReviewById($id);
            if (!is_null($review)) {
                $this->setReviewProps($component, $review);
            }
            $this->setComponent($component);
        } else {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
    }

    /**
     * @inheritDoc
     */
    public function isCapable(Request $request): bool
    {
        $id = $request->attributes->getInt('id');
        $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()->getPerformanceReviewById($id);
        if (is_null($review)
            || ($review->getEmployee() instanceof Employee
                && !is_null($review->getEmployee()->getPurgedAt()))) {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        return $this->getUserRoleManagerHelper()->isSelfByEmpNumber($review->getEmployee()->getEmpNumber());
    }
}
