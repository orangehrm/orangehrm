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

/**
 * @ORM\Table(name="hs_hr_custom_fields")
 * @ORM\Entity
 */
class CustomField
{
    public const FIELD_TYPE_STRING = 0;
    public const FIELD_TYPE_SELECT = 1;
    public const MAX_FIELD_NUM = 10;

    public const SCREEN_PERSONAL_DETAILS   = 'personal';
    public const SCREEN_CONTACT_DETAILS    = 'contact';
    public const SCREEN_EMERGENCY_CONTACTS = 'emergency';
    public const SCREEN_DEPENDENTS         = 'dependents';
    public const SCREEN_IMMIGRATION        = 'immigration';
    public const SCREEN_QUALIFICATIONS     = 'qualifications';
    public const SCREEN_TAX_EXEMPTIONS     = 'tax';
    public const SCREEN_SALARY             = 'salary';
    public const SCREEN_JOB                = 'job';
    public const SCREEN_REPORT_TO          = 'report-to';
    public const SCREEN_MEMBERSHIP         = 'membership';

    public const SCREENS = [
        self::SCREEN_PERSONAL_DETAILS,
        self::SCREEN_CONTACT_DETAILS,
        self::SCREEN_EMERGENCY_CONTACTS,
        self::SCREEN_DEPENDENTS,
        self::SCREEN_IMMIGRATION,
        self::SCREEN_QUALIFICATIONS,
        self::SCREEN_TAX_EXEMPTIONS,
        self::SCREEN_SALARY,
        self::SCREEN_JOB,
        self::SCREEN_REPORT_TO,
        self::SCREEN_MEMBERSHIP,
    ];

    public const FIELD_TYPES = [
        self::FIELD_TYPE_STRING,
        self::FIELD_TYPE_SELECT,
    ];

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
