<?php

class sfWidgetFormSchemaFormatterAddEmployee extends sfWidgetFormSchemaFormatter {

    protected static $index = 1;
    protected static $row = 0;
    protected static $noOfColumns = 1;
    protected static $elements = 0;
    protected
            $rowFormat = "%rowstart%\n  <td>%label%</td>\n  <td colspan=\"%colspan%\">%field%%error%%help%%hidden_fields%</td>\n%rowend%\n",
            $errorRowFormat = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
            $helpFormat = '<br />%help%',
            $errorListFormatInARow = '<ul class="error_list">%errors%</ul>',
            $errorRowFormatInARow = '<li>%error%</li>',
            $namedErrorRowFormatInARow = '<li>%name%: %error%</li>',
            $decoratorFormat = "<table>\n  %content%</table>";

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
        self::$elements++;
        return strtr($this->getRowFormat(), array(
                    '%label%' => $label,
                    '%field%' => $field,
                    '%colspan%' => $this->getAttr($field, 'colspan'),
                    '%rowstart%' => $this->getTrStart(),
                    '%rowend%' => $this->getTrEnd($field),
                    '%error%' => $this->formatErrorsForRow($errors),
                    '%help%' => $this->formatHelp($help),
                    '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
                ));
    }

    /**
     *
     * @return string
     */
    protected function getTrStart() {
        return (self::$elements == 0) ? '<tr>' : '';
    }

    /**
     *
     * @return string
     */
    protected function getTrEnd($field) {
        $br = '';

        if ($this->getAttr($field, 'br') == '') {
            self::$elements = self::$elements + (int) $this->getAttr($field, 'colspan');
            if ((self::$elements == self::$noOfColumns)) {
                $br = '</tr>';
                self::$elements = 0;
            }
        } else {
            $br = '</tr>';
        }

        return $br;
    }

    /**
     *
     * @return string
     */
    protected function getAttr($field, $attr) {
        $element = new SimpleXMLElement($field);
        $attrVal = '';
        if (isset($element[$attr])) {
            $attrVal = $element[$attr];
        }

        return (string) $attrVal;
    }

}
