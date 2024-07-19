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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Api\Model\JobCategoryModel;
use OrangeHRM\Admin\Service\JobCategoryService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Entity\JobCategory;

class JobCategoryAPI extends Endpoint implements CrudEndpoint
{
    /**
     * @var null|JobCategoryService
     */
    protected ?JobCategoryService $jobCategoryService = null;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';
    public const PARAM_RULE_NAME_MAX_LENGTH = 50;

    /**
     * @return JobCategoryService
     */
    public function getJobCategoryService(): JobCategoryService
    {
        if (is_null($this->jobCategoryService)) {
            $this->jobCategoryService = new JobCategoryService();
        }
        return $this->jobCategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/job-categories/{id}",
     *     tags={"Admin/Job Category"},
     *     summary="Get a Job Category",
     *     operationId="get-a-job-category",
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
     *                 ref="#/components/schemas/Admin-JobCategoryModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $jobCategory = $this->getJobCategoryService()->getJobCategoryById($id);
        if (!$jobCategory instanceof JobCategory) {
            throw new RecordNotFoundException();
        }

        return new EndpointResourceResult(JobCategoryModel::class, $jobCategory);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/job-categories",
     *     tags={"Admin/Job Category"},
     *     summary="List All Job Categories",
     *     operationId="list-all-job-categories",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"jc.name"})
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
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Admin-JobCategoryModel")
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
    public function getAll(): EndpointCollectionResult
    {
        $sortField = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_FIELD,
            'jc.name'
        );
        $sortOrder = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_ORDER,
            'ASC'
        );
        $limit = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_OFFSET, 0);

        $count = $this->getJobCategoryService()->getJobCategoryList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            true
        );

        $jobCategories = $this->getJobCategoryService()->getJobCategoryList($sortField, $sortOrder, $limit, $offset);

        return new EndpointCollectionResult(
            JobCategoryModel::class,
            $jobCategories,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(['jc.name'])
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/job-categories",
     *     tags={"Admin/Job Category"},
     *     summary="Create a Job Category",
     *     operationId="create-a-job-category",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\JobCategoryAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-JobCategoryModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $jobCategory = new JobCategory();
        $jobCategory = $this->saveJobCategory($jobCategory);

        return new EndpointResourceResult(JobCategoryModel::class, $jobCategory);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getNameRule()
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/job-categories/{id}",
     *     tags={"Admin/Job Category"},
     *     summary="Update a Job Category",
     *     operationId="update-a-job-category",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\JobCategoryAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-JobCategoryModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $jobCategory = $this->getJobCategoryService()->getJobCategoryById($this->getAttributeId());
        $this->throwRecordNotFoundExceptionIfNotExist($jobCategory, JobCategory::class);
        $jobCategory = $this->saveJobCategory($jobCategory);
        return new EndpointResourceResult(JobCategoryModel::class, $jobCategory);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getNameRule($uniqueOption)
        );
    }

    /**
     * @param JobCategory $jobCategory
     * @return JobCategory
     */
    private function saveJobCategory(JobCategory $jobCategory): JobCategory
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $jobCategory->setName($name);
        return $this->getJobCategoryService()->saveJobCategory($jobCategory);
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule
     */
    private function getNameRule(?EntityUniquePropertyOption $uniqueOption = null): ParamRule
    {
        return $this->getValidationDecorator()->requiredParamRule(
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [JobCategory::class, 'name', $uniqueOption])
            )
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/job-categories",
     *     tags={"Admin/Job Category"},
     *     summary="Delete Job Categories",
     *     operationId="delete-job-categories",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getJobCategoryService()->getJobCategoryDao()->getExistingJobCategoryIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getJobCategoryService()->deleteJobCategory($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
