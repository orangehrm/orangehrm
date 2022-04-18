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

namespace OrangeHRM\Framework;

use OrangeHRM\Framework\Exception\ServiceContainerException;
use Psr\Log\LoggerInterface;

/**
 * @deprecated
 */
class Logger implements LoggerInterface
{
    /**
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    private function __construct()
    {
        $logger = ServiceContainer::getContainer()->get(Services::LOGGER);
        if (!$logger instanceof LoggerInterface) {
            throw new ServiceContainerException(sprintf('%s not initialized', Services::LOGGER));
        }
        $this->logger = $logger;
    }

    /**
     * @param string|null $name
     * @return static
     */
    public static function getLogger(?string $name = null): self
    {
        // TODO:: improve logger with $name
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
