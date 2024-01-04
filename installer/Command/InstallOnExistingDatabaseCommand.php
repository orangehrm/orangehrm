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

namespace OrangeHRM\Installer\Command;

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\StateContainer;

class InstallOnExistingDatabaseCommand extends InstallOnNewDatabaseCommand
{
    use InstallerCommandHelperTrait;

    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'install:on-existing-database';
    }

    protected function databaseInformation(): void
    {
        dbInfo:
        $this->getIO()->title('Database Configuration');
        $this->getIO()->block('Please enter your database configuration information below.');
        $dbHost = $this->getRequiredField('Database Host Name');
        $dbPort = $this->getIO()->ask(
            'Database Host Port',
            3306,
            fn (?string $value) => $this->databasePortValidator($value)
        );
        $dbName = $this->getRequiredField('Database Name'); // not validated because existing database
        $dbUser = $this->getRequiredField('OrangeHRM Database Username');
        $dbPassword = $this->getIO()->askHidden('OrangeHRM Database User Password <comment>(hidden)</comment>');
        $enableDataEncryption = $this->getIO()->confirm('Enable Data Encryption', false);

        StateContainer::getInstance()->storeDbInfo(
            $dbHost,
            $dbPort,
            new UserCredential($dbUser, $dbPassword),
            $dbName,
            null,
            $enableDataEncryption
        );
        StateContainer::getInstance()->setDbType(AppSetupUtility::INSTALLATION_DB_TYPE_EXISTING);

        $connection = $this->getAppSetupUtility()->connectToDatabase();
        if ($connection->hasError()) {
            $this->getIO()->error($connection->getErrorMessage());
            StateContainer::getInstance()->clearDbInfo();
            goto dbInfo;
        }
        if (!$this->getAppSetupUtility()->isExistingDatabaseEmpty()) {
            $this->getIO()->error('Provided Database Not Empty');
            StateContainer::getInstance()->clearDbInfo();
            goto dbInfo;
        }
    }
}
