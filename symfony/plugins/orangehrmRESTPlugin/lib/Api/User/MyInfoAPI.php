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

namespace Orangehrm\Rest\Api\User;

use Orangehrm\Rest\Api\Admin\UserAPI;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\EmployeePhotoAPI;
use Orangehrm\Rest\Api\Pim\EmployeeSearchAPI;
use Orangehrm\Rest\Api\User\Model\UserModel;
use Orangehrm\Rest\Http\Response;
use sfContext;

class MyInfoAPI extends EndPoint
{
    const PARAMETER_WITH_PHOTO = 'withPhoto';

    /**
     * @var null|UserAPI
     */
    private $systemUserApi = null;

    /**
     * @var null|EmployeePhotoAPI
     */
    private $apiEmployeePhoto = null;

    /**
     * @var null|EmployeeSearchAPI
     */
    private $apiEmployeeSearch = null;

    /**
     * @return UserAPI|null
     */
    public function getSystemUserApi(): UserAPI
    {
        if (is_null($this->systemUserApi)) {
            $this->systemUserApi = new UserAPI($this->getRequest());
        }
        return $this->systemUserApi;
    }

    /**
     * @param UserAPI|null $systemUserApi
     */
    public function setSystemUserApi(UserAPI $systemUserApi)
    {
        $this->systemUserApi = $systemUserApi;
    }

    /**
     * @return EmployeePhotoAPI|null
     */
    public function getApiEmployeePhoto(): EmployeePhotoAPI
    {
        if (is_null($this->apiEmployeePhoto)) {
            $this->apiEmployeePhoto = new EmployeePhotoAPI($this->getRequest());
        }
        return $this->apiEmployeePhoto;
    }

    /**
     * @param EmployeePhotoAPI|null $apiEmployeePhoto
     */
    public function setApiEmployeePhoto(EmployeePhotoAPI $apiEmployeePhoto)
    {
        $this->apiEmployeePhoto = $apiEmployeePhoto;
    }

    /**
     * @return EmployeeSearchAPI|null
     */
    public function getApiEmployeeSearch(): EmployeeSearchAPI
    {
        if (is_null($this->apiEmployeeSearch)) {
            $this->apiEmployeeSearch = new EmployeeSearchAPI($this->getRequest());
        }
        return $this->apiEmployeeSearch;
    }

    /**
     * @param EmployeeSearchAPI|null $apiEmployeeSearch
     */
    public function setApiEmployeeSearch(EmployeeSearchAPI $apiEmployeeSearch)
    {
        $this->apiEmployeeSearch = $apiEmployeeSearch;
    }

    public function getMyInfo()
    {
        $params = $this->filterParameters();
        $response = [];
        $user = $this->getSystemUserApi()->getSystemUserById($this->getUserAttribute('auth.userId'));
        $userModel = new UserModel($user);
        $response['user'] = $userModel->toArray();
        $employee = $this->getApiEmployeeSearch()->getEmployeeById($user->getEmployeeId());
        $response['employee'] = $employee->toArray();
        if ($params[self::PARAMETER_WITH_PHOTO]) {
            $employeePhoto = $this->getApiEmployeePhoto()->getEmployeePhotoById($user->getEmployeeId());
            $response['employeePhoto'] = $employeePhoto;
        }
        return new Response($response);
    }

    protected function getUserAttribute(string $name): string
    {
        return sfContext::getInstance()->getUser()->getAttribute($name);
    }

    protected function filterParameters(): array
    {
        $params = [];
        $withPhoto = $this->getRequestParams()->getQueryParam(self::PARAMETER_WITH_PHOTO, 'false');
        if (!($withPhoto == 'true' || $withPhoto == 'false')) {
            throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_WITH_PHOTO));
        }
        $withPhoto = $withPhoto == 'true' ? true : false;
        $params[self::PARAMETER_WITH_PHOTO] = $withPhoto;
        return $params;
    }
}
