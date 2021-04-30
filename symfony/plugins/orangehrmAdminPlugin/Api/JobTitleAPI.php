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

use OrangeHRM\Admin\Api\Model\JobTitleModel;
use OrangeHRM\Admin\Service\JobTitleService;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;

class JobTitleAPI extends Endpoint implements CrudEndpoint
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
    public const PARAMETER_CURRENT_JOB_SPECIFICATION = 'currentJobSpecification';

    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';

    public const JOB_SPECIFICATION_KEEP_CURRENT = 'keepCurrent';
    public const JOB_SPECIFICATION_DELETE_CURRENT = 'deleteCurrent';
    public const JOB_SPECIFICATION_REPLACE_CURRENT = 'replaceCurrent';

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

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointGetOneResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $jobTitle = $this->getJobTitleService()->getJobTitleById($id);
        if (!$jobTitle instanceof JobTitle) {
            throw new RecordNotFoundException();
        }

        return new EndpointGetOneResult(JobTitleModel::class, $jobTitle);
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointGetAllResult
    {
        $sortField = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_FIELD,
            'jt.jobTitleName'
        );
        $sortOrder = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_ORDER,
            'ASC'
        );
        $activeOnly = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_ACTIVE_ONLY,
            true
        );
        $limit = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_OFFSET, 0);

        $count = $this->getJobTitleService()->getJobTitleList(
            $sortField,
            $sortOrder,
            $activeOnly,
            $limit,
            $offset,
            true
        );

        $jobTitles = $this->getJobTitleService()->getJobTitleList($sortField, $sortOrder, $activeOnly, $limit, $offset);
        return new EndpointGetAllResult(JobTitleModel::class, $jobTitles, new ParameterBag(['total' => $count]));
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointCreateResult
    {
        // TODO:: Check data group permission
        $params = $this->getPostParameters();
        $jobTitleObj = new JobTitle();
        $jobTitleObj->setIsDeleted(false);
        $jobTitleObj->setJobTitleName($params[self::PARAMETER_TITLE]);
        $jobTitleObj->setJobDescription($params[self::PARAMETER_DESCRIPTION]);
        $jobTitleObj->setNote($params[self::PARAMETER_NOTE]);

        $jobSpecification = $params[self::PARAMETER_SPECIFICATION];
        if ($jobSpecification) {
            // TODO:: validate file type and size
            $jobSpecAttachment = new JobSpecificationAttachment();
            $jobSpecAttachment->setFileName($jobSpecification['name']);
            $jobSpecAttachment->setFileType($jobSpecification['type']);
            $jobSpecAttachment->setFileSize($jobSpecification['size']);
            $fileContent = base64_decode($jobSpecification['base64']);
            $jobSpecAttachment->setFileContent($fileContent);
            $jobTitleObj->setJobSpecificationAttachment($jobSpecAttachment);
            $jobSpecAttachment->setJobTitle($jobTitleObj);
        }

        $jobTitleObj = $this->getJobTitleService()->saveJobTitle($jobTitleObj);

        return new EndpointCreateResult(JobTitleModel::class, $jobTitleObj);
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $params = $this->getPostParameters();
        $jobTitleObj = $this->getJobTitleService()->getJobTitleById($params[self::PARAMETER_ID]);
        $jobTitleObj->setJobTitleName($params[self::PARAMETER_TITLE]);
        $jobTitleObj->setJobDescription($params[self::PARAMETER_DESCRIPTION]);
        $jobTitleObj->setNote($params[self::PARAMETER_NOTE]);

        if ($params[self::PARAMETER_CURRENT_JOB_SPECIFICATION] === self::JOB_SPECIFICATION_DELETE_CURRENT) {
            $jobSpecAttachment = $jobTitleObj->getJobSpecificationAttachment();
            if ($jobSpecAttachment instanceof JobSpecificationAttachment) {
                $this->getJobTitleService()->deleteJobSpecificationAttachment($jobSpecAttachment);
            }
            $jobTitleObj->setJobSpecificationAttachment(null);
        } elseif ($params[self::PARAMETER_CURRENT_JOB_SPECIFICATION] === self::JOB_SPECIFICATION_REPLACE_CURRENT) {
            if ($params[self::PARAMETER_SPECIFICATION]) {
                $jobSpecification = $params[self::PARAMETER_SPECIFICATION];
                $jobSpecAttachment = $jobTitleObj->getJobSpecificationAttachment();
                if (!$jobSpecAttachment instanceof JobSpecificationAttachment) {
                    $jobSpecAttachment = new JobSpecificationAttachment();
                }
                $jobSpecAttachment->setFileName($jobSpecification['name']);
                $jobSpecAttachment->setFileType($jobSpecification['type']);
                $jobSpecAttachment->setFileSize($jobSpecification['size']);
                $fileContent = base64_decode($jobSpecification['base64']);
                $jobSpecAttachment->setFileContent($fileContent);
                $this->getJobTitleService()->saveJobSpecificationAttachment($jobSpecAttachment);
            }
            $jobTitleObj->setJobSpecificationAttachment($jobSpecAttachment);
        }
        $jobTitleObj->setId($jobTitleObj->getId());
        $jobTitleObj = $this->getJobTitleService()->saveJobTitle($jobTitleObj);

        return new EndpointUpdateResult(JobTitleModel::class, $jobTitleObj);
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointDeleteResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_IDS);
        $this->getJobTitleService()->deleteJobTitle($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }

    /**
     * @return array
     */
    public function getPostParameters(): array
    {
        $params = [];
        $params[self::PARAMETER_ID] = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_ID
        );
        $params[self::PARAMETER_TITLE] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TITLE
        );
        $params[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );
        $params[self::PARAMETER_NOTE] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_NOTE
        );
        $params[self::PARAMETER_SPECIFICATION] = $this->getRequestParams()->getArrayOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SPECIFICATION
        );
        $params[self::PARAMETER_CURRENT_JOB_SPECIFICATION] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_JOB_SPECIFICATION
        );
        return $params;
    }
}
