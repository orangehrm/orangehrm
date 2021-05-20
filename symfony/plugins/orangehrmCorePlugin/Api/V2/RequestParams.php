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

use OrangeHRM\Core\Dto\Base64Attachment;

class RequestParams
{
    public const PARAM_TYPE_BODY = 'body';
    public const PARAM_TYPE_ATTRIBUTE = 'attributes';
    public const PARAM_TYPE_QUERY = 'query';

    /**
     * Request body parameters ($_POST)
     * @var ParameterBag
     */
    protected ParameterBag $body;

    /**
     * Parameters from URL
     * @var ParameterBag
     */
    protected ParameterBag $attributes;

    /**
     * Query string parameters ($_GET)
     * @var ParameterBag
     */
    protected ParameterBag $query;

    public function __construct(Request $request)
    {
        $this->body = $request->getBody();
        $this->attributes = $request->getAttributes();
        $this->query = $request->getQuery();
    }

    /**
     * @param string $type
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getString(string $type, string $key, string $default = ''): string
    {
        return $this->$type->get($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getStringOrNull(string $type, string $key, ?string $default = null): ?string
    {
        $param = $this->$type->get($key, $default);
        return $this->isEmptyString($param) && is_null($default) ? null : $param;
    }

    /**
     * @param string $type
     * @param string $key
     * @param int $default
     * @return int
     */
    public function getInt(string $type, string $key, int $default = 0): int
    {
        return $this->$type->getInt($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param int|null $default
     * @return int|null
     */
    public function getIntOrNull(string $type, string $key, ?int $default = null): ?int
    {
        $param = $this->$type->get($key, $default);
        return $this->isEmptyString($param) ? null : $param;
    }

    /**
     * @param string $type
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public function getBoolean(string $type, string $key, bool $default = false): bool
    {
        return $this->$type->getBoolean($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param bool|null $default
     * @return bool|null
     */
    public function getBooleanOrNull(string $type, string $key, ?bool $default = null): ?bool
    {
        $param = $this->$type->get($key, $default);
        if (is_null($param)) {
            return null;
        }
        return $this->$type->getBoolean($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param array $default
     * @return array
     */
    public function getArray(string $type, string $key, array $default = []): array
    {
        return $this->$type->get($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    public function getArrayOrNull(string $type, string $key, ?array $default = null): ?array
    {
        return $this->$type->get($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param array|null $default
     * @return Base64Attachment|null
     */
    public function getAttachmentOrNull(string $type, string $key, ?array $default = null): ?Base64Attachment
    {
        $attachment = $this->$type->get($key, $default);
        if (isset($attachment['name']) && isset($attachment['type']) && isset($attachment['base64']) && isset($attachment['size'])) {
            return new Base64Attachment(
                $attachment['name'],
                $attachment['type'],
                $attachment['base64'],
                $attachment['size']
            );
        }

        return null;
    }

    /**
     * @param string|int|bool|null $param
     * @return bool
     */
    private function isEmptyString($param): bool
    {
        return is_string($param) && empty($param);
    }
}
