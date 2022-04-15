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

namespace OrangeHRM\Core\Service;

use DateTime;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Config;
use OrangeHRM\Framework\Logger;

/**
 * Config Service: Manages configuration entries in hs_hr_config
 *
 */
class ConfigService
{
    use DateTimeHelperTrait;

    public const FALLBACK_LANGUAGE_CODE = 'en_US';

    /**
     * @var ConfigDao|null
     */
    protected ?ConfigDao $configDao = null;

    public const KEY_PIM_SHOW_DEPRECATED = "pim_show_deprecated_fields";
    public const KEY_PIM_SHOW_SSN = 'pim_show_ssn';
    public const KEY_PIM_SHOW_SIN = 'pim_show_sin';
    public const KEY_PIM_SHOW_TAX_EXEMPTIONS = 'pim_show_tax_exemptions';
    public const KEY_TIMESHEET_TIME_FORMAT = 'timesheet_time_format';
    public const KEY_TIMESHEET_PERIOD_AND_START_DATE = 'timesheet_period_and_start_date';
    public const KEY_TIMESHEET_PERIOD_SET = 'timesheet_period_set';
    public const KEY_ADMIN_LOCALIZATION_DEFAULT_LANGUAGE = 'admin.localization.default_language';
    public const KEY_ADMIN_LOCALIZATION_USE_BROWSER_LANGUAGE = 'admin.localization.use_browser_language';
    public const KEY_ADMIN_LOCALIZATION_DEFAULT_DATE_FORMAT = 'admin.localization.default_date_format';
//    const KEY_NON_LEAP_YEAR_LEAVE_PERIOD_START_DATE = 'leave.nonLeapYearLeavePeriodStartDate';
//    const KEY_IS_LEAVE_PERIOD_START_ON_FEB_29 = 'leave.isLeavePeriodStartOnFeb29th';
//    public const KEY_LEAVE_PERIOD_START_DATE = 'leave.leavePeriodStartDate';
    public const KEY_INCLUDE_SUPERVISOR_CHAIN = 'include_supervisor_chain';
    public const KEY_THEME_NAME = 'themeName';
    public const KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME = 'admin.default_workshift_start_time';
    public const KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME = 'admin.default_workshift_end_time';
//    const KEY_AUTH_LOGINS = 'auth.logins';
    public const KEY_OPENID_PROVIDER_ADDED = 'openId.provider.added';
    public const KEY_OPEN_SOURCE_INTEGRATIONS = 'open_source_integrations';
    public const KEY_INSTANCE_IDENTIFIER = "instance.identifier";
    public const KEY_INSTANCE_IDENTIFIER_CHECKSUM = "instance.identifier_checksum";
    public const KEY_SENDMAIL_PATH = 'email_config.sendmail_path';

    public const MAX_ATTACHMENT_SIZE = 1048576; // 1 MB
    public const ALLOWED_FILE_TYPES = [
        "text/plain",
        "text/rtf",
        "text/csv",
        "application/csv",
        "application/rtf",
        "application/pdf",
        "application/msword",
        "application/vnd.ms-excel",
        "application/vnd.ms-powerpoint",
        "application/vnd.oasis.opendocument.text",
        "application/vnd.oasis.opendocument.spreadsheet",
        "application/vnd.oasis.opendocument.presentation",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
        "image/x-png",
        "image/gif",
        "image/jpeg",
        "image/jpg",
        "image/pjpeg",
        "image/png"
    ];
    public const ALLOWED_FILE_EXTENSIONS = [
        "txt",
        "csv",
        "rtf",
        "pdf",
        "doc",
        "xls",
        "ppt",
        "odt",
        "ods",
        "odp",
        "docx",
        "xlsx",
        "pptx",
        "pps",
        "ppsx",
        "gif",
        "jpeg",
        "jpg",
        "png"
    ];

    /**
     * Get ConfigDao
     * @return ConfigDao
     */
    public function getConfigDao(): ConfigDao
    {
        if (!$this->configDao instanceof ConfigDao) {
            $this->configDao = new ConfigDao();
        }

        return $this->configDao;
    }

