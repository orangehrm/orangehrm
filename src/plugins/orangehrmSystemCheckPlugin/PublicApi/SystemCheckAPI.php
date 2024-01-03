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

namespace OrangeHRM\SystemCheck\PublicApi;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Installer\Util\SystemCheck;
use Throwable;

class SystemCheckAPI extends Endpoint implements ResourceEndpoint
{
    use EntityManagerTrait;
    use ConfigServiceTrait;
    use LoggerTrait;

    public const PARAMETER_IS_INTERRUPTED = 'isInterrupted';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        if (!$this->getConfigService()->showSystemCheckScreen()) {
            throw $this->getRecordNotFoundException();
        }
        try {
            $systemCheck = new SystemCheck($this->getEntityManager()->getConnection());
            return new EndpointResourceResult(
                ArrayModel::class,
                $systemCheck->getSystemCheckResults(),
                new ParameterBag([self::PARAMETER_IS_INTERRUPTED => $systemCheck->isInterruptContinue()])
            );
        } catch (Throwable $e) {
            try {
                $this->getLogger()->error($e->getMessage());
                $this->getLogger()->error($e->getTraceAsString());
            } finally {
                return new EndpointResourceResult(
                    ArrayModel::class,
                    [],
                    new ParameterBag([
                        self::PARAMETER_IS_INTERRUPTED => true,
                        'error' => ['message' => 'Unexpected Error Occurred'],
                    ])
                );
            }
        }
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
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
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
