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

namespace OrangeHRM\Installer\Migration\V5_5_0;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\LangStringHelper;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->insertI18nGroups();
        $groups = ['claim', 'general'];
        $this->getLangStringHelper()->deleteNonCustomizedLangStrings('claim');
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->updateLangStringVersion($this->getVersion());

        $this->deleteLangStringTranslationByLangStringUnitId(
            'this_page_is_being_developed'
        );

        $this->deleteLangStringTranslationByLangStringUnitId(
            'download_latest_release_with_all_features'
        );

        $this->getLangHelper()->deleteLangStringByUnitId(
            'this_page_is_being_developed',
            $this->getLangHelper()->getGroupIdByName('general')
        );
        $this->getLangHelper()->deleteLangStringByUnitId(
            'download_latest_release_with_all_features',
            $this->getLangHelper()->getGroupIdByName('general')
        );

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
                ['onDelete' => 'SET NULL']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint1);
            $foreignKeyConstraint2 = new ForeignKeyConstraint(
                ['event_type_id'],
                'ohrm_claim_event',
                ['id'],
                'claimEventId',
                ['onDelete' => 'SET NULL']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint2);
            $foreignKeyConstraint3 = new ForeignKeyConstraint(
                ['emp_number'],
                'hs_hr_employee',
                ['emp_number'],
                'claim_Request_Employee_Number',
                ['onDelete' => 'SET NULL']
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

        if (!$this->checkClaimExists()) {
            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_module_default_page')
                ->values(
                    [
                        'module_id' => ':module_id',
                        'user_role_id' => ':user_role_id',
                        'action' => ':action',
                        'priority' => ':priority',
                    ]
                )
                ->setParameter('module_id', $this->getDataGroupHelper()->getModuleIdByName('claim'))
                ->setParameter('user_role_id', $this->getDataGroupHelper()->getUserRoleIdByName('Admin'))
                ->setParameter('action', 'claim/viewAssignClaim')
                ->setParameter('priority', 20)
                ->executeQuery();

            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_module_default_page')
                ->values(
                    [
                        'module_id' => ':module_id',
                        'user_role_id' => ':user_role_id',
                        'action' => ':action',
                        'priority' => ':priority',
                    ]
                )
                ->setParameter('module_id', $this->getDataGroupHelper()->getModuleIdByName('claim'))
                ->setParameter('user_role_id', $this->getDataGroupHelper()->getUserRoleIdByName('Supervisor'))
                ->setParameter('action', 'claim/viewAssignClaim')
                ->setParameter('priority', 10)
                ->executeQuery();

            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_module_default_page')
                ->values(
                    [
                        'module_id' => ':module_id',
                        'user_role_id' => ':user_role_id',
                        'action' => ':action',
                        'priority' => ':priority',
                    ]
                )
                ->setParameter('module_id', $this->getDataGroupHelper()->getModuleIdByName('claim'))
                ->setParameter('user_role_id', $this->getDataGroupHelper()->getUserRoleIdByName('ESS'))
                ->setParameter('action', 'claim/viewClaim')
                ->setParameter('priority', 0)
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
            $submitClaimScreenId = $this->getScreenId('Submit Claim');
            $this->insertMenuItems('Submit Claim', $submitClaimScreenId, $claimMenuItemId, 2, 100, 1, null);
            $myClaimsScreenId = $this->getScreenId('My Claims');
            $this->insertMenuItems('My Claims', $myClaimsScreenId, $claimMenuItemId, 2, 100, 1, null);
            $employeeClaimsScreenId = $this->getScreenId('Employee Claims');
            $this->insertMenuItems('Employee Claims', $employeeClaimsScreenId, $claimMenuItemId, 2, 100, 1, null);
            $assignClaimScreenId = $this->getScreenId('Assign Claim');
            $this->insertMenuItems('Assign Claim', $assignClaimScreenId, $claimMenuItemId, 2, 100, 1, null);
        }

        $this->deleteClaimWorkflowStates();
        $this->removeMarketplaceTables();
        $this->updateI18nGroups();

        $this->insertWorkflowState(
            'INITIATED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'SUBMITTED',
            10
        );
        $this->insertWorkflowState(
            'INITIATED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_CANCEL,
            'CANCELLED',
            10
        );
        $this->insertWorkflowState(
            'SUBMITTED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_CANCEL,
            'CANCELLED',
            10
        );
        $this->insertWorkflowState(
            'REJECTED',
            'ESS USER',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'SUBMITTED',
            10
        );

        $this->insertWorkflowState(
            'INITIATED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'PAID',
            0
        );
        $this->insertWorkflowState(
            'APPROVED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_REJECT,
            'REJECTED',
            0
        );
        $this->insertWorkflowState(
            'SUBMITTED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_APPROVE,
            'PAID',
            10
        );
        $this->insertWorkflowState(
            'SUBMITTED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_REJECT,
            'REJECTED',
            0
        );
        $this->insertWorkflowState(
            'APPROVED',
            'ADMIN',
            WorkflowStateMachine::CLAIM_ACTION_PAY,
            'PAID',
            0
        );

        $this->insertWorkflowState(
            'INITIATED',
            'SUPERVISOR',
            WorkflowStateMachine::CLAIM_ACTION_SUBMIT,
            'APPROVED',
            0
        );
        $this->insertWorkflowState(
            'SUBMITTED',
            'SUPERVISOR',
            WorkflowStateMachine::CLAIM_ACTION_APPROVE,
            'APPROVED',
            0
        );
        $this->insertWorkflowState(
            'SUBMITTED',
            'SUPERVISOR',
            WorkflowStateMachine::CLAIM_ACTION_REJECT,
            'REJECTED',
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
            ],
            'status' => [
                'Type' => Type::getType(Types::STRING),
                'Notnull' => false,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_expense_type', [
            'name' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::STRING),
                'Length' => 100
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
                'Length' => 100
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
        return '5.5.0';
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

    private function insertMenuItems(
        string $menuTitle,
        ?int $screenId,
        ?int $parentId,
        int $level,
        int $orderHint,
        int $status,
        ?string $additionalParams
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
                'menu_title' => $menuTitle,
                'screen_id' => $screenId,
                'parent_id' => $parentId,
                'level' => $level,
                'order_hint' => $orderHint,
                'status' => $status,
                'additional_params' => $additionalParams,
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

    private function insertWorkflowState(
        string $state,
        string $role,
        int $action,
        string $resultingState,
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
            ->setParameter('workflow', WorkflowStateMachine::FLOW_CLAIM)
            ->setParameter('state', $state)
            ->setParameter('role', $role)
            ->setParameter('action', $action)
            ->setParameter('resultingState', $resultingState)
            ->setParameter('rolesToNotify', '')
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

    private function removeMarketplaceTables(): void
    {
        if ($this->getSchemaManager()->tablesExist('ohrm_marketplace_addon')) {
            $this->getSchemaManager()->dropTable('ohrm_marketplace_addon');
        }
    }

    private function updateI18nGroups(): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string', 'langString')
            ->set('langString.group_id', ':groupId')
            ->setParameter('groupId', $this->getLangHelper()->getGroupIdByName('general'));
        $qb->andWhere($qb->expr()->in('langString.unit_id', ':unitIdToChangeGroup'))
            ->setParameter('unitIdToChangeGroup', 'today')
            ->executeQuery();
    }

    private function deleteLangStringTranslationByLangStringUnitId(string $unitId): void
    {
        $id = $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->andWhere('langString.unit_id = :unitId')
            ->setParameter('unitId', $unitId)
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->delete('ohrm_i18n_translate')
            ->andWhere('ohrm_i18n_translate.lang_string_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
