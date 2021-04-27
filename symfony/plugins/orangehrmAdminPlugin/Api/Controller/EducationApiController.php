<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */


namespace OrangeHRM\Admin\Api\Controller;
use OrangeHRM\Admin\Api\EducationAPI;
use OrangeHRM\Core\Controller\AbstractRestController;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/** many things to be changed in the AbstractRestController */



class EducationApiController extends AbstractRestController
{
    /**
     * @var null|EducationAPI
     */
    private ?EducationAPI $educationAPI = null;

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->educationAPI = new EducationAPI($request);
    }

    /**
     * @inheritDoc
     */
    protected function handleGetRequest(Request $request):Response
    {
        return $this->educationAPI->getEducation();
    }

    /**
     * @inheritDoc
     */
    protected function handlePostRequest(Request $request):Response
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    protected function handlePutRequest(Request $request):Response
    {
        return $this->educationAPI->saveEducation();
    }
}