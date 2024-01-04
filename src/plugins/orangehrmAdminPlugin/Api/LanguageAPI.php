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
use OrangeHRM\Admin\Api\Model\LanguageModel;
use OrangeHRM\Admin\Dto\LanguageSearchFilterParams;
use OrangeHRM\Admin\Service\LanguageService;
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
use OrangeHRM\Entity\Language;

class LanguageAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAM_RULE_NAME_MAX_LENGTH = 120;

    /**
     * @var null|LanguageService
     */
    protected ?LanguageService $languageService = null;

    /**
     * @return LanguageService
     * @throws Exception
     */
    public function getLanguageService(): LanguageService
    {
        if (is_null($this->languageService)) {
            $this->languageService = new LanguageService();
        }
        return $this->languageService;
    }

    /**
     * @param LanguageService $languageService
     */
    public function setLanguageService(LanguageService $languageService): void
    {
        $this->languageService = $languageService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/languages/{id}",
     *     tags={"Admin/Languages"},
     *     summary="Get a Language Record",
     *     operationId="get-a-language-record",
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
     *                 ref="#/components/schemas/Admin-LanguageModel"
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
        $language = $this->getLanguageService()->getLanguageById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($language, Language::class);
        return new EndpointResourceResult(LanguageModel::class, $language);
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
     *     path="/api/v2/admin/languages",
     *     tags={"Admin/Languages"},
     *     summary="List All Languages Records",
     *     operationId="list-all-language-records",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LanguageSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-LanguageModel")
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
        $languageParamHolder = new LanguageSearchFilterParams();
        $this->setSortingAndPaginationParams($languageParamHolder);
        $languages = $this->getLanguageService()->getLanguageList($languageParamHolder);
        $count = $this->getLanguageService()->getLanguageCount($languageParamHolder);
        return new EndpointCollectionResult(
            LanguageModel::class,
            $languages,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(LanguageSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/languages",
     *     tags={"Admin/Languages"},
     *     summary="Create a Language Record",
     *     operationId="create-a-language",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-LanguageModel"
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
        $languages = $this->saveLanguage();
        return new EndpointResourceResult(LanguageModel::class, $languages);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
        );
    }

    /**
     * @return Language
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function saveLanguage(): Language
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if ($id) {
            $language = $this->getLanguageService()->getLanguageById($id);
            $this->throwRecordNotFoundExceptionIfNotExist($language, Language::class);
        } else {
            $language = new Language();
        }
        $language->setName($name);
        return $this->getLanguageService()->saveLanguage($language);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForSaveLanguage(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/languages/{id}",
     *     tags={"Admin/Languages"},
     *     summary="Update a Language Record",
     *     operationId="update-a-language-record",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-LanguageModel"
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
        $languages = $this->saveLanguage();
        return new EndpointResourceResult(LanguageModel::class, $languages);
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
            ),
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/languages",
     *     tags={"Admin/Languages"},
     *     summary="Delete Language Records",
     *     operationId="delete-language-records",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getLanguageService()->deleteLanguages($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     * @return ParamRuleCollection
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
