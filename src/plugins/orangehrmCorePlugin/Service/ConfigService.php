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

namespace OrangeHRM\Core\Service;

use DateInterval;
use DateTime;
use Exception;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Config;
use OrangeHRM\LDAP\Dto\LDAPSetting;

class ConfigService
{
    use DateTimeHelperTrait;

    public const FALLBACK_LANGUAGE_CODE = 'en_US';

    public const KEY_PIM_SHOW_DEPRECATED = 'pim_show_deprecated_fields';
    public const KEY_PIM_SHOW_SSN = 'pim_show_ssn';
    public const KEY_PIM_SHOW_SIN = 'pim_show_sin';
    public const KEY_PIM_SHOW_TAX_EXEMPTIONS = 'pim_show_tax_exemptions';
    public const KEY_TIMESHEET_TIME_FORMAT = 'timesheet_time_format';
    public const KEY_TIMESHEET_PERIOD_AND_START_DATE = 'timesheet_period_and_start_date';
    public const KEY_TIMESHEET_PERIOD_SET = 'timesheet_period_set';
    public const KEY_ADMIN_LOCALIZATION_DEFAULT_LANGUAGE = 'admin.localization.default_language';
    public const KEY_ADMIN_LOCALIZATION_USE_BROWSER_LANGUAGE = 'admin.localization.use_browser_language';
    public const KEY_ADMIN_LOCALIZATION_DEFAULT_DATE_FORMAT = 'admin.localization.default_date_format';
    public const KEY_INCLUDE_SUPERVISOR_CHAIN = 'include_supervisor_chain';
    public const KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME = 'admin.default_workshift_start_time';
    public const KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME = 'admin.default_workshift_end_time';
    public const KEY_OPENID_PROVIDER_ADDED = 'openId.provider.added';
    public const KEY_OPEN_SOURCE_INTEGRATIONS = 'open_source_integrations';
    public const KEY_INSTANCE_IDENTIFIER = 'instance.identifier';
    public const KEY_SENDMAIL_PATH = 'email_config.sendmail_path';
    public const KEY_LDAP_SETTINGS = 'ldap_settings';
    public const KEY_DASHBOARD_EMPLOYEES_ON_LEAVE_TODAY_SHOW_ONLY_ACCESSIBLE = 'dashboard.employees_on_leave_today.show_only_accessible';
    public const KEY_SHOW_SYSTEM_CHECK_SCREEN = 'core.show_system_check_screen';
    public const MAX_PASSWORD_LENGTH = 64;
    public const KEY_MIN_PASSWORD_LENGTH = 'auth.password_policy.min_password_length';
    public const KEY_MIN_UPPERCASE_LETTERS = 'auth.password_policy.min_uppercase_letters';
    public const KEY_MIN_LOWERCASE_LETTERS = 'auth.password_policy.min_lowercase_letters';
    public const KEY_MIN_NUMBERS_IN_PASSWORD = 'auth.password_policy.min_numbers_in_password';
    public const KEY_MIN_SPECIAL_CHARACTERS = 'auth.password_policy.min_special_characters';
    public const KEY_IS_SPACES_ALLOWED = 'auth.password_policy.is_spaces_allowed';
    public const KEY_DEFAULT_PASSWORD_STRENGTH = 'auth.password_policy.default_required_password_strength';
    public const KEY_ENFORCE_PASSWORD_STRENGTH = 'auth.password_policy.enforce_password_strength';
    public const KEY_OAUTH_ENCRYPTION_KEY = 'oauth.encryption_key';
    public const KEY_OAUTH_TOKEN_ENCRYPTION_KEY = 'oauth.token_encryption_key';
    public const KEY_OAUTH_AUTH_CODE_TTL = 'oauth.auth_code_ttl';
    public const KEY_OAUTH_REFRESH_TOKEN_TTL = 'oauth.refresh_token_ttl';
    public const KEY_OAUTH_ACCESS_TOKEN_TTL = 'oauth.access_token_ttl';

    public const MAX_ATTACHMENT_SIZE = 1048576; // 1 MB
    public const ALLOWED_FILE_TYPES = [
        'text/plain',
        'text/rtf',
        'text/csv',
        'application/csv',
        'application/rtf',
        'application/pdf',
        'application/msword',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'application/xliff+xml',
        'image/x-png',
        'image/gif',
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/png',
    ];
    public const ALLOWED_FILE_EXTENSIONS = [
        'txt',
        'csv',
        'rtf',
        'pdf',
        'doc',
        'xls',
        'ppt',
        'odt',
        'ods',
        'odp',
        'docx',
        'xlsx',
        'pptx',
        'pps',
        'ppsx',
        'gif',
        'jpeg',
        'jpg',
        'jfif',
        'png',
        'xlf'
    ];

