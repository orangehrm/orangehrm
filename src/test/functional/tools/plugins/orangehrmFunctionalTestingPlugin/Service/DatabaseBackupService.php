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

namespace OrangeHRM\FunctionalTesting\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Exception;
use OrangeHRM\Core\Service\CacheService;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

class DatabaseBackupService
{
    use EntityManagerHelperTrait;
    use CacheTrait {CacheTrait::getCache as getAppCache;}
    use LoggerTrait;

    public const DB_SAVEPOINT_CACHE_KEY_PREFIX = 'db.savepoint';
    public const INITIAL_SAVEPOINT_CACHE_KEY_PREFIX = 'db.initial.savepoint';

    /**
     * @return Connection
     */
    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return AbstractSchemaManager
     */
    private function getSchemaManager(): AbstractSchemaManager
    {
        $conn = $this->getConnection();
        return $conn->createSchemaManager();
    }

    /**
     * @return AdapterInterface
     */
    private function getCache(): AdapterInterface
    {
        return CacheService::getCache('functional-testing');
    }

    /**
     * @param string $savepointName
     * @return string
     */
    private function genSavepointCacheKeyPrefix(string $savepointName): string
    {
        return self::DB_SAVEPOINT_CACHE_KEY_PREFIX . ".$savepointName";
    }

    /**
     * @param string $savepointCacheKeyPrefix
     * @param string $tableName
     * @return string
     */
    private function genSavepointCacheKey(string $savepointCacheKeyPrefix, string $tableName): string
    {
        return "$savepointCacheKeyPrefix.$tableName";
    }

    /**
     * @param string $savepointName
     * @return array
     */
    public function createSavepoint(string $savepointName): array
    {
        return $this->_createSavepoint($this->genSavepointCacheKeyPrefix($savepointName));
    }

    /**
     * @return array
     */
    public function createInitialSavepoint(): array
    {
        $this->deleteAllSavepoints();
        return $this->_createSavepoint(self::INITIAL_SAVEPOINT_CACHE_KEY_PREFIX);
    }

    /**
     * @param string $savepointCacheKeyPrefix
     * @return array
     */
    private function _createSavepoint(string $savepointCacheKeyPrefix): array
    {
        $conn = $this->getConnection();

        $tables = [];
        $allTables = $this->getSchemaManager()->listTables();
        foreach ($allTables as $table) {
            $tableName = $table->getName();
            $results = $conn->fetchAllAssociative("SELECT * FROM `$tableName`");

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->getCache()->getItem($this->genSavepointCacheKey($savepointCacheKeyPrefix, $tableName));
            if ($cacheItem->isHit()) {
                throw new Exception('Savepoint already created for the given name');
            }

            $this->getCache()->get(
                $this->genSavepointCacheKey($savepointCacheKeyPrefix, $tableName),
                function () use ($tableName, $results) {
                    return ['tableName' => $tableName, 'data' => $results];
                }
            );

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->getCache()->getItem($this->genSavepointCacheKey($savepointCacheKeyPrefix, $tableName));
            if (!$cacheItem->isHit()) {
                throw new Exception('Savepoint creation failed');
            }

            $tables[] = $table;
        }
        if (count($allTables) !== count($tables)) {
            throw new Exception('Incomplete savepoint creation');
        }
        return $tables;
    }

    /**
     * @param string $savepointName
     * @return array
     */
    public function restoreToSavepoint(string $savepointName): array
    {
        $this->getAppCache()->clear();
        return $this->_restoreToSavepoint($this->genSavepointCacheKeyPrefix($savepointName));
    }

    /**
     * @return array
     */
    public function restoreToInitialSavepoint(): array
    {
        $this->getAppCache()->clear();
        return $this->_restoreToSavepoint(self::INITIAL_SAVEPOINT_CACHE_KEY_PREFIX);
    }

    /**
     * @param string $savepointCacheKeyPrefix
     * @return array
     */
    private function _restoreToSavepoint(string $savepointCacheKeyPrefix): array
    {
        $conn = $this->getConnection();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            $tables = [];
            $allTables = $this->getSchemaManager()->listTables();
            foreach ($allTables as $table) {
                $tableName = $table->getName();
                /** @var CacheItem $cacheItem */
                $cacheItem = $this->getCache()->getItem(
                    $this->genSavepointCacheKey($savepointCacheKeyPrefix, $tableName)
                );
                if (!$cacheItem->isHit()) {
                    throw new Exception('No savepoint for the given name');
                }

                $conn->executeStatement("DELETE FROM `$tableName`");
                $conn->executeStatement("ALTER TABLE `$tableName` AUTO_INCREMENT = 1");
                $data = $cacheItem->get()['data'];
                foreach ($data as $row) {
                    $conn->insert($tableName, $row);
                }
                $tables[] = [$table, count($data)];
            }

            return $tables;
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            throw $e;
        } finally {
            $conn->executeStatement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * @param string $savepointName
     * @return bool
     */
    public function deleteSavepoint(string $savepointName): bool
    {
        return $this->_deleteSavepoint($this->genSavepointCacheKeyPrefix($savepointName));
    }

    /**
     * @param string[] $savepointNames
     * @return bool
     */
    public function deleteSavepoints(array $savepointNames = []): bool
    {
        if (empty($savepointNames)) {
            return $this->_deleteSavepoint(self::DB_SAVEPOINT_CACHE_KEY_PREFIX);
        }
        $successSavepointNames = [];
        foreach ($savepointNames as $savepointName) {
            $successSavepointNames[] = $this->_deleteSavepoint($this->genSavepointCacheKeyPrefix($savepointName));
        }
        return count($savepointNames) === count($successSavepointNames);
    }

    /**
     * @param string $savepointCacheKeyPrefix
     * @return bool
     */
    private function _deleteSavepoint(string $savepointCacheKeyPrefix): bool
    {
        return $this->getCache()->clear($savepointCacheKeyPrefix);
    }

    /**
     * @return bool
     */
    private function deleteAllSavepoints(): bool
    {
        return $this->getCache()->clear(self::DB_SAVEPOINT_CACHE_KEY_PREFIX) &&
            $this->getCache()->clear(self::INITIAL_SAVEPOINT_CACHE_KEY_PREFIX);
    }
}
