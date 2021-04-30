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

use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\Admin\Service\EmploymentStatusService;
use OrangeHRM\Admin\Api\Model\EmploymentStatusModel;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use \DaoException;
use Exception;
use OrangeHRM\Core\Api\CommonParams;

class EmploymentStatusAPI extends Endpoint implements CrudEndpoint
{
    const PARAMETER_NAME = 'name';

    const FILTER_NAME = 'name';

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
     * @return EndpointGetOneResult
     * @throws RecordNotFoundException
     * @throws DaoException
     * @throws Exception
     */
    public function getOne(): EndpointGetOneResult
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $employmentStatus = $this->getEmploymentStatusService()->getEmploymentStatusById($id);
        if (!$employmentStatus instanceof EmploymentStatus) {
            throw new RecordNotFoundException('No Record Found');
        }

        return new EndpointGetOneResult(EmploymentStatusModel::class, $employmentStatus);
    }

    /**
     * @return EndpointGetAllResult
     * @throws DaoException
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getAll(): EndpointGetAllResult
    {
        // TODO:: Check data group permission
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();
        $this->setSortingAndPaginationParams($employmentStatusSearchParams);
        $employmentStatusSearchParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $employmentStatuses = $this->getEmploymentStatusService()->searchEmploymentStatus($employmentStatusSearchParams);
        return new EndpointGetAllResult(
            EmploymentStatusModel::class,
            $employmentStatuses,
            new ParameterBag(
                [
                    'total' => $this->getEmploymentStatusService()->getSearchEmploymentStatusesCount(
                        $employmentStatusSearchParams
                    )
                ]
            )
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        // TODO:: Check data group permission
        $employmentStatus = $this->saveEmploymentStatus();

        return new EndpointCreateResult(EmploymentStatusModel::class, $employmentStatus);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $employmentStatus = $this->saveEmploymentStatus();

        return new EndpointUpdateResult(EmploymentStatusModel::class, $employmentStatus);
    }

    /**
     * @return EmploymentStatus
     * @throws DaoException
     * @throws RecordNotFoundException
     */
    public function saveEmploymentStatus(): EmploymentStatus
    {
        // TODO:: Check data group permission
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
    public function delete(): EndpointDeleteResult
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmploymentStatusService()->deleteEmploymentStatus($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }
}
