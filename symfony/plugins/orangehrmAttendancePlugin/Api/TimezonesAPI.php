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

use DateTime;
use DateTimeZone;
use OrangeHRM\Attendance\Api\Model\TimezoneModel;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class TimezonesAPI extends Endpoint implements CollectionEndpoint
{
    use DateTimeHelperTrait;
    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $timezonesIdentifiers = DateTimeZone::listIdentifiers();
        $timezones = [];
        foreach ($timezonesIdentifiers as $timezoneIdentifier) {
            $timezone = new DateTimeZone($timezoneIdentifier);
            $offsetInSeconds = $timezone->getOffset(new DateTime());
            $timezones[$timezoneIdentifier] = $offsetInSeconds;
        }
        asort($timezones);
        $sortedTimezones = [];
        $index = 0;
        foreach ($timezones as $identifier => $offsetInSeconds) {
            $offset = (float)($offsetInSeconds/3600);
            $timezoneValue = gmdate('H:i', abs($offsetInSeconds));
            $offsetPrefix = $offsetInSeconds > 0 ? '+' : '-';
            $sortedTimezones[] = [
                'id' => $index,
                'name' => $identifier,
                'gmtLabel' =>"(GMT${offsetPrefix}${timezoneValue})",
                'offset' => $offset
            ];
            $index++;
        }

        return new EndpointCollectionResult(
            TimezoneModel::class,
            $sortedTimezones,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => count($sortedTimezones)])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
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
