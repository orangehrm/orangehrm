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

use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Recruitment\Service\RecruitmentAttachmentService;

class ApplyJobVacancyViewController extends AbstractVueController implements PublicControllerInterface
{
    protected ?ConfigService $configService = null;

    public function getConfigService(): ConfigService
    {
        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $id = $request->get('id');
        $component = new Component('apply-job-vacancy');
        $component->addProp(new Prop('vacancy-id', Prop::TYPE_STRING, $id));
        $component->addProp(
            new Prop(
                'allowed-file-types',
                Prop::TYPE_ARRAY,
                RecruitmentAttachmentService::ALLOWED_CANDIDATE_ATTACHMENT_FILE_TYPES
            )
        );
        $csrfTokenManager = new CsrfTokenManager();
        $component->addProp(
            new Prop('token', Prop::TYPE_STRING, $csrfTokenManager->getToken('recruitment-applicant')->getValue())
        );
        $component->addProp(new Prop('max-file-size', Prop::TYPE_NUMBER, $this->getConfigService()->getMaxAttachmentSize()));
        $this->setComponent($component);
        $this->setTemplate('no_header.html.twig');
    }
}
