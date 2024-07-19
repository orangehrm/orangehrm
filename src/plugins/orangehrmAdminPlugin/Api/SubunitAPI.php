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

use OrangeHRM\Admin\Api\Model\SubunitModel;
use OrangeHRM\Admin\Api\Model\SubunitTreeModel;
use OrangeHRM\Admin\Traits\Service\CompanyStructureServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Entity\Subunit;

class SubunitAPI extends Endpoint implements CrudEndpoint
{
    use CompanyStructureServiceTrait;

    public const PARAMETER_PARENT_ID = 'parentId';
    public const PARAMETER_UNIT_ID = 'unitId';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';

    public const FILTER_DEPTH = 'depth';
    public const FILTER_MODE = 'mode';

    public const MODE_LIST = 'list';
    public const MODE_TREE = 'tree';

    public const PARAM_RULE_UNIT_ID_MAX_LENGTH = 100;
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_DESCRIPTION_MAX_LENGTH = 400;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/subunits/{id}",
     *     tags={"Admin/Subunits"},
     *     summary="Get a Subunit",
     *     operationId="get-a-subunit",
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
     *                 ref="#/components/schemas/Admin-SubunitModel"
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
        $unitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $subunit = $this->getCompanyStructureService()->getSubunitById($unitId);
        $this->throwRecordNotFoundExceptionIfNotExist($subunit, Subunit::class);

        return new EndpointResourceResult(SubunitModel::class, $subunit);
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
            )
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/subunits",
     *     tags={"Admin/Subunits"},
     *     summary="List All Subunits",
     *     operationId="list-all-subunits",
     *     @OA\Parameter(
     *         name="depth",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="mode",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", default="list")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="if request paraneter 'mode' is tree SubunitTreeModel is used",
     *                 @OA\Items(
     *                     oneOf={
     *                         @OA\Schema(ref="#/components/schemas/Admin-SubunitModel"),
     *                         @OA\Schema(ref="#/components/schemas/Admin-SubunitTreeModel"),
     *                     }
     *                 )
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
        $depth = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_DEPTH);
        $mode = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODE,
            self::MODE_LIST
        );
        $subunits = $this->getCompanyStructureService()->getSubunitTree($depth);

        $modelClass = SubunitModel::class;
        if ($mode === self::MODE_TREE) {
            $modelClass = SubunitTreeModel::class;
        }

        return new EndpointCollectionResult($modelClass, $subunits);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_DEPTH,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_MODE,
                    new Rule(Rules::IN, [[self::MODE_LIST, self::MODE_TREE]])
                )
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/subunits",
     *     tags={"Admin/Subunits"},
     *     summary="Create a Subunit",
     *     operationId="create-a-subunit",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Should be unique",
     *                 maxLength=OrangeHRM\Admin\Api\SubunitAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 maxLength=OrangeHRM\Admin\Api\SubunitAPI::PARAM_RULE_DESCRIPTION_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="parentId",
     *                 type="integer",
     *                 description="Should be the id of the parent node",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="unitId",
     *                 type="string",
     *                 maxLength=OrangeHRM\Admin\Api\SubunitAPI::PARAM_RULE_UNIT_ID_MAX_LENGTH
     *             ),
     *             required={"name", "parentId"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-SubunitModel"
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
        $parentUnitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PARENT_ID);
        $parentSubunit = $this->getCompanyStructureService()->getSubunitById($parentUnitId);
        $this->throwRecordNotFoundExceptionIfNotExist($parentSubunit, Subunit::class);

        $subunit = new Subunit();
        $subunit = $this->setParamsToSubunit($subunit);
        $this->getCompanyStructureService()->addSubunit($parentSubunit, $subunit);

        return new EndpointResourceResult(SubunitModel::class, $subunit);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_PARENT_ID),
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
            new ParamRule(
                self::PARAMETER_UNIT_ID,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_UNIT_ID_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Subunit::class, 'name', $uniqueOption])
                )
            ),
            new ParamRule(
                self::PARAMETER_DESCRIPTION,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DESCRIPTION_MAX_LENGTH])
            ),
        ];
    }

    /**
     * @param Subunit $subunit
     * @return Subunit
     */
    private function setParamsToSubunit(Subunit $subunit): Subunit
    {
        $unitId = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_UNIT_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $description = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );
        $subunit->setUnitId($unitId);
        $subunit->setName($name);
        $subunit->setDescription($description);
        return $subunit;
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/subunits/{id}",
     *     tags={"Admin/Subunits"},
     *     summary="Update a Subunit",
     *     operationId="update-a-subunit",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="String should be unique",
     *                 maxLength=OrangeHRM\Admin\Api\SubunitAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 maxLength=OrangeHRM\Admin\Api\SubunitAPI::PARAM_RULE_DESCRIPTION_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="unitId",
     *                 type="string",
     *                 maxLength=OrangeHRM\Admin\Api\SubunitAPI::PARAM_RULE_UNIT_ID_MAX_LENGTH
     *             ),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-SubunitModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $unitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $subunit = $this->getCompanyStructureService()->getSubunitById($unitId);
        $this->throwRecordNotFoundExceptionIfNotExist($subunit, Subunit::class);

        $subunit = $this->setParamsToSubunit($subunit);

        $this->getCompanyStructureService()->saveSubunit($subunit);
        return new EndpointResourceResult(SubunitModel::class, $subunit);
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
     *     path="/api/v2/admin/subunits/{id}",
     *     tags={"Admin/Subunits"},
     *     summary="Delete Subunits",
     *     operationId="delete-subunits",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer", minimum="2")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-SubunitModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $unitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $subunit = $this->getCompanyStructureService()->getSubunitById($unitId);
        $this->throwRecordNotFoundExceptionIfNotExist($subunit, Subunit::class);

        $resultSubunit = clone $subunit;
        $this->getCompanyStructureService()->deleteSubunit($subunit);

        return new EndpointResourceResult(SubunitModel::class, $resultSubunit);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::GREATER_THAN, [1]) // prevent deleting the top of the organization structure
            ),
        );
    }
}
