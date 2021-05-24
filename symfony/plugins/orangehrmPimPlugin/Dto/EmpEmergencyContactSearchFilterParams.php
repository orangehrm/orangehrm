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

class EmpEmergencyContactSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['ec.name', 'ec.relationship','ec.homePhone','ec.officePhone','ec.mobilePhone'];

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var string|null
     */
    protected ?string $relationship = null;

    /**
     * @var string|null
     */
    protected ?string $homePhone = null;

    /**
     * @var string|null
     */
    protected ?string $officePhone = null;

    /**
     * @var string|null
     */
    protected ?string $mobilePhone = null;

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
     * @return string|null
     */
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * @param string|null $relationship
     */
    public function setRelationship(?string $relationship): void
    {
        $this->relationship = $relationship;
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @param string|null $homePhone
     */
    public function setHomePhone(?string $homePhone): void
    {
        $this->homePhone = $homePhone;
    }

    /**
     * @return string|null
     */
    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    /**
     * @param string|null $officePhone
     */
    public function setOfficePhone(?string $officePhone): void
    {
        $this->officePhone = $officePhone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone(?string $mobilePhone): void
    {
        $this->mobilePhone = $mobilePhone;
    }



}
