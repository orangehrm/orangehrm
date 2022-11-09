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

namespace OrangeHRM\Attendance\Api;

use DateTimeZone;
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
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;

class TimezonesAPI extends Endpoint implements CollectionEndpoint
{
    use DateTimeHelperTrait;
    use NumberHelperTrait;

    public const FILTER_TIMEZONE_NAME = 'timezoneName';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $filterName = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_TIMEZONE_NAME
        );

        $identifiers = DateTimeZone::listIdentifiers();

        if (is_null($filterName)) {
            $filteredIdentifiers = $identifiers;
        } else {
            $filteredIdentifiers = array_filter(
                $identifiers,
                function ($item) use ($filterName) {
                    if (stripos($item, $filterName) !== false) {
                        return true;
                    }
                    return false;
                }
            );
        }

        $timezones = [];
        foreach ($filteredIdentifiers as $timezoneIdentifier) {
            $timezone = new DateTimeZone($timezoneIdentifier);
            $offsetInSeconds = $timezone->getOffset($this->getDateTimeHelper()->getNow());
            $offset = $this->getNumberHelper()->numberFormat((float)($offsetInSeconds / 3600), 1);
            $timezoneValue = gmdate('H:i', abs($offsetInSeconds));
            $offsetPrefix = $offsetInSeconds > 0 ? '+' : '-';
            $timezones[$timezoneIdentifier] = [
                'name' => $timezoneIdentifier,
                'label' => "${offsetPrefix}${timezoneValue}",
                'offset' => $offset
            ];
        }
        usort(
            $timezones,
            fn ($timezone1, $timezone2) => $timezone1['offset'] > $timezone2['offset']
        );
        return new EndpointCollectionResult(
            ArrayModel::class,
            array_values($timezones),
            new ParameterBag([CommonParams::PARAMETER_TOTAL => count($timezones)])
        );
    }


    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_TIMEZONE_NAME,
                    new Rule(Rules::STRING_TYPE)
                ),
                true
            )
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
