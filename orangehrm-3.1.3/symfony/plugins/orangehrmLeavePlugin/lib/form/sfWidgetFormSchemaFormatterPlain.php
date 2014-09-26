<?php

class sfWidgetFormSchemaFormatterPlain extends sfWidgetFormSchemaFormatter {

    protected $rowFormat = "%label%\n  %field%%help%\n%hidden_fields% %error%\n";
    protected $errorRowFormat = "<span>\n%errors%</span>\n";
    protected $helpFormat = '<br />%help%';

    /**
     *
     * @param string $label
     * @param string $field
     * @param array $errors
     * @param string $help
     * @param string $hiddenFields
     * @return string
     */
    public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null) {
        return strtr($this->getRowFormat(), array(
            '%label%' => $label,
            '%field%' => $field,
            '%error%' => $this->formatErrorsForRow($errors),
            '%help%' => $this->formatHelp($help),
            '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
        ));
    }

}

