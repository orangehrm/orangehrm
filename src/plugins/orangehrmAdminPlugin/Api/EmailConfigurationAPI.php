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
use OrangeHRM\Core\Utility\MailTransport;
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
    public const PARAM_RULE_MAIL_TYPE_MAP = [
        MailTransport::SCHEME_SENDMAIL,
        MailTransport::SCHEME_SMTP,
        MailTransport::SCHEME_SECURE_SMTP
    ];

    public const PARAM_RULE_AUTH_TYPE_MAP = [
        EmailConfiguration::AUTH_TYPE_LOGIN,
        EmailConfiguration::AUTH_TYPE_NONE
    ];

    public const DEFAULT_PARAMETER_MAIL_TYPE = MailTransport::SCHEME_SMTP;
    public const DEFAULT_PARAMETER_AUTH_TYPE = EmailConfiguration::AUTH_TYPE_LOGIN;
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
     * @OA\Get(
     *     path="/api/v2/admin/email-configuration",
     *     tags={"Admin/Email Configuration"},
     *     summary="Get Email Configuration",
     *     operationId="get-email-configuration",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-EmailConfigurationModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
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
     * @OA\Put(
     *     path="/api/v2/admin/email-configuration",
     *     tags={"Admin/Email Configuration"},
     *     summary="Update Email Configuration",
     *     operationId="update-email-configuration",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="mailType", type="string"),
     *             @OA\Property(property="sentAs", type="string"),
     *             @OA\Property(property="smtpHost", type="string"),
     *             @OA\Property(property="smtpPort", type="integer"),
     *             @OA\Property(property="smtpUsername", type="string"),
     *             @OA\Property(property="smtpPassword", type="string"),
     *             @OA\Property(property="smtpAuthType", type="string"),
     *             @OA\Property(property="smtpSecurityType", type="string"),
     *             @OA\Property(property="testEmailAddress", type="string"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-LanguageModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="testEmailStatus", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $emailConfiguration = $this->saveEmailConfigurationInfo();
        $testEmail = $this->getRequestParams()->getStringOrNull(
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
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_MAIL_TYPE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAIL_TYPE_MAX_LENGTH]),
                    new Rule(Rules::IN, [self::PARAM_RULE_MAIL_TYPE_MAP])
                )
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
                    self::PARAMETER_SMTP_PORT,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_PORT_MAX_LENGTH]),
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
            ...$this->getMailTypeDependentRules()
        );
    }

    /**
     * @return array
     */
    private function getMailTypeDependentRules(): array
    {
        $rules = [];

        $mailType = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MAIL_TYPE
        );

        $authType = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SMTP_AUTH_TYPE
        );

        $hostRequired = in_array($mailType, [MailTransport::SCHEME_SECURE_SMTP, MailTransport::SCHEME_SMTP]);

        $usernameRequired = $hostRequired && $authType === self::DEFAULT_PARAMETER_AUTH_TYPE;

        $emailConfiguration = $usernameRequired ?
            $this->getEmailConfigurationService()->getEmailConfigurationDao()->getEmailConfiguration() :
            null;

        $passwordRequired = $usernameRequired && (is_null($emailConfiguration) || is_null($emailConfiguration->getSmtpPassword()));

        $rules[] = $hostRequired ?
            $this->getValidationDecorator()->requiredParamRule($this->getSmtpHostRule()) :
            $this->getValidationDecorator()->notRequiredParamRule($this->getSmtpHostRule());

        $rules[] = $hostRequired ?
            $this->getValidationDecorator()->requiredParamRule($this->getSmtpAuthTypeRule()) :
            $this->getValidationDecorator()->notRequiredParamRule($this->getSmtpAuthTypeRule(), true);

        $rules[] = $usernameRequired ?
            $this->getValidationDecorator()->requiredParamRule($this->getSmtpUsernameRule()) :
            $this->getValidationDecorator()->notRequiredParamRule($this->getSmtpUsernameRule(), true);

        $rules[] = $passwordRequired ?
            $this->getValidationDecorator()->requiredParamRule($this->getSmtpPasswordRule()) :
            $this->getValidationDecorator()->notRequiredParamRule($this->getSmtpPasswordRule(), true);

        return $rules;
    }

    /**
     * @return ParamRule
     */
    private function getSmtpHostRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_SMTP_HOST,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_HOST_MAX_LENGTH]),
        );
    }

    /**
     * @return ParamRule
     */
    private function getSmtpUsernameRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_SMTP_USERNAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_USERNAME_MAX_LENGTH]),
        );
    }

    /**
     * @return ParamRule
     */
    private function getSmtpPasswordRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_SMTP_PASSWORD,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_PASSWORD_MAX_LENGTH]),
        );
    }

    /**
     * @return ParamRule
     */
    private function getSmtpAuthTypeRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_SMTP_AUTH_TYPE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SMTP_AUTH_TYPE_MAX_LENGTH]),
            new Rule(Rules::IN, [self::PARAM_RULE_AUTH_TYPE_MAP])
        );
    }

    /**
     * @return EmailConfiguration
     */
    public function saveEmailConfigurationInfo(): EmailConfiguration
    {
        $emailConfiguration = $this->getEmailConfigurationService()->getEmailConfigurationDao()->getEmailConfiguration();
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
