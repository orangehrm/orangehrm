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
use OrangeHRM\Admin\Api\Model\NationalityModel;
use OrangeHRM\Admin\Dto\NationalitySearchFilterParams;
use OrangeHRM\Admin\Service\NationalityService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Entity\Nationality;

class NationalityAPI extends EndPoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;

    /**
     * @var null|NationalityService
     */
    protected ?NationalityService $nationalityService = null;

    /**
     * @return NationalityService
     */
    public function getNationalityService(): NationalityService
    {
        if (!$this->nationalityService instanceof NationalityService) {
            $this->nationalityService = new NationalityService();
        }
        return $this->nationalityService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/nationalities/{id}",
     *     tags={"Admin/Nationality"},
     *     summary="Get a Nationality",
     *     operationId="get-a-nationality",
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
     *                 ref="#/components/schemas/Admin-NationalityModel"
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
        $nationality = $this->getNationalityService()->getNationalityById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($nationality, Nationality::class);
        return new EndpointResourceResult(NationalityModel::class, $nationality);
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
     *     path="/api/v2/admin/nationalities",
     *     tags={"Admin/Nationality"},
     *     summary="List All Nationalities",
     *     operationId="list-all-nationalities",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=NationalitySearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-NationalityModel")
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
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $nationalityParamHolder = new NationalitySearchFilterParams();
        $this->setSortingAndPaginationParams($nationalityParamHolder);
        $nationalities = $this->getNationalityService()->getNationalityList($nationalityParamHolder);
        $count = $this->getNationalityService()->getNationalityCount($nationalityParamHolder);
        return new EndpointCollectionResult(
            NationalityModel::class,
            $nationalities,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(NationalitySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/nationalities",
     *     tags={"Admin/Nationality"},
     *     summary="Create a Nationality",
     *     operationId="create-a-nationality",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\NationalityAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-NationalityModel"
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
        $nationality = new Nationality();
        $nationality = $this->saveNationality($nationality);
        return new EndpointResourceResult(NationalityModel::class, $nationality);
    }

    /**
     * @param Nationality $nationality
     * @return Nationality
     */
    public function saveNationality(Nationality $nationality): Nationality
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $nationality->setName($name);
        return $this->getNationalityService()->saveNationality($nationality);
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
     *     path="/api/v2/admin/nationalities/{id}",
     *     tags={"Admin/Nationality"},
     *     summary="Update a Nationality",
     *     operationId="update-a-nationality",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\NationalityAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-NationalityModel"
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
        $nationality = $this->getNationalityService()->getNationalityById($this->getAttributeId());
        $this->throwRecordNotFoundExceptionIfNotExist($nationality, Nationality::class);
        $nationalities = $this->saveNationality($nationality);
        return new EndpointResourceResult(NationalityModel::class, $nationalities);
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
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Nationality::class, 'name', $uniqueOption])
            )
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/nationalities",
     *     tags={"Admin/Nationality"},
     *     summary="Delete Nationalities",
     *     operationId="delete-nationalities",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getNationalityService()->getNationalityDao()->getExistingNationalityIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getNationalityService()->deleteNationalities($ids);
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
