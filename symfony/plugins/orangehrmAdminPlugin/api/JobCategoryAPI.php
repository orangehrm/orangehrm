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

namespace OrangeHRM\Rest\Admin;

use DaoException;
use JobCategoryService;
use OrangeHRM\Entity\JobCategory;
use OrangeHRM\Rest\Admin\Model\JobCategoryModel;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class JobCategoryAPI extends EndPoint
{
    /**
     * @var null|JobCategoryService
     */
    protected $jobCategoryService = null;

    const PARAMETER_ID = 'id';
    const PARAMETER_IDS = 'ids';
    const PARAMETER_NAME = 'name';

    const PARAMETER_SORT_FIELD = 'sortField';
    const PARAMETER_SORT_ORDER = 'sortOrder';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_LIMIT = 'limit';

    /**
     * @return JobCategoryService
     */
    public function getJobCategoryService(): JobCategoryService
    {
        if (is_null($this->jobCategoryService)) {
            $this->jobCategoryService = new JobCategoryService();
        }
        return $this->jobCategoryService;
    }

    /**
     * @param JobCategoryService $jobCategoryService
     */
    public function setJobCategoryService(JobCategoryService $jobCategoryService)
    {
        $this->jobCategoryService = $jobCategoryService;
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getJobCategory(): Response
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $jobCategory = $this->getJobCategoryService()->getJobCategoryById($id);
        if (!$jobCategory instanceof JobCategory) {
            throw new RecordNotFoundException('No Record Found');
        }
        return new Response(
            (new JobCategoryModel($jobCategory))->toArray()
        );
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     * @throws DaoException
     */
    public function getJobCategories(): Response
    {
        // TODO:: Check data group permission
        $sortField = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD, 'jc.name');
        $sortOrder = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER, 'ASC');
        $limit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET, 0);

        $count = $this->getJobCategoryService()->getJobCategoryList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            true
        );
        if (!($count > 0)) {
            throw new RecordNotFoundException('No Records Found');
        }

        $result = [];
        $jobCategories = $this->getJobCategoryService()->getJobCategoryList($sortField, $sortOrder, $limit, $offset);
        foreach ($jobCategories as $jobCategory) {
            array_push($result, (new JobCategoryModel($jobCategory))->toArray());
        }
        return new Response($result, [], ['total' => $count]);
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function saveJobCategory()
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $name = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        if (!empty($id)) {
            $jobCategory = $this->getJobCategoryService()->getJobCategoryById($id);
        } else {
            $jobCategory = new JobCategory();
        }

        $jobCategory->setName($name);
        $jobCategory = $this->getJobCategoryService()->saveJobCategory($jobCategory);

        return new Response(
            (new JobCategoryModel($jobCategory))->toArray()
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
        $this->getJobCategoryService()->deleteJobCategory($ids);
        return new Response($ids);
    }
}
