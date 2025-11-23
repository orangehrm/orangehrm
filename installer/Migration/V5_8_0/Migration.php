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

namespace OrangeHRM\Installer\Migration\V5_8_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\LangStringHelper;
use PDO;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $payGradeCurrencyTableDetails = $this->getSchemaManager()->introspectTable('ohrm_pay_grade_currency');
        $payGradeCurrencyCurrencyIdColumn = $payGradeCurrencyTableDetails->getColumn('currency_id');

        $basicSalaryTableDetails = $this->getSchemaManager()->introspectTable('hs_hr_emp_basicsalary');
        $basicSalaryCurrencyIdColumn = $basicSalaryTableDetails->getColumn('currency_id');

        // Check whether this is already corrected in v5_5_0 migration
        if ($basicSalaryCurrencyIdColumn->getLength() == 6 && $payGradeCurrencyCurrencyIdColumn->getLength() == 6) {
            $this->correctingCurrencyIdColumnInconsistencies();
        }

        $groups = ['auth', 'pim'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->updateLangStringVersion($this->getVersion());
    }

    /**
     * Error in foreign key constraint of table ohrm_claim_request: Alter table ohrm_claim_request with foreign key fk_currency_id constraint failed.
     * Field type or character set for column 'currency_id' does not match referenced column 'currency_id'.
     */
    public function correctingCurrencyIdColumnInconsistencies()
    {
        $this->disableForeignKeyChecks();
        $foreignKeyArray = [];
        $foreignKeyArray = array_merge($foreignKeyArray, $this->getConflictingForeignKeys('ohrm_pay_grade_currency'));
        $foreignKeyArray = array_merge($foreignKeyArray, $this->getConflictingForeignKeys('hs_hr_emp_basicsalary'));

        $this->removeConflictingForeignKeys($foreignKeyArray);
        $this->removeConflictingForeignKeys($this->getConflictingForeignKeys('ohrm_claim_request'));

        $this->getConnection()->executeStatement(
            'ALTER TABLE hs_hr_currency_type MODIFY COLUMN currency_id VARCHAR(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );

        $this->getSchemaHelper()->changeColumn('ohrm_pay_grade_currency', 'currency_id', [
            'Type' => Type::getType(Types::STRING),
            'Notnull' => true,
            'Length' => 3,
            'CustomSchemaOptions' => ['collation' => 'utf8mb3_general_ci', 'charset' => 'utf8mb3']
        ]);

        $this->getSchemaHelper()->changeColumn('hs_hr_emp_basicsalary', 'currency_id', [
            'Type' => Type::getType(Types::STRING),
            'Notnull' => true,
            'Length' => 3,
            'CustomSchemaOptions' => ['collation' => 'utf8mb3_general_ci', 'charset' => 'utf8mb3']
        ]);

        $this->getSchemaHelper()->changeColumn('ohrm_claim_request', 'currency_id', [
            'Type' => Type::getType(Types::STRING),
            'Notnull' => true,
            'Length' => 3,
            'CustomSchemaOptions' => ['collation' => 'utf8mb3_general_ci', 'charset' => 'utf8mb3']
        ]);

        $this->recreateRemovedForeignKeys($foreignKeyArray);
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['currency_id'],
            'hs_hr_currency_type',
            ['currency_id'],
            'fk_currency_id',
            ['onDelete' => 'RESTRICT', 'onUpdate' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint);

        $this->enableForeignKeyChecks();
    }

    /**
     * @param string $childTable
     * @return array
     */
    private function getConflictingForeignKeys(string $childTable): array
    {
        $foreignKeyArray = [];
        $tableDetails = $this->getSchemaManager()->introspectTable($childTable);
        $foreignKeys = $tableDetails->getForeignKeys();
        foreach ($foreignKeys as $constraintName => $constraint) {
            if ($constraint->getForeignTableName() == 'hs_hr_currency_type') {
                $foreignKeyArray[$constraintName] = ['constraint' => $constraint, 'childTable' => $childTable];
            }
        }
        return $foreignKeyArray;
    }

    /**
     * @param array $conflictingConstraints
     */
    private function removeConflictingForeignKeys(array $conflictingConstraints): void
    {
        foreach ($conflictingConstraints as $constraintName => $conflictingConstraint) {
            $this->getSchemaHelper()->dropForeignKeys($conflictingConstraint['childTable'], [$constraintName]);
        }
    }

    /**
     * @param array $conflictingConstraints
     */
    private function recreateRemovedForeignKeys(array $conflictingConstraints): void
    {
        foreach ($conflictingConstraints as $conflictingConstraint) {
            $this->getSchemaHelper()->addForeignKey(
                $conflictingConstraint['childTable'],
                $conflictingConstraint['constraint']
            );
        }
    }

    /**
     * @return PDO
     */
    private function getNativeConnection(): PDO
    {
        return $this->getConnection()->getNativeConnection();
    }

    /**
     * @return void
     */
    public function disableForeignKeyChecks(): void
    {
        $pdo = $this->getNativeConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
    }

    /**
     * @return void
     */
    public function enableForeignKeyChecks(): void
    {
        $pdo = $this->getNativeConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.8.0';
    }

    /**
     * @return LangStringHelper
     */
    private function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }

    /**
     * @param string $version
     */
    private function updateLangStringVersion(string $version): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string', 'lang_string')
            ->set('lang_string.version', ':version')
            ->setParameter('version', $version);
        $qb->andWhere($qb->expr()->isNull('lang_string.version'))
            ->executeStatement();
    }
}
