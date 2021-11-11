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

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\DisplayFieldGroup;
use OrangeHRM\Entity\SelectedDisplayFieldGroup;

class SelectedDisplayFieldGroupDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var SelectedDisplayFieldGroup
     */
    private SelectedDisplayFieldGroup $selectedDisplayFieldGroup;

    /**
     * @param SelectedDisplayFieldGroup $selectedDisplayFieldGroup
     */
    public function __construct(SelectedDisplayFieldGroup $selectedDisplayFieldGroup)
    {
        $this->selectedDisplayFieldGroup = $selectedDisplayFieldGroup;
    }

    /**
     * @return SelectedDisplayFieldGroup
     */
    protected function getSelectedDisplayFieldGroup(): SelectedDisplayFieldGroup
    {
        return $this->selectedDisplayFieldGroup;
    }

    /**
     * @param int $id
     */
    public function setDisplayFieldGroupById(int $id): void
    {
        $displayFieldGroup = $this->getReference(DisplayFieldGroup::class, $id);
        $this->getSelectedDisplayFieldGroup()->setDisplayFieldGroup($displayFieldGroup);
    }
}
