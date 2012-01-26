<?php

class sfWidgetFormSchemaFormatterBreakTags extends sfWidgetFormSchemaFormatter {

    protected static $index = 1;
    protected static $noOfColumns = 1;
    protected $rowFormat = "%label%\n  %field%%help%\n%hidden_fields% %break% %error%\n";
    protected $errorRowFormat = "<span>\n%errors%</span>\n";
    protected $helpFormat = '<br />%help%';
    protected $decoratorFormat = "<form>\n  %content%</form>";

    /**
     *
     * @return int
     */
    public static function getNoOfColumns() {
        return self::$noOfColumns;
    }

    /**
     *
     * @param int $noOfColumns 
     */
    public static function setNoOfColumns($noOfColumns) {
        self::$noOfColumns = $noOfColumns;
    }

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
            '%break%' => $this->getBreakTag(),
            '%error%' => $this->formatErrorsForRow($errors),
            '%help%' => $this->formatHelp($help),
            '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
        ));
    }

    /**
     *
     * @return string
     */
    protected function getBreakTag() {
        return((self::$index++ % self::$noOfColumns == 0) ? tag('br') : '');
    }

}

