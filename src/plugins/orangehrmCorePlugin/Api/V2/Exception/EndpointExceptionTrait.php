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

namespace OrangeHRM\Core\Api\V2\Exception;

use InvalidArgumentException;

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

    /**
     * @param int[] $entities
     * @param string $message
     * @throws RecordNotFoundException
     */
    protected function throwRecordNotFoundExceptionIfEmptyIds(
        array $entities,
        string $message = RecordNotFoundException::PLURAL_ERROR_MESSAGE
    ): void {
        if (count($entities) === 0) {
            throw $this->getRecordNotFoundException($message);
        }
    }

    /**
     * @param string|string[] $paramKeys
     * @param string|null $message
     * @return InvalidParamException
     */
    protected function getInvalidParamException($paramKeys, ?string $message = null): InvalidParamException
    {
        $errorBag = [];
        if (is_string($paramKeys)) {
            $paramKeys = [$paramKeys];
        }
        foreach ($paramKeys as $paramKey) {
            $errorBag[$paramKey] = new InvalidArgumentException($message ?? "Invalid parameter `$paramKey`");
        }
        return new InvalidParamException($errorBag);
    }
}
