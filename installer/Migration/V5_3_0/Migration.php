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

namespace OrangeHRM\Installer\Migration\V5_3_0;

use Exception;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\LangStringHelper;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;
    private DateTimeZone $utcTimeZone;
    public const CONFLICTING_FOREIGN_KEY_TABLES = [
        'ohrm_buzz_comment',
        'ohrm_buzz_like_on_comment',
        'ohrm_buzz_like_on_share',
        'ohrm_buzz_photo',
        'ohrm_buzz_post',
        'ohrm_buzz_share',
    ];

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $this->updateLangStringVersion('5.2.0');
        $this->getLangHelper()->deleteLangStringByUnitId(
            'getting_started_with_orangehrm',
            $this->getLangHelper()->getGroupIdByName('help')
        );
        $this->getLangStringHelper()->deleteNonCustomizedLangStrings('buzz');
        $oldGroups = ['buzz', 'general', 'dashboard', 'help'];
        foreach ($oldGroups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }
        $this->updateLangStringVersion($this->getVersion());

        $this->modifyBuzzTables();
        Logger::getLogger()->info('Server timezone: ' . (new DateTime())->getTimezone()->getName());
        $this->convertBuzzCommentTableTimesToUTC();
        $this->convertBuzzLikeOnCommentTableTimesToUTC();
        $this->convertBuzzLikeOnShareTableTimesToUTC();
        $this->convertBuzzPostTableTimesToUTC();
        $this->convertBuzzShareTableTimesToUTC();
        $this->changeBuzzTablesDateTimeColumnsAsNotNull();
        $this->createColumnToStoreBuzzOriginalVideoLink();

        Logger::getLogger()->info('Deleting legacy Buzz config values');
        $this->getConfigHelper()->deleteConfigValue('buzz_comment_text_lenth');
        $this->getConfigHelper()->deleteConfigValue('buzz_cookie_valid_time');
        $this->getConfigHelper()->deleteConfigValue('buzz_image_max_dimension');
        $this->getConfigHelper()->deleteConfigValue('buzz_initial_comments');
        $this->getConfigHelper()->deleteConfigValue('buzz_like_count');
        $this->getConfigHelper()->deleteConfigValue('buzz_max_notification_period');
        $this->getConfigHelper()->deleteConfigValue('buzz_most_like_posts');
        $this->getConfigHelper()->deleteConfigValue('buzz_most_like_shares');
        $this->getConfigHelper()->deleteConfigValue('buzz_post_text_lenth');
        $this->getConfigHelper()->deleteConfigValue('buzz_post_text_lines');
        $this->getConfigHelper()->deleteConfigValue('buzz_refresh_time');
        $this->getConfigHelper()->deleteConfigValue('buzz_share_count');
        $this->getConfigHelper()->deleteConfigValue('buzz_time_format');
        $this->getConfigHelper()->deleteConfigValue('buzz_viewmore_comment');
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

    private function createColumnToStoreBuzzOriginalVideoLink(): void
    {
        $this->getSchemaHelper()->addColumn(
            'ohrm_buzz_link',
            'original_link',
            Types::TEXT,
            ['Notnull' => false, 'Default' => null]
        );

        $table = 'ohrm_buzz_link';
        $count = $this->getTableRecordCount($table);
        $batchSize = 10;
        for ($i = 0; $i <= $count; $i = $i + $batchSize) {
            $result = $this->createQueryBuilder()
                ->select('buzzLink.id', 'buzzLink.post_id', 'buzzPost.text')
                ->from($table, 'buzzLink')
                ->leftJoin('buzzLink', 'ohrm_buzz_post', 'buzzPost', 'buzzPost.id = buzzLink.post_id')
                ->setFirstResult($i)
                ->setMaxResults($batchSize)
                ->executeQuery();

            $postIds = [];
            foreach ($result->fetchAllAssociative() as $row) {
                $postIds[] = $row['post_id'];
                $this->createQueryBuilder()
                    ->update($table, 'buzz')
                    ->set('buzz.original_link', ':originalLink')
                    ->where('buzz.id = :id')
                    ->setParameter('id', $row['id'])
                    ->setParameter('originalLink', $row['text'])
                    ->executeStatement();
            }

            $q = $this->createQueryBuilder()
                ->update('ohrm_buzz_post')
                ->set('ohrm_buzz_post.text', ':newValue');
            $q->where($q->expr()->in('ohrm_buzz_post.id', ':postIds'))
                ->setParameter('postIds', $postIds, Connection::PARAM_INT_ARRAY)
                ->setParameter('newValue', null)
                ->executeStatement();

            $result->free();
        }
    }

    private function modifyBuzzTables(): void
    {
        $this->getSchemaManager()->dropTable('ohrm_buzz_unlike_on_comment');
        $this->getSchemaManager()->dropTable('ohrm_buzz_unlike_on_share');
        $this->getSchemaHelper()->dropColumn('ohrm_buzz_share', 'number_of_unlikes');
        $this->getSchemaHelper()->dropColumn('ohrm_buzz_comment', 'number_of_unlikes');
        $this->getSchemaHelper()->dropColumns('ohrm_buzz_link', ['type', 'title', 'description']);

        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_buzz_post CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_buzz_share CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_buzz_comment CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $conflictingConstraints = $this->getConflictingForeignKeys();
        $droppedConstraintNames = $this->removeConflictingForeignKeys($conflictingConstraints);
        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_comment', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'comment_text' => [
                'Notnull' => false,
                'Default' => null,
                'Length' => 16_777_215, // MEDIUMTEXT
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
            'comment_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'updated_at' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'comment_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'updated_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_like_on_comment', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'like_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'like_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_like_on_share', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'like_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'like_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_photo', [
            'photo' => [
                'Notnull' => false,
                'Default' => null,
                'Type' => Type::getType(Types::BLOB),
                'Length' => 16_777_215 // mediumblob
            ],
            'filename' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'file_type' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'size' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'width' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'height' => [
                'Notnull' => false,
                'Default' => null,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_post', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'text' => [
                'Notnull' => false,
                'Default' => null,
                'Length' => 16_777_215, // MEDIUMTEXT
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
            'post_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'updated_at' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'post_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'updated_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_share', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'type' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::SMALLINT),
            ],
            'text' => [
                'Notnull' => false,
                'Default' => null,
                'Length' => 16_777_215, // MEDIUMTEXT
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
            'share_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'updated_at' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'share_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
            'updated_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => false,
                'Default' => null,
            ],
        ]);
        $this->recreateRemovedForeignKeys($conflictingConstraints, $droppedConstraintNames);
    }

    /**
     * @return array
     */
    private function getConflictingForeignKeys(): array
    {
        $foreignKeyArray = [];
        foreach (self::CONFLICTING_FOREIGN_KEY_TABLES as $table) {
            $tableDetails = $this->getSchemaManager()->listTableDetails($table);
            $foreignKeys = $tableDetails->getForeignKeys();
            foreach ($foreignKeys as $constraintName => $constraint) {
                if ($constraint->getForeignTableName() == 'hs_hr_employee') {
                    $foreignKeyArray[$constraintName] = ['constraint' => $constraint, 'localTable' => $table];
                }
            }
        }
        return $foreignKeyArray;
    }

    /**
     * @param array $conflictingConstraints
     * @return String[]
     */
    private function removeConflictingForeignKeys(array $conflictingConstraints): array
    {
        $droppedConstraintNames = [];
        foreach ($conflictingConstraints as $constraintName => $conflictingConstraint) {
            try {
                $this->getSchemaHelper()->dropForeignKeys($conflictingConstraint['localTable'], [$constraintName]);
                $droppedConstraintNames[] = $constraintName;
            } catch (Exception $exception) {
                Logger::getLogger()->error($exception->getMessage());
            }
        }
        return $droppedConstraintNames;
    }

    /**
     * @param array $conflictingConstraints
     * @param String[] $droppedConstraintNames
     */
    private function recreateRemovedForeignKeys(array $conflictingConstraints, array $droppedConstraintNames): void
    {
        foreach ($conflictingConstraints as $constraintName =>  $conflictingConstraint) {
            if (in_array($constraintName, $droppedConstraintNames)) {
                $this->getSchemaHelper()->addForeignKey($conflictingConstraint['localTable'], $conflictingConstraint['constraint']);
            }
        }
    }

    private function changeBuzzTablesDateTimeColumnsAsNotNull(): void
    {
        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_comment', [
            'comment_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => true,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_like_on_comment', [
            'like_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => true,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_like_on_share', [
            'like_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => true,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_post', [
            'post_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => true,
            ],
        ]);

        $this->getSchemaHelper()->addOrChangeColumns('ohrm_buzz_share', [
            'share_utc_time' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'Notnull' => true,
            ],
        ]);
    }

    private function convertBuzzCommentTableTimesToUTC(): void
    {
        $table = 'ohrm_buzz_comment';
        $count = $this->getTableRecordCount($table);
        $batchSize = 10;
        for ($i = 0; $i <= $count; $i = $i + $batchSize) {
            $result = $this->createQueryBuilder()
                ->select('buzz.id', 'buzz.comment_time', 'buzz.updated_at')
                ->from($table, 'buzz')
                ->setFirstResult($i)
                ->setMaxResults($batchSize)
                ->executeQuery();

            foreach ($result->fetchAllAssociative() as $row) {
                $this->createQueryBuilder()
                    ->update($table, 'buzz')
                    ->set('buzz.comment_utc_time', ':commentUtcTime')
                    ->set('buzz.updated_utc_time', ':updatedUtcTime')
                    ->where('buzz.id = :id')
                    ->setParameter('id', $row['id'])
                    ->setParameter(
                        'commentUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['comment_time'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->setParameter(
                        'updatedUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['updated_at'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->executeStatement();
            }

            $result->free();
        }
    }

    private function convertBuzzLikeOnCommentTableTimesToUTC(): void
    {
        $table = 'ohrm_buzz_like_on_comment';
        $count = $this->getTableRecordCount($table);
        $batchSize = 10;
        for ($i = 0; $i <= $count; $i = $i + $batchSize) {
            $result = $this->createQueryBuilder()
                ->select('buzz.id', 'buzz.like_time')
                ->from($table, 'buzz')
                ->setFirstResult($i)
                ->setMaxResults($batchSize)
                ->executeQuery();

            foreach ($result->fetchAllAssociative() as $row) {
                $this->createQueryBuilder()
                    ->update($table, 'buzz')
                    ->set('buzz.like_utc_time', ':likeUtcTime')
                    ->where('buzz.id = :id')
                    ->setParameter('id', $row['id'])
                    ->setParameter(
                        'likeUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['like_time'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->executeStatement();
            }

            $result->free();
        }
    }

    private function convertBuzzLikeOnShareTableTimesToUTC(): void
    {
        $table = 'ohrm_buzz_like_on_share';
        $count = $this->getTableRecordCount($table);
        $batchSize = 10;
        for ($i = 0; $i <= $count; $i = $i + $batchSize) {
            $result = $this->createQueryBuilder()
                ->select('buzz.id', 'buzz.like_time')
                ->from($table, 'buzz')
                ->setFirstResult($i)
                ->setMaxResults($batchSize)
                ->executeQuery();

            foreach ($result->fetchAllAssociative() as $row) {
                $this->createQueryBuilder()
                    ->update($table, 'buzz')
                    ->set('buzz.like_utc_time', ':likeUtcTime')
                    ->where('buzz.id = :id')
                    ->setParameter('id', $row['id'])
                    ->setParameter(
                        'likeUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['like_time'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->executeStatement();
            }
            $result->free();
        }
    }

    private function convertBuzzPostTableTimesToUTC(): void
    {
        $table = 'ohrm_buzz_post';
        $count = $this->getTableRecordCount($table);
        $batchSize = 10;
        for ($i = 0; $i <= $count; $i = $i + $batchSize) {
            $result = $this->createQueryBuilder()
                ->select('buzz.id', 'buzz.post_time', 'buzz.updated_at')
                ->from($table, 'buzz')
                ->setFirstResult($i)
                ->setMaxResults($batchSize)
                ->executeQuery();

            foreach ($result->fetchAllAssociative() as $row) {
                $this->createQueryBuilder()
                    ->update($table, 'buzz')
                    ->set('buzz.post_utc_time', ':postUtcTime')
                    ->set('buzz.updated_utc_time', ':updatedUtcTime')
                    ->where('buzz.id = :id')
                    ->setParameter('id', $row['id'])
                    ->setParameter(
                        'postUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['post_time'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->setParameter(
                        'updatedUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['updated_at'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->executeStatement();
            }

            $result->free();
        }
    }

    private function convertBuzzShareTableTimesToUTC(): void
    {
        $table = 'ohrm_buzz_share';
        $count = $this->getTableRecordCount($table);
        $batchSize = 10;
        for ($i = 0; $i <= $count; $i = $i + $batchSize) {
            $result = $this->createQueryBuilder()
                ->select('buzz.id', 'buzz.share_time', 'buzz.updated_at')
                ->from($table, 'buzz')
                ->setFirstResult($i)
                ->setMaxResults($batchSize)
                ->executeQuery();

            foreach ($result->fetchAllAssociative() as $row) {
                $this->createQueryBuilder()
                    ->update($table, 'buzz')
                    ->set('buzz.share_utc_time', ':shareUtcTime')
                    ->set('buzz.updated_utc_time', ':updatedUtcTime')
                    ->where('buzz.id = :id')
                    ->setParameter('id', $row['id'])
                    ->setParameter(
                        'shareUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['share_time'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->setParameter(
                        'updatedUtcTime',
                        $this->convertServerTimeToUTCTime(new DateTime($row['updated_at'])),
                        Types::DATETIME_MUTABLE
                    )
                    ->executeStatement();
            }

            $result->free();
        }
    }

    /**
     * @param string $tableName
     * @return int
     */
    private function getTableRecordCount(string $tableName): int
    {
        $count = $this->createQueryBuilder()
            ->select("COUNT($tableName.id)")
            ->from($tableName)
            ->executeQuery()
            ->fetchOne();
        Logger::getLogger()->info("`$tableName` record count: $count");
        return $count;
    }

    /**
     * @param DateTime $dateTime
     * @return DateTime
     */
    private function convertServerTimeToUTCTime(DateTime $dateTime): DateTime
    {
        return $dateTime->setTimezone($this->getUTCTimeZone());
    }

    /**
     * @return DateTimeZone
     */
    private function getUTCTimeZone(): DateTimeZone
    {
        return $this->utcTimeZone ??= new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.3.0';
    }

    /**
     * @return LangStringHelper
     */
    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }
}
