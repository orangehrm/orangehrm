<?php

namespace OrangeHRM\Installer\Migration\V4_8_0;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->insertConfig('help.url', 'https://opensourcehelp.orangehrm.com'); //has access issues.
        $this->insertConfig('help.processorClass', 'ZendeskHelpProcessor');

        $this->updateConfig('4.8', 'instance.version');
        $this->updateConfig('80', 'instance.increment_number');

        $this->createQueryBuilder()
            ->insert('ohrm_i18n_group')
            ->values(
                [
                    'name' => ':name',
                    'title' => ':title'
                ]
            )
            ->setParameter('name', 'help')
            ->setParameter('title', 'Help')
            ->executeQuery();
    }

    private function insertConfig(string $value, string $key): void
    {
        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', $key)
            ->setParameter('value', $value)
            ->executeQuery();
    }

    /**
     * @param string $value
     * @param string $key
     * @return void
     */
    private function updateConfig(string $value, string $key): void
    {
        $this->createQueryBuilder()
            ->update('hs_hr_config', 'config')
            ->set('config.value', ':value')
            ->setParameter('value', $value)
            ->andWhere('config.key = :key')
            ->setParameter('key', $key)
            ->executeQuery();
    }
}
