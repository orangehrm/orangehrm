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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class LanguageTranslationController extends AbstractVueController
{
    use LocalizationServiceTrait;

    public function preRender(Request $request): void
    {
        if ($request->attributes->has('languageId')) {
            $languageId = $request->attributes->getInt('languageId');
            $component = new Component('language-translation-edit');
            $component->addProp(new Prop('language-id', Prop::TYPE_NUMBER, $languageId));
            $language = $this->getLocalizationService()->getLocalizationDao()
                ->getLanguageById($languageId);
            $languagePackage = $language->getName();
            if (!$language->isAdded() || !$language->isEnabled()) {
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
            }
            $sourceLanguage = 'English (United States)';
            $component->addProp(new Prop('language-package', Prop::TYPE_STRING, $languagePackage));
            $component->addProp(new Prop('source-language', Prop::TYPE_STRING, $sourceLanguage));
            $this->setComponent($component);
        }
    }
}
