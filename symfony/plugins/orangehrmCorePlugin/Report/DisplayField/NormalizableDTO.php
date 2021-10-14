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

namespace OrangeHRM\Core\Report\DisplayField;

abstract class NormalizableDTO
{
    /**
     * @param array $fields
     * @return array|null
     */
    abstract public function toArray(array $fields): ?array;

    /**
     * @return array
     */
    abstract protected function getFieldGetterMap(): array;

    /**
     * @param iterable $data
     * @param array $fields
     * @return array
     */
    protected function normalizeArray(iterable $data, array $fields): array
    {
        $normalized = [];
        $fieldGetterMap = $this->getFieldGetterMap();
        foreach ($fields as $field) {
            $normalized[$field] = [];
        }
        foreach ($data as $item) {
            foreach ($fields as $field) {
                $getter = $fieldGetterMap[$field];
                $normalized[$field][] = $this->callGetterOnItem($item, $field, $getter);
            }
        }
        return $normalized;
    }

    /**
     * @param $item
     * @param string $field
     * @param array $getter
     * @return string|null
     */
    protected function callGetterOnItem($item, string $field, array $getter): ?string
    {
        if (count($getter) > 1) {
            $result = $item;
            foreach ($getter as $method) {
                if (empty($result)) {
                    break;
                }
                $result = call_user_func([$result, $method]);
            }
            return $result;
        }
        return call_user_func([$item, ...$getter]);
    }
}
