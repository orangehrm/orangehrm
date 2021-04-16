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

use OrangeHRM\Entity\Skill;
use OrangeHRM\Admin\Service\SkillService;
use OrangeHRM\Admin\Api\Model\SkillModel;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use \DaoException;

class SkillAPI extends EndPoint
{
    const PARAMETER_ID = 'id';
    const PARAMETER_IDS = 'ids';
    const PARAMETER_NAME = 'name';
    const PARAMETER_DESCRIPTION = 'description';

    const PARAMETER_SORT_FIELD = 'sortField';
    const PARAMETER_SORT_ORDER = 'sortOrder';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_LIMIT = 'limit';

    /**
     * @var null|SkillService
     */
    protected $skillService = null;

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
    public function setSkillService(SkillService $skillService)
    {
        $this->skillService = $skillService;
    }

    public function getSkill(): Response
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $skill = $this->getSkillService()->getSkillById($id);

        if (!$skill instanceof Skill) {
            throw new RecordNotFoundException('No Record Found');
        }

        return new Response(
            (new SkillModel($skill))->toArray()
        );
    }

    public function getSkillList(): Response
    {
        // TODO:: Check data group permission
        $sortField = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD, 's.name');
        $sortOrder = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER, 'ASC');
        $limit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT, 50);
        $offset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET, 0);

        $count = $this->getSkillService()->getSkillList(
            $sortField,
            $sortOrder,
            $limit,
            $offset,
            true
        );
        if (!($count > 0)) {
            return new Response([], [], ['total' => 0]);
        }

        $result = [];
        $skillList = $this->getSkillService()->getSkillList(
            $sortField,
            $sortOrder,
            $limit,
            $offset
        );
        foreach ($skillList as $skill) {
            array_push($result, (new SkillModel($skill))->toArray());
        }
        return new Response($result, [], ['total' => $count]);
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function saveSkill(): Response
    {
        // TODO:: Check data group permission
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $name = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        $description = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        if (!empty($id)) {
            $skill = $this->getSkillService()->getSkillById($id);
            if ($skill == null) {
                throw new RecordNotFoundException('No Record Found');
            }
        } else {
            $skill = new Skill();
        }

        $skill->setName($name);
        $skill->setDescription($description);
        $skill = $this->getSkillService()->saveSkill($skill);

        return new Response(
            (new SkillModel($skill))->toArray()
        );
    }

    /**
     * @return Response
     * @throws DaoException
     */
    public function deleteSkills(): Response
    {
        // TODO:: Check data group permission
        $ids = $this->getRequestParams()->getPostParam(self::PARAMETER_IDS);
        $this->getSkillService()->deleteSkills($ids);
        return new Response($ids);
    }
}
