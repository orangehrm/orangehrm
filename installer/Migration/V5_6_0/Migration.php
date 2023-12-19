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

namespace OrangeHRM\Installer\Migration\V5_6_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
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
        $groups = ['admin'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->getSchemaHelper()->dropForeignKeys('ohrm_i18n_translate', ['langStringId']);
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['lang_string_id'],
            'ohrm_i18n_lang_string',
            ['id'],
            'langStringId',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_translate', $foreignKeyConstraint);

        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');

        if (!$this->getSchemaHelper()->tableExists(['ohrm_openid_provider'])) {
            $this->getSchemaHelper()->createTable('ohrm_openid_provider')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true, 'Notnull' => true, 'Length' => 10])
                ->addColumn('provider_name', Types::STRING, ['Notnull' => false, 'Length' => 40])
                ->addColumn('provider_url', Types::STRING, ['Notnull' => false, 'Length' => 255])
                ->addColumn('status', Types::SMALLINT, ['Notnull' => false, 'Length' => 1])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_auth_provider_extra_details'])) {
            $this->getSchemaHelper()->createTable('ohrm_auth_provider_extra_details')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('provider_id', Types::INTEGER, ['Notnull' => true, 'Length' => 10])
                ->addColumn('provider_type', Types::INTEGER, ['Notnull' => false])
                ->addColumn('client_id', Types::TEXT)
                ->addColumn('client_secret', Types::TEXT)
                ->addColumn('developer_key', Types::TEXT)
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['provider_id'],
                'ohrm_openid_provider',
                ['id'],
                'Provider',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_auth_provider_extra_details', $foreignKeyConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_openid_user_identity'])) {
            $this->getSchemaHelper()->createTable('ohrm_openid_user_identity')
                ->addColumn('user_id', Types::INTEGER, ['Length' => 10])
                ->addColumn('provider_id', Types::INTEGER, ['Length' => 10])
                ->addColumn('user_identity', Types::STRING, ['Notnull' => false, 'Default' => null, 'Length' => 255])
                ->setPrimaryKey(['user_id'])
                ->create();
            $foreignKeyConstraint1 = new ForeignKeyConstraint(
                ['provider_id'],
                'ohrm_openid_provider',
                ['id'],
                'provideId',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_openid_user_identity', $foreignKeyConstraint1);
            $foreignKeyConstraint2 = new ForeignKeyConstraint(
                ['user_id'],
                'ohrm_user',
                ['id'],
                'providerUserId',
                ['onDelete' => 'SET NULL']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint2);
        }

        $this->modifyAuthProviderTables();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.6.0';
    }

    private function modifyAuthProviderTables(): void
    {
        $this->getSchemaHelper()->addOrChangeColumns('ohrm_openid_provider', [
            'provider_url' => [
                'Type' => Type::getType(Types::STRING),
                'Length' => 2000
            ],
            'status' => [
                'Type' => Type::getType(Types::BOOLEAN),
                'Notnull' => true,
                'Default' => true,
                'CustomSchemaOptions' => ['collation' => null, 'charset' => null]
            ],
        ]);
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
}
