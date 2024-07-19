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

use OrangeHRM\Admin\Api\I18NTranslationImportAPI;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Controller\Exception\VueControllerException;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Framework\Http\Request;

class LanguageImportController extends AbstractVueController
{
    use ConfigServiceTrait;
    use LocalizationServiceTrait;

    /**
     * @throws VueControllerException
     * @throws RequestForwardableException
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('languageId')) {
            $languageId = $request->attributes->getInt('languageId');
            $language = $this->getLocalizationService()->getLocalizationDao()
                ->getLanguageById($languageId);
            if (
                !$language instanceof I18NLanguage ||
                !$language->isAdded() ||
                !$language->isEnabled()
            ) {
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
            }

            $component = new Component('language-import');
            $component->addProp(new Prop('language-id', Prop::TYPE_NUMBER, $languageId));
            $component->addProp(new Prop('language-name', Prop::TYPE_STRING, $language->getName()));
            $component->addProp(
                new Prop(
                    'allowed-file-types',
                    Prop::TYPE_ARRAY,
                    I18NTranslationImportAPI::PARAM_RULE_IMPORT_FILE_FORMAT
                )
            );
            $component->addProp(new Prop('max-file-size', Prop::TYPE_NUMBER, $this->getConfigService()->getMaxAttachmentSize()));

            $this->setComponent($component);
        }
    }
}
