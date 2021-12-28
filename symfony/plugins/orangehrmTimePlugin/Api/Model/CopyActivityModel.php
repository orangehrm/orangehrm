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

namespace OrangeHRM\Time\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Time\Dto\CopyActivityField;
use OrangeHRM\Time\Dto\ProjectActivitySearchFilterParams;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;

class CopyActivityModel implements Normalizable
{
    use ProjectServiceTrait;

    /**
     * @var CopyActivityField
     */
    private CopyActivityField $copyActivityField;

    /**
     * @return CopyActivityField
     */
    public function getCopyActivityField(): CopyActivityField
    {
        return $this->copyActivityField;
    }

    public function __construct(CopyActivityField $copyActivity)
    {
        $this->copyActivityField = $copyActivity;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $copyActivityField = $this->getCopyActivityField();
        $projectActivitySearchFilterParams = new ProjectActivitySearchFilterParams();

        $projectActivitiesForFromProject = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityListByProjectId(
                $copyActivityField->getFromProjectId(),
                $projectActivitySearchFilterParams
            ); //target

        $projectActivitiesForToProject = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityListByProjectId(
                $copyActivityField->getToProjectId(),
                $projectActivitySearchFilterParams
            );

        $toProjectActivities = [];
        foreach ($projectActivitiesForFromProject as $value) {
            $toProjectActivities[$value->getName()] = [
                "id" => $value->getId(),
                "name" => $value->getName(),
            ];
        }

        $fromProjectActivities = [];
        foreach ($projectActivitiesForToProject as $value) {
            $fromProjectActivities[$value->getName()] = [
                "id" => $value->getId(),
                "name" => $value->getName(),
            ];
        }

        $result = [];
        foreach ($toProjectActivities as $toProjectActivity) {
            $name = $toProjectActivity['name'];
            $result[] = [
                'id' => $toProjectActivity['id'],
                'name' => $name,
                'unique' => !isset($fromProjectActivities[$name])
            ];
        }
        return $result;
    }
}
