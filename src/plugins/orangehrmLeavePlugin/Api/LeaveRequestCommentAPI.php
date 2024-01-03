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

namespace OrangeHRM\Leave\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Leave\Api\Model\LeaveRequestCommentModel;
use OrangeHRM\Leave\Api\Traits\LeaveRequestPermissionTrait;
use OrangeHRM\Leave\Dto\LeaveRequestCommentSearchFilterParams;
use OrangeHRM\Leave\Service\LeaveRequestCommentService;

class LeaveRequestCommentAPI extends Endpoint implements CollectionEndpoint
{
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;
    use AuthUserTrait;
    use LeaveRequestPermissionTrait;

    public const PARAMETER_LEAVE_REQUEST_ID = 'leaveRequestId';
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
            self::PARAMETER_LEAVE_REQUEST_ID
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-requests/{leaveRequestId}/leave-comments",
     *     tags={"Leave/Leave Request Comment"},
     *     summary="List Comments for a Leave Request",
     *     operationId="list-comments-for-a-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveRequestCommentModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $leaveRequestId = $this->getUrlAttributes();

        /** @var LeaveRequest|null $leaveRequest */
        $leaveRequest = $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
            ->getLeaveRequestById($leaveRequestId);

        $this->throwRecordNotFoundExceptionIfNotExist($leaveRequest, LeaveRequest::class);

        $this->checkLeaveRequestAccessible($leaveRequest);

        $leaveRequestCommentSearchFilterParams = new LeaveRequestCommentSearchFilterParams();

        $leaveRequestCommentSearchFilterParams->setLeaveRequestId($leaveRequestId);
        $this->setSortingAndPaginationParams($leaveRequestCommentSearchFilterParams);

        $leaveRequestComments = $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
            ->searchLeaveRequestComments($leaveRequestCommentSearchFilterParams);
        return new EndpointCollectionResult(
            LeaveRequestCommentModel::class,
            $leaveRequestComments,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
                        ->getSearchLeaveRequestCommentsCount($leaveRequestCommentSearchFilterParams)
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
            ...$this->getSortingAndPaginationParamsRules(LeaveRequestCommentSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/leave/leave-requests/{leaveRequestId}/leave-comments",
     *     tags={"Leave/Leave Request Comment"},
     *     summary="Comment on a Leave Request",
     *     operationId="comment-on-a-leave-request",
     *     @OA\PathParameter(
     *         name="leaveRequestId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 maxLength=OrangeHRM\Leave\Api\LeaveRequestCommentApi::PARAM_RULE_COMMENT_MAX_LENGTH
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveRequestCommentModel"
     *             ),
     *             @OA\Property(property="meta", type="object"),
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $leaveRequestId = $this->getUrlAttributes();

        /** @var LeaveRequest|null $leaveRequest */
        $leaveRequest = $this->getLeaveRequestCommentService()->getLeaveRequestCommentDao()
            ->getLeaveRequestById($leaveRequestId);

        $this->throwRecordNotFoundExceptionIfNotExist($leaveRequest, LeaveRequest::class);

        $this->checkLeaveRequestAccessible($leaveRequest);

        $leaveRequestComment = new LeaveRequestComment();
        $leaveRequestComment->getDecorator()->setLeaveRequestById($leaveRequestId);
        $this->setLeaveRequestComment($leaveRequestComment);
        $leaveRequestComment = $this->saveLeaveRequestComment($leaveRequestComment);
        return new EndpointResourceResult(
            LeaveRequestCommentModel::class,
            $leaveRequestComment,
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
                self::PARAMETER_LEAVE_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            )
        ];
    }

    /**
     * @param LeaveRequestComment $leaveRequestComment
     */
    private function setLeaveRequestComment(LeaveRequestComment $leaveRequestComment): void
    {
        $comment = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMMENT
        );
        $leaveRequestComment->setComment($comment);
        $leaveRequestComment->setCreatedAt($this->getDateTimeHelper()->getNow());
        $leaveRequestComment->getDecorator()->setCreatedByEmployeeByEmpNumber($this->getAuthUser()->getEmpNumber());
        $leaveRequestComment->getDecorator()->setCreatedByUserById($this->getAuthUser()->getUserId());
    }

    /**
     * @param LeaveRequestComment $leaveRequestComment
     * @return LeaveRequestComment
     * @throws Exception
     */
    private function saveLeaveRequestComment(LeaveRequestComment $leaveRequestComment): LeaveRequestComment
    {
        return $this->getLeaveRequestCommentService()
            ->getLeaveRequestCommentDao()
            ->saveLeaveRequestComment($leaveRequestComment);
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
}
