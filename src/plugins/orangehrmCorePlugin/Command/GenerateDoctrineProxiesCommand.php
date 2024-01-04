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

namespace OrangeHRM\Core\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Console\Command;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDoctrineProxiesCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'orm:generate-proxies';
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $proxyDir = Config::get(Config::DOCTRINE_PROXY_DIR);
        $cache = new ArrayAdapter();
        $paths = $this->getPaths();
        $config = ORMSetup::createAnnotationMetadataConfiguration(
            $paths,
            false,
            $proxyDir,
            $cache
        );

        $connectionParams = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
            'charset' => 'utf8mb4',
        ];

        $em = EntityManager::create($connectionParams, $config);

        $metadata = $em->getMetadataFactory()
            ->getAllMetadata();

        $count = $em->getProxyFactory()
            ->generateProxyClasses($metadata, $proxyDir);

        $this->getIO()->success("$count proxy classes created");
        return self::SUCCESS;
    }

    /**
     * @return array
     */
    private function getPaths(): array
    {
        $paths = [];
        $pluginPaths = Config::get('ohrm_plugin_paths');
        foreach ($pluginPaths as $pluginPath) {
            $entityPath = realpath($pluginPath . '/entity');
            if ($entityPath) {
                $paths[] = $entityPath;
            }
        }
        return $paths;
    }
}
