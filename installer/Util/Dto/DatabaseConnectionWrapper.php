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

namespace OrangeHRM\Installer\Util\Dto;

use Doctrine\DBAL\Connection as DBALConnection;
use InvalidArgumentException;
use OrangeHRM\Installer\Exception\SystemCheckException;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\Messages;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Installer\Util\SystemCheck;
use Throwable;

class DatabaseConnectionWrapper
{
    public const ERROR_CODE_ACCESS_DENIED = 1045;
    public const ERROR_CODE_INVALID_HOST_PORT = 2002;
    public const ERROR_CODE_DATABASE_NOT_EXISTS = 1049;

    private ?DBALConnection $conn;
    private ?Throwable $e;

    public function __construct(?DBALConnection $conn, ?Throwable $e = null)
    {
        $this->conn = $conn;
        $this->e = $e;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->e instanceof Throwable;
    }

    public function getErrorMessage(): ?string
    {
        if (!$this->hasError()) {
            return null;
        }
        $errorMessage = $this->e->getMessage();
        $errorCode = $this->e->getCode();

        if ($this->e instanceof SystemCheckException) {
            return $this->e->getMessage();
        }

        if ($errorCode === self::ERROR_CODE_INVALID_HOST_PORT) {
            $dbInfo = StateContainer::getInstance()->getDbInfo();
            $dbHost = $dbInfo[StateContainer::DB_HOST];
            $dbPort = $dbInfo[StateContainer::DB_PORT];
            $message = "The MySQL server isn't running on `$dbHost:$dbPort`. " . Messages::ERROR_MESSAGE_INVALID_HOST_PORT;
        } elseif ($errorCode === self::ERROR_CODE_ACCESS_DENIED) {
            $message = Messages::ERROR_MESSAGE_ACCESS_DENIED;
        } elseif ($errorCode === self::ERROR_CODE_DATABASE_NOT_EXISTS) {
            $message = 'Database Not Exist';
        } else {
            $message = $errorMessage . ' ' . Messages::ERROR_MESSAGE_REFER_LOG_FOR_MORE;
        }
        return $message;
    }

    /**
     * @return Throwable|null
     */
    public function getThrowable(): ?Throwable
    {
        return $this->e;
    }

    /**
     * @param callable $dbalConnectionGetter
     * @return self
     */
    public static function establishConnection(callable $dbalConnectionGetter): self
    {
        $systemCheck = new SystemCheck();
        if (!$systemCheck->checkPDOExtensionEnabled()) {
            return new self(null, SystemCheckException::notEnabledPDOExtension());
        }
        if (!$systemCheck->checkPDOMySqlExtensionEnabled()) {
            return new self(null, SystemCheckException::notEnabledPDOMySQLDriver());
        }

        try {
            $conn = $dbalConnectionGetter();
            if ($conn instanceof DBALConnection) {
                $conn->connect();
                return new self($conn);
            }
            throw new InvalidArgumentException('Invalid callback provided');
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            if ($e instanceof InvalidArgumentException) {
                throw $e;
            }
            return new self(null, $e);
        }
    }
}
