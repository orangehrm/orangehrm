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

namespace OrangeHRM\Leave\Api;

use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Leave\Api\Model\LeaveModel;
use OrangeHRM\Leave\Api\Traits\LeavePermissionTrait;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;
use OrangeHRM\ORM\Exception\TransactionException;

class BulkLeaveAPI extends Endpoint implements ResourceEndpoint
{
    use LeaveRequestServiceTrait;
    use EntityManagerHelperTrait;
    use LeavePermissionTrait;

    public const PARAMETER_LEAVE_ID = 'leaveId';
    public const PARAMETER_ACTION = 'action';
    public const PARAMETER_DATA = 'data';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leavesIdActionMap = $this->getLeavesIdActionMap();

        $this->beginTransaction();
        try {
            $leaves = $this->getLeaveRequestService()
                ->getLeaveRequestDao()
                ->getLeavesByLeaveIds(array_keys($leavesIdActionMap));

            if (count($leavesIdActionMap) !== count($leaves)) {
                throw $this->getRecordNotFoundException();
            }
            foreach ($leaves as $leave) {
                $this->checkLeaveAccessible($leave);
            }

            $detailedLeaves = array_map(fn (Leave $leave) => new DetailedLeave($leave), $leaves);

            /** @var array<string, DetailedLeave[]> $detailedLeavesGroupByAction */
            $detailedLeavesGroupByAction = [];
            /** @var array<string, Leave[]> $leavesGroupByAction */
            $leavesGroupByAction = [];

            foreach ($detailedLeaves as $detailedLeave) {
                $action = $leavesIdActionMap[$detailedLeave->getLeave()->getId()];
                if (!$detailedLeave->isActionAllowed($action)) {
                    throw $this->getBadRequestException('Performed action not allowed');
                }

                if (!isset($detailedLeavesGroupByAction[$action])) {
                    $detailedLeavesGroupByAction[$action] = [];
                    $leavesGroupByAction[$action] = [];
                }
                $detailedLeavesGroupByAction[$action][] = $detailedLeave;
                $leavesGroupByAction[$action][] = $detailedLeave->getLeave();
            }

            foreach ($detailedLeavesGroupByAction as $action => $detailedLeaves) {
                $workflow = $detailedLeaves[0]->getWorkflowForAction($action);

                $this->getLeaveRequestService()->changeLeavesStatus($leavesGroupByAction[$action], $workflow);
            }

            $this->commitTransaction();

            return new EndpointCollectionResult(LeaveModel::class, $leaves);
        } catch (RecordNotFoundException | ForbiddenException | BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @return array<int, string> e.g. array(leaveId => action)
     * @throws BadRequestException
     */
    private function getLeavesIdActionMap(): array
    {
        $leavesData = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DATA
        );

        $leavesIdActionMap = [];
        foreach ($leavesData as $leaveRequestData) {
            if (isset($leavesIdActionMap[$leaveRequestData[self::PARAMETER_LEAVE_ID]])) {
                throw $this->getBadRequestException(
                    'Multiple actions defined for the leave id: ' .
                    $leaveRequestData[self::PARAMETER_LEAVE_ID]
                );
            }
            $leavesIdActionMap[$leaveRequestData[self::PARAMETER_LEAVE_ID]] = $leaveRequestData[self::PARAMETER_ACTION];
        }
        return $leavesIdActionMap;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_DATA,
                new Rule(Rules::ARRAY_TYPE),
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(
                                Rules::KEY,
                                [self::PARAMETER_LEAVE_ID, new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))]
                            ),
                            new Rule(
                                Rules::KEY,
                                [self::PARAMETER_ACTION, new Rules\Composite\AllOf(new Rule(Rules::STRING_TYPE))]
                            ),
                        )
                    ]
                )
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
