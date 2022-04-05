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

use Exception;
use OrangeHRM\Admin\Api\Model\EmailConfigurationModel;
use OrangeHRM\Admin\Service\EmailConfigurationService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmailConfiguration;
use Symfony\Component\Mailer\Exception\TransportException as MailerException;

class EmailConfigurationAPI extends Endpoint implements ResourceEndpoint
{
    public const PARAMETER_MAIL_TYPE = 'mailType';
    public const PARAMETER_SENT_AS = 'sentAs';
    public const PARAMETER_SMTP_HOST = 'smtpHost';
    public const PARAMETER_SMTP_PORT = 'smtpPort';
    public const PARAMETER_SMTP_USERNAME = 'smtpUsername';
    public const PARAMETER_SMTP_PASSWORD = 'smtpPassword';
    public const PARAMETER_SMTP_AUTH_TYPE = 'smtpAuthType';
    public const PARAMETER_SMTP_SECURITY_TYPE = 'smtpSecurityType';
    public const PARAMETER_TEST_EMAIL_ADDRESS = 'testEmailAddress';

    public const PARAM_RULE_MAIL_TYPE_MAX_LENGTH = 50;
    public const PARAM_RULE_SENT_AS_MAX_LENGTH = 100;
    public const PARAM_RULE_SMTP_HOST_MAX_LENGTH = 100;
    public const PARAM_RULE_SMTP_PORT_MAX_LENGTH = 10;
    public const PARAM_RULE_SMTP_USERNAME_MAX_LENGTH = 100;
    public const PARAM_RULE_SMTP_PASSWORD_MAX_LENGTH = 100;
    public const PARAM_RULE_SMTP_AUTH_TYPE_MAX_LENGTH = 50;
    public const PARAM_RULE_SMTP_SECURITY_TYPE_MAX_LENGTH = 50;
    public const PARAM_RULE_TEST_EMAIL_ADDRESS_MAX_LENGTH = 250;

    public const DEFAULT_PARAMETER_MAIL_TYPE = 'sendmail';
    public const DEFAULT_PARAMETER_AUTH_TYPE = 'login';
    public const DEFAULT_PARAMETER_SECURITY_TYPE = 'ssl';

    public const TEST_EMAIL_STATUS = 'testEmailStatus';

    /**
     * @var null|EmailConfigurationService
     */
    protected ?EmailConfigurationService $emailConfigurationService = null;

    /**
     * @return EmailConfigurationService
     */
    public function getEmailConfigurationService(): EmailConfigurationService
    {
        if (is_null($this->emailConfigurationService)) {
            $this->emailConfigurationService = new EmailConfigurationService();
        }
        return $this->emailConfigurationService;
    }

    /**
     * @param EmailConfigurationService $emailConfigurationService
     */
    public function setEmailConfigurationService(EmailConfigurationService $emailConfigurationService): void
    {
        $this->emailConfigurationService = $emailConfigurationService;
    }

    /**
     * @return EndpointResourceResult
     * @throws Exception
     */
    public function getOne(): EndpointResourceResult
    {
        $emailConfiguration = $this->getEmailConfigurationService()->getEmailConfigurationDao()->getEmailConfiguration(
        );
        if (!$emailConfiguration instanceof EmailConfiguration) {
            $emailConfiguration = new EmailConfiguration();
            $emailConfiguration->setMailType(self::DEFAULT_PARAMETER_MAIL_TYPE);
            $emailConfiguration->setSentAs("");
            $emailConfiguration->setSmtpHost("");
            $emailConfiguration->setSmtpPort(null);
            $emailConfiguration->setSmtpUsername("");
            $emailConfiguration->setSmtpPassword("");
            $emailConfiguration->setSmtpAuthType(self::DEFAULT_PARAMETER_AUTH_TYPE);
            $emailConfiguration->setSmtpSecurityType(self::DEFAULT_PARAMETER_SECURITY_TYPE);
        }

        return new EndpointResourceResult(EmailConfigurationModel::class, $emailConfiguration);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
            ),
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $emailConfiguration = $this->saveEmailConfigurationInfo();
        $testEmail = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TEST_EMAIL_ADDRESS
        );
        $testEmailStatus = 1;
        if (!empty($testEmail)) {
            try {
                $this->getEmailConfigurationService()->sendTestMail($testEmail);
            } catch (MailerException $e) {
                $testEmailStatus = 0;
            }
        }
        return new EndpointResourceResult(
            EmailConfigurationModel::class,
            $emailConfiguration,
            new ParameterBag(
                [
                    self::TEST_EMAIL_STATUS => $testEmailStatus,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MAIL_TYPE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAIL_TYPE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_SENT_AS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SENT_AS_MAX_LENGTH]),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMTP_HOST,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_HOST_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMTP_PORT,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_PORT_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMTP_USERNAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_USERNAME_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMTP_PASSWORD,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_PASSWORD_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMTP_AUTH_TYPE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_AUTH_TYPE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMTP_SECURITY_TYPE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_SECURITY_TYPE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TEST_EMAIL_ADDRESS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TEST_EMAIL_ADDRESS_MAX_LENGTH]),
                ),
                true
            ),
        );
    }

    /**
     * @return EmailConfiguration
     * @throws DaoException
     * @throws DaoException
     */
    public function saveEmailConfigurationInfo(): EmailConfiguration
    {
        $emailConfiguration = $this->getEmailConfigurationService()->getEmailConfigurationDao()->getEmailConfiguration(
        );
        if ($emailConfiguration == null) {
            $emailConfiguration = new EmailConfiguration();
        }
        $this->setEmailConfiguration($emailConfiguration);
        return $this->getEmailConfigurationService()->getEmailConfigurationDao()->saveEmailConfiguration(
            $emailConfiguration
        );
    }

    public function setEmailConfiguration(EmailConfiguration $emailConfiguration)
    {
        $emailConfiguration->setMailType(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_MAIL_TYPE
            )
        );
        $emailConfiguration->setSentAs(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SENT_AS
            )
        );
        $emailConfiguration->setSmtpHost(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SMTP_HOST
            )
        );
        $emailConfiguration->setSmtpPort(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SMTP_PORT
            )
        );
        $emailConfiguration->setSmtpUsername(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SMTP_USERNAME
            )
        );

        $password = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SMTP_PASSWORD
        );

        if (!is_null($password)) {
            $emailConfiguration->setSmtpPassword($password);
        }

        $emailConfiguration->setSmtpAuthType(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SMTP_AUTH_TYPE
            )
        );
        $emailConfiguration->setSmtpSecurityType(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SMTP_SECURITY_TYPE
            )
        );
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function delete(): EndpointResourceResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
