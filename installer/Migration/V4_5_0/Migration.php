<?php

namespace OrangeHRM\Installer\Migration\V4_5_0;

use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_oauth_scope'])) {
            $this->getSchemaHelper()->createTable('ohrm_oauth_scope')
                ->addColumn('scope', Types::TEXT)
                ->addColumn('is_default', Types::BOOLEAN, ['Notnull' => true, 'Default' => false])
                ->create();
        }

        $this->getSchemaHelper()->addColumn(
            'ohrm_oauth_client',
            'grant_types',
            Types::STRING,
            ['Length' => 80,'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_oauth_client',
            'scope',
            Types::STRING,
            ['Length' => 4000,'Notnull' => false, 'Default' => null]
        );

        $this->createQueryBuilder()
            ->update('ohrm_oauth_client', 'oauth_client')
            ->set('grant_types', ':grantTypes')
            ->setParameter('grantTypes', 'client_credentials')
            ->set('scope', ':scope')
            ->setParameter('scope', 'admin')
            ->executeQuery();

        $clientId = $this->createQueryBuilder()
            ->select('oauth_client.client_id')
            ->from('ohrm_oauth_client', 'oauth_client')
            ->where('oauth_client.client_id = :clientId')
            ->setParameter('clientId', 'orangehrm_mobile_app')
            ->executeQuery()
            ->fetchOne();
        if ($clientId != 'orangehrm_mobile_app') {
            $this->createQueryBuilder()
                ->insert('ohrm_oauth_client')
                ->values(
                    [
                        'client_id' => ':clientId',
                        'client_secret' => ':clientSecret',
                        'redirect_uri' => ':redirectUri',
                        'grant_types' => ':grantTypes',
                        'scope' => ':scope'
                    ]
                )
                ->setParameter('clientId', 'orangehrm_mobile_app')
                ->setParameter('clientSecret', '')
                ->setParameter('redirectUri', '')
                ->setParameter('grantTypes', 'password refresh_token')
                ->setParameter('scope', 'user')
                ->executeQuery()
                ->fetchOne();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_rest_api_usage'])) {
            $this->getSchemaHelper()->createTable('ohrm_rest_api_usage')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('client_id', Types::STRING, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('user_id', Types::STRING, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('scope', Types::STRING, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('method', Types::STRING, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('module', Types::STRING, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('action', Types::STRING, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('path', Types::STRING, ['Notnull' => false, 'Default' => null])
                ->addColumn('parameters', Types::SMALLINT, ['Length'=> 255,'Notnull' => false, 'Default' => null])
                ->addColumn('created_at', Types::DATETIMETZ_MUTABLE, ['Notnull' => false, 'Default' => 'CURRENT_TIMESTAMP'])
                ->setPrimaryKey(['id'])
                ->create();
        }
    }
}
