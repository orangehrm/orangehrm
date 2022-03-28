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

use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class LocalizationAPI extends Endpoint implements CrudEndpoint
{
    use ConfigServiceTrait;
    use LocalizationServiceTrait;

    public const PARAMETER_LANGUAGE = 'language';
    public const PARAMETER_DATE_FORMAT = 'dateFormat';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        return new EndpointResourceResult(ArrayModel::class, [
            self::PARAMETER_LANGUAGE => $this->getConfigService()->getAdminLocalizationDefaultLanguage(),
            self::PARAMETER_DATE_FORMAT => $this->getConfigService()->getAdminLocalizationDefaultDateFormat(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $language = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LANGUAGE);
        $dateFormat = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE_FORMAT);

        $this->getConfigService()->setAdminLocalizationDefaultDateFormat($dateFormat);
        $this->getConfigService()->setAdminLocalizationDefaultLanguage($language);

        return new EndpointResourceResult(ArrayModel::class, [
            self::PARAMETER_LANGUAGE => $language,
            self::PARAMETER_DATE_FORMAT => $dateFormat,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $dateFormats = $this->getLocalizationService()->getLocalizationDateFormats();
        $languageArray = $this->getLocalizationService()->getSupportedLanguages();
        $paramRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_LANGUAGE,
                new Rule(Rules::IN, [array_column($languageArray, 'id')])
            ),
            new ParamRule(
                self::PARAMETER_DATE_FORMAT,
                new Rule(Rules::IN, [array_column($dateFormats, 'id')])
            ),
        );
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
