<?php

namespace OrangeHRM\Recruitment\Api;


use DateTime;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Api\Model\VacancyModel;
use OrangeHRM\Recruitment\Service\VacancyService;

class VacancyAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_NUM_OF_POSITIONS = 'numOfPositions';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_IS_PUBLISHED = 'isPublished';
    public const PARAMETER_JOB_TITLE_ID = 'jobTitleId';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';

    public const PARAMETER_RULE_NAME_MAX_LENGTH = 100;
    public const PARAMETER_RULE_JOB_TITLE_CODE_MAX_LENGTH = 4;
    public const PARAMETER_RULE_HIRING_MANGER_ID_MAX_LENGTH = 13;
    public const PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH = 13;
    public const PARAMETER_RULE_STATUS_MAX_LENGTH = 4;
    /**
     * @var null|VacancyService
     */
    protected ?VacancyService $vacancyService = null;

    /**
     * @return EndpointResourceResult
     * @throws DaoException
     * @throws RecordNotFoundException
     * @throws NormalizeException
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $vacancy = $this->getVacancyService()->getVacancyById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($vacancy, Vacancy::class);
        return new EndpointResourceResult(VacancyModel::class, $vacancy);
    }

    /**
     * @return VacancyService
     */
    public function getVacancyService(): VacancyService
    {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
        }
        return $this->vacancyService;
    }

    /**
     * @param  VacancyService  $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService)
    {
        $this->vacancyService = $vacancyService;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(new ParamRule(CommonParams::PARAMETER_ID));
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function getAll(): EndpointCollectionResult
    {
        $count = $this->getVacancyService()->getVacancyDao()->searchVacanciesCount();
        $vacancies = $this->getVacancyService()->getAllVacancies('');
        return new EndpointCollectionResult(
            VacancyModel::class,
            $vacancies,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function create(): EndpointResult
    {
        $vacancy = new Vacancy();
        $this->setVacancy($vacancy);

        $vacancy = $this->getVacancyService()->saveJobVacancy($vacancy);

        return new EndpointResourceResult(VacancyModel::class, $vacancy);
    }

    /**
     * @param  Vacancy  $vacancy
     */
    private function setVacancy(Vacancy $vacancy): void
    {
        $vacancy->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );
        $vacancy->setDescription(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DESCRIPTION
            )
        );
        $vacancy->setNumOfPositions(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NUM_OF_POSITIONS
            )
        );
        $vacancy->getDecorator()->setIsPublished(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_IS_PUBLISHED
            )
        );
        $vacancy->setDefinedTime(
            new DateTime()
        );
        $vacancy->setUpdatedTime(
            new DateTime()
        );
        $vacancy->setStatus(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_STATUS
            )
        );
        $vacancy->getDecorator()->setJobTitleById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_JOB_TITLE_ID
            )
        );
        $vacancy->getDecorator()->setEmployeeById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_EMPLOYEE_ID
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(

            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [!null, self::PARAMETER_RULE_NAME_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATUS,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [!null, self::PARAMETER_RULE_STATUS_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_JOB_TITLE_ID,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [!null, self::PARAMETER_RULE_JOB_TITLE_CODE_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_IS_PUBLISHED,
                    new Rule(Rules::INT_TYPE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),

                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NUM_OF_POSITIONS,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH])

                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMPLOYEE_ID,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_HIRING_MANGER_ID_MAX_LENGTH])

                ),
                true
            ),
        ];
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $vacancy = $this->getVacancyService()->getVacancyById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($vacancy, Vacancy::class);
        $this->setVacancy($vacancy);
        $this->getVacancyService()->saveJobVacancy($vacancy);
        return new EndpointResourceResult(VacancyModel::class, $vacancy);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            ...$this->getCommonBodyValidationRules(),
            ...$this->getUpdateValidationRules()
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getVacancyService()->deleteVacancies($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    private function getUpdateValidationRules()
    {

    }

}
