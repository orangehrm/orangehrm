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

namespace OrangeHRM\Core\Helper;

class ClassHelper
{
    /**
     * @param string $className
     * @param string|null $fallbackNamespace
     * @return bool
     */
    public function classExists(string $className, ?string $fallbackNamespace = null): bool
    {
        return !is_null($this->getClass($className, $fallbackNamespace));
    }

    /**
     * @param string $className
     * @param string|null $fallbackNamespace e.g. 'OrangeHRM\\Core\\', 'OrangeHRM\\Core\\Service\\'
     * @return string|null
     */
    public function getClass(string $className, ?string $fallbackNamespace = null): ?string
    {
        if (class_exists($className)) {
            return $className;
        }
        if (!is_null($fallbackNamespace)) {
            $className = $fallbackNamespace . $className;
            if (class_exists($className)) {
                return $className;
            }
        }
        return null;
    }

    /**
     * @param string|object $classNameOrInstance
     * @param string ...$interfaces
     * @return bool
     */
    public function hasClassImplements($classNameOrInstance, string ...$interfaces): bool
    {
        $implementedInterfaces = class_implements($classNameOrInstance);
        $implementedInterfacesCount = count($implementedInterfaces);
        $interfacesCount = count($interfaces);
        if ($implementedInterfacesCount < $interfacesCount) {
            return false;
        }

        return $implementedInterfacesCount - $interfacesCount == count(array_diff($implementedInterfaces, $interfaces));
    }
}
