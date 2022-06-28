<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Dto\I18NTargetLangStringSearchFilterParams;
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

class I18NTargetLangStringAPI extends Endpoint implements CollectionEndpoint
{
    use LocalizationServiceTrait;

    public const PARAMETER_SOURCE_TEXT = 'sourceText';
    public const PARAMETER_TRANSLATED_TEXT = 'translatedText';
    public const PARAMETER_MODULE_NAME = 'module';
    public const PARAMETER_SHOW_CATEGORY = 'translated';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $I18NTargetLangStringSearchFilterParams = new I18NTargetLangStringSearchFilterParams();
        $this->setSortingAndPaginationParams($I18NTargetLangStringSearchFilterParams);

        $I18NTargetLangStringSearchFilterParams->setSourceText(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_SOURCE_TEXT,
            )
        );

        $I18NTargetLangStringSearchFilterParams->setTranslatedText(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_TRANSLATED_TEXT,
            )
        );

        $I18NTargetLangStringSearchFilterParams->setModuleName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_MODULE_NAME,
            )
        );

        $I18NTargetLangStringSearchFilterParams->setShowCategory(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_SHOW_CATEGORY,
            )
        );

        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $I18NTargetLangStringSearchFilterParams->setLanguageId($id);

        $sourceTexts = $this->getLocalizationService()->getLocalizationDao()->getNormalizedTranslations(
            $I18NTargetLangStringSearchFilterParams
        );

        $languageCount = $this->getLocalizationService()->getLocalizationDao()->getTranslationsCount(
            $I18NTargetLangStringSearchFilterParams
        );

        return new EndpointCollectionResult(
            ArrayModel::class,
            array_values($sourceTexts),
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
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SOURCE_TEXT,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TRANSLATED_TEXT,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MODULE_NAME,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            new ParamRule(
                self::PARAMETER_SHOW_CATEGORY,
                new Rule(Rules::BOOL_VAL)
            ),
            ...$this->getSortingAndPaginationParamsRules(I18NTargetLangStringSearchFilterParams::ALLOWED_SORT_FIELDS)
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
