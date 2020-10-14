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
 *
 */

class SearchTranslationLanguageForm extends BaseForm
{
    public function configure()
    {
        $this->setWidgets(
            [
                'langPackage' => new ohrmWidgetDiv(),
                'sourceLang' => new ohrmWidgetDiv(),
                'group' => new sfWidgetFormSelect(
                    ['choices' => $this->getModules()],
                    ['class' => 'search-input']
                ),
                'sourceText' => new sfWidgetFormInputText([], ['class' => 'search-input']),
                'translatedText' => new sfWidgetFormInputText([], ['class' => 'search-input']),
                'translated' => new sfWidgetFormChoice(
                    ['expanded' => true, 'choices' => $this->getTranslatedChoices()]
                ),
                'reset' => new sfWidgetFormInputHidden(['default' => false])
            ]
        );

        $this->setValidators(
            [
                'group' => new sfValidatorChoice(
                    ['choices' => array_keys($this->getModules()), 'required' => false]
                ),
                'sourceText' => new sfValidatorString(['required' => false]),
                'translatedText' => new sfValidatorString(['required' => false]),
                'translated' => new sfValidatorChoice(
                    ['choices' => array_keys($this->getTranslatedChoices()), 'required' => false]
                ),
                'reset' => new sfValidatorBoolean(['required' => false]),
            ]
        );

        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->widgetSchema->setNameFormat('searchTranslationLanguage[%s]');
    }

    public function getModules()
    {
        // TODO:: fetch from DB
        return [
            null => __("All"),
            'pim' => __("PIM")
        ];
    }

    public function getTranslatedChoices()
    {
        return [
            null => __("All"),
            true => __("Translated"),
            false => __("Not Translated")
        ];
    }

    /**
     * @return array
     */
    protected function getFormLabels()
    {
        return [
            'langPackage' => __('Language Package'),
            'sourceLang' => __('Source Language'),
            'group' => __('Module'),
            'sourceText' => __('Source Text'),
            'translatedText' => __('Translated Text'),
            'translated' => __('Show'),
        ];
    }
}