    /**
     * @var ConfigDao|null
     */
    protected ?ConfigDao $configDao = null;

    /**
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
     */
    protected function _getConfigValue(string $key): ?string
    {
        return $this->getConfigDao()->getValue($key);
    }

    /**
     * @param string $key
     * @param string $value
     * @return Config
     */
    protected function _setConfigValue(string $key, string $value): Config
    {
        return $this->getConfigDao()->setValue($key, $value);
    }

    /**
     * Get Value: Whether timesheet period has been set
     * @return bool Returns true if timesheet period has been set
     */
    public function isTimesheetPeriodDefined(): bool
    {
        $val = $this->_getConfigValue(self::KEY_TIMESHEET_PERIOD_SET);
        return ($val == 'Yes');
    }

    /**
     * Return is Supervisor Chain supported
     * @return bool is Supervisor Chain supported
     */
    public function isSupervisorChainSupported(): bool
    {
        $val = $this->_getConfigValue(self::KEY_INCLUDE_SUPERVISOR_CHAIN);
        return ($val == 'Yes');
    }

    /**
     * Set Supervisor Chain supported
     * @param bool $value true or false
     */
    public function setSupervisorChainSupported(bool $value): void
    {
        $flag = $value ? 'Yes' : 'No';
        $this->_setConfigValue(self::KEY_INCLUDE_SUPERVISOR_CHAIN, $flag);
    }

