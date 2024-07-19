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

namespace OrangeHRM\Installer\Migration\V5_7_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
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
        $this->deleteLangStringTranslationByLangStringUnitId('translate_text_manually', $this->getLangHelper()->getGroupIdByName('admin'));

        $this->getLangHelper()->deleteLangStringByUnitId(
            'translate_text_manually',
            $this->getLangHelper()->getGroupIdByName('admin')
        );

        $this->deleteUnusedLangStrings();

        $this->deleteLangStringTranslationByLangStringUnitId('accepts_up_to_1mb', $this->getLangHelper()->getGroupIdByName('general'));
        $this->getLangHelper()->deleteLangStringByUnitId(
            'accepts_up_to_1mb',
            $this->getLangHelper()->getGroupIdByName('general')
        );

        $this->deleteLangStringTranslationByLangStringUnitId('accept_custom_format_file', $this->getLangHelper()->getGroupIdByName('general'));
        $this->getLangHelper()->deleteLangStringByUnitId(
            'accept_custom_format_file',
            $this->getLangHelper()->getGroupIdByName('general')
        );

        $localizationDataGroupId = $this->getDataGroupHelper()->getDataGroupIdByName(
            'apiv2_admin_localization_languages'
        );
        $this->createQueryBuilder()->update('ohrm_data_group')
            ->andWhere('id = :id')
            ->set('can_delete', ':value')
            ->setParameter('id', $localizationDataGroupId)
            ->setParameter('value', 1)
            ->executeQuery();
        $this->createQueryBuilder()->update('ohrm_user_role_data_group')
            ->andWhere('data_group_id = :dataGroupId')
            ->andWhere('user_role_id = :userRoleId')
            ->set('can_delete', ':value')
            ->setParameter('dataGroupId', $localizationDataGroupId)
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName('Admin'))
            ->setParameter('value', 1)
            ->executeQuery();

        $groups = ['admin', 'pim', 'recruitment', 'general'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->updateLangStringVersion($this->getVersion());

        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');

        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $this->dropOldTables();

        if (!$this->getSchemaHelper()->tableExists(['ohrm_i18n_error'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_error')
                ->addColumn('name', Types::STRING, ['length' => 255, 'Notnull' => true])
                ->addColumn('message', Types::STRING, ['length' => 255, 'Notnull' => true])
                ->setPrimaryKey(['name'])
                ->create();
        }

        $this->insertI18NError('placeholder_mismatch', 'Mismatch found between placeholders');
        $this->insertI18NError('select_placeholder_mismatch', 'Mismatch found between select expression placeholder');
        $this->insertI18NError('plural_placeholder_mismatch', 'Mismatch found between plural expression placeholder');
        $this->insertI18NError('invalid_syntax', 'The syntax used is invalid');

        if (!$this->getSchemaManager()->tablesExist(['ohrm_i18n_import_error'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_import_error')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true, 'Notnull' => true])
                ->addColumn('lang_string_id', Types::INTEGER, ['Notnull' => true])
                ->addColumn('language_id', Types::INTEGER, ['Notnull' => true])
                ->addColumn('error_name', Types::STRING, ['length' => 255, 'Notnull' => true])
                ->addColumn('imported_by', Types::INTEGER, ['Notnull' => true])
                ->setPrimaryKey(['id'])
                ->create();

            $langStringConstraint  = new ForeignKeyConstraint(
                ['lang_string_id'],
                'ohrm_i18n_lang_string',
                ['id'],
                'i18n_lang_string_id',
                ['onDelete' => 'CASCADE']
            );
            $languageConstraint = new ForeignKeyConstraint(
                ['language_id'],
                'ohrm_i18n_language',
                ['id'],
                'i18n_language_id',
                ['onDelete' => 'CASCADE']
            );
            $errorConstraint = new ForeignKeyConstraint(
                ['error_name'],
                'ohrm_i18n_error',
                ['name'],
                'i18n_error_name',
                ['onDelete' => 'CASCADE']
            );
            $importedByConstraint = new ForeignKeyConstraint(
                ['imported_by'],
                'hs_hr_employee',
                ['emp_number'],
                'imported_by_emp_number',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_i18n_import_error', $langStringConstraint);
            $this->getSchemaHelper()->addForeignKey('ohrm_i18n_import_error', $languageConstraint);
            $this->getSchemaHelper()->addForeignKey('ohrm_i18n_import_error', $errorConstraint);
            $this->getSchemaHelper()->addForeignKey('ohrm_i18n_import_error', $importedByConstraint);

            $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        }
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.7.0';
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

    /**
     * @param string $unitId
     * @param int $groupId
     */
    private function deleteLangStringTranslationByLangStringUnitId(string $unitId, int $groupId): void
    {
        $id = $this->getConnection()->createQueryBuilder()
            ->select('id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->andWhere('langString.unit_id = :unitId')
            ->setParameter('unitId', $unitId)
            ->andWhere('langString.group_id = :groupId')
            ->setParameter('groupId', $groupId)
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->delete('ohrm_i18n_translate')
            ->andWhere('ohrm_i18n_translate.lang_string_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    private function dropOldTables(): void
    {
        $this->getSchemaManager()->dropTable('ohrm_user_selection_rule');
        $this->getSchemaManager()->dropTable('ohrm_role_user_selection_rule');
        $this->getSchemaManager()->dropTable('ohrm_leave_entitlement_adjustment');
        $this->getSchemaManager()->dropTable('ohrm_leave_adjustment');
        $this->getSchemaManager()->dropTable('ohrm_available_group_field');
        $this->getSchemaManager()->dropTable('hs_hr_mailnotifications');
        $this->getSchemaManager()->dropTable('hs_hr_custom_export');
        $this->getSchemaManager()->dropTable('hs_hr_custom_import');
        $this->getSchemaManager()->dropTable('hs_hr_module');
        $this->getSchemaManager()->dropTable('ohrm_advanced_report');
        $this->getSchemaManager()->dropTable('ohrm_beacon_notification');
        $this->getSchemaManager()->dropTable('ohrm_datapoint');
        $this->getSchemaManager()->dropTable('ohrm_datapoint_type');
    }

    /**
     * @param string $name
     * @param string $message
     */
    private function insertI18NError(string $name, string $message): void
    {
        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_i18n_error')
            ->values([
                'name' => ':name',
                'message' => ':message'
            ])
            ->setParameter('name', $name)
            ->setParameter('message', $message)
            ->executeQuery();
    }

    private function deleteUnusedLangStrings(): void
    {
        $langStringsToDelete = [
            'general' => [
                'failed_to_import',
                'no_records_added'
            ],
            'admin' => [
                'employee_id_field',
                'firstname_field',
                'group_members_attribute',
                'group_name_attribute',
                'group_object_class',
                'group_object_filter',
                'lastname_field',
                'middlename_field',
                'use_browser_language_if_set',
                'user_membership_attribute',
                'user_status_field',
                'work_email_field',
            ],
            'pim' => [
                'first_name',
                'number_of_records_imported'
            ],
            'recruitment' => [
                'history',
                'profile'
            ],
            'maintenance' => [
                'selected_candidates'
            ],
            'buzz' => [
                'no_work_anniversaries_for_the_next_30_days',
                'years'
            ],
            'auth' => [
                'should_have_min_8_characters',
                'must_contain_lower_case_upper_case_digit_character_message',
                'your_password_should_not_contain_spaces'
            ]
        ];

        foreach ($langStringsToDelete as $group => $langStrings) {
            $groupId = $this->getLangHelper()->getGroupIdByName($group);
            foreach ($langStrings as $langString) {
                $this->deleteLangStringTranslationByLangStringUnitId(
                    $langString,
                    $groupId
                );
                $this->getLangHelper()->deleteLangStringByUnitId(
                    $langString,
                    $groupId
                );
            }
        }
    }
}
