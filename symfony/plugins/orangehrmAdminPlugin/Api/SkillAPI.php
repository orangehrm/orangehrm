<?php
/*
 *
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
 *
 */

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Entity\Skill;
use OrangeHRM\Admin\Service\SkillService;
use OrangeHRM\Admin\Api\Model\SkillModel;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use OrangeHRM\Core\Exception\DaoException;
use \Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Exception\ServiceException;

class SkillAPI extends Endpoint implements CrudEndpoint
{
    const PARAMETER_NAME = 'name';
    const PARAMETER_DESCRIPTION = 'description';

    const FILTER_NAME = 'name';
    const FILTER_DESCRIPTION = 'description';

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
     * @return EndpointGetOneResult
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getOne(): EndpointGetOneResult
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $skill = $this->getSkillService()->getSkillById($id);
        if (!$skill instanceof Skill) {
            throw new RecordNotFoundException();
        }

        return new EndpointGetOneResult(SkillModel::class, $skill);
    }

    /**
     * @return EndpointGetAllResult
     * @throws SearchParamException
     * @throws ServiceException
     * @throws Exception
     */
    public function getAll(): EndpointGetAllResult
    {
        // TODO:: Check data group permission
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

        return new EndpointGetAllResult(
            SkillModel::class,
            $skills,
            new ParameterBag(
                [
                    'total' => $this->getSkillService()->getSearchSkillsCount(
                        $skillSearchParams
                    )
                ]
            )
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        // TODO:: Check data group permission
        $skill = $this->saveSkill();

        return new EndpointCreateResult(SkillModel::class, $skill);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $skill = $this->saveSkill();

        return new EndpointUpdateResult(SkillModel::class, $skill);
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointDeleteResult
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getSkillService()->deleteSkills($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }

    /**
     * @return Skill
     * @throws RecordNotFoundException|DaoException
     */
    public function saveSkill(): Skill
    {
        // TODO:: Check data group permission
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
