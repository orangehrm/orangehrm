<?php
/*
 *
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
 *
 */

namespace OrangeHRM\Admin\Api;


use Cassandra\Exception\UnauthorizedException;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\Admin\Service\EmploymentStatusService;
use OrangeHRM\Admin\Api\Model\EmploymentStatusModel;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use \DaoException;

class EmploymentStatusAPI extends EndPoint
{

    const PARAMETER_ID = 'id';
    const PARAMETER_IDS = 'ids';
    const PARAMETER_NAME = 'name';

    const PARAMETER_SORT_FIELD = 'sortField';
    const PARAMETER_SORT_ORDER = 'sortOrder';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_LIMIT = 'limit';

    /**
     * @var null|EmploymentStatusService
     */
    protected $employmentStatusService = null;

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
    public function setEmploymentStatusService(EmploymentStatusService $employmentStatusService)
    {
        $this->employmentStatusService = $employmentStatusService;
    }

    public function getEmploymentStatus(): Response
    {

        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        $employmentStatus = $this->getEmploymentStatusService()->getEmploymentStatusById($id);

        if (!$employmentStatus instanceof EmploymentStatus) {
            throw new RecordNotFoundException('No Record Found');
        }

        return new Response(
            (new EmploymentStatusModel($employmentStatus))->toArray()
        );
    }

    public function getEmploymentStatusList(){
        // TODO:: Check data group permission
        $sortField = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD, 'es.name');
        $sortOrder = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER, 'ASC');
        $limit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET, 0);

        $count = $this->getEmploymentStatusService()->getEmploymentStatusList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            true
        );
        if (!($count > 0)) {
            return new Response([], [], ['total' => 0]);        }

        $result = [];
        $employmentStatusList = $this->getEmploymentStatusService()->getEmploymentStatusList($sortField, $sortOrder, $limit, $offset);
        foreach ($employmentStatusList as $employmentStatus) {
            array_push($result, (new EmploymentStatusModel($employmentStatus))->toArray());
        }
        return new Response($result, [], ['total' => $count]);
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function saveEmploymentStatus()
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $name = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        if (!empty($id)) {
            $employeeStatus = $this->getEmploymentStatusService()->getEmploymentStatusById($id);
            if($employeeStatus==null){
                throw new RecordNotFoundException('No Record Found');
            }
        } else {
            $employeeStatus = new EmploymentStatus();
        }

        $employeeStatus->setName($name);
        $employeeStatus = $this->getEmploymentStatusService()->saveEmploymentStatus($employeeStatus);

        return new Response(
            (new EmploymentStatusModel($employeeStatus))->toArray()
        );
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function deleteJobCategories()
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getPostParam(self::PARAMETER_IDS);
        $this->getEmploymentStatusService()->deleteEmploymentStatus($ids);
        return new Response($ids);
    }
}
