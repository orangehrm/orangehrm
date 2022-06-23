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

namespace OrangeHRM\Recruitment\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\CandidateHistory;

class CandidateHistoryListModel implements Normalizable
{
    use ModelTrait;

    public function __construct(CandidateHistory $candidateHistory)
    {
        $this->setEntity($candidateHistory);
        $this->setFilters([
            'id',
            'action',
            ['getDecorator', 'getCandidateHistoryAction'],
            ['getPerformedBy', 'getEmpNumber'],
            ['getPerformedBy', 'getLastName'],
            ['getPerformedBy', 'getFirstName'],
            ['getPerformedBy', 'getMiddleName'],
            ['getPerformedBy', 'getEmployeeTerminationRecord', 'getId'],
            ['getInterview', 'getId'],
            ['getDecorator', 'getPerformedDate'],
            'note',
        ]);

        $this->setAttributeNames([
            'id',
            ['action', 'id'],
            ['action', 'label'],
            ['performedBy', 'empNumber'],
            ['performedBy', 'lastName'],
            ['performedBy', 'firstName'],
            ['performedBy', 'middleName'],
            ['performedBy', 'terminationId'],
            ['interview', 'id'],
            'performedDate',
            'note'
        ]);
    }
}
