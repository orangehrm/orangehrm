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

namespace OrangeHRM\Core\Api\V2;

class Response
{
    public const CONTENT_TYPE_KEY = 'Content-Type';
    public const CONTENT_TYPE_JSON = 'application/json';
    /**
     * @var array
     */
    protected array $data;

    /**
     * @var array
     */
    protected array $rels;

    /**
     * @var array
     */
    protected array $meta;

    /**
     * @param array $data
     * @param array $meta
     * @param array $rels
     */
    public function __construct($data = [], $meta = [], $rels = [])
    {
        $this->data = $data;
        $this->meta = $meta;
        $this->rels = $rels;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return json_encode($this->data, true);
    }

    /**
     * @return string
     */
    public function formatData(): string
    {
        $responseFormat = [
            'data' => $this->data,
            'meta' => $this->meta,
            'rels' => $this->rels,
        ];
        return json_encode($responseFormat, true);
    }

    /**
     * @param $error
     * @return string
     */
    public static function formatError($error): string
    {
        return json_encode($error, true);
    }
}
