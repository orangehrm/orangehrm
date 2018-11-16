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

/**
 * Class APIManagerService
 */
class APIManagerService
{
    private $client = null;

    public function getAddons()
    {
        $addonList = array();
//        $res = $this->getClient()->request('GET', 'https://jsonplaceholder.typicode.com/todos/1');
//        echo $res->getBody();

//        $request = $this->getClient('GET', 'https://jsonplaceholder.typicode.com/todos');
//        $response = $this->getClient()->request('GET', '1');
//        echo $response->getBody();
        return $addonList;
    }


    private function getClient()
    {
        if (!isset($this->client)) {
            // Create a client with a base URI

            $this->client = new GuzzleHttp\Client(['base_uri' => 'https://jsonplaceholder.typicode.com/todos/']);
        }
        return $this->client;
    }
}
