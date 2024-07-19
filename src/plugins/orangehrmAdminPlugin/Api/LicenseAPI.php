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
use OrangeHRM\Admin\Api\Model\LicenseModel;
use OrangeHRM\Admin\Dto\LicenseSearchFilterParams;
use OrangeHRM\Admin\Service\LicenseService;
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
use OrangeHRM\Entity\License;

class LicenseAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;

    /**
     * @var null|LicenseService
     */
    protected ?LicenseService $licenseService = null;

    /**
     * @return LicenseService
     */
    public function getLicenseService(): LicenseService
    {
        if (is_null($this->licenseService)) {
            $this->licenseService = new LicenseService();
        }
        return $this->licenseService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/licenses/{id}",
     *     tags={"Admin/License"},
     *     summary="Get a License",
     *     operationId="get-a-license",
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
     *                 ref="#/components/schemas/Admin-LicenseModel"
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
        $license = $this->getLicenseService()->getLicenseById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($license, License::class);
        return new EndpointResourceResult(LicenseModel::class, $license);
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
     *     path="/api/v2/admin/licenses",
     *     tags={"Admin/License"},
     *     summary="List All Licenses",
     *     operationId="list-all-licenses",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-LicenseModel")
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
        $licenseParamHolder = new LicenseSearchFilterParams();
        $this->setSortingAndPaginationParams($licenseParamHolder);
        $licenses = $this->getLicenseService()->getLicenseList($licenseParamHolder);
        $count = $this->getLicenseService()->getLicenseCount($licenseParamHolder);
        return new EndpointCollectionResult(
            LicenseModel::class,
            $licenses,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(LicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/licenses",
     *     tags={"Admin/License"},
     *     summary="Create a License",
     *     operationId="create-a-license",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\LicenseAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-LicenseModel"
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
        $license = new License();
        $licenses = $this->saveLicense($license);
        return new EndpointResourceResult(LicenseModel::class, $licenses);
    }

    /**
     * @param License $license
     * @return License
     */
    public function saveLicense(License $license): License
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $license->setName($name);
        return $this->getLicenseService()->saveLicense($license);
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
     *     path="/api/v2/admin/licenses/{id}",
     *     tags={"Admin/License"},
     *     summary="Update a License",
     *     operationId="update-a-license",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Admin\Api\LicenseAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-LicenseModel"
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
        $license = $this->getLicenseService()->getLicenseById($this->getAttributeId());
        $this->throwRecordNotFoundExceptionIfNotExist($license, License::class);
        $licenses = $this->saveLicense($license);
        return new EndpointResourceResult(LicenseModel::class, $licenses);
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
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [License::class, 'name', $uniqueOption])
            )
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/licenses",
     *     tags={"Admin/License"},
     *     summary="Delete Licenses",
     *     operationId="delete-licenses",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getLicenseService()->getLicenseDao()->getExistingLicenseIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getLicenseService()->deleteLicenses($ids);
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
