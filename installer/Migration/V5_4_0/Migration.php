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

namespace OrangeHRM\Installer\Migration\V5_4_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->insertI18nGroups();
        $this->getLangStringHelper()->deleteNonCustomizedLangStrings('claim');
        $this->getLangStringHelper()->insertOrUpdateLangStrings('claim');

        $groups = ['auth', 'general'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $this->updateLangStringVersion($this->getVersion());

        if (!$this->getSchemaHelper()->tableExists(['
        '])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_event')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::TEXT, ['Notnull' => true, 'Length' => 100])
                ->addColumn('description', Types::TEXT, ['Notnull' => false, 'Length' => 1000])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('status', Types::STRING, ['Notnull' => false, 'Length' => 64])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                'addedBy',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_event', $foreignKeyConstraint);
        }

        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status',
                    'display_name' => ':display_name'
                ]
            )
            ->setParameter('name', "claim")
            ->setParameter('status', 1)
            ->setParameter('display_name', 'Claim')
            ->executeQuery();

        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status',
                    'display_name' => ':display_name'
                ]
            )
            ->setParameter('name', "auth")
            ->setParameter('status', 1)
            ->setParameter('display_name', 'Auth')
            ->executeQuery();

        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_password_length', '8');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_uppercase_letters', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_lowercase_letters', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_numbers_in_password', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_special_characters', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.default_required_password_strength', 'strong');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.is_spaces_allowed', 'false');

        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->cleanClaimScreens();
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screens.yaml');
        $this->changeClaimEventTableStatusToBoolean();

        if (!$this->getSchemaHelper()->tableExists(['ohrm_expense_type'])) {
            $this->getSchemaHelper()->createTable('ohrm_expense_type')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::TEXT, ['Notnull' => true, 'Length' => 100])
                ->addColumn('description', Types::TEXT, ['Notnull' => false, 'Length' => 1000])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('status', Types::STRING, ['Notnull' => false, 'Length' => 64])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                'addedByUser',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense_type', $foreignKeyConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_request'])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_request')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('emp_number ', Types::INTEGER, ['Notnull' => false, 'Length' => 11])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false, 'Length' => 11])
                ->addColumn('reference_id', Types::TEXT, ['Notnull' => false])
                ->addColumn('event_type_id', Types::INTEGER, ['Notnull' => false, 'Length' => 11])
                ->addColumn('description', Types::TEXT, ['Notnull' => false])
                ->addColumn('currency', Types::STRING, ['Notnull' => false, 'Length' => 3])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true])
                ->addColumn('status', Types::TEXT, ['Notnull' => false])
                ->addColumn('created_date', Types::DATE_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('submitted_date', Types::DATE_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint1 = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                'requestByUser',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint1);
            $foreignKeyConstraint2 = new ForeignKeyConstraint(
                ['event_type_id'],
                'ohrm_claim_event',
                ['id'],
                'claimEventId',
                ['onDelete' => 'RESTRICT']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint2);
            $foreignKeyConstraint3 = new ForeignKeyConstraint(
                ['emp_number'],
                'hs_hr_employee',
                ['emp_number'],
                'claim_Request_Employee_Number',
                ['onDelete' => 'RESTRICT']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint3);
        }

        $this->getSchemaHelper()->createTable('ohrm_enforce_password')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('user_id', Types::INTEGER, ['Notnull' => true])
            ->addColumn('enforce_request_date', Types::DATETIME_MUTABLE, ['Notnull' => false])
            ->addColumn('reset_code', Types::STRING, ['Notnull' => true])
            ->addColumn('expired', Types::BOOLEAN, ['Notnull' => true, 'Default' => 0])
            ->setPrimaryKey(['id'])
            ->create();

        $resetCode = new Index(
            'reset_code',
            ['reset_code']
        );
        $this->getSchemaManager()->createIndex($resetCode, 'ohrm_enforce_password');

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['user_id'],
            'ohrm_user',
            ['id'],
            'enforcePasswordUser',
            ['onDelete' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_enforce_password', $foreignKeyConstraint);

        $this->changeClaimExpenseTypeTableStatusToBoolean();
        $this->modifyClaimTables();
        $this->modifyClaimRequestCurrencyToForeignKey();
        $this->modifyDefaultRequiredPasswordStrength();
        $this->modifyDefaultPasswordEnforcement();

        if (!$this->checkClaimExists()) {
            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_module_default_page')
                ->values(
                    [
                        'module_id' => ':module_id',
                        'user_role_id' => ':user_role_id',
                        'action' => ':action',
                    ]
                )
                ->setParameter('module_id', $this->getDataGroupHelper()->getModuleIdByName('claim'))
                ->setParameter('user_role_id', $this->getDataGroupHelper()->getUserRoleIdByName('Admin'))
                ->setParameter('action', 'claim/viewEvents')
                ->executeQuery();

            $viewClaimModuleScreenId = $this->getConnection()
                ->createQueryBuilder()
                ->select('id')
                ->from('ohrm_screen')
                ->where('action_url = :action_url')
                ->setParameter('action_url', 'ViewClaimModule')
                ->executeQuery()
                ->fetchOne();

            $this->insertMenuItems('Claim', $viewClaimModuleScreenId, null, 1, 1300, 1, '{"icon":"claim"}');
            $claimMenuItemId = $this->getConnection()
                ->createQueryBuilder()
                ->select('id')
                ->from('ohrm_menu_item')
                ->Where('menu_title = :menu_title')
                ->setParameter('menu_title', 'Claim')
                ->executeQuery()
                ->fetchOne();
            $this->insertMenuItems('Configuration', null, $claimMenuItemId, 2, 100, 1, null);
            $eventListScreenId = $this->getScreenId('Events');
            $claimConfigMenuItemId = $this->getParentId('Configuration', $claimMenuItemId);
            $this->insertMenuItems('Events', $eventListScreenId, $claimConfigMenuItemId, 3, 100, 1, null);
            $expenseTypeScreenId = $this->getScreenId('Expense Types');
            $this->insertMenuItems('Expense Types', $expenseTypeScreenId, $claimConfigMenuItemId, 3, 200, 1, null);
        }
    }

    private function modifyClaimTables(): void
    {
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_claim_request CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci'
        );

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_claim_event', [
            'is_deleted' => ['Type' => Type::getType(Types::BOOLEAN), 'Notnull' => true, 'Default' => 0],
            'status' => ['Type' => Type::getType(Types::BOOLEAN), 'Notnull' => false, 'Default' => null, 'CustomSchemaOptions' => ['collation' => null, 'charset' => null]],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_claim_request', [
            'reference_id' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::STRING),
            ],
            'description' => [
                'Type' => Type::getType(Types::STRING),
                'Length' => 1000
            ],
            'currency' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::STRING),
                'Length' => 3
            ],
            'created_date' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
            ],
            'submitted_date' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
            ]
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_expense_type', [
            'name' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::STRING),
            ],
            'description' => [
                'Type' => Type::getType(Types::STRING),
                'Length' => 1000
            ],
            'is_deleted' => [
                'Type' => Type::getType(Types::BOOLEAN),
            ],
            'status' => [
                'Type' => Type::getType(Types::BOOLEAN),
                'CustomSchemaOptions' => ['collation' => null, 'charset' => null]
            ]
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_claim_event', [
            'name' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::STRING),
            ],
            'description' => [
                'Length' => 1000
            ],
            'is_deleted' => [
                'Type' => Type::getType(Types::BOOLEAN),
            ],
        ]);

        $this->getSchemaHelper()->renameColumn('ohrm_claim_request', 'currency', 'currency_id');
    }

    private function modifyClaimRequestCurrencyToForeignKey(): void
    {
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['currency_id'],
            'hs_hr_currency_type',
            ['currency_id'],
            'fk_currency_id',
            ['onDelete' => 'RESTRICT', 'onUpdate' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint);
    }

    private function changeClaimExpenseTypeTableStatusToBoolean(): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_expense_type', 'expenseType')
            ->set('expenseType.status', ':status')
            ->where('expenseType.status = :currentStatus')
            ->setParameter('currentStatus', 'on')
            ->setParameter(
                'status',
                true,
                Types::BOOLEAN
            )
            ->executeStatement();

        $q = $this->createQueryBuilder();
        $q->update('ohrm_expense_type', 'expenseType')
            ->set('expenseType.status', ':status')
            ->where($q->expr()->isNull('expenseType.status'))
            ->setParameter(
                'status',
                false,
                Types::BOOLEAN
            )
            ->executeStatement();
    }

    private function changeClaimEventTableStatusToBoolean(): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_claim_event', 'claimEvent')
            ->set('claimEvent.status', ':status')
            ->where('claimEvent.status = :currentStatus')
            ->setParameter('currentStatus', 'on')
            ->setParameter(
                'status',
                true,
                Types::BOOLEAN
            )
            ->executeStatement();

        $q = $this->createQueryBuilder();
        $q->update('ohrm_claim_event', 'claimEvent')
            ->set('claimEvent.status', ':status')
            ->where($q->expr()->isNull('claimEvent.status'))
            ->setParameter(
                'status',
                false,
                Types::BOOLEAN
            )
            ->executeStatement();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.4.0';
    }

    private function updateLangStringVersion(string $version): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string', 'lang_string')
            ->set('lang_string.version', ':version')
            ->setParameter('version', $version);
        $qb->andWhere($qb->expr()->isNull('lang_string.version'))
            ->executeStatement();
    }

    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }

    public function insertI18nGroups(): void
    {
        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_i18n_group')
            ->values([
                'name' => ':name',
                'title' => ':title',
            ])
            ->setParameters([
                'name' => 'claim',
                'title' => 'Claim',
            ])
            ->executeQuery();
    }

    public function modifyDefaultRequiredPasswordStrength(): void
    {
        $value = $this->getConfigHelper()->getConfigValue('authentication.default_required_password_strength');

        if ($value === "veryWeak" || $value === "weak" || $value === "better"
            || $value === "medium"
            || $value === "strong"
            || $value === "strongest"
        ) {
            if ($value === "medium") {
                $value = "better";
            }
            $this->createQueryBuilder()
                ->update('hs_hr_config', 'config')
                ->set('config.value', ':value')
                ->where('config.name = :name')
                ->setParameter('name', 'auth.password_policy.default_required_password_strength')
                ->setParameter('value', $value)
                ->executeStatement();
        }

        $this->getConfigHelper()->deleteConfigValue('authentication.default_required_password_strength');
    }

    public function insertMenuItems(
        string  $menu_title,
        ?int    $screen_id,
        ?int    $parent_id,
        int     $level,
        int     $order_hint,
        int     $status,
        ?string $additional_params
    ): void {
        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_menu_item')
            ->values([
                'menu_title' => ':menu_title',
                'screen_id' => ':screen_id',
                'parent_id' => ':parent_id',
                'level' => ':level',
                'order_hint' => ':order_hint',
                'status' => ':status',
                'additional_params' => ':additional_params',
            ])
            ->setParameters([
                'menu_title' => $menu_title,
                'screen_id' => $screen_id,
                'parent_id' => $parent_id,
                'level' => $level,
                'order_hint' => $order_hint,
                'status' => $status,
                'additional_params' => $additional_params,
            ])
            ->executeQuery();
    }

    public function getParentId(string $menu_title, ?int $parent_id): int
    {
        $parent_id = $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_menu_item')
            ->where('menu_title = :menu_title')
            ->setParameter('menu_title', $menu_title)
            ->andWhere('parent_id = :parent_id')
            ->setParameter('parent_id', $parent_id)
            ->executeQuery()
            ->fetchOne();
        return $parent_id;
    }

    public function getScreenId(string $name): int
    {
        $id = $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_screen')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchOne();
        return $id;
    }

    public function checkClaimExists(): bool
    {
        $claimExists = $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_menu_item')
            ->where('menu_title = :menu_title')
            ->setParameter('menu_title', 'Claim')
            ->executeQuery()
            ->fetchOne();
        return $claimExists;
    }

    private function cleanClaimScreens(): void
    {
        $screenNames = ['Events', 'Expense Types', 'Employee Claim List', 'Assign Claim', 'Submit Claim', 'My Claims List', 'View Claim Module', 'View Create Event', 'View Create Expense'];
        foreach ($screenNames as $screenName) {
            $this->createQueryBuilder()
                ->delete('ohrm_screen')
                ->andWhere('ohrm_screen.name = :screenName')
                ->setParameter('screenName', $screenName)
                ->executeQuery();
        }
    }

    public function modifyDefaultPasswordEnforcement(): void
    {
        $value = $this->getConfigHelper()->getConfigValue('authentication.enforce_password_strength');

        if ($value !== 'on') {
            $value = 'off';
        }
        $this->getConnection()->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values([
                'name' => ':name',
                'value' => ':value',
            ])
            ->setParameters([
                'name' => 'auth.password_policy.enforce_password_strength',
                'value' => $value,
            ])
            ->executeQuery();
        $this->getConfigHelper()->deleteConfigValue('authentication.enforce_password_strength');
    }
}
