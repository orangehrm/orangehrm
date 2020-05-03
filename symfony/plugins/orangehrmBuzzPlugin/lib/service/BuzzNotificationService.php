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

class BuzzNotificationService
{
    /**
     * @var BuzzNotificationDao|null
     */
    protected $buzzNotificationDao = null;

    /**
     * @var BuzzTimezoneUtility|null
     */
    protected $buzzTimeZoneUtility = null;

    /**
     * @return BuzzNotificationDao
     */
    public function getBuzzNotificationDao(): BuzzNotificationDao
    {
        if (!($this->buzzNotificationDao instanceof BuzzNotificationDao)) {
            $this->buzzNotificationDao = new BuzzNotificationDao();
        }
        return $this->buzzNotificationDao;
    }

    /**
     * @param BuzzNotificationDao $buzzNotificationDao
     */
    public function setBuzzNotificationDao(BuzzNotificationDao $buzzNotificationDao)
    {
        $this->buzzNotificationDao = $buzzNotificationDao;
    }

    /**
     * @return BuzzTimezoneUtility
     */
    public function getBuzzTimezoneUtility()
    {
        if (!$this->buzzTimeZoneUtility instanceof BuzzTimezoneUtility) {
            $this->buzzTimeZoneUtility = new BuzzTimezoneUtility();
        }
        return $this->buzzTimeZoneUtility;
    }

    /**
     * @param BuzzTimezoneUtility $buzzTimeZoneUtility
     */
    public function setBuzzTimezoneUtility(BuzzTimezoneUtility $buzzTimeZoneUtility)
    {
        $this->buzzTimeZoneUtility = $buzzTimeZoneUtility;
    }

    /**
     * @param $empNumber
     * @return BuzzNotificationMetadata|false
     * @throws DaoException
     */
    public function getBuzzNotificationMetadata($empNumber)
    {
        return $this->getBuzzNotificationDao()->getBuzzNotificationMetadata($empNumber);
    }

    /**
     * @param BuzzNotificationMetadata $buzzLastView
     * @return BuzzNotificationMetadata
     * @throws DaoException
     */
    public function saveBuzzNotificationMetadata(BuzzNotificationMetadata $buzzLastView): BuzzNotificationMetadata
    {
        return $this->getBuzzNotificationDao()->saveBuzzNotificationMetadata($buzzLastView);
    }

    /**
     * @param int $empNumber
     * @param DateTime $since
     * @return Share[]
     * @throws DaoException
     */
    public function getSharesExceptEmployeeNumberSince(int $empNumber, DateTime $since = null)
    {
        return $this->getBuzzNotificationDao()->getSharesExceptEmployeeNumberSince($empNumber, $since);
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return Comment[]
     * @throws DaoException
     */
    public function getCommentsOnEmployeePostsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        return $this->getBuzzNotificationDao()->getCommentsOnEmployeePostsSince($empNumber, $since, $excludeThisEmployee);
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return LikeOnShare[]
     * @throws DaoException
     */
    public function getLikesOnEmployeePostsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        return $this->getBuzzNotificationDao()->getLikesOnEmployeePostsSince($empNumber, $since, $excludeThisEmployee);
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return LikeOnComment[]
     * @throws DaoException
     */
    public function getLikesOnEmployeeCommentsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        return $this->getBuzzNotificationDao()->getLikesOnEmployeeCommentsSince($empNumber, $since, $excludeThisEmployee);
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $since
     * @param bool $excludeThisEmployee
     * @return Share[]
     * @throws DaoException
     */
    public function getSharesOfEmployeePostsSince(int $empNumber, DateTime $since = null, bool $excludeThisEmployee = true)
    {
        return $this->getBuzzNotificationDao()->getSharesOfEmployeePostsSince($empNumber, $since, $excludeThisEmployee);
    }

    /**
     * @param DateTime $datetime
     * @param bool $full
     * @return string
     * @throws Exception
     */
    public function timeElapsedString(DateTime $datetime, $full = false)
    {
        $now = $this->getUserNow();
        $diff = $now->diff($datetime);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => __('year'),
            'm' => __('month'),
            'w' => __('week'),
            'd' => __('day'),
            'h' => __('hour'),
            'i' => __('minute'),
            's' => __('second'),
        );

        $stringPlural = array(
            'y' => __('years'),
            'm' => __('months'),
            'w' => __('weeks'),
            'd' => __('days'),
            'h' => __('hours'),
            'i' => __('minutes'),
            's' => __('seconds'),
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . ($diff->$k > 1 ? $stringPlural[$k] : $v);
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ' . __('ago') : __('Just now');
    }

    /**
     * @param DateTime $datetime
     * @return DateTime
     * @throws sfException
     */
    public function setTimezone(DateTime $datetime)
    {
        $offset = sfContext::getInstance()->getUser()->getUserTimeZoneOffsetForBuzz();
        if (!isset($offset) || is_null($offset)) {
            return $datetime;
        }
        $datetime->setTimezone(new DateTimeZone($this->getBuzzTimezoneUtility()->getTimeZoneFromClientOffset($offset)));
        return $datetime;
    }

    /**
     * @return DateTime
     * @throws sfException
     */
    public function getUserNow()
    {
        $now = new DateTime();
        return new DateTime($this->setTimezone($now)->format('Y-m-d H:i:s'));
    }

    /**
     * Get and return converted timestamp in ISO-8601 format (YYYY-MM-DD HH:MI:SS)
     * @param string $date
     * @return string
     * @throws sfException
     */
    public function getUserDateTime(string $date)
    {
        $dateTime = new DateTime($date);
        $dateTime = $this->setTimezone($dateTime);
        return $dateTime->format('Y-m-d H:i:s');
    }
}
