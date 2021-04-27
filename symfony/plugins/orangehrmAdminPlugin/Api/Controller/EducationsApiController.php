<?php


namespace OrangeHRM\Admin\Api\Controller;


use OrangeHRM\Admin\Api\EducationAPI;
use OrangeHRM\Core\Controller\AbstractRestController;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class EducationsApiController extends AbstractRestController
{
    /**
     * @var null|EducationAPI
     */
    private ?EducationAPI $educationAPI = null;

    /**
     * @inheritDoc
     */
    protected function init(Request $request)
    {
        $this->educationAPI = new EducationAPI($request);
    }

    /**
     * @inheritDoc
     */
    protected function handleGetRequest(Request $request): Response
    {
        return $this->educationAPI->getEducations();
    }

    /**
     * @inheritDoc
     */
    protected function handlePostRequest(Request $request): Response
    {
        return $this->educationAPI->saveEducation();
    }

    /**
     * @inheritDoc
     */
    protected function handleDeleteRequest(Request $request): Response
    {
        return $this->educationAPI->deleteEducations();
    }
}