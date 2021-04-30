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
use OrangeHRM\Admin\Api\Model\EducationModel;
use OrangeHRM\Admin\Service\EducationService;
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
use OrangeHRM\Entity\Education;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;

class EducationAPI extends EndPoint implements CrudEndpoint
{
    /**
     * @var null|EducationService
     */
    protected ?EducationService $educationService = null;

    public const PARAMETER_ID = 'id';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';

    /**
     *
     * @return EducationService
     */
    public function getEducationService(): EducationService
    {
        if (is_null($this->educationService)) {
            $this->educationService = new EducationService();
        }
        return $this->educationService;
    }

    /**
     * @param EducationService $educationService
     */
    public function setEducationService(EducationService $educationService): void
    {
        $this->educationService = $educationService;
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
        $id =$this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $educations = $this->getEducationService()->getEducationById($id);
        if (!$educations instanceof Education) {
            throw new RecordNotFoundException('No Record Found');
        }

        return new EndpointGetOneResult(EducationModel::class, $educations);
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
        $sortField = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_FIELD,
            'e.name'
        );
        $sortOrder = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_ORDER,
            'ASC'
        );
        $limit = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_OFFSET, 0);

        $count = $this->getEducationService()->getEducationList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            true
        );

        $educations = $this->getEducationService()->getEducationList($sortField, $sortOrder, $limit, $offset);

        return new EndpointGetAllResult(
            EducationModel::class, $educations,
            new ParameterBag(['total' => $count])
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        // TODO:: Check data group permission
        $educations = $this->saveEducation();

        return new EndpointCreateResult(EducationModel::class, $educations);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $educations = $this->saveEducation();

        return new EndpointUpdateResult(EducationModel::class, $educations);
    }

    /**
     * @return Education
     * @throws DaoException
     */
    public function saveEducation(): Education
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!empty($id)) {
            $education = $this->getEducationService()->getEducationById($id);
        } else {
            $education = new Education();
        }

        $education->setName($name);
        return $this->getEducationService()->saveEducation($education);
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
        $this->getEducationService()->deleteEducations($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }
}
