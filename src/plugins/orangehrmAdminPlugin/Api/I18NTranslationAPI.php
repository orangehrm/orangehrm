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

use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

class I18NTranslationAPI extends Endpoint implements CollectionEndpoint
{
    use LocalizationServiceTrait;

    public const PARAMETER_LANGUAGE_ID = 'languageId';
    public const PARAMETER_SOURCE_TEXT = 'sourceText';
    public const PARAMETER_TRANSLATED_TEXT = 'translatedText';
    public const PARAMETER_GROUP_ID = 'groupId';
    public const PARAMETER_ONLY_TRANSLATED = 'onlyTranslated';

    public const PARAMETER_RULE_SOURCE_TEXT_MAX_LENGTH = 250;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/i18n/languages/{languageId}/translations",
     *     tags={"Admin/I18N"},
     *     summary="List All I18N Translations",
     *     operationId="list-all-i18n-translations",
     *     @OA\PathParameter(
     *         name="languageId",
     *         @OA\Schema(type="integer", description="a valid group id of I18N-Language")
     *     ),
     *     @OA\Parameter(
     *         name="sourceText",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="translatedText",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="groupId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", description="a valid group id of I18N-Group")
     *     ),
     *     @OA\Parameter(
     *         name="onlyTranslated",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=I18NTranslationSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(
     *                     @OA\Property(property="langStringId", type="integer"),
     *                     @OA\Property(property="source", type="boolean"),
     *                     @OA\Property(property="note", type="boolean"),
     *                     @OA\Property(property="target", type="boolean"),
     *                     @OA\Property(property="module", type="boolean"),
     *                 ),
     *                 example="langStringId: 59, note: null, source: ""Add Currency"", target: ""Valuta toevoegen"" "
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
        $i18NTranslationSearchFilterParams = new I18NTranslationSearchFilterParams();
        $this->setSortingAndPaginationParams($i18NTranslationSearchFilterParams);

        $i18NTranslationSearchFilterParams->setSourceText(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_SOURCE_TEXT,
            )
        );

        $i18NTranslationSearchFilterParams->setTranslatedText(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_TRANSLATED_TEXT,
            )
        );

        $i18NTranslationSearchFilterParams->setGroupId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_GROUP_ID,
            )
        );

        $i18NTranslationSearchFilterParams->setOnlyTranslated(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_ONLY_TRANSLATED,
            )
        );

        $languageId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LANGUAGE_ID
        );
        $i18NTranslationSearchFilterParams->setLanguageId($languageId);

        $translations = $this->getLocalizationService()->getLocalizationDao()->getNormalizedTranslations(
            $i18NTranslationSearchFilterParams
        );

        $languageCount = $this->getLocalizationService()->getLocalizationDao()->getTranslationsCount(
            $i18NTranslationSearchFilterParams
        );

        return new EndpointCollectionResult(
            ArrayModel::class,
            $translations,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $languageCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_LANGUAGE_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SOURCE_TEXT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_SOURCE_TEXT_MAX_LENGTH])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TRANSLATED_TEXT,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_GROUP_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                self::PARAMETER_ONLY_TRANSLATED,
                new Rule(Rules::BOOL_VAL)
            ),
            ...$this->getSortingAndPaginationParamsRules(I18NTranslationSearchFilterParams::ALLOWED_SORT_FIELDS)
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
}
