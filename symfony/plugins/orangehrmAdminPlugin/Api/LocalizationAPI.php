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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Model\ArrayCollectionModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\LocalizationServiceTrait;

class LocalizationAPI extends Endpoint implements CrudEndpoint
{
    use ConfigServiceTrait;
    use LocalizationServiceTrait;

    public const PARAMETER_LANGUAGE = 'language';
    public const PARAMETER_DATE_FORMAT = 'dateFormat';
    public const PARAMETER_USE_BROWSER_LANGUAGE = 'useBrowserLanguage';
    public const PARAMETER_BROWSER_LANGUAGE = 'browserLanguage';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $configService = $this->getConfigService();
        $useBrowserLanguage = $configService->getAdminLocalizationUseBrowserLanguage() ?? 'No';
        return new EndpointResourceResult(ArrayCollectionModel::class, [
            'defaultLanguage' => $configService->getAdminLocalizationDefaultLanguage(),
            'defaultDateFormat' => $configService->getAdminLocalizationDefaultDateFormat(),
            'useBrowserLanguage' => strtolower($useBrowserLanguage) === 'yes',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $language = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LANGUAGE);
        $dateFormat = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE_FORMAT);
        $browserLanguage = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_BROWSER_LANGUAGE
        );
        $useBrowserLanguage = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_USE_BROWSER_LANGUAGE
        );

        $languageArray = array_column($this->getLocalizationService()->getSupportedLanguages(), 'id');

        if ($useBrowserLanguage && in_array($browserLanguage, $languageArray)) {
            $language = $browserLanguage;
        }
        $configService = $this->getConfigService();
        $configService->setAdminLocalizationDefaultDateFormat($dateFormat);
        $configService->setAdminLocalizationDefaultLanguage($language);
        $configService->setAdminLocalizationUseBrowserLanguage($useBrowserLanguage ? 'Yes' : 'No');
        return new EndpointResourceResult(ArrayCollectionModel::class, [
            'defaultLanguage' => $language,
            'defaultDateFormat' => $dateFormat,
            'useBrowserLanguage' => $useBrowserLanguage,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $localizationService = $this->getLocalizationService();
        $dateFormats = $localizationService->getLocalizationDateFormats();
        $languageArray = $localizationService->getSupportedLanguages();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
            ),
            new ParamRule(
                self::PARAMETER_LANGUAGE,
                new Rule(Rules::IN, [array_column($languageArray, 'id')])
            ),
            new ParamRule(
                self::PARAMETER_DATE_FORMAT,
                new Rule(Rules::IN, [array_column($dateFormats, 'id')])
            ),
            new ParamRule(
                self::PARAMETER_USE_BROWSER_LANGUAGE,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_BROWSER_LANGUAGE,
                    new Rule(Rules::STRING_TYPE)
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
