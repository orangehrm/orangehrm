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
 * Boston, MA 02110-1301, USA
 */

class BuzzNotificationDao
{
    /**
     * @param $empNumber
     * @return BuzzNotificationMetadata|false
     * @throws DaoException
     */
    public function getBuzzNotificationMetadata($empNumber)
    {
        try {
            return Doctrine_Core::getTable('BuzzNotificationMetadata')->find($empNumber);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param BuzzNotificationMetadata $buzzLastView
     * @return BuzzNotificationMetadata
     * @throws DaoException
     */
    public function saveBuzzNotificationMetadata(BuzzNotificationMetadata $buzzLastView):BuzzNotificationMetadata
    {
        try {
            $buzzLastView->save();
            return $buzzLastView;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * [Someone share new post]
     * @param int $empNumber
     * @param DateTime|null $since
     * @return Share[]
     * @throws DaoException
     */
    public function getSharesExceptEmployeeNumberSince(int $empNumber, DateTime $since = null)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('s.*')
                ->from('Share s')
                ->leftJoin('s.PostShared p')
                ->addWhere('s.employee_number NOT IN (?)', [$empNumber])
                ->andWhere('p.employee_number NOT IN (?)', [$empNumber])
                ->orderBy('s.share_time DESC');

            if (!is_null($since)) {
                $timeStamp = $since->format('Y-m-d H:i:s');
                $q->andWhere('s.share_time > ?', $timeStamp);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * [Someone comment on your(given employee) post]
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return Comment[]
     * @throws DaoException
     */
    public function getCommentsOnEmployeePostsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('c.*')
                ->from('Comment c')
                ->leftJoin('c.shareComment s')
                ->where('s.employee_number = ?', $empNumber)
                ->orderBy('c.comment_time DESC');

            if (!is_null($since)) {
                $timeStamp = $since->format('Y-m-d H:i:s');
                $q->andWhere('c.comment_time > ?', $timeStamp);
            }
            if ($excludeThisEmployee) {
                $q->andWhere('c.employee_number NOT IN (?)', [$empNumber]);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * [Someone like on your(given employee) share/post]
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return LikeOnShare[]
     * @throws DaoException
     */
    public function getLikesOnEmployeePostsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('ls.*')
                ->from('LikeOnShare ls')
                ->leftJoin('ls.ShareLike s')
                ->where('s.employee_number = ?', $empNumber)
                ->orderBy('ls.like_time DESC');

            if (!is_null($since)) {
                $timeStamp = $since->format('Y-m-d H:i:s');
                $q->andWhere('ls.like_time > ?', $timeStamp);
            }
            if ($excludeThisEmployee) {
                $q->andWhere('ls.employee_number NOT IN (?)', [$empNumber]);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * [Someone like on your(given employee) comment]
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return LikeOnComment[]
     * @throws DaoException
     */
    public function getLikesOnEmployeeCommentsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('lc.*')
                ->from('LikeOnComment lc')
                ->leftJoin('lc.CommentLike c')
                ->where('c.employee_number = ?', $empNumber)
                ->orderBy('lc.like_time DESC');

            if (!is_null($since)) {
                $timeStamp = $since->format('Y-m-d H:i:s');
                $q->andWhere('lc.like_time > ?', $timeStamp);
            }
            if ($excludeThisEmployee) {
                $q->andWhere('lc.employee_number NOT IN (?)', [$empNumber]);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * [Someone share your(given employee) post]
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return Share[]
     * @throws DaoException
     */
    public function getSharesOfEmployeePostsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('s.*')
                ->from('Share s')
                ->leftJoin('s.PostShared p')
                ->where('p.employee_number = ?', $empNumber)
                ->orderBy('s.share_time DESC');

            if (!is_null($since)) {
                $timeStamp = $since->format('Y-m-d H:i:s');
                $q->andWhere('s.share_time > ?', $timeStamp);
            }
            if ($excludeThisEmployee) {
                $q->andWhere('s.employee_number NOT IN (?)', [$empNumber]);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
