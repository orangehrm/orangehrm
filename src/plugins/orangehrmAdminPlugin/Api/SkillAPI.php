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

use Exception;
use OrangeHRM\Admin\Api\Model\SkillModel;
use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
use OrangeHRM\Admin\Service\SkillService;
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
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\Skill;

class SkillAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';

    public const FILTER_NAME = 'name';
    public const FILTER_DESCRIPTION = 'description';

    public const PARAM_RULE_NAME_MAX_LENGTH = 120;
    public const PARAM_RULE_DESCRIPTION_MAX_LENGTH = 400;

    /**
     * @var null|SkillService
     */
    protected ?SkillService $skillService = null;

    /**
     * @return SkillService
     */
    public function getSkillService(): SkillService
    {
        if (is_null($this->skillService)) {
            $this->skillService = new SkillService();
        }
        return $this->skillService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/skills/{id}",
     *     tags={"Admin/Skills"},
     *     summary="Get a Skill",
     *     operationId="get-a-skill",
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
     *                 ref="#/components/schemas/Admin-SkillModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $skill = $this->getSkillService()->getSkillById($id);
        if (!$skill instanceof Skill) {
            throw new RecordNotFoundException();
        }

        return new EndpointResourceResult(SkillModel::class, $skill);
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
     *     path="/api/v2/admin/skills",
     *     tags={"Admin/Skills"},
     *     summary="List All Skills",
     *     operationId="list-all-skills",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=SkillSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-SkillModel")
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
     * @throws ServiceException
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $skillSearchParams = new SkillSearchFilterParams();
        $this->setSortingAndPaginationParams($skillSearchParams);
        $skillSearchParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $skillSearchParams->setDescription(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_DESCRIPTION
            )
        );

        $skills = $this->getSkillService()->searchSkill($skillSearchParams);

        return new EndpointCollectionResult(
            SkillModel::class,
            $skills,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getSkillService()->getSearchSkillsCount(
                        $skillSearchParams
                    )
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
            new ParamRule(self::FILTER_NAME),
            new ParamRule(self::FILTER_DESCRIPTION),
            ...$this->getSortingAndPaginationParamsRules(SkillSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/skills",
     *     tags={"Admin/Skills"},
     *     summary="Create a Skill",
     *     operationId="create-a-skill",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\SkillAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             @OA\Property(property="description", type="string, maxLength=OrangeHRM\Admin\Api\SkillAPI::PARAM_RULE_DESCRIPTION_MAX_LENGTH"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-SkillModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        $skill = new Skill();
        $skill = $this->saveSkill($skill);
        return new EndpointResourceResult(SkillModel::class, $skill);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(?EntityUniquePropertyOption $uniqueOption = null): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Skill::class, 'name', $uniqueOption])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DESCRIPTION_MAX_LENGTH])
                ),
                true
            )
        ];
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/skills/{id}",
     *     tags={"Admin/Skills"},
     *     summary="Update a Skill",
     *     operationId="update-a-skill",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\SkillAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             @OA\Property(property="description", type="string, maxLength=OrangeHRM\Admin\Api\SkillAPI::PARAM_RULE_DESCRIPTION_MAX_LENGTH"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-SkillModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $skill = $this->getSkillService()->getSkillById($this->getAttributeId());
        $this->throwRecordNotFoundExceptionIfNotExist($skill, Skill::class);
        $skill = $this->saveSkill($skill);
        return new EndpointResourceResult(SkillModel::class, $skill);
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
            ...$this->getCommonBodyValidationRules($uniqueOption),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/skills",
     *     tags={"Admin/Skills"},
     *     summary="Delete Skills",
     *     operationId="delete-skills",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getSkillService()->getSkillDao()->getExistingSkillIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getSkillService()->deleteSkills($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            ),
        );
    }

    /**
     * @param Skill $skill
     * @return Skill
     */
    public function saveSkill(Skill $skill): Skill
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $description = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );
        $skill->setName($name);
        $skill->setDescription($description);
        return $this->getSkillService()->saveSkill($skill);
    }
}