    /**
     * Set ConfigDao
     * @param ConfigDao $configDao
     * @return void
     */
    public function setConfigDao(ConfigDao $configDao): void
    {
        $this->configDao = $configDao;
    }

    /**
     * @param string $key
     * @return string|null
     * @throws CoreServiceException
     */
    protected function _getConfigValue(string $key): ?string
    {
        try {
            return $this->getConfigDao()->getValue($key);
        } catch (DaoException $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            throw new CoreServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return Config
     * @throws CoreServiceException
     */
    protected function _setConfigValue(string $key, string $value): Config
    {
        try {
            return $this->getConfigDao()->setValue($key, $value);
        } catch (DaoException $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            throw new CoreServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Value: Whether timesheet period has been set
     * @return bool Returns true if timesheet period has been set
     * @throws CoreServiceException
     */
    public function isTimesheetPeriodDefined(): bool
    {
        $val = $this->_getConfigValue(self::KEY_TIMESHEET_PERIOD_SET);
        return ($val == 'Yes');
    }

    /**
     * Return is Supervisor Chain supported
     * @return bool is Supervisor Chain supported
     * @throws CoreServiceException
     */
    public function isSupervisorChainSupported(): bool
    {
        $val = $this->_getConfigValue(self::KEY_INCLUDE_SUPERVISOR_CHAIN);
        return ($val == 'Yes');
    }

    /**
     * Set Supervisor Chain supported
     * @param bool $value true or false
     * @throws CoreServiceException
     */
    public function setSupervisorChainSupported(bool $value): void
    {
        $flag = $value ? 'Yes' : 'No';
        $this->_setConfigValue(self::KEY_INCLUDE_SUPERVISOR_CHAIN, $flag);
    }

    /**
     * Set show deprecated fields config value
     * @param bool $value true or false
     * @throws CoreServiceException
     */
    public function setShowPimDeprecatedFields(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_DEPRECATED, $flag);
    }

    /**
     * @return bool
     * @throws CoreServiceException
     */
    public function showPimDeprecatedFields(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_DEPRECATED);
        return ($val == 1);
    }

    /**
     * @param bool $value
     * @throws CoreServiceException
     */
    public function setShowPimSSN(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_SSN, $flag);
    }

    /**
     * Show PIM Deprecated Fields
     * @return bool
     * @throws CoreServiceException
     */
    public function showPimSSN(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_SSN);
        return ($val == 1);
    }

