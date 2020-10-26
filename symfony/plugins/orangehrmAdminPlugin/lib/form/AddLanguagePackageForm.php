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

class AddLanguagePackageForm extends BaseForm
{
    /**
     * @var null|I18NService
     */
    protected $i18nService = null;
    /**
     * @var array
     */
    protected $languages = [];

    public function configure()
    {
        $this->setWidgets(
            [
                'name' => new sfWidgetFormSelect(
                    ['choices' => $this->getLanguages()]
                ),
            ]
        );

        $choices = array_keys($this->getLanguages());
        array_shift($choices);
        $this->setValidators(
            [
                'name' => new sfValidatorChoice(
                    ['choices' => $choices, 'required' => true]
                ),
            ]
        );

        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->widgetSchema->setNameFormat('addLanguagePackage[%s]');
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getLanguages(): array
    {
        if (empty($this->languages)) {
            $this->languages[null] = '--' . __('Select') . '--';
            $searchParams = new ParameterObject(
                [
                    'enabled' => true,
                    'added' => false,
                    'sortField' => 'l.name'
                ]
            );
            $languages = $this->getI18NService()->searchLanguages($searchParams);
            foreach ($languages as $language) {
                $this->languages[$language->getCode()] = $language->getName();
            }
        }
        return $this->languages;
    }

    /**
     * @return array
     */
    protected function getFormLabels(): array
    {
        return [
            'name' => __('Name') . ' <em>*</em>',
        ];
    }

    /**
     * @return array
     */
    public function getActionButtons(): array
    {
        $actionButtons['btnSave'] = new ohrmWidgetButton('btnSave', "Save", []);
        $actionButtons['btnCancel'] = new ohrmWidgetButton('btnCancel', "Cancel", ['class' => 'cancel']);
        return $actionButtons;
    }

    /**
     * @return I18NService
     */
    protected function getI18NService(): I18NService
    {
        if (is_null($this->i18nService)) {
            $this->i18nService = new I18NService();
        }
        return $this->i18nService;
    }
}
