<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Pim\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Pim\Api\Model\EmployeeLanguageModel;
use OrangeHRM\Pim\Dto\EmployeeLanguagesSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeLanguageService;

class EmployeeLanguageAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_LANGUAGE_ID = 'languageId';
    public const PARAMETER_FLUENCY_ID = 'fluencyId';
    public const PARAMETER_COMPETENCY_ID = 'competencyId';
    public const PARAMETER_COMMENT = 'comment';

    public const PARAM_RULE_COMMENT_MAX_LENGTH = 100;

    /**
     * @var null|EmployeeLanguageService
     */
    protected ?EmployeeLanguageService $employeeLanguageService = null;

    /**
     * @return EmployeeLanguageService
     */
    public function getEmployeeLanguageService(): EmployeeLanguageService
    {
        if (!$this->employeeLanguageService instanceof EmployeeLanguageService) {
            $this->employeeLanguageService = new EmployeeLanguageService();
        }
        return $this->employeeLanguageService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/languages/{languageId}/fluencies/{fluencyId}",
     *     tags={"PIM/Employee Language"},
     *     summary="Get an Employee's Language and Fluency",
     *     operationId="get-an-employees-language-and-fluency",
     *     description="This endpoint allows you to get a particular employee's fluency for a particular language.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="languageId",
     *         description="Specify the numerical ID of the desired language",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="fluencyId",
     *         description="Specify the numerical ID of the desired fluency",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeLanguageModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        list($empNumber, $languageId, $fluencyId) = $this->getUrlAttributes();

        $employeeLanguage = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getEmployeeLanguage($empNumber, $languageId, $fluencyId);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeLanguage, EmployeeLanguage::class);

        return new EndpointResourceResult(
            EmployeeLanguageModel::class,
            $employeeLanguage,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @return array
     */
    private function getUrlAttributes(): array
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $languageId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LANGUAGE_ID
        );
        $fluencyId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_FLUENCY_ID
        );
        return [$empNumber, $languageId, $fluencyId];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getLanguageIdRule(),
            $this->getFluencyIdRule(),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/languages",
     *     tags={"PIM/Employee Language"},
     *     summary="List an Employee's Languages",
     *     operationId="list-an-employees-languages",
     *     description="This endpoint allows you to list the languages and fluency for a particular employee.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the languages by name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeLanguagesSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pim-EmployeeLanguageModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", description="The total number of language records", type="integer"),
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        list($empNumber) = $this->getUrlAttributes();

        $employeeLanguagesSearchFilterParams = new EmployeeLanguagesSearchFilterParams();
        $employeeLanguagesSearchFilterParams->setEmpNumber($empNumber);
        $employeeLanguages = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getEmployeeLanguages($employeeLanguagesSearchFilterParams);
        $employeeLanguagesCount = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getEmployeeLanguagesCount($employeeLanguagesSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeLanguageModel::class,
            $employeeLanguages,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $employeeLanguagesCount
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeLanguagesSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/employees/{empNumber}/languages",
     *     tags={"PIM/Employee Language"},
     *     summary="Add a Language to an Employee",
     *     operationId="add-a-language-to-an-employee",
     *     description="This endpoint allows you to add a language and fluency to an employee.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="languageId", description="Specify the numerical ID of the language", type="integer"),
     *             @OA\Property(property="fluencyId", description="Specify the numerical ID of the fluency", type="integer", enum=OrangeHRM\Entity\EmployeeLanguage::FLUENCIES),
     *             @OA\Property(property="competencyId", description="Specify the numerical ID of the compenetency", type="integer", enum=OrangeHRM\Entity\EmployeeLanguage::COMPETENCIES),
     *             @OA\Property(property="comment", description="Specify the comment", type="string"),
     *             required={"languageId", "fluencyId", "competencyId", "comment"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeLanguageModel"
     *             ),
     *             @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Repeated languageId & empNumber combination",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="messsage", type="string", default="Given `fluencyId` already there for given `languageId` & `empNumber` combination")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        list($empNumber) = $this->getUrlAttributes();

        $languageId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LANGUAGE_ID
        );
        $fluencyId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_FLUENCY_ID
        );

        $employeeLanguage = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getEmployeeLanguage($empNumber, $languageId, $fluencyId);
        if ($employeeLanguage instanceof EmployeeLanguage) {
            throw $this->getBadRequestException(
                'Given `fluencyId` already there for given `languageId` & `empNumber` combination'
            );
        }

        $employeeLanguage = new EmployeeLanguage();
        $employeeLanguage->getDecorator()->setEmployeeByEmpNumber($empNumber);
        $employeeLanguage->getDecorator()->setLanguageById($languageId);
        $employeeLanguage->setFluency($fluencyId);
        $this->setEmployeeLanguage($employeeLanguage);

        $this->getEmployeeLanguageService()->getEmployeeLanguageDao()->saveEmployeeLanguage($employeeLanguage);
        return new EndpointResourceResult(
            EmployeeLanguageModel::class,
            $employeeLanguage,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @param EmployeeLanguage $employeeLanguage
     */
    private function setEmployeeLanguage(EmployeeLanguage $employeeLanguage): void
    {
        $employeeLanguage->setCompetency(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COMPETENCY_ID
            )
        );
        $employeeLanguage->setComment(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COMMENT
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getLanguageIdRule(),
            $this->getFluencyIdRule(),
            $this->getCompetencyIdRule(),
            $this->getCommentRule(),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/languages/{languageId}/fluencies/{fluencyId}",
     *     tags={"PIM/Employee Language"},
     *     summary="Update an Employee's Language and Fluency",
     *     operationId="update-an-employees-langauge-and-fluency",
     *     description="This endpoint allows you to update an employee's language and fluency.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="languageId",
     *         description="Specify the numerical ID of the desired language",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="fluencyId",
     *         description="Specify the numerical ID of the desired fluency",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="languageId", description="Specify the numerical ID of the language", type="integer"),
     *             @OA\Property(property="fluencyId", description="Specify the numerical ID of the fluency", type="integer", enum=OrangeHRM\Entity\EmployeeLanguage::FLUENCIES),
     *             @OA\Property(property="competencyId", description="Specify the numerical ID of the competency", type="integer", enum=OrangeHRM\Entity\EmployeeLanguage::COMPETENCIES),
     *             @OA\Property(property="comment", description="Specify the comment regarding the language and fluency", type="string"),
     *             required={"competencyId"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeLanguageModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResult
    {
        list($empNumber, $languageId, $fluencyId) = $this->getUrlAttributes();

        $employeeLanguage = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getEmployeeLanguage($empNumber, $languageId, $fluencyId);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeLanguage, EmployeeLanguage::class);

        $this->setEmployeeLanguage($employeeLanguage);
        $this->getEmployeeLanguageService()->getEmployeeLanguageDao()->saveEmployeeLanguage($employeeLanguage);
        return new EndpointResourceResult(
            EmployeeLanguageModel::class,
            $employeeLanguage,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getLanguageIdRule(),
            $this->getFluencyIdRule(),
            $this->getCompetencyIdRule(),
            $this->getCommentRule(),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/employees/{empNumber}/languages",
     *     tags={"PIM/Employee Language"},
     *     summary="Delete an Employee's Languages",
     *     operationId="delete-an-employees-languages",
     *     description="This endpoint allows you to delete an employee's language and fluency.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="languageId", type="integer", description="The numerical ID of the language to be deleted"),
     *                     @OA\Property(property="fluencyId", type="integer",  description="The numerical ID of the fluency to be deleted")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $entriesToDelete = $this->getEmployeeLanguageService()->getEmployeeLanguageDao()->getExistingEmployeeLanguageRecordsForEmpNumber(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $empNumber
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($entriesToDelete);
        $this->getEmployeeLanguageService()->getEmployeeLanguageDao()->deleteEmployeeLanguages(
            $empNumber,
            $entriesToDelete
        );
        return new EndpointResourceResult(
            ArrayModel::class,
            $entriesToDelete,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(Rules::KEY, [self::PARAMETER_LANGUAGE_ID]),
                            new Rule(Rules::KEY, [self::PARAMETER_FLUENCY_ID])
                        )
                    ]
                )
            ),
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            CommonParams::PARAMETER_EMP_NUMBER,
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
        );
    }

    /**
     * @return ParamRule
     */
    private function getLanguageIdRule(): ParamRule
    {
        return new ParamRule(self::PARAMETER_LANGUAGE_ID, new Rule(Rules::POSITIVE));
    }

    /**
     * @return ParamRule
     */
    private function getFluencyIdRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_FLUENCY_ID,
            new Rule(Rules::IN, [array_keys(EmployeeLanguage::FLUENCIES)])
        );
    }

    /**
     * @return ParamRule
     */
    private function getCompetencyIdRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_COMPETENCY_ID,
            new Rule(Rules::IN, [array_keys(EmployeeLanguage::COMPETENCIES)])
        );
    }

    /**
     * @return ParamRule
     */
    private function getCommentRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_COMMENT,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENT_MAX_LENGTH]),
        );
    }
}
