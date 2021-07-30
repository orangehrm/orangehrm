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
use OrangeHRM\Admin\Api\Model\EmploymentStatusModel;
use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Admin\Service\EmploymentStatusService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmploymentStatus;

class EmploymentStatusAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';

    public const FILTER_NAME = 'name';

    /**
     * @var null|EmploymentStatusService
     */
    protected ?EmploymentStatusService $employmentStatusService = null;

    /**
     * @return EmploymentStatusService
     */
    public function getEmploymentStatusService(): EmploymentStatusService
    {
        if (is_null($this->employmentStatusService)) {
            $this->employmentStatusService = new EmploymentStatusService();
        }
        return $this->employmentStatusService;
    }

    /**
     * @param EmploymentStatusService $employmentStatusService
     */
    public function setEmploymentStatusService(EmploymentStatusService $employmentStatusService): void
    {
        $this->employmentStatusService = $employmentStatusService;
    }

    /**
     * @return EndpointResourceResult
     * @throws RecordNotFoundException
     * @throws DaoException
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $employmentStatus = $this->getEmploymentStatusService()->getEmploymentStatusById($id);
        if (!$employmentStatus instanceof EmploymentStatus) {
            throw new RecordNotFoundException();
        }

        return new EndpointResourceResult(EmploymentStatusModel::class, $employmentStatus);
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
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();
        $this->setSortingAndPaginationParams($employmentStatusSearchParams);
        $employmentStatusSearchParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $employmentStatuses = $this->getEmploymentStatusService()->searchEmploymentStatus(
            $employmentStatusSearchParams
        );
        return new EndpointCollectionResult(
            EmploymentStatusModel::class,
            $employmentStatuses,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getEmploymentStatusService(
                    )->getSearchEmploymentStatusesCount(
                        $employmentStatusSearchParams
                    )
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_NAME),
            ...$this->getSortingAndPaginationParamsRules(EmploymentStatusSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $employmentStatus = $this->saveEmploymentStatus();

        return new EndpointResourceResult(EmploymentStatusModel::class, $employmentStatus);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_NAME),
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $employmentStatus = $this->saveEmploymentStatus();

        return new EndpointResourceResult(EmploymentStatusModel::class, $employmentStatus);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(self::PARAMETER_NAME),
        );
    }

    /**
     * @return EmploymentStatus
     * @throws DaoException
     * @throws RecordNotFoundException
     */
    public function saveEmploymentStatus(): EmploymentStatus
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!empty($id)) {
            $employeeStatus = $this->getEmploymentStatusService()->getEmploymentStatusById($id);
            if ($employeeStatus == null) {
                throw new RecordNotFoundException('No Record Found');
            }
        } else {
            $employeeStatus = new EmploymentStatus();
        }

        $employeeStatus->setName($name);
        return $this->getEmploymentStatusService()->saveEmploymentStatus($employeeStatus);
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmploymentStatusService()->deleteEmploymentStatus($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
