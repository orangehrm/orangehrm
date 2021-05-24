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

namespace OrangeHRM\Entity\Decorator;

trait DecoratorTrait
{
    /**
     * @var object|null
     */
    protected ?object $entityDecorator = null;

    /**
     * @return object
     */
    public function getDecorator(): object
    {
        if (is_null($this->entityDecorator)) {
            $decoratorClassName = $this->getDecoratorClassName();
            $this->entityDecorator = new $decoratorClassName($this);
        }
        return $this->entityDecorator;
    }

    /**
     * @return string
     */
    protected function getDecoratorClassName(): string
    {
        return 'OrangeHRM\\Entity\\Decorator\\' . substr(strrchr(get_class($this), '\\'), 1) . 'Decorator';
    }
}
