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
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\UserRole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddRolePermissionCommand extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'add-role-permission';

    public const ARGUMENT_DATA_GROUP_NAME = 'name';
    public const ARGUMENT_DATA_GROUP_USER_ROLE = 'role';
    public const OPTION_SIMULATE_INSERT = 'simulate';
    public const OPTION_DATA_GROUP_CAN_READ = 'can-read';
    public const OPTION_DATA_GROUP_CAN_CREATE = 'can-create';
    public const OPTION_DATA_GROUP_CAN_UPDATE = 'can-update';
    public const OPTION_DATA_GROUP_CAN_DELETE = 'can-delete';
    public const OPTION_DATA_GROUP_IS_SELF = 'is-self';

    private SymfonyStyle $io;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Add user role permission to a data group')
            ->addArgument(self::ARGUMENT_DATA_GROUP_NAME, InputArgument::OPTIONAL)
            ->addArgument(self::ARGUMENT_DATA_GROUP_USER_ROLE, InputArgument::OPTIONAL)
            ->addOption(self::OPTION_DATA_GROUP_CAN_READ, 'R', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_CAN_CREATE, 'C', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_CAN_UPDATE, 'U', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_CAN_DELETE, 'D', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_DATA_GROUP_IS_SELF, 'S', InputOption::VALUE_NONE)
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
        $dataGroupName = $this->io->askQuestion($this->getDataGroupAutocompleteQuestion());
        $input->setArgument(self::ARGUMENT_DATA_GROUP_NAME, $dataGroupName);
        $dataGroup = $this->getRepository(DataGroup::class)->findOneBy(['name' => $dataGroupName]);
        $this->printDataGroupPermissionTable($dataGroup);

        $userRole = $this->io->askQuestion($this->getUserRoleAutocompleteQuestion());
        $input->setArgument(self::ARGUMENT_DATA_GROUP_USER_ROLE, $userRole);

        $canRead = $this->io->confirm('Can read', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_READ, $canRead);

        $canCreate = $this->io->confirm('Can create', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_CREATE, $canCreate);

        $canUpdate = $this->io->confirm('Can update', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_UPDATE, $canUpdate);

        $canDelete = $this->io->confirm('Can delete', false);
        $input->setOption(self::OPTION_DATA_GROUP_CAN_DELETE, $canDelete);

        $isApi = $this->io->confirm('Is self', false);
        $input->setOption(self::OPTION_DATA_GROUP_IS_SELF, $isApi);
    }

    /**
     * @return Question
     */
    private function getDataGroupAutocompleteQuestion(): Question
    {
        $dataGroups = $this->getRepository(DataGroup::class)->findAll();
        $dataGroups = array_map(
            function (DataGroup $dataGroup) {
                return $dataGroup->getName();
            },
            $dataGroups
        );

        $question = new Question('Enter data group name (e.g. <fg=yellow>job_titles, apiv2_job_titles</>)');
        $question->setValidator(
            function (string $value = null) use ($dataGroups): string {
                if (!in_array($value, $dataGroups)) {
                    throw new InvalidArgumentException('Invalid option');
                }
                return $value;
            }
        );
        $question->setAutocompleterValues($dataGroups);
        return $question;
    }

    /**
     * @return Question
     */
    private function getUserRoleAutocompleteQuestion(): Question
    {
        $userRoles = $this->getRepository(UserRole::class)->findAll();
        $userRoles = array_map(
            function (UserRole $userRole) {
                return $userRole->getName();
            },
            $userRoles
        );

        $question = new Question('Enter user role name (e.g. <fg=yellow>' . implode(', ', $userRoles) . '</>)');
        $question->setValidator(
            function (string $value = null) use ($userRoles): string {
                if (!in_array($value, $userRoles)) {
                    throw new InvalidArgumentException('Invalid option');
                }
                return $value;
            }
        );
        $question->setAutocompleterValues($userRoles);
        return $question;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dataGroupName = $input->getArgument(self::ARGUMENT_DATA_GROUP_NAME);
        $userRoleName = $input->getArgument(self::ARGUMENT_DATA_GROUP_USER_ROLE);

        $canRead = $input->getOption(self::OPTION_DATA_GROUP_CAN_READ);
        $canCreate = $input->getOption(self::OPTION_DATA_GROUP_CAN_CREATE);
        $canUpdate = $input->getOption(self::OPTION_DATA_GROUP_CAN_UPDATE);
        $canDelete = $input->getOption(self::OPTION_DATA_GROUP_CAN_DELETE);
        $isSelf = $input->getOption(self::OPTION_DATA_GROUP_IS_SELF);

        $this->getEntityManager()->getConfiguration()->setSQLLogger(new EchoSqlLogger());
        $dataGroup = $this->getRepository(DataGroup::class)->findOneBy(['name' => $dataGroupName]);
        $userRole = $this->getRepository(UserRole::class)->findOneBy(['name' => $userRoleName]);

        $dataGroupPermission = new DataGroupPermission();
        $dataGroupPermission->setDataGroup($dataGroup);
        $dataGroupPermission->setUserRole($userRole);
        $dataGroupPermission->setCanRead($canRead);
        $dataGroupPermission->setCanCreate($canCreate);
        $dataGroupPermission->setCanUpdate($canUpdate);
        $dataGroupPermission->setCanDelete($canDelete);
        $dataGroupPermission->setSelf($isSelf);

        $simulateInsert = $input->getOption(self::OPTION_SIMULATE_INSERT);
        if (!$simulateInsert) {
            $this->persist($dataGroupPermission);
        }

        $this->printDataGroupPermissionTable($dataGroup);
        $this->printDataGroupPermissionSQL($dataGroupPermission);
        return Command::SUCCESS;
    }

    /**
     * @param DataGroup $dataGroup
     */
    private function printDataGroupPermissionTable(DataGroup $dataGroup): void
    {
        $dataGroupPermissions = $this->getRepository(DataGroupPermission::class)
            ->findBy(['dataGroup' => $dataGroup->getId()]);

        $dataGroupPermissions = array_map(
            function (DataGroupPermission $dataGroupPermission) {
                $userRoleName = $dataGroupPermission->getUserRole()->getName() .
                    " (" . $dataGroupPermission->getUserRole()->getId() . ")";
                $dataGroupName = $dataGroupPermission->getDataGroup()->getName() .
                    " (" . $dataGroupPermission->getDataGroup()->getId() . ")";
                return [
                    $dataGroupPermission->getId(),
                    $userRoleName,
                    $dataGroupName,
                    (int)$dataGroupPermission->canRead(),
                    (int)$dataGroupPermission->canCreate(),
                    (int)$dataGroupPermission->canUpdate(),
                    (int)$dataGroupPermission->canDelete(),
                    (int)$dataGroupPermission->isSelf()
                ];
            },
            $dataGroupPermissions
        );
        $this->io->table(
            ['Id', 'User Role', 'Data Group', 'Read', 'Create', 'Update', 'Delete', 'Self'],
            $dataGroupPermissions
        );
    }

    /**
     * @param DataGroupPermission $dataGroupPermission
     */
    private function printDataGroupPermissionSQL(DataGroupPermission $dataGroupPermission): void
    {
        $this->io->title('Sample SQL queries');
        $dataGroup = $dataGroupPermission->getDataGroup()->getName();
        $userRoleName = $dataGroupPermission->getUserRole()->getName();
        $userRole = strtolower($userRoleName);
        $userRoleName = $this->getEntityManager()->getConnection()->quote($userRoleName);
        $canRead = (int)$dataGroupPermission->canRead();
        $canCreate = (int)$dataGroupPermission->canCreate();
        $canUpdate = (int)$dataGroupPermission->canUpdate();
        $canDelete = (int)$dataGroupPermission->canDelete();
        $isSelf = (int)$dataGroupPermission->isSelf();

        $this->printBlock(
            "SET @{$dataGroup}_data_group_id := (SELECT `id` FROM ohrm_data_group WHERE name = '$dataGroup' LIMIT 1);"
        );
        $this->printBlock(
            "SET @{$userRole}_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = '$userRoleName' LIMIT 1);"
        );
        $this->printBlock(
            "INSERT INTO ohrm_user_role_data_group (`can_read`, `can_create`, `can_update`, `can_delete`, `self`, `data_group_id`, `user_role_id`) VALUES ($canRead, $canCreate, $canUpdate, $canDelete, $isSelf, @{$dataGroup}_data_group_id, @{$userRole}_role_id);"
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
