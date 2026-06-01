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

namespace OrangeHRM\Slack\Api;

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Slack\Api\Model\SlackRegistrationModel;
use OrangeHRM\Slack\Service\SlackRegistrationService;

class SlackRegistrationAPI extends Endpoint implements CollectionEndpoint
{
    private ?SlackRegistrationService $registrationService = null;

    public function getRegistrationService(): SlackRegistrationService
    {
        if ($this->registrationService === null) {
            $this->registrationService = new SlackRegistrationService();
        }
        return $this->registrationService;
    }

    public function getAll(): EndpointCollectionResult
    {
        $registrations = $this->getRegistrationService()->listRegistrations();
        return new EndpointCollectionResult(
            SlackRegistrationModel::class,
            $registrations,
            new ParameterBag([
                \OrangeHRM\Core\Api\CommonParams::PARAMETER_TOTAL => count($registrations),
            ])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    public function create(): EndpointResourceResult
    {
        throw new NotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    public function delete(): EndpointResourceResult
    {
        throw new NotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
