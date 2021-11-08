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
 * Organization
 *
 * @ORM\Table(name="ohrm_organization_gen_info")
 * @ORM\Entity
 */
class Organization
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
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
     * @ORM\Column(name="tax_id", type="string", length=30, nullable=true)
     */
    private ?string $taxId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="registration_number", type="string", length=30, nullable=true)
     */
    private ?string $registrationNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=30, nullable=true)
     */
    private ?string $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax", type="string", length=30, nullable=true)
     */
    private ?string $fax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=30, nullable=true)
     */
    private ?string $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="country", type="string", length=30, nullable=true)
     */
    private ?string $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="province", type="string", length=30, nullable=true)
     */
    private ?string $province;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=30, nullable=true)
     */
    private ?string $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip_code", type="string", length=30, nullable=true)
     */
    private ?string $zipCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street1", type="string", length=100, nullable=true)
     */
    private ?string $street1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street2", type="string", length=100, nullable=true)
     */
    private ?string $street2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    private ?string $note;

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
    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    /**
     * @param string|null $taxId
     */
    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    /**
     * @return string|null
     */
    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    /**
     * @param string|null $registrationNumber
     */
    public function setRegistrationNumber(?string $registrationNumber): void
    {
        $this->registrationNumber = $registrationNumber;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @param string|null $fax
     */
    public function setFax(?string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getProvince(): ?string
    {
        return $this->province;
    }

    /**
     * @param string|null $province
     */
    public function setProvince(?string $province): void
    {
        $this->province = $province;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getStreet1(): ?string
    {
        return $this->street1;
    }

    /**
     * @param string|null $street1
     */
    public function setStreet1(?string $street1): void
    {
        $this->street1 = $street1;
    }

    /**
     * @return string|null
     */
    public function getStreet2(): ?string
    {
        return $this->street2;
    }

    /**
     * @param string|null $street2
     */
    public function setStreet2(?string $street2): void
    {
        $this->street2 = $street2;
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
}
