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

use DaoException;
use Exception;
use OrangeHRM\Admin\Api\Model\LicenseModel;
use OrangeHRM\Admin\Dto\LicenseSearchFilterParams;
use OrangeHRM\Admin\Service\LicenseService;
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
use OrangeHRM\Entity\License;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;

class LicenseAPI extends EndPoint implements CrudEndpoint
{
    /**
     * @var null|LicenseService
     */
    protected ?LicenseService $licenseService = null;

    public const PARAMETER_ID = 'id';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_NAME = 'name';

    /**
     *
     * @return LicenseService
     */
    public function getLicenseService(): LicenseService
    {
        if (is_null($this->licenseService)) {
            $this->licenseService = new LicenseService();
        }
        return $this->licenseService;
    }

    /**
     * @param LicenseService $licenseService
     */
    public function setLicenseService(LicenseService $licenseService): void
    {
        $this->licenseService = $licenseService;
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
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $licenses = $this->getLicenseService()->getLicenseById($id);
        if (!$licenses instanceof License) {
            throw new RecordNotFoundException('No Record Found');
        }

        return new EndpointGetOneResult(LicenseModel::class, $licenses);
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

        $licenseParamHolder = new LicenseSearchFilterParams();
        $this->setSortingAndPaginationParams($licenseParamHolder);
        $licenses = $this->getLicenseService()->getLicenseList($licenseParamHolder);
        $count = $this->getLicenseService()->getLicenseCount($licenseParamHolder);
        return new EndpointGetAllResult(LicenseModel::class, $licenses, new ParameterBag(['total' => $count]));
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        // TODO:: Check data group permission
        $licenses = $this->saveLicense();

        return new EndpointCreateResult(LicenseModel::class, $licenses);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $licenses = $this->saveLicense();

        return new EndpointUpdateResult(LicenseModel::class, $licenses);
    }

    /**
     * @return License
     * @throws DaoException
     */
    public function saveLicense(): License
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!empty($id)) {
            $education = $this->getLicenseService()->getLicenseById($id);
        } else {
            $education = new License();
        }

        $education->setName($name);
        return $this->getLicenseService()->saveLicense($education);
    }

    /**
     *
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointDeleteResult
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_IDS);
        $this->getLicenseService()->deleteLicenses($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }
}
