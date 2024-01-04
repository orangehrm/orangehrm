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

namespace OrangeHRM\Installer\Migration\V5_0_0;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class AttendanceHelper
{
    public const TIMEZONE_MAP = [
        'Kwajalein' => '-12.00',
        'Pacific/Midway' => '-11.00',
        'Pacific/Honolulu' => '-10.00',
        'America/Anchorage' => '-9.00',
        'America/Los_Angeles' => '-8.00',
        'America/Denver' => '-7.00',
        'America/Tegucigalpa' => '-6.00',
        'America/New_York' => '-5.00',
        'America/Caracas' => '-4.50',
        'America/Halifax' => -'4.00',
        'America/St_Johns' => '-3.50',
        'America/Argentina/Buenos_Aires' => '-3.00',
        'America/Sao_Paulo' => '-3.00',
        'Atlantic/South_Georgia' => '-2.00',
        'Atlantic/Azores' => '-1.00',
        'Europe/Dublin' => '0',
        'Europe/Belgrade' => '1.00',
        'Europe/Minsk' => '2.00',
        'Asia/Kuwait' => '3.00',
        'Asia/Tehran' => '3.50',
        'Asia/Muscat' => '4.00',
        'Asia/Yekaterinburg' => '5.00',
        'Asia/Kolkata' => '5.5',
        'Asia/Katmandu' => '5.45',
        'Asia/Dhaka' => '6.00',
        'Asia/Rangoon' => '6.50',
        'Asia/Krasnoyarsk' => '7.00',
        'Asia/Brunei' => '8.00',
        'Asia/Seoul' => '9.00',
        'Australia/Darwin' => '9.50',
        'Australia/Canberra' => '10.00',
        'Asia/Magadan' => '11.00',
        'Pacific/Fiji' => '12.00',
        'Pacific/Tongatapu' => '13.00'
    ];

    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getConnection()->createQueryBuilder();
    }

    /**
     * @param string $offset
     * @param string $timezone
     * @return void
     */
    public function updatePunchInTimezoneOffset(string $offset, string $timezone): void
    {
        $q = $this->createQueryBuilder();
        $q->update('ohrm_attendance_record')
            ->set('ohrm_attendance_record.punch_in_timezone_name', ':punchInTimezone')
            ->where('ohrm_attendance_record.punch_in_time_offset = :offset')
            ->setParameter('punchInTimezone', $timezone)
            ->setParameter('offset', $offset)
            ->executeQuery();
    }

    /**
     * @param string $offset
     * @param string $timezone
     * @return void
     */
    public function updatePunchOutTimezoneOffset(string $offset, string $timezone): void
    {
        $q = $this->createQueryBuilder();
        $q->update('ohrm_attendance_record')
            ->set('ohrm_attendance_record.punch_out_timezone_name', ':punchOutTimezone')
            ->where('ohrm_attendance_record.punch_out_time_offset = :offset')
            ->setParameter('punchOutTimezone', $timezone)
            ->setParameter('offset', $offset)
            ->executeQuery();
    }
}
