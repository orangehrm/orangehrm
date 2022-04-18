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

use OrangeHRM\Admin\Api\Model\EmailSubscriptionModel;
use OrangeHRM\Admin\Dto\EmailSubscriptionSearchFilterParams;
use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Admin\Service\EmailSubscriptionService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

class EmailSubscriptionAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_ENABLED_STATUS = 'enabled';

    /**
     * @var null|EmailSubscriptionService
     */
    protected ?EmailSubscriptionService $emailSubscriptionService = null;

    /**
     * @return EmailSubscriptionService
     */
    protected function getEmailSubscriptionService(): EmailSubscriptionService
    {
        if (!$this->emailSubscriptionService instanceof EmailSubscriptionService) {
            $this->emailSubscriptionService = new EmailSubscriptionService();
        }
        return $this->emailSubscriptionService;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $emailSubscriptionSearchFilterParams = new EmailSubscriptionSearchFilterParams();
        $this->setSortingAndPaginationParams($emailSubscriptionSearchFilterParams);
        $emailSubscriptions = $this->getEmailSubscriptionService()
            ->getEmailSubscriptionDao()
            ->getEmailSubscriptions($emailSubscriptionSearchFilterParams);
        $emailSubscriptionsCount = $this->getEmailSubscriptionService()
            ->getEmailSubscriptionDao()
            ->getEmailSubscriptionListCount($emailSubscriptionSearchFilterParams);

        return new EndpointCollectionResult(
            EmailSubscriptionModel::class,
            $emailSubscriptions,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $emailSubscriptionsCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(EmailSubscriptionSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
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

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $emailSubscriptionId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $emailSubscription = $this->getEmailSubscriptionService()
            ->getEmailSubscriptionDao()
            ->getEmailSubscriptionById($emailSubscriptionId);
        $this->throwRecordNotFoundExceptionIfNotExist($emailSubscription, EmailNotification::class);
        $enabledStatus = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENABLED_STATUS
        );
        $emailSubscription->setEnabled($enabledStatus);
        $this->getEmailSubscriptionService()->getEmailSubscriptionDao()->saveEmailSubscription($emailSubscription);
        return new EndpointResourceResult(EmailSubscriptionModel::class, $emailSubscription);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(
                self::PARAMETER_ENABLED_STATUS,
                new Rule(Rules::REQUIRED),
                new Rule(Rules::BOOL_TYPE)
            ),
        );
    }
}
