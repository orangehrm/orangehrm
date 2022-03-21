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

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_reviewer_group")
 * @ORM\Entity
 */
class ReviewerGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=7)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=50,nullable=true)
     */
    private ?string $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="piority", type="integer", length=7,nullable=true)
     */
    private ?int $piority;

    /**
     * @var Reviewer[]
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Reviewer", mappedBy="ReviewerGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="reviewer_group_id")
     * })
     */
    private iterable $ratings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getPiority(): ?int
    {
        return $this->piority;
    }

    /**
     * @param int|null $piority
     */
    public function setPiority(?int $piority): void
    {
        $this->piority = $piority;
    }
}
