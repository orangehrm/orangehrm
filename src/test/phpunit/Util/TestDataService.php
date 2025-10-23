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

namespace OrangeHRM\Tests\Util;

use DateTime;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\MappingException;
use Exception;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\ListSorter;
use PDO;
use Symfony\Component\Yaml\Yaml;

class TestDataService
{
    /** Encrypted fields in the format
     *        array('Model1' => array(field1, field2),
     *              'Model2' => array(field1, field2))
     */
    private static $encryptedModels = []; //array('EmployeeSalary' => array('amount'));

    private static array $data = [];
    private static array $tableNames;
    private static ?string $lastFixture = null;
    private static ?array $insertQueryCache = null;
    private static array $parsedFixtureFiles = [];

    /**
     * @param string $fixture
     */
    public static function populate(string $fixture, bool $resetCache = false): void
    {
        if ($resetCache) {
            self::$tableNames = [];
            self::$lastFixture = null;
        }
        $pathToFixtures = realpath($fixture);
        if (!$pathToFixtures) {
            throw new Exception(sprintf("Couldn't find fixture file in %s", $fixture));
        }
        self::_populateUsingPdoTransaction($pathToFixtures);
    }

    /**
     * @param string $fixture
     * @throws MappingException
     */
    private static function _populateUsingPdoTransaction(string $fixture): void
    {
        // Don't reload already loaded fixtures
        $useCache = true;
        if (self::$lastFixture != $fixture) {
            self::_setData($fixture);
            self::$lastFixture = $fixture;
            $useCache = false;
            self::$insertQueryCache = null;
        }

        self::_disableConstraints();

        self::_truncateTables();

        $pdo = self::_getDbConnection();
        $query = '';
        $pdo->beginTransaction();
        try {
            if ($useCache) {
                foreach (self::$insertQueryCache as $query) {
                    $pdo->exec($query);
                }
            } else {
                self::$insertQueryCache = [];

                foreach (self::$data as $tableName => $tableData) {
                    $queryArray = self::_generatePdoInsertQueryArray($tableName, $tableData);
                    self::$insertQueryCache = array_merge(self::$insertQueryCache, $queryArray);

                    foreach ($queryArray as $query) {
                        $pdo->exec($query);
                    }
                }
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo __FILE__ . ':' . __LINE__ . "\n Transaction failed: " . $e->getMessage() .
                "\nQuery: [" . $query . "]\n" . 'Fixture: ' . $fixture . "\n\n";
        }

        self::_enableConstraints();
    }

    /**
     * @param string $tableAlias
     * @param array $tableData
     * @return string[]
     */
    private static function _generatePdoInsertQueryArray(string $tableAlias, array $tableData): array
    {
        return self::_generateMultipleInsertQueryArray($tableAlias, $tableData);
        /* Multiple inserts have to be used since some fixtures contains different no
         * of columns for same data set */
    }

    /**
     * @param string $tableAlias
     * @param array $tableData
     * @return string[]
     */
    private static function _generateMultipleInsertQueryArray(string $tableAlias, array $tableData): array
    {
        $tableName = self::_getTableName($tableAlias);
        $queryArray = [];

        if (!empty($tableData)) {
            foreach ($tableData as $item) {
                $columnString = self::_generateInsetQueryColumnString($item, $tableAlias);
                $item = array_map(
                    function ($value) {
                        if (is_bool($value)) {
                            return (int)$value;
                        }
                        return is_null($value) ? 'NULL' : self::_getDbConnection()->quote($value);
                    },
                    $item
                );
                $queryArray[] = "INSERT INTO `$tableName` $columnString VALUES (" . implode(', ', $item) . ')';
            }
        }

        $queryArray[] = 'UPDATE `hs_hr_unique_id` SET `last_id` = ' . count($tableData) .
            " WHERE `table_name` = '$tableName'";

        return $queryArray;
    }

    /**
     * @param array $dataArray
     * @param string $tableAlias
     * @return string
     */
    private static function _generateInsetQueryColumnString(array $dataArray, string $tableAlias): string
    {
        $columnString = '(';

        $count = count($dataArray);
        $i = 1;

        foreach ($dataArray as $key => $value) {
            $columnName = self::_getClassMetadata($tableAlias)->getColumnName($key);

            /* Had to remove backtick (`) since hs_hr_config's "key" column contains them */
            if ($i < $count) {
                $columnString .= "$columnName, ";
            } else {
                $columnString .= "$columnName";
            }

            $i++;
        }

        $columnString .= ')';

        return $columnString;
    }

    /**
     * @param string $tableAlias
     * @return string
     */
    private static function _getTableName(string $tableAlias): string
    {
        return self::_getClassMetadata($tableAlias)->getTableName();
    }

    /**
     * @param string $tableAlias
     * @return ClassMetadata
     */
    private static function _getClassMetadata(string $tableAlias): ClassMetadata
    {
        $entityName = self::getFQEntityName($tableAlias);
        return Doctrine::getEntityManager()->getClassMetadata($entityName);
    }

    /**
     * @param string $fixture
     */
    private static function _setData(string $fixture): void
    {
        self::$data = Yaml::parseFile($fixture);
        self::_encryptFieldsInFixture();
        self::_setTableNames();
    }

    /**
     * If configured to encrypt data, encrypt fields in fixture.
     */
    private static function _encryptFieldsInFixture()
    {
        // TODO
        foreach (self::$encryptedModels as $model => $fields) {
            if (isset(self::$data[$model]) && \KeyHandler::keyExists()) {
                foreach (self::$data[$model] as $id => $row) {
                    foreach ($fields as $field) {
                        self::$data[$model][$id][$field] = \Cryptographer::encrypt($row[$field]);
                    }
                }
            }
        }
    }

    /**
     * @param string $className
     * @return string fully-qualified class name without a leading backslash
     */
    private static function getFQEntityName(string $className): string
    {
        if (class_exists($className)) {
            return $className;
        }
        return "OrangeHRM\Entity\\" . $className;
    }

    private static function _setTableNames(): void
    {
        foreach (self::$data as $key => $value) {
            try {
                self::$tableNames[] = self::_getTableName($key);
            } catch (MappingException $e) {
                echo __FILE__ . ':' . __LINE__ . ') Skipping unknown table alias: ' . $key .
                    "\n" . 'Fixture: ' . self::$lastFixture . "\n\n";
            }
        }
    }

    private static function _disableConstraints(): void
    {
        $pdo = self::_getDbConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
    }

    private static function _enableConstraints(): void
    {
        $pdo = self::_getDbConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @param string[]|null $tableNames
     */
    private static function _truncateTables(?array $tableNames = null): void
    {
        if (is_null($tableNames)) {
            $tableNames = self::$tableNames;
        }

        if (count($tableNames) > 0) {
            $pdo = self::_getDbConnection();
            self::_disableConstraints();
            $query = '';

            try {
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                Doctrine::getEntityManager()->beginTransaction();

                foreach ($tableNames as $tableName) {
                    $query = 'DELETE FROM ' . $tableName;
                    $pdo->query($query);
                    $query = 'ALTER TABLE `' . $tableName . '` AUTO_INCREMENT = 1';
                    $pdo->query($query);
                }

                $query = "UPDATE hs_hr_unique_id SET last_id = 0 WHERE table_name in ('" .
                    implode("','", $tableNames) . "')";
                $pdo->exec($query);

                Doctrine::getEntityManager()->commit();
            } catch (Exception $e) {
                Doctrine::getEntityManager()->rollback();
                echo __FILE__ . ':' . __LINE__ . "\n Transaction failed: " . $e->getMessage() .
                    "\nQuery: [" . $query . "]\n" . 'Fixture: ' . self::$lastFixture . "\n\n";
            } finally {
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            }

            // Clear table cache
            if (is_array(self::$data)) {
                foreach (self::$data as $alias => $values) {
                    Doctrine::getEntityManager()->clear();
                }
            }

            self::_enableConstraints();
        }
    }

    /**
     * @return PDO
     */
    private static function _getDbConnection(): PDO
    {
        return Doctrine::getEntityManager()->getConnection()->getNativeConnection();
    }

    /**
     * @param array $aliasArray
     * @deprecated
     */
    public static function truncateTables(array $aliasArray): void
    {
        foreach ($aliasArray as $alias) {
            try {
                self::$tableNames[] = self::_getTableName($alias);
            } catch (MappingException $e) {
                echo __FILE__ . ':' . __LINE__ . ') Skipping unknown table alias: ' . $alias . "\n";
            }
        }

        self::_truncateTables();
    }

    /**
     * @param string[] $aliasArray
     */
    public static function truncateSpecificTables(array $aliasArray): void
    {
        $tableNames = [];

        foreach ($aliasArray as $alias) {
            try {
                $tableNames[] = self::_getTableName($alias);
                Doctrine::getEntityManager()->clear();
            } catch (MappingException $e) {
                echo __FILE__ . ':' . __LINE__ . ') Skipping unknown table alias: ' . $alias . "\n";
            }
        }

        self::_truncateTables($tableNames);
    }

    /**
     * @param string $alias
     * @param int $count
     * @return array
     *
     * @template T
     * @psalm-param class-string<T> $alias
     * @psalm-return T[]
     */
    public static function fetchLastInsertedRecords(string $alias, int $count): array
    {
        $entityName = self::getFQEntityName($alias);
        $wholeCount = Doctrine::getEntityManager()->getRepository($entityName)->count([]);
        $offset = $wholeCount - $count;

        $q = Doctrine::getEntityManager()->getRepository($entityName)->createQueryBuilder('a');
        $q->setFirstResult($offset);
        $q->setMaxResults($count);

        return $q->getQuery()->execute();
    }

    /**
     * @param string $alias
     * @param string $orderBy
     * @param bool $clearEntities
     * @return object|null
     *
     * @template T
     * @psalm-param class-string<T> $alias
     * @psalm-return ?T
     */
    public static function fetchLastInsertedRecord(string $alias, string $orderBy, bool $clearEntities = true): ?object
    {
        $entityName = self::getFQEntityName($alias);
        if ($clearEntities) {
            Doctrine::getEntityManager()->clear();
        }
        $q = Doctrine::getEntityManager()->getRepository($entityName)->createQueryBuilder('a');
        $q->setMaxResults(1);
        if (substr($orderBy, 0, 2) !== 'a.') {
            $orderBy = 'a.' . $orderBy;
        }
        $q->orderBy($orderBy, ListSorter::DESCENDING);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $alias
     * @param string|int $primaryKey
     * @return object|null
     *
     * @template T
     * @psalm-param class-string<T> $alias
     * @psalm-return ?T
     */
    public static function fetchObject(string $alias, $primaryKey): ?object
    {
        $entityName = self::getFQEntityName($alias);
        Doctrine::getEntityManager()->clear();
        return Doctrine::getEntityManager()->find($entityName, $primaryKey);
    }

    /**
     * @param string $fixture
     * @return array
     */
    private static function parseFixtureFile(string $fixture): array
    {
        // TODO: evaluate memory usage
        if (!isset($parsedFixtureFiles[$fixture])) {
            $parsedFixtureFiles[$fixture] = Yaml::parseFile($fixture);
        }
        return $parsedFixtureFiles[$fixture];
    }

    /**
     * @param string $fixture
     * @param string $key
     * @return array
     */
    public static function loadFixtures(string $fixture, string $key): array
    {
        $data = self::parseFixtureFile($fixture);
        if (!isset($data[$key])) {
            throw new Exception("Key `$key` not found in fixture `$fixture`");
        }
        return $data[$key];
    }

    /**
     * @template T
     * @psalm-param class-string<T> $alias
     * @psalm-return T[]
     */
    public static function loadObjectList(string $alias, string $fixture, string $key): array
    {
        $data = self::parseFixtureFile($fixture);

        return self::loadObjectListFromArray($alias, $data[$key]);
    }

    /**
     * @param string $alias
     * @param array|object[] $data
     * @return array|object[]
     *
     * @template T
     * @psalm-param class-string<T> $alias
     * @psalm-return T[]
     */
    public static function loadObjectListFromArray(string $alias, array $data): array
    {
        $objectList = [];
        $classMetadata = self::_getClassMetadata($alias);

        foreach ($data as $row) {
            $entityName = self::getFQEntityName($alias);
            $object = new $entityName();

            foreach ($row as $attribute => $value) {
                $setMethodName = 'set' . ucfirst($attribute);

                $fieldName = self::getFieldForColumn($classMetadata, $attribute);
                if ($fieldName) {
                    $associationMapping = self::getAssociationMapping($classMetadata, $fieldName);
                } else {
                    $associationMapping = self::getAssociationMapping($classMetadata, $attribute);
                }

                if ($associationMapping) {
                    $value = Doctrine::getEntityManager()->getReference($associationMapping['targetEntity'], $value);
                }

                if (!method_exists($object, $setMethodName)) {
                    if ($attribute) {
                        $setMethodName = 'set' . ucfirst($fieldName);
                    }
                }
                $fieldType = self::getTypeForField($classMetadata, $attribute);
                if (($fieldType === 'date' || $fieldType === 'datetime' || $fieldType === 'time') && !$value instanceof DateTime) {
                    $value = new DateTime($value);
                }
                $object->$setMethodName($value);
            }

            $objectList[] = $object;
        }

        return $objectList;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $columnName
     * @return string|null
     */
    private static function getFieldForColumn(ClassMetadata $classMetadata, string $columnName): ?string
    {
        try {
            return $classMetadata->getFieldForColumn($columnName);
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            return null;
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $alias
     * @return string|null
     */
    private static function getTypeForField(ClassMetadata $classMetadata, string $alias): ?string
    {
        try {
            $fieldName = self::getFieldForColumn($classMetadata, $alias);
            $fieldMapping = $classMetadata->getFieldMapping($fieldName ?? $alias);
            if ($fieldMapping) {
                return $fieldMapping['type'];
            }
            return null;
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            return null;
        }
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $fieldName
     * @return array|null
     */
    private static function getAssociationMapping(ClassMetadata $classMetadata, string $fieldName): ?array
    {
        try {
            return $classMetadata->getAssociationMapping($fieldName);
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            return null;
        }
    }
}
