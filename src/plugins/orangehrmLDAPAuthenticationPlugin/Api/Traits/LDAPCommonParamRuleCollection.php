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

namespace OrangeHRM\LDAP\Api\Traits;

use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\LDAP\Api\LDAPConfigAPI;
use OrangeHRM\LDAP\Dto\LDAPEmployeeSelectorMapping;
use Symfony\Component\Ldap\Adapter\QueryInterface;

trait LDAPCommonParamRuleCollection
{
    /**
     * @return ParamRuleCollection
     */
    protected function getParamRuleCollection(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                LDAPConfigAPI::PARAMETER_ENABLED,
                new Rule(Rules::BOOL_VAL)
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_HOSTNAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_HOST_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_PORT,
                new Rule(Rules::NUMBER)
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_ENCRYPTION,
                new Rule(Rules::STRING_TYPE),
                new Rule(
                    Rules::IN,
                    [[LDAPConfigAPI::ENCRYPTION_NONE, LDAPConfigAPI::ENCRYPTION_TLS, LDAPConfigAPI::ENCRYPTION_SSL]]
                )
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_LDAP_IMPLEMENTATION,
                new Rule(Rules::STRING_TYPE),
                new Rule(
                    Rules::IN,
                    [
                        [
                            LDAPConfigAPI::LDAP_IMPLEMENTATION_OPEN_LDAP,
                            LDAPConfigAPI::LDAP_IMPLEMENTATION_ACTIVE_DIRECTORY
                        ]
                    ]
                )
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_BIND_ANONYMOUSLY,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_BIND_USER_DISTINGUISHED_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(
                        Rules::LENGTH,
                        [null, LDAPConfigAPI::PARAMETER_RULE_BIND_USER_DISTINGUISHED_NAME_MAX_LENGTH]
                    )
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_BIND_USER_PASSWORD,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_BIND_USER_PASSWORD_MAX_LENGTH])
                ),
                true
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_DATA_MAPPING,
                new Rule(Rules::ARRAY_TYPE)
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_USER_LOOKUP_SETTINGS,
                new Rule(Rules::ARRAY_TYPE),
                new Rule(Rules::NOT_EMPTY)
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_MERGE_LDAP_USERS_WITH_EXISTING_SYSTEM_USERS,
                new Rule(Rules::BOOL_VAL)
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_SYNC_INTERVAL,
                new Rule(Rules::INT_VAL),
                new Rule(Rules::BETWEEN, [1, 23]),
            ),
        );
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getParamRuleCollectionForDataMapping(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                LDAPConfigAPI::PARAMETER_FIRST_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_MIDDLE_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
                ),
                true
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_LAST_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_USER_STATUS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_WORK_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_EMPLOYEE_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
                ),
                true
            ),
        );
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getParamRuleCollectionForUserLookupSettings(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                LDAPConfigAPI::PARAMETER_BASE_DISTINGUISHED_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_BASE_DISTINGUISHED_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_SEARCH_SCOPE,
                new Rule(Rules::IN, [[QueryInterface::SCOPE_SUB, QueryInterface::SCOPE_ONE]]),
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_USER_NAME_ATTRIBUTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
            ),
            new ParamRule(
                LDAPConfigAPI::PARAMETER_USER_SEARCH_FILTER,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_USER_SEARCH_FILTER_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_USER_UNIQUE_ID_ATTRIBUTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LDAPConfigAPI::PARAMETER_EMPLOYEE_SELECTOR_MAPPING,
                    new Rule(Rules::ARRAY_TYPE),
                    new Rule(
                        Rules::EACH,
                        [
                            new Rules\Composite\AllOf(
                                new Rule(
                                    Rules::KEY,
                                    [
                                        LDAPConfigAPI::PARAMETER_EMPLOYEE_SELECTOR_MAPPING_FIELD,
                                        new Rules\Composite\AllOf(
                                            new Rule(Rules::IN, [LDAPEmployeeSelectorMapping::ALLOWED_FIELDS])
                                        )
                                    ]
                                ),
                                new Rule(
                                    Rules::KEY,
                                    [
                                        LDAPConfigAPI::PARAMETER_EMPLOYEE_SELECTOR_MAPPING_ATTRIBUTE_NAME,
                                        new Rules\Composite\AllOf(
                                            new Rule(Rules::STRING_TYPE),
                                            new Rule(
                                                Rules::LENGTH,
                                                [null, LDAPConfigAPI::PARAMETER_RULE_ATTRIBUTE_MAX_LENGTH]
                                            )
                                        )
                                    ]
                                ),
                            )
                        ]
                    )
                )
            ),
        );
    }
}
