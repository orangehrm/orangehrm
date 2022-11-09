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

namespace OrangeHRM\Pim\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class CustomFieldSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['cf.name', 'cf.screen', 'cf.type'];

    /**
     * @var string|null
     */
    private ?string $screen = null;

    /**
     * @var int[]|null
     */
    private ?array $fieldNumbers = null;

    public function __construct()
    {
        $this->setSortField('cf.name');
    }

    /**
     * @return string|null
     */
    public function getScreen(): ?string
    {
        return $this->screen;
    }

    /**
     * @param string|null $screen
     */
    public function setScreen(?string $screen): void
    {
        $this->screen = $screen;
    }

    /**
     * @return int[]|null
     */
    public function getFieldNumbers(): ?array
    {
        return $this->fieldNumbers;
    }

    /**
     * @param int[]|null $fieldNumbers
     */
    public function setFieldNumbers(?array $fieldNumbers): void
    {
        $this->fieldNumbers = $fieldNumbers;
    }
}
