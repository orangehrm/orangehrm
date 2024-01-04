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

namespace OrangeHRM\Core\Import;

use DateTime;
use Exception;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\NationalityService;
use OrangeHRM\Core\Api\V2\Validator\Rules\Email;
use OrangeHRM\Core\Api\V2\Validator\Rules\Phone;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Entity\Province;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PimCsvDataImport extends CsvDataImport
{
    use ServiceContainerTrait;
    use EmployeeServiceTrait;
    use LoggerTrait;
    use TextHelperTrait;

    /**
     * @var null|NationalityService
     */
    protected ?NationalityService $nationalityService = null;

    /**
     * @param array $data
     * @return bool
     */
    public function import(array $data): bool
    {
        $firstName = $data[0];
        $middleName = $data[1];
        $lastName = $data[2];
        if ($firstName === null || $lastName === null
            || $firstName === '' || $lastName === ''
            || $this->getTextHelper()->strLength($firstName) > EmployeeService::FIRST_NAME_MAX_LENGTH
            || $this->getTextHelper()->strLength($lastName) > EmployeeService::LAST_NAME_MAX_LENGTH) {
            return false;
        }
        for ($i = 3; $i < 23; $i++) {
            if (!isset($data[$i])) {
                $data[$i] = null;
            }
        }
        $employee = new Employee();
        $employee->setFirstName($firstName);
        if ($this->getTextHelper()->strLength($middleName) <= EmployeeService::MIDDLE_NAME_MAX_LENGTH) {
            $employee->setMiddleName($middleName);
        }
        $employee->setLastName($lastName);

        $employeeId = $data[3];
        if ($this->getTextHelper()->strLength($employeeId) <= EmployeeService::EMPLOYEE_ID_MAX_LENGTH) {
            if (!$this->isUniqueEmployeeId($employeeId)) {
                $this->getLogger()->warning('Employee record not imported due to duplicated employee_id');
                return false;
            }
            $employee->setEmployeeId($employeeId);
        }
        if ($this->getTextHelper()->strLength($data[4]) <= 30) {
            $employee->setOtherId($data[4]);
        }
        if ($this->getTextHelper()->strLength($data[5]) <= 30) {
            $employee->setDrivingLicenseNo($data[5]);
        }
        $employee->setDrivingLicenseExpiredDate($this->getDateTimeIfValid($data[6]));

        switch (strtolower($data[7])) {
            case 'male':
                $employee->setGender(Employee::GENDER_MALE);
                break;
            case 'female':
                $employee->setGender(Employee::GENDER_FEMALE);
                break;
        }

        switch (strtolower($data[8])) {
            case 'single':
                $employee->setMaritalStatus(Employee::MARITAL_STATUS_SINGLE);
                break;
            case 'married':
                $employee->setMaritalStatus(Employee::MARITAL_STATUS_MARRIED);
                break;
            case 'other':
                $employee->setMaritalStatus(Employee::MARITAL_STATUS_OTHER);
                break;
        }

        $employee->setNationality($this->getNationalityIfValid($data[9]));
        $employee->setBirthday($this->getDateTimeIfValid($data[10]));
        if ($this->getTextHelper()->strLength($data[11]) <= 70) {
            $employee->setStreet1($data[11]);
        }
        if ($this->getTextHelper()->strLength($data[12]) <= 70) {
            $employee->setStreet2($data[12]);
        }
        if ($this->getTextHelper()->strLength($data[13]) <= 70) {
            $employee->setCity($data[13]);
        }

        if ($this->getTextHelper()->strLength($data[15]) <= 10) {
            $employee->setZipcode($data[15]);
        }

        $code = $this->getCountryCodeIfValid($data[16]);
        if ($code !== null) {
            $employee->setCountry($code);
            if (strtolower($data[16]) == 'united states') {
                $provinceCode = $this->getProvinceIfValid($data[14]);
                $employee->setProvince($provinceCode);
            } elseif ($this->getTextHelper()->strLength($data[14]) <= 70) {
                $employee->setProvince($data[14]);
            }
        }
        if ($this->getTextHelper()->strLength($data[17]) <= 25 && $this->isValidPhoneNumber($data[17])) {
            $employee->setHomeTelephone($data[17]);
        }
        if ($this->getTextHelper()->strLength($data[18]) <= 25 && $this->isValidPhoneNumber($data[18])) {
            $employee->setMobile($data[18]);
        }
        if ($this->getTextHelper()->strLength($data[19]) <= 25 && $this->isValidPhoneNumber($data[19])) {
            $employee->setWorkTelephone($data[19]);
        }
        $workEmail = $data[20];
        $otherEmail = $data[21];
        if (!$this->isEmpty($workEmail) && $workEmail === $otherEmail) {
            $this->getLogger()->warning('Work Email and Other Email cannot be the same');
            return false;
        }
        if ($this->isValidEmail($workEmail)
            && $this->getTextHelper()->strLength($workEmail) <= EmployeeService::WORK_EMAIL_MAX_LENGTH) {
            if (!$this->isUniqueEmail($workEmail)) {
                $this->getLogger()->warning('Employee record not imported due to duplicated work_email');
                return false;
            }
            $employee->setWorkEmail($workEmail);
        }
        if ($this->isValidEmail($otherEmail)
            && $this->getTextHelper()->strLength($otherEmail) <= EmployeeService::WORK_EMAIL_MAX_LENGTH) {
            if (!$this->isUniqueEmail($otherEmail)) {
                $this->getLogger()->warning('Employee record not imported due to duplicated other_email');
                return false;
            }
            $employee->setOtherEmail($otherEmail);
        }

        $this->getEmployeeService()->saveEmployee($employee);
        return true;
    }

    /**
     * @param string|null $date
     * @return DateTime|null
     */
    private function getDateTimeIfValid(?string $date): ?DateTime
    {
        if ($this->isEmpty($date)) {
            return null;
        }
        try {
            return new DateTime($date);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param string|null $name
     * @return Nationality|null
     */
    private function getNationalityIfValid(?string $name): ?Nationality
    {
        if (!$this->isEmpty($name)) {
            return $this->getNationalityService()->getNationalityByName($name);
        }

        return null;
    }

    /**
     * @return NationalityService
     */
    public function getNationalityService(): NationalityService
    {
        return $this->nationalityService ??= new NationalityService();
    }

    /**
     * @param NationalityService $nationalityService
     */
    public function setNationalityService(NationalityService $nationalityService): void
    {
        $this->nationalityService = $nationalityService;
    }

    /**
     * @param string|null $name
     * @return string|null
     */
    private function getCountryCodeIfValid(?string $name): ?string
    {
        if (!$this->isEmpty($name) &&
            ($country = $this->getCountryService()
                ->getCountryByCountryName($name)) instanceof Country) {
            return $country->getCountryCode();
        }

        return null;
    }

    /**
     * @return CountryService
     */
    public function getCountryService(): CountryService
    {
        return $this->getContainer()->get(Services::COUNTRY_SERVICE);
    }

    /**
     * @param string|null $name
     * @return string|null
     */
    private function getProvinceIfValid(?string $name): ?string
    {
        if (!$this->isEmpty($name) &&
            ($province = $this->getCountryService()
                ->getCountryDao()
                ->getProvinceByProvinceName($name)) instanceof Province) {
            return $province->getProvinceCode();
        }

        return null;
    }

    /**
     * @param string|null $number
     * @return bool
     */
    public function isValidPhoneNumber(?string $number): bool
    {
        if ($this->isEmpty($number)) {
            return true;
        }
        $rule = new Phone();
        return $rule->validate($number);
    }

    /**
     * @param string|null $email
     * @return bool
     */
    private function isValidEmail(?string $email): bool
    {
        if ($this->isEmpty($email)) {
            return true;
        }
        $rule = new Email();
        return $rule->validate($email);
    }

    /**
     * @param string|null $email
     * @return bool
     */
    private function isUniqueEmail(?string $email): bool
    {
        return $this->isEmpty($email) || $this->getEmployeeService()->isUniqueEmail($email);
    }

    /**
     * @param string|null $employeeId
     * @return bool
     */
    private function isUniqueEmployeeId(?string $employeeId): bool
    {
        return $this->isEmpty($employeeId) ||
            $this->getEmployeeService()->isUniqueEmployeeId($employeeId);
    }

    /**
     * @param string|null $string
     * @return bool
     */
    private function isEmpty(?string $string): bool
    {
        return $string === null || $string === '';
    }
}
