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
 *
 */

namespace OrangeHRM\Core\Import;

use DateTime;
use Exception;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\NationalityService;
use OrangeHRM\Core\Api\V2\Validator\Rules\Email;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PimCsvDataImport extends CsvDataImport
{
    use ServiceContainerTrait;
    use EmployeeServiceTrait;

    /**
     * @var null|NationalityService
     */
    protected ?NationalityService $nationalityService = null;

    /**
     * @param array $data
     * @return bool
     * @throws DaoException
     */
    public function import(array $data): bool
    {
        if ($data[0] == "" || $data[2] == "" || strlen($data[0]) > 30 || strlen($data[2]) > 30) {
            return false;
        }
        for ($i = 3; $i < 23; $i++) {
            if (!isset($data[$i])) {
                $data[$i] = null;
            }
        }
        $employee = new Employee();
        $employee->setFirstName($data[0]);
        if (strlen($data[1]) <= 30) {
            $employee->setMiddleName($data[1]);
        }
        $employee->setLastName($data[2]);

        if (strlen($data[3]) <= 50) {
            $employee->setEmployeeId($data[3]);
        }
        if (strlen($data[4]) <= 30) {
            $employee->setOtherId($data[4]);
        }
        if (strlen($data[5]) <= 30) {
            $employee->setDrivingLicenseNo($data[5]);
        }
        if ($this->isValidDate($data[6])) {
            $employee->setDrivingLicenseExpiredDate(new DateTime($data[6]));
        }

        if (strtolower($data[7]) == 'male') {
            $employee->setGender('1');
        } else {
            if (strtolower($data[7]) == 'female') {
                $employee->setGender('2');
            }
        }

        if (strtolower($data[8]) == 'single') {
            $employee->setMaritalStatus('Single');
        } else {
            if (strtolower($data[8]) == 'married') {
                $employee->setMaritalStatus('Married');
            } else {
                if (strtolower($data[8]) == 'other') {
                    $employee->setMaritalStatus('Other');
                }
            }
        }

        $nationality = $this->isValidNationality($data[9]);
        if (!empty($nationality)) {
            $employee->setNationality($nationality);
        }
        if ($this->isValidDate($data[10])) {
            $employee->setBirthday(new DateTime($data[10]));
        }
        if (strlen($data[11]) <= 70) {
            $employee->setStreet1($data[11]);
        }
        if (strlen($data[12]) <= 70) {
            $employee->setStreet2($data[12]);
        }
        if (strlen($data[13]) <= 70) {
            $employee->setCity($data[13]);
        }

        if (strlen($data[15]) <= 10) {
            $employee->setZipcode($data[15]);
        }

        $code = $this->isValidCountry($data[16]);
        if (!empty($code)) {
            $employee->setCountry($code);
            if (strtolower($data[16]) == 'united states') {
                $code = $this->isValidProvince($data[14]);
                if (!empty($code)) {
                    $employee->setProvince($code);
                }
            } else {
                if (strlen($data[14]) <= 70) {
                    $employee->setProvince($data[14]);
                }
            }
        }
        if (strlen($data[17]) <= 25 && $this->isValidPhoneNumber($data[17])) {
            $employee->setHomeTelephone($data[17]);
        }
        if (strlen($data[18]) <= 25 && $this->isValidPhoneNumber($data[18])) {
            $employee->setMobile($data[18]);
        }
        if (strlen($data[19]) <= 25 && $this->isValidPhoneNumber($data[19])) {
            $employee->setWorkTelephone($data[19]);
        }
        if ($this->isValidEmail($data[20]) && strlen($data[20]) <= 50 && $this->isUniqueEmail($data[20])) {
            $employee->setWorkEmail($data[20]);
        }
        if ($this->isValidEmail($data[21]) && strlen($data[21]) <= 50 && $this->isUniqueEmail($data[21])) {
            $employee->setOtherEmail($data[21]);
        }

        $this->getEmployeeService()->saveEmployee($employee);
        return true;
    }

    /**
     * @param string|null $date
     * @return bool
     */
    private function isValidDate(?string $date): bool
    {
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
            list($year, $month, $day) = explode('-', $date);
            return checkdate($month, $day, $year);
        } else {
            return false;
        }
    }

    /**
     * @param string|null $name
     * @return Nationality|void
     * @throws DaoException
     */
    private function isValidNationality(?string $name)
    {
        $nationality = $this->getNationalityService()->getNationalityByName($name);
        if ($nationality) {
            return $nationality;
        }
    }

    /**
     * @return NationalityService
     */
    public function getNationalityService(): NationalityService
    {
        if (!$this->nationalityService instanceof NationalityService) {
            $this->nationalityService = new NationalityService();
        }
        return $this->nationalityService;
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
     * @return string|void
     * @throws DaoException
     * @throws Exception
     */
    private function isValidCountry(?string $name)
    {
        if ($name) {
            $country = $this->getCountryService()->getCountryByCountryName($name);
            if ($country) {
                return $country->getCountryCode();
            }
        }
    }

    /**
     * @return CountryService $countryService
     * @throws Exception
     */
    public function getCountryService(): CountryService
    {
        return $this->getContainer()->get(Services::COUNTRY_SERVICE);
    }

    /**
     * @param string|null $name
     * @return string|void
     * @throws Exception
     */
    private function isValidProvince(?string $name)
    {
        $province = $this->getCountryService()->getCountryDao()->getProvinceByProvinceName($name);
        if ($province) {
            return $province->getProvinceCode();
        }
    }

    /**
     * @param string|null $number
     * @return bool
     */
    public function isValidPhoneNumber(?string $number): bool
    {
        if (preg_match('/^\+?[0-9 \-]+$/', $number)) {
            return true;
        }
        return false;
    }

    /**
     * @param string|null $email
     * @return bool
     */
    private function isValidEmail(?string $email): bool
    {
        if (preg_match(Email::EMAIL_REGEX, $email)) {
            return true;
        }
        return false;
    }

    /**
     * @param string|null $email
     * @return bool
     */
    private function isUniqueEmail(?string $email): bool
    {
        $emailList = $this->getEmployeeService()->getEmployeeDao()->getEmailList();
        $isUnique = true;
        foreach ($emailList as $empEmail) {
            if ($empEmail['workEmail'] == $email || $empEmail['otherEmail'] == $email) {
                $isUnique = false;
            }
        }
        return $isUnique;
    }
}
