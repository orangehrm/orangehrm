<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
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
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Location;
use OrangeHRM\Pim\Dto\LocationSearchFilterParams;

class LocationAPI extends Endpoint implements CrudEndpoint
{

    const FILTER_LOCATION_NAME = 'name';
    const FILTER_LOCATION_CITY_NAME = 'city';
    const FILTER_LOCATION_COUNTRY_CODE = 'countryCode';

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

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
        );
    }

    /**
     * @return EndpointResourceResult
     * @throws RecordNotFoundException
     * @throws NormalizeException
     * @throws DaoException
     */
    public function getOne(): EndpointResourceResult
    {
        // TODO: Check data group permissions
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $location = $this->getLocationService()->getLocationById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($location, Location::class);
        return new EndpointResourceResult(LocationModel::class, $location);
    }

    /**
     * @return EndpointResult
     * @throws DaoException
     * @throws NormalizeException
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
                    )
                ]
            )
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_LOCATION_NAME),
            new ParamRule(self::FILTER_LOCATION_CITY_NAME),
            new ParamRule(self::FILTER_LOCATION_COUNTRY_CODE),
            ...$this->getSortingAndPaginationParamsRules(LocationSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResult
    {
        // TODO: Implement create() method.
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForCreate() method.
    }

    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }
}
