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
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Leave\Api\Model\LeaveCommentModel;
use OrangeHRM\Leave\Dto\LeaveCommentSearchFilterParams;
use OrangeHRM\Leave\Service\LeaveRequestCommentService;

class LeaveCommentAPI extends Endpoint implements CollectionEndpoint
{
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;
    use EntityManagerHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_LEAVE_ID = 'leaveId';
    public const PARAMETER_COMMENT = 'comment';

    public const PARAM_RULE_COMMENT_MAX_LENGTH = 255;
    /**
     * @var null|LeaveRequestCommentService
     */
    protected ?LeaveRequestCommentService $leaveRequestCommentService = null;

    /**
     * @return LeaveRequestCommentService
     */
    public function getLeaveRequestCommentService(): LeaveRequestCommentService
    {
        if (is_null($this->leaveRequestCommentService)) {
            $this->leaveRequestCommentService = new LeaveRequestCommentService();
        }
        return $this->leaveRequestCommentService;
    }

    /**
     * @return int|null
     */
    private function getUrlAttributes(): ?int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LEAVE_ID
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $leaveId = $this->getUrlAttributes();

        /** @var Leave|null $leave */
        $leave = $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
            ->getLeaveById($leaveId);

        $this->throwRecordNotFoundExceptionIfNotExist($leave, Leave::class);

        $this->checkLeaveAccessible($leave);

        $leaveCommentSearchFilterParams = new LeaveCommentSearchFilterParams();

        $leaveCommentSearchFilterParams->setLeaveId($leaveId);
        $this->setSortingAndPaginationParams($leaveCommentSearchFilterParams);

        $leaveComments = $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
            ->searchLeaveComments($leaveCommentSearchFilterParams);
        return new EndpointCollectionResult(
            LeaveCommentModel::class,
            $leaveComments,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
                        ->getSearchLeaveCommentsCount($leaveCommentSearchFilterParams)
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonValidationRules(),
            ...$this->getSortingAndPaginationParamsRules(LeaveCommentSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $leaveId = $this->getUrlAttributes();

        /** @var Leave|null $leave */
        $leave = $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()->getLeaveById(
            $leaveId
        );

        $this->throwRecordNotFoundExceptionIfNotExist($leave, Leave::class);

        $this->checkLeaveAccessible($leave);

        $leaveComment = new LeaveComment();
        $leaveComment->getDecorator()->setLeaveById($leaveId);
        $this->setLeaveComment($leaveComment);
        $leaveComment = $this->saveLeaveComment($leaveComment);
        return new EndpointResourceResult(
            LeaveCommentModel::class,
            $leaveComment,
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_COMMENT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENT_MAX_LENGTH]),
            ),
            ...$this->getCommonValidationRules()
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_LEAVE_ID,
                new Rule(Rules::POSITIVE)
            )
        ];
    }

    /**
     * @param LeaveComment $leaveComment
     */
    private function setLeaveComment(LeaveComment $leaveComment): void
    {
        $comment = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMMENT
        );
        $leaveComment->setComment($comment);
        $leaveComment->setCreatedAt($this->getDateTimeHelper()->getNow());
        $leaveComment->getDecorator()->setCreatedByEmployeeByEmpNumber($this->getAuthUser()->getEmpNumber());
        $leaveComment->getDecorator()->setCreatedByUserById($this->getAuthUser()->getUserId());
    }

    /**
     * @param LeaveComment $leaveComment
     * @return LeaveComment
     * @throws Exception
     */
    private function saveLeaveComment(LeaveComment $leaveComment): LeaveComment
    {
        return $this->getLeaveRequestCommentService()
            ->getLeaveRequestCommentDao()
            ->saveLeaveComment($leaveComment);
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
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

    /**
     * @param Leave $leave
     * @throws ForbiddenException
     */
    protected function checkLeaveAccessible(Leave $leave): void
    {
        $empNumber = $leave->getEmployee()->getEmpNumber();
        if (!($this->getUserRoleManager()->isEntityAccessible(Employee::class, $empNumber) ||
            $this->getUserRoleManagerHelper()->isSelfByEmpNumber($empNumber))) {
            throw $this->getForbiddenException();
        }
    }
}
