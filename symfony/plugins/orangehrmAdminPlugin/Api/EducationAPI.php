<?php


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
//use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
//use Orangehrm\Rest\Http\Response;

class EducationAPI extends EndPoint implements CrudEndpoint
{
    /**
     * @var null|EducationService
     */
    protected ?EducationService $educationService = null;


    const PARAMETER_ID = 'id';
    const PARAMETER_IDS = 'ids';
    const PARAMETER_NAME = 'name';

    const PARAMETER_SORT_FIELD = 'sortField';
    const PARAMETER_SORT_ORDER = 'sortOrder';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_LIMIT = 'limit';

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
    public function setEducationService(EducationService $educationService)
    {
        $this->educationService = $educationService;
    }

    // new test code

//    /**
//     * @return Response
//     * @throws RecordNotFoundException
//     */
//    public function getEducation(): Response
//    {
//        // TODO:: Check data group permission
//        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
//        $education = $this->getEducationService()->getEducationById($id);
//        if (!$education instanceof Education) {
//            throw new RecordNotFoundException('No Record Found');
//        }
//        return new Response(
//            (new EducationModel($education))->toArray()
//        );
//    }
//
//    /**
//     * @return Response
//     * @throws RecordNotFoundException
//     *
//     */
//    public function getEducations(): Response
//    {
//        // TODO:: Check data group permission
//        $sortField = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD, 'jc.name');
//        $sortOrder = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER, 'ASC');
//        $limit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT, 50);
//        $offset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET, 0);
//
//        $count = $this->getEducationService()->getEducationList(
//            $sortField,
//            $sortOrder,
//            $limit,
//            $offset,
//            true
//
//
//        );
//
//        if (!($count > 0)) {
//            throw new RecordNotFoundException('No Records Found');
//        }
//
//        $result = [];
//        $educations = $this->getEducationService()->getEducationList($sortField, $sortOrder, $limit, $offset);
//        foreach ($educations as $education) {
//            array_push($result, (new EducationModel($education))->toArray());
//        }
//        return new Response($result, [], ['total' => $count]);
//    }
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
    public function saveEducation() : Education
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!empty($id)) {
            $education = $this->getEducationService()->getEducationById($id);
        } else {
            $education = new Education();
        }

        $education->setName($name);
        return $this->getEducationService()->saveEducation($education);
        //$education = $this->getEducationService()->saveEducation($education);

//        return new Response(
//            (new EducationModel($education))->toArray()
//        );
    }

    /**
     *
     * @throws DaoException
     * @throws Exception
     */
    public function delete() : EndpointDeleteResult
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_IDS);
        $this->getEducationService()->deleteEducations($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }
}