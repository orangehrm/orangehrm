<?php

class ThemeForm extends sfForm
{
    public function configure()
    {
        $this->setWidgets([
            'primaryColor' => new sfWidgetFormInputText(),
            'secondaryColor' => new sfWidgetFormInputText(),
            'buttonSuccessColor' => new sfWidgetFormInputText(),
            'buttonCancelColor' => new sfWidgetFormInputText(),
        ]);

        $regex = '/#[0-9a-f]{6}$/';
        $this->setValidators([
            'primaryColor' => new sfValidatorRegex(['required' => true, 'pattern' => $regex]),
            'secondaryColor' => new sfValidatorRegex(['required' => true, 'pattern' => $regex]),
            'buttonSuccessColor' => new sfValidatorRegex(['required' => true, 'pattern' => $regex]),
            'buttonCancelColor' => new sfValidatorRegex(['required' => true, 'pattern' => $regex]),
        ]);

        $this->getValidatorSchema()->setOption('allow_extra_fields', true);
    }
}