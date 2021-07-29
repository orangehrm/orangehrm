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

use Exception;
use OrangeHRM\Admin\Api\Model\SkillModel;
use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
use OrangeHRM\Admin\Service\SkillService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\Skill;

class SkillAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';

    public const FILTER_NAME = 'name';
    public const FILTER_DESCRIPTION = 'description';

    /**
     * @var null|SkillService
     */
    protected ?SkillService $skillService = null;

    /**
     * @return SkillService
     */
    public function getSkillService(): SkillService
    {
        if (is_null($this->skillService)) {
            $this->skillService = new SkillService();
        }
        return $this->skillService;
    }

    /**
     * @param SkillService $skillService
     */
    public function setSkillService(SkillService $skillService): void
    {
        $this->skillService = $skillService;
    }

    /**
     * @return EndpointResourceResult
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $skill = $this->getSkillService()->getSkillById($id);
        if (!$skill instanceof Skill) {
            throw new RecordNotFoundException();
        }

        return new EndpointResourceResult(SkillModel::class, $skill);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
        );
    }

    /**
     * @return EndpointCollectionResult
     * @throws SearchParamException
     * @throws ServiceException
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $skillSearchParams = new SkillSearchFilterParams();
        $this->setSortingAndPaginationParams($skillSearchParams);
        $skillSearchParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $skillSearchParams->setDescription(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_DESCRIPTION
            )
        );

        $skills = $this->getSkillService()->searchSkill($skillSearchParams);

        return new EndpointCollectionResult(
            SkillModel::class,
            $skills,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getSkillService()->getSearchSkillsCount(
                        $skillSearchParams
                    )
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
            new ParamRule(self::FILTER_NAME),
            new ParamRule(self::FILTER_DESCRIPTION),
            ...$this->getSortingAndPaginationParamsRules(SkillSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        $skill = $this->saveSkill();

        return new EndpointResourceResult(SkillModel::class, $skill);
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
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(self::PARAMETER_NAME),
            new ParamRule(self::PARAMETER_DESCRIPTION),
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $skill = $this->saveSkill();

        return new EndpointResourceResult(SkillModel::class, $skill);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getSkillService()->deleteSkills($ids);
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

    /**
     * @return Skill
     * @throws RecordNotFoundException|DaoException
     */
    public function saveSkill(): Skill
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $description = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );
        if (!empty($id)) {
            $skill = $this->getSkillService()->getSkillById($id);
            if ($skill == null) {
                throw new RecordNotFoundException();
            }
        } else {
            $skill = new Skill();
        }

        $skill->setName($name);
        $skill->setDescription($description);
        return $this->getSkillService()->saveSkill($skill);
    }
}
