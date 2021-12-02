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

namespace OrangeHRM\Time\Api;

use Doctrine\ORM\NonUniqueResultException;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Time\Api\Model\ProjectDetailedModel;
use OrangeHRM\Time\Api\Model\ProjectModel;
use OrangeHRM\Time\Dto\ProjectSearchFilterParams;
use OrangeHRM\Time\Service\ProjectService;
use OrangeHRM\Core\Api\V2\ParameterBag;

class ProjectAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_CUSTOMER_ID = 'customerId';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_IS_DELETED = 'isDeleted';
    public const PARAMETER_PROJECT_ADMINS = 'projectAdmins';

    public const PARAMETER_RULE_NAME_MAX_LENGTH = 100;
    public const PARAMETER_RULE_DESCRIPTION_MAX_LENGTH = 256;
    public const PARAMETER_RULE_CUSTOMER_ID_MAX_LENGTH = 11;
    public const PARAMETER_RULE_PROJECT_ADMIN_MAX_COUNT = 5;

    public const FILTER_MODEL = 'model';
    public const FILTER_PROJECT_ID = 'projectId';
    public const FILTER_CUSTOMER_ID = 'customerId';
    public const FILTER_EMPLOYEE_NUMBER = 'empNumber';

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => ProjectModel::class,
        self::MODEL_DETAILED => ProjectDetailedModel::class,
    ];

    /**
     * @var ProjectService|null
     */
    protected ?ProjectService $projectService = null;

    /**
     * @var int|null
     */
    protected ?int $projectId = null;

    public function getAll(): EndpointCollectionResult
    {
        $projectParamHolder = new ProjectSearchFilterParams();
        $this->setSortingAndPaginationParams($projectParamHolder);

        $projectParamHolder->setProjectId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_PROJECT_ID
            )
        );
        $projectParamHolder->setCustomerId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_CUSTOMER_ID
            )
        );
        $projectParamHolder->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EMPLOYEE_NUMBER
            )
        );
        $projects = $this->getProjectService()->getProjectDao()->getAllProjects($projectParamHolder);
        $count = $this->getProjectService()->getProjectDao()->searchProjectsCount($projectParamHolder);
        return new EndpointCollectionResult(
            $this->getModelClass(),
            $projects,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @return ProjectService
     */
    private function getProjectService(): ProjectService
    {
        if (is_null($this->projectService)) {
            return new ProjectService();
        }
        return $this->projectService;
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT
        );
        return self::MODEL_MAP[$model];
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_PROJECT_ID),
            new ParamRule(self::FILTER_EMPLOYEE_NUMBER),
            new ParamRule(self::FILTER_CUSTOMER_ID),
            $this->getModelParamRule(),
            ...$this->getSortingAndPaginationParamsRules(ProjectSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    protected function getModelParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            )
        );
    }

    /**
     * @throws NormalizeException
     */
    public function create(): EndpointResourceResult
    {
        $project = new Project();
        $this->setProject($project);
        $this->getProjectService()->getProjectDao()->saveProject($project);
        return new EndpointResourceResult(ProjectDetailedModel::class, $project);
    }

    /**
     * @param  Project  $project
     */
    private function setProject(Project $project)
    {
        $project->getDecorator()->setCustomerById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CUSTOMER_ID
            )
        );
        $project->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );
        $project->setDescription(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DESCRIPTION
            )
        );
        $project->getDecorator()->setProjectAdminsByEmpNumbers(
            $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PROJECT_ADMINS
            )
        );
    }

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
                    self::PARAMETER_CUSTOMER_ID,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [!null, self::PARAMETER_RULE_CUSTOMER_ID_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROJECT_ADMINS,
                    new Rule(Rules::ARRAY_TYPE),
                    new Rule(Rules::LENGTH, [!null, self::PARAMETER_RULE_PROJECT_ADMIN_MAX_COUNT])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_DESCRIPTION_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NAME_MAX_LENGTH])

                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_IS_DELETED,
                    new Rule(Rules::BOOL_TYPE)
                ),
            )
        ];
    }

    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getProjectService()->getProjectDao()->deleteProjects($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $project = $this->getProjectService()->getProjectDao()->getProjectById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($project, Project::class);
        return new EndpointResourceResult($this->getModelClass(), $project);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            $this->getModelParamRule()
        );
    }

    /**
     * @throws NonUniqueResultException|NormalizeException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $project = $this->getProjectService()->getProjectDao()->getProjectById($id);
        foreach ($project->getProjectAdmins() as $projectAdmin){
            $project->removeProjectAdmin($projectAdmin);
        }
        $this->setProject($project);
        $this->getProjectService()->getProjectDao()->updateProject($project);
        return new EndpointResourceResult(ProjectDetailedModel::class, $project);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(CommonParams::PARAMETER_ID)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }


}
