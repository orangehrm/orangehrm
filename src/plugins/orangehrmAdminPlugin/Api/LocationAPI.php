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

use OrangeHRM\Admin\Api\Model\LocationModel;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Location;
use OrangeHRM\Admin\Dto\LocationSearchFilterParams;

class LocationAPI extends Endpoint implements CrudEndpoint
{
    public const FILTER_LOCATION_NAME = 'name';

    public const FILTER_LOCATION_CITY_NAME = 'city';

    public const FILTER_LOCATION_COUNTRY_CODE = 'countryCode';

    public const PARAMETER_NAME = 'name';

    public const PARAMETER_COUNTRY_CODE = 'countryCode';

    public const PARAMETER_PROVINCE = 'province';

    public const PARAMETER_CITY = 'city';

    public const PARAMETER_ADDRESS = 'address';

    public const PARAMETER_ZIP_CODE = 'zipCode';

    public const PARAMETER_PHONE = 'phone';

    public const PARAMETER_FAX = 'fax';

    public const PARAMETER_NOTE = 'note';

    public const PARAM_RULE_NAME_MAX_LENGTH = 100;

    public const PARAM_RULE_PROVINCE_MAX_LENGTH = 50;

    public const PARAM_RULE_CITY_MAX_LENGTH = 50;

    public const PARAM_RULE_ADDRESS_MAX_LENGTH = 250;

    public const PARAM_RULE_ZIP_CODE_MAX_LENGTH = 30;

    public const PARAM_RULE_PHONE_MAX_LENGTH = 30;

    public const PARAM_RULE_FAX_MAX_LENGTH = 30;

    public const PARAM_RULE_NOTE_MAX_LENGTH = 250;

    /**
     * @var null|LocationService
     */
    protected ?LocationService $locationService = null;

    /**
     * @return LocationService
     */
    public function getLocationService(): LocationService
    {
        if (is_null($this->locationService)) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * @param LocationService $locationService
     */
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $location = $this->getLocationService()->getLocationById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($location, Location::class);
        return new EndpointResourceResult(LocationModel::class, $location);
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();

        $this->setSortingAndPaginationParams($locationSearchFilterParams);

        $locationSearchFilterParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_LOCATION_NAME
            )
        );
        $locationSearchFilterParams->setCity(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_LOCATION_CITY_NAME
            )
        );
        $locationSearchFilterParams->setCountryCode(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_LOCATION_COUNTRY_CODE
            )
        );

        $locations = $this->getLocationService()->searchLocations($locationSearchFilterParams);

        return new EndpointCollectionResult(
            LocationModel::class,
            $locations,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getLocationService()->getSearchLocationListCount(
                        $locationSearchFilterParams
                    ),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_LOCATION_NAME),
            new ParamRule(self::FILTER_LOCATION_CITY_NAME),
            new ParamRule(self::FILTER_LOCATION_COUNTRY_CODE),
            ...$this->getSortingAndPaginationParamsRules(LocationSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $location = new Location();
        $this->setLocationData($location);
        $location = $this->getLocationService()->saveLocation($location);
        return new EndpointResourceResult(LocationModel::class, $location);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    public function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_COUNTRY_CODE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::COUNTRY_CODE),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROVINCE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PROVINCE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CITY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CITY_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ADDRESS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ADDRESS_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ZIP_CODE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ZIP_CODE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PHONE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::PHONE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PHONE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FAX,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FAX_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NOTE_MAX_LENGTH]),
                ),
                true
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getLocationService()->deleteLocations($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS, new Rule(Rules::ARRAY_TYPE)),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $location = $this->getLocationService()->getLocationById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($location, Location::class);
        $this->setLocationData($location);
        $location = $this->getLocationService()->saveLocation($location);
        return new EndpointResourceResult(LocationModel::class, $location);
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
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * This function populates the location entity with the request data.
     *
     * @param Location $location
     *
     */
    private function setLocationData(Location $location): void
    {
        $location->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );
        $location->getDecorator()->setCountryByCountryCode(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COUNTRY_CODE
            )
        );
        $location->setProvince(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PROVINCE
            )
        );
        $location->setCity(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CITY
            )
        );
        $location->setAddress(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ADDRESS
            )
        );
        $location->setZipCode(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ZIP_CODE
            )
        );
        $location->setPhone(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PHONE
            )
        );
        $location->setFax(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_FAX
            )
        );
        $location->setNote(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        );
    }
}
