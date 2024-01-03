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

namespace OrangeHRM\Installer\Migration\V5_4_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
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
        $groups = ['admin', 'auth', 'general'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->updateLangStringVersion($this->getVersion());

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

        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status',
                    'display_name' => ':display_name'
                ]
            )
            ->setParameter('name', "mobile")
            ->setParameter('status', 1)
            ->setParameter('display_name', 'Mobile')
            ->executeQuery();

        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_password_length', '8');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_uppercase_letters', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_lowercase_letters', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_numbers_in_password', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.min_special_characters', '1');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.default_required_password_strength', 'strong');
        $this->getConfigHelper()->setConfigValue('auth.password_policy.is_spaces_allowed', 'false');

        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->changePermissionForAttendanceConfigurationAPI();
        $this->changePermissionForTimeConfigPeriodAPI();
        $this->changePermissionForEmployeeWorkShiftAPI();

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

        $this->modifyDefaultRequiredPasswordStrength();
        $this->modifyDefaultPasswordEnforcement();

        $this->createOAuth2Tables();

        // https://github.com/orangehrm/orangehrm/issues/1622
        $this->getSchemaHelper()->addOrChangeColumns('ohrm_migration_log', [
            'php_version' => ['Type' => Type::getType(Types::STRING), 'Length' => 255],
        ]);
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
            ->addColumn('expiry_date_time_utc', Types::DATETIME_IMMUTABLE)
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
            ->addColumn('expiry_date_time_utc', Types::DATETIME_IMMUTABLE)
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
            ->addColumn('expiry_date_time_utc', Types::DATETIME_IMMUTABLE)
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

    private function changePermissionForAttendanceConfigurationAPI(): void
    {
        $this->getDataGroupHelper()->addDataGroupPermissions(
            'apiv2_attendance_configuration',
            'ESS',
            true,
            false,
            false,
            false,
            false
        );
    }

    private function changePermissionForTimeConfigPeriodAPI(): void
    {
        $this->getDataGroupHelper()->addDataGroupPermissions(
            'apiv2_time_time_sheet_config',
            'ESS',
            true,
            false,
            false,
            false,
            false
        );
    }

    private function changePermissionForEmployeeWorkShiftAPI(): void
    {
        $this->getDataGroupHelper()->addDataGroupPermissions(
            'apiv2_pim_employee_work_shift',
            'ESS',
            true,
            false,
            false,
            false,
            false
        );
    }
}
