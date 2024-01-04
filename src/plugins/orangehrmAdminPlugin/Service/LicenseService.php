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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\LicenseDao;
use OrangeHRM\Admin\Dto\LicenseSearchFilterParams;
use OrangeHRM\Entity\License;

class LicenseService
{
    /**
     * @var LicenseDao|null
     *
     */
    private ?LicenseDao $licenseDao = null;

    /**
     * Saves a license
     *
     * Can be used for a new record or updating.
     *
     * @param License $license
     * @return License
     */
    public function saveLicense(License $license): License
    {
        return $this->getLicenseDao()->saveLicense($license);
    }

    /**
     * @return LicenseDao
     */
    public function getLicenseDao(): LicenseDao
    {
        if (!($this->licenseDao instanceof LicenseDao)) {
            $this->licenseDao = new LicenseDao();
        }

        return $this->licenseDao;
    }

    /**
     * @param LicenseDao $licenseDao
     * @return void
     */
    public function setLicenseDao(LicenseDao $licenseDao): void
    {
        $this->licenseDao = $licenseDao;
    }

    /**
     * Retrieves a license by ID
     *
     * @param int $id
     * @return License An instance of License or NULL
     */
    public function getLicenseById(int $id): ?License
    {
        return $this->getLicenseDao()->getLicenseById($id);
    }

    /**
     * Retrieves a license by name
     *
     * Case-insensitive
     *
     * @param string $name
     * @return License An instance of License or false
     */
    public function getLicenseByName(string $name): ?License
    {
        return $this->getLicenseDao()->getLicenseByName($name);
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchParamHolder
     * @return array
     */
    public function getLicenseList(LicenseSearchFilterParams $licenseSearchParamHolder): array
    {
        return $this->getLicenseDao()->getLicenseList($licenseSearchParamHolder);
    }

    /**
     * @param LicenseSearchFilterParams $licenseSearchParamHolder
     * @return int
     */
    public function getLicenseCount(LicenseSearchFilterParams $licenseSearchParamHolder): int
    {
        return $this->getLicenseDao()->getLicenseCount($licenseSearchParamHolder);
    }

    /**
     * Deletes licenses
     *
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */
    public function deleteLicenses(array $toDeleteIds): int
    {
        return $this->getLicenseDao()->deleteLicenses($toDeleteIds);
    }

    /**
     * Checks whether the given license name exists
     *
     * Case-insensitive
     *
     * @param string $licenseName License name that needs to be checked
     * @return Bool
     */
    public function isExistingLicenseName(string $licenseName): bool
    {
        return $this->getLicenseDao()->isExistingLicenseName($licenseName);
    }
}
