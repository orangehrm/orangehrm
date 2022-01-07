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

namespace OrangeHRM\FunctionalTesting\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Exception;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use Symfony\Component\Cache\CacheItem;

class DatabaseBackupService
{
    use EntityManagerHelperTrait;
    use CacheTrait;

    public const DB_SAVEPOINT_CACHE_KEY_PREFIX = 'db.savepoint';
    public const INITIAL_SAVEPOINT_NAME = '__initial';

    /**
     * @return Connection
     */
    private function getConnection(): Connection
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        return $conn;
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
     * @param string $savepointName
     * @param string|null $tableName
     * @return string
     */
    private function genSavepointCacheKey(string $savepointName, ?string $tableName): string
    {
        return self::DB_SAVEPOINT_CACHE_KEY_PREFIX . ".$savepointName.$tableName";
    }

    /**
     * @param string $savepointName
     * @return array
     */
    public function createSavepoint(string $savepointName): array
    {
        if ($savepointName === self::INITIAL_SAVEPOINT_NAME) {
            throw new Exception('Not allowed to use reserved savepoint name');
        }
        return $this->_createSavepoint($savepointName);
    }

    /**
     * @return array
     */
    public function createInitialSavepoint(): array
    {
        $this->_deleteSavepoint(self::INITIAL_SAVEPOINT_NAME);
        return $this->_createSavepoint(self::INITIAL_SAVEPOINT_NAME);
    }

    /**
     * @param string $savepointName
     * @return array
     */
    private function _createSavepoint(string $savepointName): array
    {
        $conn = $this->getConnection();

        $tables = [];
        $allTables = $this->getSchemaManager()->listTables();
        foreach ($allTables as $table) {
            $tableName = $table->getName();
            $results = $conn->fetchAllAssociative("SELECT * FROM `$tableName`");

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->getCache()->getItem($this->genSavepointCacheKey($savepointName, $tableName));
            if ($cacheItem->isHit()) {
                throw new Exception('Savepoint already created for the given name');
            }

            $this->getCache()->get(
                $this->genSavepointCacheKey($savepointName, $tableName),
                function () use ($tableName, $results) {
                    return ['tableName' => $tableName, 'data' => $results];
                }
            );

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
        $conn = $this->getConnection();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        $this->beginTransaction();
        try {
            $tables = [];
            $allTables = $this->getSchemaManager()->listTables();
            foreach ($allTables as $table) {
                $tableName = $table->getName();
                /** @var CacheItem $cacheItem */
                $cacheItem = $this->getCache()->getItem($this->genSavepointCacheKey($savepointName, $tableName));
                if (!$cacheItem->isHit()) {
                    throw new Exception('No savepoint for the given name');
                }

                $conn->executeStatement("DELETE FROM `$tableName`");
                $data = $cacheItem->get()['data'];
                foreach ($data as $row) {
                    $conn->insert($tableName, $row);
                }
                $tables[] = [$table, count($data)];
            }
            $this->commitTransaction();

            return $tables;
        } catch (Exception $e) {
            $this->rollBackTransaction();
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
        if ($savepointName === self::INITIAL_SAVEPOINT_NAME) {
            throw new Exception('Not allowed to delete reserved savepoint name');
        }
        return $this->_deleteSavepoint($savepointName);
    }

    /**
     * @param string $savepointName
     * @return bool
     */
    private function _deleteSavepoint(string $savepointName): bool
    {
        return $this->getCache()->clear($this->genSavepointCacheKey($savepointName, null));
    }
}
