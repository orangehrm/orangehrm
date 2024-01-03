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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\ORM\NestedSet\NestedSetInterface;
use OrangeHRM\ORM\NestedSet\NestedSetTrait;
use OrangeHRM\ORM\NestedSet\Node;
use OrangeHRM\ORM\NestedSet\NodeInterface;

/**
 * Subunit
 *
 * @ORM\Table(name="ohrm_subunit")
 * @ORM\Entity
 */
class Subunit implements NestedSetInterface
{
    use NestedSetTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private string $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unit_id", type="string", length=100, nullable=true)
     */
    private ?string $unitId = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=400, nullable=true)
     */
    private ?string $description = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="lft", type="smallint", nullable=true)
     */
    private ?int $lft;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rgt", type="smallint", nullable=true)
     */
    private ?int $rgt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="level", type="smallint", nullable=true)
     */
    private ?int $level;

    /**
     * @var NodeInterface|null
     */
    private ?NodeInterface $node = null;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getUnitId(): ?string
    {
        return $this->unitId;
    }

    /**
     * @param string|null $unitId
     */
    public function setUnitId(?string $unitId): void
    {
        $this->unitId = $unitId;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public function getLft(): ?int
    {
        return $this->lft;
    }

    /**
     * @inheritDoc
     */
    public function setLft(?int $lft): void
    {
        $this->lft = $lft;
    }

    /**
     * @inheritDoc
     */
    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    /**
     * @inheritDoc
     */
    public function setRgt(?int $rgt): void
    {
        $this->rgt = $rgt;
    }

    /**
     * @inheritDoc
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    /**
     * @inheritDoc
     */
    public function getNode(): NodeInterface
    {
        if (is_null($this->node)) {
            $this->node = new Node($this);
        }
        return $this->node;
    }

    /**
     * @inheritDoc
     */
    public function setNode(?NodeInterface $node): void
    {
        $this->node = $node;
    }
}
