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

use OrangeHRM\Admin\Api\Model\EmailSubscriberModel;
use OrangeHRM\Admin\Dto\EmailSubscriberSearchFilterParams;
use OrangeHRM\Admin\Service\EmailSubscriberService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Entity\EmailSubscriber;

class EmailSubscriberAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;

    public const PARAMETER_EMAIL_SUBSCRIPTION_ID = 'emailSubscriptionId';
    public const PARAMETER_SUBSCRIBER_NAME = 'name';
    public const PARAMETER_SUBSCRIBER_EMAIL = 'email';

    public const PARAM_RULE_STRING_MAX_LENGTH = 100;
    public const PARAM_RULE_EMAIL_SUBSCRIPTION_ID_MAP = [
        EmailNotification::LEAVE_APPLICATION,
        EmailNotification::LEAVE_ASSIGNMENT,
        EmailNotification::LEAVE_APPROVAL,
        EmailNotification::LEAVE_CANCELLATION,
        EmailNotification::LEAVE_REJECTION
    ];

    /**
     * @var EmailSubscriberService|null
     */
    protected ?EmailSubscriberService $emailSubscriberService = null;

    /**
     * @return EmailSubscriberService
     */
    protected function getEmailSubscriberService(): EmailSubscriberService
    {
        if (!$this->emailSubscriberService instanceof EmailSubscriberService) {
            $this->emailSubscriberService = new EmailSubscriberService();
        }
        return $this->emailSubscriberService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/email-subscriptions/{emailSubscriptionId}/subscribers",
     *     tags={"Admin/Email Subscriber"},
     *     summary="List All Email Subscribers",
     *     operationId="list-all-email-subscribers",
     *     @OA\PathParameter(
     *         name="emailSubscriptionId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmailSubscriberSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Admin-EmailSubscriberModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="name", type="string", description="Name of the subscription")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $subscriptionId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_EMAIL_SUBSCRIPTION_ID
        );
        $emailSubscription = $this->getEmailSubscriberService()
            ->getEmailSubscriberDao()
            ->getEmailSubscriptionById($subscriptionId);
        $emailSubscriberSearchFilterParams = new EmailSubscriberSearchFilterParams();
        $this->setSortingAndPaginationParams($emailSubscriberSearchFilterParams);
        $emailSubscribers = $this->getEmailSubscriberService()
            ->getEmailSubscriberDao()
            ->getEmailSubscribersByEmailSubscriptionId($subscriptionId, $emailSubscriberSearchFilterParams);
        $emailSubscribersCount = $this->getEmailSubscriberService()
            ->getEmailSubscriberDao()
            ->getEmailSubscriberListCountByEmailSubscriptionId($subscriptionId, $emailSubscriberSearchFilterParams);

        return new EndpointCollectionResult(
            EmailSubscriberModel::class,
            $emailSubscribers,
            new ParameterBag(
                [CommonParams::PARAMETER_TOTAL => $emailSubscribersCount, 'name' => $emailSubscription->getName()]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_EMAIL_SUBSCRIPTION_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN, [self::PARAM_RULE_EMAIL_SUBSCRIPTION_ID_MAP])
            ),
            ...$this->getSortingAndPaginationParamsRules(EmailSubscriberSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/email-subscriptions/{emailSubscriptionId}/subscribers",
     *     tags={"Admin/Email Subscriber"},
     *     summary="Create an Email Subscriber",
     *     operationId="create-an-email-subscriber",
     *     @OA\PathParameter(
     *         name="emailSubscriptionId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             required={"name", "email"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-EmailSubscriberModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $emailSubscriber = new EmailSubscriber();
        $this->setParamsToEmailSubscriber($emailSubscriber);
        $this->getEmailSubscriberService()->getEmailSubscriberDao()->saveEmailSubscriber($emailSubscriber);
        return new EndpointResourceResult(EmailSubscriberModel::class, $emailSubscriber);
    }

    /**
     * @param EmailSubscriber $emailSubscriber
     */
    private function setParamsToEmailSubscriber(EmailSubscriber $emailSubscriber): void
    {
        $subscriptionId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_EMAIL_SUBSCRIPTION_ID
        );
        $emailSubscriberName = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIBER_NAME
        );
        $emailSubscriberEmailAddress = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIBER_EMAIL
        );
        $emailSubscription = $this->getEmailSubscriberService()
            ->getEmailSubscriberDao()
            ->getEmailSubscriptionById($subscriptionId);
        $emailSubscriber->setEmailNotification($emailSubscription);
        $emailSubscriber->setName($emailSubscriberName);
        $emailSubscriber->setEmail($emailSubscriberEmailAddress);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules($this->getEmailSubscriberCommonUniqueOption()),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/email-subscriptions/{emailSubscriptionId}/subscribers",
     *     tags={"Admin/Email Subscriber"},
     *     summary="Delete Email Subscribers",
     *     operationId="delete-email-subscribers",
     *     @OA\PathParameter(
     *         name="emailSubscriptionId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getEmailSubscriberService()->getEmailSubscriberDao()->getExistingEmailSubscriberIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getEmailSubscriberService()->getEmailSubscriberDao()->deleteEmailSubscribersByIds($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMAIL_SUBSCRIPTION_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN, [self::PARAM_RULE_EMAIL_SUBSCRIPTION_ID_MAP])
                ),
            ),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE),
                new Rule(
                    Rules::EACH,
                    [new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))]
                )
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/email-subscriptions/{emailSubscriptionId}/subscribers/{id}",
     *     tags={"Admin/Email Subscriber"},
     *     summary="Get an Email Subscriber",
     *     operationId="get-an-email-subscriber",
     *     @OA\PathParameter(
     *         name="emailSubscriptionId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-EmailSubscriberModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $emailSubscriberId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $subscriptionId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_EMAIL_SUBSCRIPTION_ID
        );
        $emailSubscriber = $this->getEmailSubscriberService()
            ->getEmailSubscriberDao()
            ->getEmailSubscriberById($emailSubscriberId, $subscriptionId);
        $this->throwRecordNotFoundExceptionIfNotExist($emailSubscriber, EmailSubscriber::class);
        return new EndpointResourceResult(EmailSubscriberModel::class, $emailSubscriber);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_EMAIL_SUBSCRIPTION_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN, [self::PARAM_RULE_EMAIL_SUBSCRIPTION_ID_MAP])
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/email-subscriptions/{emailSubscriptionId}/subscribers/{id}",
     *     tags={"Admin/Email Subscriber"},
     *     summary="Update an Email Susbcriber",
     *     operationId="update-an-email-subscriber",
     *     @OA\PathParameter(
     *         name="emailSubscriptionId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             required={"name", "email"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-EmailSubscriberModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $emailSubscriberId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $subscriptionId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_EMAIL_SUBSCRIPTION_ID
        );
        $emailSubscriber = $this->getEmailSubscriberService()
            ->getEmailSubscriberDao()
            ->getEmailSubscriberById($emailSubscriberId, $subscriptionId);
        $this->throwRecordNotFoundExceptionIfNotExist($emailSubscriber, EmailSubscriber::class);
        $this->setParamsToEmailSubscriber($emailSubscriber);
        $this->getEmailSubscriberService()->getEmailSubscriberDao()->saveEmailSubscriber($emailSubscriber);
        return new EndpointResourceResult(EmailSubscriberModel::class, $emailSubscriber);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = $this->getEmailSubscriberCommonUniqueOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules($uniqueOption),
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(?EntityUniquePropertyOption $uniqueOption = null): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIBER_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_STRING_MAX_LENGTH]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [EmailSubscriber::class, 'email', $uniqueOption])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMAIL_SUBSCRIPTION_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN, [self::PARAM_RULE_EMAIL_SUBSCRIPTION_ID_MAP])
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIBER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_STRING_MAX_LENGTH])
                ),
            ),
        ];
    }

    /**
     * @return EntityUniquePropertyOption
     */
    private function getEmailSubscriberCommonUniqueOption(): EntityUniquePropertyOption
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setMatchValues([
            'emailNotification' => $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_EMAIL_SUBSCRIPTION_ID
            )
        ]);
        return $uniqueOption;
    }
}
