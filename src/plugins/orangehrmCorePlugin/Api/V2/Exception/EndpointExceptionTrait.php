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

namespace OrangeHRM\Core\Api\V2\Exception;

trait EndpointExceptionTrait
{
    /**
     * @param string $message
     * @return BadRequestException
     */
    protected function getBadRequestException(
        string $message = BadRequestException::DEFAULT_ERROR_MESSAGE
    ): BadRequestException {
        return new BadRequestException($message);
    }

    /**
     * @param string $message
     * @return NotImplementedException
     */
    protected function getNotImplementedException(
        string $message = NotImplementedException::DEFAULT_ERROR_MESSAGE
    ): NotImplementedException {
        return new NotImplementedException($message);
    }

    /**
     * @param string $message
     * @return RecordNotFoundException
     */
    protected function getRecordNotFoundException(
        string $message = RecordNotFoundException::DEFAULT_ERROR_MESSAGE
    ): RecordNotFoundException {
        return new RecordNotFoundException($message);
    }

    /**
     * @param string $message
     * @return ForbiddenException
     */
    protected function getForbiddenException(
        string $message = ForbiddenException::DEFAULT_ERROR_MESSAGE
    ): ForbiddenException {
        return new ForbiddenException($message);
    }

    /**
     * @param object|null $entity
     * @param string|null $entityClass
     * @param string $message
     * @throws RecordNotFoundException
     */
    protected function throwRecordNotFoundExceptionIfNotExist(
        ?object $entity,
        ?string $entityClass = null,
        string $message = RecordNotFoundException::DEFAULT_ERROR_MESSAGE
    ) {
        if (($entityClass && !$entity instanceof $entityClass) || is_null($entity)) {
            throw $this->getRecordNotFoundException($message);
        }
    }
}
