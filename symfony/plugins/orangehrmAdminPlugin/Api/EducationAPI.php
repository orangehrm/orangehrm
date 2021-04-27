<?php


namespace OrangeHRM\Admin\Api;
use DaoException;
 // need to be replace with education
use OrangeHRM\Admin\Api\Model\EducationModel;
use OrangeHRM\Admin\Service\EducationService;
use OrangeHRM\Entity\Education;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class EducationAPI extends EndPoint
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

    /**
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEducation(): Response
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $education = $this->getEducationService()->getEducationById($id);
        if (!$education instanceof Education) {
            throw new RecordNotFoundException('No Record Found');
        }
        return new Response(
            (new EducationModel($education))->toArray()
        );
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     *
     */
    public function getEducations(): Response
    {
        // TODO:: Check data group permission
        $sortField = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD, 'jc.name');
        $sortOrder = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER, 'ASC');
        $limit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET, 0);

        $count = $this->getEducationService()->getEducationList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            true


        );

//        $sortField, removed theses
//            $sortOrder,
//            $limit,
//            $offset,
//            true
        if (!($count > 0)) {
            throw new RecordNotFoundException('No Records Found');
        }

        $result = [];
        $educations = $this->getEducationService()->getEducationList($sortField, $sortOrder, $limit, $offset);
        foreach ($educations as $education) {
            array_push($result, (new EducationModel($education))->toArray());
        }
        return new Response($result, [], ['total' => $count]);
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function saveEducation()
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $name = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        if (!empty($id)) {
            $education = $this->getEducationService()->getEducationById($id);
        } else {
            $education = new Education();
        }

        $education->setName($name);
        $education = $this->getEducationService()->saveEducation($education);

        return new Response(
            (new EducationModel($education))->toArray()
        );
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function deleteEducations()
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getPostParam(self::PARAMETER_IDS);
        $this->getEducationService()->deleteEducations($ids);  //coundn't find a deleteEducation in educationservice
        return new Response($ids);
    }
}