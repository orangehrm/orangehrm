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

namespace OrangeHRM\Installer\Util;

use DateTime;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\Installer\Controller\AbstractInstallerVueController;

class StateContainer
{
    public const DB_NAME = 'dbName';
    public const DB_HOST = 'dbHost';
    public const DB_PORT = 'dbPort';
    public const DB_USER = 'dbUser';
    public const DB_PASSWORD = 'dbPass';
    public const ORANGEHRM_DB_USER = 'ohrmDbUser';
    public const ORANGEHRM_DB_PASSWORD = 'ohrmDbPassword';
    public const IS_SET_DB_INFO = 'isSetDbInfo';
    public const INSTALLATION_DB_TYPE = 'dbType';
    public const ENABLE_DATA_ENCRYPTION = 'enableDataEncryption';

    public const CURRENT_VERSION = 'currentVersion';

    public const CURRENT_SCREEN = 'currentScreen';
    public const IS_UPGRADER = 'isUpgrader';

    public const INSTANCE_ORG_NAME = 'organizationName';
    public const INSTANCE_COUNTRY_CODE = 'countryCode';
    public const INSTANCE_LANG_CODE = 'langCode';
    public const INSTANCE_TIMEZONE = 'timezone';

    public const ADMIN_FIRST_NAME = 'firstName';
    public const ADMIN_LAST_NAME = 'lastName';
    public const ADMIN_EMAIL = 'email';
    public const ADMIN_USERNAME = 'username';
    public const ADMIN_PASSWORD = 'password';
    public const ADMIN_CONTACT = 'contact';
    public const ADMIN_REGISTRATION_CONSENT = 'registrationConsent';

    public const INSTANCE_IDENTIFIER = 'instanceIdentifier';
    public const IS_INITIAL_REG_DATA_SENT = 'isInitialRegDataSent';
    public const INITIAL_REGISTRATION_DATA_BODY = 'initialRegistrationDataBody';
    public const INSTALLER_STARTED_AT = 'installerStartedAt';
    public const INSTALLER_STARTED_EVENT_STORED = 'installerStartedEventStored';

    public const MIGRATION_COMPLETED = 'migrationCompleted';

