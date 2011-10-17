<?php

class sfWidgetFormSchemaFormatterBreakTags extends sfWidgetFormSchemaFormatter {

    protected $rowFormat = "%error%%label%\n  %field%%help%\n%hidden_fields%<br class=\"clear\">\n";
    protected $errorRowFormat = "<span>\n%errors%</span>\n";
    protected $helpFormat = '<br />%help%';
    protected $decoratorFormat = "<form>\n  %content%</form>";

}

