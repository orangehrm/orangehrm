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

namespace OrangeHRM\DevTools\Command;

use InvalidArgumentException;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\DevTools\Command\Util\EchoSqlLogger;
use OrangeHRM\Entity\ApiPermission;
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\Module;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddDataGroupCommand extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'add-data-group';

    public const ARGUMENT_DATA_GROUP_NAME = 'name';
    public const ARGUMENT_DATA_GROUP_DESCRIPTION = 'description';
    public const OPTION_SIMULATE_INSERT = 'simulate';
    public const OPTION_DATA_GROUP_CAN_READ = 'can-read';
    public const OPTION_DATA_GROUP_CAN_CREATE = 'can-create';
    public const OPTION_DATA_GROUP_CAN_UPDATE = 'can-update';
    public const OPTION_DATA_GROUP_CAN_DELETE = 'can-delete';
    public const OPTION_DATA_GROUP_IS_API = 'is-api';
    public const OPTION_API_PERMISSION_MODULE = 'module';
    public const OPTION_API_PERMISSION_API_NAME = 'api-name';

    private SymfonyStyle $io;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Add data group')
            ->addArgument(self::ARGUMENT_DATA_GROUP_NAME, InputArgument::OPTIONAL)
            ->addArgument(self::ARGUMENT_DATA_GROUP_DESCRIPTION, InputArgument::OPTIONAL)
            ->addOption(self::OPTION_DATA_GROUP_CAN_READ, 'R', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_CAN_CREATE, 'C', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_CAN_UPDATE, 'U', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_CAN_DELETE, 'D', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_IS_API, null, InputOption::VALUE_NONE)
            ->addOption(self::OPTION_API_PERMISSION_API_NAME, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::OPTION_API_PERMISSION_MODULE, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::OPTION_SIMULATE_INSERT, 's', InputOption::VALUE_NONE, 'Simulate data insertion');
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @inheritDoc
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dataGroup = $this->io->ask(
            'Enter data group name (e.g. <fg=yellow>job_titles, apiv2_job_titles</>)',
            null,
            function (string $value = null) {
                if (null === $value || '' === $value) {
                    throw new InvalidArgumentException('This value cannot be blank.');
                }
                $dataGroup = $this->getRepository(DataGroup::class)->findOneBy(['name' => $value]);
                if ($dataGroup) {
                    throw new InvalidArgumentException("`$value` already in use.");
                }
                return $value;
            }
        );
        $input->setArgument(self::ARGUMENT_DATA_GROUP_NAME, $dataGroup);

        $description = $this->io->ask(
            'Enter data group description (e.g. <fg=yellow>Admin - Job Titles, API-v2 Admin - Job Titles</>)',
            null,
            [$this, 'notBlankValidation']
        );
        $input->setArgument(self::ARGUMENT_DATA_GROUP_DESCRIPTION, $description);

        $canRead = $this->io->confirm('Can read', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_READ, $canRead);

        $canCreate = $this->io->confirm('Can create', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_CREATE, $canCreate);

        $canUpdate = $this->io->confirm('Can update', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_UPDATE, $canUpdate);

        $canDelete = $this->io->confirm('Can delete', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_DELETE, $canDelete);

        $isApi = $this->io->confirm('Is this data group for API', false);
        $input->setOption(self::OPTION_DATA_GROUP_IS_API, $isApi);

        if ($isApi) {
            $apiClassName = $this->io->ask(
                'Enter API full qualified class name (e.g. <fg=yellow>OrangeHRM\Admin\Api\JobTitleAPI</>)',
                null,
                function (string $value = null) {
                    if (!class_exists($value)) {
                        throw new InvalidArgumentException('Invalid class name');
                    }

                    return $value;
                }
            );
            $input->setOption(self::OPTION_API_PERMISSION_API_NAME, $apiClassName);

            $module = $this->io->askQuestion($this->getModuleAutocompleteQuestion());
            $input->setOption(self::OPTION_API_PERMISSION_MODULE, $module);
        }
    }

    /**
     * @return Question
     */
    private function getModuleAutocompleteQuestion(): Question
    {
        $modules = $this->getRepository(Module::class)->findAll();
        $modules = array_map(
            function (Module $module) {
                return $module->getName();
            },
            $modules
        );

        $question = new Question('Enter module name (e.g. <fg=yellow>' . implode(', ', $modules) . '</>)');
        $question->setValidator([$this, 'notBlankValidation']);

        $question->setAutocompleterValues($modules);

        return $question;
    }

    /**
     * @param string|null $value
     * @return string
     */
    public function notBlankValidation(string $value = null): string
    {
        if (null === $value || '' === $value) {
            throw new InvalidArgumentException('This value cannot be blank.');
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $simulateInsert = $input->getOption(self::OPTION_SIMULATE_INSERT);
        $dataGroupName = $input->getArgument(self::ARGUMENT_DATA_GROUP_NAME);
        $description = $input->getArgument(self::ARGUMENT_DATA_GROUP_DESCRIPTION);

        $canRead = $input->getOption(self::OPTION_DATA_GROUP_CAN_READ);
        $canCreate = $input->getOption(self::OPTION_DATA_GROUP_CAN_CREATE);
        $canUpdate = $input->getOption(self::OPTION_DATA_GROUP_CAN_UPDATE);
        $canDelete = $input->getOption(self::OPTION_DATA_GROUP_CAN_DELETE);

        $dataGroup = new DataGroup();
        $dataGroup->setName(trim($dataGroupName));
        $dataGroup->setDescription(trim($description));
        $dataGroup->setCanRead($canRead);
        $dataGroup->setCanCreate($canCreate);
        $dataGroup->setCanUpdate($canUpdate);
        $dataGroup->setCanDelete($canDelete);

        $this->getEntityManager()->getConfiguration()->setSQLLogger(new EchoSqlLogger());
        if (!$simulateInsert) {
            $this->persist($dataGroup);
        }

        $isApi = $input->getOption(self::OPTION_DATA_GROUP_IS_API);
        if (!$isApi) {
            $this->printDataGroupTable($dataGroup, $simulateInsert);
            $this->printDataGroupSQL($dataGroup);
            return Command::SUCCESS;
        }

        $moduleName = $input->getOption(self::OPTION_API_PERMISSION_MODULE);
        $apiClassName = $input->getOption(self::OPTION_API_PERMISSION_API_NAME);

        $apiPermission = new ApiPermission();
        $apiPermission->setDataGroup($dataGroup);
        $module = $this->getRepository(Module::class)->findOneBy(['name' => $moduleName]);
        $apiPermission->setModule($module);
        $apiPermission->setApiName($apiClassName);

        if (!$simulateInsert) {
            $this->persist($apiPermission);
        }

        $this->printDataGroupTable($dataGroup, $simulateInsert);
        $this->printApiPermissionTable($apiPermission, $simulateInsert);

        $this->printDataGroupSQL($dataGroup);
        $this->printApiPermissionSQL($apiPermission);

        return Command::SUCCESS;
    }

    /**
     * @param DataGroup $dataGroup
     * @param bool $simulateInsert
     */
    private function printDataGroupTable(DataGroup $dataGroup, bool $simulateInsert): void
    {
        $this->io->table(
            ['Name', 'Description', 'Read', 'Create', 'Update', 'Delete'],
            [
                [
                    $dataGroup->getName() . ($simulateInsert ? "" : " (" . $dataGroup->getId() . ")"),
                    $dataGroup->getDescription(),
                    (int)$dataGroup->canRead(),
                    (int)$dataGroup->canCreate(),
                    (int)$dataGroup->canUpdate(),
                    (int)$dataGroup->canDelete(),
                ]
            ]
        );
    }

    /**
     * @param DataGroup $dataGroup
     */
    private function printDataGroupSQL(DataGroup $dataGroup): void
    {
        $this->io->title('Sample SQL queries');
        $name = $this->getEntityManager()->getConnection()->quote($dataGroup->getName());
        $description = $this->getEntityManager()->getConnection()->quote($dataGroup->getDescription());
        $canRead = (int)$dataGroup->canRead();
        $canCreate = (int)$dataGroup->canCreate();
        $canUpdate = (int)$dataGroup->canUpdate();
        $canDelete = (int)$dataGroup->canDelete();
        $this->printBlock(
            "INSERT INTO ohrm_data_group (`name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES ($name, $description, $canRead, $canCreate, $canUpdate, $canDelete);"
        );
    }

    /**
     * @param ApiPermission $apiPermission
     * @param bool $simulateInsert
     */
    private function printApiPermissionTable(ApiPermission $apiPermission, bool $simulateInsert): void
    {
        $this->io->table(
            ['Id', 'Module', 'Data Group', 'Api Name'],
            [
                [
                    $simulateInsert ? "NULL" : $apiPermission->getId(),
                    $apiPermission->getModule()->getName() . " (" . $apiPermission->getModule()->getId() . ")",
                    $apiPermission->getDataGroup()->getName() .
                    ($simulateInsert ? "" : " (" . $apiPermission->getDataGroup()->getId() . ")"),
                    $apiPermission->getApiName(),
                ]
            ]
        );
    }

    /**
     * @param ApiPermission $apiPermission
     */
    private function printApiPermissionSQL(ApiPermission $apiPermission): void
    {
        $module = $apiPermission->getModule()->getName();
        $dataGroup = $apiPermission->getDataGroup()->getName();
        $apiName = $this->getEntityManager()->getConnection()->quote($apiPermission->getApiName());
        $this->printBlock(
            "SET @{$module}_module_id := (SELECT `id` FROM ohrm_module WHERE name = '$module' LIMIT 1);"
        );
        $this->printBlock(
            "SET @{$dataGroup}_data_group_id := (SELECT `id` FROM ohrm_data_group WHERE name = '$dataGroup' LIMIT 1);"
        );
        $this->printBlock(
            "INSERT INTO ohrm_api_permission (`api_name`, `module_id`, `data_group_id`) VALUES ($apiName, @{$module}_module_id, @{$dataGroup}_data_group_id);"
        );
    }

    /**
     * @param string $message
     */
    private function printBlock(string $message): void
    {
        $this->io->block($message, null, 'fg=yellow');
    }
}
