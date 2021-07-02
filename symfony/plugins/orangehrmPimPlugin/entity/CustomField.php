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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @ORM\Table(name="hs_hr_custom_fields")
 * @ORM\Entity
 */
class CustomField
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="field_num", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $fieldNum = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=false)
     */
    private string $name;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="string", length=11, nullable=false)
     */
    private int $type;


    /**
     * @var string | null
     *
     * @ORM\Column(name="screen", type="string", length=100, nullable=true)
     */
    private ?string $screen;

    /**
     * @var string | null
     *
     * @ORM\Column(name="extra_data", type="string", length=250, nullable=true)
     */
    private ?string $extraData = null;


    /**
     * @return int
     */
    public function getFieldNum(): int
    {
        return $this->fieldNum;
    }

    /**
     * @param int $fieldNum
     */
    public function setFieldNum(int $fieldNum): void
    {
        $this->fieldNum = $fieldNum;
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
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
     * @return string|null
     */
    public function getExtraData(): ?string
    {
        return $this->extraData;
    }

    /**
     * @param string|null $extraData
     */
    public function setExtraData(?string $extraData): void
    {
        $this->extraData = $extraData;
    }
}