    /**
     * @var null|self
     */
    protected static ?self $instance = null;

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return ServiceContainer::getContainer()->get(Services::SESSION);
    }

    private function __construct()
    {
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function setAttribute(string $name, $value): void
    {
        $this->getSession()->set($name, $value);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->getSession()->get($name, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->getSession()->has($name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function removeAttribute(string $name)
    {
        return $this->getSession()->remove($name);
    }

    /**
     * @return array
     */
    public function getAllAttributes(): array
    {
        return $this->getSession()->all();
    }

    /**
     * @param string $dbHost
     * @param string $dbPort
     * @param UserCredential $dbUserCredential
     * @param string $dbName
     * @param UserCredential|null $ohrmDbUserCredential
     */
    public function storeDbInfo(
        string $dbHost,
        string $dbPort,
        UserCredential $dbUserCredential,
        string $dbName,
        ?UserCredential $ohrmDbUserCredential = null,
        bool $enableDataEncryption = false
    ): void {
        $this->clearDbInfo();
        $this->getSession()->set(self::DB_NAME, $dbName);
        $this->getSession()->set(self::DB_USER, $dbUserCredential->getUsername());
        $this->getSession()->set(self::DB_PASSWORD, $dbUserCredential->getPassword() ?? '');
        $this->getSession()->set(self::DB_HOST, $dbHost);
        $this->getSession()->set(self::DB_PORT, $dbPort);
        $this->getSession()->set(self::ENABLE_DATA_ENCRYPTION, $enableDataEncryption);
        if ($ohrmDbUserCredential instanceof UserCredential) {
            $this->getSession()->set(self::ORANGEHRM_DB_USER, $ohrmDbUserCredential->getUsername());
            $this->getSession()->set(self::ORANGEHRM_DB_PASSWORD, $ohrmDbUserCredential->getPassword() ?? '');
        }
        $this->getSession()->set(self::IS_SET_DB_INFO, true);
    }

    /***
     * @return array
     */
    public function getDbInfo(): array
    {
        $dbInfo = [
            self::DB_NAME => $this->getSession()->get(self::DB_NAME),
            self::DB_USER => $this->getSession()->get(self::DB_USER),
            self::DB_PASSWORD => $this->getSession()->get(self::DB_PASSWORD),
            self::DB_HOST => $this->getSession()->get(self::DB_HOST),
            self::DB_PORT => $this->getSession()->get(self::DB_PORT),
            self::ENABLE_DATA_ENCRYPTION => $this->getSession()->get(self::ENABLE_DATA_ENCRYPTION),
        ];
        if ($this->getSession()->has(self::ORANGEHRM_DB_USER)) {
            $dbInfo[self::ORANGEHRM_DB_USER] = $this->getSession()->get(self::ORANGEHRM_DB_USER);
            $dbInfo[self::ORANGEHRM_DB_PASSWORD] = $this->getSession()->get(self::ORANGEHRM_DB_PASSWORD);
        }
        return $dbInfo;
    }

    /**
     * Clear stored DB configs
     */
    public function clearDbInfo(): void
    {
        $this->getSession()->remove(self::DB_NAME);
        $this->getSession()->remove(self::DB_USER);
        $this->getSession()->remove(self::DB_PASSWORD);
        $this->getSession()->remove(self::DB_HOST);
        $this->getSession()->remove(self::DB_PORT);
        $this->getSession()->remove(self::ORANGEHRM_DB_USER);
        $this->getSession()->remove(self::ORANGEHRM_DB_PASSWORD);
        $this->getSession()->remove(self::ENABLE_DATA_ENCRYPTION);
        $this->getSession()->set(self::IS_SET_DB_INFO, false);
        DatabaseServerConnection::reset();
        Connection::reset();
    }

    /**
     * @return bool
     */
    public function isSetDbInfo(): bool
    {
        return $this->getSession()->get(self::IS_SET_DB_INFO, false);
    }

    /**
     * @param string $dbType
     */
    public function setDbType(string $dbType)
    {
        $this->getSession()->set(self::INSTALLATION_DB_TYPE, $dbType);
    }

    /**
     * @return string|null
     */
    public function getDbType(): ?string
    {
        return $this->getSession()->get(self::INSTALLATION_DB_TYPE);
    }

    /**
     * @param string $screen
     * @param bool $isUpgrader
     */
    public function setCurrentScreen(string $screen, bool $isUpgrader = false): void
    {
        $this->getSession()->set(self::CURRENT_SCREEN, $screen);
        $this->getSession()->set(self::IS_UPGRADER, $isUpgrader);
        if ($screen === AbstractInstallerVueController::WELCOME_SCREEN) {
            $this->getSession()->set(self::IS_UPGRADER, null);
        }
    }

    /**
     * @return string|null
     */
    public function getCurrentScreen(): ?string
    {
        return $this->getSession()->get(self::CURRENT_SCREEN);
    }

    /**
     * @return bool|null
     */
    public function isUpgrader(): ?bool
    {
        return $this->getSession()->get(self::IS_UPGRADER);
    }

    /**
     * @param string $currentVersion
     */
    public function setCurrentVersion(string $currentVersion): void
    {
        $this->getSession()->set(self::CURRENT_VERSION, $currentVersion);
    }

    /**
     * @return string|null
     */
    public function getCurrentVersion(): ?string
    {
        return $this->getSession()->get(self::CURRENT_VERSION);
    }

    /**
     * @param string $organizationName
     * @param string $countryCode
     * @param string|null $langCode
     * @param string|null $timezone
     */
    public function storeInstanceData(
        string $organizationName,
        string $countryCode,
        ?string $langCode,
        ?string $timezone
    ): void {
        $this->getSession()->set(self::INSTANCE_ORG_NAME, $organizationName);
        $this->getSession()->set(self::INSTANCE_COUNTRY_CODE, $countryCode);
        $this->getSession()->set(self::INSTANCE_LANG_CODE, $langCode);
        $this->getSession()->set(self::INSTANCE_TIMEZONE, $timezone);
    }

    /**
     * @return array
     */
    public function getInstanceData(): array
    {
        return [
            self::INSTANCE_ORG_NAME => $this->getSession()->get(self::INSTANCE_ORG_NAME),
            self::INSTANCE_COUNTRY_CODE => $this->getSession()->get(self::INSTANCE_COUNTRY_CODE),
            self::INSTANCE_LANG_CODE => $this->getSession()->get(self::INSTANCE_LANG_CODE),
            self::INSTANCE_TIMEZONE => $this->getSession()->get(self::INSTANCE_TIMEZONE),
        ];
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param UserCredential $userCredential
     * @param string|null $contact
     */
    public function storeAdminUserData(
        string $firstName,
        string $lastName,
        string $email,
        UserCredential $userCredential,
        ?string $contact
    ): void {
        $this->getSession()->set(self::ADMIN_FIRST_NAME, $firstName);
        $this->getSession()->set(self::ADMIN_LAST_NAME, $lastName);
        $this->getSession()->set(self::ADMIN_EMAIL, $email);
        $this->getSession()->set(self::ADMIN_USERNAME, $userCredential->getUsername());
        $this->getSession()->set(self::ADMIN_PASSWORD, $userCredential->getPassword());
        $this->getSession()->set(self::ADMIN_CONTACT, $contact);
    }

    /**
     * @return array
     */
    public function getAdminUserData(): array
    {
        return [
            self::ADMIN_FIRST_NAME => $this->getSession()->get(self::ADMIN_FIRST_NAME),
            self::ADMIN_LAST_NAME => $this->getSession()->get(self::ADMIN_LAST_NAME),
            self::ADMIN_EMAIL => $this->getSession()->get(self::ADMIN_EMAIL),
            self::ADMIN_USERNAME => $this->getSession()->get(self::ADMIN_USERNAME),
            self::ADMIN_PASSWORD => $this->getSession()->get(self::ADMIN_PASSWORD),
            self::ADMIN_CONTACT => $this->getSession()->get(self::ADMIN_CONTACT),
        ];
    }

    /**
     * @param bool $agreed
     */
    public function storeRegConsent(bool $agreed): void
    {
        $this->getSession()->set(self::ADMIN_REGISTRATION_CONSENT, $agreed);
    }

    /**
     * @return bool
     */
    public function getRegConsent(): bool
    {
        return $this->getSession()->get(self::ADMIN_REGISTRATION_CONSENT, true);
    }

    /**
     * @param string $instanceIdentifier
     */
    public function storeInstanceIdentifierData(string $instanceIdentifier): void
    {
        $this->getSession()->set(self::INSTANCE_IDENTIFIER, $instanceIdentifier);
    }

    /**
     * @return array|null
     */
    public function getInstanceIdentifierData(): ?array
    {
        if ($this->getSession()->has(self::INSTANCE_IDENTIFIER)) {
            return [
                self::INSTANCE_IDENTIFIER => $this->getSession()->get(self::INSTANCE_IDENTIFIER),
            ];
        }
        return null;
    }

    /**
     * @param array $data
     * @param bool $published
     * @param bool $installerStartedEventStored
     */
    public function storeInitialRegistrationData(
        array $data,
        bool $published = false,
        bool $installerStartedEventStored = false
    ): void {
        $this->setAttribute(self::INITIAL_REGISTRATION_DATA_BODY, $data);
        $this->setAttribute(self::IS_INITIAL_REG_DATA_SENT, $published);
        $this->setAttribute(self::INSTALLER_STARTED_EVENT_STORED, $installerStartedEventStored);
        $this->setAttribute(self::INSTALLER_STARTED_AT, new DateTime());
    }

    /**
     * @return array|null
     */
    public function getInitialRegistrationData(): ?array
    {
        if ($this->hasAttribute(self::INITIAL_REGISTRATION_DATA_BODY)) {
            return [
                self::INITIAL_REGISTRATION_DATA_BODY => $this->getAttribute(self::INITIAL_REGISTRATION_DATA_BODY),
                self::IS_INITIAL_REG_DATA_SENT => $this->getAttribute(self::IS_INITIAL_REG_DATA_SENT),
                self::INSTALLER_STARTED_EVENT_STORED => $this->getAttribute(self::INSTALLER_STARTED_EVENT_STORED),
                self::INSTALLER_STARTED_AT => $this->getAttribute(self::INSTALLER_STARTED_AT),
            ];
        }
        return null;
    }

    /**
     * @param bool $completed
     */
    public function setMigrationCompleted(bool $completed): void
    {
        $this->getSession()->set(self::MIGRATION_COMPLETED, $completed);
    }

    /**
     * @return bool|null
     */
    public function isMigrationCompleted(): ?bool
    {
        return $this->getSession()->get(self::MIGRATION_COMPLETED);
    }

    public function clearMigrationCompleted(): void
    {
        $this->getSession()->remove(self::MIGRATION_COMPLETED);
    }

    public function clean(): void
    {
        $currentScreen = $this->getCurrentScreen();
        $isUpgrader = $this->isUpgrader();
        $this->getSession()->invalidate();
        $this->setCurrentScreen($currentScreen, $isUpgrader);
    }
}
