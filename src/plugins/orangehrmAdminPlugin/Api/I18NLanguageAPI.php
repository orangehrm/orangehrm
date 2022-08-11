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
use OrangeHRM\Entity\I18NLanguage;

class I18NLanguageAPI extends Endpoint implements CrudEndpoint
{
    use LocalizationServiceTrait;

    public const PARAMETER_ACTIVE_ONLY = 'activeOnly';

    /**
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
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $language = $this->getLocalizationService()->getLocalizationDao()->getLanguageById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($language, I18NLanguage::class);
        $language->setAdded(true);
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
