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

use JobTitleService;
use OrangeHRM\Admin\Api\Model\JobTitleModel;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class JobTitleAPI extends EndPoint
{
    /**
     * @var null|JobTitleService
     */
    protected ?JobTitleService $jobTitleService = null;

    public const PARAMETER_ID = 'id';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_TITLE = 'title';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_NOTE = 'note';
    public const PARAMETER_SPECIFICATION = 'specification';
    public const PARAMETER_ACTIVE_ONLY = 'activeOnly';

    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';

    /**
     * @param JobTitleService $jobTitleService
     */
    public function setJobTitleService(JobTitleService $jobTitleService)
    {
        $this->jobTitleService = $jobTitleService;
    }

    /**
     * @return JobTitleService
     */
    public function getJobTitleService(): JobTitleService
    {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }

    public function getJobTitle()
    {
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $jobTitle = $this->getJobTitleService()->getJobTitleById($id);
        if (!$jobTitle instanceof JobTitle) {
            throw new RecordNotFoundException('No Record Found');
        }
        return new Response(
            (new JobTitleModel($jobTitle))->toArray()
        );
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getJobTitles()
    {
        $sortField = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD, 'jt.jobTitleName');
        $sortOrder = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER, 'ASC');
        $activeOnly = $this->getRequestParams()->getQueryParam(self::PARAMETER_ACTIVE_ONLY, true);
        $limit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET, 0);

        $count = $this->getJobTitleService()->getJobTitleList(
            $sortField,
            $sortOrder,
            $activeOnly,
            $limit,
            $offset,
            true
        );
        if (!($count > 0)) {
            throw new RecordNotFoundException('No Records Found');
        }

        $result = [];
        $jobTitles = $this->getJobTitleService()->getJobTitleList($sortField, $sortOrder, $activeOnly, $limit, $offset);
        foreach ($jobTitles as $jobTitle) {
            array_push($result, (new JobTitleModel($jobTitle))->toArray());
        }
        return new Response($result, [], ['total' => $count]);
    }

    public function saveJobTitle()
    {
        // TODO:: Check data group permission
        $params = $this->getPostParameters();
        if (!empty($params[self::PARAMETER_ID])) {
            $jobTitleObj = $this->getJobTitleService()->getJobTitleById($params[self::PARAMETER_ID]);
        } else {
            $jobTitleObj = new JobTitle();
            $jobTitleObj->setIsDeleted(false);
        }

        $jobTitleObj->setJobTitleName($params[self::PARAMETER_TITLE]);
        $jobTitleObj->setJobDescription($params[self::PARAMETER_DESCRIPTION]);
        $jobTitleObj->setNote($params[self::PARAMETER_NOTE]);

        if ($params[self::PARAMETER_SPECIFICATION]) {
            // TODO:: validate file type and size
            $jobSpecification = $params[self::PARAMETER_SPECIFICATION];
            $jobSpecAttachment = new JobSpecificationAttachment();
            $jobSpecAttachment->setFileName($jobSpecification['name']);
            $jobSpecAttachment->setFileType($jobSpecification['type']);
            $jobSpecAttachment->setFileSize($jobSpecification['size']);

            $fileContent = base64_decode($jobSpecification['base64']);
            $jobSpecAttachment->setFileContent($fileContent);
            $jobSpecAttachment->setJobTitle($jobTitleObj);
            $jobSpecificationAttachment = $this->getJobTitleService()
                ->saveJobSpecificationAttachment($jobSpecAttachment);
            $jobTitleObj = $jobSpecificationAttachment->getJobTitle();
        } else {
            $jobTitleObj = $this->getJobTitleService()->saveJobTitle($jobTitleObj);
        }

        return new Response(
            (new JobTitleModel($jobTitleObj))->toArray()
        );
    }

    public function deleteJobTitles()
    {
        $ids = $this->getRequestParams()->getPostParam(self::PARAMETER_IDS);
        $this->getJobTitleService()->deleteJobTitle($ids);
        return new Response($ids);
    }

    /**
     * @return array
     */
    public function getPostParameters(): array
    {
        $params = [];
        $params[self::PARAMETER_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_ID);
        $params[self::PARAMETER_TITLE] = $this->getRequestParams()->getPostParam(self::PARAMETER_TITLE);
        $params[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        $params[self::PARAMETER_NOTE] = $this->getRequestParams()->getPostParam(self::PARAMETER_NOTE);
        $params[self::PARAMETER_SPECIFICATION] = $this->getRequestParams()->getPostParam(self::PARAMETER_SPECIFICATION);
        return $params;
    }
}
