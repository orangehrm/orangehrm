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

use OrangeHRM\Admin\Api\Model\I18NLanguageModel;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;

class I18NLanguageAPI extends Endpoint implements CrudEndpoint
{
    use LocalizationServiceTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_ACTIVE_ONLY = 'activeOnly';

    /**
     * @OA\Get(
     *     path="/api/v2/admin/i18n/languages",
     *     tags={"Admin/I18N"},
     *     summary="List All I18N Languages",
     *     operationId="list-all-i18n-languages",
     *     @OA\Parameter(
     *         name="activeOnly",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=I18NLanguageSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-I18NLanguageModel")
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
        $i18NLanguageSearchParams = new I18NLanguageSearchFilterParams();

        $addedOnly = $this->getRequestParams()->getBooleanOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_ACTIVE_ONLY,
        );
        $i18NLanguageSearchParams->setAddedOnly($addedOnly);

        $i18NLanguageSearchParams->setEnabledOnly(true);
        $this->setSortingAndPaginationParams($i18NLanguageSearchParams);
        $i18NLanguages = $this->getLocalizationService()->getLocalizationDao()->searchLanguages(
            $i18NLanguageSearchParams
        );
        $count = $this->getLocalizationService()->getLocalizationDao()->getLanguagesCount($i18NLanguageSearchParams);

        return new EndpointCollectionResult(
            I18NLanguageModel::class,
            $i18NLanguages,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ACTIVE_ONLY,
                new Rule(Rules::BOOL_VAL)
            ),
            ...$this->getSortingAndPaginationParamsRules(I18NLanguageSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/i18n/languages",
     *     tags={"Admin/I18N"},
     *     summary="Delete an I18N Language",
     *     operationId="delete-an-i18n-language",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getLocalizationService()->getLocalizationDao()->getExistingLanguageIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getLocalizationService()->getLocalizationDao()->deleteI18NLanguage($ids);
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
     * @OA\Get(
     *     path="/api/v2/admin/i18n/languages/{id}",
     *     tags={"Admin/I18N"},
     *     summary="Get an I18N Language",
     *     operationId="get-an-i18n-language",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-I18NLanguageModel"
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
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $language = $this->getLocalizationService()->getLocalizationDao()->getLanguageById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($language, I18NLanguage::class);

        return new EndpointResourceResult(I18NLanguageModel::class, $language);
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
     * @OA\Put(
     *     path="/api/v2/admin/i18n/languages/{id}",
     *     tags={"Admin/I18N"},
     *     summary="Update an I18N Language",
     *     operationId="update-an-i18n-language",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             description="Empty body as the language is enabled without any parameters"
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-I18NLanguageModel"
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
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $language = $this->getLocalizationService()->getLocalizationDao()->getLanguageById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($language, I18NLanguage::class);
        $language->setAdded(true);
        $language->setModifiedAt($this->getDateTimeHelper()->getNow());
        $this->getLocalizationService()->getLocalizationDao()->saveI18NLanguage($language);
        return new EndpointResourceResult(I18NLanguageModel::class, $language);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }
}
