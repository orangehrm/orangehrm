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

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class CandidateActionController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        //TODO

        $candidateId = $request->get('candidateId');
        $component = new Component('reject-action');
        $component->addProp(new Prop('candidate-id', Prop::TYPE_NUMBER, $candidateId));
        $component->addProp(new Prop('max-file-size', Prop::TYPE_NUMBER, 1024 * 1024));
        $component->addProp(
            new Prop('allowed-file-types', Prop::TYPE_ARRAY, [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
                'application/rtf',
                'application/vnd.oasis.opendocument.text'
            ])
        );
        $component->addProp(new Prop('history-id', Prop::TYPE_NUMBER, 1));
        $component->addProp(new Prop('action', Prop::TYPE_OBJECT, ['id' => 1, 'label' => 'Application initiated']));
        $this->setComponent($component);
    }
}
