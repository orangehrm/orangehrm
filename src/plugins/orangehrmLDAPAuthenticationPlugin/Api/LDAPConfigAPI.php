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
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\ValidatorTrait;
use OrangeHRM\LDAP\Api\Model\LDAPConfigModel;
use OrangeHRM\LDAP\Api\Traits\LDAPDataMapParamRuleCollection;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use Symfony\Component\Ldap\Adapter\QueryInterface;

class LDAPConfigAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;
    use ValidatorTrait;
    use LDAPDataMapParamRuleCollection;

    public const PARAMETER_ENABLED = 'enable';
    public const PARAMETER_HOSTNAME = 'hostname';
    public const PARAMETER_PORT = 'port';
    public const PARAMETER_ENCRYPTION = 'encryption';
    public const PARAMETER_LDAP_IMPLEMENTATION = 'ldapImplementation';

    public const PARAMETER_BIND_ANONYMOUSLY = 'bindAnonymously';
    public const PARAMETER_DISTINGUISHED_NAME = 'distinguishedName';
    public const PARAMETER_DISTINGUISHED_PASSWORD = 'distinguishedPassword';
    public const PARAMETER_BASE_DISTINGUISHED_NAME = 'baseDistinguishedName';
    public const PARAMETER_SEARCH_SCOPE = 'searchScope';
    public const PARAMETER_USER_NAME_ATTRIBUTE = 'userNameAttribute';

    public const PARAMETER_DATA_MAPPING = 'dataMapping';
    public const PARAMETER_FIRST_NAME = 'firstname';
    public const PARAMETER_MIDDLE_NAME = 'middlename';
    public const PARAMETER_LAST_NAME = 'lastname';
    public const PARAMETER_USER_STATUS = 'userStatus';
    public const PARAMETER_WORK_EMAIL = 'workEmail';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';

    public const PARAMETER_GROUP_OBJECT_CLASS = 'groupObjectClass';
    public const PARAMETER_GROUP_OBJECT_FILTER = 'groupObjectFilter';
    public const PARAMETER_GROUP_NAME_ATTRIBUTE = 'groupNameAttribute';
    public const PARAMETER_GROUP_MEMBERS_ATTRIBUTE = 'groupMembersAttribute';
    public const PARAMETER_GROUP_MEMBERSHIP_ATTRIBUTE = 'groupMembershipAttribute';
    public const PARAMETER_SYNC_INTERVAL = 'syncInterval';


    public const ENCRYPTION_NONE = 'none';
    public const ENCRYPTION_TLS = 'tls';
    public const ENCRYPTION_SSL = 'ssl';

    public const LDAP_IMPLEMENTATION_OPEN_LDAP = 'OpenLDAP';
    public const LDAP_IMPLEMENTATION_ACTIVE_DIRECTORY = 'ActiveDirectory';

    public const PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH = 100;
    public const PARAMETER_RULE_HOST_NAME_MAX_LENGTH = 255;
    public const PARAMETER_RULE_DISTINGUISHED_NAME_MAX_LENGTH = 255;
    public const PARAMETER_RULE_DISTINGUISHED_PASSWORD_MAX_LENGTH  = 255;
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
        $this->validate($dataMapping, $this->getParamRuleCollection());

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
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_BASE_DISTINGUISHED_NAME
            )
        );

        $this->setConfigAttributes($ldapSettings);
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
                    self::PARAMETER_DISTINGUISHED_NAME
                )
            );

            $password = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DISTINGUISHED_PASSWORD
            );
            if (!is_null($password)) {
                $ldapSetting->setBindUserPassword($password);
            }
        }
        $ldapSetting->setUserNameAttribute(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_USER_NAME_ATTRIBUTE
            )
        );
        $ldapSetting->setBaseDN(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_BASE_DISTINGUISHED_NAME
            )
        );
        $ldapSetting->setEnable(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ENABLED
            )
        );
        $ldapSetting->setSearchScope(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SEARCH_SCOPE
            )
        );
        $ldapSetting->setDataMapping(
            $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DATA_MAPPING
            )
        );
        $ldapSetting->setGroupObjectClass(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_GROUP_OBJECT_CLASS
            )
        );
        $ldapSetting->setGroupObjectFilter(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_GROUP_OBJECT_FILTER
            )
        );
        $ldapSetting->setGroupNameAttribute(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_GROUP_NAME_ATTRIBUTE
            )
        );
        $ldapSetting->setGroupMembersAttribute(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_GROUP_MEMBERS_ATTRIBUTE
            )
        );
        $ldapSetting->setGroupMembershipAttribute(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_GROUP_MEMBERSHIP_ATTRIBUTE
            )
        );
        $ldapSetting->setSyncInterval(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SYNC_INTERVAL
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ENABLED,
                new Rule(Rules::BOOL_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_HOSTNAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_HOST_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_PORT,
                new Rule(Rules::NUMBER)
            ),
            new ParamRule(
                self::PARAMETER_ENCRYPTION,
                new Rule(Rules::STRING_TYPE),
                new Rule(
                    Rules::IN,
                    [
                        [
                            self::ENCRYPTION_NONE,
                            self::ENCRYPTION_TLS,
                            self::ENCRYPTION_SSL
                        ]
                    ]
                )
            ),
            new ParamRule(
                self::PARAMETER_LDAP_IMPLEMENTATION,
                new Rule(Rules::STRING_TYPE),
                new Rule(
                    Rules::IN,
                    [
                        [
                            self::LDAP_IMPLEMENTATION_OPEN_LDAP,
                            self::LDAP_IMPLEMENTATION_ACTIVE_DIRECTORY
                        ]
                    ]
                )
            ),
            new ParamRule(
                self::PARAMETER_BIND_ANONYMOUSLY,
                new Rule(Rules::BOOL_TYPE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DISTINGUISHED_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_DISTINGUISHED_NAME_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DISTINGUISHED_PASSWORD,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_DISTINGUISHED_PASSWORD_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_BASE_DISTINGUISHED_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_BASE_DISTINGUISHED_NAME_MAX_LENGTH])
                )
            ),
            new ParamRule(
                self::PARAMETER_SEARCH_SCOPE,
                new Rule(Rules::STRING_TYPE),
                new Rule(
                    Rules::IN,
                    [
                        [QueryInterface::SCOPE_SUB, QueryInterface::SCOPE_ONE]
                    ]
                )
            ),
            new ParamRule(
                self::PARAMETER_USER_NAME_ATTRIBUTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_GROUP_OBJECT_CLASS,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_GROUP_OBJECT_FILTER,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_GROUP_NAME_ATTRIBUTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_GROUP_MEMBERS_ATTRIBUTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_GROUP_MEMBERSHIP_ATTRIBUTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_SYNC_INTERVAL,
                new Rule(Rules::NUMBER),
            ),
            new ParamRule(
                self::PARAMETER_DATA_MAPPING,
                new Rule(Rules::ARRAY_TYPE)
            )
        );
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
