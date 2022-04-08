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

namespace OrangeHRM\I18N\Dto;

class TranslationCollection
{
    /**
     * @var array<string, array> e.g. array('general.employee' => ['source' => 'Employee', 'target' => 'Employé'])
     */
    private array $keyAndSourceTarget;

    /**
     * @var array<string, string> e.g. array('general.employee' => 'Employé')
     */
    private array $keyAndTarget;

    /**
     * @var array<string, string> e.g. array('Employee' => 'Employé')
     */
    private array $sourceAndTarget;

    /**
     * @param array<string, array> $keyAndSourceTarget
     * @param array<string, string> $keyAndTarget
     * @param array<string, string> $sourceAndTarget
     */
    public function __construct(array $keyAndSourceTarget, array $keyAndTarget, array $sourceAndTarget)
    {
        $this->keyAndSourceTarget = $keyAndSourceTarget;
        $this->keyAndTarget = $keyAndTarget;
        $this->sourceAndTarget = $sourceAndTarget;
    }

    /**
     * @return array<string, array>
     */
    public function getKeyAndSourceTarget(): array
    {
        return $this->keyAndSourceTarget;
    }

    /**
     * @return array<string, string>
     */
    public function getKeyAndTarget(): array
    {
        return $this->keyAndTarget;
    }

    /**
     * @return array<string, string>
     */
    public function getSourceAndTarget(): array
    {
        return $this->sourceAndTarget;
    }
}
