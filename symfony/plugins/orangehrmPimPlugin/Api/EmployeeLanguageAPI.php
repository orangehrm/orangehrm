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
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $entriesToDelete = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_IDS
        );
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
