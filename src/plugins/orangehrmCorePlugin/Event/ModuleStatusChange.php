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

namespace OrangeHRM\Core\Event;

use OrangeHRM\Entity\Module;
use OrangeHRM\Framework\Event\Event;

class ModuleStatusChange extends Event
{
    /**
     * @var Module
     */
    private Module $previousModule;

    /**
     * @var Module
     */
    private Module $currentModule;

    /**
     * @param Module $previousModule
     * @param Module $currentModule
     */
    public function __construct(Module $previousModule, Module $currentModule)
    {
        $this->previousModule = $previousModule;
        $this->currentModule = $currentModule;
    }

    /**
     * @return Module
     */
    public function getPreviousModule(): Module
    {
        return $this->previousModule;
    }

    /**
     * @return Module
     */
    public function getCurrentModule(): Module
    {
        return $this->currentModule;
    }
}
