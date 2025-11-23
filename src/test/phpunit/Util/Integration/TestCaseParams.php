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

namespace OrangeHRM\Tests\Util\Integration;

use DateTime;
use DateTimeZone;

class TestCaseParams
{
    public const HOOK_PRE_ASSERT_VALID_TEST_CASE = 'preAssertValidTestCase';

    private ?string $name = null;

    private ?int $userId = null;

    private ?array $services = null;

    private ?array $factories = null;

    private ?array $attributes = null;

    private ?array $body = null;

    private ?array $query = null;

    private ?array $resultData = null;

    private ?array $resultMeta = null;

    private ?array $invalidOnly = null;

    private ?string $exceptionClass = null;

    private ?string $exceptionMessage = null;

    private ?DateTime $now = null;

    private ?array $hooks = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return array|null
     */
    public function getServices(): ?array
    {
        return $this->services;
    }

    /**
     * @param array|null $services
     */
    public function setServices(?array $services): void
    {
        $this->services = $services;
    }

    /**
     * @return array|null
     */
    public function getFactories(): ?array
    {
        return $this->factories;
    }

    /**
     * @param array|null $factories
     */
    public function setFactories(?array $factories): void
    {
        $this->factories = $factories;
    }

    /**
     * @return array|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param array|null $attributes
     */
    public function setAttributes(?array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array|null
     */
    public function getBody(): ?array
    {
        return $this->body;
    }

    /**
     * @param array|null $body
     */
    public function setBody(?array $body): void
    {
        $this->body = $body;
    }

    /**
     * @return array|null
     */
    public function getQuery(): ?array
    {
        return $this->query;
    }

    /**
     * @param array|null $query
     */
    public function setQuery(?array $query): void
    {
        $this->query = $query;
    }

    /**
     * @return array|null
     */
    public function getResultData(): ?array
    {
        return $this->resultData;
    }

    /**
     * @param array|null $resultData
     */
    public function setResultData(?array $resultData): void
    {
        $this->resultData = $resultData;
    }

    /**
     * @return array|null
     */
    public function getResultMeta(): ?array
    {
        return $this->resultMeta;
    }

    /**
     * @param array|null $resultMeta
     */
    public function setResultMeta(?array $resultMeta): void
    {
        $this->resultMeta = $resultMeta;
    }

    /**
     * @return array|null
     */
    public function getInvalidOnly(): ?array
    {
        return $this->invalidOnly;
    }

    /**
     * @return bool
     */
    public function isInvalid(): bool
    {
        return !empty($this->getInvalidOnly());
    }

    /**
     * @param array|null $invalidOnly
     */
    public function setInvalidOnly(?array $invalidOnly): void
    {
        $this->invalidOnly = $invalidOnly;
    }

    /**
     * @return string|null
     */
    public function getExceptionClass(): ?string
    {
        return $this->exceptionClass;
    }

    /**
     * @param string|null $exceptionClass
     */
    public function setExceptionClass(?string $exceptionClass): void
    {
        $this->exceptionClass = $exceptionClass;
    }

    /**
     * @return string|null
     */
    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    /**
     * @param string|null $exceptionMessage
     */
    public function setExceptionMessage(?string $exceptionMessage): void
    {
        $this->exceptionMessage = $exceptionMessage;
    }

    /**
     * @return DateTime|null
     */
    public function getNow(): ?DateTime
    {
        return $this->now;
    }

    /**
     * @param array|null $now
     * array(
     *   'datetime' => e.g. 2021-09-15 or 2021-09-15 11:27:00,
     *   'timezone' => https://www.php.net/manual/en/datetimezone.construct.php
     * )
     */
    public function setNowFromArray(?array $now): void
    {
        if (isset($now['datetime'])) {
            $timezone = null;
            if (isset($now['timezone'])) {
                $timezone = new DateTimeZone($now['timezone']);
            }
            $this->now = new DateTime($now['datetime'], $timezone);
        }
    }

    /**
     * @return array|null
     */
    public function getHooks(): ?array
    {
        return $this->hooks;
    }

    /**
     * @param string $name
     * @return callable|null
     */
    public function getHook(string $name): ?callable
    {
        if (isset($this->hooks[$name]) && is_callable($this->hooks[$name])) {
            return $this->hooks[$name];
        }
        return null;
    }

    /**
     * @param array|null $hooks
     */
    public function setHooks(?array $hooks): void
    {
        $this->hooks = $hooks;
    }
}