    /**
     * Set show deprecated fields config value
     * @param bool $value true or false
     */
    public function setShowPimDeprecatedFields(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_DEPRECATED, $flag);
    }

    /**
     * @return bool
     */
    public function showPimDeprecatedFields(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_DEPRECATED);
        return ($val == 1);
    }

    /**
     * @param bool $value
     */
    public function setShowPimSSN(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_SSN, $flag);
    }

    /**
     * Show PIM Deprecated Fields
     * @return bool
     */
    public function showPimSSN(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_SSN);
        return ($val == 1);
    }

    /**
     * @param bool $value
     */
    public function setShowPimSIN(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_SIN, $flag);
    }

    /**
     * Show PIM Deprecated Fields
     * @return bool
     */
    public function showPimSIN(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_SIN);
        return ($val == 1);
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setShowPimTaxExemptions(bool $value): void
    {
        $flag = $value ? 1 : 0;
        $this->_setConfigValue(self::KEY_PIM_SHOW_TAX_EXEMPTIONS, $flag);
    }

    /**
     * @return bool
     */
    public function showPimTaxExemptions(): bool
    {
        $val = $this->_getConfigValue(self::KEY_PIM_SHOW_TAX_EXEMPTIONS);
        return ($val == 1);
    }

    /**
     * @param string $value
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
     */
    public function getAllValues(): array
    {
        return $this->getConfigDao()->getAllValues();
    }

    /**
     * Get openId provider added value
     * @return string
     */
    public function getOpenIdProviderAdded(): string
    {
        return $this->_getConfigValue(self::KEY_OPENID_PROVIDER_ADDED);
    }

    /**
     * Set openId provider added value
     * @param string $value
     */
    public function setOpenIdProviderAdded($value = 'off'): void
    {
        $this->_setConfigValue(self::KEY_OPENID_PROVIDER_ADDED, $value);
    }

    /**
     * Get Opensource integrations XML as a string
     *
     * @return string
     */
    public function getIntegrationsConfigValue(): string
    {
        return $this->_getConfigValue(self::KEY_OPEN_SOURCE_INTEGRATIONS);
    }

    /**
     * Set Opensource integrations XML as a string
     *
     * @param string $value
     */
    public function setIntegrationsConfigValue(string $value): void
    {
        $this->_setConfigValue(self::KEY_OPEN_SOURCE_INTEGRATIONS, $value);
    }

    /**
     * Set the instance identifier value
     * @param string $value
     */
    public function setInstanceIdentifier(string $value): void
    {
        $this->_setConfigValue(self::KEY_INSTANCE_IDENTIFIER, $value);
    }

    /**
     * Get instance identifier value
     * @return string|null
     */
    public function getInstanceIdentifier(): ?string
    {
        return $this->_getConfigValue(self::KEY_INSTANCE_IDENTIFIER);
    }

    /**
     * @return string|null
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
     */
    public function getTimeSheetPeriodConfig(): ?string
    {
        return $this->_getConfigValue(self::KEY_TIMESHEET_PERIOD_AND_START_DATE);
    }

    /**
     * @param string $value
     */
    public function setTimeSheetPeriodConfig(string $value): void
    {
        $this->_setConfigValue(self::KEY_TIMESHEET_PERIOD_AND_START_DATE, $value);
    }

    /**
     * @param bool $value
     */
    public function setTimeSheetPeriodSetValue(bool $value): void
    {
        $this->_setConfigValue(self::KEY_TIMESHEET_PERIOD_SET, $value ? 'Yes' : 'No');
    }

    /**
     * @return string|null
     */
    public function getTimesheetTimeFormatConfig(): ?string
    {
        return $this->_getConfigValue(self::KEY_TIMESHEET_TIME_FORMAT);
    }

    /**
     * @return LDAPSetting|null
     */
    public function getLDAPSetting(): ?LDAPSetting
    {
        $ldapSetting = $this->_getConfigValue(self::KEY_LDAP_SETTINGS);
        return $ldapSetting === null ? null : LDAPSetting::fromString($ldapSetting);
    }

    /**
     * @param LDAPSetting $ldapSetting
     */
    public function setLDAPSetting(LDAPSetting $ldapSetting): void
    {
        $this->_setConfigValue(self::KEY_LDAP_SETTINGS, (string)$ldapSetting);
    }

    /**
     * @return bool
     */
    public function getDashboardEmployeesOnLeaveTodayShowOnlyAccessibleConfig(): bool
    {
        $val = $this->_getConfigValue(self::KEY_DASHBOARD_EMPLOYEES_ON_LEAVE_TODAY_SHOW_ONLY_ACCESSIBLE);
        return ($val == 1);
    }

    /**
     * @param bool $value
     */
    public function setDashboardEmployeesOnLeaveTodayShowOnlyAccessibleConfig(bool $value): void
    {
        $this->_setConfigValue(self::KEY_DASHBOARD_EMPLOYEES_ON_LEAVE_TODAY_SHOW_ONLY_ACCESSIBLE, $value ? 1 : 0);
    }

    /**
     * @return bool
     */
    public function showSystemCheckScreen(): bool
    {
        return $this->_getConfigValue(self::KEY_SHOW_SYSTEM_CHECK_SCREEN) == 1;
    }

    /**
     * @param bool $value
     */
    public function setShowSystemCheckScreen(bool $value): void
    {
        $this->_setConfigValue(self::KEY_SHOW_SYSTEM_CHECK_SCREEN, (int)$value);
    }

    /**
     * @return string
     */
    public function getOAuthEncryptionKey(): string
    {
        return $this->_getConfigValue(self::KEY_OAUTH_ENCRYPTION_KEY);
    }

    /**
     * @return string
     */
    public function getOAuthTokenEncryptionKey(): string
    {
        return $this->_getConfigValue(self::KEY_OAUTH_TOKEN_ENCRYPTION_KEY);
    }

    /**
     * @return DateInterval
     */
    public function getOAuthAuthCodeTTL(): DateInterval
    {
        $authCodeTTL = $this->_getConfigValue(self::KEY_OAUTH_AUTH_CODE_TTL);
        try {
            return new DateInterval($authCodeTTL);
        } catch (Exception $e) {
            return new DateInterval('PT5M');
        }
    }

    /**
     * @return DateInterval
     */
    public function getOAuthRefreshTokenTTL(): DateInterval
    {
        $refreshTokenTTL = $this->_getConfigValue(self::KEY_OAUTH_REFRESH_TOKEN_TTL);
        try {
            return new DateInterval($refreshTokenTTL);
        } catch (Exception $e) {
            return new DateInterval('P1M');
        }
    }

    /**
     * @return DateInterval
     */
    public function getOAuthAccessTokenTTL(): DateInterval
    {
        $accessTokenTTL = $this->_getConfigValue(self::KEY_OAUTH_ACCESS_TOKEN_TTL);
        try {
            return new DateInterval($accessTokenTTL);
        } catch (Exception $e) {
            return new DateInterval('PT30M');
        }
    }
}
