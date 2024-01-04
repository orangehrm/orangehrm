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

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_composite_display_field")
 * @ORM\Entity
 */
class CompositeDisplayField extends AbstractDisplayField
{
    /**
     * @var int
     *
     * @ORM\Column(name="composite_display_field_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var SelectedCompositeDisplayField[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\SelectedCompositeDisplayField", mappedBy="compositeDisplayField")
     */
    private iterable $selectedCompositeDisplayFields;

    public function __construct()
    {
        $this->selectedCompositeDisplayFields = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
