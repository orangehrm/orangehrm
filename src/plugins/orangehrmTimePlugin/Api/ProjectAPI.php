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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\InAccessibleEntityIdOption;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Authorization\UserRole\ProjectAdminUserRole;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Project;
use OrangeHRM\Time\Api\Model\ProjectDetailedModel;
use OrangeHRM\Time\Api\Model\ProjectModel;
use OrangeHRM\Time\Dto\ProjectSearchFilterParams;
use OrangeHRM\Time\Traits\Service\CustomerServiceTrait;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class ProjectAPI extends Endpoint implements CrudEndpoint
{
    use ProjectServiceTrait;
    use UserRoleManagerTrait;
    use CustomerServiceTrait;
    use TimesheetServiceTrait;

    public const PARAMETER_CUSTOMER_ID = 'customerId';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_PROJECT_ADMINS_EMP_NUMBERS = 'projectAdminsEmpNumbers';

    public const PARAMETER_RULE_NAME_MAX_LENGTH = 100;
    public const PARAMETER_RULE_DESCRIPTION_MAX_LENGTH = 256;
    public const PARAMETER_RULE_PROJECT_ADMIN_MAX_COUNT = 5;

    public const FILTER_MODEL = 'model';
    public const FILTER_PROJECT_ID = 'projectId';
    public const FILTER_CUSTOMER_ID = 'customerId';
    public const FILTER_EMPLOYEE_NUMBER = 'empNumber';
    public const FILTER_NAME = 'name';
    public const FILTER_ONLY_ALLOWED = 'onlyAllowed';
    public const FILTER_CUSTOMER_OR_PROJECT_NAME = 'customerOrProjectName';
    public const FILTER_EXCLUDE_PROJECT_IDS = 'excludeProjectIds';

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => ProjectModel::class,
        self::MODEL_DETAILED => ProjectDetailedModel::class,
    ];

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

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $projectParamHolder = new ProjectSearchFilterParams();
        $this->setSortingAndPaginationParams($projectParamHolder);

        $projectId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_PROJECT_ID
        );
        $onlyAllowed = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_ONLY_ALLOWED,
            true
        );
        if (!is_null($projectId)) {
            $projectParamHolder->setProjectIds([$projectId]);
        } elseif ($onlyAllowed) {
            $accessibleProjectIds = $this->getUserRoleManager()->getAccessibleEntityIds(Project::class);
            $projectParamHolder->setProjectIds($accessibleProjectIds);
        }
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
        $projectParamHolder->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $projectParamHolder->setCustomerOrProjectName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_CUSTOMER_OR_PROJECT_NAME
            )
        );
        $projectParamHolder->setExcludeProjectIds(
            $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EXCLUDE_PROJECT_IDS,
            )
        );
        $projects = $this->getProjectService()->getProjectDao()->getProjects($projectParamHolder);
        $count = $this->getProjectService()->getProjectDao()->getProjectsCount($projectParamHolder);
        return new EndpointCollectionResult(
            $this->getModelClass(),
            $projects,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_PROJECT_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Project::class])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_EMPLOYEE_NUMBER,
                    new Rule(Rules::POSITIVE),
                    new Rule(
                        Rules::IN_ACCESSIBLE_ENTITY_ID,
                        [
                            Employee::class,
                            (new InAccessibleEntityIdOption())
                                ->setRolesToExclude(['Supervisor'])
                                ->setRequiredPermissions(
                                    [BasicUserRoleManager::PERMISSION_TYPE_USER_ROLE_SPECIFIC => [ProjectAdminUserRole::INCLUDE_EMPLOYEE => true]]
                                )
                        ]
                    )
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_CUSTOMER_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Customer::class])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NAME_MAX_LENGTH])
                ),
            ),
            new ParamRule(
                self::FILTER_ONLY_ALLOWED,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_CUSTOMER_OR_PROJECT_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NAME_MAX_LENGTH])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_EXCLUDE_PROJECT_IDS,
                    new Rule(Rules::ARRAY_TYPE),
                    new Rule(
                        Rules::EACH,
                        [
                            new Rules\Composite\AllOf(
                                new Rule(Rules::POSITIVE)
                            )
                        ]
                    )
                ),
            ),
            $this->getModelParamRule(),
            ...$this->getSortingAndPaginationParamsRules(ProjectSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return ParamRule
     */
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
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $customerId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CUSTOMER_ID
        );
        $customer = $this->getCustomerService()->getCustomerDao()->getCustomerById($customerId);
        $this->throwRecordNotFoundExceptionIfNotExist($customer, Customer::class, 'Customer no longer exists');
        $project = new Project();
        $this->setProject($project);
        $this->getProjectService()->getProjectDao()->saveProject($project);
        return new EndpointResourceResult(ProjectDetailedModel::class, $project);
    }

    /**
     * @param Project $project
     */
    private function setProject(Project $project): void
    {
        $project->getDecorator()->setCustomerById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CUSTOMER_ID
            )
        );
        $project->setName(
            $this->getRequestParams()->getString(
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
                self::PARAMETER_PROJECT_ADMINS_EMP_NUMBERS
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
                    self::PARAMETER_CUSTOMER_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [Customer::class])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROJECT_ADMINS_EMP_NUMBERS,
                    new Rule(Rules::ARRAY_TYPE),
                    // TODO:: handle unique array items
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_PROJECT_ADMIN_MAX_COUNT])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_DESCRIPTION_MAX_LENGTH])
                ),
            ),
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NAME_MAX_LENGTH])
            ),
        ];
    }

    /**
     * @inheritDoc
     * @throws BadRequestException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        foreach ($ids as $id) {
            $hasTimesheetItems = $this->getProjectService()->getProjectDao()->hasTimesheetItemsForProject($id);
            if ($hasTimesheetItems) {
                throw new BadRequestException('Not Allowed to delete Project(s) Which Have Time Logged Against Them');
            }
        }
        $this->getProjectService()->getProjectDao()->deleteProjects($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Project::class])
            ),
            $this->getModelParamRule()
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $project = $this->getProjectService()->getProjectDao()->getProjectById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($project, Project::class);
        $project->getDecorator()->removeProjectAdmins();
        $this->setProject($project);
        $this->getProjectService()->getProjectDao()->saveProject($project);
        return new EndpointResourceResult(ProjectDetailedModel::class, $project);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }
}
