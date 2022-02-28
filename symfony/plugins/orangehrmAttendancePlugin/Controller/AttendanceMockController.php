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

namespace OrangeHRM\Attendance\Controller;

use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class AttendanceMockController extends AbstractController
{
    private function timezone_list()
    {
        $timezones = [];

        $now = new \DateTime('now', new \DateTimeZone('UTC'));

        foreach (\DateTimeZone::listIdentifiers() as $key => $timezone) {
            $now->setTimezone(new \DateTimeZone($timezone));
            $offset = $now->getOffset();
            $timezones[] = [
                "id" => $key + 1,
                "label" => '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone),
                "offset" => floatval($offset / 3600)
            ];
        }

        return $timezones;
    }

    private function format_GMT_offset($offset)
    {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return $offset === 0 ? 'GMT+00:00' : 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    private function format_timezone_name($name)
    {
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
    }

    /**
     * @return Response
     */
    public function getTimezones(Request $request): Response
    {
        $timezones = array_filter($this->timezone_list(), function ($timezone) use (&$request) {
            return stripos($timezone['label'], $request->query->get('name')) !== false;
        });

        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [...array_slice($timezones, 0, 5)],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