    /**
     * @param bool $value
     * @throws CoreServiceException
     */
    public function setShowPimSIN(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_SIN, $flag);
    }

    /**
     * Show PIM Deprecated Fields
     * @return bool
     * @throws CoreServiceException
     */
    public function showPimSIN(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_SIN);
        return ($val == 1);
    }

    /**
     * @param bool $value
     * @return void
     * @throws CoreServiceException
     */
    public function setShowPimTaxExemptions(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_TAX_EXEMPTIONS, $flag);
    }

    /**
     * @return bool
     * @throws CoreServiceException
     */
    public function showPimTaxExemptions(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_TAX_EXEMPTIONS);
        return ($val == 1);
    }

    /**
     * @param string $value
     * @throws CoreServiceException
     */
    public function setThemeName(string $value): void
    {
        $this->_setConfigValue(self::KEY_THEME_NAME, $value);
    }

    /**
     * @return string
     * @throws CoreServiceException
     */
    public function getThemeName(): string
    {
        return $this->_getConfigValue(self::KEY_THEME_NAME);
    }

    /**
     * @param string $value
     * @throws CoreServiceException
     */
    public function setAdminLocalizationDefaultLanguage(string $value): void
    {
        $this->_setConfigValue(self::KEY_ADMIN_LOCALIZATION_DEFAULT_LANGUAGE, $value);
    }

    /**
     * @param bool $value
     */
    public function setAdminLocalizationUseBrowserLanguage(bool $value): void
    {
        $this->_setConfigValue(self::KEY_ADMIN_LOCALIZATION_USE_BROWSER_LANGUAGE, $value ? 'Yes' : 'No');
    }

    /**
     * @param string $value
     * @throws CoreServiceException
     */
    public function setAdminLocalizationDefaultDateFormat(string $value): void
    {
        $this->_setConfigValue(self::KEY_ADMIN_LOCALIZATION_DEFAULT_DATE_FORMAT, $value);
    }

    /**
     * @return bool
     */
    public function getAdminLocalizationUseBrowserLanguage(): bool
    {
        $val = $this->_getConfigValue(self::KEY_ADMIN_LOCALIZATION_USE_BROWSER_LANGUAGE);
        return ($val == 'Yes');
    }

    /**
     * @return string
     */
    public function getAdminLocalizationDefaultDateFormat(): string
    {
        return $this->_getConfigValue(self::KEY_ADMIN_LOCALIZATION_DEFAULT_DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getAdminLocalizationDefaultLanguage(): string
    {
        $langCode = $this->_getConfigValue(self::KEY_ADMIN_LOCALIZATION_DEFAULT_LANGUAGE);
        return empty($langCode) ? self::FALLBACK_LANGUAGE_CODE : $langCode;
    }

//    /**
//     * @return string
//     * @throws CoreServiceException
//     */
//    public function getNonLeapYearLeavePeriodStartDate():string {
//        return $this->_getConfigValue(self::KEY_NON_LEAP_YEAR_LEAVE_PERIOD_START_DATE);
//    }
//
//    /**
//     * @param string $startDate
//     * @throws CoreServiceException
//     */
//    public function setNonLeapYearLeavePeriodStartDate(string $startDate):void {
//        $this->_setConfigValue(self::KEY_NON_LEAP_YEAR_LEAVE_PERIOD_START_DATE, $startDate);
//    }

//    public function getIsLeavePeriodStartOnFeb29th(): {
//        return $this->_getConfigValue(self::KEY_IS_LEAVE_PERIOD_START_ON_FEB_29);
//    }
//
//    public function setIsLeavePeriodStartOnFeb29th($value) {
//        $this->_setConfigValue(self::KEY_IS_LEAVE_PERIOD_START_ON_FEB_29, $value);
//    }
//
//    /**
//     * @return string
//     * @throws CoreServiceException
//     */
//    public function getLeavePeriodStartDate(): string
//    {
//        return $this->_getConfigValue(self::KEY_LEAVE_PERIOD_START_DATE);
//    }
//
//    /**
//     * @param string $startDate
//     * @throws CoreServiceException
//     */
//    public function setLeavePeriodStartDate(string $startDate): void
//    {
//        $this->_setConfigValue(self::KEY_LEAVE_PERIOD_START_DATE, $startDate);
//    }

    /**
     * Get default workshift start time
     * @return string
     */
    public function getDefaultWorkShiftStartTime(): string
    {
        return $this->_getConfigValue(self::KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME);
    }

    /**
     * Set workshift default start time
     * @param string $startTime Time in HH:MM format
     */
    public function setDefaultWorkShiftStartTime(string $startTime): void
    {
        $this->_setConfigValue(self::KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME, $startTime);
    }

    /**
     * Get default workshift end time
     * @return string
     */
    public function getDefaultWorkShiftEndTime(): string
    {
        return $this->_getConfigValue(self::KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME);
    }

    /**
     * Set workshift default end time
     * @param string $endTime Time in HH:MM format
     */
    public function setDefaultWorkShiftEndTime(string $endTime): void
    {
        $this->_setConfigValue(self::KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME, $endTime);
    }

    /**
     * @return float
     */
    public function getDefaultWorkShiftLength(): float
    {
        return $this->getDateTimeHelper()->dateDiffInHours(
            new DateTime($this->getDefaultWorkShiftStartTime()),
            new DateTime($this->getDefaultWorkShiftEndTime())
        );
    }

    /**
     * Get all defined config values as a key=>value array
     * @return array
     * @throws DaoException
     */
    public function getAllValues(): array
    {
        return $this->getConfigDao()->getAllValues();
    }

//     public function incrementLogins() {
//        $currentValue = (int)$this->_getConfigValue(self::KEY_AUTH_LOGINS);
//        $this->_setConfigValue(self::KEY_AUTH_LOGINS, ++$currentValue);
//    }
//
//    public function getLogins() {
//        $this->_getConfigValue(self::KEY_AUTH_LOGINS);
//    }

    /**
     * Get openId provider added value
     * @return string
     * @throws CoreServiceException
     */
    public function getOpenIdProviderAdded(): string
    {
        return $this->_getConfigValue(self::KEY_OPENID_PROVIDER_ADDED);
    }

    /**
     * Set openId provider added value
     * @param string $value
     * @throws CoreServiceException
     */
    public function setOpenIdProviderAdded($value = 'off'): void
    {
        $this->_setConfigValue(self::KEY_OPENID_PROVIDER_ADDED, $value);
    }

    /**
     * Get Opensource integrations XML as a string
     *
     * @return string
     * @throws CoreServiceException
     */
    public function getIntegrationsConfigValue(): string
    {
        return $this->_getConfigValue(self::KEY_OPEN_SOURCE_INTEGRATIONS);
    }

    /**
     * Set Opensource integrations XML as a string
     *
     * @param string $value
     * @throws CoreServiceException
     */
    public function setIntegrationsConfigValue(string $value): void
    {
        $this->_setConfigValue(self::KEY_OPEN_SOURCE_INTEGRATIONS, $value);
    }

    /**
     * Set the instance identifier value
     * @param string $value
     * @throws CoreServiceException
     */
    public function setInstanceIdentifier(string $value): void
    {
        $this->_setConfigValue(self::KEY_INSTANCE_IDENTIFIER, $value);
    }

    /**
     * Get instance identifier value
     * @return string
     * @throws CoreServiceException
     */
    public function getInstanceIdentifier(): string
    {
        return $this->_getConfigValue(self::KEY_INSTANCE_IDENTIFIER);
    }

    /**
     * Set the instance identifier checksum value
     * @param string $value
     * @throws CoreServiceException
     */
    public function setInstanceIdentifierChecksum(string $value): void
    {
        $this->_setConfigValue(self::KEY_INSTANCE_IDENTIFIER_CHECKSUM, $value);
    }

    /**
     * Get instance identifier checksum value
     * @return string
     * @throws CoreServiceException
     */
    public function getInstanceIdentifierChecksum(): string
    {
        return $this->_getConfigValue(self::KEY_INSTANCE_IDENTIFIER_CHECKSUM);
    }

    /**
     * @return string|null
     * @throws CoreServiceException
     */
    public function getSendmailPath(): ?string
    {
        return $this->_getConfigValue(self::KEY_SENDMAIL_PATH);
    }

    /**
     * @return int
     */
    public function getMaxAttachmentSize(): int
    {
        return self::MAX_ATTACHMENT_SIZE;
    }

    /**
     * @return string[]
     */
    public function getAllowedFileTypes(): array
    {
        return self::ALLOWED_FILE_TYPES;
    }

    /**
     * @return string[]
     */
    public function getAllowedFileExtensions(): array
    {
        return self::ALLOWED_FILE_EXTENSIONS;
    }

    /**
     * @return string|null
     * @throws CoreServiceException
     */
    public function getTimeSheetPeriodConfig(): ?string
    {
        return $this->_getConfigValue(self::KEY_TIMESHEET_PERIOD_AND_START_DATE);
    }

    /**
     * @param string $value
     * @throws CoreServiceException
     */
    public function setTimeSheetPeriodConfig(string $value): void
    {
        $this->_setConfigValue(self::KEY_TIMESHEET_PERIOD_AND_START_DATE, $value);
    }

    /**
     * @param bool $value
     * @throws CoreServiceException
     */
    public function setTimeSheetPeriodSetValue(bool $value): void
    {
        $this->_setConfigValue(self::KEY_TIMESHEET_PERIOD_SET, $value ? 'Yes' : 'No');
    }

    /**
     * @return string|null
     * @throws CoreServiceException
     */
    public function getTimesheetTimeFormatConfig(): ?string
    {
        return $this->_getConfigValue(self::KEY_TIMESHEET_TIME_FORMAT);
    }
}
