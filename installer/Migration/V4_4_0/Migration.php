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

namespace OrangeHRM\Installer\Migration\V4_4_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_post'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_post')
                ->addColumn('id', Types::BIGINT, ['Autoincrement' => true])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('text', Types::TEXT)
                ->addColumn('post_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->addColumn('updated_at', Types::DATETIMETZ_MUTABLE, ['CustomSchemaOptions' => ['onUpdate' => 'CURRENT_TIMESTAMP']])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzPostEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_post', $foreignKeyConstraint);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_share'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_share')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('post_id', Types::BIGINT, ['Length' => 20, 'Notnull' => true])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('number_of_likes', Types::INTEGER, ['Length' => 6, 'Default' => null, 'Notnull' => false])
                ->addColumn('number_of_unlikes', Types::INTEGER, ['Length' => 6, 'Default' => null, 'Notnull' => false])
                ->addColumn('number_of_comments', Types::INTEGER, ['Length' => 6, 'Default' => null, 'Notnull' => false])
                ->addColumn('share_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->addColumn('type', Types::SMALLINT, ['Default' => null, 'Notnull' => false])
                ->addColumn('text', Types::TEXT)
                ->addColumn('updated_at', Types::DATETIMETZ_MUTABLE, ['CustomSchemaOptions' => ['onUpdate' => 'CURRENT_TIMESTAMP']])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $postId = new Index(
            'post_id',
            ['post_id']
        );
        $this->getSchemaManager()->createIndex($postId, 'ohrm_buzz_share');
        $employeeNumber = new Index(
            'employee_number',
            ['employee_number']
        );
        $this->getSchemaManager()->createIndex($employeeNumber, 'ohrm_buzz_share');
        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzShareEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_share', $foreignKeyConstraint);

        $foreignKeyConstraint2 = new ForeignKeyConstraint(
            ['post_id'],
            'ohrm_buzz_post',
            ['id'],
            'buzzSharePost',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_share', $foreignKeyConstraint2);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_comment'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_comment')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('share_id', Types::BIGINT, ['Length' => 20])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('number_of_likes', Types::INTEGER, ['Length' => 6, 'Default' => null, 'Notnull' => false])
                ->addColumn('number_of_unlikes', Types::INTEGER, ['Length' => 6, 'Default' => null, 'Notnull' => false])
                ->addColumn('comment_text', Types::TEXT)
                ->addColumn('comment_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->addColumn('updated_at', Types::DATETIMETZ_MUTABLE, ['CustomSchemaOptions' => ['onUpdate' => 'CURRENT_TIMESTAMP']])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $postId = new Index(
            'share_id',
            ['share_id']
        );
        $this->getSchemaManager()->createIndex($postId, 'ohrm_buzz_comment');
        $employeeNumber = new Index(
            'employee_number',
            ['employee_number']
        );
        $this->getSchemaManager()->createIndex($employeeNumber, 'ohrm_buzz_comment');

        $foreignKeyConstraint3 = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzComentedEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_comment', $foreignKeyConstraint3);

        $foreignKeyConstraint4 = new ForeignKeyConstraint(
            ['share_id'],
            'ohrm_buzz_share',
            ['id'],
            'buzzComentOnShare',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_comment', $foreignKeyConstraint4);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_like_on_comment'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_like_on_comment')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('comment_id', Types::BIGINT, ['Length' => 20, 'Notnull' => true])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('like_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $commentId = new Index(
            'comment_id',
            ['comment_id']
        );
        $this->getSchemaManager()->createIndex($commentId, 'ohrm_buzz_like_on_comment');
        $employeeNumber = new Index(
            'employee_number',
            ['employee_number']
        );
        $this->getSchemaManager()->createIndex($employeeNumber, 'ohrm_buzz_like_on_comment');

        $foreignKeyConstraint5 = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzCommentLikeEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_like_on_comment', $foreignKeyConstraint5);
        $foreignKeyConstraint6 = new ForeignKeyConstraint(
            ['comment_id'],
            'ohrm_buzz_comment',
            ['id'],
            'buzzLikeOnComment',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_like_on_comment', $foreignKeyConstraint6);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_like_on_share'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_like_on_share')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('share_id', Types::BIGINT, ['Length' => 20])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('like_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $shareId = new Index(
            'share_id',
            ['share_id']
        );
        $this->getSchemaManager()->createIndex($shareId, 'ohrm_buzz_like_on_share');
        $employeeNumber = new Index(
            'employee_number',
            ['employee_number']
        );
        $this->getSchemaManager()->createIndex($employeeNumber, 'ohrm_buzz_like_on_share');
        $foreignKeyConstraint11 = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzShareLikeEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_like_on_share', $foreignKeyConstraint11);

        $foreignKeyConstraint12 = new ForeignKeyConstraint(
            ['share_id'],
            'ohrm_buzz_share',
            ['id'],
            'buzzLikeOnshare',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_like_on_share', $foreignKeyConstraint12);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_photo'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_photo')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('post_id', Types::BIGINT, ['Length' => 20])
                ->addColumn('photo', Types::BLOB, ['Length' => 7]) //change
                ->addColumn('filename', Types::STRING, ['Length' => 100])
                ->addColumn('file_type', Types::STRING, ['Length' => 50])
                ->addColumn('size', Types::STRING, ['Length' => 20])
                ->addColumn('width', Types::STRING, ['Length' => 20])
                ->addColumn('height', Types::STRING, ['Length' => 20])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $attachmentId = new Index(
            'attachment_id',
            ['post_id']
        );
        $this->getSchemaManager()->createIndex($attachmentId, 'ohrm_buzz_photo');
        $foreignKeyConstraint7 = new ForeignKeyConstraint(
            ['post_id'],
            'ohrm_buzz_post',
            ['id'],
            'photoAttached',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_photo', $foreignKeyConstraint7);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_link'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_link')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('post_id', Types::BIGINT, ['Length' => 20])
                ->addColumn('link', Types::TEXT, ['Notnull' => true])
                ->addColumn('type', Types::SMALLINT, ['Length' => 2, 'Default' => null])
                ->addColumn('title', Types::STRING, ['Length' => 600])
                ->addColumn('description', Types::TEXT)
                ->setPrimaryKey(['id'])
                ->create();
        }
        $attachmentId = new Index(
            'attachment_id',
            ['post_id']
        );
        $this->getSchemaManager()->createIndex($attachmentId, 'ohrm_buzz_link');
        $photoId = new Index(
            'photo_id',
            ['post_id']
        );
        $this->getSchemaManager()->createIndex($photoId, 'ohrm_buzz_link');
        $foreignKeyConstraint8 = new ForeignKeyConstraint(
            ['post_id'],
            'ohrm_buzz_post',
            ['id'],
            'linkAttached',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_link', $foreignKeyConstraint8);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_unlike_on_comment'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_unlike_on_comment')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('comment_id', Types::BIGINT, ['Length' => 20, 'Notnull' => true])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('like_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $commentId = new Index(
            'comment_id',
            ['comment_id']
        );
        $this->getSchemaManager()->createIndex($commentId, 'ohrm_buzz_unlike_on_comment');
        $employeeNumber = new Index(
            'employee_number',
            ['employee_number']
        );
        $this->getSchemaManager()->createIndex($employeeNumber, 'ohrm_buzz_unlike_on_comment');

        $foreignKeyConstraint9 = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzCommentUnLikeEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_unlike_on_comment', $foreignKeyConstraint9);
        $foreignKeyConstraint10 = new ForeignKeyConstraint(
            ['comment_id'],
            'ohrm_buzz_comment',
            ['id'],
            'buzzUnLikeOnComment',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_unlike_on_comment', $foreignKeyConstraint10);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_unlike_on_share'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_unlike_on_share')
                ->addColumn('id', Types::BIGINT, ['Length' => 20, 'Autoincrement' => true])
                ->addColumn('share_id', Types::BIGINT, ['Length' => 20])
                ->addColumn('employee_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('like_time', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->setPrimaryKey(['id'])
                ->create();
        }
        $shareId = new Index(
            'share_id',
            ['share_id']
        );
        $this->getSchemaManager()->createIndex($shareId, 'ohrm_buzz_unlike_on_share');
        $employeeNumber = new Index(
            'employee_number',
            ['employee_number']
        );
        $this->getSchemaManager()->createIndex($employeeNumber, 'ohrm_buzz_unlike_on_share');

        $foreignKeyConstraint13 = new ForeignKeyConstraint(
            ['employee_number'],
            'hs_hr_employee',
            ['emp_number'],
            'buzzShareUnLikeEmployee',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_unlike_on_share', $foreignKeyConstraint13);

        $foreignKeyConstraint14 = new ForeignKeyConstraint(
            ['share_id'],
            'ohrm_buzz_share',
            ['id'],
            'buzzUNLikeOnshare',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_unlike_on_share', $foreignKeyConstraint14);

        if (!$this->getSchemaHelper()->tableExists(['ohrm_buzz_notification_metadata'])) {
            $this->getSchemaHelper()->createTable('ohrm_buzz_notification_metadata')
                ->addColumn('emp_number', Types::INTEGER, ['Length' => 7])
                ->addColumn('last_notification_view_time', Types::DATETIME_MUTABLE, ['Default' => null, 'Notnull' => false])
                ->addColumn('last_buzz_view_time', Types::DATETIME_MUTABLE, ['Default' => null, 'Notnull' => false])
                ->addColumn('last_clear_notifications', Types::DATETIME_MUTABLE, ['Default' => null, 'Notnull' => false])
                ->setPrimaryKey(['emp_number'])
                ->create();
        }

        $foreignKeyConstraint15 = new ForeignKeyConstraint(
            ['emp_number'],
            'hs_hr_employee',
            ['emp_number'],
            'notificationMetadata',
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_buzz_notification_metadata', $foreignKeyConstraint15);


        $this->insertConfig('buzz_refresh_time', '60000');
        $this->insertConfig('buzz_share_count', '10');
        $this->insertConfig('buzz_initial_comments', '2');
        $this->insertConfig('buzz_viewmore_comment', '5');
        $this->insertConfig('buzz_like_count', '5');
        $this->insertConfig('buzz_time_format', 'h:i a');
        $this->insertConfig('buzz_most_like_posts', '5');
        $this->insertConfig('buzz_post_text_lenth', '500');
        $this->insertConfig('buzz_post_text_lines', '5');
        $this->insertConfig('buzz_cookie_valid_time', '5000');
        $this->insertConfig('buzz_most_like_shares', '5');
        $this->insertConfig('buzz_image_max_dimension', '1024');

        $this->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status'
                ]
            )
            ->setParameter('name', 'buzz')
            ->setParameter('status', 1)
            ->executeQuery();

        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $this->insertConfig('buzz_comment_text_lenth', '250');

        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $screenId = $this->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('screen.name = :screenName')
            ->setParameter('screenName', 'Buzz')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('ohrm_menu_item')
            ->values(
                [
                    'menu_title' => ':menuTitle',
                    'screen_id' => ':screenId',
                    'parent_id' => ':ParentId',
                    'level' => ':level',
                    'order_hint' => ':orderHint',
                    'status' => ':status'
                ]
            )
            ->setParameter('menuTitle', 'Buzz')
            ->setParameter('screenId', $screenId)
            ->setParameter('ParentId', null)
            ->setParameter('level', 1)
            ->setParameter('orderHint', 1500)
            ->setParameter('status', 1)
            ->executeQuery();

        // i.e. -4 weeks, -2 days, -1 day, -1 month
        // https://www.php.net/manual/en/datetime.formats.relative.php
        $this->insertConfig('buzz_max_notification_period', '-1 week');
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    private function insertConfig(string $key, string $value): void
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
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.4';
    }
}
