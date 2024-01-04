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

namespace OrangeHRM\Time\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Time\Api\Model\TimesheetItemModel;
use OrangeHRM\Time\Api\Traits\TimesheetPermissionTrait;
use OrangeHRM\Time\Api\ValidationRules\TimesheetCommentDateParamRule;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class TimesheetCommentAPI extends Endpoint implements ResourceEndpoint
{
    use TimesheetServiceTrait;
    use TimesheetPermissionTrait;
    use ProjectServiceTrait;

    public const PARAMETER_PROJECT_ID = 'projectId';
    public const PARAMETER_TIMESHEET_ID = 'timesheetId';
    public const PARAMETER_PROJECT_ACTIVITY_ID = 'activityId';
    public const PARAMETER_DATE = 'date';
    public const PARAMETER_COMMENT = 'comment';

    public const PARAM_RULE_COMMENT_MAX_LENGTH = 2000;

    /**
     * @OA\Put(
     *     path="/api/v2/time/timesheets/{timesheetId}/entries/comment",
     *     tags={"Time/Timesheet Comment"},
     *     summary="Update a Timesheet Comment",
     *     operationId="update-a-timesheet-comment",
     *     @OA\PathParameter(
     *         name="timesheetId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="projectId", type="integer"),
     *             @OA\Property(property="activityId", type="integer"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 maxLength=OrangeHRM\Time\Api\TimesheetCommentAPI::PARAM_RULE_COMMENT_MAX_LENGTH
     *             ),
     *             required={"projectId", "projectId", "date"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-TimesheetItemModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $timesheetId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TIMESHEET_ID
        );
        $this->checkAuthorityAccessible($timesheetId);

        $date = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE);
        $projectId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PROJECT_ID);
        $project = $this->getProjectService()->getProjectDao()->getProjectById($projectId);
        $this->throwRecordNotFoundExceptionIfNotExist($project, Project::class);

        $projectActivityId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_PROJECT_ACTIVITY_ID
        );
        $projectActivity = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityByProjectIdAndProjectActivityId($projectId, $projectActivityId);
        $this->throwRecordNotFoundExceptionIfNotExist($projectActivity, ProjectActivity::class);

        $timesheetItem = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getTimesheetItemByProjectIdAndTimesheetIdAndActivityIdAndDate(
                $timesheetId,
                $projectId,
                $projectActivityId,
                $date
            );

        if (is_null($timesheetItem)) {
            $timesheetItem = new TimesheetItem();
        }
        $this->setCommentToTimesheetItem($timesheetItem);
        $timesheetItem->getDecorator()->setTimesheetById($timesheetId);
        $timesheetItem->setDate($date);
        $timesheetItem->getDecorator()->setProjectById($projectId);
        $timesheetItem->getDecorator()->setProjectActivityById($projectActivityId);
        $timesheet = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getTimesheetById($timesheetId);
        $timesheetItem->getDecorator()->setEmployeeByEmployeeNumber($timesheet->getEmployee()->getEmpNumber());

        $this->getTimesheetService()->getTimesheetDao()->saveTimesheetItem($timesheetItem);
        return new EndpointResourceResult(TimesheetItemModel::class, $timesheetItem);
    }

    /**
     * @param TimesheetItem $timesheetItem
     * @return void
     */
    private function setCommentToTimesheetItem(TimesheetItem $timesheetItem): void
    {
        $comment = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COMMENT);
        $timesheetItem->setComment($comment);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROJECT_ID,
                    new Rule(Rules::POSITIVE),
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROJECT_ACTIVITY_ID,
                    new Rule(Rules::POSITIVE),
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_DATE,
                    new Rule(Rules::API_DATE),
                    new Rule(
                        TimesheetCommentDateParamRule::class,
                        [$this->getRequest()->getAttributes()->get(self::PARAMETER_TIMESHEET_ID)]
                    ),
                ),
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TIMESHEET_ID,
                    new Rule(Rules::POSITIVE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENT_MAX_LENGTH])
                ),
                true
            ),
        ];
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

    /**
     * @OA\Get(
     *     path="/api/v2/time/timesheets/{timesheetId}/entries/{id}/comment",
     *     tags={"Time/Timesheet Comment"},
     *     summary="Get a Timesheet Comment",
     *     operationId="get-a-timesheet-comment",
     *     @OA\PathParameter(
     *         name="timesheetId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-TimesheetItemModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $timesheetItemId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $timesheetId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TIMESHEET_ID
        );
        $this->checkAuthorityAccessible($timesheetId);
        $timesheetItem = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getTimesheetItemByTimesheetIdAndTimesheetItemId($timesheetId, $timesheetItemId);
        $this->throwRecordNotFoundExceptionIfNotExist($timesheetItem, TimesheetItem::class);

        return new EndpointResourceResult(TimesheetItemModel::class, $timesheetItem);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TIMESHEET_ID,
                    new Rule(Rules::POSITIVE),
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_ID,
                    new Rule(Rules::POSITIVE)
                ),
            ),
        );
    }

    /**
     * @param int $timesheetId
     * @return void
     * @throws ForbiddenException
     * @throws RecordNotFoundException
     */
    private function checkAuthorityAccessible(int $timesheetId): void
    {
        $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($timesheetId);
        $this->throwRecordNotFoundExceptionIfNotExist($timesheet, Timesheet::class);
        $this->checkTimesheetAccessible($timesheet);
    }
}
