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

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ApiPermission;
use OrangeHRM\Entity\DataGroupPermission;
use PhpOffice\PhpSpreadsheet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportApiPermissionCommand extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'export-api-permissions';

    private SymfonyStyle $io;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Export API permissions');
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ApiPermission[] $apiPermissions */
        $apiPermissions = $this->getRepository(ApiPermission::class)->findAll();

        $spreadsheet = new PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;
        foreach ($apiPermissions as $apiPermission) {
            $dataGroupId = $apiPermission->getDataGroup()->getId();
            /** @var DataGroupPermission[] $dataGroupPermissions */
            $dataGroupPermissions = $this->getRepository(DataGroupPermission::class)
                ->findBy(['dataGroup' => $dataGroupId]);

            $dataGroupPermissionsCount = count($dataGroupPermissions);

            $mainColIndexes = [1, 2, 3, 4];
            foreach ($mainColIndexes as $i) {
                $sheet->mergeCellsByColumnAndRow($i, $rowIndex, $i, $rowIndex + $dataGroupPermissionsCount - 1);
            }
            $sheet->setCellValueByColumnAndRow(1, $rowIndex, $apiPermission->getId());
            $sheet->setCellValueByColumnAndRow(2, $rowIndex, $apiPermission->getApiName());
            $sheet->setCellValueByColumnAndRow(3, $rowIndex, $apiPermission->getModule()->getName());
            $sheet->setCellValueByColumnAndRow(4, $rowIndex, $apiPermission->getDataGroup()->getName());

            foreach ($dataGroupPermissions as $userRoleRowIndex => $dataGroupPermission) {
                $sheet->setCellValueByColumnAndRow(
                    5,
                    $rowIndex + $userRoleRowIndex,
                    $dataGroupPermission->getUserRole()->getName()
                );
                $sheet->setCellValueByColumnAndRow(6, $rowIndex + $userRoleRowIndex, $dataGroupPermission->canRead());
                $sheet->setCellValueByColumnAndRow(7, $rowIndex + $userRoleRowIndex, $dataGroupPermission->canCreate());
                $sheet->setCellValueByColumnAndRow(8, $rowIndex + $userRoleRowIndex, $dataGroupPermission->canUpdate());
                $sheet->setCellValueByColumnAndRow(9, $rowIndex + $userRoleRowIndex, $dataGroupPermission->canDelete());
                $sheet->setCellValueByColumnAndRow(10, $rowIndex + $userRoleRowIndex, $dataGroupPermission->isSelf());
            }

            $rowIndex = $rowIndex + $dataGroupPermissionsCount;
        }

        $writer = new PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('api_permission.xlsx');

        return Command::SUCCESS;
    }
}
