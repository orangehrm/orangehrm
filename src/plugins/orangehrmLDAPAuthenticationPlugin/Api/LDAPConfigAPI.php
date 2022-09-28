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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\ValidatorTrait;
use OrangeHRM\LDAP\Api\Model\LDAPConfigModel;
use OrangeHRM\LDAP\Api\Traits\LDAPCommonParamRuleCollection;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;

class LDAPConfigAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;
    use ValidatorTrait;
    use LDAPCommonParamRuleCollection;

    public const PARAMETER_ENABLED = 'enable';
    public const PARAMETER_HOSTNAME = 'hostname';
    public const PARAMETER_PORT = 'port';
    public const PARAMETER_ENCRYPTION = 'encryption';
    public const PARAMETER_LDAP_IMPLEMENTATION = 'ldapImplementation';

    public const PARAMETER_BIND_ANONYMOUSLY = 'bindAnonymously';
    public const PARAMETER_BIND_USER_DISTINGUISHED_NAME = 'bindUserDN';
    public const PARAMETER_BIND_USER_PASSWORD = 'bindUserPassword';

    public const PARAMETER_USER_LOOKUP_SETTINGS = 'userLookupSettings';
    public const PARAMETER_BASE_DISTINGUISHED_NAME = 'baseDN';
    public const PARAMETER_SEARCH_SCOPE = 'searchScope';
    public const PARAMETER_USER_NAME_ATTRIBUTE = 'userNameAttribute';
    public const PARAMETER_USER_UNIQUE_ID_ATTRIBUTE = 'userUniqueIdAttribute';
    public const PARAMETER_USER_SEARCH_FILTER = 'userSearchFilter';

    public const PARAMETER_EMPLOYEE_SELECTOR_MAPPING = 'employeeSelectorMapping';
    public const PARAMETER_EMPLOYEE_SELECTOR_MAPPING_FIELD = 'field';
    public const PARAMETER_EMPLOYEE_SELECTOR_MAPPING_ATTRIBUTE_NAME = 'attributeName';

    public const PARAMETER_DATA_MAPPING = 'dataMapping';
    public const PARAMETER_FIRST_NAME = 'firstName';
    public const PARAMETER_MIDDLE_NAME = 'middleName';
    public const PARAMETER_LAST_NAME = 'lastName';
    public const PARAMETER_USER_STATUS = 'userStatus';
    public const PARAMETER_WORK_EMAIL = 'workEmail';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';

    public const PARAMETER_MERGE_LDAP_USERS_WITH_EXISTING_SYSTEM_USERS = 'mergeLDAPUsersWithExistingSystemUsers';
    public const PARAMETER_SYNC_INTERVAL = 'syncInterval';

    public const ENCRYPTION_NONE = 'none';
    public const ENCRYPTION_TLS = 'tls';
    public const ENCRYPTION_SSL = 'ssl';

    public const LDAP_IMPLEMENTATION_OPEN_LDAP = 'OpenLDAP';
    public const LDAP_IMPLEMENTATION_ACTIVE_DIRECTORY = 'ActiveDirectory';

    public const PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH = 100;
    public const PARAMETER_RULE_HOST_NAME_MAX_LENGTH = 255;
    public const PARAMETER_RULE_BIND_USER_DISTINGUISHED_NAME_MAX_LENGTH = 255;
    public const PARAMETER_RULE_BIND_USER_PASSWORD_MAX_LENGTH = 255;
    public const PARAMETER_RULE_USER_SEARCH_FILTER_MAX_LENGTH = 255;
    public const PARAMETER_RULE_BASE_DISTINGUISHED_NAME_MAX_LENGTH = 255;

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $dataMapping = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DATA_MAPPING
        );
        $this->validate($dataMapping, $this->getParamRuleCollectionForDataMapping());

        $userLookupSettings = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_USER_LOOKUP_SETTINGS
        );
        foreach ($userLookupSettings as $userLookupSetting) {
            $this->validate($userLookupSetting, $this->getParamRuleCollectionForUserLookupSettings());
        }

        $ldapSettings = new LDAPSetting(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_HOSTNAME
            ),
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PORT
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_LDAP_IMPLEMENTATION
            ),
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ENCRYPTION
            ),
        );

        $this->setConfigAttributes($ldapSettings);
        $this->setDataMappingAttributes($ldapSettings, $dataMapping);
        $this->setUserLookupSettings($ldapSettings, $userLookupSettings);
        $this->getConfigService()->setLDAPSetting($ldapSettings);
        return new EndpointResourceResult(LDAPConfigModel::class, $ldapSettings);
    }

    /**
     * @param LDAPSetting $ldapSetting
     */
    private function setConfigAttributes(LDAPSetting $ldapSetting): void
    {
        $bindAnonymously = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_BIND_ANONYMOUSLY
        );
        $ldapSetting->setBindAnonymously($bindAnonymously);
        if (!$bindAnonymously) {
            $ldapSetting->setBindUserDN(
                $this->getRequestParams()->getString(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_BIND_USER_DISTINGUISHED_NAME
                )
            );

            $password = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_BIND_USER_PASSWORD
            );
            $ldapSettings = $this->getConfigService()->getLDAPSetting();
            if ($ldapSettings instanceof LDAPSetting && $password === null) {
                $ldapSetting->setBindUserPassword($ldapSettings->getBindUserPassword());
            } else {
                $ldapSetting->setBindUserPassword($password);
            }
        } else {
            $ldapSetting->setBindUserDN(null);
            $ldapSetting->setBindUserPassword(null);
        }

        $ldapSetting->setEnable(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ENABLED
            )
        );
        $ldapSetting->setSyncInterval(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SYNC_INTERVAL
            )
        );
        $ldapSetting->setMergeLDAPUsersWithExistingSystemUsers(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_MERGE_LDAP_USERS_WITH_EXISTING_SYSTEM_USERS
            )
        );
    }

    /**
     * @param LDAPSetting $ldapSetting
     * @param array $dataMapping
     */
    private function setDataMappingAttributes(LDAPSetting $ldapSetting, array $dataMapping): void
    {
        $ldapSetting->getDataMapping()->setAttributeNames($dataMapping);
    }

    /**
     * @param LDAPSetting $ldapSetting
     * @param array $userLookupSettings
     */
    private function setUserLookupSettings(LDAPSetting $ldapSetting, array $userLookupSettings): void
    {
        foreach ($userLookupSettings as $userLookupSetting) {
            $ldapSetting->addUserLookupSetting(LDAPUserLookupSetting::createFromArray($userLookupSetting));
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = $this->getParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
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
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        if ($ldapSettings === null) {
            $ldapSettings = new LDAPSetting('localhost', 389, 'OpenLDAP', 'none');
        }
        return new EndpointResourceResult(LDAPConfigModel::class, $ldapSettings);
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
}
