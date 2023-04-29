<?php

namespace OrangeHRM\Authentication\Dto;

class OrganizationSetup
{
    private ?string $organizationName;
    private ?string $countryCode;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $email;
    private ?string $confirmPassword;
    private ?string $password;

    /**
     * @param string|null $organizationName
     * @return OrganizationSetup
     */
    public function setOrganizationName(?string $organizationName): OrganizationSetup
    {
        $this->organizationName = $organizationName;
        return $this;
    }

    /**
     * @param string|null $countryCode
     * @return OrganizationSetup
     */
    public function setCountryCode(?string $countryCode): OrganizationSetup
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @param string|null $firstName
     * @return OrganizationSetup
     */
    public function setFirstName(?string $firstName): OrganizationSetup
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param string|null $lastName
     * @return OrganizationSetup
     */
    public function setLastName(?string $lastName): OrganizationSetup
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string|null $email
     * @return OrganizationSetup
     */
    public function setEmail(?string $email): OrganizationSetup
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string|null $confirmPassword
     * @return OrganizationSetup
     */
    public function setConfirmPassword(?string $confirmPassword): OrganizationSetup
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @return string|null
     */
    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public static function instance(): OrganizationSetup
    {
        return new OrganizationSetup();
    }

    /**
     * @param string|null $password
     * @return OrganizationSetup
     */
    public function setPassword(?string $password): OrganizationSetup
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}