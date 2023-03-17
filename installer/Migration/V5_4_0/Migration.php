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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Entity\WorkflowStateMachine;
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

        $groups = ['admin', 'auth', 'general'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $this->updateLangStringVersion($this->getVersion());

        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_event'])) {
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

        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_attachment'])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_attachment')
                ->addColumn('request_id', Types::INTEGER)
                ->addColumn('eattach_id', Types::BIGINT)
                ->addColumn('eattach_size', Types::INTEGER, ['Default' => 0, 'Notnull' => false])
                ->addColumn('eattach_desc', Types::STRING, ['Length' => 1000, 'Notnull' => false])
                ->addColumn('eattach_filename', Types::STRING, ['Length' => 1000, 'Notnull' => false])
                ->addColumn('eattach_attachment', Types::BLOB, ['Notnull' => false])
                ->addColumn('eattach_type', Types::STRING, ['Length' => 200, 'Notnull' => false])
                ->addColumn('attached_by', Types::INTEGER, ['Default' => null, 'Notnull' => false])
                ->addColumn('attached_by_name', Types::STRING, ['Length' => 200, 'Notnull' => false])
                ->addColumn('attached_time', Types::DATETIME_MUTABLE, ['Notnull' => false])
                ->setPrimaryKey(['request_id', 'eattach_id'])
                ->create();
            $foreignKeyConstraint1 = new ForeignKeyConstraint(
                ['attached_by'],
                'hs_hr_employee',
                ['emp_number'],
                'attachedById',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_attachment', $foreignKeyConstraint1);
            $foreignKeyConstraint2 = new ForeignKeyConstraint(
                ['request_id'],
                'ohrm_claim_request',
                ['id'],
                'claimRequestId',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_attachment', $foreignKeyConstraint2);
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

        if (!$this->getSchemaHelper()->tableExists(['ohrm_expense'])) {
            $this->getSchemaHelper()->createTable('ohrm_expense')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('expense_type_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('date', Types::DATE_MUTABLE, ['Notnull' => false])
                ->addColumn('amount', Types::DECIMAL, ['Notnull' => false, 'Scale' => 2, 'Precision' => 12])
                ->addColumn('note', Types::TEXT, ['Notnull' => false])
                ->addColumn('request_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint1 = new ForeignKeyConstraint(
                ['expense_type_id'],
                'ohrm_expense_type',
                ['id'],
                'expenseTypeId',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense', $foreignKeyConstraint1);
            $foreignKeyConstraint2 = new ForeignKeyConstraint(
                ['request_id'],
                'ohrm_claim_request',
                ['id'],
                'claimRequsetId',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense', $foreignKeyConstraint2);
        }

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

        $this->createOAuth2Tables();

        $this->deleteClaimWorkflowStates();

        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'INITIATED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'SUBMITTED',
            '',
            10
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'INITIATED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_CANCEL,
            'CANCELLED',
            '',
            10
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'SUBMITTED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_CANCEL,
            'CANCELLED',
            '',
            10
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'REJECTED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'SUBMITTED',
            '',
            10
        );

        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'INITIATED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'PAID',
            '',
            0
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'APPROVED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_REJECT,
            'REJECTED',
            '',
            0
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'SUBMITTED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_APPROVE,
            'PAID',
            '',
            10
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'SUBMITTED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_REJECT,
            'REJECTED',
            '',
            0
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'APPROVED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_PAY,
            'PAID',
            '',
            0
        );

        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'INITIATED',
            'SUPERVISOR',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'APPROVED',
            '',
            0
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'SUBMITTED',
            'SUPERVISOR',
            WorkflowStateMachine::CLAIM_ACTION_APPROVE,
            'APPROVED',
            '',
            0
        );
        $this->insertWorkflowState(
            WorkflowStateMachine::FLOW_CLAIM,
            'SUBMITTED',
            'SUPERVISOR',
            WorkflowStateMachine::CLAIM_ACTION_REJECT,
            'REJECTED',
            '',
            0
        );
    }

    private function modifyClaimTables(): void
    {
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_claim_request CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci'
        );

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_claim_event', [
            'is_deleted' => ['Type' => Type::getType(Types::BOOLEAN), 'Notnull' => true, 'Default' => 0],
            'status' => [
                'Type' => Type::getType(Types::BOOLEAN),
                'Notnull' => false,
                'Default' => null,
                'CustomSchemaOptions' => ['collation' => null, 'charset' => null]
            ],
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

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_expense', [
            'date' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE)
            ],
            'note' => [
                'Type' => Type::getType(Types::STRING),
                'Length' => 1000
            ],
            'is_deleted' => [
                'Type' => Type::getType(Types::BOOLEAN),
            ],
        ]);

        $this->getSchemaHelper()->dropColumn('ohrm_claim_attachment', 'attached_by_name');

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

    private function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }

    private function insertI18nGroups(): void
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

    private function modifyDefaultRequiredPasswordStrength(): void
    {
        $value = $this->getConfigHelper()->getConfigValue('authentication.default_required_password_strength');

        if (
            $value === "veryWeak"
            || $value === "weak"
            || $value === "better"
            || $value === "medium"
            || $value === "strong"
            || $value === "strongest"
        ) {
            if ($value === "medium") {
                $value = "better";
            }
            $this->getConfigHelper()->setConfigValue('auth.password_policy.default_required_password_strength', $value);
        }

        $this->getConfigHelper()->deleteConfigValue('authentication.default_required_password_strength');
    }

    private function insertMenuItems(//TODO
        string $menu_title,
        ?int $screen_id,
        ?int $parent_id,
        int $level,
        int $order_hint,
        int $status,
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
        return $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_menu_item')
            ->where('menu_title = :menu_title')
            ->setParameter('menu_title', $menu_title)
            ->andWhere('parent_id = :parent_id')
            ->setParameter('parent_id', $parent_id)
            ->executeQuery()
            ->fetchOne();
    }

    public function getScreenId(string $name): int
    {
        return $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_screen')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchOne();
    }

    public function checkClaimExists(): bool
    {
        return $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_menu_item')
            ->where('menu_title = :menu_title')
            ->setParameter('menu_title', 'Claim')
            ->executeQuery()
            ->fetchOne();
    }

    private function cleanClaimScreens(): void
    {
        $screenNames = [
            'Events',
            'Expense Types',
            'Employee Claim List',
            'Assign Claim',
            'Submit Claim',
            'My Claims List',
            'View Claim Module',
            'View Create Event',
            'View Create Expense'
        ];
        $qb = $this->createQueryBuilder()
            ->delete('ohrm_screen');
        $qb->andWhere($qb->expr()->in('ohrm_screen.name', ':screenName'))
            ->setParameter('screenName', $screenNames, Connection::PARAM_STR_ARRAY)
            ->executeQuery();
    }

    private function modifyDefaultPasswordEnforcement(): void
    {
        $value = $this->getConfigHelper()->getConfigValue('authentication.enforce_password_strength');

        if ($value !== 'on') {
            $value = 'off';
        }
        $this->getConfigHelper()->setConfigValue('auth.password_policy.enforce_password_strength', $value);
        $this->getConfigHelper()->deleteConfigValue('authentication.enforce_password_strength');
    }

    private function createOAuth2Tables(): void
    {
        $this->getSchemaHelper()->createTable('ohrm_oauth2_client')
            ->addColumn('id', Types::BIGINT, ['Autoincrement' => true])
            ->addColumn('name', Types::STRING, ['Length' => 255])
            ->addColumn('client_id', Types::STRING, ['Length' => 255])
            ->addColumn('client_secret', Types::STRING, ['Length' => 255, 'Notnull' => false])
            ->addColumn('redirect_uri', Types::STRING, ['Length' => 2000])
            ->addColumn('is_confidential', Types::BOOLEAN)
            ->addColumn('enabled', Types::BOOLEAN)
            ->addUniqueIndex(['client_id'], 'idx_client_id')
            ->setPrimaryKey(['id'])
            ->create();

        $this->getSchemaHelper()->createTable('ohrm_oauth2_authorization_code')
            ->addColumn('id', Types::BIGINT, ['Autoincrement' => true])
            ->addColumn('authorization_code', Types::STRING, ['Length' => 255])
            ->addColumn('client_id', Types::BIGINT)
            ->addColumn('user_id', Types::INTEGER)
            ->addColumn('redirect_uri', Types::STRING, ['Length' => 2000])
            ->addColumn('expiry_date_time', Types::DATETIME_IMMUTABLE)
            ->addColumn('revoked', Types::BOOLEAN)
            ->addUniqueIndex(['authorization_code'], 'idx_authorization_code')
            ->setPrimaryKey(['id'])
            ->create();
        $foreignKeyConstraintClientId = new ForeignKeyConstraint(
            ['client_id'],
            'ohrm_oauth2_client',
            ['id'],
            'auth_code_client_id',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_oauth2_authorization_code', $foreignKeyConstraintClientId);

        $this->getSchemaHelper()->createTable('ohrm_oauth2_access_token')
            ->addColumn('id', Types::BIGINT, ['Autoincrement' => true])
            ->addColumn('access_token', Types::STRING, ['Length' => 255])
            ->addColumn('client_id', Types::BIGINT)
            ->addColumn('user_id', Types::INTEGER)
            ->addColumn('expiry_date_time', Types::DATETIME_IMMUTABLE)
            ->addColumn('revoked', Types::BOOLEAN)
            ->addUniqueIndex(['access_token'], 'idx_access_token')
            ->setPrimaryKey(['id'])
            ->create();
        $foreignKeyAccessTokenClientId = new ForeignKeyConstraint(
            ['client_id'],
            'ohrm_oauth2_client',
            ['id'],
            'access_token_client_id',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_oauth2_access_token', $foreignKeyAccessTokenClientId);

        $this->getSchemaHelper()->createTable('ohrm_oauth2_refresh_token')
            ->addColumn('id', Types::BIGINT, ['Autoincrement' => true])
            ->addColumn('refresh_token', Types::STRING, ['Length' => 255])
            ->addColumn('access_token', Types::STRING, ['Length' => 255])
            ->addColumn('expiry_date_time', Types::DATETIME_IMMUTABLE)
            ->addColumn('revoked', Types::BOOLEAN)
            ->addUniqueIndex(['refresh_token'], 'idx_refresh_token')
            ->setPrimaryKey(['id'])
            ->create();
        $foreignKeyAccessToken = new ForeignKeyConstraint(
            ['access_token'],
            'ohrm_oauth2_access_token',
            ['access_token'],
            'oauth2_access_token',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_oauth2_refresh_token', $foreignKeyAccessToken);

        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_oauth2_client')
            ->values(
                [
                    'name' => ':name',
                    'client_id' => ':client_id',
                    'client_secret' => ':client_secret',
                    'redirect_uri' => ':redirect_uri',
                    'is_confidential' => ':is_confidential',
                    'enabled' => ':enabled',
                ]
            )
            ->setParameter('name', "OrangeHRM Mobile App")
            ->setParameter('client_id', 'orangehrm_mobile_app')
            ->setParameter('client_secret', null)
            ->setParameter('redirect_uri', 'com.orangehrm.opensource://oauthredirect')
            ->setParameter('is_confidential', false, Types::BOOLEAN)
            ->setParameter('enabled', true, Types::BOOLEAN)
            ->executeQuery();

        $encryptionKey = base64_encode(random_bytes(32));
        $this->getConfigHelper()->setConfigValue('oauth.encryption_key', $encryptionKey);
        $encryptionKey = base64_encode(random_bytes(32));
        $this->getConfigHelper()->setConfigValue('oauth.token_encryption_key', $encryptionKey);

        // see https://php.net/manual/en/dateinterval.construct.php for TTL duration
        $this->getConfigHelper()->setConfigValue('oauth.auth_code_ttl', 'PT5M'); // 5 minutes
        $this->getConfigHelper()->setConfigValue('oauth.refresh_token_ttl', 'P1M'); // 1 month
        $this->getConfigHelper()->setConfigValue('oauth.access_token_ttl', 'PT30M'); // 30 minutes
    }

    private function insertWorkflowState(
        int $workflow,
        string $state,
        string $role,
        int $action,
        string $resultingState,
        string $rolesToNotify,
        int $priority
    ): void {
        $this->createQueryBuilder()
            ->insert('ohrm_workflow_state_machine')
            ->values(
                [
                    'workflow' => ':workflow',
                    'state' => ':state',
                    'role' => ':role',
                    'action' => ':action',
                    'resulting_state' => ':resultingState',
                    'roles_to_notify' => ':rolesToNotify',
                    'priority' => ':priority',
                ]
            )
            ->setParameter('workflow', $workflow)
            ->setParameter('state', $state)
            ->setParameter('role', $role)
            ->setParameter('action', $action)
            ->setParameter('resultingState', $resultingState)
            ->setParameter('rolesToNotify', $rolesToNotify)
            ->setParameter('priority', $priority)
            ->executeQuery();
    }

    private function deleteClaimWorkflowStates(): void
    {
        $this->createQueryBuilder()
            ->delete('ohrm_workflow_state_machine')
            ->where('workflow = :workflow')
            ->setParameter('workflow', 'CLAIM')
            ->executeQuery();
    }
}
