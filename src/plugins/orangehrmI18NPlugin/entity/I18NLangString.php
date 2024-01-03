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
 * @ORM\Table(name="ohrm_i18n_lang_string")
 * @ORM\Entity
 */
class I18NLangString
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_id", type="string", length=255)
     */
    private string $unitId;

    /**
     * @var I18NGroup|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\I18NGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true)
     */
    private ?I18NGroup $group = null;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", unique=true)
     */
    private string $value;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private ?string $note = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="version", type="string", length=20, nullable=true)
     */
    private ?string $version = null;

    /**
     * @var I18NTranslation[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\I18NTranslation", mappedBy="langString")
     */
    private iterable $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * @return string
     */
    public function getUnitId(): string
    {
        return $this->unitId;
    }

    /**
     * @param string $unitId
     */
    public function setUnitId(string $unitId): void
    {
        $this->unitId = $unitId;
    }

    /**
     * @return I18NGroup|null
     */
    public function getGroup(): ?I18NGroup
    {
        return $this->group;
    }

    /**
     * @param I18NGroup|null $group
     */
    public function setGroup(?I18NGroup $group): void
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     */
    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }
}
