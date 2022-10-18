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

namespace OrangeHRM\LDAP\Api;

use DateTimeZone;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\LDAPSyncStatus;
use OrangeHRM\LDAP\Api\Model\LDAPSyncStatusModel;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use Throwable;

class LDAPUserSyncAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use ConfigServiceTrait;

    private LDAPSyncService $ldapSyncService;

    /**
     * @return LDAPSyncService
     */
    private function getLDAPSyncService(): LDAPSyncService
    {
        return $this->ldapSyncService ??= new LDAPSyncService();
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
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        if (!$ldapSettings instanceof LDAPSetting) {
            throw $this->getBadRequestException('LDAP settings not configured');
        } elseif (!$ldapSettings->isEnable()) {
            throw $this->getBadRequestException('LDAP sync not enabled');
        }
        $ldapSyncStatus = new LDAPSyncStatus();
        try {
            $ldapSyncStatus->getDecorator()->setSyncedUserByUserId($this->getAuthUser()->getUserId());
            $ldapSyncStatus->setSyncStartedAt(
                $this->getDateTimeHelper()->getNow()
                    ->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC))
            );
            $this->getLDAPSyncService()->sync();
            $ldapSyncStatus->setSyncFinishedAt(
                $this->getDateTimeHelper()->getNow()
                    ->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC))
            );
            $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_SUCCEEDED);
            $ldapSyncStatus = $this->saveLDAPSyncStatus($ldapSyncStatus);
            return new EndpointResourceResult(LDAPSyncStatusModel::class, $ldapSyncStatus);
        } catch (Throwable $exception) {
            $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_FAILED);
            $this->saveLDAPSyncStatus($ldapSyncStatus);
            throw $this->getBadRequestException('Please check the settings for your LDAP configuration');
        }
    }

    /**
     * @param LDAPSyncStatus $ldapSyncStatus
     * @return LDAPSyncStatus
     */
    private function saveLDAPSyncStatus(
        LDAPSyncStatus $ldapSyncStatus
    ): LDAPSyncStatus {
        return $this->getLDAPSyncService()->getLDAPDao()->saveLdapSyncStatus($ldapSyncStatus);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $lastLdapSyncStatus = $this->getLDAPSyncService()->getLDAPDao()->getLastLDAPSyncStatus();
        if (is_null($lastLdapSyncStatus)) {
            $lastLdapSyncStatus = new LDAPSyncStatus();
        }
        return new EndpointResourceResult(LDAPSyncStatusModel::class, $lastLdapSyncStatus);
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
