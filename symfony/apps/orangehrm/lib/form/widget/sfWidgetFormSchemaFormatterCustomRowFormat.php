<?php

class sfWidgetFormSchemaFormatterCustomRowFormat extends sfWidgetFormSchemaFormatter {

    protected $counter = 0;
    protected $rowFormat = "<li>%label%\n  %field%%help%\n%hidden_fields%%error%</li>\n";
    protected static $customRowFormats;

    public static function getCustomRowFormats() {
        return self::$customRowFormats;
    }

    public static function setCustomRowFormats($customRowFormats) {
        self::$customRowFormats = $customRowFormats;
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
        
        $rowFormat = $this->getRowFormat();
        $this->counter++;
        
        return strtr($rowFormat, array(
            '%label%' => $label,
            '%field%' => $field,
            '%error%' => $this->formatErrorsForRow($errors),
            '%help%' => $this->formatHelp($help),
            '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
        ));
        
    }

    public function getRowFormat() {
        
        /*if ($this->counter < count(self::$customRowFormats)) {
            return self::$customRowFormats[$this->counter];
        }*/
        
        if (isset(self::$customRowFormats[$this->counter])) {
            return self::$customRowFormats[$this->counter];
        }
        
        return $this->rowFormat;

    }
    
}

