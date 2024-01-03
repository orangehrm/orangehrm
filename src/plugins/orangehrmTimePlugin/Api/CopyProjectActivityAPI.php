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
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Project;
use OrangeHRM\Time\Api\Model\CopyActivityModel;
use OrangeHRM\Time\Dto\ProjectActivitySearchFilterParams;
use OrangeHRM\Time\Exception\ProjectServiceException;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;

class CopyProjectActivityAPI extends Endpoint implements CollectionEndpoint
{
    use ProjectServiceTrait;

    public const PARAMETER_FROM_PROJECT_ID = 'fromProjectId';
    public const PARAMETER_TO_PROJECT_ID = 'toProjectId';
    public const PARAMETER_ACTIVITY_IDS = 'activityIds';

    /**
     * @OA\Get(
     *     path="/api/v2/time/projects/{toProjectId}/activities/copy/{fromProjectId}",
     *     tags={"Time/Copy Project Activity"},
     *     summary="List Copyable Activities Between Two Projects",
     *     operationId="list-copyable-activities-between-two-projects",
     *     @OA\PathParameter(
     *         name="toProjectId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="fromProjectId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ProjectActivitySearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Time-CopyActivityModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        list($toProjectId, $fromProjectId) = $this->getUrlAttributes();
        $projectActivitySearchFilterParams = new ProjectActivitySearchFilterParams();
        $this->setSortingAndPaginationParams($projectActivitySearchFilterParams);
        $projectActivitiesForFromProject = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityListByProjectId($fromProjectId, $projectActivitySearchFilterParams);
        $duplicateActivities = $this->getProjectService()
            ->getProjectActivityDao()
            ->getDuplicatedActivities($fromProjectId, $toProjectId);

        $projectActivityCount = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityCount($fromProjectId, $projectActivitySearchFilterParams);

        return new EndpointCollectionResult(
            CopyActivityModel::class,
            [$projectActivitiesForFromProject, $duplicateActivities],
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $projectActivityCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonURLValidationRules(),
            ...$this->getSortingAndPaginationParamsRules(ProjectActivitySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/time/projects/{toProjectId}/activities/copy/{fromProjectId}",
     *     tags={"Time/Copy Project Activity"},
     *     summary="Copy Activities From One Project",
     *     operationId="copy-activities-from-one-project",
     *     @OA\PathParameter(
     *         name="toProjectId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="fromProjectId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="activityIds",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                 )
     *             ),
     *             required={"activityIds"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Already exist",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Already exist")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        try {
            list($toProjectId, $fromProjectId) = $this->getUrlAttributes();
            $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTIVITY_IDS);
            $this->getProjectService()->validateProjectActivityName($toProjectId, $fromProjectId, $ids);
            $this->getProjectService()->getProjectActivityDao()->copyActivities($toProjectId, $ids);

            return new EndpointResourceResult(ArrayModel::class, $ids);
        } catch (ProjectServiceException $projectServiceException) {
            throw $this->getBadRequestException($projectServiceException->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_ACTIVITY_IDS,
                    new Rule(Rules::ARRAY_TYPE),
                    new Rule(
                        Rules::EACH,
                        [new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))]
                    )
                )
            ),
            ...$this->getCommonURLValidationRules()
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

    /**
     * @return ParamRule[]
     */
    private function getCommonURLValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TO_PROJECT_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [Project::class])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_FROM_PROJECT_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [Project::class])
                )
            )
        ];
    }

    /**
     * @return array
     */
    private function getUrlAttributes(): array
    {
        $fromProjectId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_FROM_PROJECT_ID
        );

        $toProjectId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TO_PROJECT_ID
        );
        return [$toProjectId, $fromProjectId];
    }
}
