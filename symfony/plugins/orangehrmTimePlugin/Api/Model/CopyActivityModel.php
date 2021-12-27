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

    public function __construct(CopyActivityField $copyActivityField)
    {
        $this->copyActivityField = $copyActivityField;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $copyActivityField = $this->getCopyActivityField();
        $projectActivitySearchFilterParams = new ProjectActivitySearchFilterParams();

        $projectActivitiesForTargetProject = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityListByProjectId(
                $copyActivityField->getToProjectId(),
                $projectActivitySearchFilterParams
            ); //Project that we are going to pull from / Base

        $projectActivitiesForBaseProject = $this->getProjectService()
            ->getProjectActivityDao()
            ->getProjectActivityListByProjectId(
                $copyActivityField->getFromProjectId(),
                $projectActivitySearchFilterParams
            );

        $targetActivity = [];
        foreach ($projectActivitiesForTargetProject as $projectActivityForTargetProject => $value) {
            $targetActivity[$projectActivityForTargetProject] = [
                "id" => $value->getId(),
                "name" => $value->getName(),
            ];
        }

        $baseActivity = [];
        foreach ($projectActivitiesForBaseProject as $projectActivityForBaseProject => $value) {
            $baseActivity[$projectActivityForBaseProject] = [
                "id" => $value->getId(),
                "name" => $value->getName(),
            ];
        }

        $result = [];
        for ($i = 0; $i < count($targetActivity); $i++) {
            $contains = false;
            for ($j = 0; $j < count($baseActivity); $j++) {
                if ($targetActivity[$i]['name'] == $baseActivity[$j]['name']) {
                    $contains = true;
                    $result[$i] = [
                        'id' => $targetActivity[$i]['id'],
                        'name' => $targetActivity[$i]['name'],
                        'unique' => false
                    ];
                    break;
                }
            }
            if (!$contains) {
                $result[$i] = [
                    'id' => $targetActivity[$i]['id'],
                    'name' => $targetActivity[$i]['name'],
                    'unique' => true
                ];
            }
        }
        return $result;
    }
}
