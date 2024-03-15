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

namespace OrangeHRM\Core\Api\V2;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use OpenApi\Annotations as OA;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Exception\SanitizerException;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Utility\Sanitizer;

class RequestParams
{
    use TextHelperTrait;

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
     * @param bool $convertEmptyStringToNull
     * @return string|null
     */
    public function getStringOrNull(
        string $type,
        string $key,
        ?string $default = null,
        bool $convertEmptyStringToNull = true
    ): ?string {
        $param = $this->$type->get($key, $default);
        if (!$convertEmptyStringToNull) {
            return $param;
        }
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
        $param = $this->$type->get($key, $default);
        if ($this->isEmptyString($param)) {
            throw new InvalidParamException([
                $key => new InvalidArgumentException("Parameter `$key` should not be an empty string")
            ]);
        }
        return (int) $param;
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
     * @param float $default
     * @return float
     */
    public function getFloat(string $type, string $key, float $default = 0): float
    {
        return $this->$type->get($key, $default);
    }

    /**
     * @param string $type
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public function getFloatOrNull(string $type, string $key, ?float $default = null): ?float
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
        return $this->$type->getBoolean($key, $param);
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
     * @OA\Schema(
     *     schema="Base64Attachment",
     *     type="object",
     *     @OA\Property(property="name", description="Specify the name of the attachment", type="string"),
     *     @OA\Property(property="type", description="Specify the type of the attachment", type="string"),
     *     @OA\Property(property="base64", description="Specify the attachment in Base64 format", type="string", format="base64"),
     *     @OA\Property(property="size", description="Specify the size of the attachment", type="integer"),
     * )
     *
     * @param string $type
     * @param string $key
     * @param array|null $default
     * @return Base64Attachment
     */
    public function getAttachment(string $type, string $key, ?array $default = null): Base64Attachment
    {
        return $this->getAttachmentOrNull($type, $key, $default);
    }

    /**
     * @OA\Schema(
     *     schema="Base64AttachmentOrNull",
     *     type="object",
     *     oneOf={
     *         @OA\Schema(ref="#/components/schemas/Base64Attachment"),
     *         @OA\Schema(ref="#/components/schemas/Null")
     *     }
     * )
     *
     * @OA\Schema(schema="Null", type="null")
     *
     * @param string $type
     * @param string $key
     * @param array|null $default
     * @return Base64Attachment|null
     */
    public function getAttachmentOrNull(string $type, string $key, ?array $default = null): ?Base64Attachment
    {
        $attachment = $this->$type->get($key, $default);
        if (isset($attachment['name']) && isset($attachment['type']) && isset($attachment['base64']) && isset($attachment['size'])) {
            $attachment = new Base64Attachment(
                $attachment['name'],
                $attachment['type'],
                $attachment['base64'],
                $attachment['size']
            );

            // Check for SVG and sanitize
            if ($attachment->getFileType() === 'image/svg+xml') {
                $sanitizer = new Sanitizer();
                try {
                    $sanitizedContent = $sanitizer->sanitizeSvg($attachment->getContent());

                    $attachment->setContent($sanitizedContent);
                    $attachment->setSize($this->getTextHelper()->strLength($sanitizedContent));
                } catch (SanitizerException $e) {
                    throw new InvalidParamException([
                        $key => new InvalidArgumentException($e->getMessage())
                    ]);
                }
            }

            return $attachment;
        }

        return null;
    }

    /**
     * @param string $type
     * @param string $key
     * @param DateTimeZone|null $timezone
     * @param DateTime|null $default
     * @return DateTime
     */
    public function getDateTime(
        string $type,
        string $key,
        ?DateTimeZone $timezone = null,
        ?DateTime $default = null
    ): DateTime {
        if ($default instanceof DateTime && $timezone instanceof DateTimeZone) {
            $default->setTimezone($timezone);
        }

        $date = $this->$type->get($key, $default);
        if (!$date instanceof DateTime && !is_null($date)) {
            $date = new DateTime($date);
        }
        if ($timezone instanceof DateTimeZone) {
            $date->setTimezone($timezone);
        }
        return $date;
    }

    /**
     * @param string $type
     * @param string $key
     * @param DateTimeZone|null $timezone
     * @param DateTime|null $default
     * @return DateTime|null
     */
    public function getDateTimeOrNull(
        string $type,
        string $key,
        ?DateTimeZone $timezone = null,
        ?DateTime $default = null
    ): ?DateTime {
        if ($default instanceof DateTime && $timezone instanceof DateTimeZone) {
            $default->setTimezone($timezone);
        }

        $date = $this->$type->get($key, $default);
        if ($this->isEmptyString($date) || is_null($date)) {
            return null;
        }

        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }
        if ($timezone instanceof DateTimeZone) {
            $date->setTimezone($timezone);
        }
        return $date;
    }

    /**
     * @param string $type
     * @param string $key
     * @return bool
     */
    public function has(string $type, string $key): bool
    {
        return $this->$type->has($key);
    }

    /**
     * @param string|int|bool|null $param
     * @return bool
     */
    private function isEmptyString($param): bool
    {
        return is_string($param) && trim($param) === '';
    }
}
