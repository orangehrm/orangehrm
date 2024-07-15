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

namespace OrangeHRM\CorporateBranding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\Theme;
use OrangeHRM\Framework\Http\Request;

class CorporateBrandingController extends AbstractVueController
{
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('corporate-branding');
        $component->addProp(new Prop('allowed-image-types', Prop::TYPE_ARRAY, Theme::ALLOWED_IMAGE_TYPES));
        $component->addProp(new Prop('aspect-ratios', Prop::TYPE_OBJECT, [
            'clientLogo' => Theme::CLIENT_LOGO_ASPECT_RATIO,
            'clientBanner' => Theme::CLIENT_BANNER_ASPECT_RATIO,
            'loginBanner' => Theme::LOGIN_BANNER_ASPECT_RATIO,
        ]));
        $component->addProp(new Prop('aspect-ratio-tolerance', Prop::TYPE_NUMBER, Theme::IMAGE_ASPECT_RATIO_TOLERANCE));
        $component->addProp(new Prop('max-file-size', Prop::TYPE_NUMBER, $this->getConfigService()->getMaxAttachmentSize()));
        $this->setComponent($component);
    }
}
