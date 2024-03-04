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

use DateTimeZone;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Controller\Installer\Api\ConfigFileAPI;
use OrangeHRM\Installer\Controller\Installer\Api\InstallerDataRegistrationAPI;
use OrangeHRM\Installer\Exception\InterruptProcessException;
use OrangeHRM\Installer\Exception\InvalidArgumentException;
use OrangeHRM\Installer\Framework\InstallerCommand;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\Connection;
use OrangeHRM\Installer\Util\DatabaseUserPermissionEvaluator;
use OrangeHRM\Installer\Util\InstanceCreationHelper;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Installer\Util\SystemCheck;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Throwable;

class InstallOnNewDatabaseCommand extends InstallerCommand
{
    use InstallerCommandHelperTrait;

    public const REQUIRED_TAG = '<comment>(required)</comment>';
    public const REQUIRED_WARNING = 'This field cannot be empty';

    public const STEP_1 = 'Database creation';
    public const STEP_2 = 'Checking database permissions';
    public const STEP_3 = 'Applying database changes';
    public const STEP_4 = 'Instance and Admin user creation';
    public const STEP_5 = 'Create OrangeHRM database user';
    public const STEP_6 = 'Creating configuration files';

    private InputInterface $input;
    private OutputInterface $output;
    private AppSetupUtility $appSetupUtility;

    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'install:on-new-database';
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->isInteractive()) {
            $this->getIO()->error('Not supported non interactive mode.');
            return self::INVALID;
        }
        if (Config::isInstalled()) {
            $this->getIO()->error('This system already installed.');
            return self::FAILURE;
        }
        $this->input = $input;
        $this->output = $output;
        try {
            return $this->executeCommand($input, $output);
        } catch (InterruptProcessException $e) {
            return self::FAILURE;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function executeCommand(InputInterface $input, OutputInterface $output): int
    {
        $this->licenseAcceptance();
        $this->databaseInformation();
        $this->systemCheck();
        $this->instanceCreation();
        $this->adminUserCreation();
        $this->installation();

        return self::SUCCESS;
    }

    /**
     * @return InputInterface
     */
    protected function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return AppSetupUtility
     */
    public function getAppSetupUtility(): AppSetupUtility
    {
        return $this->appSetupUtility ??= new AppSetupUtility();
    }

    protected function licenseAcceptance(): void
    {
        $this->getIO()->title('License Acceptance');
        $this->getIO()->block('Please review the license terms before installing OrangeHRM Starter.');
        $this->getIO()->block('You can find the license file ("LICENSE") at the root folder of the code.');
        if ($this->getIO()->confirm('I accept the terms in the License Agreement') !== true) {
            throw new InterruptProcessException();
        }
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
        $dbName = $this->getRequiredField('Database Name', function ($value) {
            $value = $this->validateStrLength($value, 64);
            return $this->alphanumericValidator($value, 'Database name should not contain special characters');
        });

        $this->getIO()->writeln(
            "<comment>Privileged Database User:</comment>\nShould have the rights to create databases, create tables, insert data into table, alter table structure and to create database users."
        );
        $this->getIO()->writeln(
            "<comment>OrangeHRM Database User:</comment>\nShould have the rights to insert data into table, update data in a table, delete data in a table."
        );

        $dbUser = $this->getRequiredField('Privileged Database Username');
        $dbPassword = $this->getIO()->askHidden('Privileged Database User Password <comment>(hidden)</comment>');
        $useSameDbUser = $this->getIO()->confirm(
            'Use the same `Privileged Database User` as `OrangeHRM Database User`',
            false
        );

        $ohrmDbUser = $dbUser;
        $ohrmDbPassword = $dbPassword;
        if ($useSameDbUser === false) {
            $ohrmDbUser = $this->getRequiredField('OrangeHRM Database Username');
            $ohrmDbPassword = $this->getIO()->askHidden(
                'OrangeHRM Database User Password <comment>(hidden)</comment>'
            );
        }
        $enableDataEncryption = $this->getIO()->confirm('Enable Data Encryption', false);

        StateContainer::getInstance()->storeDbInfo(
            $dbHost,
            $dbPort,
            new UserCredential($dbUser, $dbPassword),
            $dbName,
            new UserCredential($ohrmDbUser, $ohrmDbPassword),
            $enableDataEncryption
        );
        StateContainer::getInstance()->setDbType(AppSetupUtility::INSTALLATION_DB_TYPE_NEW);

        $connection = $this->getAppSetupUtility()->connectToDatabaseServer();
        if ($connection->hasError()) {
            $this->getIO()->error($connection->getErrorMessage());
            StateContainer::getInstance()->clearDbInfo();
            goto dbInfo;
        }
        if ($this->getAppSetupUtility()->isDatabaseExist($dbName)) {
            $this->getIO()->error('Database Already Exist');
            StateContainer::getInstance()->clearDbInfo();
            goto dbInfo;
        }
        if (!$useSameDbUser && $this->getAppSetupUtility()->isDatabaseUserExist($ohrmDbUser)) {
            $this->getIO()->error(
                "Database User `$ohrmDbUser` Already Exist. Please Use Another Username for `OrangeHRM Database Username`."
            );
            StateContainer::getInstance()->clearDbInfo();
            goto dbInfo;
        }
    }

    protected function systemCheck(): void
    {
        $systemCheck = new SystemCheck();
        $results = $systemCheck->getSystemCheckResults(true);
        $dbInfo = StateContainer::getInstance()->getDbInfo();
        if (isset($dbInfo[StateContainer::ENABLE_DATA_ENCRYPTION])
            && $dbInfo[StateContainer::ENABLE_DATA_ENCRYPTION] == true) {
            $results[1]['checks'][] = [
                'label' => 'Write Permissions for “lib/confs/cryptokeys”',
                'value' => $systemCheck->isWritableCryptoKeyDir()
            ];
        }
        $this->drawSystemCheckTable($results);
        if ($systemCheck->isInterruptContinue()) {
            $this->getIO()->error('System check failed');
            $systemCheckAcceptRisk = $this->getIO()->confirm('Do you want to accept the risk and continue?', false);

            if ($systemCheckAcceptRisk !== true) {
                throw new InterruptProcessException();
            }

            $this->getIO()->warning('Accepted the risk, so continue the upgrader.');
        }
    }

    protected function instanceCreation(): void
    {
        $this->getIO()->title('Instance Creation');
        $this->getIO()->block(
            'Fill in your organization details here. Details entered in this section will be captured to create your OrangeHRM Instance'
        );
        $organizationName = $this->getRequiredField(
            'Organization Name',
            fn ($value) => $this->validateStrLength($value, 100)
        );

        $countries = array_combine(
            array_column(InstanceCreationHelper::COUNTRIES, 'id'),
            array_column(InstanceCreationHelper::COUNTRIES, 'label')
        );
        asort($countries);
        $countries = array_map(fn ($country) => strtolower($country), $countries);
        $countries = array_flip($countries);
        $countryQuestion = new Question('Country ' . self::REQUIRED_TAG);
        $countryQuestion->setValidator(
            static function (?string $country) use ($countries): string {
                if (!isset($countries[$country])) {
                    throw new InvalidArgumentException('Invalid option');
                }
                return $country;
            }
        );

        $this->getIO()->block("Tip:\n* Use lowercase letters\n* Use `Tab` to autocomplete");
        $countryQuestion->setAutocompleterValues(array_keys($countries));
        $country = $this->getIO()->askQuestion($countryQuestion);
        $countryCode = $countries[$country];

        $this->getIO()->block("Tip:\n* Use correct letter case\n* Use `Tab` to autocomplete");
        $languages = array_combine(
            array_column(InstanceCreationHelper::LANGUAGES, 'id'),
            array_column(InstanceCreationHelper::LANGUAGES, 'label')
        );
        $languageQuestion = new ChoiceQuestion('Language', $languages);
        $languageQuestion->setValidator(
            static function (?string $language) use ($languages): ?string {
                if ($language == null || trim($language) === '') {
                    return $language;
                }
                if (!(isset($languages[$language]) || in_array($language, array_values($languages)))) {
                    throw new InvalidArgumentException('Invalid option');
                }
                return $language;
            }
        );
        $langCode = $this->getIO()->askQuestion($languageQuestion);

        $timeZoneGroups = array_combine(
            array_column(InstanceCreationHelper::TIME_ZONE_GROUPS, 'label'),
            array_column(InstanceCreationHelper::TIME_ZONE_GROUPS, 'id')
        );
        $timezoneGroupQuestion = new ChoiceQuestion('Timezone Group', array_keys($timeZoneGroups));
        $timezoneGroupQuestion->setValidator(
            static function (?string $timezoneGroup) use ($timeZoneGroups): ?string {
                if ($timezoneGroup == null || trim($timezoneGroup) === '') {
                    return $timezoneGroup;
                }
                if (!isset($timeZoneGroups[$timezoneGroup])) {
                    throw new InvalidArgumentException('Invalid option');
                }
                return $timezoneGroup;
            }
        );
        $timeZoneGroupName = $this->getIO()->askQuestion($timezoneGroupQuestion);

        $timezones = DateTimeZone::listIdentifiers($timeZoneGroups[$timeZoneGroupName] ?? DateTimeZone::ALL);
        $timezoneQuestion = new ChoiceQuestion('Timezone', $timezones);
        $timezoneQuestion->setValidator(
            static function (?string $timezone) use ($timezones): ?string {
                if ($timezone == null || trim($timezone) === '') {
                    return $timezone;
                }
                if (!in_array($timezone, $timezones)) {
                    throw new InvalidArgumentException('Invalid option');
                }
                return $timezone;
            }
        );
        $timezone = $this->getIO()->askQuestion($timezoneQuestion);

        StateContainer::getInstance()->storeInstanceData($organizationName, $countryCode, $langCode, $timezone);
    }

    protected function adminUserCreation(): void
    {
        $firstName = $this->getRequiredField('Employee First Name', fn ($value) => $this->validateStrLength($value, 30));
        $lastName = $this->getRequiredField('Employee Last Name', fn ($value) => $this->validateStrLength($value, 30));

        $email = $this->getRequiredField('Email', function ($value) {
            $value = $this->validateStrLength($value, 50);
            return $this->emailValidator($value, 'Expected format: admin@example.com');
        });
        $contact = $this->getIO()->ask('Contact Number', null, function ($value) {
            if ($value === null) {
                return null;
            }
            $value = $this->validateStrLength($value, 25);
            return $this->phoneNumberValidator($value, 'Allows numbers and only + - / ( )');
        });
        $username = $this->getRequiredField('Admin Username', fn ($value) => $this->validateStrLength($value, 40));
        $password = $this->getIO()->askHidden('Password <comment>(hidden)</comment>', function ($value) {
            $value = $this->requiredValidator($value);
            return $this->validatePassword($value);
        });
        $this->getIO()->askHidden(
            'Confirm Password <comment>(hidden)</comment>',
            function ($value) use ($password) {
                $value = $this->requiredValidator($value);
                $value = $this->validateStrLength($value, 64);
                if ($value !== $password) {
                    throw new InvalidArgumentException('Passwords do not match');
                }
                return $value;
            }
        );

        StateContainer::getInstance()->storeAdminUserData(
            $firstName,
            $lastName,
            $email,
            new UserCredential($username, $password),
            $contact
        );

        $regConsent = $this->getIO()->confirm(
            'Register your system with OrangeHRM. By registering, You will be eligible for free support via emails, receive security alerts and news letters from OrangeHRM.',
            true
        );
        StateContainer::getInstance()->storeRegConsent($regConsent);

        $this->getIO()->note(
            'Users who seek access to their data, or who seek to correct, amend, or delete the given information should direct their requests to data@orangehrm.com'
        );
    }

    protected function installation(): void
    {
        $continue = $this->getIO()->confirm('Do you want to start the installer?', true);
        if ($continue !== true) {
            $this->getIO()->info('Aborted');
            throw new InterruptProcessException();
        }

        $step1 = $this->startSection($this->getOutput(), self::STEP_1);
        $step2 = $this->startSection($this->getOutput(), self::STEP_2);
        $step3 = $this->startSection($this->getOutput(), self::STEP_3);
        $step4 = $this->startSection($this->getOutput(), self::STEP_4);
        $step5 = $this->startSection($this->getOutput(), self::STEP_5);
        $step6 = $this->startSection($this->getOutput(), self::STEP_6, "\n");

        $this->startStep($step1, self::STEP_1);
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        (new InstallerDataRegistrationAPI())->handle($request);

        $appSetupUtility = $this->getAppSetupUtility();
        $appSetupUtility->createDatabase();
        $this->completeStep($step1, self::STEP_1);

        $this->startStep($step2, self::STEP_2);
        try {
            $evaluator = new DatabaseUserPermissionEvaluator(Connection::getConnection());
            $evaluator->evalPrivilegeDatabaseUserPermission();
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            $this->getIO()->error(
                '`Checking database permissions` failed. For more details check the error log in /src/log/installer.log file'
            );
            throw new InterruptProcessException();
        }
        $this->completeStep($step2, self::STEP_2);

        $this->startStep($step3, self::STEP_3);
        $appSetupUtility->runMigrations('3.3.3', Config::PRODUCT_VERSION);
        $this->completeStep($step3, self::STEP_3);

        $this->startStep($step4, self::STEP_4);
        $appSetupUtility->insertSystemConfiguration();
        $this->completeStep($step4, self::STEP_4);

        $this->startStep($step5, self::STEP_5);
        $appSetupUtility->createDBUser();
        $this->completeStep($step5, self::STEP_5);

        $this->startStep($step6, self::STEP_6, "\n");
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        (new ConfigFileAPI())->handle($request);
        $this->completeStep($step6, self::STEP_6, "\n");
    }
}
