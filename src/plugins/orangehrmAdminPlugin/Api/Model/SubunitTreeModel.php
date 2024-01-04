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

namespace OrangeHRM\Admin\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Entity\Subunit;

/**
 * @OA\Schema(
 *     schema="Admin-SubunitTreeModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="unitId", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="level", type="integer"),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="unitId", type="string"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="level", type="integer"),
 *         )
 *     )
 * )
 */
class SubunitTreeModel implements CollectionNormalizable
{
    /**
     * @var Subunit[]
     */
    private array $subunits;

    public function __construct(array $subunits)
    {
        $this->subunits = $subunits;
    }

    public function toArray(): array
    {
        return $this->toTree($this->subunits);
    }

    /**
     * @param Subunit[] $collection
     * @return array
     */
    private function toTree(array $collection): array
    {
        $tree = [];
        $l = 0;

        if (count($collection) > 0) {
            $stack = [];

            foreach ($collection as $node) {
                $item = [
                    'id' => $node->getId(),
                    'unitId' => $node->getUnitId(),
                    'name' => $node->getName(),
                    'level' => $node->getLevel(),
                ];
                $item['children'] = [];

                $l = count($stack);
                while ($l > 0 && $stack[$l - 1]['level'] >= $item['level']) {
                    array_pop($stack);
                    $l--;
                }

                if ($l == 0) {
                    $i = count($tree);
                    $tree[$i] = $item;
                    $stack[] = &$tree[$i];
                } else {
                    $i = count($stack[$l - 1]['children']);
                    $stack[$l - 1]['children'][$i] = $item;
                    $stack[] = &$stack[$l - 1]['children'][$i];
                }
            }
        }

        return $tree;
    }
}
